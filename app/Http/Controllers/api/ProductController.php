<?php


namespace App\Http\Controllers\api;


use App\Models\Product;
use App\Models\Purchase;

class ProductController
{

    public function index()
    {
        return response()->json(Product::with('category')->get());
    }
}
