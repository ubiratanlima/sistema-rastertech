@extends('layouts.app')

@section('title', 'Inventário de Dispositivos')

@section('content')
    <div class="content-header">
        <h1 class="m-0 text-bold" style="padding-top: 20px;">Inventário de Dispositivos</h1>
        <p class="text-muted">Gerenciamento de aparelhos rastreadores e ativos da frota.</p>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card glass-card">
                <div class="card-header border-0 d-flex align-items-center">
                    <h3 class="card-title text-bold"><i class="fas fa-list mr-2"></i> Lista de Aparelhos</h3>
                    <div class="card-tools ml-auto">
                        <button class="btn btn-success btn-sm" style="background-color: var(--raster-green) !important; color: #000; border: 0; font-weight: 800;">
                            <i class="fas fa-plus mr-1"></i> NOVO DISPOSITIVO
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="color: var(--text-primary);">
                            <thead style="background: rgba(0,0,0,0.1); border-bottom: 1px solid var(--border-glass);">
                                <tr>
                                    <th class="pl-4">IMEI / ID</th>
                                    <th>MODELO</th>
                                    <th>CHIP (ICCID)</th>
                                    <th>CLIENTE / PROPRIETÁRIO</th>
                                    <th>STATUS</th>
                                    <th class="text-right pr-4">AÇÕES</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($devices as $device)
                                <tr style="border-bottom: 1px solid var(--border-glass);">
                                    <td class="pl-4">
                                        <span class="text-bold">{{ $device->imei }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-info" style="font-size: 0.8rem; padding: 5px 10px;">{{ $device->model_name ?? 'N/A' }}</span>
                                    </td>
                                    <td>{{ $device->iccid ?? 'Sem Chip' }}</td>
                                    <td>{{ $device->customer_name ?? 'Estoque Central' }}</td>
                                    <td>
                                        <span class="badge {{ $device->customer_id ? 'badge-success' : 'badge-warning' }}">
                                            {{ $device->customer_id ? 'ATIVO' : 'EM ESTOQUE' }}
                                        </span>
                                    </td>
                                    <td class="text-right pr-4">
                                        <button class="btn btn-sm btn-outline-info mr-1" title="Editar"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-outline-danger" title="Excluir"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Paginação Neon (Caminho Relativo Puro) -->
                <div class="card-footer bg-transparent border-0 d-flex justify-content-center">
                    {{ $devices->withPath('/devices')->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
@endsection

@push('css')
<style>
    .pagination .page-link { background: var(--bg-card); border-color: var(--border-glass); color: var(--raster-green); }
    .pagination .page-item.active .page-link { background: var(--raster-green); border-color: var(--raster-green); color: #000; }
</style>
@endpush
