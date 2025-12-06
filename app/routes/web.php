<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoalController;
use App\Http\Controllers\FoodController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\IntakeController;
use App\Http\Controllers\UserMetricsController;
use App\Http\Controllers\MealPlanController;

// Welcome landing page
Route::get('/', function () { return view('welcome'); });

// Home (guest + auth)
Route::get('/home', [HomeController::class, 'index'])->name('home');

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

// Daily intakes
Route::get('/intakes', [IntakeController::class, 'index'])->name('intakes.index');
Route::post('/intakes/food', [IntakeController::class, 'storeFood'])->name('intakes.food.store');
Route::post('/intakes/recipe', [IntakeController::class, 'storeRecipe'])->name('intakes.recipe.store');
Route::delete('/intakes/{intake}', [IntakeController::class, 'destroy'])->name('intakes.destroy');

// Goals 
Route::get('/goals', [GoalController::class, 'index'])->name('goals.index');
Route::get('/goals/create', [GoalController::class, 'create'])->name('goals.create');
Route::post('/goals/store', [GoalController::class, 'store'])->name('goals.store');
Route::get('/goals/{id}/edit', [GoalController::class, 'edit'])->name('goals.edit');
Route::post('/goals/{id}/update', [GoalController::class, 'update'])->name('goals.update');
Route::post('/goals/{id}/delete', [GoalController::class, 'destroy'])->name('goals.destroy');

//Biometrics
Route::get('/user-metrics', [UserMetricsController::class, 'index'])->name('user-metrics.index');
Route::get('/user-metrics/create', [UserMetricsController::class, 'create'])->name('user-metrics.create');
Route::post('/user-metrics', [UserMetricsController::class, 'store'])->name('user-metrics.store');
Route::get('/user-metrics/{id}/edit', [UserMetricsController::class, 'edit'])->name('user-metrics.edit');
Route::post('/user-metrics/{id}', [UserMetricsController::class, 'update'])->name('user-metrics.update');
Route::post('/user-metrics/{id}/delete', [UserMetricsController::class, 'destroy'])->name('user-metrics.destroy');

//Meal Plans
Route::get('/meal-plans', [MealPlanController::class, 'index'])->name('meal-plans.index');
Route::get('/meal-plans/create', [MealPlanController::class, 'create'])->name('meal-plans.create');
Route::post('/meal-plans', [MealPlanController::class, 'store'])->name('meal-plans.store');
Route::get('/meal-plans/{id}/edit', [MealPlanController::class, 'edit'])->name('meal-plans.edit');
Route::post('/meal-plans/{id}', [MealPlanController::class, 'update'])->name('meal-plans.update');
Route::post('/meal-plans/{id}/delete', [MealPlanController::class, 'destroy'])->name('meal-plans.destroy');

