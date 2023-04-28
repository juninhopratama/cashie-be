<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function getDashboard($store_id)
    {
        $trx = DB::table('transactions')->where('store_id', $store_id);
        $gSales = (int) $trx->sum('bill');

        $totalProfit = DB::table('transactions')
            ->select(DB::raw('SUM(
                    transactions.bill - (
                        SELECT SUM(product_transactions.total * items.initial_price)
                        FROM product_transactions
                        INNER JOIN items ON product_transactions.product_id = items.id
                        WHERE product_transactions.trx_id = transactions.id
                    )
                ) AS total_profit'))
            ->where('transactions.store_id', '=', $store_id)
            ->first()
            ->total_profit;

        $resTotalNet = Transaction::select('transactions.id', 'transactions.bill', DB::raw('SUM(product_transactions.total * items.initial_price) as net'))
            ->join('product_transactions', 'transactions.id', '=', 'product_transactions.trx_id')
            ->join('items', 'product_transactions.product_id', '=', 'items.id')
            ->where('transactions.store_id', '=', 1)
            ->groupBy('transactions.id', 'transactions.bill')
            ->get();

        $sumTotalNet = 0;
        foreach ($resTotalNet as $r) {
            $sumTotalNet += $r->net;
        }

        $formattedgSales = 'Rp' . number_format($gSales, 0, ',', '.');
        $formattedTotalProfit = 'Rp' . number_format((int) $totalProfit, 0, ',', '.');
        $formattedTotalNet = 'Rp' . number_format((int) $sumTotalNet, 0, ',', '.');

        return response()->json([
            'gross_sales' => $formattedgSales,
            'total_sales' => $formattedTotalNet,
            'profit' => $formattedTotalProfit,
            'trx_count' => $trx->count('id'),
            'message' => 'Retrieved Successfully'
        ], 200);
    }
}
