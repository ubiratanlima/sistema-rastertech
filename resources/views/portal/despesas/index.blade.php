@extends('layouts.app')

@section('title', 'Minhas Despesas | Rastertech')

@section('content')
<div class="container-fluid pb-5">
    <!-- 📄 CABEÇALHO DO MÓDULO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn">
        <div class="col-12 p-0 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="m-0 text-bold" style="font-size: 2.2rem;">
                    <i class="fas fa-file-invoice-dollar mr-2 text-orange"></i>Suas Despesas
                </h1>
                <p class="text-muted mb-0">Controle total de abastecimentos, manutenção e gastos operacionais.</p>
            </div>
            <a href="{{ route('portal.despesas.create') }}" class="btn btn-orange btn-lg shadow-sm px-4 py-3 text-bold animate__animated animate__pulse animate__infinite" style="border-radius: 12px; font-size: 1.1rem; background-color: #fd7e14 !important; border: 0; color: white;">
                <i class="fas fa-plus-circle mr-2"></i> REGISTRAR DESPESA
            </a>
        </div>
    </div>

    <!-- 📊 LISTAGEM TÁTICA -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle m-0">
                            <thead class="bg-light border-bottom">
                                <tr>
                                    <th class="py-3 px-4 text-muted small text-uppercase" style="width: 120px;">Data</th>
                                    <th class="py-3 text-muted small text-uppercase">Veículo</th>
                                    <th class="py-3 text-muted small text-uppercase">Categoria</th>
                                    <th class="py-3 text-muted small text-uppercase">Descrição</th>
                                    <th class="py-3 text-muted small text-uppercase text-right">Odômetro</th>
                                    <th class="py-3 px-4 text-muted small text-uppercase text-right">Valor (R$)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenses as $exp)
                                <tr class="animate__animated animate__fadeInUp animate__faster border-bottom">
                                    <td class="py-3 px-4 align-middle">
                                        <div class="d-flex flex-column text-muted">
                                            <span class="font-weight-bold text-dark" style="font-size: 0.95rem;">{{ $exp->created_at->format('d/m/Y') }}</span>
                                            <small style="font-size: 0.75rem;">{{ $exp->created_at->format('H:i') }}h</small>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-light p-2 rounded mr-3 text-muted">
                                                <i class="fas fa-truck text-teal"></i>
                                            </div>
                                            <div>
                                                <span class="d-block font-weight-bold">{{ $exp->vehicle->plate }}</span>
                                                <small class="text-muted">{{ $exp->vehicle->brand }} / {{ $exp->vehicle->model }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        @php
                                            $badges = [
                                                'Abastecimento' => 'badge-success',
                                                'Troca de Óleo' => 'badge-primary',
                                                'Manutenção' => 'badge-danger',
                                                'Lavagem' => 'badge-info',
                                                'Pneus' => 'badge-dark',
                                                'Outros Gastos' => 'badge-secondary'
                                            ];
                                            $badgeClass = $badges[$exp->type] ?? 'badge-secondary';
                                        @endphp
                                        <span class="badge {{ $badgeClass }} px-3 py-2 shadow-none" style="border-radius: 8px; font-size: 0.75rem;">
                                            {{ strtoupper($exp->type) }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <span class="text-dark font-weight-bold">{{ $exp->description }}</span>
                                        @if($exp->receipt_photo)
                                            <a href="{{ asset('storage/' . $exp->receipt_photo) }}" onclick="event.preventDefault(); viewPhoto(this.href, 'COMPROVANTE: {{ $exp->type }}')" class="ml-2 text-orange small ripple-effect" style="cursor: zoom-in;">
                                                <i class="fas fa-camera"></i> Ver Comprovante
                                            </a>
                                        @endif
                                    </td>
                                    <td class="align-middle text-right">
                                        <span class="font-weight-bold text-muted" style="font-size: 1.1rem;">
                                            {{ number_format($exp->odometer, 0, ',', '.') }}
                                        </span>
                                        <small class="text-muted">KM</small>
                                    </td>
                                    <td class="py-3 px-4 align-middle text-right">
                                        <span class="text-dark font-weight-bold" style="font-size: 1.2rem;">
                                            R$ {{ number_format($exp->amount, 2, ',', '.') }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5">
                                        <div class="py-5">
                                            <i class="fas fa-receipt fa-4x text-muted opacity-25 mb-3"></i>
                                            <p class="text-muted font-italic mb-0">Nenhuma despesa registrada até o momento.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($expenses->hasPages())
                <div class="card-footer bg-white border-0 py-3">
                    <div class="d-flex justify-content-center">
                        {{ $expenses->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .text-teal { color: #20c997 !important; }
    .text-orange { color: #fd7e14 !important; }
    .btn-orange:hover { background-color: #e8590c !important; }
    .ripple-effect { transition: all 0.2s; }
    .ripple-effect:hover { opacity: 0.7; transform: scale(1.1); }
    
    .table-hover tbody tr:hover { background-color: #fffaf5 !important; }
    .opacity-25 { opacity: 0.25; }

    /* CUSTOM LIGHTBOX SWAL - PADRÃO OURO RTECH */
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
     * 📸 VISUALIZADOR DE COMPROVANTES RTECH (PADRÃO OURO)
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
