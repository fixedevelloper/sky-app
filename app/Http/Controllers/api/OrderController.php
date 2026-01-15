<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Paiement;
use App\Models\Product;
use App\Service\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private $momo;

    public function __construct(MomoService $momo)
    {
        $this->momo = $momo;
    }

    public function index()
    {
        $orders = Order::query()
            ->where('user_id', Auth::id())
            ->with([
                'items' => function ($q) {
                    $q->select('id', 'order_id', 'product_id', 'quantity', 'amount')
                        ->with([
                            'product:id,name,image_url'
                        ]);
                }
            ])
            ->latest()
            ->get(['id', 'amount', 'status', 'reference_id', 'created_at','meta']);

        return response()->json([
            'data' => $orders
        ]);
    }


    // Détail d'une commande
    public function show($id)
    {
        $order = Order::with(['user', 'items.product'])->findOrFail($id);
        return response()->json($order);
    }

    // Création d'une commande avec items
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'operator' => 'nullable|string',
            'reference_id' => 'nullable|string',
            'meta' => 'nullable|array',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        DB::transaction(function() use ($request, &$order, $validated) {
            $referenceId = Str::uuid()->toString();
            // 1️⃣ Créer la commande
            $order = Order::create([
                'reference_id' => $referenceId,
                'user_id' => $request->user_id ?? 1,
                'operator' => $request->operator,
                'status' => 'pending',
                'meta' => $request->meta ?? null,
                'amount' => 0,
                'amount_rest' => 0
            ]);

            $totalAmount = 0;

            // 2️⃣ Ajouter les items
            foreach ($request->items as $itemData) {
                $product = Product::findOrFail($itemData['product_id']);
                $quantity = $itemData['quantity'];

                $amount = $product->price * $quantity;
                $totalAmount += $amount;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'amount' => $amount
                ]);
            }

            // 3️⃣ Mettre à jour le montant total
            $order->amount = $totalAmount;
            $order->amount_rest = $totalAmount;
            $order->save();
            if ($validated['operator'] == 'ORANGE') {
                throw new \Exception("Orange Money est temporairement indisponible.");
            }


            $status = $this->momo->requestToPay($referenceId, $validated['meta']['phone'], $amount);

            if ($status != 202) {
                throw new \Exception("Le paiement a échoué. Vérifiez le numéro, le solde et l'opérateur.");
            }

            Paiement::create([
                'phone' => $validated['meta']['phone'],
                'amount' => $amount,
                'amount_rest' => $amount,
                'operator' => $validated['operator'],
                'status' => 'PENDING',
                'reference_id' => $referenceId,
            ]);
        });

        $order->load('items.product', 'user');

        return response()->json([
            'message' => 'Commande créée avec succès',
            'referenceId' => $order->reference_id,
            'order' => $order
        ]);
    }

    // Mettre à jour le statut d'une commande
    public function updateStatus(Request $request, $orderId)
    {
        $request->validate([
            'status' => 'required|in:pending,waiting,confirmed,failed'
        ]);

        $order = Order::findOrFail($orderId);
        $order->status = $request->status;

        if ($request->status === 'confirmed') {
            $order->confirmed_at = now();
        }

        $order->save();

        return response()->json([
            'message' => 'Statut mis à jour',
            'order' => $order->load('items.product', 'user')
        ]);
    }

    // Supprimer une commande
    public function destroy($orderId)
    {
        $order = Order::findOrFail($orderId);
        $order->delete();

        return response()->json([
            'message' => 'Commande supprimée'
        ]);
    }
}
