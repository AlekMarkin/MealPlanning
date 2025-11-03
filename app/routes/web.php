<?php

//used this for test connection let's be here some times
/*
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GoalController;

Route::get('/', [HomeController::class, 'index']);
Route::get('/health', [HomeController::class, 'health']);
*/

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoalController;


//Authorisation and HomePage

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/', [AuthController::class, 'home'])->name('home');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

//Goals
Route::get('/goals', [GoalController::class, 'index']);
Route::get('/goals/create', [GoalController::class, 'create']);
Route::post('/goals/store', [GoalController::class, 'store']);
Route::get('/goals/{id}/edit', [GoalController::class, 'edit']);
Route::post('/goals/{id}/update', [GoalController::class, 'update']);
Route::post('/goals/{id}/delete', [GoalController::class, 'destroy']);