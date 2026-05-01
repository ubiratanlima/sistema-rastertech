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
            <p class="text-primary mb-0 pl-md-5 font-weight-bold" style="font-size: 1.1rem; letter-spacing: 0.5px;">
                <i class="fas fa-building mr-1"></i> {{ $customer->name ?? 'Rastertech Operacional' }}
            </p>
        </div>
        <div class="col-12 col-md-4 p-0 text-md-right mt-3 mt-md-0">
            @if($isOnline)
                <div class="d-inline-flex align-items-center bg-warning-soft px-3 px-md-4 py-2 rounded-pill border border-warning animate__animated animate__pulse animate__infinite shadow-sm">
                    <span class="status-dot bg-warning mr-2 d-none d-md-inline-block"></span>
                    <span class="text-warning-dark font-weight-bold small text-uppercase d-none d-md-inline">Jornada em Aberto</span>
                    <span class="text-warning-dark font-weight-bold small text-uppercase d-md-none">Veículo em uso</span>
                </div>
            @endif
        </div>
    </div>

    <!-- 🚥 BOTÕES DE COMANDO (PADRÃO PREMIUM) -->
    <div class="row mb-5 justify-content-center">
        <div class="col-md-6 mb-3">
            <div class="{{ $isOnline ? 'disabled-action' : '' }}" 
                 onclick="{{ $isOnline ? 'showActionError("checkin")' : 'location.href="'.route('portal.verificacoes.create', 'entry').'"' }}"
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
            <div class="{{ !$isOnline ? 'disabled-action' : '' }}" 
                 onclick="{{ !$isOnline ? 'showActionError("checkout")' : 'location.href="'.route('portal.verificacoes.create', 'exit').'"' }}"
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
        <div class="card-header bg-transparent border-0 px-4 pt-4 d-flex align-items-center justify-content-center justify-content-md-start">
            <h4 class="text-bold m-0"><i class="fas fa-clipboard-list mr-2 text-muted"></i>Histórico de Jornadas</h4>
            <div class="ml-auto d-none d-sm-block">
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
                            <th class="px-4 py-3 border-0 text-left d-none d-md-table-cell">ID</th>
                            <th class="py-3 border-0 text-center">VEÍCULO</th>
                            <!-- 📱 COLUNA UNIFICADA PARA MOBILE -->
                            <th class="py-3 border-0 text-left d-md-none">JORNADA</th>
                            <!-- 💻 COLUNAS SEPARADAS PARA DESKTOP -->
                            <th class="py-3 border-0 text-left d-none d-md-table-cell">INÍCIO</th>
                            <th class="py-3 border-0 text-left d-none d-md-table-cell">FIM</th>
                            <th class="py-3 border-0 text-center d-none d-md-table-cell">DESLOCAMENTO</th>
                            <th class="px-4 py-3 border-0 text-center text-md-right">AÇÃO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($checklists as $mission)
                        <tr>
                            <td class="px-4 py-3 text-left d-none d-md-table-cell">
                                <span class="text-bold text-dark h6 mb-0">#{{ str_pad($mission->id, 5, '0', STR_PAD_LEFT) }}</span>
                            </td>
                            <td class="py-3 text-center">
                                <!-- 🇧🇷 PLACA MERCOSUL TÁTICA (REDUZIDA 20% NO MOBILE) -->
                                <div class="mercosul-plate shadow-sm">
                                    <div class="mercosul-header">BRASIL</div>
                                    <div class="mercosul-body">{{ $mission->vehicle->plate ?? 'N/A' }}</div>
                                </div>
                            </td>

                            <!-- 📱 CÉLULA MOBILE UNIFICADA (LÓGICA DE RESUMO MESMO DIA) -->
                            <td class="py-3 text-left d-md-none">
                                <div class="d-flex flex-column" style="gap: 4px;">
                                    @php
                                        $entryDate = $mission->entryChecklist ? $mission->entryChecklist->created_at->format('d/m/Y') : null;
                                        $exitDate = $mission->exitChecklist ? $mission->exitChecklist->created_at->format('d/m/Y') : null;
                                        $sameDay = ($entryDate && $exitDate && $entryDate === $exitDate);
                                    @endphp

                                    @if($sameDay)
                                        <!-- 🛣️ RESUMO MESMO DIA -->
                                        <div class="d-flex align-items-center text-dark font-weight-bold" style="font-size: 0.85rem;">
                                            <i class="fas fa-route text-teal mr-2"></i>
                                            {{ $mission->entryChecklist->created_at->format('d/m') }}
                                        </div>
                                        <div class="small opacity-75 font-weight-bold text-muted">
                                            {{ $mission->entryChecklist->created_at->format('H:i') }} <i class="fas fa-caret-right mx-1"></i> {{ $mission->exitChecklist->created_at->format('H:i') }}
                                        </div>
                                    @else
                                        <!-- 📅 DIAS DIFERENTES OU EM CAMPO (MANTÉM ORIGINAL) -->
                                        @if($mission->entryChecklist)
                                            <div class="d-flex align-items-center text-dark font-weight-bold" style="font-size: 0.85rem;">
                                                <i class="fas fa-sign-in-alt text-success mr-2"></i>
                                                {{ $mission->entryChecklist->created_at->format('d/m H:i') }}
                                            </div>
                                        @endif
                                        @if($mission->exitChecklist)
                                            <div class="d-flex align-items-center text-primary font-weight-bold" style="font-size: 0.85rem;">
                                                <i class="fas fa-sign-out-alt text-primary mr-2"></i>
                                                {{ $mission->exitChecklist->created_at->format('d/m H:i') }}
                                            </div>
                                        @else
                                            <div class="text-warning font-weight-bold" style="font-size: 0.7rem;">
                                                <i class="fas fa-truck-moving mr-1"></i> EM CAMPO
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </td>

                            <!-- 💻 CÉLULAS DESKTOP -->
                            <td class="py-3 text-left d-none d-md-table-cell">
                                @if($mission->entryChecklist)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-arrow-circle-right text-success mr-2" style="font-size: 1rem;"></i>
                                        <div>
                                            <div class="text-bold text-dark" style="font-size: 0.9rem;">{{ $mission->entryChecklist->created_at->format('d/m/Y H:i') }}</div>
                                            <div class="text-muted small text-uppercase d-none d-md-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ $mission->driver->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted small italic opacity-50">S/ Registro</span>
                                @endif
                            </td>
                            <td class="py-3 text-left d-none d-md-table-cell">
                                @if($mission->exitChecklist)
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-arrow-circle-left text-primary mr-2" style="font-size: 1rem;"></i>
                                        <div>
                                            <div class="text-bold text-primary" style="font-size: 0.9rem;">{{ $mission->exitChecklist->created_at->format('d/m/Y H:i') }}</div>
                                            <div class="text-muted small text-uppercase d-none d-md-block" style="font-size: 0.65rem; letter-spacing: 0.5px;">{{ $mission->exitChecklist->driver->name ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                @else
                                    <span class="badge badge-warning-soft text-warning px-3 py-1 border border-warning shadow-sm" style="font-size: 0.65rem; border-radius: 20px;">
                                        <i class="fas fa-truck-moving fa-flip-horizontal mr-1"></i> EM CAMPO
                                    </span>
                                @endif
                            </td>

                            <td class="py-3 text-center d-none d-md-table-cell">
                                @if($mission->entryChecklist && $mission->exitChecklist)
                                    @php
                                        $km = $mission->exitChecklist->odometer - $mission->entryChecklist->odometer;
                                        $diff = $mission->entryChecklist->created_at->diff($mission->exitChecklist->created_at);
                                        $duration = $diff->format('%hh %im');
                                    @endphp
                                    <div class="d-inline-block px-2 py-2 rounded shadow-xs border bg-success-light text-success" style="min-width: 100px; border-color: rgba(40,167,69,0.2) !important;">
                                        <div class="d-flex justify-content-center align-items-center opacity-75" style="gap: 8px; font-size: 0.7rem;">
                                            <span title="Duração Total" class="font-weight-bold"><i class="far fa-clock mr-1"></i>{{ $duration }}</span>
                                            <span title="Distância Percorrida" class="font-weight-bold"><i class="fas fa-route mr-1"></i>{{ number_format($km, 0, ',', '.') }} KM</span>
                                        </div>
                                    </div>
                                @elseif($mission->status === 'closed')
                                    <div class="d-inline-block px-3 py-2 rounded shadow-xs border bg-success-light text-success" style="min-width: 100px; border-color: rgba(40,167,69,0.2) !important;">
                                        <div class="text-muted text-center opacity-75" style="font-size: 0.65rem;">
                                            <i class="fas fa-info-circle mr-1"></i>Baixa ADM
                                        </div>
                                    </div>
                                @else
                                    <span class="badge badge-warning-soft text-warning px-2 py-1 border border-warning shadow-sm" style="font-size: 0.6rem; border-radius: 20px;">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> ATIVO
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-md-right">
                                <div class="d-flex justify-content-center justify-content-md-end align-items-center">
                                    @if($mission->entry_id)
                                        <a href="{{ route('portal.verificacoes.show', $mission->entry_id) }}" 
                                           class="action-btn btn-success-soft mr-1 mr-md-2" title="Início">
                                            <i class="fas fa-sign-in-alt"></i>
                                        </a>
                                    @endif

                                    @if($mission->exit_id)
                                        <a href="{{ route('portal.verificacoes.show', $mission->exit_id) }}" 
                                           class="action-btn btn-primary-soft" title="Fim">
                                            <i class="fas fa-sign-out-alt"></i>
                                        </a>
                                    @else
                                        <div class="action-btn btn-light disabled d-none d-md-flex" style="opacity: 0.2;">
                                            <i class="fas fa-clock"></i>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-5 text-center text-muted">
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
        width: 34px; height: 34px; border-radius: 10px; 
        display: flex; align-items: center; justify-content: center; 
        text-decoration: none; transition: all 0.2s; 
    }
    @media (max-width: 768px) {
        .action-btn { width: 30px; height: 30px; border-radius: 8px; }
    }
    .btn-success-soft { background: rgba(40, 167, 69, 0.1); color: #28a745; border: 1px solid rgba(40, 167, 69, 0.2); }
    .btn-success-soft:hover { background: #28a745; color: white; transform: translateY(-2px); }
    
    .btn-primary-soft { background: rgba(0, 123, 255, 0.1); color: #007bff; border: 1px solid rgba(0, 123, 255, 0.2); }
    .btn-primary-soft:hover { background: #007bff; color: white; transform: translateY(-2px); }
    
    .status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .text-warning-dark { color: #856404; }
    .shadow-xs { box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    
    .disabled-action { filter: grayscale(1); opacity: 0.6; pointer-events: none; }

    /* 🇧🇷 ESTILO PLACA MERCOSUL (LEGACY LOCAL REMOVED - NOW GLOBAL) */
    @media (max-width: 768px) {
        /* 📏 PADDINGS LATERAIS ZERADOS NO MOBILE */
        .container-fluid { padding-left: 0 !important; padding-right: 0 !important; }
        .card-header { padding-left: 10px !important; padding-right: 10px !important; padding-top: 15px !important; }
        .table th, .table td { padding-left: 5px !important; padding-right: 5px !important; }
        .px-4 { padding-left: 0 !important; padding-right: 0 !important; }
    }
</style>

@push('scripts')
<script>
    function showActionError(type) {
        let title = 'OPERAÇÃO BLOQUEADA';
        let msg = type === 'checkin' 
            ? 'Para iniciar uma nova missão, você deve primeiro encerrar a jornada ativa.' 
            : 'Não é possível realizar Check-out pois não existem jornadas ativas no momento.';
        
        Swal.fire({
            title: `<span class="text-bold">${title}</span>`,
            html: msg,
            icon: 'warning',
            confirmButtonColor: '#007bff'
        });
    }
</script>
@endpush
@endsection
