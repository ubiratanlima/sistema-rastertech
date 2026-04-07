@extends('layouts.app')

@section('title', 'Filas de Homologação | Rastertech Admin')

@section('content')
<div class="container-fluid pb-5">
    <!-- 🏗️ CABEÇALHO EXECUTIVO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn">
        <div class="col-12 p-0 d-flex justify-content-between align-items-end flex-wrap">
            <div>
                <h1 class="m-0 text-bold" style="font-size: 2.2rem;">
                    <i class="fas fa-microchip mr-2 text-primary"></i>Validação de Sinais
                </h1>
                <p class="text-muted mb-0 font-weight-bold uppercase small"><i class="fas fa-stream mr-1"></i> Auditoria de Hardware e Homologação Técnica de Campo</p>
            </div>
            
            <!-- 🔍 FILTROS TÁTICOS -->
            <div class="mt-3 mt-md-0 d-flex flex-wrap">
                <form action="{{ route('admin.installations.index') }}" method="GET" class="d-flex flex-wrap align-items-center">
                    <select name="status" class="form-control form-control-sm mr-2 mb-2" style="width: 180px; border-radius: 8px;">
                        <option value="">Status: TODOS</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>PENDENTE</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>APROVADO</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>REJEITADO</option>
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
                                    <th class="py-3 px-4 text-muted small text-uppercase" style="width: 140px;">Data de Obra</th>
                                    <th class="py-3 text-muted small text-uppercase">Ativo / Cliente</th>
                                    <th class="py-3 text-muted small text-uppercase">Instalador</th>
                                    <th class="py-3 text-muted small text-uppercase">Estado do Sinal</th>
                                    <th class="py-3 text-muted small text-uppercase">Validador</th>
                                    <th class="py-3 px-4 text-muted small text-uppercase text-right">Ação</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($installations as $inst)
                                <tr class="animate__animated animate__fadeInUp animate__faster border-bottom">
                                    <td class="py-3 px-4 align-middle">
                                        <div class="d-flex flex-column">
                                            <span class="text-bold text-dark">{{ $inst->created_at->format('d/m/Y') }}</span>
                                            <small class="text-muted">{{ $inst->created_at->format('H:i') }}h</small>
                                        </div>
                                    </td>
                                    <td class="align-middle text-left">
                                        <div class="d-flex align-items-center">
                                            <div class="p-2 mr-3 rounded" style="background: rgba(0,0,0,0.05);">
                                                <i class="fas fa-car-side text-muted"></i>
                                            </div>
                                            <div>
                                                <span class="d-block font-weight-bold text-uppercase">{{ $inst->vehicle_plate }}</span>
                                                <small class="text-muted d-block text-truncate" style="max-width: 250px;">{{ $inst->customer_name }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $inst->installer->image ?? asset('img/user-default.png') }}" class="rounded-circle mr-2" width="30" height="30" style="object-fit: cover; border: 1px solid #ddd;">
                                            <span class="font-weight-bold small text-dark">{{ $inst->installer->name }}</span>
                                        </div>
                                    </td>
                                    <td class="align-middle">
                                        @if($inst->validation_status == 'pending')
                                            <span class="badge badge-warning px-3 py-1 font-weight-bold" style="border-radius: 20px;">
                                                <i class="fas fa-clock mr-1"></i> AGUARDANDO CENTRAL
                                            </span>
                                        @elseif($inst->validation_status == 'approved')
                                            <span class="badge badge-success px-3 py-1 font-weight-bold" style="border-radius: 20px;">
                                                <i class="fas fa-check-circle mr-1"></i> SINAL OK
                                            </span>
                                        @else
                                            <span class="badge badge-danger px-3 py-1 font-weight-bold" style="border-radius: 20px;">
                                                <i class="fas fa-times-circle mr-1"></i> REJEITADO
                                            </span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @if($inst->validator)
                                            <div class="d-flex flex-column align-items-center">
                                                <span class="font-weight-bold text-dark small">{{ $inst->validator->name }}</span>
                                                <small class="text-muted" style="font-size: 0.65rem;">{{ $inst->validated_at->format('d/m/Y H:i') }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted small">--</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 align-middle text-right">
                                        <a href="{{ route('admin.installations.show', $inst->id) }}" class="btn btn-outline-primary btn-sm px-3 font-weight-bold" style="border-radius: 8px;">
                                            <i class="fas fa-search-plus mr-1"></i> HOMOLOGAR
                                        </a>
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
@endsection
