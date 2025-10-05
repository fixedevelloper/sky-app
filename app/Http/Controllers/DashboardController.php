<?php


namespace App\Http\Controllers;


use App\Http\Helpers\Helpers;
use App\Models\Paiement;
use App\Models\PointSale;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Http\Request;

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
            'items'=>$partners
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
            'items'=>$paiements
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
}
