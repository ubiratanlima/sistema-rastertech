@extends('adminlte::page')

@section('title', 'Detalhes do Dispositivo | Rastertech')

@section('content_header')
    <h1><i class="fas fa-satellite"></i> Comandos do Equipamento: {{ $device->imei }}</h1>
@stop

@section('content')
    <div class="row">
        {{-- INFORMAÇÕES DO DISPOSITIVO --}}
        <div class="col-md-4">
            <div class="card card-outline card-success">
                <div class="card-header"><h3 class="card-title">Resumo da Instalação</h3></div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><b>Modelo:</b> {{ $device->deviceModel ? $device->deviceModel->name : 'N/A' }}</li>
                        <li class="list-group-item"><b>Chip (Tel):</b> {{ $device->gsmCard ? $device->gsmCard->phone_number : 'SEM NÚMERO' }}</li>
                        <li class="list-group-item"><b>IP do Servidor:</b> {{ $device->platform ? $device->platform->server_ip : 'N/A' }}</li>
                        <li class="list-group-item"><b>Porta:</b> {{ $device->port_number ?? 'N/A' }}</li>
                        <li class="list-group-item bg-dark text-white"><b>APN Configurada:</b> {{ $device->gsmCard ? $device->gsmCard->apn : 'Vazio' }}</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- LISTAGEM DE COMANDOS DINÂMICOS --}}
        <div class="col-md-8">
            <div class="card card-success card-outline shadow-sm">
                <div class="card-header"><h3 class="card-title">Biblioteca de Comandos SMS (Manuais)</h3></div>
                <div class="card-body">
                    @if($device->deviceModel && count($device->deviceModel->commands) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="bg-secondary text-white">
                                    <tr>
                                        <th>Descrição do Comando</th>
                                        <th>Instrução SMS (Copiável)</th>
                                        <th width="50">Copiar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        // Variáveis mestres para substituição dinâmica
                                        $ip = $device->platform->server_ip ?? 'IP_ERR';
                                        $port = $device->port_number ?? 'PORT_ERR';
                                        $apn = $device->gsmCard->apn ?? 'APN_ERR';
                                        $apn_u = $device->gsmCard->apn_user ?? 'APN_USER';
                                        $apn_p = $device->gsmCard->apn_pass ?? 'APN_PASS';
                                    @endphp

                                    @foreach($device->deviceModel->commands as $command)
                                        @php
                                            // A mágica da substituição de placeholders
                                            $final_command = str_replace(
                                                ['{IP}', '{PORT}', '{APN}', '{APN_USER}', '{APN_PASS}'],
                                                [$ip, $port, $apn, $apn_u, $apn_p],
                                                $command->command_template
                                            );
                                        @endphp
                                        <tr>
                                            <td><b>{{ $command->description }}</b></td>
                                            <td><span class="text-success">{{ $final_command }}</span></td>
                                            <td class="text-center">
                                                <button class="btn btn-xs btn-outline-success" onclick="navigator.clipboard.writeText('{{ $final_command }}'); alert('Comando Copiado!');">
                                                    <i class="fas fa-copy"></i>
                                                </td>
                                            </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-warning">Nenhum comando cadastrado para este modelo.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@stop
