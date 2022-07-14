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
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/product', [ProductController::class, 'index'])->name('product');
Route::get('/product/manage', [ProductController::class, 'manage'])->name('product.manage');
Route::get('/product/manage/{id}', [ProductController::class, 'manage'])->name('product.manage');
Route::post('/product/process', [ProductController::class, 'process'])->name('product.process');
Route::delete('/product/delete/{id}', [ProductController::class, 'delete'])->name('product.delete');
Route::post('/product/updateAjax', [ProductController::class, 'updateAjax'])->name('product.updateAjax');