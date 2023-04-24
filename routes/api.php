<?php

use App\Http\Controllers\HistoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::resource('/store', StoreController::class);

Route::get('/item/{store_id}', [ItemController::class, 'getProductByStore']);

Route::get('/item', [ItemController::class, 'getAll']);

Route::post('/login', [LoginController::class, 'login']);

Route::post('/item/singleitem', [ItemController::class, 'getProductByIdAndStoreId']);

Route::post('/item', [ItemController::class, 'createItem']);

Route::put('/item', [ItemController::class, 'updateItem']);

Route::delete('/item', [ItemController::class, 'deleteItem']);

Route::post('/transaction/pre', [TransactionController::class, 'preTransaction']);

Route::post('/transaction/finalize', [TransactionController::class, 'finalizeTransaction']);

Route::get('/history/{store_id}', [HistoryController::class, 'getHistoryByStoreId']);

Route::get('/history/detail/{trx_id}', [HistoryController::class, 'getHistoryByTrxId']);

Route::get('/check-join', [HistoryController::class, 'checkJoin']);

Route::get('/hello', [LoginController::class, 'sanity']);
