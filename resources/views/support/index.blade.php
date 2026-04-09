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
                                                <!-- 🛸 LINHA DO VEÍCULO (ZEBRADO MANUAL) -->
                                                <tr class="vehicle-row {{ $loop->index % 2 == 0 ? 'bg-white' : 'bg-zebra' }}" data-toggle="collapse" data-target="#history-{{ $vehicle->id }}" style="cursor: pointer; border-top: 1px solid #dee2e6;">
                                                    <td class="align-middle text-center py-3">
                                                        <div class="mercosul-plate shadow-sm mx-auto">
                                                            <div class="plate-header">BRASIL</div>
                                                            <div class="plate-number">{{ $vehicle->plate }}</div>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <div class="text-dark">{{ $vehicle->brand }}</div>
                                                        <div class="text-muted text-uppercase small">{{ $vehicle->model }}</div>
                                                    </td>
                                                    <td class="align-middle text-center" style="font-size: 0.95rem;">
                                                        <div class="text-indigo font-weight-bold">{{ $vehicle->rtech_code }}</div>
                                                        <div class="text-muted small">{{ $vehicle->imei }}</div>
                                                    </td>
                                                    <td class="align-middle text-center" style="font-size: 0.95rem;">
                                                        <div class="text-primary font-weight-bold">{{ $vehicle->phone_number }}</div>
                                                        <div class="text-muted text-uppercase small">{{ $vehicle->operator }}</div>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <button class="btn btn-indigo btn-sm btn-block shadow-sm text-bold" style="border-radius: 8px; font-size: 0.75rem;" onclick="event.stopPropagation(); startAttendance('{{ $vehicle->id }}', '{{ $customer->id }}')">
                                                            <i class="fas fa-play-circle mr-1"></i> INICIAR
                                                        </button>
                                                    </td>
                                                    <td class="text-center align-middle pr-4">
                                                        <button class="btn btn-light btn-square shadow-sm" style="border-radius: 8px;" title="Enviar SMS" onclick="event.stopPropagation(); openSMSModal('{{ $vehicle->id }}', '{{ $vehicle->rtech_code }}')">
                                                            <i class="fas fa-comment-dots fa-lg text-primary"></i>
                                                        </button>
                                                    </td>
                                                </tr>

                                                <!-- 📜 NÍVEL 3: HISTÓRICOS (SLIDE SUAVE) -->
                                                <tr class="history-row">
                                                    <td colspan="6" class="p-0 border-0">
                                                        <div id="history-{{ $vehicle->id }}" class="collapse bg-light px-4 py-3 border-bottom shadow-inner">
                                                            <div class="px-3 py-2 border-left-indigo bg-white rounded shadow-sm" style="border-left: 5px solid #6610f2;">
                                                                <h6 class="text-indigo font-weight-bold mb-3 small text-uppercase letter-spacing-1">
                                                                    <i class="fas fa-history mr-2"></i>Histórico Recente
                                                                </h6>
                                                                
                                                                @forelse($vehicle->attendances as $att)
                                                                    <div class="d-flex align-items-center justify-content-between p-2 mb-1 border-bottom">
                                                                        <div class="d-flex align-items-center">
                                                                            <span class="badge badge-pill {{ $att->type == 'installation' ? 'badge-success' : 'badge-primary' }} mr-3" style="font-size: 0.65rem; min-width: 90px;">
                                                                                @php
                                                                                    $types = ['support' => 'SUPORTE', 'installation' => 'INSTALAÇÃO', 'administrative' => 'ADMIN', 'commercial' => 'COMER'];
                                                                                    echo $types[$att->type] ?? strtoupper($att->type);
                                                                                @endphp
                                                                            </span>
                                                                            <span class="text-dark font-weight-bold mr-3" style="font-size: 0.85rem;">{{ $att->created_at->format('d/m/Y H:i') }}</span>
                                                                            <span class="text-muted small">por {{ str_replace(' Admin', '', $att->user->name) }}</span>
                                                                        </div>
                                                                        <button onclick="event.stopPropagation(); viewAttendance('{{ route('support.log.view', $att->id) }}')" class="btn btn-xs btn-outline-indigo px-3">
                                                                            <i class="fas fa-eye mr-1"></i> VER ATENDIMENTO
                                                                        </button>
                                                                    </div>
                                                                @empty
                                                                    <div class="text-muted small py-2 text-center">Nenhum atendimento registrado.</div>
                                                                @endforelse
                                                            </div>
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
                            <td colspan="3" class="text-center py-5">
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

    /* 🦓 ZEBRADO MANUAL (FIX) */
    .bg-zebra { background-color: #f2f4f7 !important; }
    .vehicle-row:hover { background-color: #e9ecef !important; }
    
    /* 🌓 DARK MODE RASTERTECH */
    .dark-mode .bg-light { background-color: #1a1a2e !important; }
    .dark-mode .bg-white { background-color: #16213e !important; }
    .dark-mode .bg-zebra { background-color: #1c2a4d !important; }
    .letter-spacing-1 { letter-spacing: 1px; }

    /* 🛡️ MODAL CUSTOMIZADO (80% DA TELA) */
    .modal-attendance-dialog {
        max-width: 80vw !important;
        width: 80vw !important;
        margin: 10vh auto !important;
    }
    .modal-attendance-content {
        height: 80vh !important;
        border-radius: 12px !important;
        border: 2px solid #6610f2 !important;
        box-shadow: 0 15px 50px rgba(0,0,0,0.3) !important;
    }
    .modal-attendance-body {
        background: #f8f9fa;
        overflow-y: auto;
        padding: 30px !important;
    }
    .log-content-area {
        background: white;
        padding: 25px;
        border-radius: 8px;
        min-height: 100%;
        font-family: 'Courier New', Courier, monospace;
        white-space: pre-wrap;
        font-size: 1rem;
        color: #333;
        border: 1px solid #dee2e6;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
    }
    .border-left-indigo { border-left: 4px solid #6610f2 !important; }
</style>

<!-- 👁️ VISOR DE ATENDIMENTO (MODAL) -->
<div class="modal fade" id="modalViewAttendance" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-attendance-dialog animate__animated animate__zoomIn" role="document">
        <div class="modal-content modal-attendance-content">
            <div class="modal-header border-0 bg-white py-3 px-4 shadow-sm align-items-center">
                <h4 class="modal-title text-bold text-indigo m-0">
                    <i class="fas fa-file-invoice mr-2"></i>Dossiê Técnico de Atendimento
                </h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true" style="font-size: 2rem;">&times;</span>
                </button>
            </div>
            <div class="modal-body modal-attendance-body">
                <div id="logLoader" class="text-center py-5">
                    <div class="spinner-border text-indigo" role="status" style="width: 3rem; height: 3rem;"></div>
                    <p class="mt-3 text-muted text-bold animate__animated animate__pulse animate__infinite">Acessando Dossiê no Servidor...</p>
                </div>
                <div id="logContent" class="log-content-area d-none"></div>
            </div>
            <div class="modal-footer border-0 bg-white px-4">
                <button type="button" class="btn btn-secondary px-4 shadow-sm" data-dismiss="modal">FECHAR VISOR</button>
            </div>
        </div>
    </div>
</div>

<script>
    function viewAttendance(url) {
        const modal = $('#modalViewAttendance');
        const content = $('#logContent');
        const loader = $('#logLoader');

        content.addClass('d-none').html('');
        loader.removeClass('d-none');
        modal.modal('show');

        // Busca o conteúdo TXT via AJAX
        $.get(url, function(data) {
            content.html(data).removeClass('d-none');
            loader.addClass('d-none');
        }).fail(function() {
            Swal.fire({
                icon: 'error',
                title: 'Erro de Acesso',
                text: 'Não foi possível ler o arquivo técnico no servidor.',
                confirmButtonColor: '#6610f2'
            });
            modal.modal('hide');
        });
    }

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
