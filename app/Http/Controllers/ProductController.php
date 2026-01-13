<?php


namespace App\Http\Controllers;


use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
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

    public function edit(Product $product)
    {
        return view('admin.product-edit', [
            'product'    => $product,
            'categories' => Category::all(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        try {
            $validated = $request->validate([
                'name'               => 'required|string|max:255',
                'memory'             => 'nullable|string|max:255',
                'price'              => 'required|numeric|min:0',
                'price_leasing'      => 'nullable|numeric|min:0',
                'price_pme'          => 'nullable|numeric|min:0',
                'price_distribute'   => 'nullable|numeric|min:0',
                'price_commercial'   => 'nullable|numeric|min:0',
                'category_id'        => 'required|exists:categories,id',
                'image_url'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            ]);

            // 🖼 Gestion image
            if ($request->hasFile('image_url')) {

                // Supprimer l’ancienne image
                if ($product->image_url) {
                    $oldPath = str_replace('/storage/', '', $product->image_url);
                    Storage::disk('public')->delete($oldPath);
                }

                $path = $request->file('image_url')->store('products', 'public');
                $validated['image_url'] = Storage::url($path);
            }

            // 🔄 Update
            $product->update($validated);

            return redirect()
                ->route('admin.products.edit', $product)
                ->with('success', '✅ Produit mis à jour avec succès');

        } catch (\Exception $e) {
            logger($e->getMessage());

            return back()->with('failed', '❌ Échec de la mise à jour');
        }
    }

}
