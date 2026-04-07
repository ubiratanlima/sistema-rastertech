@extends('layouts.app')

@section('title', 'Check-in: Entrada de Veículo | Rastertech')

@section('content')
<div class="container-fluid pb-5">
    <!-- 🏗️ CABEÇALHO DA FASE 1 -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 p-0">
            <a href="{{ route('portal.instalador.index') }}" class="btn btn-sm btn-light border mb-2 shadow-none" style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-1"></i> Voltar
            </a>
            <h1 class="m-0 text-bold" style="font-size: 2rem;">
                <i class="fas fa-truck-loading mr-2 text-warning"></i>Fase 1: CHECK-IN
            </h1>
            <p class="text-muted mb-0 font-weight-bold uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Vistoria de entrada e auditoria de avarias</p>
        </div>
    </div>

    <!-- 🔧 FORMULÁRIO DE CHECK-IN -->
    <form action="{{ route('portal.instalador.checkin.store') }}" method="POST" enctype="multipart/form-data" id="checkinForm">
        @csrf
        <div class="row">
            <!-- 👤 DADOS BÁSICOS -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h4 class="text-bold m-0 text-muted small uppercase"><i class="fas fa-id-card-alt mr-2"></i>Identificação do Ativo</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-group mb-4">
                            <label class="text-uppercase text-muted font-weight-bold small">Nome do Cliente</label>
                            <input type="text" name="customer_name" class="form-control form-control-lg border-0 bg-light" placeholder="Ex: Transportadora RTA" style="border-radius: 10px;" required>
                        </div>
                        <div class="form-group mb-4">
                            <label class="text-uppercase text-muted font-weight-bold small">Placa do Veículo</label>
                            <input type="text" name="vehicle_plate" class="form-control form-control-lg border-0 bg-light text-center text-bold" placeholder="ABC1D23" style="border-radius: 10px; font-size: 1.5rem; letter-spacing: 2px;" required>
                        </div>
                        <div class="form-group mb-0">
                            <label class="text-uppercase text-muted font-weight-bold small">Observações de Entrada</label>
                            <textarea name="vehicle_details" class="form-control form-control-lg border-0 bg-light" rows="4" placeholder="Algum detalhe relevante sobre o estado geral..." style="border-radius: 10px;"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 📸 VISTORIA OBRIGATÓRIA (7 FOTOS) -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h4 class="text-bold m-0 text-muted small uppercase"><i class="fas fa-camera-retro mr-2"></i>Vistoria Obrigatória</h4>
                        <span class="badge badge-warning px-3 py-2 text-dark" style="border-radius: 20px;">7/7 OBRIGATÓRIAS</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row row-cols-2 row-cols-md-4 g-3 text-center">
                            @php
                                $mandatory = [
                                    'frente' => 'Frente Veículo',
                                    'placa_frente' => 'Placa Frente',
                                    'lat_dir' => 'Lateral Direita',
                                    'lat_esq' => 'Lateral Esquerda',
                                    'traseira' => 'Traseira',
                                    'odometro' => 'Odômetro',
                                    'interna_pre' => 'Interna (Pré-Inst)'
                                ];
                            @endphp

                            @foreach($mandatory as $key => $label)
                            <div class="col mb-3">
                                <div class="photo-slot bg-light rounded d-flex flex-column align-items-center justify-content-center" 
                                     style="border: 2px dashed #ddd; height: 140px; cursor: pointer; position: relative;"
                                     onclick="document.getElementById('{{ $key }}').click()">
                                    <i class="fas fa-camera fa-2x text-muted mb-2" id="icon_{{ $key }}"></i>
                                    <span class="text-bold text-muted small uppercase" style="line-height: 1.1;">{{ $label }}</span>
                                    <input type="file" name="{{ $key }}" id="{{ $key }}" class="d-none" accept="image/*" required onchange="previewPhoto(this, '{{ $key }}')">
                                    <div id="thumb_{{ $key }}" class="thumb-preview d-none"><img src="" id="img_{{ $key }}"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    
                    <div class="card-header bg-transparent border-0 pt-0 px-4">
                        <h4 class="text-bold m-0 text-muted small uppercase"><i class="fas fa-shield-alt mr-2"></i>Fotos de Proteção (Opcional)</h4>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <div class="row row-cols-2 row-cols-md-4 g-3 text-center">
                            @for ($i = 1; $i <= 4; $i++)
                            <div class="col mb-3">
                                <div class="photo-slot bg-light rounded d-flex flex-column align-items-center justify-content-center" 
                                     style="border: 2px dashed #eee; height: 120px; cursor: pointer; position: relative;"
                                     onclick="document.getElementById('extra_{{ $i }}').click()">
                                    <i class="fas fa-camera fa-1x text-muted mb-2" id="icon_extra_{{ $i }}"></i>
                                    <span class="text-muted small uppercase">EXTRA {{ $i }}</span>
                                    <input type="file" name="extra_{{ $i }}" id="extra_{{ $i }}" class="d-none" accept="image/*" onchange="previewPhoto(this, 'extra_{{ $i }}')">
                                    <div id="thumb_extra_{{ $i }}" class="thumb-preview d-none"><img src="" id="img_extra_{{ $i }}"></div>
                                </div>
                            </div>
                            @endfor
                        </div>

                        <!-- 🏁 BOTÃO DE SALVAMENTO -->
                        <button type="submit" class="btn btn-warning btn-lg btn-block shadow-sm py-4 text-bold text-uppercase mt-4" 
                                id="btnSave" style="border-radius: 15px; font-size: 1.3rem; border: 0;">
                            <i class="fas fa-check-circle mr-2"></i> CONCLUIR CHECK-IN E INICIAR
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .bg-light { background: #fbfbfb !important; }
    .photo-slot:hover { border-color: #ffc107 !important; background: #fff !important; }
    .thumb-preview { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; background: #fff; padding: 3px; border-radius: 8px; }
    .thumb-preview img { height: 100%; width: 100%; object-fit: cover; border-radius: 6px; }
    .uppercase { text-transform: uppercase; }
</style>

@push('scripts')
<script>
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

    $('#checkinForm').submit(function(e) {
        let errors = [];
        
        // 🔍 VALIDAÇÃO DE CAMPOS TEXTO
        if (!$('input[name="customer_name"]').val()) errors.push("Nome do Cliente");
        if (!$('input[name="vehicle_plate"]').val()) errors.push("Placa do Veículo");

        // 📸 VALIDAÇÃO DE FOTOS OBRIGATÓRIAS
        const mandatoryPhotos = {
            'frente': 'Foto da Frente',
            'placa_frente': 'Foto da Placa',
            'lat_dir': 'Lateral Direita',
            'lat_esq': 'Lateral Esquerda',
            'traseira': 'Foto da Traseira',
            'odometro': 'Foto do Odômetro',
            'interna_pre': 'Interna (Entrada)'
        };

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
                title: '<span class="text-bold">PENDÊNCIAS DE VISTORIA</span>',
                html: `<div class="text-left font-weight-bold text-muted small uppercase mb-2">Os itens abaixo são obrigatórios:</div>
                       <ul class="text-left text-danger font-weight-bold" style="list-style: none; padding-left: 0;">
                        ${errors.map(err => `<li><i class="fas fa-times-circle mr-2"></i>${err}</li>`).join('')}
                       </ul>`,
                confirmButtonText: 'ENTENDIDO, VOU CORRIGIR',
                confirmButtonColor: '#ffc107',
                background: '#fff',
                customClass: { popup: 'rounded-lg border-0 shadow-lg' }
            });
            return false;
        }

        $('#btnSave').html('<i class="fas fa-sync fa-spin mr-2"></i> BLINDANDO ENTRADA...').prop('disabled', true);
    });
</script>
@endpush
@endsection
