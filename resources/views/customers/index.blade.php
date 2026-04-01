@extends('layouts.app')

@section('title', 'Gestão de Clientes | Rastertech')

@section('content')
<div class="container-fluid">
    
    <!-- 🏗️ CABEÇALHO DA PÁGINA -->
    <div class="row mb-4 animate__animated animate__fadeIn">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark">
                <i class="fas fa-users-cog mr-2 text-primary"></i>Clientes & Frotas
            </h1>
            <p class="text-muted small mb-0">Monitoramento e custódia de ativos ativos no sistema.</p>
        </div>
        <div class="col-sm-6 text-right">
            <button type="button" class="btn btn-primary px-4 shadow-sm font-weight-bold" onclick="openCreateCustomerModal()" style="border-radius: 8px;">
                <i class="fas fa-plus-circle mr-2"></i>NOVO CLIENTE
            </button>
        </div>
    </div>

    <!-- 📊 CARD PRINCIPAL: LISTAGEM -->
    <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-white border-0 py-3 d-flex align-items-center">
            <h3 class="card-title font-weight-bold mb-0"><i class="fas fa-list-ul mr-2 text-primary"></i>Portfólio de Atendimento</h3>
            <div class="card-tools ml-auto">
                <form action="/customers" method="GET" class="d-flex align-items-center">
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Localizar registro..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary shadow-none"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="customerTable">
                    <thead class="bg-light">
                        <tr class="text-muted text-uppercase small font-weight-bold">
                            <th class="py-3 px-4" style="width: 80px;">ID</th>
                            <th class="py-3">DADOS DO CLIENTE</th>
                            <th class="py-3 text-center">VEÍCULOS</th>
                            <th class="py-3 text-center">PLATAFORMA</th>
                            <th class="py-3 text-center" style="width: 200px;">AÇÕES OPERACIONAIS</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <!-- 🏁 LINHA MASTER -->
                        <tr class="accordion-toggle cursor-pointer" data-toggle="collapse" data-target="#row-detail-{{ $customer->id }}" style="transition: background 0.3s;">
                            <td class="align-middle px-4 font-weight-bold text-muted">{{ $customer->id }}</td>
                            <td class="align-middle">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-box mr-3 d-flex align-items-center justify-content-center text-white font-weight-bold" 
                                         style="width: 40px; height: 40px; border-radius: 10px; background: #1e293b;">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark">{{ $customer->name }}</div>
                                        <div class="small text-muted">{{ $customer->company_name ?? $customer->email }}</div>
                                    </div>
                                    <i class="fas fa-chevron-down ml-auto text-primary opacity-50"></i>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge badge-light border px-3 py-1" style="font-size: 0.9rem;">
                                    <i class="fas fa-car mr-2 text-primary"></i>{{ $customer->vehicles_count }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                @php
                                    $firstDevice = $customer->vehicles->flatMap->devices->first();
                                    $platformName = $firstDevice?->platform?->name ?? 'OPERAÇÃO MANUAL';
                                @endphp
                                <span class="badge px-3 py-1 font-weight-bold" style="border: 1px solid #6610f2; color: #6610f2; font-size: 0.7rem; background: #f8f0ff;">
                                    {{ $platformName }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-light border bg-white" 
                                            onclick="viewDossier(this)" 
                                            data-id="{{ $customer->id }}"
                                            data-name="{{ $customer->name }}"
                                            data-email="{{ $customer->email }}"
                                            data-doc="{{ $customer->document }}"
                                            data-code="{{ $customer->code }}"
                                            data-vehicles="{{ $customer->vehicles_count }}"
                                            data-platform="{{ $platformName }}"
                                            title="Ver Dossiê">
                                        <i class="fas fa-eye text-info"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border bg-white" 
                                            onclick="editCustomer(this)" 
                                            data-id="{{ $customer->id }}"
                                            data-name="{{ $customer->name }}"
                                            data-company="{{ $customer->company_name }}"
                                            data-email="{{ $customer->email }}"
                                            data-doc="{{ $customer->document }}"
                                            data-cell="{{ $customer->cell_phone }}"
                                            data-landline="{{ $customer->landline_phone }}"
                                            data-zip="{{ $customer->zip_code }}"
                                            data-street="{{ $customer->street }}"
                                            data-number="{{ $customer->number }}"
                                            data-neigh="{{ $customer->neighborhood }}"
                                            data-city="{{ $customer->city }}"
                                            data-code="{{ $customer->code }}"
                                            data-notes="{{ $customer->notes }}"
                                            title="Editar Dados">
                                        <i class="fas fa-tools text-warning"></i>
                                    </button>
                                    <button class="btn btn-sm btn-light border bg-white" onclick="confirmDelete({{ $customer->id }})" title="Inativar">
                                        <i class="fas fa-power-off text-danger"></i>
                                    </button>
                                </div>
                                <form id="form-delete-{{ $customer->id }}" action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                            </td>
                        </tr>

                        <!-- 🛠️ LINHA DETALHE (ACORDEÃO) -->
                        <tr class="detail-row">
                            <td colspan="5" class="p-0 border-0">
                                <div id="row-detail-{{ $customer->id }}" class="collapse" data-parent="#customerTable">
                                    <div class="px-5 py-4 bg-light shadow-inner">
                                        <div class="card border-0 shadow-sm mb-0">
                                            <div class="card-body p-0">
                                                <table class="table table-sm table-borderless mb-0">
                                                    <thead>
                                                        <tr class="bg-dark text-white tiny-text uppercase" style="letter-spacing: 1px;">
                                                            <th class="pl-4 py-2">ID Ativo</th>
                                                            <th class="py-2">Placa</th>
                                                            <th class="py-2">Hardware</th>
                                                            <th class="py-2">IMEI</th>
                                                            <th class="py-2">ICCID / SIM</th>
                                                            <th class="text-right pr-4 py-2">Gerenciar</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($customer->vehicles as $vehicle)
                                                        <tr class="border-bottom bg-white">
                                                            <td class="pl-4 align-middle text-muted small font-weight-bold">{{ $vehicle->id }}</td>
                                                            <td class="align-middle"><span class="badge badge-dark px-2 py-1" style="font-family: monospace;">{{ $vehicle->plate }}</span></td>
                                                            <td class="align-middle small">{{ $vehicle->devices->first()?->deviceModel?->name ?? 'N/A' }}</td>
                                                            <td class="align-middle text-primary small font-weight-bold">{{ $vehicle->devices->first()?->imei ?? '---' }}</td>
                                                            <td class="align-middle small text-muted">{{ $vehicle->devices->first()?->gsmCard?->phone_number ?? '---' }}</td>
                                                            <td class="text-right pr-4 align-middle">
                                                                <button class="btn btn-xs btn-outline-info px-3" onclick="viewVehicle({{ $vehicle->id }})">DETALHES</button>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="6" class="text-center py-4 text-muted small"><i class="fas fa-ghost mr-2"></i>Nenhum veículo vinculado.</td>
                                                        </tr>
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
        <div class="card-footer bg-white border-0 py-3">{{ $customers->links() }}</div>
    </div>
</div>

<!-- 📦 MODAIS -->
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
                        <div class="col-6 mb-3"><label class="small text-muted mb-1 d-block text-uppercase">CÓDIGO DE SEGURANÇA</label><div class="h6 font-weight-bold">${d.code || 'N/A'}</div></div>
                        <div class="col-6 mb-3"><label class="small text-muted mb-1 d-block text-uppercase">TOTAL VEÍCULOS</label><div class="h6 font-weight-bold text-primary"><i class="fas fa-car mr-2"></i>${d.vehicles}</div></div>
                        <div class="col-12 mb-0"><label class="small text-muted mb-1 d-block text-uppercase">PLATAFORMA</label><span class="badge bg-indigo text-white px-3 py-1">${d.platform}</span></div>
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
</script>

<script>
    @if(session('success')) Swal.fire({ icon: 'success', title: 'Sucesso', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false }); @endif
    @if(session('error')) Swal.fire({ icon: 'error', title: 'Erro de Operação', text: "{{ session('error') }}" }); @endif
</script>
@endpush

<style>
    .tiny-text { font-size: 0.65rem; }
    .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
    .cursor-pointer { cursor: pointer; }
    .accordion-toggle[aria-expanded="true"] { background: rgba(59, 130, 246, 0.05); border-left: 4px solid #3b82f6; }
</style>
@endsection
