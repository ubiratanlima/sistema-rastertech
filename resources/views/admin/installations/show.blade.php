@extends('layouts.app')

@section('title', 'Homologação Técnica | Rastertech Admin')

@section('content')
<div class="container-fluid pb-5 font-roboto">
    <!-- 🏗️ CABEÇALHO DE AUDITORIA -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 p-0 d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <a href="{{ route('admin.installations.index') }}" class="btn btn-sm btn-light border mb-2 shadow-none" style="border-radius: 8px;">
                    <i class="fas fa-arrow-left mr-1"></i> Voltar à Fila
                </a>
                <h1 class="m-0 text-bold" style="font-size: 2rem;">
                    <i class="fas fa-check-double mr-2 text-primary"></i>Homologação Técnica #{{ $inst->id }}
                </h1>
                <p class="text-muted mb-0 font-weight-bold uppercase small">Revisão de Evidências de Campo e Teste de Sinais</p>
            </div>
            <div class="mt-2 mt-md-0 d-flex align-items-center">
                <span class="mr-3 font-weight-bold opacity-50">Técnico: <span class="text-dark">{{ $inst->installer->name }}</span></span>
                <img src="{{ $inst->installer->image ?? asset('img/user-default.png') }}" class="rounded-circle shadow-sm" width="45" height="45" style="object-fit: cover;">
            </div>
        </div>
    </div>

    <div class="row">
        <!-- 🎯 TERMINAL DE TESTES DA CENTRAL (ADMIN-ONLY) -->
        <div class="col-md-4">
            <form action="{{ route('admin.installations.validate', $inst->id) }}" method="POST" id="validationForm">
                @csrf
                <div class="card shadow-lg border-0 mb-4" style="border-radius: 15px; position: sticky; top: 100px;">
                    <div class="card-header bg-dark text-white pt-4 pb-3 px-4 border-0" style="border-radius: 15px 15px 0 0;">
                        <h4 class="text-bold m-0 small uppercase"><i class="fas fa-project-diagram mr-2 text-warning"></i>Terminal de Sinal</h4>
                        <small class="opacity-75 uppercase" style="font-size: 0.65rem;">Validação em tempo real com hardware</small>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <!-- ✅ CHECKLIST DE TESTES -->
                        <div class="test-item mb-3 p-3 rounded d-flex align-items-center justify-content-between border-bottom bg-light">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-wifi mr-3 text-muted"></i>
                                <span class="font-weight-bold text-dark">Dispositivo Online?</span>
                            </div>
                            <div class="custom-control custom-switch custom-switch-lg p-0">
                                <input type="checkbox" class="custom-control-input" id="test_online" name="test_online" value="1" {{ $inst->test_online ? 'checked' : '' }}>
                                <label class="custom-control-label" for="test_online" style="cursor: pointer;"></label>
                            </div>
                        </div>

                        @if($inst->has_block)
                        <div class="test-item mb-3 p-3 rounded d-flex align-items-center justify-content-between border-bottom bg-light">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-lock mr-3 text-muted"></i>
                                <span class="font-weight-bold text-dark">Bloqueio Ativo?</span>
                            </div>
                            <div class="custom-control custom-switch custom-switch-lg p-0">
                                <input type="checkbox" class="custom-control-input" id="test_block" name="test_block" value="1" {{ $inst->test_block ? 'checked' : '' }}>
                                <label class="custom-control-label" for="test_block" style="cursor: pointer;"></label>
                            </div>
                        </div>
                        @endif

                        <div class="test-item mb-3 p-3 rounded d-flex align-items-center justify-content-between border-bottom bg-light">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-key mr-3 text-muted"></i>
                                <span class="font-weight-bold text-dark">Pós Chave (ON)?</span>
                            </div>
                            <div class="custom-control custom-switch custom-switch-lg p-0">
                                <input type="checkbox" class="custom-control-input" id="test_ignition_on" name="test_ignition_on" value="1" {{ $inst->test_ignition_on ? 'checked' : '' }}>
                                <label class="custom-control-label" for="test_ignition_on" style="cursor: pointer;"></label>
                            </div>
                        </div>

                        <div class="test-item mb-4 p-3 rounded d-flex align-items-center justify-content-between border-bottom bg-light">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-power-off mr-3 text-muted"></i>
                                <span class="font-weight-bold text-dark">Ignição (OFF)?</span>
                            </div>
                            <div class="custom-control custom-switch custom-switch-lg p-0">
                                <input type="checkbox" class="custom-control-input" id="test_ignition_off" name="test_ignition_off" value="1" {{ $inst->test_ignition_off ? 'checked' : '' }}>
                                <label class="custom-control-label" for="test_ignition_off" style="cursor: pointer;"></label>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label class="text-uppercase text-muted font-weight-bold small">Observações de Validação</label>
                            <textarea name="validation_notes" class="form-control border bg-light small" rows="4" placeholder="Alguna observação sobre os testes sinais..." style="border-radius: 10px;">{{ $inst->validation_notes }}</textarea>
                        </div>

                        <input type="hidden" name="validation_status" id="validation_status" value="{{ $inst->validation_status }}">

                        <div class="row g-2">
                            <div class="col-6">
                                <button type="button" onclick="confirmValidation('approved')" class="btn btn-success btn-lg btn-block shadow-sm py-3 text-bold uppercase small" style="border-radius: 12px;">
                                    <i class="fas fa-check-circle d-block mb-1"></i> APROVAR
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" onclick="confirmValidation('rejected')" class="btn btn-danger btn-lg btn-block shadow-sm py-3 text-bold uppercase small" style="border-radius: 12px;">
                                    <i class="fas fa-times-circle d-block mb-1"></i> REJEITAR
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- 📸 REVISÃO DE CAMPO (DOSSIÊ DO TÉCNICO) -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0 bg-transparent mb-4">
                <div class="card-body p-0">
                    <!-- ✅ CABEÇALHO DO ATIVO -->
                    <div class="card shadow-sm border-0 mb-4 bg-white" style="border-radius: 15px;">
                        <div class="card-body p-4 d-flex justify-content-between align-items-center border-left border-primary" style="border-width: 5px !important;">
                            <div>
                                <h3 class="plate-mercosul mb-0 mx-auto">
                                    <span class="p-header">BRASIL</span>
                                    <span class="p-body text-primary">{{ $inst->vehicle_plate }}</span>
                                </h3>
                                <small class="text-muted font-weight-bold uppercase d-block mt-2">{{ $inst->customer_name }}</small>
                            </div>
                            <div class="text-right">
                                <p class="text-muted small mb-0 uppercase font-weight-bold">Status Técnico</p>
                                <span class="badge badge-success px-3 py-1" style="border-radius: 10px;">{{ strtoupper($inst->status) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 📸 GALERIA CONSOLIDADA -->
                    <ul class="nav nav-pills mb-3 bg-white p-2 shadow-sm rounded-pill justify-content-center" id="pills-tab" role="tablist">
                        <li class="nav-item mx-1" role="presentation">
                            <button class="nav-link active rounded-pill px-4 py-2 font-weight-bold uppercase small" id="tab-checkin" data-toggle="pill" data-target="#pills-checkin" type="button" role="tab">Check-in</button>
                        </li>
                        <li class="nav-item mx-1" role="presentation">
                            <button class="nav-link rounded-pill px-4 py-2 font-weight-bold uppercase small" id="tab-process" data-toggle="pill" data-target="#pills-process" type="button" role="tab">Elétrica</button>
                        </li>
                        <li class="nav-item mx-1" role="presentation">
                            <button class="nav-link rounded-pill px-4 py-2 font-weight-bold uppercase small" id="tab-checkout" data-toggle="pill" data-target="#pills-checkout" type="button" role="tab">Checkout</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-checkin" role="tabpanel">
                            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                                <div class="card-body p-4">
                                    <div class="row row-cols-2 row-cols-md-4 g-3">
                                        @foreach($inst->checkin_photos as $key => $path)
                                        <div class="col mb-3">
                                            <div class="photo-review-item rounded shadow-sm border" style="background: url('{{ asset('storage/'.$path) }}') center/cover; height: 120px; cursor: zoom-in;" onclick="viewPhoto('{{ asset('storage/'.$path) }}', '{{ strtoupper($key) }}')">
                                                <div class="badge-label-rtech">{{ strtoupper(str_replace('_', ' ', $key)) }}</div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-process" role="tabpanel">
                            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                                <div class="card-body p-4">
                                    <div class="row row-cols-2 row-cols-md-3 g-4">
                                        @foreach($inst->process_photos as $key => $path)
                                        <div class="col mb-3">
                                            <div class="photo-review-item rounded shadow-sm border" style="background: url('{{ asset('storage/'.$path) }}') center/cover; height: 160px; cursor: zoom-in;" onclick="viewPhoto('{{ asset('storage/'.$path) }}', 'ELÉTRICA: {{ strtoupper($key) }}')">
                                                <div class="badge-label-rtech bg-primary">{{ strtoupper(str_replace('_', ' ', $key)) }}</div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-checkout" role="tabpanel">
                            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                                <div class="card-body p-4">
                                    <div class="row row-cols-1 row-cols-md-3 g-4">
                                        @foreach($inst->checkout_photos as $key => $path)
                                        <div class="col mb-3">
                                            <div class="photo-review-item rounded shadow-sm border" style="background: url('{{ asset('storage/'.$path) }}') center/cover; height: 180px; cursor: zoom-in;" onclick="viewPhoto('{{ asset('storage/'.$path) }}', 'CHECKOUT: {{ strtoupper($key) }}')">
                                                <div class="badge-label-rtech bg-success">{{ strtoupper(str_replace('_', ' ', $key)) }}</div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <hr class="my-4">
                                    <h6 class="text-bold uppercase small text-muted">Relato Final do Técnico:</h6>
                                    <p class="text-muted font-italic bg-light p-3 rounded">"{{ $inst->checkout_notes }}"</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .plate-mercosul { display: flex; flex-direction: column; background: white; border: 3px solid #333; border-radius: 6px; width: 160px; overflow: hidden; }
    .plate-mercosul .p-header { background: #003399; color: white; font-size: 0.5rem; padding: 2px; font-weight: bold; letter-spacing: 2px; }
    .plate-mercosul .p-body { font-size: 1.8rem; font-weight: 800; padding: 2px; letter-spacing: 2px; }
    
    .nav-pills .nav-link.active { background-color: #f8f9fa !important; color: #007bff !important; border: 2px solid #007bff !important; }
    .nav-pills .nav-link { color: #666; border: 2px solid transparent; }
    
    .photo-review-item { position: relative; transition: all 0.3s ease; }
    .photo-review-item:hover { filter: brightness(1.2); transform: scale(1.02); }
    .badge-label-rtech { position: absolute; bottom: 0; left: 0; width: 100%; padding: 3px 6px; font-size: 0.6rem; font-weight: bold; color: white; background: rgba(0,0,0,0.6); }

    /* 🚥 SWITCH LG */
    .custom-switch-lg .custom-control-label::before { height: 1.8rem; width: 3.2rem; border-radius: 2rem; }
    .custom-switch-lg .custom-control-label::after { width: calc(1.8rem - 4px); height: calc(1.8rem - 4px); border-radius: 2rem; }
    .custom-switch-lg .custom-control-input:checked ~ .custom-control-label::after { transform: translateX(1.4rem); }
</style>

@push('scripts')
<script>
    function confirmValidation(status) {
        $('#validation_status').val(status);
        const color = status === 'approved' ? '#28a745' : '#dc3545';
        const title = status === 'approved' ? 'APROVAR INSTALAÇÃO?' : 'REJEITAR INSTALAÇÃO?';
        const text = status === 'approved' ? 'Esta obra será marcada como HOMOLOGADA no sistema.' : 'Esta obra será devolvida para correção técnica.';

        Swal.fire({
            title: `<span class="text-bold">${title}</span>`,
            text: text,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'SIM, CONFIRMAR',
            cancelButtonText: 'CANCELAR',
            confirmButtonColor: color,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $('#validationForm').submit();
            }
        });
    }

    function viewPhoto(url, title) {
        Swal.fire({
            title: `<span class="text-bold text-uppercase" style="font-size: 1.1rem; color: #fff;">${title}</span>`,
            imageUrl: url,
            width: '80vw',
            background: 'transparent',
            showConfirmButton: false,
            showCloseButton: true,
            backdrop: `rgba(0,0,15,0.95)`, 
            customClass: { image: 'rounded border border-white' }
        });
    }
</script>
@endpush
@endsection
