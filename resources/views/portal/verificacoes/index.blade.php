@extends('layouts.app')

@section('title', 'Missões e Jornadas | Rastertech')

@section('content')
<div class="container-fluid">
    <!-- 🚜 CABEÇALHO DA CENTRAL DE MISSÕES -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 col-md-8 p-0">
            <h1 class="m-0 text-bold" style="font-size: 2.2rem;">
                <i class="fas fa-route mr-3 text-teal"></i>Controle de Jornadas
            </h1>
            <p class="text-muted mb-0 pl-md-5">Gestão unificada de Check-in e Checkout por missão operacional.</p>
        </div>
        <div class="col-12 col-md-4 p-0 text-md-right mt-3 mt-md-0">
            @if($isOnline)
                <div class="d-inline-flex align-items-center bg-warning-soft px-4 py-2 rounded-pill border border-warning animate__animated animate__pulse animate__infinite shadow-sm">
                    <span class="status-dot bg-warning mr-2"></span>
                    <span class="text-warning-dark font-weight-bold small text-uppercase">Jornada em Aberto</span>
                </div>
            @endif
        </div>
    </div>

    <!-- 🚥 BOTÕES DE COMANDO (PADRÃO PREMIUM) -->
    <div class="row mb-5 justify-content-center">
        <div class="col-md-6 mb-3">
            <div class="{{ ($isOnline && !$isSupervisor) ? 'disabled-action' : '' }}" 
                 onclick="{{ ($isOnline && !$isSupervisor) ? 'showActionError("checkin")' : 'location.href="'.route('portal.verificacoes.create', 'entry').'"' }}"
                 style="cursor: pointer;">
                <div class="card bg-success shadow border-0 h-100 hover-zoom text-white overflow-hidden" style="border-radius: 20px;">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="mr-4 rounded-circle bg-white-10 p-3">
                            <i class="fas fa-sign-in-alt fa-3x"></i>
                        </div>
                        <div class="flex-fill">
                            <h2 class="text-bold mb-1">CHECK-IN</h2>
                            <p class="mb-0 opacity-75">Iniciar nova jornada.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="{{ (!$isOnline && !$isSupervisor) ? 'disabled-action' : '' }}" 
                 onclick="{{ (!$isOnline && !$isSupervisor) ? 'showActionError("checkout")' : 'location.href="'.route('portal.verificacoes.create', 'exit').'"' }}"
                 style="cursor: pointer;">
                <div class="card bg-primary shadow border-0 h-100 hover-zoom text-white overflow-hidden" style="border-radius: 20px;">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="mr-4 rounded-circle bg-white-10 p-3">
                            <i class="fas fa-sign-out-alt fa-3x"></i>
                        </div>
                        <div class="flex-fill">
                            <h2 class="text-bold mb-1">CHECKOUT</h2>
                            <p class="mb-0 opacity-75">Encerrar e devolver veículo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 📋 TABELA PADRONIZADA (PADRÃO RASTERTECH) -->
    <div class="card shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 16px;">
        <div class="card-header bg-transparent border-0 px-4 pt-4 d-flex align-items-center">
            <h4 class="text-bold m-0"><i class="fas fa-clipboard-list mr-2 text-muted"></i>Histórico de Jornadas</h4>
            <div class="ml-auto">
                <span class="badge badge-light border px-3 py-2 text-muted font-weight-normal" style="border-radius: 10px;">
                    <i class="fas fa-filter mr-1"></i> Filtrando Últimas 15
                </span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-light">
                        <tr class="text-sm">
                            <th class="px-4 py-3 border-0 text-left">ID</th>
                            <th class="py-3 border-0 text-center">VEÍCULO (PLACA)</th>
                            <th class="py-3 border-0 text-left">INÍCIO</th>
                            <th class="py-3 border-0 text-left">FIM</th>
                            <th class="py-3 border-0 text-center">DESLOCAMENTO</th>
                            <th class="px-4 py-3 border-0 text-right">AÇÃO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($checklists as $mission)
                        <tr>
                            <td class="px-4 py-3 text-left">
                                <span class="text-bold text-dark h6 mb-0">#{{ str_pad($mission->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="py-3 text-center">
                                <!-- 🇧🇷 PLACA MERCOSUL TÁTICA -->
                                <div class="d-inline-block border border-dark shadow-sm" style="border-radius: 4px; overflow: hidden; min-width: 95px; background: #fff;">
                                    <div class="bg-primary text-white text-center" style="font-size: 0.5rem; line-height: 1; padding: 2px 0; font-weight: bold;">BRASIL</div>
                                    <div class="text-dark font-weight-bold px-2 py-0" style="font-size: 1.05rem; font-family: 'Courier New', Courier, monospace; letter-spacing: 1px;">
                                        {{ $mission->vehicle->plate ?? 'N/A' }}
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 text-left">
                                @if($mission->entryChecklist)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-arrow-circle-right text-success mr-2" style="font-size: 1rem;"></i>
                                        <div>
                                            <div class="text-bold text-dark" style="font-size: 0.9rem;">{{ $mission->entryChecklist->created_at->format('d/m/Y H:i') }}</div>
                                            <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ $mission->driver->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small italic opacity-50">S/ Registro</span>
                                @endif
                            </td>
                            <td class="py-3 text-left">
                                @if($mission->exitChecklist)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-arrow-circle-left text-primary mr-2" style="font-size: 1rem;"></i>
                                        <div>
                                            <div class="text-bold text-primary" style="font-size: 0.9rem;">{{ $mission->exitChecklist->created_at->format('d/m/Y H:i') }}</div>
                                            <div class="text-muted small text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ $mission->exitChecklist->driver->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge badge-warning-soft text-warning px-3 py-1 border border-warning shadow-sm" style="font-size: 0.65rem; border-radius: 20px;">
                                        <i class="fas fa-truck-moving fa-flip-horizontal mr-1"></i> EM CAMPO
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 text-center">
                                @if($mission->entryChecklist && $mission->exitChecklist)
                                    @php
                                        $km = $mission->exitChecklist->odometer - $mission->entryChecklist->odometer;
                                        $diff = $mission->entryChecklist->created_at->diff($mission->exitChecklist->created_at);
                                        $duration = $diff->format('%hh %im');
                                    @endphp
                                    <div class="d-inline-block px-3 py-2 rounded shadow-xs border bg-success-light text-success" style="min-width: 120px; border-color: rgba(40,167,69,0.2) !important;">
                                        <div class="text-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">
                                            <i class="fas fa-check-double mr-1 font-weight-normal"></i>FINALIZADO
                                        </div>
                                        <div class="d-flex justify-content-center align-items-center opacity-75" style="gap: 12px; font-size: 0.75rem;">
                                            <span title="Duração Total"><i class="far fa-clock mr-1"></i>{{ $duration }}</span>
                                            <span title="Distância Percorrida"><i class="fas fa-route mr-1"></i>{{ number_format($km, 0, ',', '.') }} KM</span>
                                        </div>
                                    </div>
                                @elseif($mission->status === 'closed')
                                    {{-- Missão fechada administrativamente (sem check-in vinculado) --}}
                                    <div class="d-inline-block px-3 py-2 rounded shadow-xs border bg-success-light text-success" style="min-width: 120px; border-color: rgba(40,167,69,0.2) !important;">
                                        <div class="text-bold text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">
                                            <i class="fas fa-check-double mr-1 font-weight-normal"></i>FINALIZADO
                                        </div>
                                        <div class="text-muted text-center opacity-75" style="font-size: 0.65rem;">
                                            <i class="fas fa-info-circle mr-1"></i>Baixa Administrativa
                                        </div>
                                    </div>
                                @else
                                    <span class="badge badge-warning-soft text-warning px-3 py-2 border border-warning animate__animated animate__pulse animate__infinite" style="font-size: 0.65rem; border-radius: 20px;">
                                        <i class="fas fa-truck-moving fa-flip-horizontal mr-1"></i> EM CAMPO
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <div class="d-flex justify-content-end align-items-center">
                                    @if($mission->entry_id)
                                        <a href="{{ route('portal.verificacoes.show', $mission->entry_id) }}" 
                                           class="action-btn btn-success-soft mr-2" title="Log de Início">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </a>
                                    @endif

                                    @if($mission->exit_id)
                                        <a href="{{ route('portal.verificacoes.show', $mission->exit_id) }}" 
                                           class="action-btn btn-primary-soft" title="Log de Fim">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </a>
                                    @else
                                        <div class="action-btn btn-light disabled" style="opacity: 0.2;" title="Em campo">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted">
                                <i class="fas fa-map-marker-alt fa-3x mb-3 opacity-25"></i>
                                <p>Aguardando a primeira jornada do sistema.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($checklists->hasPages())
        <div class="card-footer bg-transparent border-0 px-4 py-4">
            {{ $checklists->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    /* ✨ STYLING PADRÃO RASTERTECH PREMIUM */
    .hover-zoom:hover { transform: scale(1.02); transition: all 0.3s; }
    .bg-teal { background-color: #20c997 !important; }
    .text-teal { color: #20c997 !important; }
    .bg-white-10 { background: rgba(255,255,255,0.15); }
    
    /* 🎨 UTILITÁRIOS DE CORES SUAVES */
    .bg-success-light { background-color: rgba(40, 167, 69, 0.08); }
    .bg-primary-light { background-color: rgba(0, 123, 255, 0.08); }
    
    /* 🔘 BOTÕES DE AÇÃO CIRCULARES */
    .action-btn { 
        width: 38px; height: 38px; border-radius: 12px; 
        display: flex; align-items: center; justify-content: center; 
        text-decoration: none; transition: all 0.2s; 
    }
    .btn-success-soft { background: rgba(40, 167, 69, 0.1); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.2); }
    .btn-success-soft:hover { background: #28a745; color: white; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(40, 167, 69, 0.2); }
    
    .btn-primary-soft { background: rgba(0, 123, 255, 0.1); color: #007bff; border: 1px solid rgba(0, 123, 255, 0.2); }
    .btn-primary-soft:hover { background: #007bff; color: white; transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2); }
    
    .status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .text-warning-dark { color: #856404; }
    .shadow-xs { box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    
    .disabled-action { filter: grayscale(1); opacity: 0.6; pointer-events: none; }
</style>

@push('scripts')
<script>
    function showActionError(type) {
        Swal.fire({
            title: '<span class="text-bold">OPERAÇÃO BLOQUEADA</span>',
            html: 'Para iniciar uma nova missão, você deve primeiro encerrar a jornada ativa.',
            icon: 'warning',
            confirmButtonColor: '#007bff'
        });
    }
</script>
@endpush
@endsection
