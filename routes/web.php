<?php

use App\Http\Controllers\PokemonsController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return Inertia::render('Dashboard');
    })->name('dashboard');

    Route::get('/products', [ProductController::class, 'index'])
        ->name('products.index');
    Route::get('/pokemons', [PokemonsController::class, 'index'])
        ->name('pokemons.index');
    Route::get('/pokemons/{pokemon}/edit', [PokemonsController::class, 'edit'])
        ->name('pokemons.edit');
    Route::put('/pokemons/{pokemon}', [PokemonsController::class, 'update'])
        ->name('pokemons.update');
    Route::delete('/pokemons/{pokemon}', [PokemonsController::class, 'destroy'])
        ->name('pokemons.destroy');
    Route::get('/pokemons/{pokemon}', [PokemonsController::class, 'show'])
        ->name('pokemons.show');
    Route::post('/pokemons/sync', [PokemonsController::class, 'sync'])
        ->name('pokemons.sync');
    Route::get('/products/create', [ProductController::class, 'create'])
        ->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])
        ->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])
        ->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])
        ->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])
        ->name('products.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
