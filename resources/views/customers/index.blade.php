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
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02); font-size: 1rem;">
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
                        <tr style="transition: background 0.3s; height: 75px;">
                            <td class="align-middle text-center font-weight-bold text-muted small accordion-toggle cursor-pointer" data-toggle="collapse" data-target="#row-detail-{{ $customer->id }}">{{ $customer->id }}</td>
                            <td class="align-middle px-4 accordion-toggle cursor-pointer" data-toggle="collapse" data-target="#row-detail-{{ $customer->id }}">
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
                            <td class="text-center align-middle accordion-toggle cursor-pointer" data-toggle="collapse" data-target="#row-detail-{{ $customer->id }}">
                                <span class="badge badge-light border px-4 py-2 font-weight-bold" style="font-size: 1.1rem; border-radius: 50px;">
                                    <i class="fas fa-car mr-2 text-primary"></i>{{ $customer->vehicles_count }}
                                </span>
                            </td>
                            <td class="text-center align-middle accordion-toggle cursor-pointer" data-toggle="collapse" data-target="#row-detail-{{ $customer->id }}">
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
                                            data-id="{{ $customer->id }}" data-name="{{ $customer->name }}" title="Dossiê do Cliente">
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
                                    <div class="shadow-inner">
                                        <div class="table-responsive overflow-hidden mb-0 border-0">
                                            <table class="table table-sm table-borderless table-zebra mb-0">
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
                                                    <tr class="text-center">
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
                                                                <button class="btn btn-light btn-square-sm border-right" title="Rastreador" onclick="viewDevice(this)" 
                                                                    data-has-device="{{ $vehicle->devices->count() > 0 ? 'true' : 'false' }}"
                                                                    data-internal-code="{{ $vehicle->devices->first()?->internal_code ?? '' }}"
                                                                    data-model="{{ $vehicle->devices->first()?->deviceModel?->name ?? '' }}"
                                                                    data-imei="{{ $vehicle->devices->first()?->imei ?? '' }}"
                                                                    data-sim="{{ $vehicle->devices->first()?->gsmCard?->phone_number ? '+55 ' . $vehicle->devices->first()?->gsmCard?->phone_number : '---' }}"
                                                                    data-vehicle-plate="{{ $vehicle->plate }}"
                                                                    data-vehicle-customer="{{ $vehicle->customer->name ?? '' }}"
                                                                    data-updated="{{ $vehicle->devices->first()?->updated_at?->format('d/m/Y H:i') ?? '' }}"
                                                                    data-status="{{ $vehicle->devices->first()?->status ?? '' }}"
                                                                    data-reason="{{ $vehicle->devices->first()?->cancellation_reason ?? '' }}"
                                                                    data-cancelled-at="{{ $vehicle->devices->first()?->cancelled_at ? \Carbon\Carbon::parse($vehicle->devices->first()?->cancelled_at)->format('d/m/Y H:i') : '' }}"
                                                                >
                                                                    <i class="fas fa-eye text-info"></i>
                                                                </button>
                                                                <button class="btn btn-light btn-square-sm border-right" title="Gestão de Hardware" onclick="openCustomerDeviceEdit(this)"
                                                                    data-id="{{ $vehicle->devices->first()?->id ?? 0 }}"
                                                                    data-has-device="{{ $vehicle->devices->count() > 0 ? 'true' : 'false' }}"
                                                                    data-internal-code="{{ $vehicle->devices->first()?->internal_code ?? '' }}"
                                                                    data-model="{{ $vehicle->devices->first()?->deviceModel?->name ?? '' }}"
                                                                    data-imei="{{ $vehicle->devices->first()?->imei ?? '' }}"
                                                                    data-sim="{{ $vehicle->devices->first()?->gsmCard?->phone_number ? '+55 ' . $vehicle->devices->first()?->gsmCard?->phone_number : '---' }}"
                                                                    data-sim-operator="{{ $vehicle->devices->first()?->gsmCard?->provider?->name ?? '---' }}"
                                                                    data-vehicle-plate="{{ $vehicle->plate }}"
                                                                    data-vehicle-customer="{{ $vehicle->customer->company_name ?? $vehicle->customer->name ?? '' }}"
                                                                    data-status="{{ $vehicle->devices->first()?->status ?? '' }}"
                                                                    data-customer-id="{{ $vehicle->customer_id }}"
                                                                    data-vehicle-id="{{ $vehicle->id }}"
                                                                >
                                                                    <i class="fas fa-tools text-warning"></i>
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
@include('customers.modals.create')

