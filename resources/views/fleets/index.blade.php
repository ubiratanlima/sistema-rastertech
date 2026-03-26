@extends('layouts.app')

@section('title', 'Gestão de Frotas')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 animate__animated animate__fadeIn">
        <div class="col-sm-6">
            <h1 class="m-0 text-bold"><i class="fas fa-truck-moving mr-2 text-primary"></i>Gestão de Frotas</h1>
            <p class="text-muted">Inventário consolidado de veículos e tecnologias instaladas.</p>
        </div>
        <div class="col-sm-6 text-right">
            <button class="btn btn-success shadow-sm btn-lg px-4"><i class="fas fa-plus mr-2"></i>Cadastrar Novo Veículo</button>
        </div>
    </div>

    <!-- Tabela de Frotas -->
    <div class="card card-outline card-primary shadow-lg border-0 animate__animated animate__fadeInUp">
        <div class="card-header border-0 bg-transparent">
            <h3 class="card-title text-bold mt-2"><i class="fas fa-car-side mr-2 text-primary"></i>Veículos & Ativos Vinculados</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr class="bg-dark text-white text-center text-sm">
                            <th>PLACA</th>
                            <th>MARCA / MODELO</th>
                            <th>CLIENTE / PROPRIETÁRIO</th>
                            <th>RASTRADOR (IMEI)</th>
                            <th>CHIP (CONECTIVIDADE)</th>
                            <th>STATUS</th>
                            <th>AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicles as $vehicle)
                        <tr>
                            <td class="text-center align-middle">
                                <div class="plate-container shadow-sm mx-auto">
                                    <div class="plate-header">BRASIL</div>
                                    <div class="plate-number">{{ strtoupper($vehicle->plate) }}</div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <div class="text-bold">{{ $vehicle->brand ?? '---' }}</div>
                                <div class="small text-muted text-uppercase">{{ $vehicle->vehicle_model ?? '---' }}</div>
                            </td>
                            <td class="align-middle text-center">
                                <div class="text-bold"><i class="fas fa-user-tie mr-1 text-primary"></i>{{ $vehicle->customer_name }}</div>
                            </td>
                            <td class="text-center align-middle">
                                @if($vehicle->device_imei)
                                    <span class="badge badge-light border px-2 py-2 text-uppercase font-weight-normal">
                                        <i class="fas fa-microchip mr-1 text-primary small"></i> {{ $vehicle->device_imei }}
                                    </span>
                                @else
                                    <span class="text-muted small italic"><i class="fas fa-times mr-1"></i> Desvinculado</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @if($vehicle->sim_number)
                                    <div class="text-bold small"><i class="fas fa-sim-card mr-1 text-info"></i>{{ $vehicle->sim_number }}</div>
                                    <div class="text-muted text-xs text-uppercase">{{ $vehicle->sim_operator }}</div>
                                @else
                                    <span class="text-muted small italic">Sem conexão</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                @php
                                    $statusClass = [
                                        'active' => 'badge-success',
                                        'maintenance' => 'badge-warning',
                                        'inactive' => 'badge-danger'
                                    ][$vehicle->device_status ?? 'inactive'] ?? 'badge-secondary';
                                @endphp
                                <span class="badge {{ $statusClass }} px-3 py-1 shadow-sm">
                                    {{ strtoupper($vehicle->device_status ?? 'ESTOQUE') }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-primary shadow-sm" title="Editar"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-outline-danger shadow-sm" title="Excluir"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-car fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Nenhum veículo cadastrado</h4>
                                <p>Comece a organizar a sua frota hoje mesmo.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix bg-transparent border-0">
            <div class="float-right pagination-relative">
                {{ $vehicles->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilo da Placa Mercosul */
    .plate-container {
        display: block;
        border: 2px solid #333;
        border-radius: 4px;
        background: #fff;
        width: 130px;
        overflow: hidden;
        font-family: 'Arial Black', Gadget, sans-serif;
    }
    .plate-header {
        background: #003399;
        color: white;
        font-size: 8px;
        padding: 1px 0;
        text-align: center;
        letter-spacing: 2px;
        font-weight: bold;
    }
    .plate-number {
        color: #000;
        font-weight: bold;
        font-size: 18px;
        padding: 2px 0;
        text-align: center;
        letter-spacing: 2px;
    }
    
    body.dark-mode .card { background: #1a1a2e; border-top: 3px solid #007bff !important; }
    body.dark-mode .table td { color: #e0e0e0; border-color: #2d2d44; }
    body.dark-mode .table thead th { background: #0f3460; border-color: #2d2d44; }
    body.dark-mode .badge-light { background: #16213e; color: #fff; border-color: #2d2d44; }
    
    .btn-group .btn { margin: 0 2px; }
    .animate__animated { --animate-duration: 0.8s; }
</style>
@endsection
