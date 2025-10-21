<?php


namespace App\Http\Controllers\api;



use App\Http\Controllers\Controller;
use App\Models\PointSale;
use App\Models\User;
use App\Service\MomoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PointSaleController extends Controller
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
        try {
            $validated = $request->validate([
                'name' => 'required|string',
                'vendor_name' => 'required|string',
                'vendor_phone' => 'required|string',
                'vendor_activity' => 'required|string',
                'activity' => 'nullable|string',
                'localisation' => 'nullable|string',
                'vendor_image' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
                'image_url' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
                'image_cni_verso' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'image_cni_recto' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
                'image_doc_fiscal' => 'nullable|image|mimes:jpg,jpeg,png,gif|max:2048',
            ]);
           // logger($validated);
            DB::beginTransaction();

            // ✅ Vérifie si le vendeur existe
            $vendor = User::firstWhere('phone', $validated['vendor_phone']);

            if (!$vendor) {
                $vendor = User::create([
                    'name' => $validated['vendor_name'],
                    'phone' => $validated['vendor_phone'],
                    'activity' => $validated['vendor_activity'],
                    'user_type' => 'vendor',
                    'email' => $validated['vendor_phone'] . '@sky.com',
                    'password' => bcrypt('12345678@9'),
                    'image_url' => $this->storeImage($request, 'vendor_image', 'vendors'),
                    'image_cni_recto' => $this->storeImage($request, 'image_cni_recto', 'cni'),
                    'image_cni_verso' => $this->storeImage($request, 'image_cni_verso', 'cni'),
                ]);
            }

            // ✅ Création du point de vente

            $referenceId = Str::uuid()->toString();
            if ($request->platform=='ORANGE'){
                throw new \Exception("La plateforme Orange Money est temporairement en maintenance. Veuillez utiliser une autre option comme MTN.");

            }
           $status = $this->momo->requestToPay($referenceId, $request->vendor_phone, 15000);

            if ($status == 202) {

            } else {
                throw new \Exception("Le paiement a échoué. Veuillez vérifier les points suivants :
- Le numéro de téléphone est valide.
- Le solde est suffisant.
- L'opérateur est correct.");
            }
            $pointSale = PointSale::create([
                'name' => $validated['name'],
                'activity' => $validated['activity'] ?? null,
                'localisation' => $validated['localisation'] ?? null,
                'operator' => $request->platform,
                'referenceId' => $referenceId,
                'image_url' => $this->storeImage($request, 'image_url', 'point_sales'),
                'image_doc_fiscal' => $this->storeImage($request, 'image_doc_fiscal', 'point_sales'),
                'vendor_id' => $vendor->id
            ]);
            DB::commit();

            return response()->json([
                'referenceId' => $referenceId,
                'name'=>$pointSale->name,
                'activity'=>$pointSale->vendor->activity,
                'localisation'=>$pointSale->localisation,
                'image_url'=>$pointSale->image_url,
                'image_doc_fiscal'=>$pointSale->image_doc_fiscal,
                'vendor_name'=>$pointSale->vendor->name,
                'phone'=>$pointSale->vendor->phone,
                'image_cni_recto'=>$pointSale->vendor->image_cni_recto,
                'image_cni_verso'=>$pointSale->vendor->image_cni_verso,
            ], 201);

        } catch (\Exception $exception) {
            logger($exception->getMessage());
            DB::rollBack();
            if ($exception->getCode() == 23000) { // Violation de contrainte unique
                return response()->json([
                    'error' => "⚠️ Le nom du point de vente existe déjà. Veuillez choisir un autre nom."
                ], 409); // 409 = Conflit
            }

            // Autres erreurs SQL
            return response()->json([
                'error' => 'Erreur : ' . $exception->getMessage()
            ], 400);
        }
    }

    /**
     * Stocke un fichier uploadé dans storage/app/public et retourne l'URL publique
     */
    private function storeImage(Request $request,  $field,  $folder): ?string
    {
        if ($request->hasFile($field)) {
            logger($field);
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
        return response()->json(['message' => 'Point Sale deleted successfully']);
    }
}
