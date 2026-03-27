@extends('layouts.app')

@section('title', 'Inventário de Dispositivos')

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

    <!-- ⚓ CABEÇALHO PADRÃO OURO (8:4) -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center overflow-hidden flex-nowrap">
        <div class="col-8 col-sm-6 p-0 p-sm-2">
            <h1 class="m-0 text-bold d-none d-sm-block" style="font-size: 2.2rem;">
                <i class="fas fa-microchip mr-2 text-primary"></i>Equipamentos
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-microchip mr-1 text-primary"></i>Inventário GPS
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block">Gerenciamento de aparelhos rastreadores e ativos da frota.</p>
        </div>
        <div class="col-4 col-sm-6 text-right p-0 pr-sm-2">
            <button class="btn btn-primary shadow-sm px-3 py-2" style="border-radius: 8px; font-weight: 600;" data-toggle="modal" data-target="#modalNovoEquipamento">
                <i class="fas fa-plus mr-sm-2"></i>
                <span class="d-none d-sm-inline">Novo Equipamento</span>
            </button>
        </div>
    </div>

    <!-- 🛠️ TABELA CAMALEÃO -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-broadcast-tower mr-2 text-primary"></i>Hardware em Operação
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02);">
                            <th class="text-left px-4">IMEI / IDENTIFICADOR</th>
                            <th class="d-none d-md-table-cell">MODELO</th>
                            <th class="d-none d-lg-table-cell">CHIP (ICCID)</th>
                            <th class="text-left">PROPRIETÁRIO</th>
                            <th class="d-none d-sm-table-cell">STATUS</th>
                            <th style="width: 120px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devices as $device)
                        <tr>
                            <td class="align-middle px-4">
                                <div class="text-primary">{{ $device->imei }}</div>
                                <div class="d-block d-md-none text-muted">{{ $device->model_name ?? 'N/A' }}</div>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="badge badge-light border px-2 py-1 text-uppercase font-weight-normal">
                                    {{ $device->model_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-center align-middle d-none d-lg-table-cell">
                                <span class="text-pink">{{ \Illuminate\Support\Str::limit($device->iccid ?? 'Sem Chip', 12) }}</span>
                            </td>
                            <td class="align-middle">
                                <div class="text-dark d-none d-sm-block">{{ $device->customer_name ?? 'Estoque Central' }}</div>
                                <div class="text-dark d-block d-sm-none">{{ \Illuminate\Support\Str::limit($device->customer_name ?? 'Estoque', 10) }}</div>
                            </td>
                            <td class="text-center align-middle d-none d-sm-table-cell">
                                <span class="badge {{ $device->customer_id ? 'bg-success' : 'bg-warning' }} px-3 py-1 shadow-sm">
                                    {{ $device->customer_id ? 'ATIVO' : 'ESTOQUE' }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <button class="btn btn-light btn-square border-right" title="Editar"><i class="fas fa-tools fa-lg text-warning"></i></button>
                                    <button class="btn btn-light btn-square border-right" title="Link"><i class="fas fa-plug fa-lg text-info"></i></button>
                                    <form action="{{ route('devices.destroy', $device->id) }}" method="POST" class="m-0" onsubmit="return confirm('Inativar equipamento?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-square" title="Excluir"><i class="fas fa-trash fa-lg text-danger"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @if($devices->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
            {{ $devices->withPath('/devices')->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .btn-square {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
    .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
    .dark-mode .btn-light { background: #1a1a2e; border-color: #2d2d44; color: #fff; }
    .dark-mode .btn-light:hover { background: #2d2d44; }
    .dark-mode code.text-pink { background: #16213e; color: #ff007f; border: 1px solid #33213e; }
    .dark-mode .text-dark { color: #fff !important; }
    
    .animate__animated { --animate-duration: 0.6s; }
    code.text-pink { background: #fff0f5; padding: 2px 5px; border-radius: 4px; color: #e83e8c; }
</style>
@endsection
