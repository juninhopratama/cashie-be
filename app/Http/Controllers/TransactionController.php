<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\ProductTransaction;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function preTransaction(Request $request)
    {
        $store_id = $request->store_id;
        $products = $request->products;

        $bill = 0;
        foreach ($products as $p) {
            $item = Item::where([
                'id' => $p['product_id'],
                'store_id' => $store_id
            ])->first();

            $bill += $item->selling_price * $p['total'];
            $formattedBill = 'Rp. ' . number_format($bill, 0, ',', '.');
        }

        return response()->json([
            'bill' => $bill,
            'formatted_bill' => $formattedBill,
            'message' => 'Retrieved Successfully'
        ], 200);
    }

    public function finalizeTransaction(Request $request)
    {
        $store_id = $request->store_id;
        $products = $request->products;
        $payment_method = $request->payment_method;
        $total_cash = $request->total_cash;

        $bill = 0;

        // money checking
        foreach ($products as $p) {
            $item = Item::where([
                'id' => $p['product_id'],
                'store_id' => $store_id
            ])->first();

            $currentStock = $item->stock;
            if ($currentStock <= 0 || $currentStock < $p['total']) {
                return response()->json([
                    'insufficient_stock' => $item->name,
                    'message' => 'Insufficient stock'
                ], 400);
            }

            $bill += $item->selling_price * $p['total'];
        }

        $subtotal = $bill;

        $bill = $bill + ($bill * 0.1);

        $change = $total_cash - $bill;

        if ($change < 0) {
            return response()->json([
                'message' => 'Insufficient money'
            ], 400);
        }

        $trx = Transaction::create([
            'store_id' => $store_id,
            'bill' => 0,
            'total_cash' => $total_cash,
            'change' => 0,
            'payment_method' => $payment_method
        ]);

        $resProducts = [];

        // create product transaction record
        foreach ($products as $p) {
            $item = Item::where([
                'id' => $p['product_id'],
                'store_id' => $store_id
            ])->first();

            $newStock = $item->stock - $p['total'];
            $item->update([
                'stock' => $newStock
            ]);
            $total_price = $item->selling_price * $p['total'];

            ProductTransaction::create([
                'product_id' => $item->id,
                'trx_id' => $trx->id,
                'total' => $p['total'],
                'total_price' => $total_price
            ]);

            $push_product = (object) [
                'product_name' => $item->name,
                'total' => $p['total'],
                'total_price' => $total_price
            ];

            array_push($resProducts, $push_product);
        }

        $trx->update([
            'bill' => $bill,
            'change' => $change,
        ]);

        $formattedBill = 'Rp. ' . number_format($bill, 0, ',', '.');
        $formattedChange = 'Rp. ' . number_format($change, 0, ',', '.');
        // Set the locale to Indonesian
        setlocale(LC_TIME, 'id_ID');

        // Get the current date and time
        $date = date('d F Y H:i');
        return response()->json([
            'products' => $resProducts,
            'tax' => '10%',
            'total_cash' => $total_cash,
            'subtotal' => $subtotal,
            'bill' => $bill,
            'change' => $change,
            'formatted_bill' => $formattedBill,
            'formatted_change' => $formattedChange,
            'transaction_date' => $date,
            'message' => 'Retrieved Successfully'
        ], 200);
    }
}
