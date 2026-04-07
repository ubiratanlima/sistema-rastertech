@extends('layouts.app')

@section('title', 'Central do Instalador | Rastertech')

@section('content')
<div class="container-fluid pb-5">
    <!-- 🏗️ CABEÇALHO TÉCNICO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn">
        <div class="col-12 p-0 d-flex justify-content-between align-items-center flex-wrap">
            <div class="mb-2">
                <h1 class="m-0 text-bold" style="font-size: 2.2rem;">
                    <i class="fas fa-tools mr-2 text-primary"></i>Central do Instalador
                </h1>
                <p class="text-muted mb-0">Gestão e auditoria de vistorias técnicas e instalações (FLUXO TRIFÁSICO).</p>
            </div>
            <a href="{{ route('portal.instalador.checkin') }}" class="btn btn-primary btn-lg shadow-sm px-4 py-3 text-bold animate__animated animate__pulse animate__infinite" style="border-radius: 12px; font-size: 1.1rem; background-color: #007bff !important; border: 0; color: white;">
                <i class="fas fa-plus-circle mr-2"></i> INICIAR NOVA VISTORIA
            </a>
        </div>
    </div>

    <!-- 📊 LISTAGEM DE OBRAS -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="bg-light border-bottom">
                                <tr>
                                    <th class="py-3 px-4 text-muted small text-uppercase" style="width: 120px;">Data</th>
                                    <th class="py-3 text-muted small text-uppercase">Veículo / Placa</th>
                                    <th class="py-3 text-muted small text-uppercase">Cliente</th>
                                    <th class="py-3 text-muted small text-uppercase text-center">Status / Progresso</th>
                                    <th class="py-3 px-4 text-muted small text-uppercase text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($installations as $inst)
                                <tr class="animate__animated animate__fadeInUp animate__faster border-bottom">
                                    <td class="py-3 px-4 align-middle">
                                        <div class="d-flex flex-column text-muted">
                                            <span class="font-weight-bold text-dark" style="font-size: 0.95rem;">{{ $inst->created_at->format('d/m/Y') }}</span>
                                            <small style="font-size: 0.75rem;">{{ $inst->created_at->format('H:i') }}h</small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary-light p-2 rounded mr-3 text-primary" style="background: rgba(0,123,255,0.1);">
                                                <i class="fas fa-car-side"></i>
                                            </div>
                                            <div>
                                                <span class="d-block font-weight-bold text-uppercase">{{ $inst->vehicle_plate }}</span>
                                                <small class="text-muted d-block text-truncate" style="max-width: 250px;">{{ $inst->vehicle_details }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle font-weight-bold text-dark">
                                        {{ $inst->customer_name }}
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($inst->status == 'checked_in')
                                            <span class="badge badge-warning px-3 py-2" style="border-radius: 8px;">
                                                <i class="fas fa-truck-loading mr-1"></i> AGUARDANDO INSTALAÇÃO
                                            </span>
                                            <div class="progress mt-2" style="height: 4px; border-radius: 10px;">
                                                <div class="progress-bar bg-warning w-33"></div>
                                            </div>
                                        @elseif($inst->status == 'processing')
                                            <span class="badge badge-primary px-3 py-2" style="border-radius: 8px;">
                                                <i class="fas fa-bolt mr-1"></i> FIOS REGISTRADOS
                                            </span>
                                            <div class="progress mt-2" style="height: 4px; border-radius: 10px;">
                                                <div class="progress-bar bg-primary w-66"></div>
                                            </div>
                                        @elseif($inst->status == 'completed')
                                            <span class="badge badge-success px-3 py-2" style="border-radius: 8px;">
                                                <i class="fas fa-check-double mr-1"></i> FINALIZADA
                                            </span>
                                            <div class="progress mt-2" style="height: 4px; border-radius: 10px;">
                                                <div class="progress-bar bg-success w-100"></div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 align-middle text-right">
                                        @if($inst->status == 'checked_in')
                                            <a href="{{ route('portal.instalador.process', $inst->id) }}" class="btn btn-warning btn-sm text-bold px-3" style="border-radius: 8px;">
                                                <i class="fas fa-bolt mr-1"></i> INICIAR INSTALAÇÃO
                                            </a>
                                        @elseif($inst->status == 'processing')
                                            <a href="{{ route('portal.instalador.checkout', $inst->id) }}" class="btn btn-primary btn-sm text-bold px-3" style="border-radius: 8px;">
                                                <i class="fas fa-sign-out-alt mr-1"></i> FINALIZAR CHECKOUT
                                            </a>
                                        @else
                                            <a href="{{ route('portal.instalador.show', $inst->id) }}" class="btn btn-outline-dark btn-sm px-3" style="border-radius: 8px;">
                                                <i class="fas fa-eye mr-1"></i> VER DOSSIÊ
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="py-5">
                                            <i class="fas fa-clipboard-list fa-4x text-muted opacity-25 mb-3"></i>
                                            <p class="text-muted font-italic mb-0">Nenhuma instalação em andamento.</p>
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

<style>
    .bg-primary-light { background: rgba(0,123,255,0.08); }
    .w-33 { width: 33%; } .w-66 { width: 66%; } .w-100 { width: 100%; }
</style>
@endsection
