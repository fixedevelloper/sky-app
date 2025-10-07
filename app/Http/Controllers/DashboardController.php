<?php


namespace App\Http\Controllers;


use App\Http\Helpers\Helpers;
use App\Models\Category;
use App\Models\Paiement;
use App\Models\PointSale;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{

    public function index()
    {
        return view('admin.dashboard');
    }
    public function vendors(Request $request)
    {
        $vendors=User::query()->where('user_type','vendor')->paginate(20);
        return view('admin.vendors',[
            'items'=>$vendors
        ]);
    }
    public function partners()
    {
        $partners=User::query()->where('user_type','partner')->paginate(20);
        return view('admin.partners',[
            'items'=>$partners,
            'categories'=>Category::all()
        ]);
    }
    public function purchase()
    {
        $purchase=Purchase::query()->paginate(20);
        return view('admin.purchase',[
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
