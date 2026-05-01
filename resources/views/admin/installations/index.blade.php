@extends('layouts.app')

@section('title', 'Filas de Homologação | Rastertech Admin')

@push('styles')
<style>
    /* 🇧🇷 ESTILO PLACA MERCOSUL (GOLD STANDARD) - IDENTICO AO MENU VEICULOS */
    .mercosul-plate { 
        display: inline-flex; 
        flex-direction: column; 
        background: #fff; 
        border: 1.5px solid #000; 
        border-radius: 4px; 
        overflow: hidden; 
        min-width: 110px; 
        line-height: 1; 
        vertical-align: middle; 
    }
    .mercosul-header { 
        background: #003399; 
        color: #fff; 
        font-size: 0.45rem; 
        text-align: center; 
        padding: 2px 0; 
        font-weight: 800; 
        letter-spacing: 1.5px; 
        border-bottom: 0.5px solid #000; 
    }
    .mercosul-body { 
        color: #000; 
        font-size: 1.2rem; 
        text-align: center; 
        padding: 4px 10px; 
        font-weight: bold; 
        font-family: 'Roboto Mono', monospace; 
        letter-spacing: -1px; 
    }
</style>
@endpush

@section('content')
<div class="container-fluid pb-5">
    <!-- 🏗️ CABEÇALHO EXECUTIVO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn">
        <div class="col-12 p-0 d-flex justify-content-between align-items-end flex-wrap">
            <div>
                <h1 class="m-0 text-bold" style="font-size: 2.2rem;">
                    <i class="fas fa-check-double mr-2 text-success"></i>Validação
                </h1>
                <p class="text-muted mb-0 font-weight-bold uppercase small"><i class="fas fa-stream mr-1"></i> Auditoria de Hardware e Homologação Técnica de Campo</p>
            </div>
            
            <!-- 🔍 FILTROS TÁTICOS -->
            <div class="mt-3 mt-md-0 d-flex flex-wrap">
                <form action="{{ route('admin.installations.index') }}" method="GET" class="d-flex flex-wrap align-items-center">
                    <select name="status" class="form-control form-control-sm mr-2 mb-2 font-weight-bold" onchange="this.form.submit()" style="width: 180px; border-radius: 8px;">
                        <option value="">Status: TODOS</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>🟡 PENDENTE</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>🟢 APROVADO</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>🔴 REJEITADO</option>
                    </select>
                    <input type="text" name="search" class="form-control form-control-sm mr-2 mb-2" style="width: 200px; border-radius: 8px;" placeholder="Placa ou Cliente..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary btn-sm mb-2 px-3" style="border-radius: 8px;"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </div>

    <!-- 📊 LISTAGEM DE AUDITORIA -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body p-0 text-center">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="bg-light border-bottom">
                                <tr>
                                    <th class="py-3 px-4 text-muted small text-uppercase text-center" style="width: 140px;">DATA</th>
                                    <th class="py-3 text-muted small text-uppercase text-center">Veículo</th>
                                    <th class="py-3 text-muted small text-uppercase text-center">Instalador</th>
                                    <th class="py-3 text-muted small text-uppercase text-center">Estado do Sinal</th>
                                    <th class="py-3 text-muted small text-uppercase text-center">Validador</th>
                                    <th class="py-3 px-4 text-muted small text-uppercase text-center">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($installations as $inst)
                                <tr class="animate__animated animate__fadeInUp animate__faster border-bottom">
                                    <td class="py-3 px-4 align-middle text-center">
                                        <div class="d-flex flex-column">
                                            <span class="text-bold text-dark">{{ $inst->created_at->format('d/m/Y') }}</span>
                                            <small class="text-muted">{{ $inst->created_at->format('H:i') }}h</small>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex flex-column align-items-center justify-content-center py-2">
                                            <div class="mercosul-plate shadow-sm mb-1 mx-auto">
                                                <div class="mercosul-header">BRASIL</div>
                                                <div class="mercosul-body">{{ $inst->vehicle_plate }}</div>
                                            </div>
                                            <small class="text-muted d-block text-truncate font-weight-bold" style="max-width: 250px;">{{ $inst->customer_name }}</small>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="d-flex align-items-center justify-content-center">
                                            @if($inst->installer && $inst->installer->image)
                                                <img src="{{ asset('storage/' . $inst->installer->image) }}" class="rounded-circle mr-2" width="30" height="30" style="object-fit: cover; border: 1px solid #ddd;">
                                            @else
                                                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mr-2 shadow-sm" style="width: 30px; height: 30px; border: 1px solid #ddd;">
                                                    <i class="fas fa-user-cog text-muted" style="font-size: 0.8rem;"></i>
                                                </div>
                                            @endif
                                            <span class="font-weight-bold small text-dark">{{ $inst->installer->name ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($inst->validation_status == 'pending')
                                            <span class="badge badge-warning px-3 py-1 font-weight-bold shadow-sm" style="border-radius: 20px;">
                                                <i class="fas fa-clock mr-1"></i> PENDENTE
                                            </span>
                                        @elseif($inst->validation_status == 'approved')
                                            <span class="badge badge-success px-3 py-1 font-weight-bold shadow-sm" style="border-radius: 20px;">
                                                <i class="fas fa-check-circle mr-1"></i> APROVADO
                                            </span>
                                        @else
                                            <span class="badge badge-danger px-3 py-1 font-weight-bold shadow-sm" style="border-radius: 20px;">
                                                <i class="fas fa-times-circle mr-1"></i> REJEITADO
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($inst->validator)
                                            <div class="d-flex flex-column align-items-center">
                                                <span class="font-weight-bold text-dark small">{{ $inst->validator->name }}</span>
                                                <small class="text-muted" style="font-size: 0.65rem;">{{ $inst->validated_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted small">--</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 align-middle text-center">
                                        @if(!$inst->completed_at)
                                            @php
                                                $statusTxt = "<b>Fase 1:</b> Vistoria em andamento";
                                                if($inst->checkin_at) $statusTxt = "<b>Fase 1:</b> Completa<br><b>Fase 2:</b> Instalação em andamento";
                                                if($inst->processed_at) $statusTxt = "<b>Fase 1:</b> Completa<br><b>Fase 2:</b> Completa<br><b>Fase 3:</b> Checkout em andamento";
                                            @endphp
                                            <a href="javascript:void(0)" onclick="Swal.fire({ title: 'Instalação em Andamento', html: '{!! $statusTxt !!}', icon: 'info', confirmButtonColor: '#ffc107' })" class="btn btn-outline-warning btn-sm px-3 font-weight-bold" style="border-radius: 8px;">
                                                <i class="fas fa-tools mr-1"></i> STATUS
                                            </a>
                                        @elseif($inst->validation_status == 'approved')
                                            <a href="{{ route('admin.installations.show', $inst->id) }}" class="btn btn-outline-success btn-sm px-3 font-weight-bold" style="border-radius: 8px;">
                                                <i class="fas fa-check-double mr-1"></i> DETALHES
                                            </a>
                                        @else
                                            <a href="{{ route('admin.installations.show', $inst->id) }}" class="btn btn-outline-primary btn-sm px-3 font-weight-bold" style="border-radius: 8px;">
                                                <i class="fas fa-search-plus mr-1"></i> VALIDAR
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-5">
                                            <i class="fas fa-search fa-4x text-muted opacity-25 mb-3"></i>
                                            <p class="text-muted font-italic mb-0">Nenhuma instalação localizada para auditoria.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($installations->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-center">
                        {{ $installations->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if(session('info_status'))
            Swal.fire({
                title: '{{ session('info_status')['title'] }}',
                html: `{!! str_replace(["\r", "\n"], [' ', '<br>'], session('info_status')['message']) !!}`,
                icon: 'info',
                confirmButtonText: 'ENTENDIDO',
                confirmButtonColor: '#ffc107'
            });
        @endif
    });
</script>
@endpush

@endsection
