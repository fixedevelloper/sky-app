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
        $validated = $request->validate([
            'name'             => 'required|string',
            'phone'            => 'required|string|unique:customers,phone',
            'point_sale_id'    => 'required|exists:point_sales,id',
            'activity'         => 'nullable|string',
            'localisation'     => 'nullable|string',
            'commercial_code'  => 'nullable|string',
            'code_key_account' => 'nullable|string',

            // fichiers optionnels
            'image_url'        => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image_cni_recto'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'image_cni_verso'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        logger($validated);
        // Sauvegarde des fichiers et récupération des chemins
        foreach (['image_url', 'image_cni_recto', 'image_cni_verso'] as $field) {
            if ($request->hasFile($field)) {
                $validated[$field] = $request->file($field)->store("customers/$field", 'public');
            }
        }

        DB::beginTransaction();
        try {
            // Création du client
            $customer = Customer::create([
                'name'             => $validated['name'],
                'phone'            => $validated['phone'],
                'activity'         => $validated['activity'] ?? null,
                'localisation'     => $validated['localisation'] ?? null,
                'commercial_code'  => $validated['commercial_code'] ?? null,
                'code_key_account' => $validated['code_key_account'] ?? null,
                'image_cni_recto'  => $validated['image_cni_recto'] ?? null,
                'image_cni_verso'  => $validated['image_cni_verso'] ?? null,
                'image_url'        => $validated['image_url'] ?? null,
                'point_sale_id'    => $validated['point_sale_id'],
            ]);

            // Création de l'achat
            $purchase = Purchase::create([
                'product_name' => $request->nomTelephone,
                'price'        => $request->prixCash ?? $request->prixLeasing,
                'amount_by_day'     => 2000,
                'payment_mode' => $request->prixCash ? 'CASH' : 'LEASING',
                'image_url'    => $request->imageTelephone ?? null,
                'customer_id'  => $customer->id,
            ]);

            // Générer un identifiant unique pour le paiement
            $referenceId = Str::uuid()->toString();

            // Lancer la requête de paiement mobile
            $status = $this->momo->requestToPay($referenceId, $request->phone, $request->amount);

            if ($status) {
                Paiement::create([
                    'phone'       => $request->phone,
                    'amount'      => $request->amount,
                    'amount_rest' => $request->amount, // ou calculer si partiellement payé
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
            logger($e->getMessage());
            DB::rollBack();
            return response()->json([
                'error' => 'Erreur serveur: '.$e->getMessage()
            ], 500);
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
