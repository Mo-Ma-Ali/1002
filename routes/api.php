<?php

use App\Http\Controllers\FavoritesController;
use App\Http\Controllers\PharmaceuticalController;
use App\Http\Controllers\UserController;
use App\Models\Pharmaceutical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('signup', [UserController::class, 'store']);
Route::post('login', [UserController::class, 'login']);
Route::delete('logout',[UserController::class, 'logout']);

//get the info of the user by the token
Route::get('user', [UserController::class, 'getUser']);

//to check if the user has a token valuoe
Route::middleware('checkToken')->group(function () {

//Check if the user is a warehouse and then store the pharmaceutical data in the database
Route::middleware('checkWarehouse')->post('store',[PharmaceuticalController::class,'store']);


Route::middleware('checkWarehouse')->post('edit',[PharmaceuticalController::class,'quantity']);

//search the pharamceutical by the calssification
Route::post('saerch',[PharmaceuticalController::class,'serch']);

//search the pharamceutical by the company name
Route::post('saerchComp',[PharmaceuticalController::class,'serchCompany']);
//Route::get('getClass/{calssification}',[PharmaceuticalController::class,'getByCalss']);

//return all the calssifications in the column in the database
Route::get('getAll',[PharmaceuticalController::class,'getAllClass']);

//return all the medicine that has the same classification
Route::get('getAllMedicine',[PharmaceuticalController::class,'getTheClass']);

//review all the orders
Route::middleware('checkWarehouse')->get('/orders', [OrderController::class, 'retrieveOrders']);

//erturn the users that sended the orders
Route::middleware('checkWarehouse')->get('/getClients',[OrderController::class,'getClients']);

//return all the user dates of order by th id
Route::middleware('checkWarehouse')->post('/getDate',[OrderController::class,'getDate']);

//return all the user dates without id only token with middleware to check if it is a pharmacy
Route::middleware('checkPharmacy')->get('/getTDate',[OrderController::class,'getToken']);

//return the order by user id and the date created at
Route::post('getOrder',[OrderController::class,'getOrder']);

//to send an order
Route::middleware('checkPharmacy')->post('/order', [OrderController::class, 'store']);

//change the order status
Route::middleware('checkWarehouse')->post('/status',[OrderController::class,'status']);

//cahnge the order to paid
Route::middleware('checkWarehouse')->put('/payment', [OrderController::class, 'payment']);
});
