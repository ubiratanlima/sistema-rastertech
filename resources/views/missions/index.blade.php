@extends('layouts.app')

@section('title', 'Missões em Campo | Rastertech')

@section('content')
<div class="container-fluid">
    
    <!-- 🏗️ CABEÇALHO DA PÁGINA -->
    <div class="row mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark" style="font-size: 2.2rem;">
                <i class="fas fa-route mr-2 text-teal"></i>Missões em Campo
            </h1>
            <p class="text-muted small mb-0 d-none d-sm-block">Torre de Controle: Monitoramento de jornadas, check-ins e checkouts.</p>
        </div>
    </div>

    <!-- 🔍 BARRA DE FILTROS AVANÇADOS -->
    <div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeInDown" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('missions.index') }}" method="GET" class="row row-sm align-items-end">
                <div class="col-md-3">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1">Pesquisa Geral</label>
                    <div class="input-group input-group-sm">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-white border-right-0"><i class="fas fa-search"></i></span>
                        </div>
                        <input type="text" name="search" class="form-control border-left-0" placeholder="Placa, Motorista ou Cliente..." value="{{ $search }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1">Status</label>
                    <select name="status" class="form-control form-control-sm">
                        <option value="">TODOS OS STATUS</option>
                        <option value="open" {{ $status == 'open' ? 'selected' : '' }}>EM CAMPO (ABERTAS)</option>
                        <option value="closed" {{ $status == 'closed' ? 'selected' : '' }}>FINALIZADAS</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1">Cliente</label>
                    <select name="customer_id" class="form-control form-control-sm">
                        <option value="">TODOS CLIENTES</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ $customerId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1">Veículo</label>
                    <select name="vehicle_id" class="form-control form-control-sm">
                        <option value="">TODOS VEÍCULOS</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ $vehicleId == $v->id ? 'selected' : '' }}>{{ $v->plate }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 text-right">
                    <div class="btn-group shadow-sm">
                        <button type="submit" class="btn btn-sm btn-dark px-4 font-weight-bold">
                            FILTRAR
                        </button>
                        <a href="{{ route('missions.index') }}" class="btn btn-sm btn-default border px-3" title="Limpar Filtros">
                            <i class="fas fa-undo"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 📊 LISTAGEM DE MISSÕES -->
    <div class="card card-outline card-teal shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3">
            <h3 class="card-title font-weight-bold mb-0">
                <i class="fas fa-clipboard-list mr-2 text-teal"></i>Jornadas Recentes
            </h3>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02); font-size: 0.85rem;">
                            <th class="sortable text-center" style="width: 120px; cursor: pointer;">Status <i class="fas fa-sort text-muted ml-1"></i></th>
                            <th class="sortable text-center" style="width: 130px; cursor: pointer;">Veículo <i class="fas fa-sort text-muted ml-1"></i></th>
                            <th class="sortable text-left" style="cursor: pointer;">Motorista <i class="fas fa-sort text-muted ml-1"></i></th>
                            <th class="sortable text-left" style="cursor: pointer;">Cliente <i class="fas fa-sort text-muted ml-1"></i></th>
                            <th class="sortable text-center" style="cursor: pointer;">Início (Check-in) <i class="fas fa-sort text-muted ml-1"></i></th>
                            <th class="sortable text-center" style="cursor: pointer;">Fim (Checkout) <i class="fas fa-sort text-muted ml-1"></i></th>
                            <th class="sortable text-center" style="cursor: pointer;">DeslocAMENTO <i class="fas fa-sort text-muted ml-1"></i></th>
                            <th style="width: 100px;">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($missions as $m)
                        <tr class="text-center align-middle" style="height: 70px;">
                            <td class="align-middle">
                                @if($m->status === 'open')
                                    <span class="badge badge-warning px-3 py-1 shadow-sm pulse-warning" style="font-size: 0.75rem; border-radius: 50px;">
                                        EM CAMPO
                                    </span>
                                @else
                                    <span class="badge badge-success px-3 py-1 text-uppercase" style="font-size: 0.75rem; border-radius: 50px;">
                                        FINALIZADO
                                    </span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <div class="mercosul-plate shadow-sm mx-auto">
                                    <div class="mercosul-header">BRASIL</div>
                                    <div class="mercosul-body">{{ $m->vehicle->plate }}</div>
                                </div>
                            </td>
                            <td class="text-left align-middle px-3">
                                <div class="font-weight-bold text-dark">{{ $m->driver->name ?? '---' }}</div>
                                <div class="small text-muted">{{ $m->driver->cpf ?? 'N/A' }}</div>
                            </td>
                            <td class="text-left align-middle px-3">
                                <div class="font-weight-bold text-primary">{{ $m->customer->company_name ?? $m->customer->name ?? '---' }}</div>
                            </td>
                            <td class="align-middle font-weight-bold text-muted">
                                @if($m->entryChecklist)
                                    <div>{{ $m->entryChecklist->created_at->format('d/m/Y') }}</div>
                                    <div class="small badge badge-light border">{{ $m->entryChecklist->created_at->format('H:i') }}</div>
                                @else
                                    <span class="text-muted small">---</span>
                                @endif
                            </td>
                            <td class="align-middle font-weight-bold text-muted">
                                @if($m->exitChecklist)
                                    <div>{{ $m->exitChecklist->created_at->format('d/m/Y') }}</div>
                                    <div class="small badge badge-light border">{{ $m->exitChecklist->created_at->format('H:i') }}</div>
                                @elseif($m->status === 'open')
                                    <span class="text-orange small">ATIVO</span>
                                @else
                                    <span class="text-muted small">BAIXA ADM</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                @if($m->entryChecklist && $m->exitChecklist)
                                    @php $km = $m->exitChecklist->odometer - $m->entryChecklist->odometer; @endphp
                                    <div class="font-weight-bold text-{{ $km > 0 ? 'success' : 'dark' }}">
                                        {{ number_format($km, 0, ',', '.') }} KM
                                    </div>
                                    <div class="small text-muted">
                                        {{ $m->exitChecklist->created_at->diffForHumans($m->entryChecklist->created_at, true) }}
                                    </div>
                                @elseif($m->status === 'open')
                                    <i class="fas fa-spinner fa-spin text-warning opacity-50"></i>
                                @else
                                    <span class="small text-muted opacity-50">N/A</span>
                                @endif
                            </td>
                            <td class="align-middle px-3">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden; border: 1px solid #dee2e6;">
                                    @if($m->entryChecklist)
                                    <a href="/portal/verificacoes/{{ $m->entry_id }}" class="btn btn-light btn-square-sm border-right" title="Ver Checkin">
                                        <i class="fas fa-sign-in-alt text-primary"></i>
                                    </a>
                                    @endif
                                    @if($m->exitChecklist)
                                    <a href="/portal/verificacoes/{{ $m->exit_id }}" class="btn btn-light btn-square-sm" title="Ver Checkout">
                                        <i class="fas fa-sign-out-alt text-danger"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-route fa-3x text-muted mb-3 opacity-20"></i>
                                <h4 class="text-muted font-weight-bold">Nenhuma jornada encontrada</h4>
                                <p class="text-muted">Revise os filtros aplicados ou certifique-se de que há operações em campo.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($missions->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $missions->links() }}
        </div>
        @endif
    </div>
</div>

<style>
    .btn-square-sm { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    
    .pulse-warning {
        box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4);
        animation: pulse-warning-anim 2s infinite;
    }

    @keyframes pulse-warning-anim {
        0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }

    /* 🇧🇷 ESTILO PLACA MERCOSUL (LEGACY LOCAL REMOVED - NOW GLOBAL) */
</style>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;
        const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
            v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? 
            v1 - v2 : v1.toString().localeCompare(v2)
        )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

        document.querySelectorAll('th.sortable').forEach(th => th.addEventListener('click', function() {
            const table = th.closest('table');
            const tbody = table.querySelector('tbody');
            
            // Remove previous icons
            table.querySelectorAll('th i.fa-sort-up, th i.fa-sort-down').forEach(i => i.className = 'fas fa-sort text-muted ml-1');
            
            this.asc = !this.asc;
            th.querySelector('i').className = this.asc ? 'fas fa-sort-up text-teal ml-1' : 'fas fa-sort-down text-teal ml-1';
            
            Array.from(tbody.querySelectorAll('tr'))
                .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc))
                .forEach(tr => tbody.appendChild(tr));
        }));
    });
</script>
@endpush
@endsection
