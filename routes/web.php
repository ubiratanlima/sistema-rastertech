<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\SimCardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VehicleController;

/*
|--------------------------------------------------------------------------
| Web Routes - RASTERTECH COMMAND CENTER v1.0
|--------------------------------------------------------------------------
*/

// 🚀 DASHBOARD DINÂMICO
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// 🖥️ GESTÃO DE ATIVOS: DISPOSITIVOS
Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');

// 📟 GESTÃO DE ATIVOS: CARTÕES SIM (CHIPS)
Route::get('/sim-cards', [SimCardController::class, 'index'])->name('sim-cards.index');

// 👥 ADMINISTRAÇÃO: CLIENTES E FROTAS
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

// 🚗 GESTÃO DE FROTAS (VEÍCULOS)
Route::get('/fleets', [VehicleController::class, 'index'])->name('fleets.index');

// Futuras rotas de Gestão (SIM Cards, Clientes, Frotas)
// Route::get('/sims', [SimCardController::class, 'index'])->name('sims.index');
// Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
