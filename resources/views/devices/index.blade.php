@extends('adminlte::page')

@section('title', 'Inventário | Rastertech')

@section('content_header')
    <h1><i class="fas fa-warehouse"></i> Inventário de Equipamentos</h1>
@stop

@section('content')
    <div class="card card-outline card-success shadow-lg">
        <div class="card-header">
            <h3 class="card-title">Listagem de Rastreadores (Gestão Massiva)</h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover mb-0">
                    <thead class="bg-dark">
                        <tr>
                            <th>IMEI</th>
                            <th>Modelo</th>
                            <th>Cliente Associado</th>
                            <th>Chip (Simcard)</th>
                            <th>Plataforma e IP</th>
                            <th>Situação</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($devices as $device)
                            <tr>
                                <td><code class="text-primary">{{ $device->imei }}</code></td>
                                <td><span class="badge badge-info">{{ $device->deviceModel ? $device->deviceModel->name : 'N/A' }}</span></td>
                                <td>
                                    {{ $device->customer ? $device->customer->name : 'ESTOQUE GERAL' }}
                                    @if($device->vehicle)
                                        <br><small class="text-muted"><i class="fas fa-car mx-1"></i> Placa: {{ $device->vehicle->plate }}</small>
                                    @endif
                                </td>
                                <td>{{ $device->gsmCard ? $device->gsmCard->iccid : 'SEM CHIP' }}</td>
                                <td>{{ $device->platform ? $device->platform->server_ip : 'OFFLINE' }}</td>
                                <td>
                                    @if($device->gsm_card_id && $device->platform_id)
                                        <span class="badge badge-success">OPERACIONAL</span>
                                    @else
                                        <span class="badge badge-secondary">EM ESTOQUE</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('devices.show', $device->id) }}" class="btn btn-xs btn-success shadow-sm">
                                        <i class="fas fa-terminal"></i> Ver Comandos
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix">
            <div class="float-right">
                {{ $devices->links() }}
            </div>
        </div>
    </div>
@stop