@push('scripts')
<script>
    window.openCreateCustomerModal = () => $('#modalCreateCustomer').modal('show');
    
    window.viewDossier = function(el) {
        const id = $(el).data('id');
        const name = $(el).data('name');

        Swal.fire({
            title: `<div class="d-flex align-items-center justify-content-center" style="font-weight: 800; font-size: 1.5rem; letter-spacing: -0.5px;"><i class="fas fa-user-tie mr-3 text-primary"></i> Cliente: ${name}</div>`,
            width: '900px',
            html: `<div class="p-5 text-center"><i class="fas fa-circle-notch fa-spin fa-2x text-primary opacity-50"></i><br><span class="small text-muted mt-2 d-block">Carregando Dossiê Operacional...</span></div>`,
            showConfirmButton: false,
            didOpen: () => {
                $.get(`/customers/${id}/dossier`, function(d) {
                    let subUsersHtml = d.sub_users && d.sub_users.length ? 
                        d.sub_users.map(u => `
                            <div class="p-3 border rounded shadow-xs mb-3 bg-white">
                                <div class="row align-items-center">
                                    <div class="col-1"><i class="fas fa-user-shield text-info fa-lg opacity-50"></i></div>
                                    <div class="col-4 border-right"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">Administrativo</label><div class="small font-weight-bold text-dark">${u.name}</div></div>
                                    <div class="col-4 border-right"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">Usuário / Senha</label><div class="small text-muted">${u.external_username} / <span class="text-primary">${u.external_password}</span></div></div>
                                    <div class="col-3"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">WhatsApp</label><div class="small font-weight-bold text-success"><i class="fab fa-whatsapp"></i> ---</div></div>
                                </div>
                            </div>
                        `).join('') : '<div class="text-center py-4 text-muted border rounded bg-light small">Nenhum sub-cliente cadastrado.</div>';

                    let driversHtml = d.drivers && d.drivers.length ? 
                        d.drivers.map(u => `
                            <div class="p-3 border rounded shadow-xs mb-3 bg-white border-left-indicator" style="border-left: 4px solid #3b82f6 !important;">
                                <div class="row align-items-center">
                                    <div class="col-1"><i class="fas fa-steering-wheel text-primary fa-lg opacity-50"></i></div>
                                    <div class="col-3 border-right"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">Motorista</label><div class="small font-weight-bold text-dark">${u.name}</div></div>
                                    <div class="col-3 border-right"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">CNH / Vencimento</label><div class="small font-weight-bold text-danger">${u.cnh_number} / ${new Date(u.cnh_expiry).toLocaleDateString('pt-BR')}</div></div>
                                    <div class="col-3 border-right"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">Usuário / Senha</label><div class="small text-muted">${u.cpf} / <span class="text-primary">123456</span></div></div>
                                    <div class="col-2"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">Status</label><span class="badge badge-success px-2 py-1">ATIVO</span></div>
                                </div>
                            </div>
                        `).join('') : '<div class="text-center py-4 text-muted border rounded bg-light small">Nenhum motorista cadastrado.</div>';

                    Swal.update({
                        html: `
                            <div class="text-left py-2" style="font-family: 'Inter', sans-serif;">
                                <ul class="nav nav-pills mb-4 d-flex justify-content-center p-1 bg-light rounded" style="border: 1px solid #dee2e6;">
                                    <li class="nav-item flex-fill"><a class="nav-link active font-weight-bold text-center py-2" data-toggle="pill" href="#tab-cadastro"><i class="fas fa-id-card mr-2"></i>CADASTRO</a></li>
                                    <li class="nav-item flex-fill"><a class="nav-link font-weight-bold text-center py-2" data-toggle="pill" href="#tab-equipe"><i class="fas fa-users mr-2"></i>EQUIPE E ACESSOS</a></li>
                                </ul>

                                <div class="tab-content" style="max-height: 550px; overflow-y: auto; overflow-x: hidden; padding: 5px;">
                                    
                                    <!-- TAB: CADASTRO -->
                                    <div class="tab-pane fade show active" id="tab-cadastro">
                                        <div class="mb-4">
                                            <div class="small text-primary font-weight-bold mb-2 text-uppercase" style="letter-spacing: 1px;"><i class="fas fa-building mr-2"></i>Identidade Cadastral</div>
                                            <div class="row bg-light rounded p-3 mb-0 mx-0 border shadow-xs">
                                                <div class="col-8 border-right"><label class="tiny-text text-muted mb-1 d-block text-uppercase font-weight-bold">Razão Social / Nome Fantasia</label><div class="h6 font-weight-bold text-dark">${d.company_name || 'Pessoa Física / Não Informado'}</div></div>
                                                <div class="col-4 pl-3"><label class="tiny-text text-muted mb-1 d-block text-uppercase font-weight-bold">CPF/CNPJ</label><div class="h6 font-weight-bold text-dark text-truncate">${d.document || 'N/I'}</div></div>
                                            </div>
                                        </div>

                                        <div class="mb-4">
                                            <div class="small text-primary font-weight-bold mb-2 text-uppercase" style="letter-spacing: 1px;"><i class="fas fa-id-badge mr-2"></i>Contatos</div>
                                            <div class="row mx-0">
                                                <div class="col-5 pl-0 pr-2"><div class="p-3 border rounded shadow-xs h-100 bg-white d-flex align-items-center justify-content-center"><div class="small font-weight-bold text-dark text-center">${(d.email || 'N/I').split(',').join('<br>')}</div></div></div>
                                                <div class="col-4 px-2">
                                                    <div class="p-3 border rounded shadow-xs h-100 bg-white d-flex flex-column justify-content-center align-items-center">
                                                        <label class="tiny-text text-muted mb-1 d-block text-uppercase font-weight-bold">WhatsApp</label>
                                                        <a href="https://api.whatsapp.com/send/?phone=55${(d.cell_phone || '').replace(/\D/g, '')}" target="_blank" class="h6 font-weight-bold text-dark text-decoration-none hover-opacity-75 transition-all">
                                                            <i class="fab fa-whatsapp text-success mr-1"></i> ${d.cell_phone || '---'}
                                                        </a>
                                                    </div>
                                                </div>
                                                <div class="col-3 pr-0 pl-2"><div class="p-3 border border-primary rounded h-100 d-flex flex-column justify-content-center align-items-center" style="background: rgba(0,123,255,0.02);"><label class="tiny-text text-primary mb-1 d-block text-uppercase font-weight-bold">Segurança</label><div class="h5 font-weight-bold text-primary mb-0">${d.code || 'N/A'}</div></div></div>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <div class="small text-primary font-weight-bold mb-2 text-uppercase" style="letter-spacing: 1px;"><i class="fas fa-map-marked-alt mr-2"></i>Endereço</div>
                                            <div class="p-3 border rounded shadow-xs bg-light">
                                                <div class="row px-1">
                                                    <div class="col-3 border-right p-0 pr-3"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">CEP</label><div class="small font-weight-bold">${d.zip_code || '---'}</div></div>
                                                    <div class="col-6 border-right px-3"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">Logradouro</label><div class="small font-weight-bold">${d.street || '---'}, ${d.number || 'S/N'}</div></div>
                                                    <div class="col-3 pl-3 p-0"><label class="tiny-text text-muted d-block text-uppercase font-weight-bold">Cidade</label><div class="small font-weight-bold">${d.city || '---'}</div></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- TAB: EQUIPE -->
                                    <div class="tab-pane fade" id="tab-equipe">
                                        <div class="mb-4">
                                            <div class="small text-info font-weight-bold mb-3 text-uppercase border-bottom pb-1" style="letter-spacing: 1px;"><i class="fas fa-user-shield mr-2"></i>Equipe Administrativa (Sub-clientes)</div>
                                            ${subUsersHtml}
                                        </div>
                                        <div>
                                            <div class="small text-primary font-weight-bold mb-3 text-uppercase border-bottom pb-1" style="letter-spacing: 1px;"><i class="fas fa-truck-moving mr-2"></i>Corpo de Motoristas & Operadores</div>
                                            ${driversHtml}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: '<i class="fas fa-times-circle mr-2"></i>FECHAR HUB DO CLIENTE',
                        confirmButtonColor: '#1e293b'
                    });
                });
            }
        });
    };

    window.editCustomer = function(el) {
        const d = $(el).data();
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
        
        $('#formEditCustomer').attr('action', `/customers/${d.id}`);
        
        // Forçar reset para a primeira aba de dados
        $('#editTabs a[id="tab-data-link"]').tab('show');
        
        $('#modalEditCustomer').modal('show');
    };

    // 👥 GESTÃO DE EQUIPE (RASTERTECH GOLD STANDARD)
    window.loadTeamMembers = () => {
        const id = $('#formEditCustomer').attr('action').split('/').pop();
        const containerOps = $('#list-operators');
        const containerDrvs = $('#list-drivers');
        
        containerOps.html('<div class="col-12 text-center py-3 text-muted small"><i class="fas fa-sync fa-spin mr-2"></i>Carregando Operadores...</div>');
        containerDrvs.html('<div class="col-12 text-center py-3 text-muted small"><i class="fas fa-sync fa-spin mr-2"></i>Carregando Motoristas...</div>');

        $.get(`/customers/${id}/dossier`, function(d) {
            containerOps.empty();
            containerDrvs.empty();

            if (d.sub_users.length === 0) containerOps.html('<div class="col-12 text-center py-4 text-muted border rounded-lg border-dashed small">Nenhum operador administrativo.</div>');
            if (d.drivers.length === 0) containerDrvs.html('<div class="col-12 text-center py-4 text-muted border rounded-lg border-dashed small">Nenhum motorista cadastrado.</div>');

            // Renderização Fiel - Sub-Clientes (Operadores)
            d.sub_users.forEach(u => injectTeamCard('operator', u, containerOps));
            // Renderização Fiel - Motoristas
            d.drivers.forEach(u => injectTeamCard('driver', u, containerDrvs));
        });
    };

    window.showNewMemberForm = () => {
        Swal.fire({
            title: 'Novo Integrante',
            text: "Escolha o perfil para iniciar o cadastro:",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3b82f6',
            cancelButtonColor: '#10b981',
            confirmButtonText: '<i class="fas fa-truck-moving mr-2"></i>MOTORISTA',
            cancelButtonText: '<i class="fas fa-user-tie mr-2"></i>OPERADOR'
        }).then((result) => {
            const role = result.isConfirmed ? 'driver' : (result.dismiss === Swal.DismissReason.cancel ? 'operator' : null);
            if (role) injectTeamCard(role, null, role === 'driver' ? $('#list-drivers') : $('#list-operators'), true);
        });
    };

    const injectTeamCard = (role, data = null, container, isNew = false) => {
        const id = data ? data.id : Date.now();
        const isDriver = role === 'driver';
        const isDeactivated = data && data.deleted_at !== null;
        
        if (isNew) container.find('.border-dashed').remove();

        const card = `
            <div class="col-12 mb-4 animate__animated animate__fadeInUp">
                <div class="card border shadow-xs transition-all" style="border-radius: 15px; border-left: 5px solid ${isDeactivated ? '#9ca3af' : (isDriver ? '#3b82f6' : '#10b981')} !important; ${isDeactivated ? 'background-color: #f3f4f6;' : ''}">
                    <div class="card-body p-4" style="${isDeactivated ? 'opacity: 0.75;' : ''}">
                        <div class="row align-items-center mb-3">
                            <div class="col-md-6 text-left">
                                <span class="badge ${isDeactivated ? 'badge-secondary' : (isDriver ? 'badge-primary' : 'badge-success')} text-uppercase py-1 px-3" style="font-size: 0.7rem; border-radius: 20px;">
                                    <i class="fas ${isDriver ? 'fa-steering-wheel' : 'fa-user-shield'} mr-2"></i>${isDriver ? 'Motorista' : 'Operador'} ${isDeactivated ? ' | DESATIVADO' : ''}
                                </span>
                            </div>
                            <div class="col-md-6 text-right">
                                ${isNew ? '<button class="btn btn-outline-danger btn-xs font-weight-bold px-3 py-1" onclick="$(this).closest(\'.col-12\').remove()"><i class="fas fa-times-circle mr-1"></i>CANCELAR CADASTRO</button>' : ''}
                            </div>
                        </div>

                        <div class="row mb-3" style="${isDeactivated ? 'pointer-events: none; filter: grayscale(1);' : ''}">
                            <div class="col-md-5 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Nome Completo</label>
                                <input type="text" class="form-control gold-input" value="${data ? data.name : ''}" placeholder="Nome Completo">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Usuário do Sistema</label>
                                <input type="text" class="form-control gold-input" value="${data ? (data.external_username || data.document) : ''}" placeholder="Login">
                            </div>
                            <div class="col-md-4 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Senha (Mín. 8 caracteres)</label>
                                <div class="input-group">
                                    <input type="password" class="form-control gold-input border-right-0" id="pass-${id}" placeholder="Defina a senha">
                                    <div class="input-group-append">
                                        <button class="btn border bg-white border-left-0 px-3" type="button" onclick="togglePassVisibility('${id}')" style="border-radius: 0 10px 10px 0;">
                                            <i class="fas fa-eye-slash text-muted" id="eye-${id}"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3" style="${isDeactivated ? 'pointer-events: none; filter: grayscale(1);' : ''}">
                            <div class="col-md-3 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">${isDriver ? 'Registro da CNH' : 'Registro do CPF'}</label>
                                <input type="text" class="form-control gold-input" value="${data ? (data.cnh_number || data.document) : ''}" placeholder="Somente números">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Email de Contato</label>
                                <input type="email" class="form-control gold-input" value="${data ? (data.email || '') : ''}" placeholder="email@exemplo.com">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">WhatsApp</label>
                                <input type="text" class="form-control gold-input" value="${data ? (data.whatsapp || '') : ''}" placeholder="(00) 00000-0000">
                            </div>
                            <div class="col-md-3 form-group">
                                <label class="tiny-text text-muted text-uppercase font-weight-bold mb-1">Anexo Documento</label>
                                <div class="input-group">
                                    <button type="button" class="btn btn-outline-secondary btn-block gold-input d-flex align-items-center justify-content-center" onclick="$('#file-${id}').click()">
                                        <i class="fas fa-camera mr-2"></i> ${data && data.photo_path ? 'DOC. ANEXADO' : 'FOTO OU PDF'}
                                    </button>
                                    <input type="file" id="file-${id}" class="d-none">
                                </div>
                            </div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-md-12 text-right">
                                <div class="btn-group shadow-sm" style="height: 48px;">
                                    <button type="button" class="btn btn-primary font-weight-bold px-5" style="border-radius: 10px 0 0 10px;" onclick="updateMember(${id}, '${role}')" ${isDeactivated ? 'disabled' : ''}>
                                        <i class="fas fa-check-circle mr-2"></i>SALVAR ${isDriver ? 'MOTORISTA' : 'OPERADOR'}
                                    </button>
                                    <button type="button" class="btn btn-light border px-3" style="border-radius: 0 10px 10px 0;" ${data ? 'onclick="toggleMemberStatus(' + data.id + ', \'' + role + '\')"' : 'disabled'}>
                                        <i class="fas fa-power-off ${isDeactivated ? 'text-success' : 'text-danger'}"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
        
        isNew ? container.prepend(card) : container.append(card);
    };

    window.togglePassVisibility = (id) => {
        const input = $(`#pass-${id}`);
        const icon = $(`#eye-${id}`);
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('fa-eye-slash').addClass('fa-eye text-primary');
        } else {
            input.attr('type', 'password');
            icon.removeClass('fa-eye').addClass('fa-eye-slash text-muted');
        }
    };

    window.updateMember = (id, role) => {
        const customerId = $('#formEditCustomer').attr('action').split('/').pop();
        const card = $(`#file-${id}`).closest('.card');
        
        const data = {
            _token: '{{ csrf_token() }}',
            role: role,
            name: card.find('input[placeholder="Nome Completo"]').val(),
            username: card.find('input[placeholder="Login"]').val(),
            password: card.find(`#pass-${id}`).val(),
            document: card.find('input[placeholder="Somente números"]').val(),
            email: card.find('input[type="email"]').val(),
            whatsapp: card.find('input[placeholder="(00) 00000-0000"]').val()
        };

        if (data.password.length > 0 && data.password.length < 8) {
            Swal.fire({ icon: 'error', title: 'Segurança de Senha', text: 'A senha deve ter no mínimo 8 caracteres.' });
            return;
        }

        Swal.fire({ title: 'Salvando no Servidor...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        $.post(`/customers/${customerId}/members`, data, function(res) {
            Swal.fire({ 
                icon: 'success', 
                title: 'Gravação Realizada', 
                text: res.message, 
                timer: 2000, 
                showConfirmButton: false 
            });
            loadTeamMembers();
        }).fail(function(err) {
            Swal.fire({ icon: 'error', title: 'Erro de Sincronização', text: 'Verifique os dados e tente novamente.' });
        });
    };

    window.toggleMemberStatus = (memberId, role) => {
        const customerId = $('#formEditCustomer').attr('action').split('/').pop();
        Swal.fire({
            title: 'Alterar Status?',
            text: 'Deseja mudar a disponibilidade deste acesso?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            confirmButtonText: 'Sim, Executar!'
        }).then((res) => {
            if(res.isConfirmed) {
                $.ajax({
                    url: `/customers/${customerId}/members/${memberId}/toggle`,
                    method: 'PUT',
                    data: { _token: '{{ csrf_token() }}', role: role },
                    success: function(resp) {
                        Swal.fire({ icon: 'success', title: 'Atualizado', text: resp.message, timer: 1500 });
                        loadTeamMembers();
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: 'Erro', text: 'Não foi possível alterar o status do integrante.'});
                    }
                });
            }
        });
    };
    // 🌎 BUSCA GLOBAL DE CEP (RASTERTECH STANDARD)
    $(document).on('blur', '.cep-lookup', function() {
        const $input = $(this);
        const prefix = $input.data('prefix') || '';
        const cep = $input.val().replace(/\D/g, '');
        
        if (cep.length === 8) {
            $input.addClass('is-loading').prop('readonly', true);
            $.getJSON(`https://viacep.com.br/ws/${cep}/json/`, function(d) {
                $input.removeClass('is-loading').prop('readonly', false);
                if (!("erro" in d)) {
                    $(`#${prefix}street`).val(d.logradouro).addClass('animate__animated animate__fadeIn');
                    $(`#${prefix}neigh`).val(d.bairro).addClass('animate__animated animate__fadeIn');
                    $(`#${prefix}city`).val(d.localidade).addClass('animate__animated animate__fadeIn');
                    $(`#${prefix}number`).focus();
                } else {
                    Swal.fire({ icon: 'warning', title: 'CEP não encontrado', text: 'Verifique o número e tente novamente.', timer: 2000, showConfirmButton: false });
                }
            }).fail(function() {
                $input.removeClass('is-loading').prop('readonly', false);
            });
        }
    });

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

    window.viewDevice = (el) => {
        const data = $(el).data();
        if(data.hasDevice === false) {
            Swal.fire({
                icon: 'info',
                title: 'Nenhum Ativo',
                text: 'Este veículo não possui rastreador vinculado no momento.',
                confirmButtonColor: '#17a2b8'
            });
            return;
        }
        
        let cancellationBox = '';
        if (data.status === 'canceled') {
            cancellationBox = `
                <div class="mt-4 p-3 rounded" style="background: rgba(220, 53, 69, 0.08); border: 1px solid #dc3545;">
                    <label class="small text-danger mb-1 d-block font-weight-bold text-uppercase"><i class="fas fa-ban mr-1"></i> MOTIVO DO CANCELAMENTO</label>
                    <div style="max-height: 120px; overflow-y: auto; font-size: 0.95rem; color: #721c24;">${data.reason || 'Não informado.'}</div>
                    <div class="mt-2 text-right text-muted small">Auditado em: ${data.cancelledAt || '--'}</div>
                </div>`;
        }

        Swal.fire({
            title: '<i class="fas fa-microchip mr-2 text-warning"></i> DOSSIÊ DE HARDWARE',
            width: '600px',
            confirmButtonText: 'FECHAR',
            confirmButtonColor: '#6c757d',
            html: `
                <div class="text-left px-2" style="font-family: 'Source Sans Pro', sans-serif;">
                    <!-- 💎 CORE GRID (HARDWARE + SIM) -->
                    <div class="row no-gutters mb-3">
                        <div class="col-7 pr-2">
                            <label class="small text-muted mb-2 d-block font-weight-bold text-uppercase">Rastreador em USO</label>
                            <div class="rounded shadow-sm px-3 d-flex flex-column justify-content-center align-items-center position-relative" style="background: #1e293b; height: 120px; border-radius: 15px !important; border: 2px solid #334155;">
                                <div class="text-uppercase mb-1 text-center" style="color: #94a3b8; font-size: 1.35rem; font-weight: 900; letter-spacing: 1px; width: 100%;">
                                    ${data.internalCode}
                                </div>
                                <div class="text-uppercase mb-2 text-center" style="color: rgba(148, 163, 184, 0.4); font-size: 0.55rem; font-weight: 700; letter-spacing: 2px; width: 100%;">
                                    ${data.model} CORE / V1.0
                                </div>
                                <div class="d-flex align-items-center justify-content-center w-100">
                                    <i class="fas fa-microchip mr-2" style="color: #94a3b8; font-size: 1.5rem;"></i>
                                    <span style="color: #94a3b8; font-size: 1rem; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">${data.imei}</span>
                                </div>
                                <div style="position: absolute; bottom: 8px; right: 15px; color: #475569; font-size: 0.6rem; font-weight: 900; letter-spacing: 1px; opacity: 0.6;">ANATEL</div>
                            </div>
                        </div>
                        <div class="col-5 pl-1">
                            <label class="small text-muted mb-2 d-block font-weight-bold text-uppercase">Conectividade</label>
                            <div class="p-2 rounded d-flex flex-column align-items-center justify-content-center text-center" style="border: 1px dashed #cbd5e1; border-radius: 15px !important; height: 120px; background: #fff;">
                                <i class="fas fa-sim-card mb-2 text-success" style="font-size: 2.7rem; opacity: 0.85;"></i>
                                <div class="h6 font-weight-bold text-pink mb-1" style="letter-spacing: -0.5px; font-size: 1rem;">${data.sim || '---'}</div>
                                <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 0.7rem;">Link Ativo</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 📟 ATIVOS VINCULADOS (VEÍCULO) -->
                    <div class="p-3 border rounded shadow-sm position-relative animate__animated animate__fadeIn" style="border-radius: 12px !important; border-left: 5px solid #17a2b8 !important; min-height: 100px; background: #f8fafc;">
                        <div class="row align-items-center">
                            <div class="col-5 d-flex align-items-center justify-content-center">
                                ${data.vehiclePlate !== 'NÃO POSSUI' ? `
                                    <div class="mercosul-plate shadow-none">
                                        <div class="mercosul-header">BRASIL</div>
                                        <div class="mercosul-body" style="font-size: 1.3rem;">${data.vehiclePlate}</div>
                                    </div>
                                ` : `
                                    <div class="mercosul-plate" style="opacity: 0.3; filter: grayscale(1); border-style: dashed;">
                                        <div class="mercosul-header">BRASIL</div>
                                        <div class="mercosul-body" style="font-size: 1.2rem;">000-0000</div>
                                    </div>
                                `}
                            </div>
                            <div class="col-7 text-center">
                                <label class="small font-weight-bold text-info text-uppercase mb-1"><i class="fas fa-link mr-1"></i>Vínculo de Operação</label>
                                <div class="small text-muted font-weight-bold d-block">PROPRIEDADE: ${data.vehicleCustomer || 'ESTOQUE'}</div>
                                <div class="small text-muted" style="font-size: 0.65rem;">Sincronizado: ${data.updated}</div>
                            </div>
                        </div>
                    </div>

                    ${cancellationBox}
                </div>`
        });
    };

    window.openCustomerDeviceEdit = function(el) {
        const $btn = $(el);
        const data = $btn.data();
        
        if (String(data.hasDevice) === 'false' || !data.hasDevice) {
            Swal.fire({
                title: '<i class="fas fa-microchip mr-2 text-warning"></i> INSERIR HARDWARE',
                width: '500px',
                html: '<div class="text-left">' +
                        '<div class="text-center mb-5">' +
                            '<div class="font-weight-bold small text-muted text-uppercase mb-3">VEÍCULO SELECIONADO</div>' +
                            '<div class="mercosul-plate shadow-none mx-auto" style="transform: scale(1.5); transform-origin: center; margin-bottom: 20px;">' +
                                '<div class="mercosul-header">BRASIL</div>' +
                                '<div class="mercosul-body">' + (data.vehiclePlate || '---') + '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="form-group">' +
                            '<label class="font-weight-bold small text-muted text-uppercase mb-1"><i class="fas fa-satellite-dish text-primary mr-1"></i> Rastreador Disponível no Estoque</label>' +
                            '<select id="new_device_id" class="form-control font-weight-bold" style="border-radius: 8px;">' +
                                '<option value="">--- SELECIONE UM ATIVO ---</option>' +
                                '@foreach($freeDevices as $fd)' +
                                    '<option value="{{$fd->id}}" data-code="{{$fd->internal_code}}" data-imei="{{$fd->imei}}">{{$fd->internal_code}} &bull; IMEI: {{$fd->imei}}</option>' +
                                '@endforeach' +
                            '</select>' +
                        '</div>' +
                        '<div class="form-group mb-0">' +
                            '<label class="font-weight-bold small text-muted text-uppercase mb-1"><i class="fas fa-sim-card text-success mr-1"></i> Linha de Dados (Opcional)</label>' +
                            '<select id="new_sim_id" class="form-control font-weight-bold text-success" style="border-radius: 8px;">' +
                                '<option value="">--- INSERIR SEM CHIP ---</option>' +
                                '@foreach($freeSims as $fs)' +
                                    '<option value="{{$fs->id}}">ICCID: {{$fs->iccid}} / +55 {{$fs->phone_number}}</option>' +
                                '@endforeach' +
                            '</select>' +
                            '<small class="text-muted d-block mt-2"><i class="fas fa-info-circle mr-1"></i> Apenas equipamentos e chips não vinculados a veículos aparecem aqui.</small>' +
                        '</div>' +
                    '</div>',
                showCancelButton: true,
                cancelButtonText: 'CANCELAR',
                confirmButtonText: 'VINCULAR AO VEÍCULO',
                confirmButtonColor: '#007bff',
                preConfirm: () => {
                    const devId = $('#new_device_id').val();
                    if (!devId) {
                        Swal.showValidationMessage('Você precisa selecionar um Rastreador.');
                        return false;
                    }
                    const opt = $('#new_device_id option:selected');
                    return $.ajax({
                        url: '/devices/' + devId,
                        method: 'PUT',
                        data: {
                            new_vehicle_id: data.vehicleId,
                            new_gsm_card_id: $('#new_sim_id').val() || null,
                            internal_code: opt.data('code'),
                            imei: opt.data('imei'),
                            status: 'active',
                            customer_id: data.customerId,
                            unlink_vehicle: 0,
                            unlink_chip: 0,
                            _token: '{{ csrf_token() }}'
                        }
                    }).catch(error => Swal.showValidationMessage(error.responseJSON?.message || 'Erro ao sincronizar.'));
                }
            }).then((result) => result.isConfirmed && location.reload());
            return;
        }

        const id = data.id;
        let chipActionBox = '';
        if (data.sim !== '---') {
            chipActionBox = `
                <div id="chip_active_section" class="animate__animated animate__fadeIn h-100 d-flex flex-column align-items-center justify-content-center p-2 rounded shadow-none" style="border: 1px dashed #cbd5e1; border-radius: 15px !important; width: 100%; height: 145px;">
                    <div class="small text-muted font-weight-bold text-uppercase mb-2" style="font-size: 1rem;">CHIP ATIVO | ${data.simOperator}</div>
                    <i class="fas fa-sim-card mb-1 text-success" style="font-size: 2.2rem; opacity: 1;"></i>
                    <div class="h6 font-weight-bold text-pink mb-1" style="letter-spacing: -0.5px; font-size: 0.85rem;">${data.sim}</div>
                    <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold mt-1 px-3 py-1 shadow-sm" style="border-radius: 6px; border-width: 2px;" 
                            onclick="document.getElementById('unlink_chip_hidden').value='1'; this.closest('#chip_active_section').style.opacity='0.4'; this.innerHTML='⏳ AGUARDANDO SALVAR'; this.disabled=true;">
                        💔 DESVINCULAR
                    </button>
                    <input type="hidden" id="unlink_chip_hidden" value="0">
                </div>`;
        } else {
            chipActionBox = `<div class="d-flex align-items-center justify-content-center h-100 text-muted small font-weight-bold">SEM CHIP</div>`;
        }

        let vehicleBox = `
            <div class="row bg-light p-3 border rounded shadow-sm position-relative animate__animated animate__fadeIn mb-3" id="unlink_section" style="border-radius: 12px !important; border-left: 5px solid #17a2b8 !important; min-height: 110px;">
                <div class="col-5 d-flex align-items-center justify-content-center">
                    <div class="mercosul-plate shadow-none">
                        <div class="mercosul-header">BRASIL</div>
                        <div class="mercosul-body" style="font-size: 1.3rem;">${data.vehiclePlate}</div>
                    </div>
                </div>
                <div class="col-7 d-flex flex-column align-items-center justify-content-center text-center">
                    <div class="small font-weight-bold text-info text-uppercase mb-1"><i class="fas fa-link mr-1"></i>Vínculo Ativo</div>
                    <div class="small text-muted font-weight-bold text-truncate d-block mb-2" style="max-width: 200px;">FROTA: ${data.vehicleCustomer}</div>
                    
                    <div class="w-100 mt-1">
                         <button type="button" class="btn btn-xs btn-outline-danger font-weight-bold px-3 py-1 shadow-sm" style="border-radius: 6px; border-width: 2px;" onclick="$('#unlink_form_device').fadeIn(); $(this).fadeOut();">
                            💔 DESVINCULAR VEÍCULO
                        </button>
                    </div>
                </div>

                <div id="unlink_form_device" class="col-12 mt-3" style="display: none; border-top: 1px dashed #ddd; padding-top: 15px;">
                    <label class="font-weight-bold small text-danger text-uppercase">Motivo da Desvinculação</label>
                    <textarea id="unlink_reason" class="form-control form-control-sm" rows="2" placeholder="Ex: Manutenção, retirada final..."></textarea>
                </div>
            </div>`;

        Swal.fire({
            title: '<i class="fas fa-microchip mr-2 text-warning"></i> GESTÃO DE HARDWARE',
            width: '600px',
            html: `
                <div class="text-left px-2">
                    <div class="row mb-3">
                        <div class="col-6">
                             <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">RTECH CODE</label>
                             <input type="text" class="form-control font-weight-bold text-primary" value="${data.internalCode}" readonly style="border-radius: 8px; height: 45px; background: #f0f7ff; border: 1px solid #007bff;">
                        </div>
                        <div class="col-6">
                             <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">IMEI / ANATEL</label>
                             <input type="text" class="form-control font-weight-bold bg-light" value="${data.imei}" readonly style="border-radius: 8px; height: 45px;">
                        </div>
                    </div>
                    
                    <div class="row align-items-end mb-4 mt-4">
                        <div class="col-7">
                            <label class="font-weight-bold small text-muted text-uppercase mb-2">Rastreador em USO</label>
                            <div class="rounded shadow-sm px-3 d-flex flex-column justify-content-center align-items-center position-relative" style="background: #1e293b; height: 120px; border-radius: 15px !important; border: 2px solid #334155;">
                                <div class="text-uppercase mb-1 text-center" style="color: #94a3b8; font-size: 1.4rem; font-weight: 900; letter-spacing: 1px; width: 100%;">
                                    ${data.internalCode}
                                </div>
                                <div class="text-uppercase mb-2 text-center" style="color: rgba(148, 163, 184, 0.4); font-size: 0.55rem; font-weight: 700; letter-spacing: 2px; width: 100%;">
                                    ${data.model} CORE / V1.0
                                </div>
                                <div class="d-flex align-items-center justify-content-center w-100">
                                    <i class="fas fa-microchip mr-2" style="color: #94a3b8; font-size: 1.8rem;"></i>
                                    <span style="color: #94a3b8; font-size: 0.9rem; font-weight: 800; text-shadow: 0 2px 4px rgba(0,0,0,0.5);">${data.imei}</span>
                                </div>
                                <div style="position: absolute; bottom: 10px; right: 15px; color: #475569; font-size: 0.65rem; font-weight: 900; letter-spacing: 1px; opacity: 0.6;">ANATEL</div>
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="d-flex align-items-center justify-content-center" style="height: 145px;">
                                ${chipActionBox}
                            </div>
                        </div>
                    </div>
                    
                    ${vehicleBox}
                </div>`,
            showCancelButton: true,
            cancelButtonText: 'Cancel',
            confirmButtonText: 'SALVAR ALTERAÇÕES',
            confirmButtonColor: '#28a745',
            preConfirm: () => {
                const unlinkReason = $('#unlink_reason').val();
                const isUnlinking = $('#unlink_form_device').is(':visible');
                const isUnlinkingChip = $('#unlink_chip_hidden').length && $('#unlink_chip_hidden').val() === '1';

                if (isUnlinking && (!unlinkReason || unlinkReason.trim().length < 5)) {
                    Swal.showValidationMessage('Informe o motivo da desinstalação do veículo.');
                    return false;
                }

                if (!isUnlinking && !isUnlinkingChip) {
                    Swal.close();
                    return;
                }

                // If they unlink vehicle/chip, send to back-end
                return $.ajax({
                    url: '/devices/' + id,
                    method: 'PUT',
                    data: {
                        internal_code: data.internalCode,
                        imei: data.imei,
                        status: data.status,
                        customer_id: data.customerId,
                        unlink_vehicle: isUnlinking ? 1 : 0,
                        unlink_reason: unlinkReason,
                        unlink_chip: isUnlinkingChip ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    }
                }).catch(error => Swal.showValidationMessage(error.responseJSON?.message || 'Erro ao salvar.'));
            }
        }).then((result) => result.isConfirmed && location.reload());
    };

    @if(session('success')) Swal.fire({ icon: 'success', title: 'Sucesso', text: "{{ session('success') }}", timer: 2000, showConfirmButton: false }); @endif
    @if(session('error')) Swal.fire({ icon: 'error', title: 'Erro de Operação', text: "{{ session('error') }}" }); @endif
    @if(session('warning_block')) Swal.fire({ title: '<div class="mb-3"><i class="fas fa-exclamation-triangle" style="color: #f39c12; font-size: 5.5rem;"></i></div><span style="color: #f39c12; font-weight: 800; font-size: 1.8rem;">ATENÇÃO!</span>', html: '<div class="mt-2" style="font-size: 1.1rem; color: #555;">{!! session("warning_block") !!}</div>', confirmButtonText: 'ENTENDI', confirmButtonColor: '#f39c12' }); @endif
</script>
@endpush

<style>
    .tiny-text { font-size: 0.65rem; }
    .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
    .cursor-pointer { cursor: pointer; }
    .accordion-toggle { outline: none !important; }
    .accordion-toggle[aria-expanded="true"] { background: rgba(59, 130, 246, 0.05); }
    .accordion-toggle[aria-expanded="true"] .accordion-icon { transform: rotate(180deg); }
    .btn-square { width: 42px; height: 42px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .btn-square-xs { width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .btn-square-sm { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    
    /* 🇧🇷 ESTILO PLACA MERCOSUL */
    .mercosul-plate { display: inline-flex; flex-direction: column; background: #fff; border: 1.5px solid #000; border-radius: 4px; overflow: hidden; min-width: 110px; line-height: 1; vertical-align: middle; }
    .mercosul-header { background: #003399; color: #fff; font-size: 0.45rem; text-align: center; padding: 2px 0; font-weight: 800; letter-spacing: 1.5px; border-bottom: 0.5px solid #000; }
    .mercosul-body { color: #000; font-size: 1.2rem; text-align: center; padding: 4px 10px; font-weight: bold; font-family: 'Roboto Mono', monospace; letter-spacing: -1px; }
    
    /* 🛡️ LIMPEZA DE FOCUS */
    td[data-toggle="collapse"] { outline: none !important; box-shadow: none !important; -webkit-tap-highlight-color: transparent; }

    /* 🦓 SISTEMA DE ZEBRADO */
    .table-zebra tbody tr:nth-child(odd)  { background-color: #e2e5eb; }
    .table-zebra tbody tr:nth-child(even) { background-color: #f8f9fa; }
    .table-zebra tbody tr { transition: background-color 0.2s ease; }
    .table-zebra tbody tr:hover { background-color: #cfd5e8; }
</style>
@endsection
