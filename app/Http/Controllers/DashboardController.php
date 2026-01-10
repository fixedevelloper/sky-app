<?php


namespace App\Http\Controllers;


use App\Http\Helpers\Helpers;
use App\Models\Category;
use App\Models\Paiement;
use App\Models\Partner;
use App\Models\PointSale;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{

    public function index()
    {
        return view('admin.dashboard');
    }
    public function vendors(Request $request)
    {
      //  $vendors=User::query()->where('user_type','vendor')->paginate(20);
        $vendors=PointSale::query()->with('vendor')->paginate(20);
        return view('admin.vendors',[
           // 'items'=>$vendors
        ]);
    }
    public function partners(Request $request)
    {
        if ($request->isMethod('POST')) {
            try {
            // ✅ Validation stricte
                $validated = $request->validate([
                    'name'       => 'required|string|max:255',
                    'role'    => 'required|string|in:vendor,distribute,pme,commercial,partner',
                    'phone'      => 'required|string|max:20|unique:users,phone',
                    'email'      => 'required|email|unique:users,email',
                    'password'   => 'nullable|string|min:6',
                    'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
                ]);


            $imageUrl = null;

            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('partners', 'public');
                $imageUrl = asset('storage/' . $imagePath);
            }


                DB::beginTransaction();

                // ✅ Création utilisateur
                $user = User::create([
                    'name'      => $validated['name'],
                    'phone'     => $validated['phone'],
                    'email'     => $validated['email'],
                    'password'  => Hash::make($validated['password'] ?? 'password123'),
                    'image_url' => $imageUrl,

                ]);
                $user->addRole($validated['role']);

                DB::commit();

                return redirect()
                    ->back()
                    ->with('success', '✅ Partenaire ajouté avec succès');

            } catch (\Throwable $e) {
                DB::rollBack();

                Log::error('Erreur ajout partenaire', [
                    'error' => $e->getMessage()
                ]);

                return redirect()
                    ->back()
                    ->with('error', '❌ Une erreur est survenue, veuillez réessayer');
            }
        }

        // 📌 Liste des partenaires
        $users = User::query()
            ->latest()
            ->paginate(20);

        return view('admin.partners', [
            'items' => $users,
        ]);
    }



    public function updatePartner(Request $request, $id)
    {
        // ✅ Validation
        $validated = $request->validate([
            'name'        => 'required|string|max:255',
            'phone'       => 'required|string|max:20|unique:users,phone,' . $id,
            'email'       => 'required|email|unique:users,email,' . $id,
            'categories'  => 'required|string',
            'password'    => 'nullable|string|min:6|confirmed',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            DB::beginTransaction();

            // ✅ Récupération du user + relation partner
            $user = User::where('user_type', 'partner')->findOrFail($id);
            $partner = Partner::where('user_id', $user->id)->first();

            // ✅ Si nouvelle image, supprimer l’ancienne
            if ($request->hasFile('image')) {
                if ($user->image_url) {
                    $oldImagePath = str_replace(asset('storage/') . '/', '', $user->image_url);
                    Storage::disk('public')->delete($oldImagePath);
                }

                $newImagePath = $request->file('image')->store('partners', 'public');
                $user->image_url = asset('storage/' . $newImagePath);
            }

            // ✅ Mise à jour des champs de l’utilisateur
            $user->name = $validated['name'];
            $user->phone = $validated['phone'];
            $user->email = $validated['email'];

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            // ✅ Mise à jour du partenaire
            if ($partner) {
                $partner->categories = $validated['categories'];
                $partner->save();
            } else {
                Partner::create([
                    'user_id' => $user->id,
                    'categories' => $validated['categories'],
                ]);
            }

            DB::commit();

            return redirect()
                ->back()
                ->with('success', '✅ Partenaire mis à jour avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour partenaire : ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', '❌ Une erreur est survenue. Merci de réessayer.');
        }
    }

    public function purchase()
    {

        $purchase=Paiement::query()->paginate(20);
        return view('admin.purchase',[
            'items'=>$purchase
        ]);
    }
    public function facture()
    {

        return view('admin.facture',[

        ]);
    }
    public function purchase_commercial()
    {

        //$purchase=Purchase::query()->paginate(20);
        $purchase=Paiement::query()
            ->join('purchases', 'purchases.id', '=', 'paiements.purchase_id')
            ->join('customers', 'customers.id', '=', 'purchases.customer_id')
            ->where('customers.point_sale_id','!=',null)->paginate(20);
        return view('admin.purchase-commercial',[
            'items'=>$purchase
        ]);
    }
    public function paiements(Request $request,$id)
    {
        $paiements=Paiement::query()->where('purchase_id',$id)->paginate(20);
        return view('admin.paiments',[
            'items'=>$paiements,
            'purchase'=>Purchase::find($id)
        ]);
    }
    public function pointSale(Request $request,$id)
    {
        $vendor=User::query()->find($id);
        $points=PointSale::query()->where(['vendor_id'=>$id])->get();
        return view('admin.point_sale',[
            'items'=>$points,
            'vendor'=>$vendor
        ]);
    }
    public function products(Request $request)
    {
        // 🟢 Si c’est une requête POST → ajout de produit
        if ($request->isMethod('POST')) {
            try {

                logger($request->all());
                // Validation
                $validated = $request->validate([
                    'name' => 'required|string|max:255',
                    'memory' => 'nullable|string|max:255',
                    'price' => 'required|numeric|min:0',
                    'price_leasing' => 'nullable|numeric|min:0',
                    'price_pme' => 'nullable|numeric|min:0',
                    'price_distribute' => 'nullable|numeric|min:0',
                    'price_commercial' => 'nullable|numeric|min:0',
                    'category_id' => 'required|exists:categories,id',
                    'image_url' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
                ]);

                // Gestion de l'image si présente
                if ($request->hasFile('image_url')) {
                  //  $validated['image_url'] = $request->file('image_url')->store('products', 'public');
                    $validated['image_url'] = Storage::url(
                        $request->file('image_url')->store('products', 'public')
                    );

                }

                // Création du produit
                Product::create($validated);

                // Redirection avec message de succès
                return redirect()
                    ->back()
                    ->with('success', '✅ Produit ajouté avec succès !');
            }catch (\Exception $exception){
                logger($exception->getMessage());
                return redirect()
                    ->back()
                    ->with('failed', 'Ajout echoue !');
            }
        }

        // 🔵 Sinon (GET) → on affiche la liste
        $produits = Product::query()->latest()->paginate(20);

        return view('admin.product', [
            'items'       => $produits,
            'categories'  => Category::all(),
        ]);
    }

    public function categories(Request $request)
    {
        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'name' => 'required|string|unique:categories,name',
            ]);

            Category::create([
                'name' => $validated['name']
            ]);

            return redirect()->back()->with('success', 'Catégorie créée avec succès');
        }

        $categories = Category::query()->paginate(20);

        return view('admin.category', [
            'items' => $categories,
        ]);
    }

}
