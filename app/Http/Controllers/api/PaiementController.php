<?php


namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\Paiement;
use Illuminate\Http\Request;

class PaiementController extends Controller
{
    public function index()
    {
        return response()->json(Paiement::with('purchase')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric',
            'amount_rest' => 'required|numeric',
            'operator' => 'nullable|string',
            'status' => 'required|in:pending,confirmed,failed',
            'purchase_id' => 'required|exists:purchases,id',
        ]);

        $paiement = Paiement::create($validated);

        return response()->json($paiement, 201);
    }

    public function show(Paiement $paiement)
    {
        return response()->json($paiement->load('purchase'));
    }

    public function update(Request $request, Paiement $paiement)
    {
        $validated = $request->validate([
            'phone' => 'sometimes|string',
            'amount' => 'sometimes|numeric',
            'amount_rest' => 'sometimes|numeric',
            'operator' => 'nullable|string',
            'status' => 'sometimes|in:pending,confirmed,failed',
            'purchase_id' => 'sometimes|exists:purchases,id',
        ]);

        $paiement->update($validated);

        return response()->json($paiement);
    }

    public function destroy(Paiement $paiement)
    {
        $paiement->delete();
        return response()->json(['message' => 'Paiement deleted successfully']);
    }
}
