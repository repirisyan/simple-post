<?php

namespace App\Http\Controllers;

use App\Models\DetailTransaction;
use App\Models\Product;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class TransactionController extends Controller
{
    private $product;

    public function __construct()
    {
        $this->product = new Product();
    }

    public function index(){
        return Inertia::render('Transaction/Index',[
            'products' => $this->product->getListProduct()
        ]);
    }

    public function searchProduct(Request $request)
    {
        $request->validate([
            'product_code' => 'required|exists:products,product_code'
        ]);
        try {
            return response()->json(Product::where('product_code',$request->product_code)->first());
        } catch (Exception $e) {
            return response()->json(404);
        }
    }

    public function pay(Request $request){
        try {
            $invoice = 'INV'.date("ymdhis");
            DB::transaction(function() use ($request,$invoice){
                $id = Transaction::create([
                    'invoice' => $invoice,
                    'user_id' => auth()->user()->id,
                    'grand_total' => $request[2],
                    'grand_pay' => $request[1]
                ])->id;
                foreach ($request[0] as $item) {
                    DetailTransaction::create([
                        'product_id' => $item['id'],
                        'transaction_id' => $id,
                        'qty' => $item['qty'],
                        'price' => $item['price']
                    ]);
                    Product::where('id',$item['id'])->decrement('qty',$item['qty']);
                }
            });
            return response()->json(['success','Pembayaran berhasil']);
        } catch (Exception $e) {
            return response()->json(['error','Terjadi kesalahan saat melakukan pembayaran']);

        }
    }


}
