@extends('layouts.app')

@section('title', 'Gestão de Frotas')

@section('content')
<div class="container-fluid">
    <!-- 🔔 ALERTAS DE OPERAÇÃO -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible animate__animated animate__fadeInDown">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-check"></i> Sucesso!</h5>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible animate__animated animate__shakeX">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Erro de Segurança!</h5>
            {{ session('error') }}
        </div>
    @endif

    <!-- ⚓ CABEÇALHO PADRÃO OURO (8:4) -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center overflow-hidden flex-nowrap">
        <div class="col-8 col-sm-6 p-0 p-sm-2">
            <h1 class="m-0 text-bold d-none d-sm-block" style="font-size: 2.2rem;">
                <i class="fas fa-truck-moving mr-2 text-primary"></i>Gestão de Frotas
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-truck-moving mr-1 text-primary"></i>Frotas & Ativos
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block">Inventário consolidado de veículos e tecnologias instaladas.</p>
        </div>
        <div class="col-4 col-sm-6 text-right p-0 pr-sm-2">
            <button class="btn btn-primary shadow-sm px-3 py-2" style="border-radius: 8px; font-weight: 600;">
                <i class="fas fa-plus mr-sm-2"></i>
                <span class="d-none d-sm-inline">Novo Veículo</span>
            </button>
        </div>
    </div>

    <!-- 🛠️ TABELA CAMALEÃO -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-car-side mr-2 text-primary"></i>Status da Frota
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center text-sm" style="background-color: rgba(0,0,0,0.02);">
                            <th style="width: 140px;">PLACA</th>
                            <th class="d-none d-md-table-cell">MARCA / MODELO</th>
                            <th>CLIENTE</th>
                            <th class="d-none d-lg-table-cell">RASTRADOR (IMEI)</th>
                            <th class="d-none d-xl-table-cell">CONECTIVIDADE</th>
                            <th class="d-none d-sm-table-cell text-center">STATUS</th>
                            <th style="width: 120px;">AÇÕES</th>
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
                            <td class="align-middle d-none d-md-table-cell">
                                <div class="text-bold">{{ $vehicle->brand ?? '---' }}</div>
                                <div class="small text-muted text-uppercase">{{ $vehicle->vehicle_model ?? '---' }}</div>
                            </td>
                            <td class="align-middle">
                                <div class="text-bold text-primary">
                                    <span class="d-none d-sm-inline">{{ $vehicle->customer_name }}</span>
                                    <span class="d-inline d-sm-none" title="{{ $vehicle->customer_name }}">
                                        {{ \Illuminate\Support\Str::limit($vehicle->customer_name, 10) }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center align-middle d-none d-lg-table-cell">
                                @if($vehicle->device_imei)
                                    <code class="text-dark font-weight-bold">{{ $vehicle->device_imei }}</code>
                                @else
                                    <span class="text-muted small">---</span>
                                @endif
                            </td>
                            <td class="text-center align-middle d-none d-xl-table-cell">
                                @if($vehicle->sim_number)
                                    <div class="small">
                                        <i class="fas fa-sim-card mr-1 text-info"></i>{{ $vehicle->sim_number }}
                                    </div>
                                @else
                                    <span class="text-muted small">Sem Chip</span>
                                @endif
                            </td>
                            <td class="text-center align-middle d-none d-sm-table-cell">
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
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <a href="#" class="btn btn-xs btn-light border-right" title="Visualizar"><i class="fas fa-eye text-primary"></i></a>
                                    <a href="#" class="btn btn-xs btn-light border-right" title="Editar"><i class="fas fa-edit text-warning"></i></a>
                                    <form action="{{ route('fleets.destroy', $vehicle->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('Inativar veículo {{ $vehicle->plate }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-light" title="Inativar">
                                            <i class="fas fa-trash text-danger"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-car fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Nenhum veículo cadastrado</h4>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($vehicles->hasPages())
        <div class="card-footer bg-transparent border-0 py-3">
            <div class="float-right">{{ $vehicles->links() }}</div>
        </div>
        @endif
    </div>
</div>

<style>
    /* 🇧🇷 ESTILO DA PLACA MERCOSUL RASTERTECH */
    .plate-container { 
        display: block; border: 2px solid #333; border-radius: 4px; background: #fff; width: 110px; overflow: hidden; font-family: 'Arial Black', Gadget, sans-serif; 
    }
    .plate-header { background: #003399; color: white; font-size: 7px; padding: 1px 0; text-align: center; letter-spacing: 1px; font-weight: bold; }
    .plate-number { color: #000; font-weight: bold; font-size: 16px; padding: 1px 0; text-align: center; letter-spacing: 1px; }
    
    /* 🌓 ADAPTAÇÃO DARK MODE */
    .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
    .dark-mode .btn-light { background: #1a1a2e; border-color: #2d2d44; color: #fff; }
    .dark-mode .btn-light:hover { background: #2d2d44; }
    .dark-mode code { background: #16213e; color: #00ff88; padding: 2px 4px; border-radius: 4px; }
    
    .btn-group .btn { padding: 8px 12px; }
    .animate__animated { --animate-duration: 0.6s; }
</style>
@endsection
