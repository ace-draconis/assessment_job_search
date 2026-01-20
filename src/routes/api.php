<?php

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

// SQL Optimization Demo Routes
Route::get('/search/optimized', 'JobController@searchOptimized');
Route::get('/search/slow', 'JobController@searchSlow');
Route::get('/search/compare', 'JobController@compare');

