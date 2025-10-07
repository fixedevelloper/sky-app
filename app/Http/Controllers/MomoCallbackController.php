<?php

namespace App\Http\Controllers;

use App\Models\MomoCallback;
use Illuminate\Http\Request;
use App\Models\Paiement;
use Illuminate\Support\Facades\Log;

class MomoCallbackController extends Controller
{
    public function callback(Request $request)
    {
        Log::info('📩 Callback MoMo reçu', $request->all());

        $data = $request->all();
        $referenceId = $data['referenceId'] ?? null;
        $status = strtoupper($data['status'] ?? '');
        $amount = $data['amount'] ?? null;

        // 🔹 Enregistrer le callback complet dans la table momo_callbacks
        MomoCallback::create([
            'reference_id' => $referenceId,
            'status' => $status,
            'amount' => $amount,
            'payload' => $data,
        ]);

        if (!$referenceId) {
            Log::warning('⚠️ Callback MoMo sans referenceId', $data);
            return response()->json(['message' => 'Référence manquante'], 400);
        }

        $paiement = Paiement::where('reference_id', $referenceId)->first();

        if (!$paiement) {
            Log::error('❌ Paiement introuvable pour le callback MoMo', ['referenceId' => $referenceId]);
            return response()->json(['message' => 'Paiement introuvable'], 404);
        }

        $paiement->update([
            'status' => match ($status) {
            'SUCCESSFUL' => 'confirmed',
            'FAILED' => 'failed',
            default => 'pending',
        },
        'confirmed_at' => now(),
        'amount' => $amount ?? $paiement->amount,
    ]);

    Log::info('✅ Paiement mis à jour depuis callback MoMo', [
        'referenceId' => $referenceId,
        'status' => $status,
        'amount' => $amount,
    ]);

    return response()->json(['message' => 'Callback traité avec succès']);
}
}
