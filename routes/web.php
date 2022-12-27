<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'prevent-back-history'],function(){

Auth::routes();

    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    Route::post('categorylist', [App\Http\Controllers\HomeController::class, 'categorylist'])->name('categorylist');
    Route::post('storecategory', [App\Http\Controllers\HomeController::class, 'storecategory'])->name('storecategory');
    Route::post('categorydetail', [App\Http\Controllers\HomeController::class, 'categorydetail'])->name('categorydetail');
    Route::post('categorydelete', [App\Http\Controllers\HomeController::class, 'categorydelete'])->name('categorydelete');
    Route::post('loadtree', [App\Http\Controllers\HomeController::class, 'loadtree'])->name('loadtree');

});