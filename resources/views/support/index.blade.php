@extends('layouts.app')

@section('title', 'Central de Atendimento')

@section('content')
<div class="container-fluid">
    <!-- 🎧 CABEÇALHO TÁTICO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-md-6 p-0">
            <h1 class="m-0 text-bold" style="font-size: 2rem;">
                <i class="fas fa-headset mr-2 text-warning"></i>Atendimento ao Cliente
            </h1>
            <p class="text-muted mb-0">Gestão de suporte e intervenção técnica em tempo real.</p>
        </div>
        <div class="col-md-6 p-0 mt-3 mt-md-0">
            <form action="{{ route('support.customers') }}" method="GET" class="input-group shadow-sm">
                <input type="text" name="search" class="form-control border-0" placeholder="Buscar por RTECH, Nome ou CPF/CNPJ..." value="{{ $search }}" style="height: 50px; border-radius: 10px 0 0 10px;">
                <div class="input-group-append">
                    <button class="btn btn-warning px-4" type="submit" style="border-radius: 0 10px 10px 0;">
                        <i class="fas fa-search mr-2"></i> FILTRAR
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🏗️ GRID DE ATENDIMENTO (ACORDEON) -->
    <div class="card card-outline card-warning shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3">
            <h3 class="card-title text-bold mb-0">
                <i class="fas fa-users mr-2 text-warning"></i>Clientes com Veículos Vinculados
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0" id="supportTable">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02);">
                            <th style="width: 150px;">RTECH CODE</th>
                            <th class="text-left px-4">NOME DO CLIENTE</th>
                            <th style="width: 150px;">VEÍCULOS</th>
                            <th style="width: 80px;">AÇÃO</th>
                        </tr>
                    </thead>
                    <tbody id="accordionSupport">
                        @forelse($customers as $customer)
                        <!-- 🏁 LINHA DE BASE (CLICÁVEL) -->
                        <tr class="base-row bg-white" data-toggle="collapse" data-target="#details-{{ $customer->id }}" aria-expanded="false" style="cursor: pointer;">
                            <td class="text-center align-middle text-indigo">
                                <span class="text-indigo" style="background: rgba(102, 16, 242, 0.05); padding: 4px 8px; border-radius: 4px; border: 1px solid rgba(102, 16, 242, 0.1);">{{ $customer->customer_rtech ?? str_pad($customer->id, 12, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="align-middle px-4">
                                <span class="text-dark d-block">{{ $customer->name }}</span>
                                <span class="text-muted">Doc: {{ $customer->document ?? '---' }}</span>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-warning px-3 py-1 shadow-sm">{{ $customer->vehicle_count }} ATIVOS</span>
                            </td>
                            <td class="text-center align-middle">
                                <i class="fas fa-chevron-down text-muted transition-icon"></i>
                            </td>
                        </tr>

                        <!-- 📟 LINHA EXPANSÍVEL (DADOS DO VEÍCULO) -->
                        <tr class="collapse-row">
                            <td colspan="4" class="p-0 border-0">
                                <div id="details-{{ $customer->id }}" class="collapse px-4 py-3 bg-light border-top shadow-inner" data-parent="#accordionSupport">
                                    <div class="table-responsive bg-white rounded shadow-sm border">
                                        <table class="table table-sm mb-0">
                                            <thead>
                                                <tr class="text-uppercase text-muted font-weight-bold" style="background: #f8f9fa;">
                                                        <th class="text-center py-2" style="width: 140px;">PLACA</th>
                                                        <th class="text-center py-2">MARCA / MODELO</th>
                                                        <th class="text-center py-2">STATUS</th>
                                                        <th class="text-center py-2">RASTR. / IMEI</th>
                                                        <th class="text-center py-2" style="width: 150px;">CHIP NO. / OP.</th>
                                                        <th class="text-center py-2 pr-4">AÇÕES TÁTICAS</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($customer->vehicles as $vehicle)
                                                <tr>
                                                    <td class="align-middle text-center py-3 border-top-0">
                                                        <div class="mercosul-plate shadow-sm mx-auto">
                                                            <div class="plate-header">BRASIL</div>
                                                            <div class="plate-number">{{ $vehicle->plate }}</div>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle text-center border-top-0">
                                                        <div class="text-dark">{{ $vehicle->brand }}</div>
                                                        <div class="text-muted text-uppercase">{{ $vehicle->model }}</div>
                                                    </td>
                                                    <td class="align-middle text-center border-top-0">
                                                        @php
                                                            $statusClass = [
                                                                'active' => 'badge-success',
                                                                'inactive' => 'badge-danger',
                                                                'maintenance' => 'badge-warning'
                                                            ][$vehicle->device_status] ?? 'badge-secondary';
                                                            $statusLabel = [
                                                                'active' => 'ATIVO',
                                                                'inactive' => 'INATIVO',
                                                                'maintenance' => 'MANUTENÇÃO'
                                                            ][$vehicle->device_status] ?? '---';
                                                        @endphp
                                                        <span class="badge {{ $statusClass }} px-2">
                                                            {{ $statusLabel }}
                                                        </span>
                                                    </td>
                                                    <td class="align-middle text-center border-top-0">
                                                        <div class="text-indigo">{{ $vehicle->rtech_code }}</div>
                                                        <div class="text-muted">IMEI: {{ $vehicle->imei }}</div>
                                                    </td>
                                                    <td class="align-middle text-center border-top-0">
                                                        <div class="text-primary">{{ $vehicle->phone_number }}</div>
                                                        <div class="text-muted">{{ $vehicle->operator }}</div>
                                                    </td>
                                                    <td class="text-center align-middle pr-4 border-top-0">
                                                        <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                                            <button class="btn btn-light btn-square border-right" title="Enviar SMS" onclick="event.stopPropagation(); openSMSModal('{{ $vehicle->id }}', '{{ $vehicle->rtech_code }}')">
                                                                <i class="fas fa-comment-dots fa-lg text-primary"></i>
                                                            </button>
                                                            <button class="btn btn-light btn-square border-right" title="Manutenção" onclick="event.stopPropagation(); openMaintenanceModal('{{ $vehicle->id }}', '{{ $vehicle->plate }}')">
                                                                <i class="fas fa-tools fa-lg text-warning"></i>
                                                            </button>
                                                            <button class="btn btn-light btn-square" title="Alterar Status" onclick="event.stopPropagation(); toggleVehicleStatus('{{ $vehicle->id }}', '{{ $vehicle->device_status }}')">
                                                                <i class="fas fa-power-off fa-lg {{ $vehicle->device_status === 'active' ? 'text-danger' : 'text-success' }}"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="fas fa-headset fa-3x text-muted mb-3 d-block d-none d-sm-block"></i>
                                <h4 class="text-muted">Nenhum cliente ativo para atendimento</h4>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($customers->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 📱 MODAL: COMANDOS SMS (MOCKUP PLANNING) -->
<div class="modal fade" id="modalSMS" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
            <div class="modal-header bg-dark text-white border-0" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-comment-dots mr-2 text-warning"></i>Console de Comando SMS</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body p-4 text-center">
                <i class="fas fa-satellite-dish fa-3x text-primary mb-3"></i>
                <h5 class="text-bold">Motor de Mensagens</h5>
                <p class="text-muted">Aguardando mapeamento modelo vs chip.</p>
                <div class="p-3 bg-light rounded mt-3">
                    <strong>EQUIPAMENTO:</strong> <span id="smsVehicleInfo" class="text-primary text-bold"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    /* 🇧🇷 Estilo Placa Mercosul (Oficial Rastertech) */
    .mercosul-plate {
        width: 120px;
        height: 44px;
        border: 2px solid #333;
        border-radius: 6px;
        background: white;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        line-height: 1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
        border-bottom-width: 3px;
    }

    .plate-header {
        background: #003399;
        color: white;
        font-size: 7px;
        text-align: center;
        padding: 2px 0;
        font-weight: bold;
        letter-spacing: 1px;
    }

    .plate-number {
        flex-grow: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Arial Black', sans-serif;
        font-size: 1.2rem;
        color: #000;
        letter-spacing: 1.5px;
        padding-top: 1px;
    }

    .base-row:hover { background-color: rgba(255, 193, 7, 0.05) !important; }
    .base-row[aria-expanded="true"] { background-color: rgba(255, 193, 7, 0.1) !important; border-left: 5px solid #ffc107 !important; }
    .base-row[aria-expanded="true"] .transition-icon { transform: rotate(180deg); color: #ffc107 !important; }
    .transition-icon { transition: transform 0.3s ease; }
    .collapse-row td { border-top: none !important; }
    .shadow-inner { box-shadow: inset 0 2px 10px rgba(0,0,0,0.05); }
    .text-indigo { color: #6610f2; }
    
    /* 🌓 DARK MODE RASTERTECH */
    .dark-mode .bg-light { background-color: #1a1a2e !important; }
    .dark-mode .bg-white { background-color: #16213e !important; }
    .dark-mode .base-row:hover { background-color: rgba(255, 193, 7, 0.1) !important; }
    .dark-mode .text-dark { color: #fff !important; }
</style>

<script>
    function openSMSModal(id, rtech) {
        $('#smsVehicleInfo').text(rtech);
        $('#modalSMS').modal('show');
    }

    function openMaintenanceModal(id, plate) {
        alert('🛠️ MÓDULO DE MANUTENÇÃO\nVeículo: ' + plate + '\n\nEste popup permitirá:\n- Manutenção cadastral\n- Troca de rastreador\n- Troca de chip');
    }

    function toggleVehicleStatus(id, current) {
        const action = current === 'active' ? 'INATIVAR' : 'ATIVAR';
        const msg = current === 'active' 
            ? '⚠️ ATENÇÃO: Ao INATIVAR, o rastreador e o chip vinculados retornarão ao estoque com o status "EM MANUTENÇÃO".\n\nDeseja prosseguir?' 
            : 'Deseja ATIVAR este veículo para operação de telemetria?';
        
        if(confirm(msg)) {
            alert('📡 Comando enviado: ' + action + ' para veículo ID: ' + id);
        }
    }
</script>
@endsection
