<?php


namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\Paiement;
use App\Service\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PaiementController extends Controller
{
    private $momo;

    public function __construct(MomoService $momo)
    {
        $this->momo = $momo;
    }
    public function index()
    {
        return response()->json(Paiement::with('purchase')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'amount' => 'required|numeric',
            'platform' => 'nullable|string',
            'purchase_id' => 'required|exists:purchases,id',
        ]);

        $referenceId = Str::uuid()->toString();
        $status = $this->momo->requestToPay($referenceId, $request->phone, $request->amount);

        if ($status) {
            Paiement::create([
                'phone'       => $request->phone,
                'amount'      => $request->amount,
                'amount_rest' => $request->amount,
                'operator'    => $request->platform ?? 'MTN',
                'status'      => 'PENDING',
                'purchase_id' => $request->purchase_id,
                'reference_id'=>$referenceId
            ]);
        }

        return response()->json([
            'status'=>$status,
            'referenceId'=>$referenceId
        ], 201);
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
