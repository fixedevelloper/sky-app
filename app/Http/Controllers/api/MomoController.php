<?php


namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Models\PointSale;
use App\Service\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MomoController extends Controller
{
    private $momo;

    public function __construct(MomoService $momo)
    {
        $this->momo = $momo;
    }

    // Initier un paiement
    public function pay(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric|min:100'
        ]);

        $referenceId = Str::uuid()->toString();

        $status = $this->momo->requestToPay($referenceId, $request->phone, $request->amount);

        return response()->json([
            'referenceId' => $referenceId,
            'status' => $status
        ]);
    }

    public function checkStatus($referenceId)
    {
        $response=$this->momo->getPaymentStatus($referenceId);
        // Rechercher le paiement correspondant
        $paiement = Paiement::where('reference_id', $referenceId)->first();

        // Si non trouvé
        if (!$paiement) {
            return response()->json([
                'referenceId' => $referenceId,
                'status' => 'not_found',
                'message' => 'Aucun paiement trouvé pour cette référence.'
            ], 404);
        }

        // Si trouvé
        return response()->json([
            'referenceId' => $paiement->reference_id,
            'status' => $paiement->status,
            'amount' => $paiement->amount,
            'confirmed_at' => $paiement->confirmed_at,
        ]);
    }
    public function checkStatusSalePoint($referenceId)
    {

        $pointSale = PointSale::where('referenceId', $referenceId)->first();

        // Si non trouvé
        if (!$pointSale) {
            return response()->json([
                'referenceId' => $referenceId,
                'status' => 'not_found',
                'message' => 'Aucun paiement trouvé pour cette référence.'
            ], 404);
        }

        // Si trouvé
        return response()->json([
            'referenceId' => $pointSale->referenceId,
            'status' => $pointSale->status,
            'amount' => $pointSale->amount,
            'confirmed_at' => $pointSale->confirmed_at,
            'name'=>$pointSale->name,
            'activity'=>$pointSale->vendor->activity,
            'localisation'=>$pointSale->localisation,
            'image_url'=>$pointSale->image_url,
            'image_doc_fiscal'=>$pointSale->image_doc_fiscal,
            'vendor_name'=>$pointSale->vendor->name,
            'phone'=>$pointSale->vendor->phone,
            'image_cni_recto'=>$pointSale->vendor->image_cni_recto,
            'image_cni_verso'=>$pointSale->vendor->image_cni_verso,
        ]);
    }
    public function getToken(Request $request)
    {
        $url = 'https://proxy.momoapi.mtn.com/collection/oauth2/token';

        $headers = [
            'Ocp-Apim-Subscription-Key' => config('services.momo.subscription_key'),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        try {
            // ✅ Envoie du body en "form-data" (grant_type=client_credentials)
            $response = Http::asForm()
                ->withHeaders($headers)
                ->withBasicAuth(
                    config('services.momo.api_user'),
                    config('services.momo.api_key')
                )
                ->post($url, [
                    'grant_type' => 'client_credentials',
                ]);

            if ($response->successful()) {
                logger($response);
                return response()->json($response->json());
            }

            return response()->json([
                'status' => $response->status(),
                'body' => $response->body(),
            ], $response->status());
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'request_failed',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
