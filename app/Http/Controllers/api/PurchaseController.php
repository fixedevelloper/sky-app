<?php


namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\Purchase;
use Illuminate\Http\Request;
class PurchaseController extends Controller
{
    public function index()
    {
        return response()->json(Purchase::with('customer')->get());
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
