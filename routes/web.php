<?php

use App\Http\Controllers\AnimalController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('auth/login');
})->name('/');

Route::get('home', [HomeController::class, 'index'])->name('home');
Route::get('animal/index', [AnimalController::class, 'animal_index'])->name('animal.index');
Route::post('animal/create', [AnimalController::class, 'animal_create'])->name('animal.create');
Route::get('reproduction/index', [AnimalController::class, 'reproduction_index'])->name('reproduction.index');
Route::post('reproduction/store', [AnimalController::class, 'reproduction_store'])->name('reproduction.store');
Route::get('embauche/index', [AnimalController::class, 'embauche_index'])->name('embauche.index');
Route::post('embauche/store', [AnimalController::class, 'embauche_store'])->name('embauche.store');





Route::resource('employee', EmployeeController::class)->only(['index', 'store', 'update', 'destroy']);

require __DIR__.'/auth.php';
