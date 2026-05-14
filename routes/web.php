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
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SupportController;
use App\Http\Controllers\HelpController;
use App\Http\Controllers\Portal\CustomerPortalController;
use App\Http\Controllers\SystemSettingsController;
use App\Http\Controllers\Auth\LoginController;


/*
|--------------------------------------------------------------------------
| Web Routes - RASTERTECH COMMAND CENTER v1.0
|--------------------------------------------------------------------------
*/

// 🛡️ AUTENTICAÇÃO TÁTICA RASTERTECH
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// 🛠️ ROTA TEMPORÁRIA DE SANEAMENTO (Remover após teste)
Route::get('/setup-test-user', function() {
    $user = \App\Models\User::updateOrCreate(
        ['email' => 'marcela_cliente@rastertech.com.br'],
        [
            'name' => 'Marcela Cliente',
            'password' => bcrypt('cliente123'),
            'role' => 'Cliente',
            'customer_id' => 1,
            'gender' => 'Feminino'
        ]
    );

    auth()->login($user);
    return redirect()->route('portal.dashboard');
});

// 🚧 Rotas Protegidas por Autenticação
Route::group(['middleware' => ['auth']], function () {

    // 🚀 ACESSO GLOBAL (Admin, Gerente, Operador, Motorista, Cliente, Autorizado)
    Route::group(['middleware' => ['role:admin,gerente,suporte,motorista,cliente,autorizado']], function () {
        
        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

        // GESTÃO DE ATIVOS
        Route::get('/devices', [DeviceController::class, 'index'])->name('devices.index');
        Route::get('/devices/check-imei/{imei}', [DeviceController::class, 'checkImei'])->name('devices.check-imei');
        Route::post('/devices', [DeviceController::class, 'store'])->name('devices.store');
        Route::put('/devices/{id}', [DeviceController::class, 'update'])->name('devices.update');
        Route::put('/devices/{id}/restore', [DeviceController::class, 'restore'])->name('devices.restore');
        Route::delete('/devices/{id}', [DeviceController::class, 'destroy'])->name('devices.destroy');

        Route::get('/sim-cards', [SimCardController::class, 'index'])->name('sim-cards.index');
        Route::post('/sim-cards', [SimCardController::class, 'store'])->name('sim-cards.store');
        Route::get('/sim-cards/trash', [SimCardController::class, 'trash'])->name('sim-cards.trash');
        Route::put('/sim-cards/{id}/restore', [SimCardController::class, 'restore'])->name('sim-cards.restore');
        Route::delete('/sim-cards/{id}/force', [SimCardController::class, 'forceDelete'])->name('sim-cards.force-delete');
        Route::put('/sim-cards/{id}', [SimCardController::class, 'update'])->name('sim-cards.update');
        Route::delete('/sim-cards/{id}', [SimCardController::class, 'destroy'])->name('sim-cards.destroy');

        // CLIENTES E FROTAS
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
        Route::post('/customers/{customer}/members', [CustomerController::class, 'storeMember'])->name('customers.members.store');
        Route::put('/customers/{customer}/members/{memberId}/toggle', [CustomerController::class, 'toggleMember'])->name('customers.members.toggle');
        Route::get('/customers/{customer}/dossier', [CustomerController::class, 'dossier'])->name('customers.dossier');
        Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
        Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
        Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
        
        // --- 🛰️ INTEGRAÇÃO ASAAS ---
        Route::post('/asaas/sync', function() {
            try {
                \Illuminate\Support\Facades\Artisan::call('asaas:sync-customers');
                $output = \Illuminate\Support\Facades\Artisan::output();
                return response()->json(['success' => true, 'message' => $output]);
            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
        })->name('asaas.sync');

        Route::get('/fleets', [VehicleController::class, 'index'])->name('fleets.index');
        Route::post('/fleets', [VehicleController::class, 'store'])->name('fleets.store');
        Route::put('/fleets/{id}/restore', [VehicleController::class, 'restore'])->name('fleets.restore');
        Route::get('/missoes', [\App\Http\Controllers\VehicleMissionController::class, 'index'])->name('missions.index');
        Route::delete('/fleets/{id}', [VehicleController::class, 'destroy'])->name('fleets.destroy');

        // ENGENHARIA E FORNECEDORES
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
        Route::post('/device-commands/batch', [DeviceCommandController::class, 'batchStore'])->name('device-commands.batch-store');
        Route::get('/device-commands/by-model/{modelId}', [DeviceCommandController::class, 'getCommandsByModel'])->name('device-commands.by-model');
        Route::put('/device-commands/batch/{modelId}', [DeviceCommandController::class, 'batchUpdate'])->name('device-commands.batch-update');
        Route::put('/device-commands/{id}/restore', [DeviceCommandController::class, 'restore'])->name('device-commands.restore');
        Route::delete('/device-commands/{id}', [DeviceCommandController::class, 'destroy'])->name('device-commands.destroy');

        // AUDITORIA E SUPORTE
        Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('/support/customers', [\App\Http\Controllers\SupportController::class, 'index'])->name('support.customers');
        Route::get('/support/start/{vehicle}/{customer}', [\App\Http\Controllers\SupportController::class, 'start'])->name('support.start');
        Route::post('/support/finish', [\App\Http\Controllers\SupportController::class, 'finish'])->name('support.finish');
        Route::get('/support/log/{attendance}', [\App\Http\Controllers\SupportController::class, 'viewLog'])->name('support.log.view');
        
        // ADMIN INSTALLATIONS
        Route::prefix('admin')->name('admin.')->group(function() {
            Route::get('/installations', [\App\Http\Controllers\Admin\AdminInstallationController::class, 'index'])->name('installations.index');
            Route::get('/installations/{id}', [\App\Http\Controllers\Admin\AdminInstallationController::class, 'show'])->name('installations.show');
            Route::post('/installations/{id}/validate', [\App\Http\Controllers\Admin\AdminInstallationController::class, 'storeValidation'])->name('installations.validate');
        });

        Route::get('/help', [HelpController::class, 'index'])->name('help');
    });

    // 👤 USUÁRIOS INTERNOS (Admin, Gerente)
    Route::group(['middleware' => ['role:admin,gerente']], function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::put('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        
        // AUDITORIA GLOBAL
        Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit.index');
    });

    // ⚙️ CONFIGURAÇÕES (Admin)
    Route::group(['middleware' => ['role:admin']], function () {
        Route::get('/settings', [SystemSettingsController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SystemSettingsController::class, 'update'])->name('settings.update');
    });

    // 📱 CREDENCIAIS APPS (Admin, Gerente, Operador, Cliente, Autorizado)
    Route::group(['middleware' => ['role:admin,gerente,suporte,cliente,autorizado']], function () {
        Route::get('/customer-sub-users', [CustomerSubUserController::class, 'index'])->name('customer-sub-users.index');
        Route::post('/customer-sub-users', [CustomerSubUserController::class, 'store'])->name('customer-sub-users.store');
        Route::put('/customer-sub-users/{id}', [CustomerSubUserController::class, 'update'])->name('customer-sub-users.update');
        Route::put('/customer-sub-users/{id}/restore', [CustomerSubUserController::class, 'restore'])->name('customer-sub-users.restore');
        Route::delete('/customer-sub-users/{id}', [CustomerSubUserController::class, 'destroy'])->name('customer-sub-users.destroy');
        Route::get('/customer-sub-users/verify/{token}', [CustomerSubUserController::class, 'verifyEmail'])->name('customer-sub-users.verify');
        Route::get('/customer-sub-users/{id}/validate', [CustomerSubUserController::class, 'validateManual'])->name('customer-sub-users.validate-manual');
    });

    // 🌐 PORTAIS (Cliente, Motorista, Instalador)
    Route::prefix('portal')->name('portal.')->group(function () {
        
        // Helper global dentro do portal (Permitido pra todos do portal)
        Route::get('/check-customers', function() {
            return response()->json(\Illuminate\Support\Facades\Schema::getColumnListing('customers'));
        });

        // 1. ÁREA DO CLIENTE / AUTORIZADO (E Admins)
        Route::group(['middleware' => ['role:admin,gerente,cliente,autorizado']], function () {
            Route::get('/', [CustomerPortalController::class, 'index'])->name('dashboard');
            Route::post('/save-driver', [CustomerPortalController::class, 'saveDriver'])->name('driver.save');
            Route::get('/view/{component}', [CustomerPortalController::class, 'loadComponent'])->name('view');
            Route::post('/profile/update', [CustomerPortalController::class, 'updateProfile'])->name('profile.update');
            Route::post('/component/whatsapp/add', [CustomerPortalController::class, 'addWhatsapp'])->name('whatsapp.add');
            Route::post('/component/whatsapp/delete/{id}', [CustomerPortalController::class, 'deleteWhatsapp'])->name('whatsapp.delete');
            Route::post('/drivers', [CustomerPortalController::class, 'storeDriver'])->name('drivers.store');
            Route::delete('/drivers/{id}', [CustomerPortalController::class, 'deleteDriver'])->name('drivers.delete');
            Route::post('/checklist', [CustomerPortalController::class, 'storeChecklist'])->name('checklist.store');
        });

        // 2. MÓDULO MOTORISTA
        Route::group(['middleware' => ['role:admin,gerente,suporte,motorista,cliente,autorizado']], function () {
            Route::get('/verificacoes', [CustomerPortalController::class, 'verificacoes'])->name('verificacoes.index');
            Route::get('/verificacoes/nova/{type}', [CustomerPortalController::class, 'createChecklist'])->name('verificacoes.create');
            Route::post('/verificacoes/salvar', [CustomerPortalController::class, 'storeChecklistAction'])->name('verificacoes.store');
            Route::get('/verificacoes/{id}', [CustomerPortalController::class, 'showChecklist'])->name('verificacoes.show');

            Route::get('/verificacoes/last-odometer/{vehicle_id}', [CustomerPortalController::class, 'getLastOdometer'])->name('verificacoes.last-odometer');
            Route::get('/despesas', [CustomerPortalController::class, 'despesas'])->name('despesas.index');
            Route::get('/despesas/nova', [CustomerPortalController::class, 'createDespesa'])->name('despesas.create');
            Route::post('/despesas/salvar', [CustomerPortalController::class, 'storeDespesaAction'])->name('despesas.store');
        });

        // 3. MÓDULO INSTALADOR
        Route::group(['middleware' => ['role:admin,gerente,suporte,instalador']], function () {
            Route::get('/instalador', [\App\Http\Controllers\Portal\InstallerPortalController::class, 'index'])->name('instalador.index');
            Route::get('/instalador/checkin', [\App\Http\Controllers\Portal\InstallerPortalController::class, 'createCheckin'])->name('instalador.checkin');
            Route::post('/instalador/checkin/salvar', [\App\Http\Controllers\Portal\InstallerPortalController::class, 'storeCheckin'])->name('instalador.checkin.store');
            Route::get('/instalador/processo/{id}', [\App\Http\Controllers\Portal\InstallerPortalController::class, 'createProcess'])->name('instalador.process');
            Route::post('/instalador/processo/salvar/{id}', [\App\Http\Controllers\Portal\InstallerPortalController::class, 'storeProcess'])->name('instalador.process.store');
            Route::get('/instalador/checkout/{id}', [\App\Http\Controllers\Portal\InstallerPortalController::class, 'createCheckout'])->name('instalador.checkout');
            Route::post('/instalador/checkout/salvar/{id}', [\App\Http\Controllers\Portal\InstallerPortalController::class, 'storeCheckout'])->name('instalador.checkout.store');
            Route::get('/instalador/{id}', [\App\Http\Controllers\Portal\InstallerPortalController::class, 'show'])->name('instalador.show');
        });
    });

    // 🎨 Rota global
    Route::post('/update-theme', [UserController::class, 'updateTheme'])->name('user.update-theme');

});
