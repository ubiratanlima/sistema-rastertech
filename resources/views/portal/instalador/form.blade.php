@extends('layouts.app')

@section('title', 'Nova Instalação | Rastertech')

@section('content')
<div class="container-fluid pb-5">
    <!-- 🏗️ CABEÇALHO DO FORMULÁRIO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 p-0">
            <a href="{{ route('portal.instalador.index') }}" class="btn btn-sm btn-light border mb-2 shadow-none" style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-1"></i> Voltar à Central
            </a>
            <h1 class="m-0 text-bold" style="font-size: 2rem;">
                <i class="fas fa-tools mr-2 text-primary"></i>Nova VISTORIA TÉCNICA
            </h1>
            <p class="text-muted mb-0">Registre os dados da instalação e vistorie o ativo com precisão.</p>
        </div>
    </div>

    <!-- 🔧 FORMULÁRIO DE INSTALAÇÃO -->
    <form action="{{ route('portal.instalador.store') }}" method="POST" enctype="multipart/form-data" id="installForm">
        @csrf
        <div class="row">
            <!-- 👤 DADOS DO CLIENTE E ATIVO -->
            <div class="col-md-5">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h4 class="text-bold m-0 text-muted"><i class="fas fa-user-check mr-2"></i>Responsável & Veículo</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-group mb-4">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Nome do Cliente / Responsável</label>
                            <input type="text" name="customer_name" class="form-control form-control-lg border-0 bg-light" placeholder="Quem acompanhou a instalação..." style="border-radius: 10px;" required>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Foto do Documento (ID/CNH)</label>
                            <div class="custom-file-upload bg-light p-3 rounded text-center" style="border: 2px dashed #ddd; cursor: pointer;" onclick="document.getElementById('customer_id_photo').click()">
                                <i class="fas fa-id-card fa-2x text-muted mb-2"></i>
                                <p class="small text-muted mb-0">Clique para fotografar o documento</p>
                                <input type="file" name="customer_id_photo" id="customer_id_photo" class="d-none" accept="image/*" required onchange="previewFile(this, 'id-preview')">
                            </div>
                            <img id="id-preview" class="img-fluid rounded shadow-sm mt-2 d-none" style="max-height: 150px;">
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Placa do Veículo</label>
                            <input type="text" name="vehicle_plate" class="form-control form-control-lg border-0 bg-light text-center text-bold" placeholder="ABC1D23" style="border-radius: 10px; font-size: 1.5rem; letter-spacing: 2px;" required>
                        </div>

                        <div class="form-group mb-0">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Detalhamento Técnico do Ativo</label>
                            <textarea name="vehicle_details" class="form-control form-control-lg border-0 bg-light" rows="4" placeholder="Descreva as condições do veículo e onde o equipamento foi instalado..." style="border-radius: 10px;" required></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 📸 VISTORIA VISUAL (10 SLOTS) -->
            <div class="col-md-7">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h4 class="text-bold m-0 text-muted"><i class="fas fa-camera mr-2"></i>Auditoria Visceral</h4>
                        <span class="badge badge-primary px-3 py-2" style="border-radius: 20px;">10 SLOTS DISPONÍVEIS</span>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="row row-cols-2 row-cols-md-3 g-3">
                            @for ($i = 1; $i <= 10; $i++)
                            <div class="col mb-3">
                                <div class="photo-slot bg-light rounded p-3 d-flex flex-column align-items-center justify-content-center" 
                                     style="border: 2px dashed #ccc; height: 160px; cursor: pointer; position: relative;"
                                     onclick="document.getElementById('photo_{{ $i }}').click()">
                                    
                                    <div id="loading_{{ $i }}" class="d-none"><i class="fas fa-sync fa-spin"></i></div>
                                    <i class="fas fa-camera fa-2x text-muted mb-2" id="icon_{{ $i }}"></i>
                                    <span class="small text-muted text-bold">FOTO {{ $i }}</span>
                                    @if($i <= 5) <small class="text-danger">OBRIGATÓRIO</small> @else <small class="text-info">EXTRA</small> @endif
                                    
                                    <input type="file" name="photo_{{ $i }}" id="photo_{{ $i }}" class="d-none" accept="image/*" 
                                           @if($i <= 5) required @endif
                                           onchange="previewPhoto(this, {{ $i }})">
                                    
                                    <div id="thumb_{{ $i }}" class="thumb-preview d-none">
                                        <img src="" id="img_{{ $i }}" class="img-fluid rounded shadow-sm">
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>

                        <!-- 🏁 BOTÃO DE SALVAMENTO -->
                        <button type="submit" class="btn btn-primary btn-lg btn-block shadow-sm py-4 text-bold text-uppercase mt-4" 
                                id="btnSave" style="border-radius: 15px; font-size: 1.3rem; background: linear-gradient(135deg, #007bff 0%, #004085 100%); border: 0;">
                            <i class="fas fa-cloud-upload-alt mr-2"></i> Finalizar Dossiê de Instalação
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .bg-light { background: #f8f9fa !important; }
    .photo-slot:hover { border-color: #007bff !important; background: #fff !important; }
    .thumb-preview { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 10; background: #fff; padding: 5px; border-radius: 10px; }
    .thumb-preview img { height: 100%; width: 100%; object-fit: cover; }
    
    @media (max-width: 768px) {
        .photo-slot { height: 140px; }
    }
</style>

@push('scripts')
<script>
    function previewFile(input, previewId) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $(`#${previewId}`).attr('src', e.target.result).removeClass('d-none').addClass('animate__animated animate__fadeIn');
            }
            reader.readAsDataURL(file);
        }
    }

    function previewPhoto(input, index) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $(`#thumb_${index}`).removeClass('d-none');
                $(`#img_${index}`).attr('src', e.target.result);
                $(`#icon_${index}`).addClass('d-none');
            }
            reader.readAsDataURL(file);
        }
    }

    $('#installForm').submit(function() {
        const btn = $('#btnSave');
        btn.html('<i class="fas fa-sync fa-spin mr-2"></i> SINCRONIZANDO DOSSIÊ...').prop('disabled', true);
    });
</script>
@endpush
@endsection
