<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaction;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class TransactionHistoryController extends Controller
{
    public function index(Request $request){
        return Inertia::render('TransactionHistory/Index',[
            'histories' => Transaction::with('detail_transaction')->when($request->query('from_date') != null && $request->query('until_date') != null, function($query) use ($request){
                $query->whereBetween('created_at',[$request->query('from_date'),$request->query('until_date')]);
            })->simplePaginate(10)
        ]);
    }

    public function print(Request $request){
        $data = Transaction::with('detail_transaction.product')->whereBetween('created_at',[$request->query('from_date'),$request->query('until_date')])->orderBy('created_at','asc')->get();
        $pdf = Pdf::loadView('pdf.riwayat_transaksi', ['data' => $data,'from_date' => $request->query('from_date'),'until_date' => $request->query('until_date')]);
        return $pdf->download('Riwayat Transaksi '.$request->query('from_date').' s/d '.$request->query('until_date').'.pdf');
    }

    public function transactionDetail($transaction_id){
        return response()->json(DetailTransaction::with('product:id,nama')->where('transaction_id',$transaction_id)->get());
    }

}
