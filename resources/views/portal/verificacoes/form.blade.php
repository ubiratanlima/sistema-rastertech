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
        <input type="hidden" name="driver_id" value="{{ $driver ? $driver->id : '0' }}">
        
        @if($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4 animate__animated animate__shakeX" style="border-radius: 12px;">
                <h6 class="font-weight-bold"><i class="fas fa-exclamation-triangle mr-2"></i> Verifique os seguintes pontos:</h6>
                <ul class="mb-0 pl-3">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if($isSupervisor && $type == 'exit')
            <div class="alert alert-warning border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <i class="fas fa-user-shield mr-2"></i> <strong>MODO SUPERVISOR:</strong> Você está encerrando a jornada do motorista <b>{{ $driver->name ?? 'N/A' }}</b> administrativamente. Fotos são opcionais, mas a justificativa é obrigatória.
            </div>
        @elseif($isSupervisor)
            <div class="alert alert-info border-0 shadow-sm mb-4" style="border-radius: 12px;">
                <i class="fas fa-user-shield mr-2"></i> <strong>MODO ADMINISTRADOR:</strong> Você está registrando uma verificação administrativa para a frota.
            </div>
        @endif

        <input type="hidden" name="type" value="{{ $type }}">

        <div class="row">
            <!-- 🚛 DADOS DO VEÍCULO E ODÔMETRO -->
            <div class="col-md-4">
                <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h4 class="text-bold m-0 text-muted"><i class="fas fa-truck mr-2"></i>Dados do Veiculo</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="form-group mb-4">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Selecione o Veículo</label>
                            <select name="vehicle_id" class="form-control form-control-lg border-0 bg-light select2" style="border-radius: 10px;" required {{ ($type == 'exit' && $currentVehicleId && !$isSupervisor) ? 'readonly' : '' }}>
                                <option value="">--- ESCOLHA O VEÍCULO ---</option>
                                @foreach($vehicles as $v)
                                    <option value="{{ $v->id }}" {{ ($currentVehicleId == $v->id) ? 'selected' : '' }} {{ ($v->is_locked && ($currentVehicleId != $v->id) && !$isSupervisor) ? 'disabled' : '' }}>
                                        {{ $v->plate }} ({{ $v->brand }} / {{ $v->model }}) 
                                        @if($v->is_locked)
                                            [OCUPADO: {{ $v->locked_by_name }} às {{ $v->locked_at }}]
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @if($type == 'exit' && $currentVehicleId)
                                <input type="hidden" name="vehicle_id" value="{{ $currentVehicleId }}">
                                <small class="text-primary font-weight-bold"><i class="fas fa-lock mr-1"></i> Veículo vinculado ao Check-in ativo.</small>
                            @endif
                        </div>

                        <div class="form-group mb-4" id="odometer_group">
                            <label class="text-uppercase text-muted font-weight-bold" style="font-size: 0.75rem;">Odômetro Atual (KM)</label>
                            <input type="number" name="odometer" id="odometer_input" class="form-control form-control-lg border-0 bg-light font-weight-bold" 
                                   placeholder="0" step="1" min="0" style="border-radius: 10px;" required value="{{ old('odometer', ($type == 'exit' && $activeJourney) ? $activeJourney->odometer : '') }}">
                            
                            <div class="mt-2">
                                <span class="badge {{ $isSupervisor ? 'badge-warning' : 'badge-light border' }} text-muted px-2 py-1" style="font-size: 0.8rem;">
                                    <i class="fas fa-history mr-1"></i> 
                                    @if($type == 'entry')
                                        Último Checkout: <strong>{{ number_format($last_odometer, 0, ',', '.') }}</strong> KM
                                    @else
                                        Entrada desta jornada: <strong>{{ number_format($last_odometer, 0, ',', '.') }}</strong> KM
                                    @endif
                                </span>
                                <input type="hidden" id="last_odometer_value" value="{{ $last_odometer }}">
                                <div id="odometer_warning" class="text-danger small mt-1 font-weight-bold" style="display: none;"></div>
                            </div>
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
                            <div class="col-md-3 col-6 mb-4">
                                <div class="photo-slot border text-center p-3 h-100 d-flex flex-column justify-content-center" 
                                     style="border-style: dashed !important; border-radius: 12px; transition: all 0.3s; background: #fafafa; border-width: 2px;">
                                    
                                    <div class="preview-container mb-2" id="preview-{{ $key }}">
                                        <i class="{{ $slot['icon'] }} fa-2x opacity-25"></i>
                                    </div>

                                    <div class="slot-info">
                                        <label class="d-block text-uppercase font-weight-bold mb-1" style="font-size: 0.65rem; letter-spacing: 0.5px;">
                                            {{ $slot['label'] }} {!! $slot['required'] ? '<span class="text-danger">*</span>' : '' !!}
                                        </label>
                                        <label for="input-{{ $key }}" class="btn btn-xs btn-outline-dark m-0 px-3 py-1" style="border-radius: 20px; font-size: 0.7rem; cursor: pointer;">
                                            <i class="fas fa-upload mr-1"></i> Foto
                                        </label>
                                        <input type="file" name="photo_{{ $key }}" id="input-{{ $key }}" class="d-none photo-input" 
                                               accept="image/*" @if($slot['required'] && !($isSupervisor && $type == 'exit')) required @endif data-preview="preview-{{ $key }}">
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🚀 BOTÃO DE AÇÃO GLOBAL -->
        <div class="row mt-4">
            <div class="col-12">
                <button type="submit" class="btn btn-teal btn-lg btn-block shadow-sm py-3 text-bold text-uppercase" 
                        style="border-radius: 12px; font-size: 1.2rem; background-color: #20c997 !important; color: white !important; border: 0;">
                    <i class="fas fa-save mr-2"></i> {{ $type == 'entry' ? 'SALVAR CHECK-IN' : 'SALVAR CHECKOUT' }}
                </button>
            </div>
        </div>
    </form>
</div>

<style>
    .photo-slot:hover { border-color: #20c997 !important; background: white !important; cursor: pointer; transform: translateY(-3px); }
    .photo-slot.has-image { border-style: solid !important; border-color: #20c997 !important; background: #f0fffb !important; }
    .preview-image { width: 100%; height: 80px; object-fit: cover; border-radius: 8px; }
    .opacity-25 { opacity: 0.25; }
    .text-teal { color: #20c997 !important; }
</style>

@push('scripts')
<script>
    $(document).ready(function() {
        /**
         * GATILHO GLOBAL DE FOTO (CLIQUE NO BOX)
         */
        $(document).on('click', '.photo-slot', function(e) {
            if (!$(e.target).is('input')) {
                $(this).find('.photo-input').click();
            }
        });

        /**
         * MOTOR DE PREVIEW (UX REALTIME)
         */
        $(document).on('change', '.photo-input', function() {
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
         * Corrigido para só disparar se o formulário for válido
         */
        $('#checklistForm').on('submit', function(e) {
            if (this.checkValidity()) {
                const btn = $(this).find('button[type="submit"]');
                btn.html('<i class="fas fa-spinner fa-spin mr-2"></i> PROCESSANDO REGISTRO...').prop('disabled', true);
            } else {
                e.preventDefault();
            }
        });

        // 🛡️ VALIDAÇÃO DE ODÔMETRO EM TEMPO REAL
        const odometerInput = $('#odometer_input');
        const lastKm = parseInt($('#last_odometer_value').val()) || 0;
        const warning = $('#odometer_warning');
        const type = "{{ $type }}";
        const isSupervisor = {{ $isSupervisor ? 'true' : 'false' }};

        odometerInput.on('input change', function() {
            const currentKm = parseInt($(this).val()) || 0;
            let error = false;
            let msg = "";

            if (type === 'entry') {
                if (currentKm !== lastKm) {
                    error = true;
                    msg = `⚠️ O KM de entrada deve ser exatamente ${lastKm.toLocaleString('pt-BR')}.`;
                }
            } else {
                if (currentKm < lastKm) {
                    error = true;
                    msg = `⚠️ O KM de saída não pode ser menor que ${lastKm.toLocaleString('pt-BR')}.`;
                }
            }

            if (error) {
                $(this).addClass('is-invalid').css('border', '2px solid #dc3545');
                warning.text(msg).show();
                if (isSupervisor) {
                    warning.removeClass('text-danger').addClass('text-warning').html(msg + "<br><small>Como Supervisor, você pode salvar, mas justifique abaixo.</small>");
                    $(this).css('border', '2px solid #ffc107');
                }
            } else {
                $(this).removeClass('is-invalid').css('border', 'none');
                warning.hide();
            }
        });
    });
</script>
@endpush
@endsection
