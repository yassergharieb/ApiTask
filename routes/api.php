<?php

use App\Http\Controllers\PostController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\UserLoginController;
use App\Http\Controllers\VerificationCodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*
 *  Register and login routes
 */
Route::group(['prefix' => 'users' , 'name' => 'user'] , function () {
    Route::post('/register',  [RegisterController::class , 'register']);
    Route::post('/login',  [UserLoginController::class , 'login'])->middleware('VerifiedUser');
    Route::post("/verify" , [VerificationCodeController::class , 'verify'])->name('.verification')->middleware('auth:sanctum');
    Route::post("/delete_account" , [UserLoginController::class , "destroy"])->middleware("auth:sanctum");
});


Route::group(['middleware' =>  ['auth:sanctum'
    , 'VerifiedUser'
]] , function () {

    Route::resource('tags' , TagController::class)->only(['show' , "destroy" , "store"]);
    Route::post("/tags/{tag}" , [TagController::class , "update"]);
    Route::resource('posts' , PostController::class)->only(['show' , "destroy" , "store"]);
    Route::post("/posts/{post}" , [PostController::class , "update"]);
    Route::get('stats' , [StatsController::class , 'getInfo']);
});
