<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', function () {
    return view('panel.dashboard.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'dashBoard'])->name('dashboard');
    Route::get('country', [\App\Http\Controllers\CountryController::class, 'index'])->name('country_index');
    Route::get('state', [\App\Http\Controllers\StateController::class, 'index'])->name('state.index');
    Route::get('city', [\App\Http\Controllers\CityController::class, 'index'])->name('city.index');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::prefix('ajax')->name('ajax.')->group(function () {
        Route::get('countries', [\App\Http\Controllers\CountryController::class, 'fetchCountries'])->name('fetch_countries');
        Route::post('countries', [\App\Http\Controllers\CountryController::class, 'storeCountries'])->name('store_countries');
        Route::get('/countries/{id}', [\App\Http\Controllers\CountryController::class, 'edit']); // Get country data
        Route::put('/countries/{id}', [\App\Http\Controllers\CountryController::class, 'update']); // Update country data
        Route::delete('/countries/{id}', [\App\Http\Controllers\CountryController::class, 'destroy']); // Delete country data


        Route::get('states', [\App\Http\Controllers\StateController::class, 'fetchStates'])->name('fetch.states');
        Route::post('states', [\App\Http\Controllers\StateController::class, 'storeStates'])->name('store.states');
        Route::get('states/{id}', [\App\Http\Controllers\StateController::class, 'edit']); // Get state data
        Route::put('states/{id}', [\App\Http\Controllers\StateController::class, 'update']); // Update state data
        Route::delete('states/{id}', [\App\Http\Controllers\StateController::class, 'destroy']); // Delete state data


        Route::get('cities', [\App\Http\Controllers\CityController::class, 'fetchCities'])->name('fetch.cities');
        Route::post('cities', [\App\Http\Controllers\CityController::class, 'storeCities'])->name('store.cities');
        Route::get('cities/{id}', [\App\Http\Controllers\CityController::class, 'edit']); // Get city data
        Route::put('cities/{id}', [\App\Http\Controllers\CityController::class, 'update']); // Update city data
        Route::delete('cities/{id}', [\App\Http\Controllers\CityController::class, 'destroy']); // Delete city data

    });
});

require __DIR__ . '/auth.php';
