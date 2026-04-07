@extends('layouts.app')

@section('title', 'Fase 3: Checkout Final | Rastertech')

@section('content')
<div class="container-fluid pb-5">
    <!-- 🏗️ CABEÇALHO DA FASE 3 -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 p-0">
            <a href="{{ route('portal.instalador.index') }}" class="btn btn-sm btn-light border mb-2 shadow-none" style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-1"></i> Voltar
            </a>
            <h1 class="m-0 text-bold" style="font-size: 2rem;">
                <i class="fas fa-flag-checkered mr-2 text-success"></i>Fase 3: CHECKOUT FINAL
            </h1>
            <p class="text-muted mb-0 font-weight-bold uppercase" style="font-size: 0.8rem; letter-spacing: 1px;">Encerramento de vistoria e coleta de aceite</p>
        </div>
    </div>

    <!-- 🔧 FORMULÁRIO DE CHECKOUT -->
    <form action="{{ route('portal.instalador.checkout.store', $installation->id) }}" method="POST" enctype="multipart/form-data" id="checkoutForm">
        @csrf
        <div class="row">
            <!-- 👤 RESUMO DO VEÍCULO -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border-left: 5px solid #28a745 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success-light p-3 rounded mr-3 text-success" style="background: rgba(40,167,69,0.1);">
                                <i class="fas fa-check-double fa-2x"></i>
                            </div>
                            <div>
                                <h3 class="m-0 text-bold">{{ $installation->vehicle_plate }}</h3>
                                <small class="text-muted text-bold uppercase">{{ $installation->customer_name }}</small>
                            </div>
                        </div>
                        <ul class="list-group list-group-flush small">
                            <li class="list-group-item bg-transparent px-0"><i class="fas fa-calendar-alt mr-1"></i> Entrada: {{ $installation->checkin_at->format('d/m/Y H:i') }}</li>
                            <li class="list-group-item bg-transparent px-0"><i class="fas fa-bolt mr-1 text-primary"></i> Elétrica: {{ $installation->processed_at->format('d/m/Y H:i') }}</li>
                        </ul>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="text-uppercase text-muted font-weight-bold small"><i class="fas fa-edit mr-1"></i> Relato Final do Técnico (Até 500 carac.)</label>
                    <textarea name="notes" class="form-control border-0 bg-white shadow-sm" rows="6" maxlength="500" placeholder="Descreva como foi a instalação e se houve alguma observação extra..." style="border-radius: 12px;" required></textarea>
                </div>
            </div>

            <!-- 📸 FOTOS DE FINALIZAÇÃO (3 FOTOS) -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h4 class="text-bold m-0 text-muted small uppercase"><i class="fas fa-user-shield mr-2"></i>Aceite e Finalização</h4>
                        <span class="badge badge-success px-3 py-2" style="border-radius: 20px;">ENTREGA FORMAL</span>
                    </div>
                    <div class="card-body p-4">
                        <div class="row row-cols-1 row-cols-md-3 g-4 text-center">
                            @php
                                $slots = [
                                    'interna_pos' => ['Interna Finalizada', 'fas fa-car-side'],
                                    'acompanhante' => ['Rosto do Cliente', 'fas fa-user-tie'],
                                    'documento_cliente' => ['Documento (ID)', 'fas fa-id-card']
                                ];
                            @endphp

                            @foreach($slots as $key => $data)
                            <div class="col mb-3">
                                <div class="photo-slot bg-light rounded d-flex flex-column align-items-center justify-content-center" 
                                     style="border: 2px dashed #ddd; height: 180px; cursor: pointer; position: relative;"
                                     onclick="document.getElementById('{{ $key }}').click()">
                                    
                                    <i class="{{ $data[1] }} fa-3x text-muted mb-2"></i>
                                    <span class="text-bold text-muted small uppercase" style="line-height: 1.1;">{{ $data[0] }}</span>
                                    
                                    <input type="file" name="{{ $key }}" id="{{ $key }}" class="d-none" accept="image/*" required onchange="previewPhoto(this, '{{ $key }}')">
                                    <div id="thumb_{{ $key }}" class="thumb-preview d-none"><img src="" id="img_{{ $key }}"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- 🏁 BOTÃO DE FINALIZAÇÃO -->
                        <button type="submit" class="btn btn-success btn-lg btn-block shadow-sm py-4 text-bold text-uppercase mt-4" 
                                id="btnSave" style="border-radius: 15px; font-size: 1.3rem; border: 0; background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);">
                            <i class="fas fa-check-circle mr-2"></i> FINALIZAR INSTALAÇÃO
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .bg-light { background: #fbfbfb !important; }
    .photo-slot:hover { border-color: #28a745 !important; background: #fff !important; }
    .thumb-preview { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; background: #fff; padding: 4px; border-radius: 10px; }
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
            }
            reader.readAsDataURL(file);
        }
    }

    $('#checkoutForm').submit(function(e) {
        let errors = [];

        // 🔍 VALIDAÇÃO DE RELATO TÉCNICO
        if (!$('textarea[name="notes"]').val() || $('textarea[name="notes"]').val().length < 5) {
            errors.push("Relato Técnico (Mín. 5 caracteres)");
        }

        // 📸 VALIDAÇÃO DE FOTOS OBRIGATÓRIAS (ACEITE)
        const mandatoryPhotos = {
            'interna_pos': 'Interna Finalizada',
            'acompanhante': 'Rosto do Cliente',
            'documento_cliente': 'Documento do Cliente'
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
                title: '<span class="text-bold">PENDÊNCIAS DE FINALIZAÇÃO</span>',
                html: `<div class="text-left font-weight-bold text-muted small uppercase mb-2">Preencha os itens abaixo para encerrar:</div>
                       <ul class="text-left text-success font-weight-bold" style="list-style: none; padding-left: 0;">
                        ${errors.map(err => `<li><i class="fas fa-check-circle mr-2"></i>${err}</li>`).join('')}
                       </ul>`,
                confirmButtonText: 'ENTENDIDO, VOU FINALIZAR',
                confirmButtonColor: '#28a745',
                background: '#fff',
                customClass: { popup: 'rounded-lg border-0 shadow-lg' }
            });
            return false;
        }

        $('#btnSave').html('<i class="fas fa-sync fa-spin mr-2"></i> FINALIZANDO OPERAÇÃO...').prop('disabled', true);
    });
</script>
@endpush
@endsection
