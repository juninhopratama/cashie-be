<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Exception;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $stores = Store::all();
            if ($stores) {
                return response()->json([
                    'data' => $stores,
                    'message' => 'Retrieved Successfully'
                ], 200);
            }else{
                return response()->json([
                    'data' => $stores,
                    'message' => 'No Data Found'
                ], 204);
            };
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
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
        try {
            $phoneNumber = $request->input('phone');

            // Check if phone number already exists in the database
            $existingStore = Store::where('phone', $phoneNumber)->get();
            if ($existingStore->count() > 0) {
                return response()->json([
                    'message' => 'Phone number already exists'
                ], 409);
            }

            $createdStore = Store::create($request->all());
            return response()->json([
                'data' => $createdStore,
                'message' => 'Created Successfully'
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }


    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $storeById = Store::find($id);
            if ($storeById) {
                return response()->json([
                    'data' => $storeById,
                    'message' => 'Retrieved Successfully'
                ], 200);
            }else{
                return response()->json([
                    'data' => $storeById,
                    'message' => 'No Data Found'
                ], 204);
            };
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $store = Store::find($id);
            if ($store) {
                // $store->update([
                //     'store_name' => $request->store_name,
                //     'owner_name' => $request->owner_name,
                //     'email' => $request->email,
                //     'phone' => $request->phone,
                //     'password' => $request->password,
                //     'image' => $request->image,
                //     'qr' => $request->qr
                // ]);

                $data = array_filter([
                    'store_name' => $request->input('store_name'),
                    'owner_name' => $request->input('owner_name'),
                    'email' => $request->input('email'),
                    'phone' => $request->input('phone'),
                    'password' => $request->input('password'),
                    'image' => $request->input('image'),
                    'qr' => $request->input('qr')
                ]);

                $store->update($data);

                return response()->json([
                    'data' => $store,
                    'message' => 'Updated Successfully'
                ], 200);
            }else{
                return response()->json([
                    'data' => $store,
                    'message' => 'No Data Found'
                ], 404);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Store  $store
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $store = Store::find($id);
        if ($store) {
            Store::destroy($id);
            return response()->json([
                'data' => $store,
                'message' => 'Deleted'
            ], 200);
        }else {
            return response()->json([
                'data' => $store,
                'message' => 'No Data Found'
            ], 204);
        }
    }
}
