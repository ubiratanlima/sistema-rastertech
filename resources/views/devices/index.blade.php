@extends('layouts.app')

@section('title', 'Hardware em Operação')

@section('content')
<div class="container-fluid pt-3">
    <!-- 🚀 CABEÇALHO LIMPO (TÍTULO E APOIO) -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 p-0">
            <h1 class="m-0 text-bold d-none d-sm-block text-dark" style="font-size: 2.2rem; letter-spacing: -1.2px;">
                <i class="fas fa-microchip mr-2 text-primary"></i>Hardware em Operação
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.6rem; letter-spacing: -1.2px;">
                <i class="fas fa-microchip mr-1 text-primary"></i>Equipamentos
            </h1>
            <p class="text-muted mb-0 font-weight-bold" style="font-size: 1.05rem; opacity: 0.8;">Controle de inventário de hardware e controle de aparelhos em operação.</p>
        </div>
    </div>

    <!-- 📊 GRID DE INVENTÁRIO (PADRÃO UNIVERSAL 1.0) -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <!-- 🛠️ CARD HEADER: BARRA DE AÇÕES INTEGRADA -->
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-list mr-2 text-primary"></i>Equipamentos Ativos
            </h3>

            <div class="card-tools ml-auto">
                <form action="/devices" method="GET" class="d-flex align-items-center">
                    <input type="hidden" name="view" value="{{ $view }}">
                    <!-- 🔍 PESQUISAR POR IDENTIFICADOR/CLIENTE -->
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Filtrar por RTECH, IMEI ou Cliente..." value="{{ $search }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default shadow-none border">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ⚙️ SELETOR DE VISÃO (TRI-ESTADO) -->
                    <div class="ml-5 d-flex align-items-center">
                        <label class="small font-weight-bold text-muted mr-2 mb-0">VISÃO:</label>
                        <select name="view" class="form-control form-control-sm shadow-sm" 
                                onchange="this.form.submit()"
                                style="width: 130px; border-radius: 6px; font-weight: bold; border-color: #dee2e6;">
                            <option value="active" {{ $view == 'active' ? 'selected' : '' }}>🟢 ATIVOS</option>
                            <option value="inventory" {{ $view == 'inventory' ? 'selected' : '' }}>📦 ESTOQUE</option>
                            <option value="canceled" {{ $view == 'canceled' ? 'selected' : '' }}>🚫 CANCELADOS</option>
                            <option value="trash" {{ $view == 'trash' ? 'selected' : '' }}>⛔ INATIVOS</option>
                        </select>
                    </div>

                    <!-- ➕ BOTÃO NOVO EQUIPAMENTO -->
                    <button type="button" 
                            class="btn btn-sm btn-primary ml-3 px-3 font-weight-bold shadow-sm"
                            onclick="openCreateWizard()"
                            style="border-radius: 6px; height: 31px; display: flex; align-items: center;">
                        <i class="fas fa-plus-circle mr-2"></i> NOVO EQUIPAMENTO
                    </button>

                    @if($search || $view !== 'active')
                        <a href="/devices" class="btn btn-xs btn-outline-danger ml-2" title="Limpar Filtros"><i class="fas fa-times"></i></a>
                    @endif
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-center text-dark font-weight-bold text-uppercase border-bottom" style="background-color: rgba(15, 23, 42, 0.02);">
                            <th class="py-3 d-none d-md-table-cell" style="width: 60px;">
                                <a href="?sort=id&direction={{ $sort == 'id' && $direction == 'asc' ? 'desc' : 'asc' }}&view={{ $view }}&search={{ $search }}" class="sort-link text-dark">
                                    ID {!! $sort == 'id' ? ($direction == 'asc' ? '<i class="fas fa-sort-up ml-1 text-primary"></i>' : '<i class="fas fa-sort-down ml-1 text-primary"></i>') : '<i class="fas fa-sort ml-1 opacity-50"></i>' !!}
                                </a>
                            </th>
                            <th class="text-left px-4">
                                <a href="?sort=internal_code&direction={{ $sort == 'internal_code' && $direction == 'asc' ? 'desc' : 'asc' }}&view={{ $view }}&search={{ $search }}" class="sort-link text-dark">
                                    RTECH CODE {!! $sort == 'internal_code' ? ($direction == 'asc' ? '<i class="fas fa-sort-up ml-1 text-primary"></i>' : '<i class="fas fa-sort-down ml-1 text-primary"></i>') : '<i class="fas fa-sort ml-1 opacity-50"></i>' !!}
                                </a>
                            </th>
                            <th class="d-none d-md-table-cell">
                                <a href="?sort=modelo&direction={{ $sort == 'modelo' && $direction == 'asc' ? 'desc' : 'asc' }}&view={{ $view }}&search={{ $search }}" class="sort-link text-dark">
                                    MODELO / HARDWARE {!! $sort == 'modelo' ? ($direction == 'asc' ? '<i class="fas fa-sort-up ml-1 text-primary"></i>' : '<i class="fas fa-sort-down ml-1 text-primary"></i>') : '<i class="fas fa-sort ml-1 opacity-50"></i>' !!}
                                </a>
                            </th>
                            <th class="d-none d-lg-table-cell">
                                <a href="?sort=chip&direction={{ $sort == 'chip' && $direction == 'asc' ? 'desc' : 'asc' }}&view={{ $view }}&search={{ $search }}" class="sort-link text-dark">
                                    CHIP VINCULADO {!! $sort == 'chip' ? ($direction == 'asc' ? '<i class="fas fa-sort-up ml-1 text-primary"></i>' : '<i class="fas fa-sort-down ml-1 text-primary"></i>') : '<i class="fas fa-sort ml-1 opacity-50"></i>' !!}
                                </a>
                            </th>
                            <th>
                                <a href="?sort=cliente&direction={{ $sort == 'cliente' && $direction == 'asc' ? 'desc' : 'asc' }}&view={{ $view }}&search={{ $search }}" class="sort-link text-dark">
                                    CLIENTE RESPONSÁVEL {!! $sort == 'cliente' ? ($direction == 'asc' ? '<i class="fas fa-sort-up ml-1 text-primary"></i>' : '<i class="fas fa-sort-down ml-1 text-primary"></i>') : '<i class="fas fa-sort ml-1 opacity-50"></i>' !!}
                                </a>
                            </th>
                            <th class="d-none d-sm-table-cell">
                                <a href="?sort=status&direction={{ $sort == 'status' && $direction == 'asc' ? 'desc' : 'asc' }}&view={{ $view }}&search={{ $search }}" class="sort-link text-dark">
                                    STATUS {!! $sort == 'status' ? ($direction == 'asc' ? '<i class="fas fa-sort-up ml-1 text-primary"></i>' : '<i class="fas fa-sort-down ml-1 text-primary"></i>') : '<i class="fas fa-sort ml-1 opacity-50"></i>' !!}
                                </a>
                            </th>
                            <th style="width: 140px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices_list as $device)
                        <tr>
                            <td class="text-center align-middle d-none d-md-table-cell text-muted">{{ $device->id }}</td>
                            <td class="align-middle px-4">
                                <div class="text-primary font-weight-bold" style="font-size: 1rem;">{{ $device->internal_code }}</div>
                                <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                    IMEI: {{ $device->imei }}
                                </div>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="badge badge-light border px-2 py-1 text-uppercase font-weight-normal">
                                    {{ $device->deviceModel->name ?? $device->model_description ?? 'PADRÃO' }}
                                </span>
                            </td>
                            <td class="text-center align-middle d-none d-lg-table-cell">
                                @if($device->gsmCard)
                                    <span class="text-pink">{{ $device->gsmCard->iccid ?? 'N/A' }}</span>
                                @else
                                    <span class="text-muted small">---</span>
                                @endif
                            </td>
                            <td class="align-middle d-none d-md-table-cell text-center">
                                {{ $device->customer->name ?? 'ESTOQUE' }}
                            </td>
                            <td class="text-center align-middle d-none d-sm-table-cell">
                                @if($device->trashed())
                                    <span class="badge bg-danger px-3 py-1 shadow-sm">INATIVADO</span>
                                @else
                                    <span class="badge {{ $device->status === 'active' ? 'bg-success' : ($device->status === 'canceled' ? 'bg-dark' : 'bg-warning') }} px-3 py-1 shadow-sm">
                                        {{ $device->status === 'active' ? 'ATIVO' : ($device->status === 'canceled' ? 'CANCELADO' : 'ESTOQUE') }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <!-- 👁️ DOSSIÊ TÁTICO -->
                                    <button class="btn btn-light btn-square border-right" title="Dossiê Técnico" onclick="openDeviceDossier(this)"
                                            data-id="{{ $device->id }}"
                                            data-internal-code="{{ $device->internal_code }}"
                                            data-imei="{{ $device->imei }}"
                                            data-model="{{ $device->deviceModel->name ?? $device->model_description ?? 'PADRÃO' }}"
                                            data-sim="{{ $device->gsmCard->iccid ?? '---' }}"
                                            data-customer="{{ $device->customer->name ?? 'ESTOQUE' }}"
                                            data-vehicle-plate="{{ $device->vehicle->plate ?? 'NÃO POSSUI' }}"
                                            data-vehicle-customer="{{ $device->vehicle->customer->name ?? '---' }}"
                                            data-status="{{ $device->status }}"
                                            data-created="{{ $device->created_at->format('d/m/Y H:i') }}"
                                            data-updated="{{ $device->updated_at->format('d/m/Y H:i') }}"
                                            data-cancelled-at="{{ $device->cancelled_at ? $device->cancelled_at->format('d/m/Y') : '' }}"
                                            data-reason="{{ $device->cancellation_reason }}">
                                        <i class="fas fa-eye fa-lg text-info"></i>
                                    </button>

                                    @if($device->trashed())
                                        <!-- ♻️ RESTAURAR EQUIPAMENTO -->
                                        <form action="/devices/{{ $device->id }}/restore" method="POST" class="d-inline">
                                            @csrf @method('PUT')
                                            <button type="submit" class="btn btn-light btn-square border-left" title="Restaurar Equipamento">
                                                <i class="fas fa-undo fa-lg text-success"></i>
                                            </button>
                                        </form>
                                    @else
                                        <!-- 🛠️ EDIÇÃO -->
                                        <button class="btn btn-light btn-square border-right" title="Editar Hardware" 
                                                onclick="openDeviceEdit(this)"
                                                data-id="{{ $device->id }}"
                                                data-internal-code="{{ $device->internal_code }}"
                                                data-imei="{{ $device->imei }}"
                                                data-model-id="{{ $device->device_model_id }}"
                                                data-gsm-card-id="{{ $device->gsm_card_id }}"
                                                data-customer-id="{{ $device->customer_id }}"
                                                data-vehicle-plate="{{ $device->vehicle->plate ?? 'NÃO POSSUI' }}"
                                                data-vehicle-customer="{{ $device->vehicle->customer->name ?? '---' }}"
                                                data-model-name="{{ $device->deviceModel->name ?? $device->model_description ?? 'PADRÃO' }}"
                                                data-sim="{{ $device->gsmCard->iccid ?? '' }}"
                                                data-sim-operator="{{ $device->gsmCard->operator ?? '---' }}"
                                                data-cancelled-at="{{ $device->cancelled_at ? $device->cancelled_at->format('Y-m-d') : '' }}"
                                                data-status="{{ $device->status }}">
                                            <i class="fas fa-tools fa-lg text-warning"></i>
                                        </button>

                                        <!-- ⚡ INATIVAR (BOTÃO POWER) -->
                                        <button type="button" class="btn btn-light btn-square text-danger" title="Inativar Equipamento" 
                                                onclick="confirmDeviceDeletion({{ $device->id }})">
                                            <i class="fas fa-power-off fa-lg"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-microchip fa-4x text-muted mb-4 opacity-25"></i>
                                <h4 class="text-muted font-weight-bold">Nenhum equipamento nesta visão</h4>
                                <p class="text-muted">Ajuste o filtro ou a busca para localizar ativos.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($devices_list->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
            {{ $devices_list->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 📦 MODAL: ASSISTENTE DE REGISTRO (WIZARD) -->
<div class="modal fade animate__animated animate__fadeIn" id="modalNovoDevice" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-warning text-dark border-0">
                <h5 class="modal-title font-weight-bold">
                    <i class="fas fa-plus-circle mr-2"></i> Assistente de Registro Moderno
                </h5>
                <button type="button" class="close text-dark" data-dismiss="modal">&times;</button>
            </div>
            
            <div class="modal-body p-4 bg-light">
                <!-- 🧭 STEP INDICATOR -->
                <div class="d-flex justify-content-center mb-4 text-center">
                    <div class="px-3">
                        <div class="badge badge-warning p-2 d-md-block mb-1 shadow-sm" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">1</div>
                        <small class="font-weight-bold text-uppercase" style="font-size: 0.65rem;">Configuração</small>
                    </div>
                    <div style="width: 60px; height: 2px; background: #ddd; margin-top: 20px;"></div>
                    <div class="px-3">
                        <div id="step-two-badge" class="badge badge-light p-2 d-md-block mb-1" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border: 2px solid #ddd;">2</div>
                        <small id="step-two-label" class="text-muted text-uppercase" style="font-size: 0.65rem;">Hardware</small>
                    </div>
                </div>

                <form action="/devices" method="POST" id="formWizardDevice">
                    @csrf
                    <input type="hidden" name="wizard" value="true">
                    <input type="hidden" name="equipments" id="equipments_json_hidden">

                    <!-- 🚥 ETAPA 1: CONFIGURAÇÃO COMUM -->
                    <div id="step1">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold small text-muted text-uppercase">Modelo de Hardware</label>
                                <select name="device_model_id" id="wizard_model_id" class="form-control" required style="height: 45px; border-radius: 8px;">
                                    <option value="">Selecione o Modelo...</option>
                                    @foreach($deviceModels as $m)
                                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="font-weight-bold small text-muted text-uppercase">Proprietário / Cliente</label>
                                <select name="customer_id" class="form-control" style="height: 45px; border-radius: 8px;">
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="alert alert-info border-0 shadow-sm" style="border-left: 5px solid #17a2b8 !important; border-radius: 8px;">
                            <i class="fas fa-info-circle mr-2"></i> Você poderá adicionar um único aparelho ou uma lista massiva na próxima etapa.
                        </div>

                        <div class="text-right mt-4">
                            <button type="button" class="btn btn-warning px-5 font-weight-bold" onclick="nextStep()" style="height: 45px; border-radius: 8px;">
                                PROXIMO PASSO <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- 🚥 ETAPA 2: LISTAGEM DE IMEIs -->
                    <div id="step2" style="display: none;">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="card border-0 shadow-sm" style="border-radius: 12px; height: 100%;">
                                    <div class="card-body">
                                        <label class="font-weight-bold small text-muted text-uppercase">Identificador (IMEI)</label>
                                        <div class="input-group mb-3">
                                            <input type="text" id="input_imei" class="form-control" placeholder="Ex: 866..." style="height: 45px; border-radius: 8px 0 0 8px;">
                                            <div class="input-group-append">
                                                <button class="btn btn-warning font-weight-bold" type="button" onclick="addEquipmentToWizard()">FIXAR</button>
                                            </div>
                                        </div>
                                        <p class="small text-muted"><i class="fas fa-lightbulb mr-1 text-warning"></i> Digite e clique em <b>FIXAR</b> para ir empilhando os aparelhos.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-7">
                                <div class="bg-white p-3 border" style="border-radius: 12px; min-height: 200px; max-height: 300px; overflow-y: auto;">
                                    <label class="font-weight-bold small text-muted text-uppercase d-block mb-3 border-bottom pb-2">Hardware na Fila de Registro</label>
                                    <div id="equipment_list_wizard">
                                        <div class="text-center py-4 text-muted opacity-50">
                                            <i class="fas fa-barcode fa-3x mb-2"></i>
                                            <p>Nenhum equipamento adicionado</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-between d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-light px-4 font-weight-bold" onclick="prevStep()" style="border-radius: 8px;">
                                <i class="fas fa-arrow-left mr-2"></i> VOLTAR
                            </button>
                            <button type="button" class="btn btn-success px-5 font-weight-bold shadow-sm" onclick="submitWizard()" id="btnSubmitWizard" disabled style="height: 45px; border-radius: 8px; font-size: 1.1rem;">
                                CONCLUIR REGISTRO <i class="fas fa-cloud-upload-alt ml-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .sort-link { text-decoration: none !important; color: inherit !important; display: block; filter: grayscale(1); transition: 0.2s; }
    .sort-link:hover { filter: grayscale(0); background: rgba(0,0,0,0.03); }
    .btn-square { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .badge-light { background: #f8f9fa; color: #6c757d; font-weight: 600; }
    .text-pink { color: #d63384 !important; background: rgba(214, 51, 132, 0.05); padding: 2px 5px; border-radius: 4px; }
    .blink { animation: blinker 1.5s linear infinite; }
    @keyframes blinker { 50% { opacity: 0.5; } }

    /* 🇧🇷 ESTILO DA PLACA MERCOSUL (OFICIAL RASTERTECH) */
    .mercosul-plate {
        width: 144px;
        height: 53px;
        border: 2px solid #333;
        border-radius: 6px;
        background: white;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        line-height: 1;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
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

    /* 📱 ESTILO DO CARTÃO GSM TÁTICO */
    .gsm-card-mini {
        width: 140px;
        height: 80px;
        background: #f1f5f9;
        border: 2px solid #cbd5e1;
        border-radius: 10px;
        position: relative;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    .gsm-card-mini .card-brand {
        background: #e11d48;
        color: #fff;
        font-size: 8px;
        padding: 2px 8px;
        font-weight: bold;
        text-transform: uppercase;
    }
    .gsm-card-mini .card-chip {
        width: 28px;
        height: 22px;
        background: linear-gradient(135deg, #ffd700 0%, #daa520 100%);
        border-radius: 4px;
        position: absolute;
        top: 38px;
        left: 12px;
        border: 1px solid rgba(0,0,0,0.1);
        z-index: 10;
    }
    .gsm-card-mini .chip-lines {
        width: 100%;
        height: 1px;
        background: rgba(0,0,0,0.1);
        position: absolute;
        top: 50%;
    }
</style>

@endsection

@push('scripts')
<script>
    // 🧱 REGISTRO GLOBAL DE CLIENTES E MODELOS
    const globalCustomers = {!! json_encode($customers->mapWithKeys(fn($c) => [$c->id => $c->name])) !!};
    const globalModels = {!! json_encode($deviceModels->mapWithKeys(fn($m) => [$m->id => $m->name])) !!};
    const globalSims = {!! json_encode($sims->map(fn($s) => ['id' => $s->id, 'iccid' => $s->iccid])) !!};
    const globalVehicles = {!! json_encode($freeVehicles->map(fn($v) => ['id' => $v->id, 'plate' => $v->plate, 'customer_id' => $v->customer_id])) !!};

    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'SUCESSO!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
    @endif

    @if(session('error'))
        Swal.fire({ icon: 'error', title: 'ERRO TÉCNICO', text: "{{ session('error') }}", confirmButtonColor: '#dc3545' });
    @endif

    /**
     * 🧙‍♂️ ENGINE DO WIZARD: GESTÃO DE HARDWARE
     */
    let wizardEquipments = [];

    window.openCreateWizard = function() {
        $('#modalNovoDevice').modal('show');
        resetWizard();
    };

    window.nextStep = function() {
        if(!$('#wizard_model_id').val()) {
            Swal.fire({ icon: 'warning', title: 'CAMPO OBRIGATÓRIO', text: 'Selecione o modelo do hardware antes de prosseguir.' });
            return;
        }
        $('#step1').hide();
        $('#step2').fadeIn();
        $('#step-two-badge').removeClass('badge-light').addClass('badge-warning').css('border', 'none');
        $('#step-two-label').removeClass('text-muted').addClass('text-warning font-weight-bold');
    };

    window.prevStep = function() {
        $('#step2').hide();
        $('#step1').fadeIn();
        $('#step-two-badge').addClass('badge-light').removeClass('badge-warning').css('border', '2px solid #ddd');
        $('#step-two-label').addClass('text-muted').removeClass('text-warning font-weight-bold');
    };

    window.addEquipmentToWizard = function() {
        const imei = $('#input_imei').val().trim();
        const modelName = $('#wizard_model_id option:selected').text();

        if (imei.length < 5) return;

        wizardEquipments.push({ imei: imei, model: modelName });
        $('#input_imei').val('').focus();
        updateWizardList();
    };

    function updateWizardList() {
        const container = $('#equipment_list_wizard');
        if (wizardEquipments.length === 0) {
            container.html('<div class="text-center py-4 text-muted">Aguardando inclusão...</div>');
            $('#btnSubmitWizard').prop('disabled', true);
            return;
        }

        let html = '';
        wizardEquipments.forEach((item, index) => {
            html += `
                <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded animate__animated animate__slideInLeft" style="border-left: 3px solid #ffc107;">
                    <div>
                        <span class="small text-muted font-weight-bold mr-2 text-uppercase">IMEI:</span>
                        <span class="font-weight-bold text-dark">${item.imei}</span>
                    </div>
                    <button type="button" class="btn btn-xs text-danger" onclick="removeEquipmentFromWizard(${index})">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
        });
        container.html(html);
        $('#btnSubmitWizard').prop('disabled', false);
    }

    window.removeEquipmentFromWizard = function(index) {
        wizardEquipments.splice(index, 1);
        updateWizardList();
    };

    window.submitWizard = function() {
        $('#equipments_json_hidden').val(JSON.stringify(wizardEquipments));
        Swal.fire({
            title: 'Confirmar Lote?',
            text: `Deseja registrar ${wizardEquipments.length} aparelhos simultaneamente?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'SIM, REGISTRAR LOTE',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                $('#formWizardDevice')[0].submit();
            }
        });
    };

    function resetWizard() {
        wizardEquipments = [];
        $('#formWizardDevice')[0].reset();
        $('#equipment_list_wizard').html('<div class="text-center py-4 text-muted">Aguardando inclusão...</div>');
        prevStep();
    }

    /**
     * 👁️ DOSSIÊ TÁTICO: VISUALIZAÇÃO DE DEVICE
     */
    window.openDeviceDossier = function(el) {
        const data = $(el).data();
        
        let cancellationBox = '';
        if (data.status === 'canceled') {
            cancellationBox = `
                <div class="mt-4 p-3 rounded" style="background: rgba(220, 53, 69, 0.08); border: 1px solid #dc3545;">
                    <label class="small text-danger mb-1 d-block font-weight-bold text-uppercase"><i class="fas fa-ban mr-1"></i> MOTIVO DO CANCELAMENTO</label>
                    <div style="max-height: 120px; overflow-y: auto; font-size: 0.95rem; color: #721c24;">${data.reason || 'Não informado.'}</div>
                    <div class="mt-2 text-right text-muted small">Auditado em: ${data.cancelledAt || '--'}</div>
                </div>`;
        }

        Swal.fire({
            title: '<i class="fas fa-microchip mr-2 text-warning"></i> DOSSIÊ DE HARDWARE',
            width: '600px',
            confirmButtonText: 'FECHAR',
            confirmButtonColor: '#6c757d',
            html: `
                <div class="text-left px-2" style="font-family: 'Source Sans Pro', sans-serif;">
                    <!-- 💎 CORE GRID (HARDWARE + SIM) -->
                    <div class="row no-gutters mb-3">
                        <div class="col-7 pr-2">
                            <label class="small text-muted mb-2 d-block font-weight-bold text-uppercase">Rastreador em USO</label>
                            <div class="rounded shadow-sm px-3 d-flex flex-column justify-content-center align-items-center position-relative" style="background: #1e293b; height: 120px; border-radius: 15px !important; border: 2px solid #334155;">
                                <div class="text-uppercase mb-1 text-center" style="color: #94a3b8; font-size: 1.35rem; font-weight: 900; letter-spacing: 1px; width: 100%;">
                                    ${data.internalCode}
                                </div>
                                <div class="text-uppercase mb-2 text-center" style="color: rgba(148, 163, 184, 0.4); font-size: 0.55rem; font-weight: 700; letter-spacing: 2px; width: 100%;">
                                    ${data.model} CORE / V1.0
                                </div>
                                <div class="d-flex align-items-center justify-content-center w-100">
                                    <i class="fas fa-microchip mr-2" style="color: #94a3b8; font-size: 1.5rem;"></i>
                                    <span style="color: #94a3b8; font-size: 1rem; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">${data.imei}</span>
                                </div>
                                <div style="position: absolute; bottom: 8px; right: 15px; color: #475569; font-size: 0.6rem; font-weight: 900; letter-spacing: 1px; opacity: 0.6;">ANATEL</div>
                            </div>
                        </div>
                        <div class="col-5 pl-1">
                            <label class="small text-muted mb-2 d-block font-weight-bold text-uppercase">Conectividade</label>
                            <div class="p-2 rounded d-flex flex-column align-items-center justify-content-center text-center" style="border: 1px dashed #cbd5e1; border-radius: 15px !important; height: 120px; background: #fff;">
                                <i class="fas fa-sim-card mb-2 text-success" style="font-size: 2.7rem; opacity: 0.85;"></i>
                                <div class="h6 font-weight-bold text-pink mb-1" style="letter-spacing: -0.5px; font-size: 1rem;">${data.sim || '---'}</div>
                                <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">Link Ativo</div>
                            </div>
                        </div>
                    </div>

                    <!-- 👥 PROPRIEDADE E RESPONSABILIZAÇÃO -->
                    <div class="p-3 bg-light rounded border-left mb-3" style="border-left: 4px solid #ffc107 !important; border-radius: 10px !important;">
                        <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">Cliente Responsável</label>
                        <div class="h6 font-weight-bold mb-1 text-dark">${data.customer}</div>
                        <div class="small text-muted">Cadastrado em: ${data.created}</div>
                    </div>
                    
                    <!-- 📟 ATIVOS VINCULADOS (VEÍCULO) -->
                    <div class="p-3 border rounded shadow-sm position-relative animate__animated animate__fadeIn" style="border-radius: 12px !important; border-left: 5px solid #17a2b8 !important; min-height: 100px; background: #f8fafc;">
                        <div class="row align-items-center">
                            <div class="col-5 d-flex align-items-center justify-content-center">
                                ${data.vehiclePlate !== 'NÃO POSSUI' ? `
                                    <div class="mercosul-plate shadow-none">
                                        <div class="plate-header">BRASIL</div>
                                        <div class="plate-number font-weight-bold" style="font-size: 1.3rem;">${data.vehiclePlate}</div>
                                    </div>
                                ` : `
                                    <div class="mercosul-plate" style="opacity: 0.3; filter: grayscale(1); border-style: dashed;">
                                        <div class="plate-header">BRASIL</div>
                                        <div class="plate-number" style="font-size: 1.2rem;">000-0000</div>
                                    </div>
                                `}
                            </div>
                            <div class="col-7 text-center">
                                <label class="small font-weight-bold text-info text-uppercase mb-1"><i class="fas fa-link mr-1"></i>Vínculo de Operação</label>
                                <div class="small text-muted font-weight-bold d-block">PROPRIEDADE: ${data.vehicleCustomer || 'ESTOQUE'}</div>
                                <div class="small text-muted" style="font-size: 0.65rem;">Sincronizado: ${data.updated}</div>
                            </div>
                        </div>
                    </div>

                    ${cancellationBox}
                </div>`
        });
    };

    /**
     * 🛠️ EDIÇÃO TÁTICA E AUDITORIA
     */
    window.openDeviceEdit = function(el) {
        const btn = $(el);
        const id = btn.data('id');
        const currentInternalCode = btn.data('internal-code');
        const currentImei = btn.data('imei');
        const currentStatus = btn.data('status');
        const currentCustomerId = btn.data('customer-id');
        const dbReason = btn.data('reason') || '';
        const dbDate = btn.data('cancelled-at') || '';
        const vehiclePlate = btn.data('vehicle-plate') || 'NÃO POSSUI';
        const vehicleCustomer = btn.data('vehicle-customer') || '---';
        const currentSim = btn.data('sim') || '---';
        const modelName = btn.data('model-name') || 'PADRÃO';
        const simOperator = btn.data('sim-operator') || '---';
        const cacheKey = `device_cancel_draft_${id}`;
        const draftReason = localStorage.getItem(cacheKey) || dbReason;

        let customerOpts = '';
        Object.entries(globalCustomers).forEach(([cid, cname]) => {
            customerOpts += `<option value="${cid}" ${cid == currentCustomerId ? 'selected' : ''}>${cname}</option>`;
        });

        let vehicleBox = '';
        if (vehiclePlate !== 'NÃO POSSUI') {
            vehicleBox = `
                <div class="row bg-light p-3 border rounded shadow-sm position-relative animate__animated animate__fadeIn mb-3" id="unlink_section" style="border-radius: 12px !important; border-left: 5px solid #17a2b8 !important; min-height: 110px;">
                    <div class="col-5 d-flex align-items-center justify-content-center">
                        <div class="mercosul-plate shadow-none">
                            <div class="plate-header">BRASIL</div>
                            <div class="plate-number font-weight-bold" style="font-size: 1.3rem;">${vehiclePlate}</div>
                        </div>
                    </div>
                    <div class="col-7 d-flex flex-column align-items-center justify-content-center text-center">
                        <div class="small font-weight-bold text-info text-uppercase mb-1"><i class="fas fa-link mr-1"></i>Vínculo Ativo</div>
                        <div class="small text-muted font-weight-bold text-truncate d-block mb-2" style="max-width: 200px;">FROTA: ${vehicleCustomer}</div>
                        
                        <div class="w-100 mt-1">
                             <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold px-3 py-1 shadow-sm" style="border-radius: 6px; border-width: 2px;" onclick="$('#unlink_form_device').fadeIn(); $(this).fadeOut();">
                                💔 DESVINCULAR VEÍCULO
                            </button>
                        </div>
                    </div>

                    <div id="unlink_form_device" class="col-12 mt-3" style="display: none; border-top: 1px dashed #ddd; padding-top: 15px;">
                        <label class="font-weight-bold small text-danger text-uppercase">Motivo da Desvinculação</label>
                        <textarea id="unlink_reason" class="form-control form-control-sm" rows="2" placeholder="Ex: Manutenção, upgrade ou devolução..."></textarea>
                    </div>
                </div>`;
        } else {
            // 📟 BOX DE VÍNCULO RÁPIDO PARA VEÍCULO (NOVO)
            const clientVehicles = globalVehicles.filter(v => parseInt(v.customer_id) === parseInt(currentCustomerId));
            const vehicleOptions = clientVehicles.map(v => `<option value="${v.id}">${v.plate}</option>`).join('');
            
            vehicleBox = `
                <div class="row bg-light p-3 border rounded shadow-sm animate__animated animate__fadeIn mb-3" style="border-radius: 12px !important; border-left: 5px solid #6c757d !important; background: #fff8f0; border: 1px dashed #fd7e14; min-height: 110px;">
                    <div class="col-5 d-flex align-items-center justify-content-center">
                        <div class="mercosul-plate" style="opacity: 0.3; filter: grayscale(1); border-style: dashed;">
                            <div class="plate-header">BRASIL</div>
                            <div class="plate-number" style="font-size: 1.2rem;">000-0000</div>
                        </div>
                    </div>
                    <div class="col-7 d-flex flex-column align-items-center justify-content-center text-center px-4">
                        <div class="small font-weight-bold text-muted text-uppercase mb-2">SEM VEÍCULO VINCULADO</div>
                        <select id="new_vehicle_id" class="form-control form-control-sm shadow-sm" style="border-radius: 6px; border: 1px dashed #fd7e14; background: #fffcf5; font-weight: bold; color: #856404;">
                            <option value="">-- VINCULAR CARRO --</option>
                            ${vehicleOptions || '<option disabled>Nenhum carro livre encontrado</option>'}
                        </select>
                        <div class="small text-muted mt-2" style="font-size: 0.6rem;"><i class="fas fa-search mr-1"></i>Placa na frota livre do cliente</div>
                    </div>
                </div>`;
        }

        let chipActionBox = '';
        if (currentSim !== '---') {
            chipActionBox = `
                <div id="chip_active_section" class="animate__animated animate__fadeIn h-100 d-flex flex-column align-items-center justify-content-center p-2 rounded shadow-none" style="border: 1px dashed #cbd5e1; border-radius: 15px !important; width: 100%; height: 145px;">
                    <div class="small text-muted font-weight-bold text-uppercase mb-2" style="font-size: 1rem;">CHIP ATIVO | ${simOperator}</div>
                    <i class="fas fa-sim-card mb-1 text-success" style="font-size: 2.2rem; opacity: 1;"></i>
                    <div class="h6 font-weight-bold text-pink mb-1" style="letter-spacing: -0.5px; font-size: 0.85rem;">${currentSim}</div>
                    <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold mt-1 px-3 py-1 shadow-sm" style="border-radius: 6px; border-width: 2px;" 
                            onclick="document.getElementById('unlink_chip_hidden').value='1'; this.closest('#chip_active_section').style.opacity='0.4'; this.innerHTML='⏳ AGUARDANDO SALVAR'; this.disabled=true;">
                        💔 DESVINCULAR
                    </button>
                    <input type="hidden" id="unlink_chip_hidden" value="0">
                </div>`;
        } else {
            let simOptions = globalSims.map(s => `<option value="${s.id}">${s.iccid}</option>`).join('');
            chipActionBox = `
                <div class="p-3 rounded h-100 d-flex flex-column justify-content-center align-items-center" style="background: transparent; height: 145px; border: 1px dashed #cbd5e1; border-radius: 15px !important; width: 100%;">
                    <div class="small text-primary font-weight-bold text-uppercase mb-2" style="font-size: 0.9rem;">VINCULAR CHIP</div>
                    <i class="fas fa-sim-card fa-3x text-muted opacity-20 mb-3"></i>
                    <select id="new_gsm_card_id" class="form-control form-control-sm shadow-sm" style="border-radius: 6px; border: 1px dashed #007bff; font-weight: bold; background: #f0f7ff; color: #0056b3;">
                        <option value="">-- SELECIONE O CHIP --</option>
                        ${simOptions}
                    </select>
                    <input type="hidden" id="unlink_chip_hidden" value="0">
                </div>`;
        }

        Swal.fire({
            title: '<i class="fas fa-microchip mr-2 text-warning"></i> GESTÃO DE HARDWARE',
            didOpen: () => {
                const statusSelect = Swal.getPopup().querySelector('#edit_status_device');
                const auditBox = Swal.getPopup().querySelector('#device_audit_fields');
                const reasonArea = Swal.getPopup().querySelector('#edit_reason_device');
                
                statusSelect.addEventListener('change', (e) => {
                    auditBox.style.display = (e.target.value === 'canceled') ? 'block' : 'none';
                    if (e.target.value === 'canceled' && !$('#edit_date_device').val()) {
                        $('#edit_date_device').val(new Date().toISOString().split('T')[0]);
                    }
                });
                
                if (reasonArea) {
                    reasonArea.addEventListener('input', (e) => localStorage.setItem(cacheKey, e.target.value));
                }
            },
            html: `
                <div class="text-left px-2">
                    <div class="row mb-3">
                        <div class="col-6">
                             <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">RTECH CODE</label>
                             <input type="text" id="edit_internal_code_device" class="form-control font-weight-bold text-primary" value="${currentInternalCode}" style="border-radius: 8px; height: 45px; background: #f0f7ff; border: 1px solid #007bff;">
                        </div>
                        <div class="col-6">
                             <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">IMEI / ANATEL</label>
                             <input type="text" id="edit_imei_device" class="form-control font-weight-bold" value="${currentImei}" style="border-radius: 8px; height: 45px;">
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-8">
                            <label class="font-weight-bold small text-muted text-uppercase">Cliente Responsável</label>
                            <select id="edit_customer_device" class="form-control" style="border-radius: 8px; height: 45px;">${customerOpts}</select>
                        </div>
                        <div class="col-4">
                            <label class="font-weight-bold small text-muted text-uppercase">Status</label>
                            <select id="edit_status_device" class="form-control font-weight-bold" style="border-radius: 8px; height: 45px;">
                                <option value="active" ${currentStatus === 'active' ? 'selected' : ''}>🟢 ATIVO</option>
                                <option value="inactive" ${currentStatus === 'inactive' ? 'selected' : ''}>📦 ESTOQUE</option>
                                <option value="canceled" ${currentStatus === 'canceled' ? 'selected' : ''}>🚫 CANCELADO</option>
                            </select>
                        </div>
                    </div>

                    <div class="row align-items-end mb-4">
                        <div class="col-7">
                            <label class="font-weight-bold small text-muted text-uppercase mb-2">Rastreador em USO</label>
                            <div class="rounded shadow-sm px-3 d-flex flex-column justify-content-center align-items-center position-relative" style="background: #1e293b; height: 120px; border-radius: 15px !important; border: 2px solid #334155;">
                                <div class="text-uppercase mb-1 text-center" style="color: #94a3b8; font-size: 1.4rem; font-weight: 900; letter-spacing: 1px; width: 100%;">
                                    ${currentInternalCode}
                                </div>
                                <div class="text-uppercase mb-2 text-center" style="color: rgba(148, 163, 184, 0.4); font-size: 0.55rem; font-weight: 700; letter-spacing: 2px; width: 100%;">
                                    ${modelName} CORE / V1.0
                                </div>
                                <div class="d-flex align-items-center justify-content-center w-100">
                                    <i class="fas fa-microchip mr-2" style="color: #94a3b8; font-size: 1.8rem;"></i>
                                    <span style="color: #94a3b8; font-size: 0.9rem; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">${currentImei}</span>
                                </div>
                                <div style="position: absolute; bottom: 10px; right: 15px; color: #475569; font-size: 0.65rem; font-weight: 900; letter-spacing: 1px; opacity: 0.6;">ANATEL</div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="d-flex align-items-center justify-content-center" style="height: 145px;">
                                ${chipActionBox}
                            </div>
                        </div>
                    </div>
                    
                    ${vehicleBox}

                    <div id="device_audit_fields" class="mt-3" style="display: ${currentStatus === 'canceled' || draftReason ? 'block' : 'none'}">
                        <div class="row">
                            <div class="col-12 mb-2">
                                <label class="font-weight-bold small text-danger text-uppercase"><i class="fas fa-calendar-alt mr-1"></i> Data Retroativa</label>
                                <input type="date" id="edit_date_device" class="form-control" value="${dbDate || new Date().toISOString().split('T')[0]}" style="border-radius: 8px; border: 1px solid #dc3545;">
                            </div>
                            <div class="col-12">
                                <label class="font-weight-bold small text-danger text-uppercase">Motivo do Cancelamento</label>
                                <textarea id="edit_reason_device" class="form-control" rows="3" style="border-radius: 8px; border: 1px dashed #dc3545; background: #fff8f8;">${draftReason}</textarea>
                            </div>
                        </div>
                    </div>
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'SALVAR ALTERAÇÕES',
            confirmButtonColor: '#28a745',
            preConfirm: () => {
                const status = $('#edit_status_device').val();
                const reason = $('#edit_reason_device').val();
                const unlinkReason = $('#unlink_reason').val();
                const isUnlinking = $('#unlink_form_device').is(':visible');
                const isUnlinkingChip = $('#unlink_chip_hidden').val() === '1';

                if (status === 'canceled' && (!reason || reason.trim().length < 5)) {
                    Swal.showValidationMessage('Descreva um motivo de cancelamento válido.');
                    return false;
                }

                if (isUnlinking && (!unlinkReason || unlinkReason.trim().length < 5)) {
                    Swal.showValidationMessage('Informe o motivo da desinstalação (veículo).');
                    return false;
                }

                return $.ajax({
                    url: `/devices/${id}`,
                    method: 'PUT',
                    data: {
                        internal_code: $('#edit_internal_code_device').val(),
                        imei: $('#edit_imei_device').val(),
                        status: status,
                        customer_id: $('#edit_customer_device').val() || null,
                        cancellation_reason: reason,
                        cancelled_at: $('#edit_date_device').val(),
                        unlink_vehicle: isUnlinking ? 1 : 0,
                        unlink_reason: unlinkReason,
                        unlink_chip: isUnlinkingChip ? 1 : 0,
                        new_gsm_card_id: $('#new_gsm_card_id').val() || null,
                        new_vehicle_id: $('#new_vehicle_id').val() || null,
                        _token: '{{ csrf_token() }}'
                    }
                }).then(() => {
                    localStorage.removeItem(cacheKey);
                }).catch(error => {
                    Swal.showValidationMessage(error.responseJSON?.message || 'Erro ao sincronizar dados.');
                });
            }
        }).then((result) => result.isConfirmed && location.reload());
    };

    /**
     * 🟢 ATIVAÇÃO RÁPIDA (DO ESTOQUE)
     */
    window.quickActivate = function(id, imei) {
        Swal.fire({
            title: 'Ativar Equipamento?',
            text: 'Deseja marcar este aparelho como ATIVO e vincular as operações?',
            icon: 'info',
            showCancelButton: true,
            confirmButtonText: 'SIM, ATIVAR',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/devices/${id}`,
                    method: 'PUT',
                    data: {
                        status: 'active',
                        imei: imei,
                        _token: '{{ csrf_token() }}'
                    },
                    success: () => location.reload()
                });
            }
        });
    };

    /**
     * 🚫 CANCELAMENTO TÁTICO (ACTIVE -> CANCELED)
     */
    window.cancelDevice = function(id, imei) {
        Swal.fire({
            title: '<i class="fas fa-ban text-danger mr-2"></i> CANCELAR HARDWARE',
            html: `
                <div class="text-left px-2">
                    <p class="small text-muted mb-1 font-weight-bold">EQUIPAMENTO: <span class="text-dark">${imei}</span></p>
                    <label class="font-weight-bold small text-danger text-uppercase">MOTIVO DO CANCELAMENTO (MÍN. 5 CARACT.)</label>
                    <textarea id="swal_cancel_reason" class="form-control" rows="3" placeholder="Ex: Devolvido pelo cliente, defeito físico..." style="border-radius: 8px; border: 1px dashed #dc3545; background: #fff8f8;"></textarea>
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'CONFIRMAR CANCELAMENTO',
            confirmButtonColor: '#dc3545',
            preConfirm: () => {
                const reason = $('#swal_cancel_reason').val();
                if (!reason || reason.trim().length < 5) {
                    Swal.showValidationMessage('Descreva um motivo válido.');
                    return false;
                }
                return $.ajax({
                    url: `/devices/${id}`,
                    method: 'PUT',
                    data: {
                        status: 'canceled',
                        imei: imei,
                        cancellation_reason: reason,
                        cancelled_at: new Date().toISOString().split('T')[0],
                        _token: '{{ csrf_token() }}'
                    }
                });
            }
        }).then((result) => result.isConfirmed && location.reload());
    };

    /**
     * 🔄 REATIVAÇÃO (CANCELED -> ACTIVE)
     */
    window.restoreDevice = function(id, imei) {
        Swal.fire({
            title: 'Reativar Equipamento?',
            text: `Deseja remover o cancelamento do aparelho ${imei}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'SIM, REATIVAR',
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/devices/${id}`,
                    method: 'PUT',
                    data: {
                        status: 'active',
                        imei: imei,
                        cancellation_reason: '', // Clear reason
                        _token: '{{ csrf_token() }}'
                    },
                    success: () => location.reload()
                });
            }
        });
    };

    /**
     * ⚡ INATIVAÇÃO (LIXEIRA TÁTICA)
     */
    window.confirmDeviceDeletion = function(id) {
        Swal.fire({
            title: 'Inativar Equipamento?',
            text: 'O aparelho será movido para o estado INATIVO. Esta ação é reversível.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'SIM, INATIVAR'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/devices/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: () => location.reload(),
                    error: (xhr) => Swal.fire({
                        title: `
                            <div class="mb-3"><i class="fas fa-exclamation-triangle" style="color: #f39c12; font-size: 5.5rem;"></i></div>
                            <span style="color: #f39c12; font-weight: 800; font-size: 1.8rem;">AÇÃO PROIBIDA</span>`,
                        html: `<div class="mt-2" style="font-size: 1.1rem; color: #555;">${xhr.responseJSON.message || 'Operação bloqueada pelo sistema.'}</div>`,
                        confirmButtonText: 'ENTENDI',
                        confirmButtonColor: '#f39c12'
                    })
                });
            }
        });
    };
</script>
@endpush
