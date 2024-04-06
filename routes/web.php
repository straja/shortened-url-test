<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IndexController;

Route::get('/', function () {
    return inertia('Index/Index');
});

Route::post('set-url', [IndexController::class, 'store']);
Route::get('get-urls', [IndexController::class, 'getUrls']);
