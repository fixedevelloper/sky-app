<?php


namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{

    public function index(Request $request)
    {
        // code => champ prix
        $codePriceMap = [
            'VENDOR'     => 'price_leasing',
            'DIST'       => 'price_distribute',
            'PME'        => 'price_pme',
            'COM'        => 'price_commercial',
        ];

        // ✅ Par défaut : prix customer
        $priceField = 'price';

        // ✅ Si code présent
        if ($request->filled('code')) {
            $code = strtoupper($request->get('code'));

            if (isset($codePriceMap[$code])) {
                $priceField = $codePriceMap[$code];
            }
        }

        logger('PRICE FIELD', [$priceField]);

        $products = Product::with('category')->get();

        $data = $products->map(function ($product) use ($priceField) {
            return [
                'id'       => $product->id,
                'name'     => $product->name,
                'image'    => $product->image_url,
                'price'    => $product->$priceField ?? $product->price,
                'category' => $product->category?->name,
        ];
    });

        return response()->json($data);
    }


    public function index2()
    {
        $user = Auth::user();

        // Mapping rôle => champ prix
        $rolePriceMap = [
            'vendor'      => 'price_leasing',
            'distribute'  => 'price_distribute',
            'pme'         => 'price_pme',
            'commercial'  => 'price_commercial',
            'customer'    => 'price',
        ];

        // ✅ Par défaut : prix customer
        $priceField = 'price';

        // ✅ Si utilisateur connecté
        if ($user) {
            foreach ($rolePriceMap as $role => $field) {
                if ($user->hasAnyRole([$role])) {
                    $priceField = $field;
                    break;
                }
            }
        }
        logger($user);
        logger($priceField);
        $products = Product::with('category')->get();

        $data = $products->map(function ($product) use ($priceField) {
            return [
                'id'       => $product->id,
                'name'     => $product->name,
                'image'    => $product->image_url,
                'price'    => $product->$priceField ?? $product->price,
                'category' => $product->category?->name,
        ];
    });

        return response()->json($data);
    }
}
