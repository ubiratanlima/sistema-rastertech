@extends('layouts.app')

@section('title', 'Gestão de Chips (SIM Cards)')

@section('content')
<div class="container-fluid">
    <!-- 🔔 MOTOR DE ALERTAS PREMIUM (CONTROLADO VIA JS NO FIM DO ARQUIVO) -->

    <!-- ⚓ CABEÇALHO PADRÃO OURO (8:4) -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center overflow-hidden flex-nowrap">
        <div class="col-8 col-sm-6 p-0 p-sm-2">
            <h1 class="m-0 text-bold d-none d-sm-block" style="font-size: 2.2rem;">
                <i class="fas fa-sim-card mr-2 text-primary"></i>Gestão de Chips
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-sim-card mr-1 text-primary"></i>Inventário SIM
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block">Controle de conectividade, estoque e operadoras.</p>
        </div>
        </div>
    </div>

    <!-- 🛠️ TABELA CAMALEÃO -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-list mr-2 text-primary"></i>Conectividade Ativa
            </h3>
            
            <div class="card-tools ml-auto">
                <form action="/sim-cards" method="GET" class="d-flex align-items-center">
                    <!-- 🔍 PESQUISAR POR CLIENTE (PADRÃO ADMINLTE) -->
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Filtrar por Cliente..." value="{{ $search }}">
                        <input type="hidden" name="sort" value="{{ $sort }}">
                        <input type="hidden" name="direction" value="{{ $direction }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default shadow-none border">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ⚙️ SELETOR DE VISÃO (TRI-ESTADO: ATIVO, ESTOQUE, LIXEIRA) -->
                    <div class="ml-5 d-flex align-items-center">
                        <label class="small font-weight-bold text-muted mr-2 mb-0">VISÃO:</label>
                        <select name="view" class="form-control form-control-sm shadow-sm" 
                                onchange="this.form.submit()"
                                style="width: 130px; border-radius: 6px; font-weight: bold; border-color: #dee2e6;">
                            <option value="active" {{ $view == 'active' ? 'selected' : '' }}>🟢 ATIVOS</option>
                            <option value="inventory" {{ $view == 'inventory' ? 'selected' : '' }}>📦 ESTOQUE</option>
                            <option value="canceled" {{ $view == 'canceled' ? 'selected' : '' }}>🚫 CANCELADOS</option>
                            <option value="trash" {{ $view == 'trash' ? 'selected' : '' }}>⛔ INATIVOS</option>
                        </select>
                    </div>

                    <!-- ➕ BOTÃO NOVO CHIP -->
                    <button type="button" 
                            class="btn btn-sm btn-primary ml-3 px-3 font-weight-bold shadow-sm"
                            onclick="openCreateFormManual()"
                            style="border-radius: 6px; height: 31px; display: flex; align-items: center;">
                        <i class="fas fa-plus-circle mr-2"></i> NOVO CHIP
                    </button>

                    @if($search || $view !== 'active')
                        <a href="/sim-cards" class="btn btn-xs btn-outline-danger ml-2" title="Limpar Filtros"><i class="fas fa-times"></i></a>
                    @endif
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02);">
                            <th class="d-none d-md-table-cell">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'id', 'direction' => ($sort == 'id' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark sort-link">
                                    ID <i class="fas fa-sort{{ $sort == 'id' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="d-none d-lg-table-cell text-left px-4">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'iccid', 'direction' => ($sort == 'iccid' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark sort-link">
                                    ICCID <i class="fas fa-sort{{ $sort == 'iccid' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="text-left px-4">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'numero', 'direction' => ($sort == 'numero' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark sort-link">
                                    NÚMERO <i class="fas fa-sort{{ $sort == 'numero' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="d-none d-md-table-cell">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'operator', 'direction' => ($sort == 'operator' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark sort-link">
                                    OPERADORA <i class="fas fa-sort{{ $sort == 'operator' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="d-none d-md-table-cell text-center">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'cliente', 'direction' => ($sort == 'cliente' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark sort-link">
                                    CLIENTE <i class="fas fa-sort{{ $sort == 'cliente' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="d-none d-md-table-cell text-center">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'equipamento', 'direction' => ($sort == 'equipamento' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark sort-link">
                                    EQUIPAMENTO <i class="fas fa-sort{{ $sort == 'equipamento' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="d-md-none text-left px-3">VÍNCULO</th>
                            <th class="d-none d-sm-table-cell">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'status', 'direction' => ($sort == 'status' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark sort-link">
                                    STATUS <i class="fas fa-sort{{ $sort == 'status' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th style="width: 140px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sims as $sim)
                        <tr>
                            <td class="text-center align-middle d-none d-md-table-cell text-muted">{{ $sim->id }}</td>
                            <td class="align-middle d-none d-lg-table-cell px-4">
                                <span class="text-pink">{{ $sim->iccid ?? 'N/A' }}</span>
                            </td>
                            <td class="align-middle px-4">
                                <div class="text-primary">{{ $sim->phone_number ?? '---' }}</div>
                                <div class="d-block d-md-none text-muted">ICCID: {{ \Illuminate\Support\Str::limit($sim->iccid, 8) }}</div>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="badge badge-light border px-2 py-1 text-uppercase font-weight-normal">
                                    {{ $sim->operator }}
                                </span>
                            </td>

                            <!-- 🖥️ VISÃO DESKTOP: VÍNCULOS -->
                            <td class="align-middle d-none d-md-table-cell text-center">
                                {{ $sim->customer_name ?? 'ESTOQUE' }}
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="text-indigo">{{ $sim->rtech_code ?? '---' }}</span>
                            </td>

                            <!-- 📱 VISÃO MOBILE: RESUMIDA -->
                            <td class="align-middle d-md-none">
                                {{ \Illuminate\Support\Str::limit($sim->customer_name ?? 'ESTOQUE', 10) }}
                            </td>

                            <td class="text-center align-middle d-none d-sm-table-cell">
                                @if($sim->trashed())
                                    <span class="badge bg-danger px-3 py-1 shadow-sm">INATIVADO</span>
                                @else
                                    <span class="badge {{ $sim->status === 'active' ? 'bg-success' : 'bg-warning' }} px-3 py-1 shadow-sm">
                                        {{ $sim->status === 'active' ? 'ATIVO' : 'ESTOQUE' }}
                                    </span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <!-- 👁️ RAIO-X (DOSSIÊ TÁTICO) -->
                                    <button class="btn btn-light btn-square border-right" 
                                            type="button"
                                            title="Raio-X (Dossiê)" 
                                            onclick="openTacticalDossier(this)"
                                            data-id="{{ $sim->id }}"
                                            data-iccid="{{ $sim->iccid }}"
                                            data-phone="{{ $sim->phone_number }}"
                                            data-operator="{{ $sim->operator }}"
                                            data-customer="{{ $sim->customer_name }}"
                                            data-code="{{ $sim->rtech_code }}"
                                            data-reason="{{ $sim->cancellation_reason }}"
                                            data-cancelled-at="{{ $sim->cancelled_at ? \Carbon\Carbon::parse($sim->cancelled_at)->format('d/m/Y H:i') : '' }}"
                                            data-status="{{ $sim->status }}">
                                        <i class="fas fa-eye fa-lg text-info"></i>
                                    </button>

                                    <!-- 🛠️ EDITAR (FORMULÁRIO DIRETO) -->
                                    @if($sim->trashed())
                                        <!-- ♻️ RESTAURAR CHIP -->
                                        <form action="/sim-cards/{{ $sim->id }}/restore" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-light btn-square border-left" title="Restaurar Chip">
                                                <i class="fas fa-undo fa-lg text-success"></i>
                                            </button>
                                        </form>
                                    @else
                                        <!-- 🛠️ EDITAR CHIP -->
                                        <button class="btn btn-light btn-square border-right" 
                                                type="button" 
                                                title="Editar Chip" 
                                                onclick="openEditFormManual(this)"
                                                data-id="{{ $sim->id }}"
                                                data-iccid="{{ $sim->iccid }}"
                                                data-phone="{{ $sim->phone_number }}"
                                                data-operator="{{ $sim->operator }}"
                                                data-status="{{ $sim->status }}"
                                                data-customer-id="{{ $sim->customer_id }}"
                                                data-reason="{{ $sim->cancellation_reason }}"
                                                data-cancelled-at="{{ $sim->cancelled_at ? \Carbon\Carbon::parse($sim->cancelled_at)->format('Y-m-d') : '' }}">
                                            <i class="fas fa-tools fa-lg text-warning"></i>
                                        </button>

                                        <!-- ⚡ DESATIVAR (LIXEIRA TÁTICA) -->
                                        <button type="button" class="btn btn-light btn-square" title="Inativar Chip" onclick="confirmDeactivation({{ $sim->id }})">
                                            <i class="fas fa-power-off fa-lg text-danger"></i>
                                        </button>
                                    @endif
                                </div>

                                <form action="{{ route('sim-cards.destroy', $sim->id) }}" method="POST" class="d-none" id="form-delete-{{ $sim->id }}">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-sim-card fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Nenhum chip encontrado</h4>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sims->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
            {{ $sims->links() }}
        </div>
        @endif
    </div>

<style>
    .sort-link { text-decoration: none !important; color: inherit !important; display: block; filter: grayscale(1); transition: 0.2s; }
    .sort-link:hover { filter: grayscale(0); background: rgba(0,0,0,0.03); }
    .btn-square {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    <!-- 📟 MODAL: REGISTRO PREMIUM -->
    <div class="modal fade animate__animated animate__fadeIn" id="modalNovoChip" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-plus-circle mr-2"></i> Registrar Novo Chip
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form action="/sim-cards" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase">Número ICCID (Serial)</label>
                            <input type="text" name="iccid" class="form-control" placeholder="8955..." required style="height: 45px;">
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase">Número da Linha</label>
                            <input type="text" name="phone_number" class="form-control" placeholder="11999998888" style="height: 45px;">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-muted text-uppercase">Operadora</label>
                                    <select name="operator" class="form-control" required style="height: 45px;">
                                        <option value="Vivo">Vivo</option>
                                        <option value="Claro">Claro</option>
                                        <option value="Tim">Tim</option>
                                        <option value="Arqia">Arqia</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-muted text-uppercase">Status</label>
                                    <select name="status" class="form-control" required style="height: 45px;">
                                        <option value="inactive">ESTOQUE</option>
                                        <option value="active">ATIVO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-3">
                        <button type="button" class="btn btn-link text-muted" data-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">SALVAR CHIP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 🏢 MODAL: CAMALEÃO TÁTICO (VIEW / EDIT UNIFICADO) -->
    <div class="modal fade animate__animated animate__fadeIn" id="modalTacticalChip" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <!-- Header Dinâmico -->
                <div id="modalHeader" class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title font-weight-bold" id="modalTitle">
                        <i class="fas fa-sim-card mr-2 text-warning"></i> Dossiê Logístico
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>

                <!-- 🔄 CONTAINER DE TRANSIÇÃO -->
                <div class="modal-body p-0">
                    <!-- 📄 ABA 1: VISUALIZAÇÃO (ILUSTRADA) -->
                    <div id="viewMode" class="p-4 animate__animated animate__fadeIn">
                        <div id="dossierBody">
                            <!-- Preenchido via JS -->
                        </div>
                    </div>

                    <!-- 📝 ABA 2: EDIÇÃO (FORMULÁRIO) -->
                    <div id="editMode" class="p-4 d-none animate__animated animate__fadeIn">
                        <form id="formEditChip" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-muted text-uppercase">ICCID (Serial Hardware)</label>
                                <input type="text" id="edit_iccid" class="form-control bg-light" readonly style="height: 45px; opacity: 0.7;">
                            </div>
                            <div class="form-group mb-3">
                                <label class="font-weight-bold small text-muted text-uppercase">Número da Linha</label>
                                <input type="text" name="phone_number" id="edit_phone" class="form-control" placeholder="11999998888" style="height: 45px;">
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold small text-muted text-uppercase">Operadora</label>
                                        <select name="operator" id="edit_operator" class="form-control" required style="height: 45px;">
                                            <option value="Vivo">Vivo</option>
                                            <option value="Claro">Claro</option>
                                            <option value="Tim">Tim</option>
                                            <option value="Arqia">Arqia</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label class="font-weight-bold small text-muted text-uppercase">Status</label>
                                        <select name="status" id="edit_status" class="form-control" required style="height: 45px;">
                                            <option value="inactive">ESTOQUE</option>
                                            <option value="active">ATIVO</option>
                                            <option value="suspended">SUSPENSO</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Footer Dinâmico -->
                <div class="modal-footer bg-light border-0 p-3">
                    <div id="viewFooter">
                        <button type="button" class="btn btn-dark px-4" data-dismiss="modal">FECHAR</button>
                        <button type="button" class="btn btn-warning px-4 font-weight-bold" onclick="toggleEditMode(true)">
                            <i class="fas fa-edit mr-1"></i>EDITAR
                        </button>
                    </div>
                    <div id="editFooter" class="d-none">
                        <button type="button" class="btn btn-link text-muted" onclick="toggleEditMode(false)">CANCELAR</button>
                        <button type="button" class="btn btn-success px-4 font-weight-bold" onclick="document.getElementById('formEditChip').submit()">
                            <i class="fas fa-save mr-1"></i>SALVAR MUDANÇAS
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* 🎨 DESIGN SYSTEM RASTERTECH (BOTÕES PADRONIZADOS) */
        .btn-rastertech { 
            background: #ffc107; 
            color: #212529; 
            border-radius: 8px; 
            font-weight: 700; 
            transition: all 0.3s ease;
            border: none;
            padding: 8px 18px;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
        .btn-rastertech:hover { 
            background: #e0a800; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,193,7,0.3);
        }

        .text-indigo { color: #6610f2; }

    /* 🎨 DESIGN SYSTEM RASTERTECH (BOTÕES PADRONIZADOS) */
    .btn-rastertech { 
        background: #ffc107; 
        color: #212529; 
        border-radius: 8px; 
        font-weight: 700; 
        transition: all 0.3s ease;
        border: none;
        padding: 8px 18px;
        text-transform: uppercase;
        font-size: 0.75rem;
    }
    .btn-rastertech:hover { 
        background: #e0a800; 
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255,193,7,0.3);
    }

    .text-indigo { color: #6610f2; }

    /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
    .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
    .dark-mode .btn-light { background: #1a1a2e; border-color: #2d2d44; color: #fff; }
    .dark-mode .btn-light:hover { background: #2d2d44; }
    .dark-mode code.text-pink { background: #16213e; color: #ff007f; border: 1px solid #33213e; }
    
    .btn-group .btn { padding: 10px 14px; }
    .animate__animated { --animate-duration: 0.6s; }
</style>

</div> <!-- Fechamento da container-fluid -->
@endsection

@push('scripts')
<script>
    // 🧱 REGISTRO GLOBAL DE CLIENTES (USADO PELO MOTOR DE EDIÇÃO)
    const globalCustomers = {!! json_encode($customers->mapWithKeys(fn($c) => [$c->id => $c->name])) !!};

    /**
     * 🔔 MOTOR DE ALERTAS PREMIUM (Sessões)
     */
    @if(session('success'))
        Swal.fire({ icon: 'success', title: 'SUCESSO!', text: "{{ session('success') }}", timer: 3000, showConfirmButton: false });
    @endif

    @if(session('error'))
        Swal.fire({ 
            html: `
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-5x mb-3" style="color: #ff8c00;"></i>
                    <h2 class="font-weight-bold text-dark mb-2">ATENÇÃO!</h2>
                    <div class="text-dark h6 font-weight-normal px-3">{{ session('error') }}</div>
                </div>
            `,
            confirmButtonColor: '#ffc107',
            confirmButtonText: 'ENTENDI',
            background: '#fff3cd',
            customClass: { confirmButton: 'px-5 py-2 font-weight-bold text-dark border-0 shadow-sm mt-3' }
        });
    @endif

    /**
     * 👁️ DOSSIÊ TÁTICO: VISUALIZAÇÃO
     */
    window.openTacticalDossier = function(el) {
        const btn = $(el);
        const iccid = btn.data('iccid');
        const phone = btn.data('phone') || '---';
        const operator = btn.data('operator');
        const status = btn.data('status');
        const reason = btn.data('reason') || '';
        const cancelledAt = btn.data('cancelled-at') || '';

        let cancellationBox = '';
        if (status === 'canceled') {
            cancellationBox = `
                <div class="mt-4 p-3 rounded" style="background: rgba(220, 53, 69, 0.08); border: 1px solid #dc3545;">
                    <label class="small text-danger mb-1 d-block font-weight-bold text-uppercase">
                        <i class="fas fa-ban mr-1"></i> MOTIVO DO CANCELAMENTO
                    </label>
                    <div style="max-height: 120px; overflow-y: auto; font-size: 0.95rem; color: #721c24; scrollbar-width: thin;">
                        ${reason || 'Nenhum motivo registrado.'}
                    </div>
                    <div class="mt-2 text-right">
                        <small class="text-muted font-italic">Auditado em: ${cancelledAt}</small>
                    </div>
                </div>
            `;
        }

        Swal.fire({
            title: '<i class="fas fa-sim-card mr-2 text-warning"></i> DOSSIÊ LOGÍSTICO',
            width: '500px',
            html: `
                <div class="text-left" style="font-family: 'Source Sans Pro', sans-serif;">
                    <div class="mb-3 p-3 bg-light rounded shadow-sm border-left" style="border-left: 4px solid #ffc107 !important;">
                        <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">SERIAL ICCID</label>
                        <div class="h5 font-weight-bold text-dark mb-0">${iccid}</div>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">NÚMERO LINHA</label>
                            <div class="font-weight-bold text-dark h6">${phone}</div>
                        </div>
                        <div class="col-6 mb-3 text-right">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">OPERADORA</label>
                            <span class="badge badge-warning px-3 py-2" style="font-size: 0.9rem;">${operator}</span>
                        </div>
                    </div>
                    ${cancellationBox}
                </div>
            `,
            showCancelButton: false,
            confirmButtonText: 'FECHAR',
            confirmButtonColor: '#343a40'
        });
    };

    /**
     * 🛠️ EDIÇÃO MANUAL: FORMULÁRIO RÁPIDO
     */
    window.openEditFormManual = function(el) {
        const btn = $(el);
        const id = btn.data('id');
        const iccid = btn.data('iccid');
        const phone = btn.data('phone') || '';
        const operator = btn.data('operator');
        const status = btn.data('status');
        const currentCustomerId = btn.data('customer-id');
        const dbReason = btn.data('reason') || '';
        const dbDate = btn.data('cancelled-at') || '';
        const cacheKey = `sim_cancel_draft_${id}`;
        const draftReason = localStorage.getItem(cacheKey) || dbReason;

        // 🏢 GERAÇÃO DINÂMICA DO SELETOR DE CLIENTES
        let customerOptions = '<option value="">--- SEM CLIENTE (ESTOQUE) ---</option>';
        Object.entries(globalCustomers).forEach(([cid, cname]) => {
            customerOptions += `<option value="${cid}" ${cid == currentCustomerId ? 'selected' : ''}>${cname}</option>`;
        });

        Swal.fire({
            title: '<i class="fas fa-sim-card mr-2 text-warning"></i> CHIP / SIMCARD',
            didOpen: () => {
                const statusSelect = Swal.getPopup().querySelector('#edit_status_swal');
                const reasonContainer = Swal.getPopup().querySelector('#audit_cancellation_fields');
                const reasonTextarea = Swal.getPopup().querySelector('#edit_reason_swal');
                
                statusSelect.addEventListener('change', (e) => {
                    reasonContainer.style.display = (e.target.value === 'canceled') ? 'block' : 'none';
                    if (e.target.value === 'canceled' && !$('#edit_date_swal').val()) {
                        $('#edit_date_swal').val(new Date().toISOString().split('T')[0]);
                    }
                });
                
                // 💾 PERSISTÊNCIA EM TEMPO REAL (DRAFT)
                reasonTextarea.addEventListener('input', (e) => {
                    localStorage.setItem(cacheKey, e.target.value);
                });
            },
            html: `
                <div class="text-left px-2" style="font-family: 'Source Sans Pro', sans-serif;">
                    <div class="form-group mb-3 text-center p-2 bg-light rounded shadow-sm border">
                        <label class="small text-muted mb-0 d-block font-weight-bold text-uppercase">SERIAL ICCID</label>
                        <code class="h6 font-weight-bold text-primary">${iccid}</code>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-muted text-uppercase"><i class="fas fa-user-tie mr-1"></i> CLIENTE RESPONSÁVEL</label>
                        <select id="edit_customer_swal" class="form-control" style="border-radius: 8px; height: 45px;">
                            ${customerOptions}
                        </select>
                    </div>

                    <div class="form-group mb-3">
                        <label class="font-weight-bold small text-muted text-uppercase">LINHA</label>
                        <input type="text" id="edit_phone_swal" class="form-control" value="${phone}" style="border-radius: 8px; height: 45px;">
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label class="font-weight-bold small text-muted text-uppercase">OPERADORA</label>
                            <select id="edit_operator_swal" class="form-control" style="border-radius: 8px; height: 45px;">
                                <option value="Vivo" ${operator === 'Vivo' ? 'selected' : ''}>Vivo</option>
                                <option value="Claro" ${operator === 'Claro' ? 'selected' : ''}>Claro</option>
                                <option value="Tim" ${operator === 'Tim' ? 'selected' : ''}>Tim</option>
                                <option value="Arqia" ${operator === 'Arqia' ? 'selected' : ''}>Arqia</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="font-weight-bold small text-muted text-uppercase">STATUS</label>
                            <select id="edit_status_swal" class="form-control font-weight-bold" style="border-radius: 8px; height: 45px;">
                                <option value="active" ${status === 'active' ? 'selected' : ''} class="text-success">🟢 ATIVO</option>
                                <option value="inactive" ${status === 'inactive' ? 'selected' : ''} class="text-warning">📦 ESTOQUE</option>
                                <option value="canceled" ${status === 'canceled' ? 'selected' : ''} class="text-danger">🚫 CANCELADO</option>
                            </select>
                        </div>
                    </div>
                    <div id="audit_cancellation_fields" class="mt-3 animate__animated animate__fadeIn" style="display: ${status === 'canceled' || draftReason ? 'block' : 'none'}">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-danger text-uppercase"><i class="fas fa-calendar-alt mr-1"></i> Data do Cancelamento</label>
                            <input type="date" id="edit_date_swal" class="form-control" value="${dbDate || (status === 'canceled' || draftReason ? new Date().toISOString().split('T')[0] : '')}" style="border-radius: 8px; height: 45px; border: 1px solid #dc3545;">
                            <p class="small text-muted mt-1">Deixe em branco para usar a data de hoje.</p>
                        </div>
                        <label class="font-weight-bold small text-danger text-uppercase"><i class="fas fa-ban mr-1"></i> Motivo do Cancelamento</label>
                        <textarea id="edit_reason_swal" class="form-control" rows="3" placeholder="Descreva o motivo para auditoria..." style="border-radius: 8px; border: 1px dashed #dc3545; background: #fff8f8;">${draftReason}</textarea>
                        <p class="small text-muted mt-1"><i class="fas fa-info-circle mr-1"></i> O motivo é obrigatório para cancelamentos.</p>
                    </div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'SALVAR',
            confirmButtonColor: '#28a745',
            preConfirm: () => {
                const newStatus = $('#edit_status_swal').val();
                const reason = $('#edit_reason_swal').val();
                const cancelDate = $('#edit_date_swal').val();
                const customerId = $('#edit_customer_swal').val();

                if (newStatus === 'canceled' && (!reason || reason.trim().length < 5)) {
                    Swal.showValidationMessage('Por favor, descreva um motivo válido (min. 5 caract.).');
                    return false;
                }

                return $.ajax({
                    url: `/sim-cards/${id}`,
                    method: 'PUT',
                    data: {
                        phone_number: $('#edit_phone_swal').val(),
                        operator: $('#edit_operator_swal').val(),
                        status: newStatus,
                        customer_id: customerId,
                        cancellation_reason: reason,
                        cancelled_at: cancelDate,
                        _token: '{{ csrf_token() }}'
                    }
                }).then(() => {
                    // Limpa o rascunho após sucesso
                    localStorage.removeItem(cacheKey);
                }).catch(error => {
                    Swal.showValidationMessage(error.responseJSON?.message || 'Erro ao sincronizar dados.');
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ icon: 'success', title: 'SINCRONIZADO', timer: 1500, showConfirmButton: false }).then(() => location.reload());
            }
        });
    };

    /**
     * ⚡ INATIVAÇÃO SEGURA
     */
    window.confirmDeactivation = function(id) {
        Swal.fire({
            title: 'Desativar Chip?',
            text: "O Chip será movido para a Lixeira Tática.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sim, inativar!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/sim-cards/${id}`,
                    method: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' }
                }).then(() => {
                    Swal.fire({ icon: 'success', title: 'INATIVADO', showConfirmButton: false, timer: 1500 }).then(() => location.reload());
                }).catch(err => {
                    Swal.fire({
                        title: `
                            <div class="mb-3"><i class="fas fa-exclamation-triangle" style="color: #f39c12; font-size: 5.5rem;"></i></div>
                            <span style="color: #f39c12; font-weight: 800; font-size: 1.8rem;">AÇÃO PROIBIDA</span>`,
                        html: `<div class="mt-2" style="font-size: 1.1rem; color: #555;">${err.responseJSON?.message || 'Operação bloqueada pelo sistema.'}</div>`,
                        confirmButtonText: 'ENTENDI',
                        confirmButtonColor: '#f39c12'
                    });
                });
            }
        });
    };

    /**
     * ➕ CADASTRO DE NOVO CHIP: ENGINE MULTI-ROWS (MODO TÁTICO)
     */
    window.openCreateFormManual = function() {
        const cache = JSON.parse(localStorage.getItem('sim_bulk_cache') || '{"rows": [{}], "massive": false}');
        const providers = {!! $providers->toJson() !!};
        
        const renderGSMRow = (index, data = {}) => `
            <div class="gsm-row mb-3 p-3 border rounded shadow-sm bg-light animate__animated animate__fadeInSmall" data-index="${index}">
                <div class="row align-items-center mb-2">
                    <div class="col-md-5">
                        <label class="font-weight-bold small text-muted text-uppercase mb-1">SERIAL ICCID</label>
                        <input type="text" class="form-control iccid-field" value="${data.iccid || ''}" placeholder="8955..." style="height: 40px; border-radius: 8px;">
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold small text-muted text-uppercase mb-1">LINHA</label>
                        <input type="text" class="form-control phone-field" value="${data.phone_number || ''}" placeholder="+55..." style="height: 40px; border-radius: 8px;">
                    </div>
                    <div class="col-md-3">
                        <label class="font-weight-bold small text-muted text-uppercase mb-1">STATUS</label>
                        <select class="form-control status-field" style="height: 40px; border-radius: 8px;">
                            <option value="active" ${data.status === 'active' ? 'selected' : ''}>ATIVO</option>
                            <option value="inactive" ${data.status === 'inactive' || !data.status ? 'selected' : ''}>ESTOQUE</option>
                            <option value="suspended" ${data.status === 'suspended' ? 'selected' : ''}>SUSPENSO</option>
                        </select>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-3 px-1">
                        <label style="font-size: 0.65rem; color: #a0aec0;">PIN</label>
                        <input type="text" class="form-control pin-field form-control-sm" value="${data.pin || ''}" style="border-radius: 6px;">
                    </div>
                    <div class="col-3 px-1">
                        <label style="font-size: 0.65rem; color: #a0aec0;">PUK</label>
                        <input type="text" class="form-control puk-field form-control-sm" value="${data.puk || ''}" style="border-radius: 6px;">
                    </div>
                    <div class="col-3 px-1">
                        <label style="font-size: 0.65rem; color: #a0aec0;">PIN 2</label>
                        <input type="text" class="form-control pin2-field form-control-sm" value="${data.pin2 || ''}" style="border-radius: 6px;">
                    </div>
                    <div class="col-3 px-1">
                        <label style="font-size: 0.65rem; color: #a0aec0;">PUK 2</label>
                        <input type="text" class="form-control puk2-field form-control-sm" value="${data.puk2 || ''}" style="border-radius: 6px;">
                    </div>
                </div>

                <div class="text-right mt-2 action-container">
                    <button type="button" class="btn btn-xs btn-outline-danger remove-row-btn" style="border-radius: 50%;" title="Remover"><i class="fas fa-trash"></i></button>
                    <button type="button" class="btn btn-xs btn-outline-primary add-row-btn ml-2" style="border-radius: 50%; width: 30px; height: 30px;" title="Adicionar"><i class="fas fa-plus"></i></button>
                </div>
            </div>
        `;

        Swal.fire({
            title: '<i class="fas fa-sim-card mr-2 text-primary"></i> CADASTRO DE CHIPS GSM',
            width: '850px',
            html: `
                <div class="text-left" style="font-family: 'Source Sans Pro', sans-serif;">
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="font-weight-bold small text-muted text-uppercase">FORNECEDOR / PARCEIRO</label>
                            <div class="input-group">
                                <select id="bulk_provider" class="form-control" style="height: 45px; border-radius: 8px 0 0 8px;">
                                    <option value="">Selecione...</option>
                                    ${providers.map(p => `<option value="${p.id}" ${cache.provider_id == p.id ? 'selected' : ''}>${p.name}</option>`).join('')}
                                </select>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-primary" type="button" onclick="createNewProviderPopup()"><i class="fas fa-plus"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 d-flex align-items-end justify-content-end">
                            <button type="button" id="toggle_bulk_mode" class="btn btn-sm ${cache.massive ? 'btn-primary' : 'btn-outline-primary'} px-4 font-weight-bold" style="height: 45px; border-radius: 8px;">
                                MODO: ${cache.massive ? 'MASSIVO' : 'SINGLE'}
                            </button>
                        </div>
                    </div>

                    <div class="row mb-4 p-3 rounded" style="background: rgba(0,123,255,0.05); border: 1px solid rgba(0,123,255,0.1);">
                        <div class="col-3">
                            <label class="font-weight-bold small text-muted">OPERADORA</label>
                            <input type="text" id="bulk_operator" class="form-control form-control-sm" value="${cache.operator || ''}" placeholder="Vivo/Claro...">
                        </div>
                        <div class="col-3">
                            <label class="font-weight-bold small text-muted">APN</label>
                            <input type="text" id="bulk_apn" class="form-control form-control-sm" value="${cache.apn || ''}">
                        </div>
                        <div class="col-3">
                            <label class="font-weight-bold small text-muted">USUÁRIO APN</label>
                            <input type="text" id="bulk_user" class="form-control form-control-sm" value="${cache.apn_user || ''}">
                        </div>
                        <div class="col-3">
                            <label class="font-weight-bold small text-muted">SENHA APN</label>
                            <input type="text" id="bulk_pass" class="form-control form-control-sm" value="${cache.apn_pass || ''}">
                        </div>
                    </div>

                    <div id="gsm_rows_container"></div>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'SALVAR REGISTROS',
            cancelButtonText: 'CANCELAR',
            confirmButtonColor: '#28a745',
            didOpen: () => {
                const container = $('#gsm_rows_container');
                const updateUI = () => {
                    const rows = container.find('.gsm-row');
                    rows.each(function(i) {
                        $(this).find('.add-row-btn').toggle(i === rows.length - 1 && cache.massive);
                        $(this).find('.remove-row-btn').toggle(rows.length > 1);
                    });
                    const currentData = {
                        massive: cache.massive,
                        provider_id: $('#bulk_provider').val(),
                        operator: $('#bulk_operator').val(),
                        apn: $('#bulk_apn').val(),
                        apn_user: $('#bulk_user').val(),
                        apn_pass: $('#bulk_pass').val(),
                        rows: []
                    };
                    container.find('.gsm-row').each(function() {
                        currentData.rows.push({
                            iccid: $(this).find('.iccid-field').val(),
                            phone_number: $(this).find('.phone-field').val(),
                            status: $(this).find('.status-field').val(),
                            pin: $(this).find('.pin-field').val(),
                            puk: $(this).find('.puk-field').val(),
                            pin2: $(this).find('.pin2-field').val(),
                            puk2: $(this).find('.puk2-field').val()
                        });
                    });
                    localStorage.setItem('sim_bulk_cache', JSON.stringify(currentData));
                };

                if (cache.rows && cache.rows.length > 0) {
                    cache.rows.forEach((r, i) => container.append(renderGSMRow(i, r)));
                } else {
                    container.append(renderGSMRow(0));
                }

                $('#toggle_bulk_mode').click(function() {
                    cache.massive = !cache.massive;
                    $(this).toggleClass('btn-primary btn-outline-primary').text(`MODO: ${cache.massive ? 'MASSIVO' : 'SINGLE'}`);
                    updateUI();
                });

                container.on('click', '.add-row-btn', function() {
                    container.append(renderGSMRow(container.find('.gsm-row').length));
                    updateUI();
                });

                container.on('click', '.remove-row-btn', function() {
                    $(this).closest('.gsm-row').remove();
                    updateUI();
                });

                container.on('change input', 'input, select', updateUI);
                updateUI();
            },
            preConfirm: () => {
                const data = JSON.parse(localStorage.getItem('sim_bulk_cache'));
                if (data.rows.some(r => !r.iccid)) return Swal.showValidationMessage('Todos os ICCIDs são obrigatórios.');
                
                return $.ajax({
                    url: '/sim-cards',
                    method: 'POST',
                    data: { ...data, _token: '{{ csrf_token() }}' },
                    dataType: 'json'
                }).then(res => {
                    localStorage.removeItem('sim_bulk_cache');
                    return res;
                }).catch(err => {
                    Swal.showValidationMessage(`🔴 ERRO: ${err.responseJSON?.message || 'Falha ao salvar'}`);
                });
            }
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({ icon: 'success', title: 'CHIPS REGISTRADOS', timer: 2000, showConfirmButton: false }).then(() => location.reload());
            }
        });
    };

    /**
     * 🏢 POPUP DE NOVO FORNECEDOR
     */
    window.createNewProviderPopup = function() {
        Swal.fire({
            title: 'NOVO FORNECEDOR',
            html: `
                <div class="text-left">
                    <label>NOME</label>
                    <input type="text" id="prov_name" class="form-control mb-3">
                    <label>TIPO</label>
                    <select id="prov_type" class="form-control">
                        <option value="connectivity">CONECTIVIDADE</option>
                    </select>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'SALVAR',
            preConfirm: () => {
                const name = $('#prov_name').val();
                if (!name) return Swal.showValidationMessage('Nome obrigatório');
                return $.ajax({
                    url: '/providers',
                    method: 'POST',
                    data: { name, type: $('#prov_type').val(), _token: '{{ csrf_token() }}' }
                });
            }
        }).then(res => {
            if (res.isConfirmed) {
                Swal.fire({ icon: 'success', title: 'SALVO', timer: 1000, showConfirmButton: false }).then(() => location.reload());
            } else {
                openCreateFormManual();
            }
        });
    };
</script>
@endpush
