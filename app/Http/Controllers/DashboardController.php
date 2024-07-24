<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(){
        return Inertia::render('Dashboard',[
            'product' => Product::count(),
            'product_empty' => Product::where('qty',0)->count(),
            'transaction' => Transaction::whereMonth('created_at',date('m'))->count()
        ]);
    }
}
