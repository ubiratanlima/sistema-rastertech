@extends('layouts.app')

@section('title', 'Gestão de Plataformas')

@section('content')
<div class="container-fluid">
    <!-- 🏗️ CABEÇALHO DA PÁGINA (Padrão Ouro Limpo) -->
    <div class="row mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark" style="font-size: 2.2rem;">
                <i class="fas fa-server mr-2 text-info"></i>Plataformas
            </h1>
            <p class="text-muted small mb-0 d-none d-sm-block">Gestão de infraestrutura, servidores e ecossistemas de rastreamento.</p>
        </div>
    </div>

    <!-- 📊 CARD PRINCIPAL: LISTAGEM INTEGRADA -->
    <div class="card card-outline card-info shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title font-weight-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-network-wired mr-2 text-info"></i>Configurações de Rede
            </h3>
            
            <div class="card-tools ml-auto">
                <form action="{{ route('platforms.index') }}" method="GET" class="d-flex align-items-center">
                    <!-- 🔍 PESQUISAR -->
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Filtrar por Nome ou IP..." value="{{ $search }}">
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

                    <!-- ➕ NOVA PLATAFORMA -->
                    <button type="button" class="btn btn-sm btn-info ml-4 px-3 font-weight-bold shadow-sm text-white" data-toggle="modal" data-target="#modalNovaPlataforma" style="border-radius: 6px; height: 31px; display: flex; align-items: center;">
                        <i class="fas fa-plus-circle mr-2"></i> NOVA PLATAFORMA
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
                                    SISTEMA <i class="fas fa-sort{{ $sort == 'name' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th style="width: 180px;">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'server_ip', 'direction' => ($sort == 'server_ip' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    IP SERVIDOR <i class="fas fa-sort{{ $sort == 'server_ip' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="d-none d-md-table-cell">URL DE ACESSO</th>
                            <th class="d-none d-md-table-cell" style="width: 150px;">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'devices_count', 'direction' => ($sort == 'devices_count' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    APARELHOS <i class="fas fa-sort{{ $sort == 'devices_count' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th style="width: 180px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($platforms as $platform)
                        <tr class="platform-row">
                            <td class="align-middle px-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-box mr-3 d-flex align-items-center justify-content-center text-white font-weight-bold shadow-sm" 
                                         style="width: 40px; height: 40px; border-radius: 10px; background: #00d2ff; border: 1px solid rgba(0,0,0,0.05);">
                                        <i class="fas fa-server"></i>
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark" style="font-size: 1rem;">{{ $platform->name }}</div>
                                        <div class="small text-muted font-weight-bold text-uppercase opacity-75" style="letter-spacing: 0.5px;">{{ $platform->supplier_name ?? 'INFRA PRÓPRIA' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center align-middle">
                                <code class="px-2 py-1 shadow-sm" style="border-radius: 6px; background: #f0f4f8; font-weight: bold; border: 1px solid #d1d9e6;">{{ $platform->server_ip }}</code>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                @if($platform->url)
                                    <a href="{{ $platform->url }}" target="_blank" class="text-info font-weight-bold">
                                        {{ Str::limit($platform->url, 30) }} <i class="fas fa-external-link-alt ml-1 small"></i>
                                    </a>
                                @else
                                    <span class="text-muted small">NÃO DEFINIDA</span>
                                @endif
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="badge badge-light border text-bold px-3 py-2" style="border-radius: 20px;">{{ $platform->devices_count }} ativos</span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm platform-actions" style="border-radius: 8px; overflow: hidden; border: 1px solid #dee2e6;">
                                    @if(!$platform->deleted_at)
                                        <button type="button" class="btn btn-light btn-square btn-view-platform" title="Ver Detalhes" data-id="{{ $platform->id }}"><i class="fas fa-eye fa-lg text-info"></i></button>
                                        <button type="button" class="btn btn-light btn-square border-right btn-edit-platform" title="Editar"
                                            data-id="{{ $platform->id }}"
                                            data-name="{{ $platform->name }}"
                                            data-ip="{{ $platform->server_ip }}"
                                            data-url="{{ $platform->url }}"
                                            data-supplier="{{ $platform->supplier_name }}"
                                            data-android="{{ $platform->app_android_url }}"
                                            data-ios="{{ $platform->app_ios_url }}">
                                            <i class="fas fa-tools fa-lg text-warning"></i>
                                        </button>
                                        <form id="formDelete_{{ $platform->id }}" action="{{ route('platforms.destroy', $platform->id) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                        <button type="button" class="btn btn-light btn-square btn-delete-platform" title="Inativar"
                                            data-id="{{ $platform->id }}"
                                            data-name="{{ $platform->name }}">
                                            <i class="fas fa-power-off fa-lg text-danger"></i>
                                        </button>
                                    @else
                                        <form action="{{ route('platforms.restore', $platform->id) }}" method="POST" class="m-0 w-100">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="btn btn-light btn-block font-weight-bold text-success py-2 px-4" title="Reativar Plataforma" style="min-width: 155px;">
                                                <i class="fas fa-recycle mr-2"></i> REATIVAR SISTEMA
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-server fa-3x mb-3 opacity-20"></i><br>
                                Nenhuma plataforma encontrada para os filtros selecionados.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($platforms->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
            {{ $platforms->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 🏗️ MODAL NOVA PLATAFORMA -->
<div class="modal fade" id="modalNovaPlataforma" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-info text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-plus-circle mr-2"></i>Nova Plataforma de Operação</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('platforms.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Nome do Sistema</label>
                            <input type="text" name="name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="Ex: Traccar, Wialon, RasterTech v3" required>
                        </div>
                        <div class="col-12 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Endereço IP (Server IP)</label>
                            <input type="text" name="server_ip" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="Ex: 54.12.33.10" required>
                        </div>
                        <div class="col-12 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">URL de Acesso (Login)</label>
                            <input type="url" name="url" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="https://plataforma.clound.com">
                        </div>
                        <div class="col-12 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Nome do Fornecedor / Infra</label>
                            <input type="text" name="supplier_name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="Ex: AWS, Google Cloud">
                        </div>
                        <div class="col-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold text-success"><i class="fab fa-android mr-1"></i> Play Store</label>
                            <input type="url" name="app_android_url" class="form-control form-control-sm border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="https://play....">
                        </div>
                        <div class="col-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold text-info"><i class="fab fa-apple mr-1"></i> Apple Store</label>
                            <input type="url" name="app_ios_url" class="form-control form-control-sm border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="https://apps....">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-info px-4 shadow-sm font-weight-bold text-white" style="border-radius: 8px;">ATIVAR NO RADAR</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!-- Configurações Adicionais via Script -->

@php
    $platformData = $platforms->mapWithKeys(function ($p) {
        return [$p->id => [
            'name' => $p->name,
            'ip' => $p->server_ip,
            'url' => $p->url ?? 'Não definida',
            'supplier' => $p->supplier_name ?? 'Infra Própria',
            'devices' => $p->devices_count,
            'android' => $p->app_android_url,
            'ios' => $p->app_ios_url,
        ]];
    });
@endphp

@push('scripts')
<script>
    $(document).ready(function() {
        const platforms = @json($platformData);

        // 🔎 HANDLER: DOSSIÊ TÁTICO (PADRÃO GSM)
        $('.btn-view-platform').click(function() {
            const id = $(this).data('id');
            const platform = platforms[id];

            if (!platform) return;

            let appLinks = '';
            if (platform.android) appLinks += `<span class="badge badge-success px-3 py-2 mr-2"><i class="fab fa-android"></i> Store</span>`;
            if (platform.ios) appLinks += `<span class="badge badge-info px-3 py-2"><i class="fab fa-apple"></i> Store</span>`;
            if (!appLinks) appLinks = '<span class="text-muted small italic">Apps não vinculados</span>';

            Swal.fire({
                title: '<i class="fas fa-server mr-2 text-info"></i> PLATAFORMA',
                width: '550px',
                html: `
                    <div class="text-left" style="font-family: 'Source Sans Pro', sans-serif;">
                        <div class="mb-3 p-3 bg-light rounded shadow-sm border-left" style="border-left: 4px solid #17a2b8 !important;">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">NOME DO SISTEMA</label>
                            <div class="h5 font-weight-bold text-dark mb-0">${platform.name}</div>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-light rounded border-left" style="border-left: 4px solid #6c757d !important;">
                                    <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">IP DO SERVIDOR</label>
                                    <code class="font-weight-bold text-dark h6">${platform.ip}</code>
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="p-3 bg-light rounded border-left" style="border-left: 4px solid #28a745 !important;">
                                    <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">ATIVOS REAIS</label>
                                    <div class="font-weight-bold text-success h5 mb-0">${platform.devices} un</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3 p-3 bg-light rounded shadow-sm border-left" style="border-left: 4px solid #007bff !important;">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">URL DE ACESSO</label>
                            <div class="font-weight-bold text-primary truncate" style="font-size: 0.95rem;">${platform.url}</div>
                        </div>

                        <div class="row align-items-center">
                            <div class="col-6">
                                <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">INFRAESTRUTURA</label>
                                <span class="text-dark font-weight-bold text-uppercase font-italic small">${platform.supplier}</span>
                            </div>
                            <div class="col-6 text-right">
                                <label class="small text-muted mb-2 d-block font-weight-bold text-uppercase">DISPONIBILIDADE APPS</label>
                                ${appLinks}
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

        // ✏️ HANDLER: EDIÇÃO TÁTICA (PADRÃO GSM)
        $('.btn-edit-platform').click(function() {
            const id = $(this).data('id');
            const platform = platforms[id];

            if (!platform) return;

            Swal.fire({
                title: '<i class="fas fa-tools mr-2 text-warning"></i> EDITAR PLATAFORMA',
                width: '550px',
                html: `
                    <div class="text-left" style="font-family: 'Source Sans Pro', sans-serif;">
                        <div class="form-group mb-3">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">NOME DO SISTEMA</label>
                            <input type="text" id="swal_edit_name" class="form-control" value="${platform.name}" style="height: 45px; border-radius: 8px;">
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">IP DO SERVIDOR</label>
                                <input type="text" id="swal_edit_ip" class="form-control text-primary font-weight-bold" value="${platform.ip}" style="height: 45px; border-radius: 8px;">
                            </div>
                            <div class="col-6 mb-3">
                                <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">FORNECEDOR / INFRA</label>
                                <input type="text" id="swal_edit_supplier" class="form-control" value="${platform.supplier}" style="height: 45px; border-radius: 8px;">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">URL DE ACESSO (LOGIN)</label>
                            <input type="url" id="swal_edit_url" class="form-control" value="${platform.url || ''}" style="height: 45px; border-radius: 8px;">
                        </div>

                        <div class="row">
                            <div class="col-6">
                                <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase text-success font-weight-bold">
                                    <i class="fab fa-android mr-1"></i> PLAY STORE
                                </label>
                                <input type="url" id="swal_edit_android" class="form-control form-control-sm" value="${platform.android || ''}" style="border-radius: 8px;">
                            </div>
                            <div class="col-6">
                                <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase text-info font-weight-bold">
                                    <i class="fab fa-apple mr-1"></i> APPLE STORE
                                </label>
                                <input type="url" id="swal_edit_ios" class="form-control form-control-sm" value="${platform.ios || ''}" style="border-radius: 8px;">
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
                        url: `/platforms/${id}`,
                        method: 'PUT',
                        data: {
                            name: $('#swal_edit_name').val(),
                            server_ip: $('#swal_edit_ip').val(),
                            supplier_name: $('#swal_edit_supplier').val(),
                            url: $('#swal_edit_url').val(),
                            app_android_url: $('#swal_edit_android').val(),
                            app_ios_url: $('#swal_edit_ios').val(),
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
                        text: 'Dados da infraestrutura atualizados no radar.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                }
            });
        });

        // ⛔ HANDLER: INATIVAR COM SEGURANÇA (SWEETALERT2)
        $('.btn-delete-platform').click(function() {
            const id = $(this).data('id');
            const name = $(this).data('name');

            Swal.fire({
                title: 'Inativar Sistema?',
                html: `Você está prestes a remover o acesso à plataforma <b class="text-danger">${name}</b>.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sim, inativar!',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    $(`#formDelete_${id}`).submit();
                }
            });
        });

        // ✨ FEEDBACK DE SUCESSO (SWEETALERT2 CENTRALIZADO)
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: "{{ session('success') }}",
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

        // 📝 ERROS DE VALIDAÇÃO DO LARAVEL
        @if($errors->any())
            Swal.fire({
                icon: 'warning',
                title: 'Atenção!',
                html: `@foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach`,
                confirmButtonColor: '#f39c12',
            });
        @endif
    });
</script>
@endpush

<style>
    .avatar-box { transition: all 0.3s ease; }
    .platform-row:hover { background-color: rgba(0, 210, 255, 0.02) !important; }
    .platform-row:hover .avatar-box { transform: scale(1.1); }
    .btn-square { width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; }
    .btn-group .btn:hover { background: #f8f9fa; z-index: 1; }
    
    /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
    .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
    .dark-mode .modal-content { background: #1a1a2e; border: 1px solid #2d2d44; }
    .dark-mode .modal-body input { background: #16213e !important; color: #fff !important; border: 1px solid #2d2d44 !important; }
    .dark-mode .modal-footer { background: #16213e !important; }
    .dark-mode code { background: #16213e !important; border-color: #2d2d44 !important; color: #00d2ff !important; }
    
    .animate__animated { --animate-duration: 0.6s; }
</style>
@endsection
