<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

Auth::routes();
Route::get('/verify_phone',  'AuthController@showVerifyForm')->name('verify_phone');

Route::post('/verify_phone', 'AuthController@verify')->name('verify_phonenumber');

Route::get('/logout', 'Auth\LoginController@logout');
Route::get('/{component?}', 'HomeController@index')->name('home');
Route::get('/{group}/{component}/{id?}', 'HomeController@show');



Route::get('{any}', function () { 
    return view('vue_app'); 
})->where('any', '.*'); 
