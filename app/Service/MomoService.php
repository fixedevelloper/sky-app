<?php

namespace App\Service;

use Illuminate\Support\Facades\Http;

class MomoService
{
    private $baseUrl;
    private $subscriptionKey;
    private $apiUser;
    private $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.momo.base_url');
        $this->subscriptionKey = config('services.momo.subscription_key');
        $this->apiUser = config('services.momo.api_user');
        $this->apiKey = config('services.momo.api_key');
    }

    // 🔹 Obtenir un token d'accès
    public function getToken()
    {
        logger($this->apiUser);
        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            'Authorization' => 'Basic ' . base64_encode($this->apiUser . ':' . $this->apiKey),
        ])->post($this->baseUrl . '/token/');

        logger($response->json());
        return $response->json()['access_token'] ?? null;
    }

    // 🔹 Initier un paiement
    public function requestToPay($referenceId, $phone, $amount, $currency = 'XAF')
    {
        $token = $this->getToken();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'X-Reference-Id' => $referenceId,
            'X-Target-Environment' => config('services.momo.env', 'sandbox'),
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            'Content-Type' => 'application/json'
        ])->post($this->baseUrl . '/requesttopay', [
            'amount' => $amount,
            'currency' => $currency,
            'externalId' => $referenceId,
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $phone
            ],
            'payerMessage' => 'Paiement commande',
            'payeeNote' => 'Merci pour votre achat'
        ]);

        return $response->status();
    }

    // 🔹 Vérifier le statut d’un paiement
    public function getPaymentStatus($referenceId)
    {
        $token = $this->getToken();

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'X-Target-Environment' => config('services.momo.env', 'sandbox'),
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
        ])->get($this->baseUrl . '/requesttopay/' . $referenceId);

        return $response->json();
    }
}
