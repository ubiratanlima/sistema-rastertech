@extends('layouts.app')

@section('title', 'Gestão de Clientes | Rastertech')

@section('content')
<div class="container-fluid">
    
    <!-- 🏗️ CABEÇALHO DA PÁGINA (Padrão Ouro Limpo) -->
    <div class="row mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark" style="font-size: 2.2rem;">
                <i class="fas fa-users-cog mr-2 text-primary"></i>Clientes & Frotas
            </h1>
            <p class="text-muted small mb-0 d-none d-sm-block">Monitoramento e custódia de dados ativos no sistema.</p>
        </div>
    </div>

    <!-- 📊 CARD PRINCIPAL: LISTAGEM INTEGRADA -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title font-weight-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-list-ul mr-2 text-primary"></i>Portfólio de Atendimento
            </h3>
            
            <div class="card-tools ml-auto">
                <form action="/customers" method="GET" class="d-flex align-items-center">
                    <!-- 🔍 PESQUISAR -->
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

                    <!-- ⚙️ SELETOR DE VISÃO -->
                    <div class="ml-4 d-flex align-items-center">
                        <label class="small font-weight-bold text-muted mr-2 mb-0">VISÃO:</label>
                        <select name="view" class="form-control form-control-sm" onchange="this.form.submit()" style="width: 120px; font-weight: bold; border-radius: 6px;">
                            <option value="active" {{ $view == 'active' ? 'selected' : '' }}>🟢 ATIVOS</option>
                            <option value="trash" {{ $view == 'trash' ? 'selected' : '' }}>⛔ INATIVOS</option>
                        </select>
                    </div>

                    <!-- ➕ NOVO CLIENTE -->
                    <button type="button" class="btn btn-sm btn-primary ml-4 px-3 font-weight-bold shadow-sm" onclick="openCreateCustomerModal()" style="border-radius: 6px; height: 31px; display: flex; align-items: center;">
                        <i class="fas fa-plus-circle mr-2"></i> NOVO CLIENTE
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="customerTable">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02); font-size: 0.75rem;">
                            <th style="width: 80px;">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'id', 'direction' => ($sort == 'id' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    ID <i class="fas fa-sort{{ $sort == 'id' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="text-left px-4">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sort == 'name' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    CLIENTE <i class="fas fa-sort{{ $sort == 'name' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th style="width: 160px;">
                                <a href="#" class="text-dark">VEÍCULOS <i class="fas fa-sort opacity-20 ml-1"></i></a>
                            </th>
                            <th style="width: 220px;">PLATAFORMA</th>
                            <th style="width: 180px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <!-- 🏁 LINHA MASTER -->
                        <tr class="accordion-toggle cursor-pointer" data-toggle="collapse" data-target="#row-detail-{{ $customer->id }}" style="transition: background 0.3s; height: 75px;">
                            <td class="align-middle text-center font-weight-bold text-muted small">{{ $customer->id }}</td>
                            <td class="align-middle px-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-box mr-3 d-flex align-items-center justify-content-center text-white font-weight-bold shadow-sm" 
                                         style="width: 40px; height: 40px; border-radius: 10px; background: #1e293b; border: 1px solid rgba(255,255,255,0.1);">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark" style="font-size: 1rem;">{{ $customer->name }}</div>
                                        <div class="small text-muted font-weight-bold text-uppercase opacity-75" style="letter-spacing: 0.5px;">Cód. Segurança: {{ $customer->code ?: 'N/A' }}</div>
                                    </div>
                                    <i class="fas fa-chevron-down ml-auto text-primary opacity-50 accordion-icon"></i>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-light border px-4 py-2 font-weight-bold" style="font-size: 1.1rem; border-radius: 50px;">
                                    <i class="fas fa-car mr-2 text-primary"></i>{{ $customer->vehicles_count }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                @php
                                    $firstDevice = $customer->vehicles->flatMap->devices->first();
                                    $platformName = $firstDevice?->platform?->name ?? 'OPERAÇÃO MANUAL';
                                @endphp
                                <span class="badge badge-light border px-3 py-1 font-weight-normal text-uppercase" 
                                      style="font-size: 1rem; color: #1e293b; border-color: #dee2e6; border-radius: 50px;">
                                    {{ $platformName }}
                                </span>
                            </td>
                            <td class="text-center align-middle px-4">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden; border: 1px solid #dee2e6;">
                                    <button class="btn btn-light btn-square border-right" onclick="viewDossier(this)" 
                                            data-id="{{ $customer->id }}" data-name="{{ $customer->name }}" data-doc="{{ $customer->document }}"
                                            data-email="{{ $customer->email }}" data-code="{{ $customer->code }}" data-vehicles="{{ $customer->vehicles_count }}"
                                            data-platform="{{ $platformName }}" title="Dossiê">
                                        <i class="fas fa-eye fa-lg text-info"></i>
                                    </button>
                                    <button class="btn btn-light btn-square border-right" onclick="editCustomer(this)" 
                                            data-id="{{ $customer->id }}" data-name="{{ $customer->name }}" data-company="{{ $customer->company_name }}"
                                            data-email="{{ $customer->email }}" data-doc="{{ $customer->document }}" data-cell="{{ $customer->cell_phone }}"
                                            data-landline="{{ $customer->landline_phone }}" data-zip="{{ $customer->zip_code }}" data-street="{{ $customer->street }}"
                                            data-number="{{ $customer->number }}" data-neigh="{{ $customer->neighborhood }}" data-city="{{ $customer->city }}"
                                            data-code="{{ $customer->code }}" data-notes="{{ $customer->notes }}" title="Editar">
                                        <i class="fas fa-tools fa-lg text-warning"></i>
                                    </button>
                                    <button class="btn btn-light btn-square" onclick="confirmDelete({{ $customer->id }})" title="Inativar">
                                        <i class="fas fa-power-off fa-lg text-danger"></i>
                                    </button>
                                </div>
                                <form id="form-delete-{{ $customer->id }}" action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                            </td>
                        </tr>

                        <!-- 🛠️ DETALHE ACORDEÃO (MANTER PADRÃO) -->
                        <tr class="detail-row">
                            <td colspan="5" class="p-0 border-0">
                                <div id="row-detail-{{ $customer->id }}" class="collapse" data-parent="#customerTable">
                                    <div class="bg-light shadow-inner">
                                        <div class="table-responsive overflow-hidden mb-0 border-0">
                                            <table class="table table-sm table-striped table-borderless mb-0">
                                                <thead style="background: rgba(0,0,0,0.03); border-bottom: 2px solid #dee2e6;">
                                                    <tr class="text-dark text-uppercase text-center font-weight-bold" style="font-size: 0.95rem; letter-spacing: 2px;">
                                                        <th class="py-3 px-3" style="width: 80px;">ID</th>
                                                        <th class="py-3 text-left">PLACA</th>
                                                        <th class="py-3 text-left px-4">HARDWARE</th>
                                                        <th class="py-3 text-left">IMEI</th>
                                                        <th class="py-3 text-left">SIM</th>
                                                        <th class="py-3" style="width: 140px;">AÇÕES</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @forelse($customer->vehicles as $vehicle)
                                                    <tr class="text-center" style="transition: 0.2s;">
                                                        <td class="align-middle text-muted" style="font-size: 0.95rem;">{{ $vehicle->id }}</td>
                                                        <td class="align-middle text-left px-3">
                                                            <div class="mercosul-plate shadow-sm">
                                                                <div class="mercosul-header">BRASIL</div>
                                                                <div class="mercosul-body">{{ $vehicle->plate }}</div>
                                                            </div>
                                                        </td>
                                                        <td class="align-middle text-left text-dark px-4" style="font-size: 1.15rem;">
                                                            <i class="fas fa-microchip mr-2 text-primary opacity-50"></i>{{ $vehicle->devices->first()?->deviceModel?->name ?? 'OPERAÇÃO MANUAL' }}
                                                        </td>
                                                        <td class="align-middle text-left text-primary px-2" style="font-size: 1.35rem; letter-spacing: -0.5px;">
                                                            <i class="fas fa-barcode mr-2 text-primary opacity-50"></i>{{ $vehicle->devices->first()?->imei ?? '---' }}
                                                        </td>
                                                        <td class="align-middle text-left text-muted px-2" style="font-size: 1.1rem;">
                                                            <i class="fas fa-sim-card mr-2 text-muted opacity-50"></i>{{ $vehicle->devices->first()?->gsmCard?->phone_number ? '+55 ' . $vehicle->devices->first()?->gsmCard?->phone_number : '---' }}
                                                        </td>
                                                        <td class="text-center align-middle p-2">
                                                            <div class="btn-group shadow-sm border bg-white" style="border-radius: 6px; overflow: hidden;">
                                                                <button class="btn btn-light btn-square-sm border-right" title="Dossiê do Ativo" onclick="viewVehicle({{ $vehicle->id }})">
                                                                    <i class="fas fa-eye text-info"></i>
                                                                </button>
                                                                <button class="btn btn-light btn-square-sm border-right" title="Histórico" onclick="viewHistory({{ $vehicle->id }})">
                                                                    <i class="fas fa-history text-warning"></i>
                                                                </button>
                                                                <button class="btn btn-light btn-square-sm" title="Comandos">
                                                                    <i class="fas fa-terminal text-danger"></i>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    @empty
                                                    <tr><td colspan="6" class="text-center py-5 text-muted small"><i class="fas fa-satellite-dish fa-2x mb-2 opacity-20"></i><br>Nenhum ativo vinculado a este cliente.</td></tr>
                                                    @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                        </div>
                                    </div>
                                </div>
                             </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted"><i class="fas fa-users-slash fa-3x mb-3 opacity-30"></i><br>Nenhum cliente encontrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-bottom-0 py-3 d-flex justify-content-center">{{ $customers->links() }}</div>
    </div>
</div>

@include('customers.modals.edit')

@push('scripts')
<script>
    window.openCreateCustomerModal = () => Swal.fire('Novo Registro', 'Em breve: Interface de Cadastro Integrada', 'info');
    
    window.viewDossier = function(el) {
        const d = $(el).data();
        Swal.fire({
            title: `<span style="font-weight: 800;">DOSSIÊ: ${d.name}</span>`,
            width: '600px',
            html: `
                <div class="text-left py-3" style="font-family: Inter, sans-serif;">
                    <div class="row px-3">
                        <div class="col-6 mb-3"><label class="small text-muted mb-1 d-block text-uppercase">EMAIL</label><div class="h6 font-weight-bold">${d.email || 'N/I'}</div></div>
                        <div class="col-6 mb-3"><label class="small text-muted mb-1 d-block text-uppercase">DOC (CPF/CNPJ)</label><div class="h6 font-weight-bold">${d.doc || 'N/I'}</div></div>
                        <div class="col-6 mb-3"><label class="small text-muted mb-1 d-block text-uppercase">CÓDIGO DE SEGURANÇA</label><div class="h6 font-weight-bold text-primary">${d.code || 'N/A'}</div></div>
                        <div class="col-6 mb-3"><label class="small text-muted mb-1 d-block text-uppercase">TOTAL VEÍCULOS</label><div class="h6 font-weight-bold text-indigo"><i class="fas fa-car mr-2"></i>${d.vehicles}</div></div>
                        <div class="col-12 mb-0"><label class="small text-muted mb-1 d-block text-uppercase">PLATAFORMA ATIVA</label><span class="badge badge-light border text-dark px-3 py-1 font-weight-normal" style="font-size: 1rem;">${d.platform}</span></div>
                    </div>
                </div>
            `,
            confirmButtonText: 'FECHAR',
            confirmButtonColor: '#1e293b'
        });
    };

    window.editCustomer = function(el) {
        const d = $(el).data();
        const f = $('#formEditCustomer');
        f.attr('action', `/customers/${d.id}`);
        $('#edit_name').val(d.name);
        $('#edit_company').val(d.company);
        $('#edit_email').val(d.email);
        $('#edit_doc').val(d.doc);
        $('#edit_cell').val(d.cell);
        $('#edit_landline').val(d.landline);
        $('#edit_zip').val(d.zip);
        $('#edit_street').val(d.street);
        $('#edit_number').val(d.number);
        $('#edit_neigh').val(d.neigh);
        $('#edit_city').val(d.city);
        $('#edit_code').val(d.code);
        $('#edit_notes').val(d.notes);
        $('#modalEditCustomer').modal('show');
    };

    window.confirmDelete = function(id) {
        Swal.fire({
            title: 'Você tem certeza?',
            text: "O cliente será removido logicamente da base.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sim, inativar!'
        }).then((res) => { if(res.isConfirmed) document.getElementById(`form-delete-${id}`).submit(); });
    };

    @if(session('success')) Swal.fire({ icon: 'success', title: 'Sucesso', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false }); @endif
    @if(session('error')) Swal.fire({ icon: 'error', title: 'Erro de Operação', text: "{{ session('error') }}" }); @endif
</script>
@endpush

<style>
    .tiny-text { font-size: 0.65rem; }
    .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
    .cursor-pointer { cursor: pointer; }
    .accordion-toggle[aria-expanded="true"] { background: rgba(59, 130, 246, 0.05); border-left: 4px solid #3b82f6; }
    .accordion-toggle[aria-expanded="true"] .accordion-icon { transform: rotate(180deg); }
    .btn-square { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .btn-square-xs { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .btn-square-sm { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    
    /* 🇧🇷 ESTILO PLACA MERCOSUL */
    .mercosul-plate { display: inline-flex; flex-direction: column; background: #fff; border: 1.5px solid #000; border-radius: 4px; overflow: hidden; min-width: 110px; line-height: 1; vertical-align: middle; }
    .mercosul-header { background: #003399; color: #fff; font-size: 0.45rem; text-align: center; padding: 2px 0; font-weight: 800; letter-spacing: 1.5px; border-bottom: 0.5px solid #000; }
    .mercosul-body { color: #000; font-size: 1.2rem; text-align: center; padding: 4px 10px; font-weight: bold; font-family: 'Roboto Mono', monospace; letter-spacing: -1px; }
</style>
@endsection
