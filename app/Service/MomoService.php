<?php

namespace App\Service;

use Illuminate\Routing\Route;
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

        $response = Http::withHeaders([
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            'Authorization' => 'Basic ' . base64_encode($this->apiUser . ':' . $this->apiKey),
        ])->post($this->baseUrl . '/token/');

        return $response['access_token'] ?? null;
    }

    // 🔹 Initier un paiement
    /* public function requestToPay($referenceId, $phone, $amount, $currency = 'XAF')
    {
        $token = $this->getToken();

        $url = $this->baseUrl . '/collection/accounts/' .'12f411d8a5af4e549bb763543cbae983' . '/transactions';

        $data = [
            'amount' => $amount,
            'currency' => $currency,
            'externalId' => $referenceId,
            'payer' => [
                'partyIdType' => 'MSISDN',
                'partyId' => $phone
            ],
            'payerMessage' => 'Paiement commande',
            'payeeNote' => 'Merci pour votre achat'
        ];

        logger('Request To Pay payload', $data);

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'X-Reference-Id' => $referenceId,
            'X-Target-Environment' => config('services.momo.env', 'sandbox'),
            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
            'Content-Type' => 'application/json'
        ])->post($url, $data);

        logger('Request To Pay response', ['status' => $response->status(), 'body' => $response->body()]);

        return [
            'status' => $response->status(),
            'body' => $response->json()
        ];
    }
*/
       public function requestToPay($referenceId, $phone, $amount, $currency = 'EUR')
        {
            $token = $this->getToken();
            logger(route('momo.callback'));
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'X-Reference-Id' => $referenceId,
                'X-Target-Environment' => config('services.momo.env', 'sandbox'),
                'Ocp-Apim-Subscription-Key' => $this->subscriptionKey,
                'Content-Type' => 'application/json',
                'X-Callback-Url'=>route('momo.callback')
            ])->post($this->baseUrl . '/v1_0/requesttopay', [
                'amount' => $amount,
                'currency' => $currency,
                'externalId' => $referenceId,
                'payer' => [
                    'partyIdType' => 'MSISDN',
                    'partyId' => '+237'.$phone
                ],
                'payerMessage' => 'Paiement commande',
                'payeeNote' => 'Merci pour votre achat'
            ]);
            logger('**************************'.json_encode($response));
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
        ])->get($this->baseUrl . '/v1_0/requesttopay/' . $referenceId);

        return $response->json();
    }
}
