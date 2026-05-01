@extends('layouts.app')

@section('title', 'Homologação Técnica | Rastertech Admin')

@section('content')
<div class="container-fluid pb-5 font-roboto" style="background: #f4f6f9; min-height: 100vh;">
    <!-- 🏗️ CABEÇALHO EXECUTIVO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center pt-3">
        <div class="col-12 p-0 d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <a href="{{ route('admin.installations.index') }}" class="btn btn-sm btn-white border mb-2 shadow-sm" style="border-radius: 8px; font-weight: bold;">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar à Fila
                </a>
                <h1 class="m-0 text-bold text-dark" style="font-size: 1.8rem; letter-spacing: -0.5px;">
                    <i class="fas fa-check-double mr-2 text-primary"></i>Homologação Técnica #{{ $inst->id }}
                </h1>
                <p class="text-muted mb-0 font-weight-bold uppercase small" style="letter-spacing: 1px;">Auditoria de Campo e Certificação de Sinais</p>
            </div>
            <div class="mt-2 mt-md-0 d-flex align-items-center bg-white p-2 px-3 rounded-pill shadow-sm border">
                <div class="text-right mr-3">
                    <small class="text-muted d-block uppercase font-weight-bold" style="font-size: 0.6rem;">Técnico Responsável</small>
                    <span class="text-dark font-weight-bold">{{ $inst->installer->name ?? 'N/A' }}</span>
                </div>
                @if($inst->installer && $inst->installer->image)
                    <img src="{{ asset('storage/' . $inst->installer->image) }}" class="rounded-circle shadow-sm" width="40" height="40" style="object-fit: cover; border: 2px solid #007bff;">
                @else
                    <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                        <i class="fas fa-user-cog text-white"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 📲 GRID COM REORDENAMENTO INTELIGENTE -->
    <div class="row m-0">
        <!-- 📸 COLUNA DO VEÍCULO/FOTOS (Vem antes no mobile, mas fica na direita no PC) -->
        <div class="col-lg-8 col-md-7 p-0 pl-md-2 order-1 order-md-2">
            <!-- 🏷️ CABEÇALHO DO ATIVO -->
            <div class="card shadow-sm border-0 mb-4 bg-white" style="border-radius: 20px;">
                <div class="card-body p-4 d-flex justify-content-between align-items-center flex-wrap">
                    <div class="d-flex align-items-center mb-2 mb-md-0">
                        <div class="mercosul-plate shadow-sm mr-4">
                            <div class="mercosul-header">BRASIL</div>
                            <div class="mercosul-body text-primary">{{ $inst->vehicle_plate }}</div>
                        </div>
                        <div>
                            <h4 class="m-0 font-weight-bold text-dark text-uppercase" style="font-size: 1.2rem;">{{ $inst->customer_name }}</h4>
                            <small class="text-muted uppercase font-weight-bold small"><i class="fas fa-map-marker-alt mr-1"></i>Instalação Técnica Finalizada</small>
                        </div>
                    </div>
                    <div class="bg-light px-4 py-2 rounded-pill border shadow-inner">
                        <span class="text-success font-weight-bold small uppercase"><i class="fas fa-check-circle mr-2"></i>Técnico Concluído</span>
                    </div>
                </div>
            </div>

            <!-- 🖼️ GALERIA MULTI-ABAS -->
            <div class="card shadow-sm border-0 bg-white mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 p-3 pt-4 pb-0">
                    <ul class="nav nav-pills nav-justified bg-light p-2 rounded-pill shadow-inner" id="pills-tab" role="tablist" style="border: 1px solid #eee;">
                        <li class="nav-item mx-1">
                            <button class="nav-link active rounded-pill font-weight-bold uppercase small" id="pills-checkin-tab" data-toggle="pill" data-target="#pills-checkin" type="button" role="tab" style="font-size: 0.7rem;">1. Check-in</button>
                        </li>
                        <li class="nav-item mx-1">
                            <button class="nav-link rounded-pill font-weight-bold uppercase small" id="pills-process-tab" data-toggle="pill" data-target="#pills-process" type="button" role="tab" style="font-size: 0.7rem;">2. Instalação</button>
                        </li>
                        <li class="nav-item mx-1">
                            <button class="nav-link rounded-pill font-weight-bold uppercase small" id="pills-checkout-tab" data-toggle="pill" data-target="#pills-checkout" type="button" role="tab" style="font-size: 0.7rem;">3. Checkout</button>
                        </li>
                    </ul>
                </div>
                
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane fade show active" id="pills-checkin">
                            <div class="row mx-n2">
                                @forelse($inst->checkin_photos ?? [] as $key => $path)
                                <div class="col-6 col-md-3 px-2 mb-3">
                                    <div class="photo-card rounded border shadow-sm" style="background: url('{{ asset('storage/'.$path) }}') center/cover; height: 120px; cursor: pointer;" onclick="viewPhoto('{{ asset('storage/'.$path) }}', 'CHECK-IN: {{ $key }}')">
                                        <div class="photo-overlay">{{ strtoupper(str_replace('_', ' ', $key)) }}</div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12 text-center py-5 opacity-25"><p>Sem fotos</p></div>
                                @endforelse
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-process">
                            <div class="row mx-n2">
                                @forelse($inst->process_photos ?? [] as $key => $path)
                                <div class="col-6 col-md-4 px-2 mb-3">
                                    <div class="photo-card rounded border shadow-sm" style="background: url('{{ asset('storage/'.$path) }}') center/cover; height: 160px; cursor: pointer;" onclick="viewPhoto('{{ asset('storage/'.$path) }}', 'INSTALAÇÃO: {{ $key }}')">
                                        <div class="photo-overlay bg-primary-gradient">{{ strtoupper(str_replace('_', ' ', $key)) }}</div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12 text-center py-5 opacity-25"><p>Sem fotos</p></div>
                                @endforelse
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-checkout">
                            <div class="row mx-n2 mb-4">
                                @forelse($inst->checkout_photos ?? [] as $key => $path)
                                <div class="col-6 col-md-4 px-2 mb-3">
                                    <div class="photo-card rounded border shadow-sm" style="background: url('{{ asset('storage/'.$path) }}') center/cover; height: 160px; cursor: pointer;" onclick="viewPhoto('{{ asset('storage/'.$path) }}', 'CHECKOUT: {{ $key }}')">
                                        <div class="photo-overlay bg-success-gradient">{{ strtoupper(str_replace('_', ' ', $key)) }}</div>
                                    </div>
                                </div>
                                @empty
                                <div class="col-12 text-center py-5 opacity-25"><p>Sem fotos</p></div>
                                @endforelse
                            </div>
                            <div class="bg-light p-4 rounded border-left border-success shadow-inner" style="border-width: 5px !important;">
                                <h6 class="font-weight-bold uppercase small text-success mb-2">Relato do Técnico:</h6>
                                <p class="text-dark mb-0 font-italic">"{{ $inst->checkout_notes ?? 'Sem observações.' }}"</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🎯 COLUNA DO TERMINAL (Vem depois no mobile, mas fica na esquerda no PC) -->
        <div class="col-lg-4 col-md-5 p-0 pr-md-4 mb-4 order-2 order-md-1">
            <form action="{{ route('admin.installations.validate', $inst->id) }}" method="POST" id="validationForm">
                @csrf
                <input type="hidden" name="validation_status" id="validation_status" value="">
                
                <div class="card shadow-lg border-0 sticky-top" style="border-radius: 20px; top: 20px; z-index: 10; overflow: hidden;">
                    <div class="card-header bg-dark text-white p-4 border-0">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning rounded-circle p-2 mr-3 shadow-sm" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-project-diagram text-dark fa-sm"></i>
                            </div>
                            <div>
                                <h5 class="text-bold m-0 uppercase" style="font-size: 0.9rem; letter-spacing: 1px;">Terminal de Sinal</h5>
                                <small class="opacity-75 uppercase" style="font-size: 0.65rem;">Interação em tempo real</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body p-4 bg-white">
                        <div class="test-container mb-4">
                            <div class="test-row d-flex align-items-center justify-content-between p-3 mb-2 rounded bg-light border">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-wifi mr-3 text-primary"></i>
                                    <span class="font-weight-bold text-dark small uppercase">Dispositivo Online?</span>
                                </div>
                                <div class="custom-control custom-switch custom-switch-lg">
                                    <input type="checkbox" class="custom-control-input" id="test_online" name="test_online" value="1" {{ $inst->test_online ? 'checked' : '' }} {{ $inst->validation_status == 'approved' ? 'disabled' : '' }}>
                                    <label class="custom-control-label" for="test_online"></label>
                                </div>
                            </div>
                            <!-- ... Outros testes mantidos ... -->
                            @if($inst->has_block)
                            <div class="test-row d-flex align-items-center justify-content-between p-3 mb-2 rounded bg-light border">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-lock mr-3 text-danger"></i>
                                    <span class="font-weight-bold text-dark small uppercase">Bloqueio Ativo?</span>
                                </div>
                                <div class="custom-control custom-switch custom-switch-lg">
                                    <input type="checkbox" class="custom-control-input" id="test_block" name="test_block" value="1" {{ $inst->test_block ? 'checked' : '' }} {{ $inst->validation_status == 'approved' ? 'disabled' : '' }}>
                                    <label class="custom-control-label" for="test_block"></label>
                                </div>
                            </div>
                            @endif
                            <div class="test-row d-flex align-items-center justify-content-between p-3 mb-2 rounded bg-light border">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-key mr-3 text-warning"></i>
                                    <span class="font-weight-bold text-dark small uppercase">Pós Chave (ON)?</span>
                                </div>
                                <div class="custom-control custom-switch custom-switch-lg">
                                    <input type="checkbox" class="custom-control-input" id="test_ignition_on" name="test_ignition_on" value="1" {{ $inst->test_ignition_on ? 'checked' : '' }} {{ $inst->validation_status == 'approved' ? 'disabled' : '' }}>
                                    <label class="custom-control-label" for="test_ignition_on"></label>
                                </div>
                            </div>
                            <div class="test-row d-flex align-items-center justify-content-between p-3 mb-4 rounded bg-light border text-muted">
                                <div class="d-flex align-items-center text-muted">
                                    <i class="fas fa-power-off mr-3 opacity-50"></i>
                                    <span class="font-weight-bold small uppercase">Ignição (OFF)?</span>
                                </div>
                                <div class="custom-control custom-switch custom-switch-lg">
                                    <input type="checkbox" class="custom-control-input" id="test_ignition_off" name="test_ignition_off" value="1" {{ $inst->test_ignition_off ? 'checked' : '' }} {{ $inst->validation_status == 'approved' ? 'disabled' : '' }}>
                                    <label class="custom-control-label" for="test_ignition_off"></label>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <textarea name="validation_notes" class="form-control border-light bg-light small" rows="3" style="border-radius: 12px; resize: none;" {{ $inst->validation_status == 'approved' ? 'disabled' : '' }} placeholder="Parecer técnico...">{{ $inst->validation_notes }}</textarea>
                            </div>
                        </div>

                        @if($inst->validation_status == 'approved')
                            <div class="bg-success rounded-lg p-3 text-center text-white font-weight-bold shadow-sm">
                                <i class="fas fa-certificate mr-2"></i>HOMOLOGADO
                            </div>
                        @else
                            <div class="d-flex w-100" style="gap: 10px;">
                                <div style="flex: 1;"><button type="button" onclick="confirmValidation('rejected')" class="btn btn-danger btn-block shadow-sm py-3 font-weight-bold uppercase" style="border-radius: 15px; font-size: 0.9rem;">REJEITAR</button></div>
                                <div style="flex: 1;"><button type="button" onclick="confirmValidation('approved')" class="btn btn-success btn-block shadow-sm py-3 font-weight-bold uppercase" style="border-radius: 15px; font-size: 0.9rem;">APROVAR</button></div>
                            </div>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* 🇧🇷 PLACA MERCOSUL */
    .mercosul-plate { display: inline-flex; flex-direction: column; background: #fff; border: 1.5px solid #000; border-radius: 4px; overflow: hidden; min-width: 120px; line-height: 1; }
    .mercosul-header { background: #003399; color: #fff; font-size: 0.5rem; text-align: center; padding: 2px 0; font-weight: 800; border-bottom: 0.5px solid #000; }
    .mercosul-body { color: #000; font-size: 1.6rem; text-align: center; padding: 4px 10px; font-weight: bold; font-family: 'Roboto Mono', monospace; }

    /* 🎨 TABS */
    .nav-pills .nav-link { color: #6c757d; border: 1px solid transparent; width: 100%; cursor: pointer; transition: 0.3s; }
    .nav-pills .nav-link.active { background: #fff !important; color: #007bff !important; border-color: #007bff !important; box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }

    /* 📸 PHOTO CARDS */
    .photo-card { position: relative; transition: transform 0.2s ease, filter 0.2s ease; overflow: hidden; }
    .photo-card:hover { transform: scale(1.02); filter: brightness(1.1); }
    .photo-overlay { position: absolute; bottom: 0; left: 0; width: 100%; padding: 5px; background: rgba(0,0,0,0.6); color: #fff; font-size: 0.6rem; font-weight: bold; text-align: center; text-transform: uppercase; }
    .bg-primary-gradient { background: linear-gradient(transparent, #007bff) !important; }
    .bg-success-gradient { background: linear-gradient(transparent, #28a745) !important; }

    /* 🚥 SWITCHES */
    .custom-switch-lg .custom-control-label::before { height: 1.8rem; width: 3.2rem; border-radius: 2rem; }
    .custom-switch-lg .custom-control-label::after { width: calc(1.8rem - 4px); height: calc(1.8rem - 4px); border-radius: 2rem; }
    .custom-switch-lg .custom-control-input:checked ~ .custom-control-label::after { transform: translateX(1.4rem); }

    @media (max-width: 768px) {
        .sticky-top { position: relative !important; top: 0 !important; }
    }
</style>

@push('scripts')
<script>
    function confirmValidation(status) {
        $('#validation_status').val(status);
        const title = status === 'approved' ? 'APROVAR?' : 'REJEITAR?';
        const color = status === 'approved' ? '#28a745' : '#dc3545';
        Swal.fire({
            title: `<span class="font-weight-bold">${title}</span>`,
            text: 'Deseja confirmar o resultado técnico?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'SIM',
            confirmButtonColor: color,
            reverseButtons: true
        }).then((result) => { if (result.isConfirmed) $('#validationForm').submit(); });
    }
    function viewPhoto(url, title) { Swal.fire({ title: title, imageUrl: url, width: 'auto', imageWidth: 'auto', imageHeight: '80vh', showConfirmButton: false, showCloseButton: true, backdrop: 'rgba(0,0,0,0.9)' }); }
</script>
@endpush
@endsection
