<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ProductTransaction;
use App\Models\Store;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class HistoryController extends Controller
{
    public function getHistoryByStoreId($store_id)
    {
        $trx = Transaction::where('store_id', $store_id)
            ->orderBy('created_at', 'DESC')
            ->get();
        return response()->json([
            'data' => $trx,
            'message' => 'Retrieved Successfully'
        ], 200);
    }

    public function getHistoryByTrxId($trx_id)
    {
        $res = DB::table('product_transactions')
            ->where('trx_id', '=', $trx_id)
            ->join('items', 'product_transactions.product_id', '=', 'items.id')
            ->get();

        $products = [];

        $catch_id = 0;

        foreach ($res as $r) {
            $product_push = (object) [
                'product_name' => $r->name,
                'total' => $r->total,
                'total_price' => $r->total_price
            ];

            $catch_id = $r->store_id;

            array_push($products, $product_push);
        }

        $trx = Transaction::where('id', $trx_id)->first();
        $formattedChange = 'Rp. ' . number_format($trx->change, 0, ',', '.');
        $formattedBill = 'Rp. ' . number_format($trx->bill, 0, ',', '.');
        $formattedCash = 'Rp. ' . number_format($trx->total_cash, 0, ',', '.');
        $store = Store::where('id', $catch_id)->first();

        return response()->json([
            'store_name' => $store->store_name,
            'products' => $products,
            'bill' => $formattedBill,
            'total_cash' => $formattedCash,
            'change' => $formattedChange,
            'timestamp' => $trx->created_at,
            'message' => 'Retrieved Successfully'
        ], 200);
    }

    public function checkJoin()
    {
        $res = DB::table('product_transactions')
            ->where('trx_id', '=', 3)
            ->join('items', 'product_transactions.product_id', '=', 'items.id')
            ->get();
        dd($res);
    }

    public function getLatestHistory($store_id)
    {
        $trx = Transaction::where('store_id', $store_id)
            ->orderBy('created_at', 'DESC')
            ->first();

        $productTrx = ProductTransaction::where('trx_id', $trx->id)->get();

        setlocale(LC_TIME, 'id_ID');
        $formattedDate = $trx->created_at->format('d F Y, H:i');

        return response()->json([
            'data' => $trx,
            'product_count' => $productTrx->count('id'),
            'formatted_date' => $formattedDate,
            'message' => 'Retrieved Successfully'
        ], 200);
    }
}
