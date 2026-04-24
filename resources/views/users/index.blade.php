@extends('layouts.app')

@section('title', 'Gestão de Usuários Internos')

@section('content')
<div class="container-fluid">

    <style>
        .bg-pink { background-color: #e83e8c !important; }
        .badge-primary-soft { background-color: rgba(0,123,255,0.1); color: #007bff; }
    </style>

    <!-- ⚓ CABEÇALHO PADRÃO OURO (8:4) -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center overflow-hidden flex-nowrap">
        <div class="col-12 p-0 p-sm-2">
            <h1 class="m-0 text-bold d-none d-sm-block" style="font-size: 2.2rem;">
                <i class="fas fa-user-shield mr-2 text-primary"></i>Controle Central
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-user-shield mr-1 text-primary"></i>Usuários
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block">Gestão de administradores e comandantes do sistema.</p>
        </div>
    </div>

    <!-- 🛠️ CARD DE LISTAGEM -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center flex-wrap">
            <h3 class="card-title text-bold mb-0 mr-auto" style="font-size: 1.1rem;">
                <i class="fas fa-users mr-2 text-primary"></i>Equipe Administrativa
            </h3>

            <div class="card-tools ml-auto d-flex align-items-center">
                <form action="{{ route('users.index') }}" method="GET" class="d-flex align-items-center">

                    <!-- 🔍 BUSCA TÁTICA -->
                    <div class="input-group input-group-sm shadow-sm mr-3" style="width: 250px;">
                        <input type="text" name="search" class="form-control border-0 bg-light"
                               placeholder="Buscar por nome, e-mail..." value="{{ $search }}"
                               style="border-radius: 8px 0 0 8px;">
                        <div class="input-group-append">
                            @if($search)
                                <a href="{{ route('users.index', ['view' => $view]) }}" class="btn btn-light border-0">
                                    <i class="fas fa-times text-muted"></i>
                                </a>
                            @endif
                            <button type="submit" class="btn btn-default shadow-none border">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>

                    <!-- ⚙️ SELETOR DE VISÃO -->
                    <div class="ml-2 d-flex align-items-center mr-3">
                        <label class="small font-weight-bold text-muted mr-2 mb-0">VISÃO:</label>
                        <select name="view" class="form-control form-control-sm" onchange="this.form.submit()" style="width: 130px; font-weight: bold; border-radius: 6px;">
                            <option value="active" {{ $view !== 'trash' ? 'selected' : '' }}>🟢 ATIVOS</option>
                            <option value="trash" {{ $view === 'trash' ? 'selected' : '' }}>⛔ INATIVOS</option>
                        </select>
                    </div>

                    <!-- ➕ BOTÃO NOVO USUÁRIO -->
                    <button type="button" class="btn btn-sm btn-primary shadow-sm px-3 font-weight-bold"
                            style="border-radius: 6px; height: 31px; display: flex; align-items: center;"
                            data-toggle="modal" data-target="#modalNovoUsuario">
                        <i class="fas fa-plus-circle mr-2"></i> NOVO
                    </button>

                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-uppercase font-weight-bold" style="font-size: 1rem; letter-spacing: 0.5px; border-bottom: 2px solid #eee;">
                            <th class="text-center py-3 text-dark sortable" style="width: 80px; cursor:pointer;" onclick="sortTable(this, 0)">ID <i class="fas fa-sort ml-1 opacity-50"></i></th>
                            <th class="text-left px-4 py-3 text-dark sortable" style="cursor:pointer;" onclick="sortTable(this, 1)">ADMINISTRADOR <i class="fas fa-sort ml-1 opacity-50"></i></th>
                            <th class="text-left py-3 text-dark sortable" style="cursor:pointer;" onclick="sortTable(this, 2)">CARGO <i class="fas fa-sort ml-1 opacity-50"></i></th>
                            <th class="text-left py-3 text-dark sortable" style="cursor:pointer;" onclick="sortTable(this, 3)">E-MAIL <i class="fas fa-sort ml-1 opacity-50"></i></th>
                            <th class="text-center py-3 pr-4 text-dark" style="width: 140px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr class="animate__animated animate__fadeIn">
                            <td class="text-center align-middle text-muted py-3" style="font-size: 0.85rem;">
                                {{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="align-middle px-4 py-3">
                                <div class="d-flex align-items-center">
                                    @if($user->image)
                                        <img src="{{ asset('storage/' . $user->image) }}" class="img-circle mr-3 shadow-sm border" style="width: 38px; height: 38px; object-fit: cover; opacity: {{ $user->trashed() ? '0.6' : '1' }};">
                                    @else
                                        <div class="img-circle mr-3 shadow-sm d-flex align-items-center justify-content-center {{ $user->gender === 'Feminino' ? 'bg-pink' : 'bg-primary' }}" 
                                             style="width: 38px; height: 38px; opacity: {{ $user->trashed() ? '0.6' : '1' }};">
                                            <i class="fas {{ $user->gender === 'Feminino' ? 'fa-user' : 'fa-user-tie' }} text-white" style="font-size: 1.1rem;"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-primary" style="font-size: 1.05rem;">
                                            {{ $user->name }}
                                            @if(auth()->id() == $user->id)
                                                <span class="badge badge-primary-soft ml-1" style="font-size: 0.65rem;">VOCÊ</span>
                                            @endif
                                        </div>
                                        @if($user->trashed())
                                            <span class="badge bg-danger px-2 shadow-sm" style="font-size: 0.65rem;">ACESSO REVOGADO</span>
                                        @else
                                            <span class="badge bg-success px-2 shadow-sm" style="font-size: 0.65rem;">ATIVO</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle text-left py-3">
                                <span class="text-pink font-weight-bold text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                    {{ $user->role ?? 'NÃO DEFINIDO' }}
                                </span>
                            </td>
                            <td class="align-middle text-left py-3">
                                <span class="text-muted" style="font-size: 0.95rem;">{{ $user->email }}</span>
                            </td>
                            <td class="text-center align-middle pr-4 py-3">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    @if($user->trashed())
                                        <button class="btn btn-light btn-square border-right" title="Ver Dados" onclick="viewUser('{{ $user->id }}')">
                                            <i class="fas fa-eye fa-lg text-info"></i>
                                        </button>
                                        <button class="btn btn-light btn-square" title="Restaurar Acesso" onclick="restoreUser('{{ $user->id }}', '{{ $user->name }}')">
                                            <i class="fas fa-undo fa-lg text-success"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-light btn-square border-right" title="Ver Dados" onclick="viewUser('{{ $user->id }}')">
                                            <i class="fas fa-eye fa-lg text-info"></i>
                                        </button>
                                        <button class="btn btn-light btn-square border-right" title="Editar Usuário" onclick="editUser('{{ $user->id }}')">
                                            <i class="fas fa-tools fa-lg text-warning"></i>
                                        </button>
                                        <button class="btn btn-light btn-square" title="Inativar"
                                                {{ auth()->id() == $user->id ? 'disabled' : '' }}
                                                onclick="confirmInactivation('{{ $user->id }}', '{{ $user->name }}')">
                                            <i class="fas fa-power-off fa-lg text-danger"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-user-slash fa-3x mb-3 opacity-20"></i><br>
                                Nenhum administrador localizado nestes registros.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($users->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 d-flex align-items-center justify-content-between">
            <small class="text-muted">Exibindo {{ $users->firstItem() }} a {{ $users->lastItem() }} de {{ $users->total() }} registros</small>
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 🏗️ MODAL EDITAR USUÁRIO -->
<div class="modal fade" id="modalEditarUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-warning text-dark border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-edit mr-2"></i>Editar Usuário</h5>
                <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-edit-user">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="edit_id">
                <div class="modal-body p-4">
                    <!-- 🖼️ PREVIEW TÁTICO (Novidade) -->
                    <div class="text-center mb-4">
                        <div id="edit_avatar_slot" class="d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <img id="edit_avatar_preview" src="" class="img-circle shadow-sm border border-white d-none" style="width: 100px; height: 100px; border-width: 3px !important; object-fit: cover;">
                            <div id="edit_avatar_placeholder" class="img-circle shadow-sm border border-white d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; border-width: 3px !important; background-color: #007bff;">
                                <i id="edit_avatar_icon" class="fas fa-user-tie text-white" style="font-size: 2.8rem;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <div class="form-group mb-3">
                                <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Nome Completo</label>
                                <input type="text" name="name" id="edit_name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px; font-size: 1.1rem;" placeholder="Ex: Ubiratan Silva" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group mb-3">
                                <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Gênero</label>
                                <select name="gender" id="edit_gender" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px; font-size: 1rem;" required>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Feminino">Feminino</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">E-mail Corporativo</label>
                        <input type="email" name="email" id="edit_email" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px; font-size: 1.1rem;" placeholder="nome@rastertech.com" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Patente / Cargo</label>
                        <select name="role" id="edit_role" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px; font-size: 1rem;" required>
                            <option value="">Selecione a função...</option>
                            @php $uRole = auth()->user()->role; @endphp
                            @if($uRole === 'Administrador')
                                <option value="Administrador">Administrador</option>
                            @endif
                            @if(in_array($uRole, ['Administrador', 'Gerente']))
                                <option value="Gerente">Gerente</option>
                                <option value="Suporte Técnico">Suporte Técnico</option>
                                <option value="Técnico Instalador">Técnico Instalador</option>
                            @endif
                            <option value="Cliente">Cliente</option>
                        </select>
                    </div>
                    <div class="row mt-3">
                        <div class="col-6 form-group mb-0">
                            <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Nova Senha (Opcional)</label>
                            <div class="input-group input-group-lg shadow-sm" style="background: #f8f9fa; border-radius: 8px; overflow: hidden;">
                                <input type="password" name="password" id="edit_password" class="form-control border-0 bg-transparent" placeholder="******" style="font-size: 1.1rem;">
                                <div class="input-group-append">
                                    <button class="btn btn-light border-0" type="button" onclick="togglePass('edit_password', this)">
                                        <i class="fas fa-eye text-muted"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 form-group mb-0">
                            <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Confirmar Senha</label>
                            <div class="input-group input-group-lg shadow-sm" style="background: #f8f9fa; border-radius: 8px; overflow: hidden;">
                                <input type="password" name="password_confirmation" id="edit_password_confirmation" class="form-control border-0 bg-transparent" placeholder="******" style="font-size: 1.1rem;">
                                <div class="input-group-append">
                                    <button class="btn btn-light border-0" type="button" onclick="togglePass('edit_password_confirmation', this)">
                                        <i class="fas fa-eye text-muted"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3 mb-0">
                        <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Alterar Foto de Perfil</label>
                        <input type="file" name="image" class="form-control-file p-2 border shadow-sm w-100" style="background: #f8f9fa; border-radius: 8px;">
                        <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle mr-1"></i> Formatos aceitos: JPG, PNG. Máx 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-end">
                    <button type="button" class="btn btn-link text-muted font-weight-bold mr-2" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-warning px-4 shadow-sm font-weight-bold" style="border-radius: 8px; color: #000;">SALVAR ALTERAÇÕES</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 🏗️ MODAL DOSSIÊ DO ADMINISTRADOR -->
<div class="modal fade" id="modalVerUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-id-card mr-2 text-primary"></i>Dados do Usuário</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 bg-light">
                <div class="text-center mb-4">
                    <div id="view_avatar_slot" class="d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <img id="view_avatar" src="" class="img-circle shadow-sm border border-white d-none" style="width: 100px; height: 100px; border-width: 4px !important; object-fit: cover;">
                        <div id="view_avatar_placeholder" class="img-circle shadow-sm border border-white d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; border-width: 4px !important;">
                            <i id="view_avatar_icon" class="fas text-white" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h4 id="view_name" class="font-weight-bold mt-3 mb-0"></h4>
                    <span id="view_status_badge" class="badge px-3 py-1 mt-2 shadow-sm" style="border-radius: 20px; font-size: 0.75rem;"></span>
                </div>
                
                <div class="card border-0 shadow-sm" style="border-radius: 10px;">
                    <div class="card-body p-3">
                        <div class="row mb-3">
                            <div class="col-12">
                                <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">E-mail Corporativo</label>
                                <span id="view_email" class="text-dark d-block font-weight-bold" style="font-size: 1.15rem;"></span>
                            </div>
                        </div>
                        <div class="row mb-3 border-top pt-3">
                            <div class="col-6">
                                <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Patente / Cargo</label>
                                <span id="view_role" class="badge badge-light border text-uppercase px-2 py-1" style="font-size: 0.85rem;"></span>
                            </div>
                            <div class="col-6">
                                <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Membro Desde</label>
                                <span id="view_created_at" class="text-dark d-block" style="font-size: 1.1rem; font-weight: 500;"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 bg-white">
                <button type="button" class="btn btn-secondary px-4 shadow-sm font-weight-bold" data-dismiss="modal" style="border-radius: 8px;">FECHAR</button>
            </div>
        </div>
    </div>
</div>

<!-- 🏗️ MODAL NOVO USUÁRIO -->
<div class="modal fade" id="modalNovoUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus mr-2"></i>Novo Usuário</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="form-new-user">
                @csrf
                <div class="modal-body p-4">
                    <!-- 🖼️ PREVIEW TÁTICO -->
                    <div class="text-center mb-4">
                        <div id="new_avatar_slot" class="d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <img id="new_avatar_preview" src="" class="img-circle shadow-sm border border-white d-none" style="width: 100px; height: 100px; border-width: 3px !important; object-fit: cover;">
                            <div id="new_avatar_placeholder" class="img-circle shadow-sm border border-white d-flex align-items-center justify-content-center" style="width: 100px; height: 100px; border-width: 3px !important; background-color: #007bff;">
                                <i id="new_avatar_icon" class="fas fa-user-tie text-white" style="font-size: 2.8rem;"></i>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-8">
                            <div class="form-group mb-3">
                                <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Nome Completo</label>
                                <input type="text" name="name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px; font-size: 1.1rem;" placeholder="Ex: Ubiratan Silva" required>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group mb-3">
                                <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Gênero</label>
                                <select name="gender" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px; font-size: 1rem;" required>
                                    <option value="Masculino">Masculino</option>
                                    <option value="Feminino">Feminino</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">E-mail Corporativo</label>
                        <input type="email" name="email" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px; font-size: 1.1rem;" placeholder="nome@rastertech.com" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Patente / Cargo</label>
                        <select name="role" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px; font-size: 1rem;" required>
                            <option value="">Selecione a função...</option>
                            @php $uRole = auth()->user()->role; @endphp
                            @if($uRole === 'Administrador')
                                <option value="Administrador">Administrador</option>
                            @endif
                            @if(in_array($uRole, ['Administrador', 'Gerente']))
                                <option value="Gerente">Gerente</option>
                                <option value="Suporte Técnico">Suporte Técnico</option>
                                <option value="Técnico Instalador">Técnico Instalador</option>
                            @endif
                            <option value="Cliente">Cliente</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group mb-0">
                            <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Senha ADM</label>
                            <div class="input-group input-group-lg shadow-sm" style="background: #f8f9fa; border-radius: 8px; overflow: hidden;">
                                <input type="password" name="password" id="new_password" class="form-control border-0 bg-transparent" placeholder="******" style="font-size: 1.1rem;" required>
                                <div class="input-group-append">
                                    <button class="btn btn-light border-0" type="button" onclick="togglePass('new_password', this)">
                                        <i class="fas fa-eye text-muted"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 form-group mb-0">
                            <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Confirmar</label>
                            <div class="input-group input-group-lg shadow-sm" style="background: #f8f9fa; border-radius: 8px; overflow: hidden;">
                                <input type="password" name="password_confirmation" id="new_password_confirmation" class="form-control border-0 bg-transparent" placeholder="******" style="font-size: 1.1rem;" required>
                                <div class="input-group-append">
                                    <button class="btn btn-light border-0" type="button" onclick="togglePass('new_password_confirmation', this)">
                                        <i class="fas fa-eye text-muted"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mt-3 mb-0">
                        <label class="text-muted font-weight-bold text-uppercase d-block mb-1" style="font-size: 0.8rem;">Foto de Perfil</label>
                        <input type="file" name="image" class="form-control-file p-2 border shadow-sm w-100" style="background: #f8f9fa; border-radius: 8px;">
                        <small class="text-muted mt-1 d-block"><i class="fas fa-info-circle mr-1"></i> Formatos aceitos: JPG, PNG. Máx 2MB.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">CADASTRAR USUÁRIO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 📦 FORMULÁRIO OCULTO PARA RESTORE -->
<form id="form-restore" method="POST" style="display: none;">
    @csrf
    @method('PUT')
</form>

<style>
    .badge-primary-soft { background-color: rgba(0, 123, 255, 0.1); color: #007bff; }
    .animate__animated { --animate-duration: 0.6s; }
    .btn-square { width: 40px; height: 40px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .opacity-20 { opacity: 0.2; }
    .opacity-50 { opacity: 0.5; }
</style>

@push('scripts')
<script>
    function togglePass(id, btn) {
        const input = document.getElementById(id);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    $('#form-new-user').on('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'AGUARDE...',
            text: 'Cadastrando...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '{{ route("users.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'SUCESSO',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => { location.reload(); });
                }
            },
            error: function(xhr) {
                Swal.fire('ERRO DE VALIDAÇÃO', xhr.responseJSON.message || 'Erro ao processar o registro.', 'error');
            }
        });
    });

    function editUser(id) {
        $.ajax({
            url: '/users/' + id,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    const user = response.user;
                    $('#edit_id').val(id);
                    $('#edit_name').val(user.name);
                    $('#edit_email').val(user.email);
                    $('#edit_role').val(user.role);
                    $('#edit_gender').val(user.gender);
                    
                    // 🔄 Atualizar Preview do Modal de Edição
                    if(user.has_photo) {
                        $('#edit_avatar_preview').attr('src', user.avatar).removeClass('d-none');
                        $('#edit_avatar_placeholder').removeClass('d-flex').addClass('d-none');
                    } else {
                        $('#edit_avatar_preview').addClass('d-none');
                        $('#edit_avatar_placeholder').removeClass('d-none').addClass('d-flex');
                        $('#edit_avatar_placeholder').css('background-color', user.gender === 'Feminino' ? '#e83e8c' : '#007bff');
                        $('#edit_avatar_icon').attr('class', 'fas ' + (user.gender === 'Feminino' ? 'fa-user' : 'fa-user-tie') + ' text-white');
                    }
                    
                    $('#modalEditarUsuario').modal('show');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Não foi possível carregar os dados para edição.';
                Swal.fire('ERRO', msg, 'error');
            }
        });
    }

    // 🎨 LÓGICA DE PREVIEW DINÂMICO (Gênero)
    $('#edit_gender').on('change', function() {
        const gender = $(this).val();
        const placeholder = $('#edit_avatar_placeholder');
        const icon = $('#edit_avatar_icon');
        
        // Só atualiza se NÃO tiver foto ativa no momento
        if($('#edit_avatar_preview').is(':hidden')) {
            placeholder.css('background-color', gender === 'Feminino' ? '#e83e8c' : '#007bff');
            icon.attr('class', 'fas ' + (gender === 'Feminino' ? 'fa-user' : 'fa-user-tie') + ' text-white');
        }
    });

    $('#form-edit-user').on('submit', function(e) {
        e.preventDefault();
        const id = $('#edit_id').val();
        const formData = new FormData(this);
        
        Swal.fire({
            title: 'AGUARDE...',
            text: 'Atualizando...',
            allowOutsideClick: false,
            didOpen: () => { Swal.showLoading(); }
        });

        $.ajax({
            url: '/users/' + id,
            type: 'POST', // Usamos POST com spoofing de PUT (FormData não suporta PUT nativo facilmente em Laravel sem _method)
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'ATUALIZADO',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => { location.reload(); });
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao processar as alterações.';
                Swal.fire('ERRO', msg, 'error');
            }
        });
    });

    // 🎨 PREVIEW DE GÊNERO NO MODAL NOVO
    $('select[name="gender"]').on('change', function() {
        const modal = $(this).closest('.modal');
        const gender = $(this).val();
        const placeholder = modal.find('.img-circle[id$="_placeholder"]');
        const icon = modal.find('.fas[id$="_icon"]');
        const preview = modal.find('img[id$="_preview"]');
        
        // Só atualiza se NÃO tiver foto ativa no momento
        if(preview.hasClass('d-none')) {
            placeholder.css('background-color', gender === 'Feminino' ? '#e83e8c' : '#007bff');
            icon.attr('class', 'fas ' + (gender === 'Feminino' ? 'fa-user' : 'fa-user-tie') + ' text-white');
        }
    });

    // 📸 PREVIEW DE ARQUIVO INSTANTÂNEO
    $('input[type="file"][name="image"]').on('change', function() {
        const modal = $(this).closest('.modal');
        const input = this;
        const preview = modal.find('img[id$="_preview"]');
        const placeholder = modal.find('.img-circle[id$="_placeholder"]');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.attr('src', e.target.result).removeClass('d-none');
                placeholder.removeClass('d-flex').addClass('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    });

    function viewUser(id) {
        $.ajax({
            url: '/users/' + id,
            type: 'GET',
            success: function(response) {
                if(response.success) {
                    const user = response.user;
                    
                    if(user.has_photo) {
                        $('#view_avatar').attr('src', user.avatar).removeClass('d-none');
                        $('#view_avatar_placeholder').removeClass('d-flex').addClass('d-none');
                    } else {
                        $('#view_avatar').addClass('d-none');
                        $('#view_avatar_placeholder').removeClass('d-none').addClass('d-flex');
                        $('#view_avatar_placeholder').css('background-color', user.gender === 'Feminino' ? '#e83e8c' : '#007bff');
                        $('#view_avatar_icon').attr('class', 'fas ' + (user.gender === 'Feminino' ? 'fa-user' : 'fa-user-tie') + ' text-white');
                    }

                    $('#view_name').text(user.name);
                    $('#view_email').text(user.email);
                    $('#view_role').text(user.role);
                    $('#view_created_at').text(user.created_at);
                    
                    const badge = $('#view_status_badge');
                    badge.text(user.status);
                    if(user.status === 'ATIVO') {
                        badge.removeClass('badge-danger').addClass('badge-success');
                    } else {
                        badge.removeClass('badge-success').addClass('badge-danger');
                    }
                    
                    $('#modalVerUsuario').modal('show');
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Não foi possível carregar os detalhes do usuário.';
                Swal.fire('ERRO', msg, 'error');
            }
        });
    }

    function confirmInactivation(id, name) {
        Swal.fire({
            title: '<span style="font-weight: 400; font-size: 1.1rem;">Deseja inativar o acesso de </span><br><strong>' + name + '</strong>?',
            html: '<small class="text-muted">O administrador perderá acesso instantâneo ao quartel-general do sistema.</small>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'SIM, INATIVAR',
            cancelButtonText: 'CANCELAR'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '/users/' + id,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'ACESSO REMOVIDO',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => { location.reload(); });
                    },
                    error: function(xhr) {
                        const msg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Erro ao processar solicitação.';
                        Swal.fire('ERRO', msg, 'error');
                    }
                });
            }
        });
    }

    function restoreUser(id, name) {
        Swal.fire({
            title: '<span style="font-weight: 400; font-size: 1.1rem;">Reativar acesso de </span><br><strong>' + name + '</strong>?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'SIM, REATIVAR',
            cancelButtonText: 'CANCELAR'
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('form-restore');
                form.action = '/users/' + id + '/restore';
                form.submit();
            }
        });
    }

    /**
     * 🟢 LÓGICA DE ORDENAÇÃO DE TABELA FRONT-END
     */
    function sortTable(th, colIndex) {
        const table = th.closest('table');
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr:not(.text-center.text-muted)'));
        
        // Verifica se a tabela não tá vazia
        if (rows.length === 0 || rows[0].querySelector('td[colspan]')) return;

        // Limpar ícones das outras colunas
        table.querySelectorAll('th i.fas').forEach(icon => {
            icon.className = 'fas fa-sort ml-1 opacity-50';
        });

        // Alterna direção
        let asc = th.getAttribute('data-asc') === 'true';
        asc = !asc;
        th.setAttribute('data-asc', asc);
        
        const icon = th.querySelector('i');
        if(icon) {
            icon.className = asc ? 'fas fa-sort-up ml-1 text-primary' : 'fas fa-sort-down ml-1 text-primary';
        }

        const comparer = (a, b) => {
            // Pega o texto da coluna, remove crases e extras do layout
            let v1 = a.children[colIndex].innerText.replace(/\n|VOCÊ|ATIVO|ACESSO REVOGADO/g, '').trim();
            let v2 = b.children[colIndex].innerText.replace(/\n|VOCÊ|ATIVO|ACESSO REVOGADO/g, '').trim();
            
            // Tratamento Numérico pro ID (colIndex 0)
            if (v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2)) {
                return asc ? v1 - v2 : v2 - v1;
            }
            return asc ? v1.localeCompare(v2) : v2.localeCompare(v1);
        };

        rows.sort(comparer).forEach(tr => tbody.appendChild(tr));
    }
</script>
@endpush
@endsection
