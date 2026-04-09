@extends('layouts.app')

@section('title', 'Dossiê Detalhado de Instalação | Rastertech')

@section('content')
<div class="container-fluid pb-5">
    <!-- 🏗️ CABEÇALHO DO DOSSIÊ SUPREMO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 p-0">
            <a href="{{ route('portal.instalador.index') }}" class="btn btn-sm btn-light border mb-2 shadow-none" style="border-radius: 8px;">
                <i class="fas fa-arrow-left mr-1"></i> Voltar à Central
            </a>
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="m-0 text-bold" style="font-size: 2rem;">
                        <i class="fas fa-shield-alt mr-2 text-primary"></i>Dossiê Consolidado <small class="text-muted">#{{ $inst->id }}</small>
                    </h1>
                    <p class="text-muted mb-0 font-weight-bold uppercase small"><i class="fas fa-history mr-1"></i> Auditoria de Ciclo Completo (Check-in, Elétrica, Checkout)</p>
                </div>
                <div class="mt-2 mt-md-0">
                    <span class="badge badge-success px-4 py-2" style="border-radius: 20px; font-size: 0.9rem;">
                        <i class="fas fa-check-double mr-1"></i> OPERAÇÃO FINALIZADA
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- 📊 LINHA DO TEMPO RTECH -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <div class="row text-center align-items-center">
                        <div class="col-md-3 mb-2 mb-md-0">
                            <i class="fas fa-truck-loading text-warning mb-1"></i>
                            <p class="small text-muted uppercase mb-0 font-weight-bold">Check-in</p>
                            <h5 class="text-bold mb-0">{{ $inst->checkin_at->format('d/m/Y H:i') }}</h5>
                        </div>
                        <div class="col-md-1 d-none d-md-block"><i class="fas fa-chevron-right text-muted opacity-25"></i></div>
                        <div class="col-md-3 mb-2 mb-md-0 border-left border-right border-md-0">
                            <i class="fas fa-bolt text-primary mb-1"></i>
                            <p class="small text-muted uppercase mb-0 font-weight-bold">Elétrica</p>
                            <h5 class="text-bold mb-0 text-primary">{{ $inst->processed_at ? $inst->processed_at->format('d/m/Y H:i') : '--' }}</h5>
                        </div>
                        <div class="col-md-1 d-none d-md-block"><i class="fas fa-chevron-right text-muted opacity-25"></i></div>
                        <div class="col-md-3 border-md-0">
                            <i class="fas fa-flag-checkered text-success mb-1"></i>
                            <p class="small text-muted uppercase mb-0 font-weight-bold">Checkout</p>
                            <h5 class="text-bold mb-0 text-success">{{ $inst->completed_at ? $inst->completed_at->format('d/m/Y H:i') : '--' }}</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 🚛 IDENTIFICAÇÃO DO ATIVO -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 15px;">
                <div class="card-header bg-dark text-white pt-4 px-4 text-center border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="text-bold m-0 small uppercase"><i class="fas fa-file-contract mr-2 text-warning"></i>INSTALAÇÃO REALIZADA</h5>
                </div>
                <div class="card-body px-4 pb-4 pt-4">
                    <div class="text-center mb-4">
                        <div class="plate-mercosul shadow-sm mb-3 mx-auto">
                            <div class="plate-header">BRASIL</div>
                            <div class="plate-body">{{ $inst->vehicle_plate }}</div>
                        </div>
                    </div>

                    <ul class="list-group list-group-flush" style="font-size: 1rem;">
                        <li class="list-group-item px-0 bg-transparent d-flex justify-content-between border-0 py-1">
                            <span class="text-muted text-bold small uppercase mt-1">CLIENTE:</span>
                            <span class="text-bold text-dark text-right ml-2">{{ $inst->customer_name }}</span>
                        </li>
                        <li class="list-group-item px-0 bg-transparent d-flex justify-content-between border-0 py-1">
                            <span class="text-muted text-bold small uppercase mt-1">Bloqueio:</span>
                            @if($inst->has_block)
                                <span class="badge badge-success px-3 py-1 font-weight-bold" style="border-radius: 10px;">POSSUI BLOQUEIO</span>
                            @else
                                <span class="badge badge-danger px-3 py-1 font-weight-bold" style="border-radius: 10px;">SEM BLOQUEIO</span>
                            @endif
                        </li>
                    </ul>
                    <hr class="my-3 opacity-25">
                    <h6 class="text-bold text-muted small uppercase mb-2">Relato Técnico Final</h6>
                    <div class="bg-light p-3 rounded text-muted font-italic mb-4" style="border-left: 4px solid #28a745; line-height: 1.5;">
                        "{{ $inst->checkout_notes ?? 'Sem observações registradas.' }}"
                    </div>

                    <h6 class="text-bold text-muted small uppercase mb-2">HISTÓRICO DE ATENDIMENTO</h6>
                    <div class="attendance-history bg-light p-3 rounded text-muted small border">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge badge-primary px-2 py-1 uppercase" style="font-size: 0.6rem;">INSTALAÇÃO</span>
                            <span class="font-weight-bold opacity-50">{{ $inst->completed_at ? $inst->completed_at->format('d/m/Y') : $inst->created_at->format('d/m/Y') }}</span>
                        </div>
                        <p class="mb-0 font-italic">O técnico {{ $inst->installer->name }} realizou a instalação e vistoria completa deste ativo.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 📸 GALERIA CONSOLIDADA (DIVIDIDA POR FASES) -->
        <div class="col-md-8">
            <!-- 🏁 NAVEGAÇÃO DE FASES (TABS ESTILIZADAS) -->
            <ul class="nav nav-pills mb-3 bg-white p-2 shadow-sm rounded-pill justify-content-center" id="pills-tab" role="tablist">
                <li class="nav-item mx-1" role="presentation">
                    <button class="nav-link active rounded-pill px-4 py-2 font-weight-bold uppercase" style="font-size: 0.75rem" id="tab-checkin" data-toggle="pill" data-target="#pills-checkin" type="button" role="tab"><i class="fas fa-truck-loading mr-1"></i> Fase 1: Check-in</button>
                </li>
                <li class="nav-item mx-1" role="presentation">
                    <button class="nav-link rounded-pill px-4 py-2 font-weight-bold uppercase" style="font-size: 0.75rem" id="tab-process" data-toggle="pill" data-target="#pills-process" type="button" role="tab"><i class="fas fa-bolt mr-1"></i> Fase 2: Instalação</button>
                </li>
                <li class="nav-item mx-1" role="presentation">
                    <button class="nav-link rounded-pill px-4 py-2 font-weight-bold uppercase" style="font-size: 0.75rem" id="tab-checkout" data-toggle="pill" data-target="#pills-checkout" type="button" role="tab"><i class="fas fa-flag-checkered mr-1"></i> Fase 3: Checkout</button>
                </li>
            </ul>

            <div class="tab-content" id="pills-tabContent">
                <!-- 📸 ABA 1: CHECKIN -->
                <div class="tab-pane fade show active" id="pills-checkin" role="tabpanel">
                    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="row row-cols-2 row-cols-md-4 g-3">
                                @if($inst->checkin_photos)
                                @foreach($inst->checkin_photos as $key => $path)
                                <div class="col mb-3">
                                    <div class="gallery-item-rtech rounded shadow-sm border" 
                                         style="background: url('{{ asset('storage/'.$path) }}') center/cover; height: 140px; cursor: zoom-in; position: relative;"
                                         onclick="viewPhoto('{{ asset('storage/'.$path) }}', 'CHECK-IN: {{ strtoupper($key) }}')">
                                        <div class="label-photo text-white px-2 py-1 rounded-bottom w-100" style="background: rgba(255,193,7,0.7); position: absolute; bottom: 0; left: 0; font-size: 0.6rem; font-weight: bold; color: #000 !important;">
                                            {{ strtoupper(str_replace('_', ' ', $key)) }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 📸 ABA 2: PROCESSO ELÉTRICO -->
                <div class="tab-pane fade" id="pills-process" role="tabpanel">
                    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                        <div class="card-body p-4">
                            <div class="row row-cols-2 row-cols-md-3 g-4">
                                @if($inst->process_photos)
                                @foreach($inst->process_photos as $key => $path)
                                <div class="col mb-3">
                                    <div class="gallery-item-rtech rounded shadow-sm border" 
                                         style="background: url('{{ asset('storage/'.$path) }}') center/cover; height: 180px; cursor: zoom-in; position: relative;"
                                         onclick="viewPhoto('{{ asset('storage/'.$path) }}', 'ELÉTRICA: {{ strtoupper($key) }}')">
                                        <div class="label-photo text-white px-2 py-1 rounded-bottom w-100" style="background: rgba(0,123,255,0.7); position: absolute; bottom: 0; left: 0; font-size: 0.65rem; font-weight: bold;">
                                            {{ strtoupper(str_replace('_', ' ', $key)) }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 📸 ABA 3: CHECKOUT -->
                <div class="tab-pane fade" id="pills-checkout" role="tabpanel">
                    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                        <div class="card-body p-4 text-center">
                            <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
                                @if($inst->checkout_photos)
                                @foreach($inst->checkout_photos as $key => $path)
                                <div class="col mb-3">
                                    <div class="gallery-item-rtech rounded shadow-sm border" 
                                         style="background: url('{{ asset('storage/'.$path) }}') center/cover; height: 220px; cursor: zoom-in; position: relative;"
                                         onclick="viewPhoto('{{ asset('storage/'.$path) }}', 'CHECKOUT: {{ strtoupper($key) }}')">
                                        <div class="label-photo text-white px-2 py-1 rounded-bottom w-100" style="background: rgba(40,167,69,0.7); position: absolute; bottom: 0; left: 0; font-size: 0.7rem; font-weight: bold;">
                                            {{ strtoupper(str_replace('_', ' ', $key)) }}
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .plate-mercosul { display: inline-block; background: white; border: 3px solid #333; border-radius: 8px; width: 180px; overflow: hidden; }
    .plate-header { background: #003399; color: white; font-size: 0.6rem; padding: 2px 5px; font-weight: bold; letter-spacing: 2px; }
    .plate-body { font-size: 2rem; font-weight: 800; font-family: 'Arial Narrow', sans-serif; color: #333; letter-spacing: 2px; color: #003399; }
    
    .nav-pills .nav-link.active { background-color: #f8f9fa !important; color: #007bff !important; border: 2px solid #007bff !important; }
    .nav-pills .nav-link { color: #666; border: 2px solid transparent; }
    
    .gallery-item-rtech { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .gallery-item-rtech:hover { transform: scale(1.03); filter: brightness(1.2); }
    
    .uppercase { text-transform: uppercase; }
    .opacity-25 { opacity: 0.25; }

    /* 📸 AJUSTE BOTÃO FECHAR SWAL (DENTRO DA IMAGEM) - PADRÃO RTECH */
    .swal2-close {
        position: absolute !important;
        top: 20px !important;
        right: 20px !important;
        color: white !important;
        text-shadow: 0 0 10px rgba(0,0,0,0.8);
        z-index: 1000 !important;
        transition: transform 0.2s ease !important;
    }
    .swal2-close:hover {
        transform: scale(1.2);
        color: #ff3333 !important;
    }
    .swal2-image {
        margin-top: 0 !important;
        border: 4px solid white !important;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5) !important;
    }
</style>

@push('scripts')
<script>
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
                closeButton: 'custom-close-btn'
            }
        });
    }
</script>
@endpush
@endsection
