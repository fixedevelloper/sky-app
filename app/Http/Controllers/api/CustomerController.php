<?php


namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\Customer;
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
            'name'            => 'required|string',
            'phone'           => 'required|string|unique:customers,phone',
            'localisation'    => 'nullable|string',
            'commercial_code' => 'nullable|string',
            'is_customer'     => 'required|string',
        ];

        // Si ce n’est pas un client
        if ($request->get('is_customer') === 'false') {
            $rules = array_merge($rules, [
                'activity'         => 'nullable|string',
                'point_sale_id'    => 'nullable|exists:point_sales,id',
                'code_key_account' => 'nullable|string',
                'image_url'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'image_cni_recto'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                'image_cni_verso'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);
        }

        try {
            $validated = $request->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
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
            $data = [
                'name'            => $validated['name'],
                'phone'           => $validated['phone'],
                'activity'        => $validated['activity'] ?? null,
                'localisation'    => $validated['localisation'] ?? null,
                'commercial_code' => $validated['commercial_code'] ?? null,
            ];

            if ($request->get('is_customer') === 'false') {
                $data['code_key_account'] = $validated['code_key_account'] ?? null;
                $data['point_sale_id']    = $validated['point_sale_id'] ?? null;
                foreach (['image_url', 'image_cni_recto', 'image_cni_verso'] as $field) {
                    if (isset($validated[$field])) {
                        $data[$field] = $validated[$field];
                    }
                }
            }

            $customer = Customer::create($data);

            // Création du purchase
            $purchase = Purchase::create([
                'product_id'  => $request->product_id,
                'pay_type'         => $request->is_cash==0 ?'cash' : 'leasing',
                'payment_mode'  => $request->prixCash ? 'CASH' : 'LEASING',
                'image_url'     => $request->imageTelephone ?? null,
                'customer_id'   => $customer->id,
            ]);

            $referenceId = Str::uuid()->toString();
            $status = $this->momo->requestToPay($referenceId, $request->phone, $request->amount);

            if ($status) {
                Paiement::create([
                    'phone'       => $request->phone,
                    'amount'      => $request->amount,
                    'amount_rest' => $request->amount,
                    'operator'    => $request->platform ?? 'MTN',
                    'status'      => 'PENDING',
                    'purchase_id' => $purchase->id,
                ]);
            }

            DB::commit();

            return response()->json([
                'message'  => '✅ Client créé avec succès',
                'customer' => $customer,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            logger($e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
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
