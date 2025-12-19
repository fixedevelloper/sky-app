<?php


namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\CustomProduct;
use App\Models\Paiement;
use App\Models\PointSale;
use App\Models\Purchase;
use App\Models\User;
use App\Service\MomoService;
use Illuminate\Container\Attributes\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
class PurchaseController extends Controller
{
    private $momo;

    public function __construct(MomoService $momo)
    {
        $this->momo = $momo;
    }
  public function index()
{
    $user = auth()->user();

    // 🔹 Vérification d'authentification
    if (!$user) {
        return response()->json(['error' => 'Utilisateur non authentifié'], 401);
    }

    // 🔹 Base de la requête avec relations nécessaires
    $query = Purchase::with(['customer.user', 'product', 'customProduct', 'paiements']);

    // 🔹 Filtrage selon le type d'utilisateur
    switch ($user->user_type) {
        case 'customer':
            $query->whereHas('customer', fn($q) => $q->where('user_id', $user->id));
            break;

        case 'vendor':
            $query->where('vendor_id', $user->id);
            break;

        case 'admin':
            // Pas de filtre, voit tout
            break;

        default:
            return response()->json(['error' => 'Non autorisé'], 403);
    }

    // 🔹 Récupération optimisée des achats
    $purchases = $query->orderByDesc('created_at')->get();

    // 🔹 Transformation des données
    $data = $purchases->map(function ($purchase) {
        $isCustom = $purchase->is_custom_product ?? !is_null($purchase->customProduct);

        $product = $isCustom
            ? optional($purchase->customProduct)
            : optional($purchase->product);

        $total = $isCustom
            ? (float) $product->amount
            : (float) ($product->price ?? 0);

        $paid = (float) $purchase->paiements->sum('amount');
        $remaining = max(0, $total - $paid);

        return [
            'id' => $purchase->id,
            'reference' => $purchase->referenceId ?? $purchase->id,
            'product' => $product->name ?? 'Produit personnalisé',
            'total' => $total,
            'paid' => $paid,
            'rest_pay' => $remaining,
            'status' => $remaining==0?'PAID' : 'PENDING',
            'pay_type' => strtoupper($purchase->pay_type ?? 'CASH'),
            'created_at' => $purchase->created_at->format('Y-m-d H:i:s'),
            'customer' => [
                'name' => $purchase->customer->user->name ?? 'N/A',
                'phone' => $purchase->customer->user->phone ?? 'N/A',
            ],
        ];
    });

    return response()->json($data, 200);
}



    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'price' => 'required|numeric',
            'quantity' => 'required|integer',
            'customer_id' => 'required|exists:customers,id',
            'payment_mode' => 'nullable|string',
        ]);

        $purchase = Purchase::create($validated);

        return response()->json($purchase, 201);
    }
    public function storeCustomer(Request $request)
    {
        $user = auth()->user();
        logger($request->all());
        try {
            $validated = $request->validate([
                'product_name' => 'required_if:is_custom,1|string',
                'platform' => 'required|string',
                'product_id' => 'required_if:is_custom,0|numeric',
                'is_cash' => 'required|boolean',
                'is_custom' => 'required|boolean',
                'phone' => 'required|string|min:9',
                'amount' => 'required|numeric|min:0',
            ]);

            DB::beginTransaction();

            $customer = Customer::where('user_id', $user->id)->firstOrFail();

            $payType = $validated['is_cash'] ? 'cash' : 'leasing';
            $paymentMode = $validated['is_cash'] ? 'CASH' : 'LEASING';

            $purchase = Purchase::create([
                'product_id' => $validated['is_custom'] ? null : $validated['product_id'],
                'pay_type' => $payType,
                'is_custom_product' => $$validated['is_custom'],
                'payment_mode' => $paymentMode,
                'image_url' => $request->imageTelephone ?? null,
                'customer_id' => $customer->id,
            ]);

            if ($validated['is_custom']) {
                $purchase->customProduct()->create([
                    'name' => $validated['product_name'],
                    'amount' => $validated['amount'],
                ]);
            }

            if ($validated['platform'] == 'ORANGE') {
                throw new \Exception("Orange Money est temporairement indisponible.");
            }

            $referenceId = Str::uuid()->toString();
            $status = $this->momo->requestToPay($referenceId, $validated['phone'], $validated['amount']);

            if ($status != 202) {
                throw new \Exception("Le paiement a échoué. Vérifiez le numéro, le solde et l'opérateur.");
            }

            $purchase->paiements()->create([
                'phone' => $validated['phone'],
                'amount' => $validated['amount'],
                'amount_rest' => ($validated['is_custom'] ? $validated['amount'] : $purchase->product->price) - $validated['amount'],
                'operator' => $validated['platform'],
                'status' => 'PENDING',
                'reference_id' => $referenceId,
            ]);

            DB::commit();

            return response()->json([
                'message' => '✅ Achat enregistré avec succès',
                'customer' => $customer,
                'purchase_id' => $purchase->id,
                'referenceId' => $referenceId
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error("storeCustomer error: " . $e->getMessage(), ['user_id' => $user->id]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeCommercial(Request $request)
    {
        $user = auth()->user();
        logger($request->all());

        try {
            $validated = $request->validate([
                'product_name' => 'required_if:is_custom,1|string',
                'platform' => 'required|string',
                'product_id' => 'required_if:is_custom,0|numeric',
                'is_cash' => 'required|boolean',
                'is_custom' => 'required|boolean',
                'phone' => 'required|string|min:9',
                'amount' => 'required|numeric|min:0',
                'customer_name' => 'required|string',
                'customer_phone' => 'required|string',
                'customer_activity' => 'required|string',
                'customer_localisation' => 'required|string',
                'manager' => 'required|string',
                'sale_point' => 'required|string',
            ]);

            DB::beginTransaction();

            $user_customer = User::firstOrCreate(
                ['phone' => $validated['customer_phone']],
                [
                    'name' => $validated['customer_name'],
                    'role' => 'customer',
                    'phone' => $validated['customer_phone'],
                    'email' => $validated['customer_phone'] . '@skypay.org',
                    'password' => bcrypt('123456789')
                ]
            );

            $customer = Customer::firstOrCreate(
                ['user_id' => $user_customer->id],
                [

                    'activity' => $validated['customer_activity'],
                    'localisation' => $validated['customer_localisation'],

                ]
            );

            $salePoint = PointSale::findOrFail($validated['sale_point']);

            $payType = $validated['is_cash'] ? 'cash' : 'leasing';
            $paymentMode = $validated['is_cash'] ? 'CASH' : 'LEASING';

            $purchase = Purchase::create([
                'product_id' => $validated['is_custom'] ? null : $validated['product_id'],
                'pay_type' => $payType,
                'is_custom_product' => $$validated['is_custom'],
                'payment_mode' => $paymentMode,
                'image_url' => $request->imageTelephone ?? null,
                'customer_id' => $customer->id,
                'vendor_id' => $salePoint->user_id,
            ]);

            if ($validated['is_custom']) {
                $purchase->customProduct()->create([
                    'name' => $validated['product_name'],
                    'amount' => $validated['amount'],
                ]);
            }

            if ($validated['platform'] === 'ORANGE') {
                throw new \Exception("Orange Money est temporairement indisponible.");
            }

            $referenceId = Str::uuid()->toString();
            $status = $this->momo->requestToPay($referenceId, $validated['phone'], $validated['amount']);

            if ($status != 202) {
                throw new \Exception("Le paiement a échoué. Vérifiez le numéro, le solde et l'opérateur.");
            }

            $purchase->paiements()->create([
                'phone' => $validated['phone'],
                'amount' => $validated['amount'],
                'amount_rest' => $validated['is_custom']
                    ? 0
                    : max(0, $purchase->product->price - $validated['amount']),
                'operator' => $validated['platform'],
                'status' => 'PENDING',
                'reference_id' => $referenceId,
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '✅ Achat enregistré avec succès',

                'customer' => $customer,
                'purchase' => $purchase,
                'referenceId' => $referenceId,

            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            logger()->error("Erreur storeCommercial", [
                'user_id' => $user->id,
                'request' => $request->all(),
                'message' => $e->getMessage()
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function showCurrent(Request $request)
    {
        try {
            $user = auth()->user();

            // Vérification du client lié à l'utilisateur
            $customer = Customer::where('user_id', $user->id)->first();
            if (!$customer) {
                return response()->json(['error' => 'Aucun client associé à cet utilisateur.'], 404);
            }

            // Récupération du dernier achat
            $purchase = Purchase::where('customer_id', $customer->id)
                ->with(['paiements', 'product', 'customProduct'])
                ->latest()
                ->first();

            if (!$purchase) {
                return response()->json(['error' => 'Aucun achat trouvé pour ce client.'], 404);
            }
        $isCustom = $purchase->is_custom_product ?? !is_null($purchase->customProduct);

        $product = $isCustom
            ? optional($purchase->customProduct)
            : optional($purchase->product);

        $total = $isCustom
            ? (float) $product->amount
            : (float) ($product->price ?? 0);

        $paid = (float) $purchase->paiements->sum('amount');
        $remaining = max(0, $total - $paid);
            return response()->json([
                'purchase' => [
                    'id'=>$purchase->id,
                    'product_name'=>$product->name,
                    'rest_pay'=>$remaining
                ],
                'paiements' => $purchase->paiements,
            ], 200);

        } catch (\Exception $e) {
            logger()->error("Erreur showCurrent : " . $e->getMessage(), [
                'user_id' => $user->id ?? null,
            ]);

            return response()->json([
                'error' => 'Une erreur est survenue lors de la récupération de l’achat.',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    public function show(Purchase $purchase)
    {
        return response()->json($purchase->load('paiements'));
    }

    public function update(Request $request, Purchase $purchase)
    {
        $validated = $request->validate([
            'product_name' => 'sometimes|string',
            'price' => 'sometimes|numeric',
            'quantity' => 'sometimes|integer',
            'payment_mode' => 'nullable|string',
            'customer_id' => 'sometimes|exists:customers,id',
        ]);

        $purchase->update($validated);

        return response()->json($purchase);
    }

    public function destroy(Purchase $purchase)
    {
        $purchase->delete();
        return response()->json(['message' => 'Purchase deleted successfully']);
    }
}
