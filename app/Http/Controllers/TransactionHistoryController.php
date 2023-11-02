<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TransactionHistoryController extends Controller
{
    public function index(Request $request){
        return Inertia::render('TransactionHistory/Index',[
            'histories' => Transaction::with('detail_transaction')->when($request->query('date') != null, function($query) use ($request){
                $query->whereDate('created_at',$request->query('date'));
            })->simplePaginate(10)
        ]);
    }

}
