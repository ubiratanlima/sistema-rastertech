@extends('layouts.app')

@section('title', 'Central de Atendimento')

@section('content')
<div class="container-fluid">
    <!-- 🎧 CABEÇALHO TÁTICO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 p-0">
            <h1 class="m-0 text-bold" style="font-size: 2rem;">
                <i class="fas fa-headset mr-2 text-warning"></i>Atendimento ao Cliente
            </h1>
            <p class="text-muted mb-0">Gestão de suporte e intervenção técnica em tempo real.</p>
        </div>
    </div>

    <!-- 🏗️ GRID DE ATENDIMENTO (ACORDEON) -->
    <div class="card card-outline card-warning shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title text-bold mb-0">
                <i class="fas fa-users mr-2 text-warning"></i>Clientes com Veículos Vinculados
            </h3>
            <div class="card-tools ml-auto">
                <form action="{{ route('support.customers') }}" method="GET" class="d-flex align-items-center">
                    <div class="input-group input-group-sm" style="width: 280px;">
                        @if($search)
                            <div class="input-group-prepend">
                                <a href="{{ route('support.customers') }}" class="btn btn-default shadow-none border text-danger" title="Limpar Filtro">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @endif
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nome, RTECH, CPF..." value="{{ $search }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default shadow-none border">
                                <i class="fas fa-search text-warning"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0" id="supportTable">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02);">
                            <th style="width: 150px;">RTECH CODE</th>
                            <th class="text-left px-4">CLIENTE</th>
                            <th style="width: 200px;">VEÍCULOS</th>
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
                                <span class="text-dark d-block font-weight-bold" style="font-size: 1.05rem;">{{ $customer->name }}</span>
                                <span class="text-muted small">Doc: {{ $customer->document ?? '---' }}</span>
                            </td>
                            <td class="text-center align-middle">
                                <i class="fas fa-plus-circle mr-1 transition-icon text-muted" style="font-size: 0.9rem; vertical-align: middle;"></i>
                                <small class="text-open font-weight-bold text-uppercase mr-1 text-muted" style="vertical-align: middle; font-size: 0.7rem; letter-spacing: 1px;">ABRIR</small>
                                <small class="text-close font-weight-bold text-uppercase mr-1 text-warning d-none" style="vertical-align: middle; font-size: 0.7rem; letter-spacing: 1px;">FECHAR</small>
                                <span class="badge badge-warning px-2 py-1 shadow-sm" style="vertical-align: middle; font-size: 0.85rem;">{{ $customer->vehicle_count }} ATIVOS</span>
                            </td>
                        </tr>

                        <tr class="collapse-row">
                            <td colspan="3" class="p-0 border-0">
                                <div id="details-{{ $customer->id }}" class="collapse px-4 py-3 bg-light border-top shadow-inner" data-parent="#accordionSupport">
                                    <div class="table-responsive bg-white rounded shadow-sm border">
                                        <table class="table table-sm table-zebra mb-0">
                                            <thead>
                                                <tr class="text-uppercase text-muted font-weight-bold" style="background: #f8f9fa;">
                                                        <th class="text-center py-2" style="width: 140px;">PLACA</th>
                                                        <th class="text-center py-2">MARCA / MODELO</th>
                                                        <th class="text-center py-2">DEVICE</th>
                                                        <th class="text-center py-2" style="width: 150px;">SIMCARD</th>
                                                        <th class="text-center py-2" style="width: 140px;">ATENDIMENTO</th>
                                                        <th class="text-center py-2 pr-4">SMS</th>
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
                                                    <td class="align-middle text-center border-top-0" style="font-size: 0.95rem;">
                                                        <div class="text-indigo font-weight-bold">{{ $vehicle->rtech_code }}</div>
                                                        <div class="text-muted">{{ $vehicle->imei }}</div>
                                                    </td>
                                                    <td class="align-middle text-center border-top-0" style="font-size: 0.95rem;">
                                                        <div class="text-primary font-weight-bold">{{ $vehicle->phone_number }}</div>
                                                        <div class="text-muted text-uppercase">{{ $vehicle->operator }}</div>
                                                    </td>
                                                    <td class="align-middle text-center border-top-0">
                                                        <button class="btn btn-indigo btn-sm btn-block shadow-sm text-bold" style="border-radius: 8px; letter-spacing: 0.5px;" onclick="event.stopPropagation(); startAttendance('{{ $vehicle->id }}', '{{ $customer->id }}')">
                                                            <i class="fas fa-play-circle mr-1"></i> INICIAR
                                                        </button>
                                                    </td>
                                                    <td class="text-center align-middle pr-4 border-top-0">
                                                        <button class="btn btn-light btn-square shadow-sm" style="border-radius: 8px;" title="Enviar SMS" onclick="event.stopPropagation(); openSMSModal('{{ $vehicle->id }}', '{{ $vehicle->rtech_code }}')">
                                                            <i class="fas fa-comment-dots fa-lg text-primary"></i>
                                                        </button>
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

    /* 🔄 INTERATIVIDADE ABRIR/FECHAR */
    .base-row[aria-expanded="false"] .text-close { display: none !important; }
    .base-row[aria-expanded="true"] .text-open { display: none !important; }
    .base-row[aria-expanded="true"] .text-close { display: inline-block !important; }

    /* 🦓 ZEBRADO DA LISTAGEM INTERNA */
    .table-zebra tbody tr:nth-child(odd)  { background-color: #e9ecef; }
    .table-zebra tbody tr:nth-child(even) { background-color: #ffffff; }
    .table-zebra tbody tr { transition: background-color 0.2s ease; }
    .table-zebra tbody tr:hover { background-color: #d0d7f5; }
    
    /* 🌓 DARK MODE RASTERTECH */
    .dark-mode .bg-light { background-color: #1a1a2e !important; }
    .dark-mode .bg-white { background-color: #16213e !important; }
    .dark-mode .base-row:hover { background-color: rgba(255, 193, 7, 0.1) !important; }
    .dark-mode .text-dark { color: #fff !important; }
</style>

<script>
    function startAttendance(vehicleId, customerId) {
        window.location.href = `/support/start/${vehicleId}/${customerId}`;
    }

    function openSMSModal(id, rtech) {
        $('#smsVehicleInfo').text(rtech);
        $('#modalSMS').modal('show');
    }

    function openMaintenanceModal(id, plate) {
        Swal.fire({
            title: '<i class="fas fa-tools mr-2 text-warning"></i> MANUTENÇÃO',
            width: '480px',
            confirmButtonText: 'FECHAR',
            confirmButtonColor: '#6c757d',
            html: `
                <div class="text-left px-2">
                    <div class="p-3 bg-light rounded mb-3" style="border-left: 4px solid #ffc107;">
                        <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Veículo</label>
                        <div class="font-weight-bold text-dark" style="font-size: 1.1rem;">${plate}</div>
                    </div>
                    <p class="text-muted mb-0" style="font-size: 0.95rem;">Este painel permitirá:</p>
                    <ul class="text-left text-muted mt-2" style="font-size: 0.95rem;">
                        <li>Manutenção cadastral</li>
                        <li>Troca de rastreador</li>
                        <li>Troca de chip</li>
                    </ul>
                    <div class="alert alert-warning mt-3 mb-0 py-2" style="font-size: 0.85rem;">
                        <i class="fas fa-info-circle mr-1"></i> Módulo em desenvolvimento.
                    </div>
                </div>`
        });
    }

    function toggleVehicleStatus(id, current) {
        const isActive = current === 'active';
        Swal.fire({
            title: `<span style="font-weight: 400; font-size: 1rem;">${isActive ? 'Tem certeza que deseja inativar este veículo?' : 'Tem certeza que deseja ativar este veículo?'}</span>`,
            html: isActive
                ? '<small class="text-muted">O rastreador e o chip vinculados retornarão ao estoque com status <strong>EM MANUTENÇÃO</strong>.</small>'
                : '<small class="text-muted">O veículo será habilitado para operação de telemetria.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: isActive ? '#d33' : '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: isActive ? 'SIM, INATIVAR' : 'SIM, ATIVAR',
            cancelButtonText: 'CANCELAR'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    icon: 'info',
                    title: 'COMANDO ENVIADO',
                    text: `Solicitação de ${isActive ? 'inativação' : 'ativação'} registrada.`,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        });
    }
</script>
@endsection
