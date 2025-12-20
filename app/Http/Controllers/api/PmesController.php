<?php


namespace App\Http\Controllers\api;



use App\Http\Controllers\Controller;
use App\Models\Pmes;
use App\Models\PointSale;
use App\Models\User;
use App\Service\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PmesController extends Controller
{
    private $momo;

    public function __construct(MomoService $momo)
    {
        $this->momo = $momo;
    }
    public function index()
    {
        return response()->json(PointSale::with('vendor')->get());
    }


    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'name_entreprise' => ['required', 'string', 'max:255'],
                'name_responsable' => ['required', 'string', 'max:255'],
                'poste_responsable' => ['required', 'string', 'max:255'],

                'amount_bc' => ['required', 'numeric', 'min:0'],
                'number_souscripteur' => ['required', 'integer', 'min:1'],
                'number_echeance_paiement' => ['required', 'integer', 'min:1'],

                'name_gestionnaire' => ['required', 'string', 'max:255'],
                'name_manager' => ['required', 'string', 'max:255'],

                'platform' => ['required', 'in:MTN,ORANGE'],
                'phone' => ['required', 'string'],

                // Images PME
                'image_bc' => ['required', 'image', 'max:2048'],
                'image_bl' => ['required', 'image', 'max:2048'],
                'image_facture' => ['required', 'image', 'max:2048'],
            ]);

            // 🔢 Calcul montant total (SOURCE DE VÉRITÉ)
             $fRAIS_PAR_SOUSCRIPTEUR = 2000;
            $montantTotal = $validated['number_souscripteur'] * $fRAIS_PAR_SOUSCRIPTEUR;

            // ❌ Orange en maintenance
            if ($validated['platform'] === 'ORANGE') {
                throw new \Exception(
                    "La plateforme Orange Money est temporairement en maintenance."
                );
            }

            // 📁 Upload images
            $imageBc = $this->storeImage($request, 'image_bc', 'pmes/bc');
            $imageBl = $this->storeImage($request, 'image_bl', 'pmes/bl');
            $imageFacture = $this->storeImage($request, 'image_facture', 'pmes/factures');

            // ✅ Vérifie si le vendeur existe
            $vendor = User::firstWhere('phone', $validated['phone']);

            if (!$vendor) {
                $vendor = User::create([
                    'name' => $validated['name_responsable'],
                    'phone' => $validated['phone'],
                    'user_type' => 'vendor',
                    'email' => $validated['phone'] . '@sky.com',
                    'password' => bcrypt('12345678@9'),
                ]);
            }
            // 💳 Paiement MoMo
            $referenceId = (string) Str::uuid();

            $status = $this->momo->requestToPay(
                $referenceId,
                $validated['phone'],
                $montantTotal
            );

            if ($status !== 202) {
                throw new \Exception("Le paiement a échoué");
            }

            // 🏪 Création PME
            $pme = Pmes::create([
                'referenceId' => $referenceId,
                'operator' => $validated['platform'],

                'name_entreprise' => $validated['name_entreprise'],
                'name_responsable' => $validated['name_responsable'],
                'poste_responsable' => $validated['poste_responsable'],

                'amount_bc' => $validated['amount_bc'],
                'number_souscripteur' => $validated['number_souscripteur'],
                'number_echeance_paiement' => $validated['number_echeance_paiement'],
                'montant_total' => $montantTotal,

                'name_gestionnaire' => $validated['name_gestionnaire'],
                'name_manager' => $validated['name_manager'],
                'image_bc' => $imageBc,
                'image_bl' => $imageBl,
                'image_facture' => $imageFacture,
                'status' => Pmes::STATUS_PENDING,
                'vendor_id' => $vendor->id
            ]);

            DB::commit();

            return response()->json([
                'referenceId' => $referenceId,
                'montant_total' => $montantTotal,
                'message' => 'PME créée avec succès',
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            logger($e);

            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }


    /**
     * Stocke un fichier uploadé dans storage/app/public et retourne l'URL publique
     * @param Request $request
     * @param $field
     * @param $folder
     * @return string|null
     */
    private function storeImage(Request $request,  $field,  $folder): ?string
    {
        if ($request->hasFile($field)) {
            return Storage::url($request->file($field)->store($folder, 'public'));
        }
        return null;
    }



    public function show(PointSale $pointSale)
    {
        return response()->json($pointSale->load('customers'));
    }

    public function update(Request $request, PointSale $pointSale)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string',
            'activity' => 'nullable|string',
            'localisation' => 'nullable|string',
            'vendor_id' => 'sometimes|exists:users,id',
        ]);

        $pointSale->update($validated);

        return response()->json($pointSale);
    }

    public function destroy(PointSale $pointSale)
    {
        $pointSale->delete();
        return response()->json(['message' => 'Pmes deleted successfully']);
    }
}
