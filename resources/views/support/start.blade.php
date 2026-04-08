@extends('layouts.app')

@section('title', 'Atendimento')

@section('content')
<div class="container-fluid animate__animated animate__fadeIn">
    <!-- 🎧 HEADER -->
    <div class="row m-0 mb-4 align-items-center">
        <div class="col-md-8 p-0">
            <h1 class="m-0 text-bold" style="font-size: 2rem;">
                <i class="fas fa-terminal mr-2 text-indigo"></i>Atendimento
            </h1>
            <p class="text-muted mb-0">Intervenção técnica monitorada | Protocolo: <span class="text-indigo text-bold">#{{ date('Ymd') }}-{{ $vehicle->id }}</span></p>
        </div>
        <div class="col-md-4 p-0 text-right">
            <a href="{{ route('support.customers') }}" class="btn btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left mr-1"></i> VOLTAR
            </a>
        </div>
    </div>

    <div class="row">
        <!-- 🛰️ COLUNA ESQUERDA: DADOS CONSOLIDADOS -->
        <div class="col-lg-4">
            <!-- BLOCO 1: O VEÍCULOS -->
            <div class="card card-outline card-indigo shadow-sm mb-4" style="border-radius: 12px;">
                <div class="card-header border-0 bg-transparent py-3">
                    <h3 class="card-title text-bold"><i class="fas fa-car mr-2 text-indigo"></i>DADOS DO ATIVO</h3>
                </div>
                <div class="card-body pt-0 text-center">
                    <div class="mercosul-plate mx-auto mb-3 shadow-sm">
                        <div class="plate-header">BRASIL</div>
                        <div class="plate-number">{{ $vehicle->plate }}</div>
                    </div>
                    <h5 class="text-bold mb-1">{{ $vehicle->brand }}</h5>
                    <p class="text-muted text-uppercase small">{{ $vehicle->model }}</p>
                    <div class="p-3 bg-light rounded text-left mt-3" style="font-size: 0.9rem;">
                        <div class="mb-2">
                            <span class="text-muted d-block small text-uppercase font-weight-bold">Cliente:</span>
                            <span class="text-bold text-dark d-block" style="font-size: 1.1rem;">{{ $customer->name }}</span>
                        </div>
                        <div>
                            <span class="text-muted d-block small text-uppercase font-weight-bold">RTECH Code:</span>
                            <span class="text-indigo text-bold d-block" style="font-size: 1.1rem;">{{ $vehicle->devices->first()->model_description ?? '---' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLOCO 2 & 3: HARDWARE E CONECTIVIDADE -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-body p-0">
                    <div class="p-3 border-bottom">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-microchip mr-2 text-primary"></i>
                            <span class="text-bold">HARDWARE (MÓDULO)</span>
                        </div>
                        <div class="text-muted small">IMEI: <span class="text-dark font-weight-bold">{{ $vehicle->devices->first()->imei ?? 'SEM VÍNCULO' }}</span></div>
                    </div>
                    <div class="p-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-sim-card mr-2 text-success"></i>
                            <span class="text-bold">CONECTIVIDADE (SIM)</span>
                        </div>
                        <div class="text-muted small">NÚMERO: <span class="text-dark font-weight-bold">{{ $vehicle->devices->first()->gsmCard->phone_number ?? '---' }}</span></div>
                        <div class="text-muted small">OPERADORA: <span class="text-dark font-weight-bold">{{ $vehicle->devices->first()->gsmCard->operator ?? '---' }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🛡️ COLUNA DIREITA: FORMULÁRIO E HISTÓRICO -->
        <div class="col-lg-8">
            <form id="formAttendance" action="{{ route('support.finish') }}" method="POST">
                @csrf
                <input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
                <input type="hidden" name="customer_id" value="{{ $customer->id }}">

                <div class="card card-outline card-indigo shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-header border-0 bg-transparent py-3 d-flex align-items-center">
                        <h3 class="card-title text-bold"><i class="fas fa-edit mr-2 text-indigo"></i>Novo Atendimento</h3>
                        
                        <!-- DROP DOWN DE TIPO (SE ADMIN) OU TEXTO FIXO -->
                        <div class="ml-auto" style="width: 200px;">
                            @if($allowTypeSelection)
                                <select name="type" class="form-control form-control-sm border-indigo text-bold">
                                    <option value="support" {{ $defaultType == 'support' ? 'selected' : '' }}>SUPORTE TÉCNICO</option>
                                    <option value="installation" {{ $defaultType == 'installation' ? 'selected' : '' }}>INSTALAÇÃO</option>
                                    <option value="administrative" {{ $defaultType == 'administrative' ? 'selected' : '' }}>ADMINISTRATIVO</option>
                                    <option value="commercial" {{ $defaultType == 'commercial' ? 'selected' : '' }}>COMERCIAL</option>
                                </select>
                            @else
                                <input type="hidden" name="type" value="{{ $defaultType }}">
                                <span class="badge badge-indigo px-3 py-2">
                                    {{ $defaultType == 'installation' ? 'INSTALAÇÃO' : 'SUPORTE TÉCNICO' }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body mt-0">
                        <div class="form-group">
                            <label class="text-muted font-weight-bold text-uppercase" style="font-size: 0.85rem;">Histórico / Relato Técnico</label>
                            <textarea name="history" class="form-control border-light shadow-sm" rows="6" placeholder="Descreva aqui o atendimento realizado, peças trocadas ou comandos enviados..." style="border-radius: 8px; resize: none; background: #fafafa;"></textarea>
                        </div>
                        <button type="button" class="btn btn-indigo btn-block py-2 text-bold shadow-sm" id="btnFinalizar">
                            <i class="fas fa-check-double mr-2"></i> FINALIZAR ATENDIMENTO E GERAR DOSSIÊ
                        </button>
                    </div>
                </div>
            </form>

            <!-- 📜 HISTÓRICO (LISTA DIRETA) -->
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header border-0 bg-transparent py-3">
                    <h3 class="card-title text-bold"><i class="fas fa-history mr-2 text-muted"></i>Historico de atendimento</h3>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light">
                                <tr class="text-uppercase small font-weight-bold text-muted">
                                    <th class="px-4">Data / Hora</th>
                                    <th>Tipo</th>
                                    <th>Atendente</th>
                                    <th class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($history as $item)
                                <tr>
                                    <td class="px-4 align-middle font-weight-bold text-dark">
                                        {{ $item->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="align-middle">
                                        <span class="badge badge-pill {{ $item->type == 'installation' ? 'badge-success' : 'badge-primary' }}">
                                            {{ strtoupper($item->type) }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-muted small">
                                        {{ $item->user->name }}
                                    </td>
                                    <td class="text-center align-middle">
                                        <button onclick="viewAttendance('{{ route('support.log.view', $item->id) }}')" class="btn btn-xs btn-outline-indigo shadow-sm">
                                            <i class="fas fa-eye mr-1"></i> VER ATENDIMENTO
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="p-5 text-center text-muted">
                                        <i class="fas fa-folder-open fa-2x mb-2 opacity-50"></i>
                                        <p class="mb-0">Nenhum atendimento anterior registrado.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .btn-indigo { background-color: #6610f2; color: #fff; border-color: #6610f2; }
    .btn-indigo:hover { background-color: #520dc2; color: #fff; }
    .text-indigo { color: #6610f2; }
    .badge-indigo { background-color: rgba(102, 16, 242, 0.1); color: #6610f2; border: 1px solid rgba(102, 16, 242, 0.2); }
    .border-indigo { border-color: #6610f2 !important; }
    
    /* 🇧🇷 Placa Mercosul */
    .mercosul-plate { width: 140px; height: 50px; border: 2px solid #333; border-radius: 8px; background: white; overflow: hidden; display: flex; flex-direction: column; line-height: 1; }
    .plate-header { background: #003399; color: white; font-size: 8px; text-align: center; padding: 3px 0; font-weight: bold; letter-spacing: 1px; }
    .plate-number { flex-grow: 1; display: flex; align-items: center; justify-content: center; font-family: 'Arial Black', sans-serif; font-size: 1.4rem; color: #000; letter-spacing: 2px; }

    .transition-icon { transition: transform 0.3s ease; }
    .card-header[aria-expanded="true"] .transition-icon { transform: rotate(180deg); }

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

@push('scripts')
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

    $(function() {
        $('#btnFinalizar').on('click', function() {
            const history = $('textarea[name="history"]').val();
            
            if (!history.trim()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Relato Vazio',
                    text: 'Por favor, descreva o atendimento no campo histórico antes de finalizar.',
                    confirmButtonColor: '#6610f2'
                });
                return;
            }

            Swal.fire({
                title: 'Finalizar Atendimento?',
                text: "Isso irá gerar um dossiê imutável (.txt) no servidor e blindar este registro técnico.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'SIM, FINALIZAR E SALVAR',
                cancelButtonText: 'VOLTAR'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Gerando Dossiê...',
                        html: 'Processando persistência híbrida SQL + TXT',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    $('#formAttendance').submit();
                }
            });
        });
    });
</script>
@endpush
@endsection
