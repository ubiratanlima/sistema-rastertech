@extends('layouts.app')

@section('title', 'Fase 2: Instalação | Rastertech')

@section('content')
<div class="container-fluid pb-5">
    <!-- 🏗️ CABEÇALHO DA FASE 2 -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 p-0">
            <a href="{{ route('portal.instalador.index') }}" class="btn btn-sm btn-light border mb-2 shadow-none" style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-1"></i> Voltar
            </a>
            <h1 class="m-0 text-bold" style="font-size: 2rem;">
                <i class="fas fa-bolt mr-2 text-primary"></i>Fase 2: INSTALAÇÃO
            </h1>
            <p class="text-muted mb-0 font-weight-bold uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Mapeamento técnico de chicotes e conexões</p>
        </div>
    </div>

    <!-- 🔧 FORMULÁRIO DE PROCESSO -->
    <form action="{{ route('portal.instalador.process.store', $installation->id) }}" method="POST" enctype="multipart/form-data" id="processForm">
        @csrf
        <div class="row">
            <!-- 👤 DADOS DO VEÍCULO (READ ONLY) -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border-left: 5px solid #007bff !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary-light p-3 rounded mr-3 text-primary" style="background: rgba(0,123,255,0.1);">
                                <i class="fas fa-car fa-2x"></i>
                            </div>
                            <div>
                                <h3 class="m-0 text-bold">{{ $installation->vehicle_plate }}</h3>
                                <small class="text-muted text-bold uppercase">{{ $installation->customer_name }}</small>
                            </div>
                        </div>
                        <p class="text-muted small mb-0 font-italic">"{{ $installation->vehicle_details }}"</p>
                    </div>
                </div>
                
                <!-- 🚥 SELETOR DE BLOQUEIO DINÂMICO -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
                    <div class="card-body p-4 text-center">
                        <label class="text-uppercase text-muted font-weight-bold small d-block mb-3">Bloqueio Veicular?</label>
                        <div class="d-flex justify-content-center align-items-center">
                            <span class="mr-4 font-weight-bold status-badge" id="status-nao">NÃO</span>
                            <div class="custom-control custom-switch custom-switch-lg p-0" style="width: 4rem;">
                                <input type="checkbox" class="custom-control-input" id="has_block" name="has_block" value="1" onchange="toggleBlockSection(this)">
                                <label class="custom-control-label" for="has_block" style="cursor: pointer;"></label>
                            </div>
                            <span class="ml-4 font-weight-bold status-badge" id="status-sim">SIM</span>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info border-0 shadow-sm" style="border-radius: 12px;">
                    <i class="fas fa-info-circle mr-1"></i> <strong>Padrão Ouro:</strong> Siga a risca o mapeamento de cores dos fios para auditoria.
                </div>
            </div>

            <!-- 📸 VISTORIA DE INSTALAÇÃO (SLOTS DINÂMICOS) -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h4 class="text-bold m-0 text-muted small uppercase"><i class="fas fa-microchip mr-2"></i>Mapeamento de Instalação</h4>
                        <span class="badge badge-primary px-3 py-2" style="border-radius: 20px;" id="photo-count">4/4 OBRIGATÓRIAS</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row row-cols-2 row-cols-md-3 g-4 text-center">
                            <!-- ✅ SLOTS SEMPRE OBRIGATÓRIOS -->
                            <div class="col mb-2">
                                @include('portal.instalador.components.photo_slot', ['id' => 'chicote', 'label' => 'Chicote Veículo', 'icon' => 'fas fa-project-diagram', 'color' => '#666', 'required' => true])
                            </div>
                            <div class="col mb-2">
                                @include('portal.instalador.components.photo_slot', ['id' => 'acc', 'label' => 'ACC (Laranja/Branco)', 'icon' => 'fas fa-key', 'color' => '#fd7e14', 'required' => true])
                            </div>
                            <div class="col mb-2">
                                @include('portal.instalador.components.photo_slot', ['id' => 'positivo', 'label' => 'Positivo (Vermelho)', 'icon' => 'fas fa-plus-circle', 'color' => '#dc3545', 'required' => true])
                            </div>
                            <div class="col mb-2">
                                @include('portal.instalador.components.photo_slot', ['id' => 'neutro', 'label' => 'Aterramento (Preto)', 'icon' => 'fas fa-minus-circle', 'color' => '#333', 'required' => true])
                            </div>

                            <!-- 🚥 SLOTS CONDICIONAIS (BLOQUEIO) -->
                            <div class="col mb-2 block-section" style="display: none;">
                                @include('portal.instalador.components.photo_slot', ['id' => 'bloqueio', 'label' => 'Fio Bloqueio (Amarelo)', 'icon' => 'fas fa-cut', 'color' => '#ffc107', 'required' => false])
                            </div>
                            <div class="col mb-2 block-section" style="display: none;">
                                @include('portal.instalador.components.photo_slot', ['id' => 'rele', 'label' => 'Relé no Veículo', 'icon' => 'fas fa-toggle-on', 'color' => '#28a745', 'required' => false])
                            </div>
                        </div>

                        <!-- 🏁 BOTÃO DE SALVAMENTO -->
                        <button type="submit" class="btn btn-primary btn-lg btn-block shadow-sm py-4 text-bold text-uppercase mt-4" 
                                id="btnSave" style="border-radius: 15px; font-size: 1.3rem; border: 0; background: linear-gradient(135deg, #007bff 0%, #004085 100%);">
                            <i class="fas fa-check-circle mr-2"></i> CONCLUIR INSTALAÇÃO
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .bg-light { background: #fbfbfb !important; }
    .uppercase { text-transform: uppercase; }
    
    /* 🚥 CUSTOM SWITCH LG RECALIBRADO */
    .custom-switch-lg .custom-control-label::before { 
        height: 2.2rem; 
        width: 4rem; 
        border-radius: 2rem; 
        top: -0.25rem;
    }
    .custom-switch-lg .custom-control-label::after { 
        width: calc(2.2rem - 4px); 
        height: calc(2.2rem - 4px); 
        border-radius: 2rem; 
        top: calc(-0.25rem + 2px);
    }
    .custom-switch-lg .custom-control-input:checked ~ .custom-control-label::after { 
        transform: translateX(1.8rem); 
    }
    
    .status-badge {
        font-size: 1.1rem;
        transition: all 0.3s ease;
        padding: 5px 15px;
        border-radius: 10px;
    }
    .status-active-sim { color: #fff !important; background: #28a745; box-shadow: 0 4px 10px rgba(40,167,69,0.3); }
    .status-active-nao { color: #fff !important; background: #dc3545; box-shadow: 0 4px 10px rgba(220,53,69,0.3); }
</style>

@push('scripts')
<script>
    function toggleBlockSection(checkbox) {
        if (checkbox.checked) {
            $('.block-section').fadeIn().find('input').prop('required', true);
            $('#photo-count').text('6/6 OBRIGATÓRIAS').removeClass('badge-primary').addClass('badge-warning').css('color', '#000');
            $('#status-sim').addClass('status-active-sim');
            $('#status-nao').removeClass('status-active-nao');
        } else {
            $('.block-section').fadeOut().find('input').prop('required', false);
            $('#photo-count').text('4/4 OBRIGATÓRIAS').removeClass('badge-warning').addClass('badge-primary').css('color', '#fff');
            $('#status-nao').addClass('status-active-nao');
            $('#status-sim').removeClass('status-active-sim');
        }
    }

    // Inicialização da UI
    $(document).ready(function() {
        $('#status-nao').addClass('status-active-nao');
    });

    function previewPhoto(input, key) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $(`#thumb_${key}`).removeClass('d-none');
                $(`#img_${key}`).attr('src', e.target.result);
                $(`#icon_${key}`).addClass('d-none');
            }
            reader.readAsDataURL(file);
        }
    }

    $('#processForm').submit(function(e) {
        let errors = [];
        const isBlockEnabled = $('#has_block').is(':checked');

        // 📸 VALIDAÇÃO DE FOTOS OBRIGATÓRIAS (INSTALAÇÃO)
        let mandatoryPhotos = {
            'chicote': 'Foto do Chicote',
            'acc': 'Fio do ACC',
            'positivo': 'Fio do Positivo',
            'neutro': 'Fio do Neutro'
        };

        if (isBlockEnabled) {
            mandatoryPhotos['bloqueio'] = 'Fio do Bloqueio (Amarelo)';
            mandatoryPhotos['rele'] = 'Foto do Relé';
        }

        for (let key in mandatoryPhotos) {
            if (!$(`#${key}`)[0].files.length) {
                errors.push(mandatoryPhotos[key]);
            }
        }

        // 🛡️ DISPARO DE ALERTA TÁTICO
        if (errors.length > 0) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: '<span class="text-bold">PENDÊNCIAS DE INSTALAÇÃO</span>',
                html: `<div class="text-left font-weight-bold text-muted small uppercase mb-2">As evidências abaixo são obrigatórias:</div>
                       <ul class="text-left text-primary font-weight-bold" style="list-style: none; padding-left: 0;">
                        ${errors.map(err => `<li><i class="fas fa-bolt mr-2"></i>${err}</li>`).join('')}
                       </ul>`,
                confirmButtonText: 'ENTENDIDO, VOU CAPTURAR',
                confirmButtonColor: '#007bff',
                background: '#fff',
                customClass: { popup: 'rounded-lg border-0 shadow-lg' }
            });
            return false;
        }

        $('#btnSave').html('<i class="fas fa-sync fa-spin mr-2"></i> REGISTRANDO CONEXÕES...').prop('disabled', true);
    });
</script>
@endpush
@endsection
