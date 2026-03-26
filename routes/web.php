<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DeviceController;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema Rastertech
|--------------------------------------------------------------------------
| Rotas estruturadas padrão Embraet Academy
*/

// Redirecionamento Inicial
Route::get('/', function () {
    return redirect('/home');
});

// Auth (Apenas para o AdminLTE carregar)
Auth::routes(['register' => false]);

// Rotas do Sistema (Dashboard e Inventário)
Route::middleware(['auth'])->group(function () {
    
    // DASHBOARD (Página Inicial)
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // DISPOSITIVOS (Rastreadores)
    Route::group(['prefix' => 'devices', 'as' => 'devices.'], function () {
        Route::get('/', [DeviceController::class, 'index'])->name('index');
        Route::get('/{device}', [DeviceController::class, 'show'])->name('show');
    });

});
