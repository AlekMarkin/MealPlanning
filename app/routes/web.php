<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\RecipeController;

// Home (guest + auth see the same landing page)
Route::get('/', [HomeController::class, 'index'])->name('home');

// Show the same homepage for GET /login and /register so the links work
Route::get('/login', [HomeController::class, 'index'])->name('login.form');
Route::get('/register', [HomeController::class, 'index'])->name('register.form');

// Auth actions (POST)
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Foods (no show route)
Route::resource('foods', FoodController::class)->except(['show']);

// Recipes (resource without show, then explicit show + item routes)
Route::resource('recipes', RecipeController::class)->except(['show']);
Route::get('/recipes/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');
Route::post('/recipes/{recipe}/items', [RecipeController::class, 'addItem'])->name('recipes.items.store');
Route::delete('/recipes/{recipe}/items/{item}', [RecipeController::class, 'removeItem'])->name('recipes.items.destroy');

// Goals 
Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
Route::get('/goals/create', [GoalController::class, 'create'])->name('goals.create');
Route::post('/goals/store', [GoalController::class, 'store'])->name('goals.store');
Route::get('/goals/{id}/edit', [GoalController::class, 'edit'])->name('goals.edit');
Route::post('/goals/{id}/update', [GoalController::class, 'update'])->name('goals.update');
Route::post('/goals/{id}/delete', [GoalController::class, 'destroy'])->name('goals.destroy');
