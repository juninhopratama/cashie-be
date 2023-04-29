<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getProductByStore($store_id)
    {
        $items = Item::where('store_id', $store_id)
            ->orderBy('id', 'DESC')
            ->get();
        return response()->json([
            'data' => $items,
            'message' => 'Retrieved Successfully'
        ], 200);
    }

    public function getProductByStoreAndName(Request $request)
    {
        $items = Item::where('store_id', $request->store_id)
            ->where('name', 'like', '%'.$request->search.'%')
            ->orderBy('id', 'DESC')
            ->get();
        return response()->json([
            'data' => $items,
            'message' => 'Retrieved Successfully here'
        ], 200);
    }

    public function getAll()
    {
        $items = Item::all();
        return response()->json([
            'data' => $items,
            'message' => 'Retrieved Successfully'
        ], 200);
    }

    public function getProductByIdAndStoreId(Request $request)
    {
        $productId = $request->product_id;
        $storeId = $request->store_id;
        $item = Item::where([
            'id' => $productId,
            'store_id' => $storeId
        ])->first();

        if ($item) {
            return response()->json([
                'data' => $item,
                'message' => 'Retrieved Successfully'
            ], 200);
        }else{
            return response()->json([
                'message' => 'No Item Found'
            ], 404);
        }
    }

    public function createItem(Request $request)
    {
        $item = Item::create($request->all());
        if ($item) {
            return response()->json([
                'data' => $item,
                'message' => 'Created Successfully'
            ], 201);
        }else{
            return response()->json([
                'data' => $item,
                'message' => 'Failed creating item'
            ], 409);
        };
    }

    public function updateItem(Request $request)
    {
        $productId = $request->product_id;
        $storeId = $request->store_id;
        $item = Item::where([
            'id' => $productId,
            'store_id' => $storeId
        ])->first();

        if ($item->count() > 0) {
            // $item->update([
            //     'name' => $request->name,
            //     'description' => $request->description,
            //     'initial_price' => $request->initial_price,
            //     'selling_price' => $request->selling_price,
            //     'stock' => $request->stock,
            //     'image' => $request->has('image') ? $request->image : $item->image
            // ]);

            $data = array_filter([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'initial_price' => $request->input('initial_price'),
                'selling_price' => $request->input('selling_price'),
                'stock' => $request->input('stock'),
                'image' => $request->input('image')
            ]);

            $item->update($data);
            
            return response()->json([
                'data' => $item,
                'message' => 'Updated Successfully'
            ], 200);
        }else {
            return response()->json([
                'message' => 'No Item Found'
            ], 404);
        }
    }

    public function deleteItem(Request $request)
    {
        $productId = $request->product_id;
        $storeId = $request->store_id;
        $item = Item::where([
            'id' => $productId,
            'store_id' => $storeId
        ])->first();

        if ($item) {
            $item->delete();
            return response()->json([
                'data' => $item,
                'message' => 'Deleted Successfully'
            ], 200);
        }else {
            return response()->json([
                'message' => 'No Item Found'
            ], 404);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Item $item)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Item  $item
     * @return \Illuminate\Http\Response
     */
    public function destroy($storeId, $productId)
    {

    }
}
