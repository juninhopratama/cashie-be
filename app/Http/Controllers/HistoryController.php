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

        setlocale(LC_TIME, 'id_ID');

        $trxData = [];

        foreach ($trx as $t) {
            $trx_push = (object) [
                'id' => $t->id,
                'store_id' => $t->store_id,
                'bill' => $t->bill,
                'total_cash' => $t->total_cash,
                'change' => $t->change,
                'payment_method' => $t->payment_method,
                'formatted_date' => $t->created_at->format('d F Y, H:i'),
                'created_at' => $t->created_at,
                'updated_at' => $t->updated_at
            ];

            array_push($trxData, $trx_push);
        }

        return response()->json([
            'data' => $trxData,
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
        setlocale(LC_TIME, 'id_ID');
        $formattedDate = $trx->created_at->format('d F Y, H:i');
        $store = Store::where('id', $catch_id)->first();

        return response()->json([
            'store_name' => $store->store_name,
            'products' => $products,
            'bill' => $formattedBill,
            'total_cash' => $formattedCash,
            'change' => $formattedChange,
            'payment_method' => $trx->payment_method,
            'timestamp' => $trx->created_at,
            'formatted_date' => $formattedDate,
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

        if (!$trx) {
            return response()->json([
                'bill' => "Rp0",
                'payment_method' => "Total Payment",
                'product_count' => "Data Not Available",
                'formatted_date' => "-",
                'message' => 'Retrieved Successfully'
            ], 200);
        }

        $productTrx = ProductTransaction::where('trx_id', $trx->id)->sum('total');

        setlocale(LC_TIME, 'id_ID');
        $formattedDate = $trx->created_at->format('d F Y, H:i');

        $formattedBill = 'Rp' . number_format((int) $trx->bill, 0, ',', '.');

        return response()->json([
            'bill' => $formattedBill,
            'payment_method' => $trx->payment_method,
            'product_count' => $productTrx . " Produk",
            'formatted_date' => $formattedDate,
            'message' => 'Retrieved Successfully'
        ], 200);
    }
}
