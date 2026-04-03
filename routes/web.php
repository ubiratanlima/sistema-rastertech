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
use App\Http\Controllers\SupportController;
use App\Http\Controllers\Portal\CustomerPortalController;

/*
|--------------------------------------------------------------------------
| Web Routes - RASTERTECH COMMAND CENTER v1.0
|--------------------------------------------------------------------------
*/

// 🚀 DASHBOARD DINÂMICO
Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// 🖥️ GESTÃO DE ATIVOS: DISPOSITIVOS (HARDWARE)
Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
Route::put('/devices/{id}', [DeviceController::class, 'update'])->name('devices.update');
Route::put('/devices/{id}/restore', [DeviceController::class, 'restore'])->name('devices.restore');
Route::delete('/devices/{id}', [DeviceController::class, 'destroy'])->name('devices.destroy');

// 📟 GESTÃO DE ATIVOS: CARTÕES SIM (CHIPS)
Route::get('/sim-cards', [SimCardController::class, 'index'])->name('sim-cards.index');
Route::post('/sim-cards', [SimCardController::class, 'store'])->name('sim-cards.store');
Route::get('/sim-cards/trash', [SimCardController::class, 'trash'])->name('sim-cards.trash');
Route::put('/sim-cards/{id}/restore', [SimCardController::class, 'restore'])->name('sim-cards.restore');
Route::delete('/sim-cards/{id}/force', [SimCardController::class, 'forceDelete'])->name('sim-cards.force-delete');
Route::put('/sim-cards/{id}', [SimCardController::class, 'update'])->name('sim-cards.update');
Route::delete('/sim-cards/{id}', [SimCardController::class, 'destroy'])->name('sim-cards.destroy');

// 👥 ADMINISTRAÇÃO: CLIENTES E FROTAS
Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
Route::post('/customers/{customer}/members', [CustomerController::class, 'storeMember'])->name('customers.members.store');
Route::put('/customers/{customer}/members/{memberId}/toggle', [CustomerController::class, 'toggleMember'])->name('customers.members.toggle');
Route::get('/customers/{customer}/dossier', [CustomerController::class, 'dossier'])->name('customers.dossier');
Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

// 🚗 GESTÃO DE FROTAS (VEÍCULOS)
Route::get('/fleets', [VehicleController::class, 'index'])->name('fleets.index');
Route::delete('/fleets/{id}', [VehicleController::class, 'destroy'])->name('fleets.destroy');

// 🏢 ENGENHARIA: FORNECEDORES E PLATAFORMAS
Route::get('/providers', [ProviderController::class, 'index'])->name('providers.index');
Route::post('/providers', [ProviderController::class, 'store'])->name('providers.store');
Route::put('/providers/{id}', [ProviderController::class, 'update'])->name('providers.update');
Route::put('/providers/{id}/restore', [ProviderController::class, 'restore'])->name('providers.restore');
Route::delete('/providers/{id}', [ProviderController::class, 'destroy'])->name('providers.destroy');

Route::get('/platforms', [PlatformController::class, 'index'])->name('platforms.index');
Route::post('/platforms', [PlatformController::class, 'store'])->name('platforms.store');
Route::put('/platforms/{id}', [PlatformController::class, 'update'])->name('platforms.update');
Route::put('/platforms/{id}/restore', [PlatformController::class, 'restore'])->name('platforms.restore');
Route::delete('/platforms/{id}', [PlatformController::class, 'destroy'])->name('platforms.destroy');

Route::get('/device-models', [DeviceModelController::class, 'index'])->name('device-models.index');
Route::post('/device-models', [DeviceModelController::class, 'store'])->name('device-models.store');
Route::put('/device-models/{id}', [DeviceModelController::class, 'update'])->name('device-models.update');
Route::put('/device-models/{id}/restore', [DeviceModelController::class, 'restore'])->name('device-models.restore');
Route::delete('/device-models/{id}', [DeviceModelController::class, 'destroy'])->name('device-models.destroy');

Route::get('/device-commands', [DeviceCommandController::class, 'index'])->name('device-commands.index');
Route::post('/device-commands', [DeviceCommandController::class, 'store'])->name('device-commands.store');
Route::delete('/device-commands/{id}', [DeviceCommandController::class, 'destroy'])->name('device-commands.destroy');

// 👥 GESTÃO: ACESSOS DE CLIENTES (SUB-USUÁRIOS)
Route::get('/customer-sub-users', [CustomerSubUserController::class, 'index'])->name('customer-sub-users.index');
Route::post('/customer-sub-users', [CustomerSubUserController::class, 'store'])->name('customer-sub-users.store');
Route::put('/customer-sub-users/{id}', [CustomerSubUserController::class, 'update'])->name('customer-sub-users.update');
Route::put('/customer-sub-users/{id}/restore', [CustomerSubUserController::class, 'restore'])->name('customer-sub-users.restore');
Route::delete('/customer-sub-users/{id}', [CustomerSubUserController::class, 'destroy'])->name('customer-sub-users.destroy');
Route::get('/customer-sub-users/verify/{token}', [CustomerSubUserController::class, 'verifyEmail'])->name('customer-sub-users.verify');

// 🛡️ ADMINISTRAÇÃO: COMANDO CENTRAL
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::post('/users', [UserController::class, 'store'])->name('users.store');
Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
Route::post('/update-theme', [UserController::class, 'updateTheme'])->name('user.update-theme');

// 📊 INTELIGÊNCIA: AUDITORIA E REPORTS
Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

// 🎧 ATENDIMENTO E SUPORTE TÁTICO
Route::get('/support/customers', [SupportController::class, 'index'])->name('support.customers');

// 🌐 PORTAL DO CLIENTE (EXPERIÊNCIA PWA)
Route::group(['prefix' => 'portal', 'as' => 'portal.'], function() {
    Route::get('/check-customers', function() {
        return response()->json(Schema::getColumnListing('customers'));
    });

    Route::get('/', [CustomerPortalController::class, 'index'])->name('dashboard');
    Route::post('/save-driver', [CustomerPortalController::class, 'saveDriver'])->name('driver.save');
    Route::get('/view/{component}', [CustomerPortalController::class, 'loadComponent'])->name('view');
    
    // Perfil e WhatsApps
    Route::post('/profile/update', [CustomerPortalController::class, 'updateProfile'])->name('profile.update');
    Route::post('/component/whatsapp/add', [CustomerPortalController::class, 'addWhatsapp'])->name('whatsapp.add');
    Route::post('/component/whatsapp/delete/{id}', [CustomerPortalController::class, 'deleteWhatsapp'])->name('whatsapp.delete');

    // Gestão de Motoristas
    Route::post('/drivers', [CustomerPortalController::class, 'storeDriver'])->name('drivers.store');
    Route::delete('/drivers/{id}', [CustomerPortalController::class, 'deleteDriver'])->name('drivers.delete');

    // Checklist Operacional
    Route::post('/checklist', [CustomerPortalController::class, 'storeChecklist'])->name('checklist.store');
});

// ⚙️ CONFIGURAÇÕES DO SISTEMA
use App\Http\Controllers\SystemSettingsController;
Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings.index');
Route::put('/settings', [SystemSettingsController::class, 'update'])->name('settings.update');

// 🛡️ AUTENTICAÇÃO TÁTICA RASTERTECH
use App\Http\Controllers\Auth\LoginController;
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

