@extends('layouts.app')

@section('title', 'Portfólio de Clientes')

@section('content')
<div class="container-fluid pt-3 px-4">
    <!-- 🚀 CABEÇALHO LIMPO (MESMO PADRÃO DEVICES) -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-12 p-0">
            <h1 class="m-0 text-bold d-none d-sm-block text-dark" style="font-size: 2.2rem; letter-spacing: -1.2px;">
                <i class="fas fa-users mr-2 text-primary opacity-80"></i>Clientes & Frotas
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.6rem; letter-spacing: -1.2px;">
                <i class="fas fa-users mr-1 text-primary"></i>Clientes
            </h1>
            <p class="text-muted mb-0 font-weight-bold" style="font-size: 1.05rem; opacity: 0.8;">Gerenciamento de proprietários, carteira de ativos e custódia de patrimônio.</p>
        </div>
    </div>

    <!-- 📊 GRID DE CLIENTES (PADRÃO UNIVERSAL 1.0) -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <!-- 🛠️ CARD HEADER: BARRA DE AÇÕES INTEGRADA -->
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem; color: #334155;">
                <i class="fas fa-list mr-2 text-primary opacity-50"></i>Gestão de Custódia
            </h3>

            <div class="card-tools ml-auto">
                <form action="/customers" method="GET" class="d-flex align-items-center">
                    <!-- 🔍 PESQUISAR POR CLIENTE / DOCUMENTO -->
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Filtrar por Nome ou Doc..." value="{{ request('search') }}">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-default shadow-none border">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ➕ NOVO CLIENTE -->
                    <button type="button" 
                            class="btn btn-sm btn-primary ml-3 px-3 font-weight-bold shadow-sm"
                            onclick="location.href='/customers/create'"
                            style="border-radius: 6px; height: 31px; display: flex; align-items: center; border: none; background: #007bff;">
                        <i class="fas fa-plus-circle mr-2"></i> NOVO CLIENTE
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr class="text-center text-dark font-weight-bold text-uppercase border-bottom" style="background-color: rgba(15, 23, 42, 0.02); font-size: 0.75rem; letter-spacing: 1.5px;">
                            <th class="py-3 d-none d-md-table-cell" style="width: 60px;">ID</th>
                            <th class="text-left px-4">IDENTIFICAÇÃO / CLIENTE</th>
                            <th class="d-none d-lg-table-cell">DETALHES CADASTRÁIS</th>
                            <th class="text-center">ESTRUTURA DE ATIVOS</th>
                            <th class="text-center" style="width: 140px;">STATUS</th>
                            <th class="text-center" style="width: 140px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr>
                            <td class="text-center align-middle d-none d-md-table-cell text-muted small">{{ $customer->id }}</td>
                            <td class="align-middle px-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-capsule mr-3 d-flex align-items-center justify-content-center text-white font-weight-bold" 
                                         style="width: 38px; height: 38px; min-width: 38px; border-radius: 10px; background: linear-gradient(135deg, {{ ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'][$customer->id % 5] }} 0%, {{ ['#1d4ed8', '#047857', '#b45309', '#b91c1c', '#6d28d9'][$customer->id % 5] }} 100%);">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="text-primary font-weight-bold" style="font-size: 1rem; line-height: 1.1;">{{ $customer->name }}</div>
                                        <div class="small text-muted font-weight-bold opacity-60" style="font-size: 0.65rem; letter-spacing: 0.5px;">Ref: {{ $customer->code ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center align-middle d-none d-lg-table-cell">
                                <span class="badge badge-light border px-2 py-1 text-uppercase font-weight-normal text-muted" style="font-size: 0.7rem;">
                                    {{ $customer->document ?? '---' }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="badge badge-light border px-3 py-1 font-weight-bold shadow-xs" style="border-radius: 8px;">
                                    <i class="fas fa-microchip mr-2 text-primary opacity-50"></i>{{ $customer->devices_count }} | 
                                    <i class="fas fa-truck ml-2 mr-2 text-info opacity-50"></i>{{ $customer->vehicles_count }}
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                @if($customer->is_default_stock)
                                    <span class="badge bg-dark px-3 py-1 shadow-sm" style="font-size: 0.65rem;">INVENTÁRIO</span>
                                @else
                                    <span class="badge bg-success px-3 py-1 shadow-sm" style="font-size: 0.65rem;">ATIVO</span>
                                @endif
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <!-- 👁️ DOSSIÊ -->
                                    <button class="btn btn-light btn-square border-right" title="Dossiê de Cliente" onclick="openCustomerDossier(this)"
                                            data-id="{{ $customer->id }}"
                                            data-name="{{ $customer->name }}"
                                            data-document="{{ $customer->document }}"
                                            data-code="{{ $customer->code }}"
                                            data-devices="{{ $customer->devices_count }}"
                                            data-vehicles="{{ $customer->vehicles_count }}"
                                            data-users="{{ $customer->sub_users_count }}"
                                            data-created="{{ $customer->created_at->format('d/m/Y H:i') }}">
                                        <i class="fas fa-eye text-info"></i>
                                    </button>

                                    <!-- ⚙️ CONFIG -->
                                    <button class="btn btn-light btn-square border-right" title="Configurações" onclick="openCustomerEdit(this)"
                                            data-id="{{ $customer->id }}"
                                            data-name="{{ $customer->name }}"
                                            data-document="{{ $customer->document }}"
                                            data-code="{{ $customer->code }}"
                                            data-stock="{{ $customer->is_default_stock }}">
                                        <i class="fas fa-tools text-warning"></i>
                                    </button>

                                    <!-- 🗑️ INATIVAR -->
                                    <form action="/customers/{{ $customer->id }}" method="POST" onsubmit="return confirm('Deseja realmente inativar este cliente? Esta ação preservará os dados históricos.')" class="m-0 d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-square text-danger" title="Inativar Cliente">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted font-italic">Nenhum cliente registrado na carteira.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 px-4 py-3">
            <div class="float-right">{{ $customers->links() }}</div>
        </div>
    </div>
</div>

<style>
    body { background-color: #f8fafc !important; }
    .shadow-xs { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
    .animate__animated { --animate-duration: 0.6s; }
    .table th { border-top: none !important; }
    .avatar-capsule { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); cursor: pointer; }
    .avatar-capsule:hover { transform: scale(1.1) rotate(5deg); box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1) !important; }
</style>

@endsection

@push('scripts')
<script>
    /**
     * 👁️ DOSSIÊ DE CLIENTE
     */
    window.openCustomerDossier = function(el) {
        const data = $(el).data();
        
        Swal.fire({
            title: '<i class="fas fa-user-shield mr-2 text-primary"></i> DOSSIÊ DE CLIENTE',
            width: '600px',
            confirmButtonText: 'FECHAR',
            confirmButtonColor: '#6c757d',
            html: `
                <div class="text-left px-2" style="font-family: 'Source Sans Pro', sans-serif;">
                    <!-- 💎 CABEÇALHO MESTRE -->
                    <div class="mb-4 p-4 border rounded shadow-xs text-center" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 20px !important;">
                        <div class="avatar-circle mx-auto mb-3 shadow d-flex align-items-center justify-content-center text-white font-weight-bold h1" style="width: 80px; height: 80px; border-radius: 20px; background: #007bff;">
                            ${data.name.substring(0,1)}
                        </div>
                        <h4 class="font-weight-bold text-dark mb-1">${data.name}</h4>
                        <div class="small text-muted font-weight-bold text-uppercase" style="letter-spacing: 2px;">REG: ${data.code || '---'}</div>
                    </div>

                    <!-- 📦 ESTRUTURA DE ATIVOS (GRID 3) -->
                    <div class="row no-gutters mb-4 text-center">
                        <div class="col-4 pr-1">
                            <div class="p-3 border rounded shadow-xs" style="background: #f8fafc; border-radius: 15px !important;">
                                <div class="small text-muted font-weight-bold text-uppercase mb-1" style="font-size: 0.6rem;">Rastreadores</div>
                                <div class="h4 font-weight-bold text-primary mb-0">${data.devices}</div>
                            </div>
                        </div>
                        <div class="col-4 px-1">
                            <div class="p-3 border rounded shadow-xs" style="background: #f8fafc; border-radius: 15px !important;">
                                <div class="small text-muted font-weight-bold text-uppercase mb-1" style="font-size: 0.6rem;">Veículos</div>
                                <div class="h4 font-weight-bold text-info mb-0">${data.vehicles}</div>
                            </div>
                        </div>
                        <div class="col-4 pl-1">
                            <div class="p-3 border rounded shadow-xs" style="background: #f8fafc; border-radius: 15px !important;">
                                <div class="small text-muted font-weight-bold text-uppercase mb-1" style="font-size: 0.6rem;">Usuários</div>
                                <div class="h4 font-weight-bold text-dark mb-0">${data.users}</div>
                            </div>
                        </div>
                    </div>

                    <!-- 📑 INFORMAÇÕES CADASTRÁIS -->
                    <div class="p-4 bg-light rounded border-left" style="border-left: 4px solid #007bff !important; border-radius: 12px !important;">
                        <div class="mb-3">
                            <label class="small text-muted mb-0 d-block font-weight-bold text-uppercase">Documento Mestre</label>
                            <div class="h6 font-weight-bold">${data.document || 'NÃO INFORMADO'}</div>
                        </div>
                        <div>
                            <label class="small text-muted mb-0 d-block font-weight-bold text-uppercase">Registro Inicial na Plataforma</label>
                            <div class="h6 font-weight-bold">${data.created}</div>
                        </div>
                    </div>
                </div>`
        });
    }

    /**
     * 🛠️ EDIÇÃO DE CLIENTE
     */
    window.openCustomerEdit = function(el) {
        const data = $(el).data();
        
        Swal.fire({
            title: '<i class="fas fa-edit mr-2 text-warning"></i> CONFIGURAÇÕES DE CLIENTE',
            width: '550px',
            html: `
                <div class="text-left px-2" style="font-family: 'Source Sans Pro', sans-serif;">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase mb-1">NOME DA INSTITUIÇÃO / CLIENTE</label>
                            <input type="text" id="edit_cust_name" class="form-control form-control-sm border-0 bg-light p-3" value="${data.name}" style="height: 45px; border-radius: 10px;">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase mb-1">DOCUMENTO (CPF/CNPJ)</label>
                            <input type="text" id="edit_cust_doc" class="form-control form-control-sm border-0 bg-light" value="${data.document}" style="height: 40px; border-radius: 8px;">
                        </div>
                        <div class="col-6 mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase mb-1">CÓDIGO INTERNO</label>
                            <input type="text" id="edit_cust_code" class="form-control form-control-sm border-0 bg-light" value="${data.code}" style="height: 40px; border-radius: 8px;">
                        </div>
                        <div class="col-12">
                            <div class="p-3 border rounded d-flex align-items-center justify-content-between" style="border-radius: 12px !important; background: #fefce8; border: 1px solid #fef08a;">
                                <div>
                                    <div class="font-weight-bold text-warning-emphasis small text-uppercase">PERFIL DE ESTOQUE INTERNO</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">Define se este cliente representa o inventário tático de ativos da própria Rastertech.</div>
                                </div>
                                <div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">
                                    <input type="checkbox" class="custom-control-input" id="edit_cust_stock" ${data.stock ? 'checked' : ''}>
                                    <label class="custom-control-label" for="edit_cust_stock"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'SALVAR CONFIGURAÇÕES',
            confirmButtonColor: '#28a745',
            showLoaderOnConfirm: true,
            preConfirm: () => {
                const name = $('#edit_cust_name').val();
                if (!name) return Swal.showValidationMessage('O nome do cliente é obrigatório');
                
                return $.ajax({
                    url: `/customers/${data.id}`,
                    method: 'PUT',
                    data: {
                        name: name,
                        document: $('#edit_cust_doc').val(),
                        code: $('#edit_cust_code').val(),
                        is_default_stock: $('#edit_cust_stock').is(':checked') ? 1 : 0,
                        _token: '{{ csrf_token() }}'
                    }
                }).catch(() => Swal.showValidationMessage('Falha ao atualizar o portfólio. Verifique a integridade.'));
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire('SUCESSO!', 'Portfólio atualizado com êxito.', 'success').then(() => location.reload());
            }
        });
    }
</script>
@endpush
