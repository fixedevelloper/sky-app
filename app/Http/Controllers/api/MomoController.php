<?php


namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Service\MomoService;
use Illuminate\Http\Request;
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

    // Vérifier un paiement
    public function checkStatus($referenceId)
    {
        $status = $this->momo->getPaymentStatus($referenceId);
        return response()->json($status);
    }
}
