@extends('adminlte::page')

@section('title', 'Dashboard | Rastertech')

@section('content_header')
    <h1><i class="fas fa-satellite"></i> Centro de Operações Rastertech</h1>
@stop

@section('content')
    <div class="row">
        {{-- BLOCKS DE ESTATÍSTICAS --}}
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $stats['devices'] }}</h3>
                    <p>Rastreadores Totais</p>
                </div>
                <div class="icon">
                    <i class="fas fa-satellite-dish"></i>
                </div>
                <a href="{{ route('devices.index') }}" class="small-box-footer">Ver Inventário <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $stats['customers'] }}</h3>
                    <p>Clientes Ativos</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="#" class="small-box-footer">Gestão de Clientes <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $stats['gsm_cards'] }}</h3>
                    <p>Chips em Estoque</p>
                </div>
                <div class="icon">
                    <i class="fas fa-sim-card"></i>
                </div>
                <a href="#" class="small-box-footer">Gestão de Conectividade <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $stats['vehicles'] }}</h3>
                    <p>Veículos Monitorados</p>
                </div>
                <div class="icon">
                    <i class="fas fa-car-side"></i>
                </div>
                <a href="#" class="small-box-footer">Frota Rastertech <i class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>
@stop
<!-- slide -->
@extends('adminlte::page')

@section('title', 'Inventário | Rastertech')

@section('content_header')
    <h1><i class="fas fa-warehouse"></i> Inventário de Equipamentos</h1>
@stop

@section('content')
    <div class="card card-outline card-success">
        <div class="card-header">
            <h3 class="card-title">Listagem de Rastreadores (1000 registros ativos)</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped table-hover">
                <thead class="bg-dark">
                    <tr>
                        <th>IMEI</th>
                        <th>Modelo</th>
                        <th>Cliente Atual</th>
                        <th>Chip (ICCID)</th>
                        <th>Plataforma (IP)</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($devices as $device)
                        <tr>
                            <td><b>{{ $device->imei }}</b></td>
                            <td><span class="badge badge-info">{{ $device->deviceModel ? $device->deviceModel->name : 'N/A' }}</span></td>
                            <td>{{ $device->customer ? $device->customer->name : 'ESTOQUE GERAL' }}</td>
                            <td>{{ $device->gsmCard ? $device->gsmCard->iccid : 'SEM CHIP' }}</td>
                            <td>{{ $device->platform ? $device->platform->server_ip : 'OFFLINE' }}</td>
                            <td>
                                @if($device->status == 'active')
                                    <span class="badge badge-success">ONLINE</span>
                                @else
                                    <span class="badge badge-secondary">ESTOQUE</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('devices.show', $device->id) }}" class="btn btn-xs btn-default">
                                    <i class="fas fa-eye"></i> Comandos
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer clearfix">
            {{ $devices->links() }}
        </div>
    </div>
@stop
