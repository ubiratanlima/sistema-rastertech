@extends('layouts.app')

@section('title', 'Detalhes da Verificação | Rastertech')

@section('content')
<div class="container-fluid pb-5">
    <!-- 📄 CABEÇALHO DO REGISTRO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 p-0">
            <a href="{{ route('portal.verificacoes.index') }}" class="btn btn-sm btn-light border mb-2 shadow-none" style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-1"></i> Voltar ao Histórico
            </a>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="m-0 text-bold" style="font-size: 2rem;">
                        <i class="fas fa-file-invoice mr-2 text-muted"></i>Registro de Verificação #{{ $checklist->id }}
                    </h1>
                    <p class="text-muted mb-0">Documento imutável registrado em {{ $checklist->created_at->format('d/m/Y H:i:s') }}.</p>
                </div>
                <div class="d-flex align-items-center" style="gap: 10px;">
                    @php
                        // 🛡️ LÓGICA DE AÇÃO DINÂMICA
                        $isEntry = ($checklist->type == 'entry');
                        $hasExit = \App\Models\VehicleChecklist::where('vehicle_id', $checklist->vehicle_id)
                            ->where('driver_id', $checklist->driver_id)
                            ->where('type', 'exit')
                            ->where('created_at', '>', $checklist->created_at)
                            ->exists();
                        $showCheckoutAction = ($isEntry && !$hasExit);

                        // 🔗 NAVEGAÇÃO BIDIRECIONAL: encontra o checklist par via missão vinculada
                        $mission = \App\Models\VehicleMission::where('entry_id', $checklist->id)
                            ->orWhere('exit_id', $checklist->id)
                            ->first();

                        $pairedChecklistId = null;
                        if ($mission) {
                            $pairedChecklistId = $isEntry ? $mission->exit_id : $mission->entry_id;
                        }
                    @endphp

                    {{-- Botão de ir para o CHECK-IN (visível na tela de checkout) --}}
                    @if(!$isEntry && $pairedChecklistId)
                        <a href="/portal/verificacoes/{{ $pairedChecklistId }}"
                           class="btn btn-success shadow-sm text-bold px-4 py-2" style="border-radius: 10px;">
                            <i class="fas fa-sign-in-alt mr-2"></i> Ver Check-in
                        </a>
                    @endif

                    {{-- Botão de ir para o CHECKOUT (visível na tela de check-in finalizado) --}}
                    @if($isEntry && $pairedChecklistId)
                        <a href="/portal/verificacoes/{{ $pairedChecklistId }}"
                           class="btn btn-primary shadow-sm text-bold px-4 py-2" style="border-radius: 10px;">
                            <i class="fas fa-sign-out-alt mr-2"></i> Ver Checkout
                        </a>
                    @endif

                    {{-- Botão de FAZER checkout (quando ainda está em jornada ativa) --}}
                    @if($showCheckoutAction)
                        <a href="{{ route('portal.verificacoes.create', ['type' => 'exit', 'vehicle_id' => $checklist->vehicle_id]) }}"
                           class="btn btn-primary shadow-sm text-bold px-4 py-2" style="border-radius: 10px;">
                            <i class="fas fa-sign-out-alt mr-2"></i> CHECKOUT
                        </a>
                    @endif

                    @if($hasExit || $checklist->type == 'exit')
                        <a href="/portal/verificacoes" style="text-decoration: none;">
                            <span class="badge badge-success px-4 py-3 shadow-sm border border-success" style="font-size: 1rem; border-radius: 10px; background-color: rgba(40,167,69,0.1) !important; color: #28a745 !important;">
                                <i class="fas fa-check-double mr-2"></i> JORNADA FINALIZADA
                            </span>
                        </a>
                    @else
                        <span class="badge badge-warning px-4 py-3 shadow-sm" style="font-size: 1rem; border-radius: 10px; color: #856404 !important; background-color: #ffeeba !important;">
                            <i class="fas fa-truck-moving mr-2"></i> EM JORNADA ATIVA
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 🚛 INFO DO VEÍCULO E ODÔMETRO -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-dark text-white border-0 py-3 px-4">
                    <h5 class="text-bold m-0"><i class="fas fa-truck mr-2"></i>Dados do Ativo</h5>
                </div>
                <div class="card-body p-4">
                    <div class="mb-4">
                        <label class="text-uppercase text-muted font-weight-bold d-block mb-1" style="font-size: 0.7rem;">Veículo</label>
                        <h4 class="text-bold m-0">{{ $checklist->vehicle->plate }}</h4>
                        <p class="text-muted">{{ $checklist->vehicle->brand }} / {{ $checklist->vehicle->model }}</p>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="text-uppercase text-muted font-weight-bold d-block mb-1" style="font-size: 0.7rem;">Odômetro</label>
                            <h4 class="text-bold text-teal m-0">{{ number_format($checklist->odometer, 0, ',', '.') }} <small>KM</small></h4>
                        </div>
                        <div class="col-6 border-left">
                            <label class="text-uppercase text-muted font-weight-bold d-block mb-1" style="font-size: 0.7rem;">Combustível</label>
                            <h4 class="text-bold text-primary m-0">{{ $checklist->fuel_level }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ✍️ RELATO DO MOTORISTA -->
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border-left: 5px solid #20c997 !important;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="text-bold m-0 text-muted"><i class="fas fa-quote-left mr-2"></i>Relato do Motorista</h5>
                </div>
                <div class="card-body p-4 pt-1">
                    <p class="text-dark bg-light p-3 rounded" style="font-size: 1rem; line-height: 1.6; border: 1px solid #eee;">
                        "{{ $checklist->notes }}"
                    </p>
                    <div class="text-right">
                        <small class="text-muted"><i class="fas fa-user-edit mr-1"></i> Registrado por: {{ $checklist->driver->name }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- 📸 GALERIA DE EVIDÊNCIAS -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
                <div class="card-header bg-transparent border-0 pt-4 px-4">
                    <h5 class="text-bold m-0 text-muted"><i class="fas fa-images mr-2"></i>Evidências Visuais e Vistoria</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        @php
                            $labels = [
                                'photo_1' => 'Odômetro',
                                'photo_2' => 'Frente',
                                'photo_3' => 'Traseira',
                                'photo_4' => 'Lateral Dir.',
                                'photo_5' => 'Lateral Esq.',
                                'photo_6' => 'Carroceria Dir.',
                                'photo_7' => 'Carroceria Esq.',
                                'photo_8' => 'Extra 1',
                                'photo_9' => 'Extra 2',
                                'photo_10' => 'Extra 3',
                            ];
                        @endphp

                        @foreach($labels as $key => $label)
                            @if(isset($checklist->photos[$key]))
                                <div class="col-6 col-sm-4 col-lg-3 mb-4">
                                    <div class="photo-card border shadow-sm p-1 h-100" style="border-radius: 10px; background: #fff;">
                                        <div class="label-badge text-center py-1 bg-light text-muted font-weight-bold mb-1" style="font-size: 0.65rem; border-radius: 5px;">
                                            {{ $label }}
                                        </div>
                                        <a href="{{ asset('storage/' . $checklist->photos[$key]) }}" onclick="event.preventDefault(); viewPhoto(this.href, '{{ $label }}')">
                                            <img src="{{ asset('storage/' . $checklist->photos[$key]) }}" class="img-fluid rounded" 
                                                 style="width: 100%; height: 120px; object-fit: cover; cursor: zoom-in;" alt="{{ $label }}">
                                        </a>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
                <div class="card-footer bg-light border-0 py-3 text-center">
                    <small class="text-muted"><i class="fas fa-lock mr-1"></i> Este registro é protegido e não pode ser alterado.</small>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .text-teal { color: #20c997 !important; }
    .bg-teal { background-color: #20c997 !important; }
    .photo-card { transition: all 0.3s; }
    .photo-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; border-color: #20c997 !important; }
    
    /* 🌓 DARK MODE SUPPORT */
    .dark-mode .card { background-color: #1a1a2e; }
    .dark-mode .bg-light { background-color: #16213e !important; }
    .dark-mode .text-dark { color: #fff !important; }
    .dark-mode .bg-light.text-muted { color: #b2bac2 !important; }

    /* CUSTOM LIGTHBOX SWAL */
    .rtech-swal-image {
        max-height: 80vh;
        object-fit: contain;
        border: 4px solid #fff;
        box-shadow: 0 0 50px rgba(0,0,0,0.5);
    }
    .rtech-close-btn {
        position: absolute !important;
        top: 20px !important;
        right: 20px !important;
        color: white !important;
        text-shadow: 0 0 10px rgba(0,0,0,0.8);
        z-index: 1000 !important;
        transition: transform 0.2s ease !important;
    }
    .rtech-close-btn:hover {
        transform: scale(1.2);
        color: #ff3333 !important;
    }
</style>

@push('scripts')
<script>
    /**
     * 📸 VISUALIZADOR DE EVIDÊNCIAS RTECH (LIGHTBOX)
     */
    function viewPhoto(url, title) {
        Swal.fire({
            title: `<span class="text-bold text-uppercase d-block mb-2" style="font-size: 1.1rem; color: #fff; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">${title}</span>`,
            imageUrl: url,
            width: 'auto',
            imageWidth: 'auto',
            imageHeight: '75vh',
            background: 'transparent',
            showConfirmButton: false,
            showCloseButton: true,
            backdrop: `rgba(0,0,15,0.95)`, 
            showClass: { popup: 'animate__animated animate__zoomIn animate__faster' },
            hideClass: { popup: 'animate__animated animate__zoomOut animate__faster' },
            customClass: { 
                image: 'rounded m-0 shadow-lg',
                closeButton: 'rtech-close-btn shadow-none'
            }
        });
    }
</script>
@endpush
@endsection
