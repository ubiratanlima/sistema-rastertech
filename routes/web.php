<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\SimCardController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VehicleController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\PlatformController;
use App\Http\Controllers\DeviceModelController;
use App\Http\Controllers\DeviceCommandController;
use App\Http\Controllers\CustomerSubUserController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Web Routes - RASTERTECH COMMAND CENTER v1.0
|--------------------------------------------------------------------------
*/

// 🚀 DASHBOARD DINÂMICO
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// 🖥️ GESTÃO DE ATIVOS: DISPOSITIVOS
Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
Route::delete('/devices/{id}', [DeviceController::class, 'destroy'])->name('devices.destroy');

// 📟 GESTÃO DE ATIVOS: CARTÕES SIM (CHIPS)
Route::get('/sim-cards', [SimCardController::class, 'index'])->name('sim-cards.index');
Route::delete('/sim-cards/{id}', [SimCardController::class, 'destroy'])->name('sim-cards.destroy');

// 👥 ADMINISTRAÇÃO: CLIENTES E FROTAS
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

// 🚗 GESTÃO DE FROTAS (VEÍCULOS)
Route::get('/fleets', [VehicleController::class, 'index'])->name('fleets.index');
Route::delete('/fleets/{id}', [VehicleController::class, 'destroy'])->name('fleets.destroy');

// 🏢 ENGENHARIA: FORNECEDORES E PLATAFORMAS
Route::get('/providers', [ProviderController::class, 'index'])->name('providers.index');
Route::post('/providers', [ProviderController::class, 'store'])->name('providers.store');
Route::delete('/providers/{id}', [ProviderController::class, 'destroy'])->name('providers.destroy');

Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');
Route::post('/platforms', [PlatformController::class, 'store'])->name('platforms.store');
Route::delete('/platforms/{id}', [PlatformController::class, 'destroy'])->name('platforms.destroy');

Route::get('/device-models', [DeviceModelController::class, 'index'])->name('device-models.index');
Route::post('/device-models', [DeviceModelController::class, 'store'])->name('device-models.store');
Route::delete('/device-models/{id}', [DeviceModelController::class, 'destroy'])->name('device-models.destroy');

Route::get('/device-commands', [DeviceCommandController::class, 'index'])->name('device-commands.index');
Route::post('/device-commands', [DeviceCommandController::class, 'store'])->name('device-commands.store');
Route::delete('/device-commands/{id}', [DeviceCommandController::class, 'destroy'])->name('device-commands.destroy');

// 👥 GESTÃO: ACESSOS DE CLIENTES (SUB-USUÁRIOS)
Route::get('/customer-sub-users', [CustomerSubUserController::class, 'index'])->name('customer-sub-users.index');
Route::post('/customer-sub-users', [CustomerSubUserController::class, 'store'])->name('customer-sub-users.store');
Route::delete('/customer-sub-users/{id}', [CustomerSubUserController::class, 'destroy'])->name('customer-sub-users.destroy');

// 🛡️ ADMINISTRAÇÃO: COMANDO CENTRAL
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

// 📊 INTELIGÊNCIA: AUDITORIA E REPORTS
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

// Futuras rotas de Gestão (SIM Cards, Clientes, Frotas)
// Route::get('/sims', [SimCardController::class, 'index'])->name('sims.index');
// Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
