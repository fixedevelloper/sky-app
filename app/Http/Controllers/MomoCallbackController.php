<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Paiement;
use Illuminate\Support\Facades\Log;

class MomoCallbackController extends Controller
{
    /**
     * Callback pour recevoir la réponse du serveur MTN MoMo
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleCallback(Request $request)
    {
        // ✅ Log pour vérification
        Log::info('📩 MoMo Callback reçu:', $request->all());

        // Exemple de structure renvoyée par MoMo
        // {
        //   "referenceId": "8e8f137a-b535-4e99-a8b6-346af661fb37",
        //   "status": "SUCCESSFUL",
        //   "payer": {"partyIdType": "MSISDN", "partyId": "677000111"},
        //   "amount": "5000",
        //   "currency": "XAF"
        // }

        $referenceId = $request->input('referenceId');
        $status      = strtoupper($request->input('status', 'PENDING'));
        $amount      = $request->input('amount');
        $payer       = $request->input('payer.partyId') ?? null;

        // 🧾 Mettre à jour le paiement correspondant
        $paiement = Paiement::where('reference_id', $referenceId)->first();

        if (!$paiement) {
            Log::warning("⚠️ Aucun paiement trouvé pour la référence: $referenceId");
            return response()->json(['message' => 'Paiement non trouvé'], 404);
        }

        // 🧠 Mise à jour du statut
        $paiement->update([
            'status' => match (strtoupper($status)) {
            'SUCCESSFUL' => 'confirmed',
        'FAILED'     => 'failed',
        default      => 'pending',
         },
    'confirmed_at' => now(),
    'amount'       => $amount ?? $paiement->amount,
]);


        Log::info("✅ Paiement mis à jour: {$paiement->id} -> {$status}");

        return response()->json(['message' => 'Callback reçu avec succès']);
    }
}
