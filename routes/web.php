<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


use Illuminate\Http\Request;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');


/**
 * Ruta para agregar tarjeta
 */
Route::get('/setup-card',function (Request $request){
    $user = \App\User::find(auth()->user()->id);
    return view('list.update-payment-method', [
        'intent' => $user->createSetupIntent()
    ]);
});

Route::post('/card-save',function (Request $request){
    $user = \App\User::find(auth()->user()->id);
    $user->updateDefaultPaymentMethod($request->get('card'));

});

/**
 * Ruta para comprar productos
 */

Route::get('/{sku}/product-buy',function (Request $request,$sku){
    $user = \App\User::find(auth()->user()->id);

    \Stripe\Stripe::setApiKey('sk_test_etZKsNtrlVCgaKMHIfR9LjVa00eCofEenr');
    $sku = \Stripe\SKU::retrieve($sku);

    $user->invoiceFor($sku->attributes->name, $sku->price, [
       // 'quantity' => 1,
    ], [
        'tax_percent' => 18,
    ]);
})->name('product-buy');
