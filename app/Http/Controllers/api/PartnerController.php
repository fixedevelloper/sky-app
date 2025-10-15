<?php


namespace App\Http\Controllers\api;


use App\Models\Partner;

class PartnerController
{

    public function index()
    {
        return response()->json(Partner::with('user')->get());
    }
}
