<?php


namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomProduct;
use App\Models\Paiement;
use App\Models\Purchase;
use App\Service\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    private $momo;

    public function __construct(MomoService $momo)
    {
        $this->momo = $momo;
    }

    public function index()
    {
        return response()->json(Customer::with('pointSale')->get());
    }

    public function store(Request $request)
    {

        // Corriger les valeurs "undefined" venant du front
        foreach (['point_sale_id', 'activity', 'localisation', 'commercial_code', 'code_key_account'] as $field) {
            if ($request->input($field) === 'undefined') {
                $request->merge([$field => null]);
            }
        }

        // Validation de base
        $rules = [
            'name' => 'required|string',
            'phone' => 'required|string',
            'localisation' => 'nullable|string',
            'commercial_code' => 'nullable|string',
            'is_customer' => 'required|string',
        ];

        // Si ce n’est pas un client
        if ($request->get('is_customer') === 'false') {
            $rules = array_merge($rules, [
                'activity' => 'nullable|string',
                'point_sale_id' => 'nullable|exists:point_sales,id',
                'code_key_account' => 'nullable|string',
                'image_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'image_cni_recto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'image_cni_verso' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);
        }

        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger($e->getMessage());
            return response()->json([
                'error' => 'Erreur de validation',
                'messages' => $e->errors()
            ], 422);
        }


        // Upload fichiers
        foreach (['image_url', 'image_cni_recto', 'image_cni_verso'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store("customers/$field", 'public');
            }
        }

        DB::beginTransaction();
        try {
            $isCustom = filter_var($request->input('is_custom'), FILTER_VALIDATE_BOOLEAN);
            $data = ['name' => $validated['name'], 'phone' => $validated['phone'], 'activity' => $validated['activity'] ?? null, 'localisation' => $validated['localisation'] ?? null, 'commercial_code' => $validated['commercial_code'] ?? null,];
            if ($request->get('is_customer') === 'false') {
                $data['code_key_account'] = $validated['code_key_account'] ?? null;
                $data['point_sale_id'] = $validated['point_sale_id'] ?? null;
                foreach (['image_url', 'image_cni_recto', 'image_cni_verso'] as $field) {
                    if (isset($validated[$field])) {
                        $data[$field] = $validated[$field];
                    }
                }
            }
            $customer = Customer::where(['phone' => $data['phone']])->first();

            if (is_null($customer)) {
                $customer = Customer::create($data);
            } else {
                $customer->update($data); // ✅ Correction ici
            }


            $payType = $request->is_cash == 0 ? 'cash' : 'leasing';
            $paymentMode = $request->is_cash == 0 ? 'CASH' : 'LEASING';

            if ($isCustom) {
                $purchase = Purchase::create([
                    'pay_type' => $payType,
                    'payment_mode' => $paymentMode,
                    'image_url' => $request->imageTelephone ?? null,
                    'customer_id' => $customer->id,
                ]);

                $customProduct = $purchase->customProduct()->create([
                    'name' => $request->nomTelephone,
                    'amount' => $request->amount,
                ]);

                $amount = $request->amount;
            } else {
                $purchase = Purchase::create([
                    'product_id' => $request->product_id,
                    'pay_type' => $payType,
                    'payment_mode' => $paymentMode,
                    'image_url' => $request->imageTelephone ?? null,
                    'customer_id' => $customer->id,
                ]);

                $amount = $purchase->product->price ?? 0;
            }

            $referenceId = Str::uuid()->toString();
            if ($request->platform=='ORANGE'){
                throw new \Exception("La plateforme Orange Money est temporairement en maintenance. Veuillez utiliser une autre option comme MTN.");

            }
            $status = $this->momo->requestToPay($referenceId, $request->phone, $request->amount);

            if ($status == 202) {
                $purchase->paiements()->create([
                    'phone' => $request->phone,
                    'amount' => $request->amount,
                    'amount_rest' => $amount-$request->amount,
                    'operator' => $request->platform ?? 'MTN',
                    'status' => 'PENDING',
                    'reference_id' => $referenceId,
                ]);
            } else {
                throw new \Exception("Le paiement a échoué. Veuillez vérifier les points suivants :
- Le numéro de téléphone est valide.
- Le solde est suffisant.
- L'opérateur est correct.");
            }


            DB::commit();

            return response()->json([
                'message' => '✅ Client créé avec succès',
                'customer' => $customer,
                'referenceId' => $referenceId
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }


    public function getCurrentCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'phone' => 'sometimes|string|exists:customers,phone',
            'commercial_code' => 'required|string',
        ]);

        // 🔹 Récupération du client
        $customer = Customer::query()
            ->where('commercial_code', $validated['commercial_code'])
            ->when(isset($validated['phone']), fn($q) => $q->where('phone', $validated['phone']))
            ->when(isset($validated['name']), fn($q) => $q->where('name', $validated['name']))
            ->firstOrFail();

        // 🔹 Dernier achat du client
        $purchase = Purchase::query()
            ->where('customer_id', $customer->id)
            ->latest()
            ->firstOrFail();

        // 🔹 Chargement du produit et paiements
        $product = $purchase->product;
        $paiements = $purchase->paiements()->get();

        // 🔹 Calcul du total payé
        $total = 0.0;
        foreach ($paiements as $item) {
            if ($item->status === 'confirmed') {
                $total += $item->amount;
            }
        }

        // 🔹 Construction des données
        $data = [
            'customer_name' => $customer->name,
            'customer_phone' => $customer->phone,
            'point_sale' => optional($customer->pointSale)->name,
            'product_name' => $product->name ?? null,
            'product_price' => $product->price ?? null,
            'product_price_leasing' => $product->price_leasing ?? null,
            'purchase_id' => $purchase->id,
            'total_pay' => $total,
            'rest_pay' => ($product->price ?? 0) - $total,
            'paiements' => $paiements,
        ];

        return response()->json($data);
    }


    public function show(Customer $customer)
    {
        return response()->json($customer->load('purchases'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'phone' => 'sometimes|string|unique:customers,phone,' . $customer->id,
            'point_sale_id' => 'sometimes|exists:point_sales,id',
            'activity' => 'nullable|string',
            'localisation' => 'nullable|string',
        ]);

        $customer->update($validated);

        return response()->json($customer);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
