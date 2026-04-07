@extends('layouts.app')

@section('title', 'Minhas Verificações | Rastertech')

@section('content')
<div class="container-fluid">
    <!-- 🚜 CABEÇALHO DO MOTORISTA -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 col-md-8 p-0">
            <h1 class="m-0 text-bold" style="font-size: 2rem;">
                <i class="fas fa-clipboard-check mr-2 text-teal"></i>Minhas Verificações
            </h1>
            <p class="text-muted mb-0">Gestão de jornada e inspeção veicular em tempo real.</p>
        </div>
        <div class="col-12 col-md-4 p-0 text-md-right mt-3 mt-md-0">
            @if($isOnline)
                <div class="d-inline-flex align-items-center bg-warning-soft px-3 py-2 rounded-pill border border-warning animate__animated animate__pulse animate__infinite">
                    <span class="status-dot bg-warning mr-2"></span>
                    <span class="text-warning-dark font-weight-bold small text-uppercase">Jornada em Aberto</span>
                </div>
            @endif
        </div>
    </div>

    <!-- 🛡️ PERFIL OPERACIONAL -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0 bg-dark text-white" style="border-radius: 12px; overflow: hidden;">
                <div class="card-body p-4 d-flex align-items-center">
                    <div class="mr-4">
                        <div class="rounded-circle bg-teal d-flex align-items-center justify-content-center shadow-sm" style="width: 80px; height: 80px;">
                            <i class="fas fa-user-tie fa-2x"></i>
                        </div>
                    </div>
                    <div>
                        <h4 class="text-bold mb-1">{{ $driver->name }}</h4>
                        <div class="d-flex flex-wrap" style="gap: 15px;">
                            <span class="badge badge-teal p-2 px-3 shadow-sm" style="font-size: 0.85rem;">
                                <i class="fas fa-id-card mr-1"></i> CNH: {{ $driver->cnh_number }}
                            </span>
                            <span class="badge badge-outline-light p-2 px-3 border" style="font-size: 0.85rem; opacity: 0.8;">
                                <i class="fas fa-calendar-check mr-1"></i> Última: {{ $driver->last_checklist_at ? $driver->last_checklist_at->format('d/m/Y H:i') : 'Nenhuma realizada' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 🚥 BOTÕES DE AÇÃO (PONTO DE COMANDO) -->
    <div class="row mb-5 justify-content-center">
        <!-- BOTÃO CHECK-IN -->
        <div class="col-md-6 mb-3">
            <div class="h-100 {{ $isOnline ? 'disabled-action gray-scale opacity-50' : '' }}" 
                 onclick="{{ $isOnline ? 'showActionError("checkin")' : 'location.href="'.route('portal.verificacoes.create', 'entry').'"' }}"
                 style="cursor: pointer;">
                <div class="card bg-success shadow border-0 h-100 hover-zoom text-white" style="border-radius: 16px; transition: transform 0.2s;">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="mr-4">
                            <i class="fas fa-sign-in-alt fa-4x opacity-50"></i>
                        </div>
                        <div class="flex-fill">
                            <h2 class="text-bold mb-1">CHECK-IN</h2>
                            <p class="mb-0 opacity-75">Verificação obrigatória <strong>antes de sair</strong> com o veículo.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- BOTÃO CHECK-OUT -->
        <div class="col-md-6 mb-3">
            <div class="h-100 {{ !$isOnline ? 'disabled-action gray-scale opacity-50' : '' }}" 
                 onclick="{{ !$isOnline ? 'showActionError("checkout")' : 'location.href="'.route('portal.verificacoes.create', 'exit').'"' }}"
                 style="cursor: pointer;">
                <div class="card bg-primary shadow border-0 h-100 hover-zoom text-white" style="border-radius: 16px; transition: transform 0.2s;">
                    <div class="card-body p-4 d-flex align-items-center">
                        <div class="mr-4">
                            <i class="fas fa-sign-out-alt fa-4x opacity-50"></i>
                        </div>
                        <div class="flex-fill">
                            <h2 class="text-bold mb-1">CHECK-OUT</h2>
                            <p class="mb-0 opacity-75">Verificação ao <strong>devolver o veículo</strong> na empresa.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 📜 HISTÓRICO DE VERIFICAÇÕES -->
    <div class="card shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px;">
        <div class="card-header bg-transparent border-0 px-4 pt-4">
            <h4 class="text-bold m-0"><i class="fas fa-history mr-2 text-muted"></i>Histórico Recente</h4>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-center">
                    <thead class="bg-light">
                        <tr class="text-uppercase text-muted" style="font-size: 0.75rem; letter-spacing: 1px;">
                            <th class="px-4 py-3 border-0 text-left">Data / Hora</th>
                            <th class="py-3 border-0">Tipo</th>
                            <th class="py-3 border-0">Veículo</th>
                            <th class="py-3 border-0">Odômetro</th>
                            <th class="px-4 py-3 border-0 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($checklists as $checklist)
                        <tr class="{{ ($loop->first && $isOnline) ? 'bg-warning-light animate__animated animate__flash animate__slow' : '' }}">
                            <td class="px-4 py-3 text-left">
                                <div class="text-bold d-flex align-items-center">
                                    @if($loop->first && $isOnline)
                                        <i class="fas fa-exclamation-triangle text-warning mr-2" title="Jornada Ativa"></i>
                                    @endif
                                    {{ $checklist->created_at->format('d/m/Y') }}
                                </div>
                                <div class="text-muted small" style="font-weight: 400;">{{ $checklist->created_at->format('H:i:s') }}</div>
                            </td>
                            <td class="py-3">
                                @if($checklist->type == 'entry')
                                    <span class="badge badge-success px-3 py-2 shadow-sm" style="border-radius: 5px;">
                                        CHECK-IN
                                    </span>
                                    @if($loop->first && $isOnline)
                                        <div class="mt-1"><span class="badge badge-warning text-xs">EM JORNADA</span></div>
                                    @endif
                                @else
                                    <span class="badge badge-primary px-3 py-2 shadow-sm" style="border-radius: 5px;">
                                        CHECK-OUT
                                    </span>
                                @endif
                            </td>
                            <td class="py-3">
                                <span class="text-dark font-weight-bold">{{ $checklist->vehicle?->plate ?? 'N/A' }}</span>
                                <div class="text-muted small">{{ $checklist->vehicle?->brand }} / {{ $checklist->vehicle?->model }}</div>
                            </td>
                            <td class="py-3">
                                <span class="badge badge-light border px-2 py-1">{{ number_format($checklist->odometer, 0, ',', '.') }} KM</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('portal.verificacoes.show', $checklist->id) }}" class="btn btn-sm btn-outline-dark shadow-none" style="border-radius: 8px;">
                                    <i class="fas fa-eye mr-1"></i> Detalhes
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-5">
                                <i class="fas fa-clipboard-list fa-3x mb-3 opacity-25"></i>
                                <p>Nenhuma verificação registrada ainda.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($checklists->hasPages())
        <div class="card-footer bg-transparent border-0 px-4">
            {{ $checklists->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .hover-zoom:hover { transform: translateY(-5px); }
    .bg-teal { background-color: #20c997 !important; }
    .text-teal { color: #20c997 !important; }
    .badge-teal { background-color: #20c997; color: white; }
    .gray-scale { filter: grayscale(1); }
    .opacity-50 { opacity: 0.5; }
    .disabled-action { transition: all 0.3s; }
    .disabled-action:hover { filter: grayscale(0.5); opacity: 0.7; }
    
    .status-dot { width: 10px; height: 10px; border-radius: 50%; display: inline-block; }
    .bg-warning-soft { background-color: rgba(255, 193, 7, 0.1); }
    .text-warning-dark { color: #856404; }
    .bg-warning-light { background-color: rgba(255, 193, 7, 0.05); }

    /* 🌓 DARK MODE SUPPORT */
    .dark-mode .card { background-color: #1a1a2e; }
    .dark-mode .bg-light { background-color: #16213e !important; }
    .dark-mode .text-dark { color: #fff !important; }
</style>

@push('scripts')
<script>
    /**
     * 🛡️ COMANDO DE ALERTA ASSISTIDO (SWEETALERT2)
     */
    function showActionError(type) {
        if (type === 'checkin') {
            Swal.fire({
                title: '<span class="text-bold">CHECK-IN BLOQUEADO</span>',
                html: 'Você já possui uma jornada <b>em aberto</b> com o veículo <b>{{ $lastChecklist->vehicle?->plate ?? "" }}</b>.<br><br>Para iniciar um novo Check-in, você deve primeiro realizar o Check-out da jornada anterior.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-sign-out-alt mr-2"></i> IR PARA CHECK-OUT',
                cancelButtonText: 'CANCELAR',
                reverseButtons: true,
                customClass: {
                    popup: 'border-0 shadow-lg',
                    confirmButton: 'btn-lg shadow-sm',
                    cancelButton: 'btn-lg shadow-none'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('portal.verificacoes.create', 'exit') }}";
                }
            });
        } else {
            Swal.fire({
                title: '<span class="text-bold">CHECK-OUT BLOQUEADO</span>',
                html: 'Nenhuma jornada ativa encontrada para o seu perfil.<br><br>O Check-out só é permitido quando você já realizou a entrada de um veículo na empresa.',
                icon: 'info',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-sign-in-alt mr-2"></i> REALIZAR CHECK-IN AGORA',
                cancelButtonText: 'VOLTAR',
                reverseButtons: true,
                customClass: {
                    popup: 'border-0 shadow-lg',
                    confirmButton: 'btn-lg shadow-sm',
                    cancelButton: 'btn-lg shadow-none'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('portal.verificacoes.create', 'entry') }}";
                }
            });
        }
    }

    // Alertas de redirecionamento (caso o controller barra o acesso direto)
    @if(session('warning'))
        Swal.fire({
            icon: 'warning',
            title: 'AVISO OPERACIONAL',
            text: "{{ session('warning') }}",
            confirmButtonColor: '#20c997'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'BLOQUEIO DE SEGURANÇA',
            text: "{{ session('error') }}",
            confirmButtonColor: '#dc3545'
        });
    @endif
</script>
@endpush
@endsection
