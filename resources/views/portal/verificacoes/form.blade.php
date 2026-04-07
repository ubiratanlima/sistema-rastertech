@extends('layouts.app')

@section('title', 'Nova Verificação | Rastertech')

@section('content')
<div class="container-fluid pb-5">
    <!-- 🏗️ CABEÇALHO DO FORMULÁRIO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 p-0">
            <a href="{{ route('portal.verificacoes.index') }}" class="btn btn-sm btn-light border mb-2 shadow-none" style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-1"></i> Voltar ao Dashboard
            </a>
            <h1 class="m-0 text-bold" style="font-size: 2rem;">
                @if($type == 'entry')
                    <i class="fas fa-sign-in-alt mr-2 text-success"></i>Realizar CHECK-IN
                @else
                    <i class="fas fa-sign-out-alt mr-2 text-primary"></i>Realizar CHECK-OUT
                @endif
            </h1>
            <p class="text-muted mb-0">Complete todos os campos obrigatórios para registrar a jornada.</p>
        </div>
    </div>

    <!-- 📝 FORMULÁRIO TÁTICO -->
    <form action="{{ route('portal.verificacoes.store') }}" method="POST" enctype="multipart/form-data" id="checklistForm">
        @csrf
        <input type="hidden" name="driver_id" value="{{ $driver->id }}">
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="row">
            <!-- 🚛 DADOS DO VEÍCULO E ODÔMETRO -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h4 class="text-bold m-0 text-muted"><i class="fas fa-truck mr-2"></i>Ativo & Telemetria</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-group mb-4">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Selecione o Veículo</label>
                            <select name="vehicle_id" class="form-control form-control-lg border-0 bg-light select2" style="border-radius: 10px;" required {{ ($type == 'exit' && $currentVehicleId) ? 'readonly' : '' }}>
                                <option value="">--- ESCOLHA O VEÍCULO ---</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ ($currentVehicleId == $v->id) ? 'selected' : '' }}>{{ $v->plate }} ({{ $v->brand }} / {{ $v->model }})</option>
                                @endforeach
                            </select>
                            @if($type == 'exit' && $currentVehicleId)
                                <input type="hidden" name="vehicle_id" value="{{ $currentVehicleId }}">
                                <small class="text-primary font-weight-bold"><i class="fas fa-lock mr-1"></i> Veículo vinculado ao Check-in ativo.</small>
                            @endif
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Odômetro Atual (KM)</label>
                            <input type="number" name="odometer" class="form-control form-control-lg border-0 bg-light font-weight-bold" 
                                   placeholder="0" step="1" min="0" style="border-radius: 10px;" required>
                        </div>

                        <div class="form-group mb-0">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Nível de Combustível (Opcional)</label>
                            <select name="fuel_level" class="form-control form-control-lg border-0 bg-light" style="border-radius: 10px;">
                                <option value="Vazio">Vazio</option>
                                <option value="1/4">1/4</option>
                                <option value="1/2">Meio (1/2)</option>
                                <option value="3/4">3/4</option>
                                <option value="Cheio" selected>Cheio</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- ✍️ RELATO DO MOTORISTA -->
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h4 class="text-bold m-0 text-muted"><i class="fas fa-comment-alt mr-2"></i>Relato do Motorista</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-group mb-0">
                            <textarea name="notes" class="form-control border-0 bg-light" rows="5" 
                                      placeholder="Descreva as condições observadas (mínimo 15 caracteres)..." 
                                      style="border-radius: 10px; resize: none;" required></textarea>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted"><i class="fas fa-exclamation-circle mr-1"></i> Mínimo de 15 caracteres.</small>
                                <small id="charCount" class="text-muted">0/500</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 📸 GRADE DE FOTOS (10 SLOTS) -->
            <div class="col-md-8">
                <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 d-flex justify-content-between align-items-center">
                        <h4 class="text-bold m-0 text-muted"><i class="fas fa-camera mr-2 text-teal"></i>Evidências Visuais</h4>
                        <span class="badge badge-info shadow-none py-2 px-3">5 Obrigatórias | 5 Extras</span>
                    </div>
                    <div class="card-body p-4 pt-1">
                        <div class="row">
                            <!-- LOOP DE FOTOS -->
                            @php
                                $slots = [
                                    1 => ['label' => 'Odômetro', 'required' => true, 'icon' => 'fas fa-tachometer-alt'],
                                    2 => ['label' => 'Frente', 'required' => true, 'icon' => 'fas fa-car'],
                                    3 => ['label' => 'Traseira', 'required' => true, 'icon' => 'fas fa-car'],
                                    4 => ['label' => 'Lateral Direita', 'required' => true, 'icon' => 'fas fa-arrow-right'],
                                    5 => ['label' => 'Lateral Esquerda', 'required' => true, 'icon' => 'fas fa-arrow-left'],
                                    6 => ['label' => 'Carroceria Dir.', 'required' => false, 'icon' => 'fas fa-box'],
                                    7 => ['label' => 'Carroceria Esq.', 'required' => false, 'icon' => 'fas fa-box'],
                                    8 => ['label' => 'Extra 1', 'required' => false, 'icon' => 'fas fa-plus'],
                                    9 => ['label' => 'Extra 2', 'required' => false, 'icon' => 'fas fa-plus'],
                                    10 => ['label' => 'Extra 3', 'required' => false, 'icon' => 'fas fa-plus'],
                                ];
                            @endphp

                            @foreach($slots as $key => $slot)
                            <div class="col-6 col-sm-4 col-lg-3 mb-4">
                                <div class="photo-slot border text-center p-2 h-100 d-flex flex-column" 
                                     style="border-radius: 12px; transition: all 0.3s; background: #fafafa; border-style: dashed !important;">
                                    <label class="mb-1 text-uppercase font-weight-bold text-muted small" style="font-size: 0.65rem;">
                                        {{ $slot['label'] }} @if($slot['required']) <span class="text-danger">*</span> @endif
                                    </label>
                                    
                                    <div class="flex-fill d-flex align-items-center justify-content-center py-2 preview-container" id="preview-{{ $key }}">
                                        <i class="{{ $slot['icon'] }} fa-2x text-muted opacity-25"></i>
                                    </div>

                                    <div class="mt-auto">
                                        <label for="input-{{ $key }}" class="btn btn-sm btn-light border btn-block m-0" style="border-radius: 8px;">
                                            <i class="fas fa-upload mr-1"></i> Foto
                                        </label>
                                        <input type="file" name="photo_{{ $key }}" id="input-{{ $key }}" class="d-none photo-input" 
                                               accept="image/*" @if($slot['required']) required @endif data-preview="preview-{{ $key }}">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🏁 BOTÃO DE SALVAMENTO -->
        <div class="row mt-4">
            <div class="col-12">
                <button type="submit" class="btn btn-teal btn-lg btn-block shadow-sm py-3 text-bold text-uppercase" 
                        style="border-radius: 12px; font-size: 1.2rem; background-color: #20c997 !important; border: 0;">
                    <i class="fas fa-cloud-upload-alt mr-2"></i> Registrar Verificação de {{ $type == 'entry' ? 'Entrada' : 'Saída' }}
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .photo-slot:hover { border-color: #20c997 !important; background: white !important; }
    .photo-slot.has-image { border-style: solid !important; border-color: #20c997 !important; background: #f0fffb !important; }
    .opacity-25 { opacity: 0.25; }
    .btn-teal { background-color: #20c997; color: white; }
    
    .preview-image { width: 100%; height: 80px; object-fit: cover; border-radius: 8px; }
</style>

@push('scripts')
<script>
    /**
     * MOTOR DE PREVIEW (UX REALTIME)
     */
    $('.photo-input').change(function() {
        const input = this;
        const previewId = $(input).data('preview');
        const container = $('#' + previewId);
        const parent = $(input).closest('.photo-slot');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                container.html(`<img src="${e.target.result}" class="preview-image animate__animated animate__zoomIn">`);
                parent.addClass('has-image');
            }
            reader.readAsDataURL(input.files[0]);
        }
    });

    /**
     * VALIDADOR DE RELATO (MÍNIMO 15 CARACTS)
     */
    $('textarea[name="notes"]').on('input', function() {
        const len = $(this).val().length;
        $('#charCount').text(len + '/500');
        if (len >= 15) {
            $('#charCount').removeClass('text-muted').addClass('text-success');
        } else {
            $('#charCount').removeClass('text-success').addClass('text-muted');
        }
    });

    /**
     * LOADING AO SALVAR (EVITA CLIQUE DUPLO)
     */
    $('#checklistForm').submit(function() {
        const btn = $(this).find('button[type="submit"]');
        btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> PROCESSANDO REGISTRO...').prop('disabled', true);
    });
</script>
@endpush
@endsection
