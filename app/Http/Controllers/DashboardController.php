<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Carbon\Carbon;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'product' => Product::count(),
            'product_empty' => Product::where('qty', 0)->count(),
            'transaction' => Transaction::whereMonth('created_at', date('m'))->count(),
            'monthly_transaction' => Transaction::selectRaw('MONTH(created_at) as month, COUNT(*) as count')->whereYear('created_at', date('Y'))->groupBy('month')->orderBy('month', 'asc')->get()
                ->map(function ($item) {
                    $item->month = Carbon::create()->month($item->month)->format('F');

                    return $item;
                }),
        ]);
    }
}
