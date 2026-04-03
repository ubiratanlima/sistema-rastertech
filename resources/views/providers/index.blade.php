@extends('layouts.app')

@section('title', 'Gestão de Fornecedores')

@section('content')
<div class="container-fluid">

    <!-- 🏗️ CABEÇALHO DA PÁGINA (Padrão Ouro Limpo) -->
    <div class="row mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark" style="font-size: 2.2rem;">
                <i class="fas fa-industry mr-2 text-primary"></i>Fornecedores
            </h1>
            <p class="text-muted small mb-0 d-none d-sm-block">Gestão de parceiros de hardware, conectividade e serviços integrados.</p>
        </div>
    </div>

    <!-- 📊 CARD PRINCIPAL: LISTAGEM INTEGRADA -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title font-weight-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-list-ul mr-2 text-primary"></i>Base de Fornecimento
            </h3>
            
            <div class="card-tools ml-auto">
                <form action="{{ route('providers.index') }}" method="GET" class="d-flex align-items-center">
                    <!-- 🔍 PESQUISAR -->
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Filtrar por Fornecedor..." value="{{ $search }}">
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

                    <!-- ➕ NOVO FORNECEDOR -->
                    <button type="button" class="btn btn-sm btn-primary ml-4 px-3 font-weight-bold shadow-sm" data-toggle="modal" data-target="#modalNovoFornecedor" style="border-radius: 6px; height: 31px; display: flex; align-items: center;">
                        <i class="fas fa-plus-circle mr-2"></i> NOVO FORNECEDOR
                    </button>
                </form>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02); font-size: 0.95rem;">
                            <th class="text-left px-4">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sort == 'name' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    PARCEIRO <i class="fas fa-sort{{ $sort == 'name' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th style="width: 150px;">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'type', 'direction' => ($sort == 'type' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    TIPO <i class="fas fa-sort{{ $sort == 'type' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="d-none d-md-table-cell" style="width: 150px;">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'devices_count', 'direction' => ($sort == 'devices_count' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    HARDWARE <i class="fas fa-sort{{ $sort == 'devices_count' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="d-none d-md-table-cell" style="width: 150px;">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'gsm_cards_count', 'direction' => ($sort == 'gsm_cards_count' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    CHIPS <i class="fas fa-sort{{ $sort == 'gsm_cards_count' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th style="width: 180px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($providers as $provider)
                        <tr class="provider-row">
                            <td class="align-middle px-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-box mr-3 d-flex align-items-center justify-content-center text-white font-weight-bold shadow-sm" 
                                         style="width: 40px; height: 40px; border-radius: 10px; background: #1e293b; border: 1px solid rgba(255,255,255,0.1);">
                                        {{ substr($provider->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark" style="font-size: 1rem;">{{ $provider->name }}</div>
                                        <div class="small text-muted font-weight-bold text-uppercase opacity-75" style="letter-spacing: 0.5px;">{{ $provider->email ?? 'SEM CONTATO' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge provider-type-badge provider-type-{{ $provider->type }} text-uppercase">
                                    {{ ucfirst($provider->type) }}
                                </span>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="badge badge-light border">{{ $provider->devices_count }} un</span>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="badge badge-light border">{{ $provider->gsm_cards_count }} un</span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm provider-actions" style="border-radius: 8px; overflow: hidden; border: 1px solid #dee2e6;">
                                    @if(!$provider->deleted_at)
                                        <button type="button" class="btn btn-light btn-square btn-view-provider" title="Ver Detalhes" data-provider-id="{{ $provider->id }}"><i class="fas fa-eye fa-lg text-info"></i></button>
                                        <button type="button" class="btn btn-light btn-square border-right btn-edit-provider" title="Editar"
                                            data-id="{{ $provider->id }}"
                                            data-name="{{ $provider->name }}"
                                            data-type="{{ $provider->type }}"
                                            data-email="{{ $provider->email }}"
                                            data-phone="{{ $provider->phone }}"
                                            data-document="{{ $provider->document }}"
                                            data-contact="{{ $provider->contact_name }}">
                                            <i class="fas fa-tools fa-lg text-warning"></i>
                                        </button>
                                        <form id="formDelete_{{ $provider->id }}" action="{{ route('providers.destroy', $provider->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" class="btn btn-light btn-square btn-delete-provider" title="Inativar"
                                            data-id="{{ $provider->id }}"
                                            data-name="{{ $provider->name }}">
                                            <i class="fas fa-power-off fa-lg text-danger"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('providers.restore', $provider->id) }}" method="POST" class="m-0 w-100">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-light btn-block font-weight-bold text-success py-2 px-4" title="Reativar Fornecedor">
                                                <i class="fas fa-recycle mr-2"></i> REATIVAR PARCEIRO
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-industry fa-3x mb-3 opacity-20"></i><br>
                                Nenhum fornecedor encontrado para os filtros selecionados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($providers->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
            {{ $providers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 🏗️ MODAL NOVO FORNECEDOR -->
<div class="modal fade" id="modalNovoFornecedor" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3 shadow-sm">
                <h5 class="modal-title font-weight-bold" style="letter-spacing: -0.5px;">
                    <i class="fas fa-plus-circle mr-2"></i>Novo Cadastro de Fornecedor
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('providers.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-8 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-industry mr-1"></i> Nome da Empresa</label>
                            <input type="text" name="name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="Ex: Queclink, Vivo, RasterTech Cloud" required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-tags mr-1"></i> Tipo</label>
                            <select name="type" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" required>
                                <option value="hardware">Hardware (Rastreadores)</option>
                                <option value="connectivity">Conectividade (SIM Cards)</option>
                                <option value="software">Software / Serviços</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-user mr-1"></i> Nome do Contato</label>
                            <input type="text" name="contact_name" class="form-control border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="Ex: João Silva">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-id-card mr-1"></i> CNPJ</label>
                            <input type="text" name="document" class="form-control border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="00.000.000/0001-00">
                        </div>
                        <div class="col-md-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-envelope mr-1"></i> E-mail</label>
                            <input type="email" name="email" class="form-control border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="contato@empresa.com">
                        </div>
                        <div class="col-md-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-phone mr-1"></i> Telefone</label>
                            <input type="text" name="phone" class="form-control border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="(11) 99999-9999">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">SALVAR PARCEIRO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ✏️ MODAL EDITAR FORNECEDOR -->
<div class="modal fade" id="modalEditarFornecedor" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-warning text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-tools mr-2"></i>Editar Fornecedor</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="formEditarFornecedor" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-md-8 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-industry mr-1"></i> Nome da Empresa</label>
                            <input type="text" name="name" id="edit_provider_name" class="form-control border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" required>
                        </div>
                        <div class="col-md-4 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-tags mr-1"></i> Tipo</label>
                            <select name="type" id="edit_provider_type" class="form-control border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" required>
                                <option value="hardware">Hardware (Rastreadores)</option>
                                <option value="connectivity">Conectividade (SIM Cards)</option>
                                <option value="software">Software / Serviços</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-user mr-1"></i> Nome do Contato</label>
                            <input type="text" name="contact_name" id="edit_provider_contact" class="form-control border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-id-card mr-1"></i> CNPJ</label>
                            <input type="text" name="document" id="edit_provider_document" class="form-control border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;">
                        </div>
                        <div class="col-md-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-envelope mr-1"></i> E-mail</label>
                            <input type="email" name="email" id="edit_provider_email" class="form-control border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;">
                        </div>
                        <div class="col-md-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-phone mr-1"></i> Telefone</label>
                            <input type="text" name="phone" id="edit_provider_phone" class="form-control border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-warning text-white px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">SALVAR ALTERAÇÕES</button>
                </div>
            </form>
        </div>
    </div>
</div>

    @php
        $providerData = $providers->mapWithKeys(function ($provider) {
            return [$provider->id => [
                'name' => $provider->name,
                'type' => $provider->type,
                'email' => $provider->email ?? 'N/I',
                'phone' => $provider->phone ?? 'N/I',
                'document' => $provider->document ?? 'N/I',
                'contact_name' => $provider->contact_name ?? 'N/I',
                'status' => $provider->status ?? 'ativo',
            ]];
        });
    @endphp



<style>
    /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
    .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
    .dark-mode .btn-light { background: #1a1a2e; border-color: #2d2d44; color: #fff; }
    .dark-mode .btn-light:hover { background: #2d2d44; }
    .dark-mode .modal-content { background: #1a1a2e; border: 1px solid #2d2d44; }
    .dark-mode .modal-body input, .dark-mode .modal-body select { background: #16213e !important; color: #fff !important; border: 1px solid #2d2d44 !important; }
    .dark-mode .modal-footer { background: #16213e !important; }
    
    .provider-type-badge {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 0.4rem 0.7rem;
        border-radius: 1rem;
        color: #fff;
        letter-spacing: 0.02em;
    }
    .provider-type-hardware { background: #17a2b8; }
    .provider-type-connectivity { background: #28a745; }
    .provider-type-software { background: #6c757d; }

    .provider-row:hover { background: rgba(0, 123, 255, 0.04); }
    .provider-actions .btn { width: 44px; height: 44px; border-radius: 10px; }

    .btn-group .btn { padding: 8px 12px; }
    .animate__animated { --animate-duration: 0.6s; }
</style>

@push('scripts')
<script>
    const providerData = @json($providerData);

    $(document).ready(function(){
        // 🔎 HANDLER: DOSSIÊ TÁTICO DO FORNECEDOR (PADRÃO GSM)
        $(document).on('click', '.btn-view-provider', function() {
            const id = $(this).data('provider-id');
            const provider = providerData[id];
            
            if (!provider) {
                Swal.fire({ icon: 'error', title: 'Erro', text: 'Dados do fornecedor não localizados.' });
                return;
            }

            Swal.fire({
                title: '<i class="fas fa-truck-loading mr-2 text-warning"></i> FORNECEDOR',
                width: '550px',
                html: `
                    <div class="text-left" style="font-family: 'Source Sans Pro', sans-serif;">
                        <div class="mb-3 p-3 bg-light rounded shadow-sm border-left" style="border-left: 4px solid #ffc107 !important;">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">EMPRESA / NOME</label>
                            <div class="h5 font-weight-bold text-dark mb-0">${provider.name}</div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-light rounded border-left" style="border-left: 4px solid #6c757d !important;">
                                    <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">CNPJ / DOC</label>
                                    <div class="font-weight-bold text-dark h6">${provider.document || '---'}</div>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-light rounded border-left" style="border-left: 4px solid #17a2b8 !important;">
                                    <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">TIPO / CATEGORIA</label>
                                    <div class="font-weight-bold text-info h6 mb-0 text-uppercase">${provider.type}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 p-3 bg-light rounded shadow-sm border-left" style="border-left: 4px solid #00d2ff !important;">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">CONTATO PRINCIPAL / GESTOR</label>
                            <div class="h6 font-weight-bold text-dark mb-0">${provider.contact_name || '---'}</div>
                        </div>

                        <div class="row align-items-center px-1">
                            <div class="col-6">
                                <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">E-MAIL CORPORATIVO</label>
                                <span class="text-primary font-weight-bold small">${provider.email || '---'}</span>
                            </div>
                            <div class="col-6 text-right">
                                <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">TELEFONE REDE</label>
                                <span class="text-dark font-weight-bold h6">${provider.phone || '---'}</span>
                            </div>
                        </div>
                    </div>
                `,
                confirmButtonText: 'FECHAR',
                confirmButtonColor: '#343a40',
                customClass: {
                    confirmButton: 'px-5 py-2 font-weight-bold'
                }
            });
        });

        $(document).on('click', '.btn-delete-provider', function() {
            const id   = $(this).data('id');
            const name = $(this).data('name');
            Swal.fire({
                title: 'Inativar Fornecedor?',
                html: `Deseja realmente inativar <strong>${name}</strong>?<br><small class="text-muted">Esta ação só é permitida se não houver equipamentos ou chips vinculados.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74c3c',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Sim, inativar!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) { $('#formDelete_' + id).submit(); }
            });
        });

        // ✏️ HANDLER: EDIÇÃO TÁTICA (PADRÃO GSM)
        $(document).on('click', '.btn-edit-provider', function() {
            const id = $(this).data('id');
            const provider = providerData[id];
            
            if (!provider) return;

            Swal.fire({
                title: '<i class="fas fa-tools mr-2 text-warning"></i> EDITAR FORNECEDOR',
                width: '650px',
                html: `
                    <div class="text-left px-2" style="font-family: 'Source Sans Pro', sans-serif;">
                        <div class="form-group mb-3">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">NOME DA EMPRESA</label>
                            <input type="text" id="swal_prov_name" class="form-control" value="${provider.name}" style="height: 45px; border-radius: 8px;">
                        </div>

                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">TIPO / CATEGORIA</label>
                                <select id="swal_prov_type" class="form-control" style="height: 45px; border-radius: 8px;">
                                    <option value="hardware" ${provider.type === 'hardware' ? 'selected' : ''}>Hardware (Rastreadores)</option>
                                    <option value="connectivity" ${provider.type === 'connectivity' ? 'selected' : ''}>Conectividade (SIM Cards)</option>
                                    <option value="software" ${provider.type === 'software' ? 'selected' : ''}>Software / Serviços</option>
                                </select>
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">CNPJ / DOCUMENTO</label>
                                <input type="text" id="swal_prov_doc" class="form-control" value="${provider.document || ''}" style="height: 45px; border-radius: 8px;">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">CONTATO PRINCIPAL / GESTOR</label>
                            <input type="text" id="swal_prov_contact" class="form-control" value="${provider.contact_name || ''}" style="height: 45px; border-radius: 8px;">
                        </div>

                        <div class="row">
                            <div class="col-md-7 mb-3">
                                <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">E-MAIL CORPORATIVO</label>
                                <input type="email" id="swal_prov_email" class="form-control" value="${provider.email || ''}" style="height: 45px; border-radius: 8px;">
                            </div>
                            <div class="col-md-5 mb-3">
                                <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">TELEFONE REDE</label>
                                <input type="text" id="swal_prov_phone" class="form-control" value="${provider.phone || ''}" style="height: 45px; border-radius: 8px;">
                            </div>
                        </div>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'SALVAR',
                cancelButtonText: 'CANCELAR',
                confirmButtonColor: '#ffc107',
                cancelButtonColor: '#6c757d',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return $.ajax({
                        url: `/providers/${id}`,
                        method: 'PUT',
                        data: {
                            name: $('#swal_prov_name').val(),
                            type: $('#swal_prov_type').val(),
                            contact_name: $('#swal_prov_contact').val(),
                            document: $('#swal_prov_doc').val(),
                            email: $('#swal_prov_email').val(),
                            phone: $('#swal_prov_phone').val(),
                            _token: '{{ csrf_token() }}'
                        }
                    }).catch(error => {
                        Swal.showValidationMessage(error.responseJSON?.message || 'Erro ao processar requisição');
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'SINCRONIZADO',
                        text: 'Dados do fornecedor atualizados com sucesso.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }
            });
        });
    });

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Sucesso!',
        text: '{{ session("success") }}',
        confirmButtonColor: '#3085d6',
        timer: 3000
    });
    @endif

    // 🚫 FEEDBACK DE ERRO / BLOQUEIO (PADRÃO GSM PREMIUM)
    @if(session('error'))
    Swal.fire({ 
        html: `
            <div class="text-center">
                <div class="mb-3">
                    <i class="fas fa-exclamation-triangle fa-5x" style="color: #ff8c00;"></i>
                </div>
                <h2 class="font-weight-bold text-dark mb-2" style="font-size: 1.8rem;">ATENÇÃO!</h2>
                <div class="text-dark h6 font-weight-normal px-3 py-2" style="line-height: 1.5;">
                    {{ session('error') }}
                </div>
            </div>
        `,
        confirmButtonColor: '#ffc107',
        confirmButtonText: 'ENTENDI',
        background: '#fff3cd',
        customClass: {
            confirmButton: 'px-5 py-2 font-weight-bold text-dark border-0 shadow-sm mt-3'
        }
    });
    @endif
</script>
@endpush

@endsection
