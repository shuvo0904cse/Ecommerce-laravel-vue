<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProductController;

Route::group(['prefix' => 'v1'], function () {
    //login
    Route::post('login', [AuthController::class, 'login'])->name("login");

    Route::group(['middleware' => 'auth:api'], function() {
        //User
        Route::get('user-lists', [UserController::class, 'index'])->name("user.lists");
        Route::post('store-user', [UserController::class, 'store'])->name("user.store");
        Route::put('update-user/{id}', [UserController::class, 'update'])->name("user.update");
        Route::delete('delete-user/{id}', [UserController::class, 'delete'])->name("user.delete");

        //Product
        Route::get('product-lists', [ProductController::class, 'index'])->name("product.lists");
        Route::post('store-product', [ProductController::class, 'store'])->name("product.store");
        Route::put('update-product/{id}', [ProductController::class, 'update'])->name("product.update");
        Route::delete('delete-product/{id}', [ProductController::class, 'delete'])->name("product.delete");

        //Logged user
        Route::get('logout', [AuthController::class, 'logout'])->name("user.logout");
        Route::get('user', [AuthController::class, 'user'])->name("logged.user.details");
    });
});
