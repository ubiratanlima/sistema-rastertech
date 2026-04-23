@extends('layouts.app')

@section('title', 'Credenciais Apps | Rastertech')

@section('content')
<div class="container-fluid">
    
    <!-- 🏗️ CABEÇALHO DA PÁGINA (Padrão Ouro) -->
    <div class="row mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-sm-6">
            <h1 class="m-0 font-weight-bold text-dark" style="font-size: 2.2rem;">
                <i class="fas fa-key mr-2 text-teal"></i>Credenciais Apps
            </h1>
            <p class="text-muted small mb-0 d-none d-sm-block">Gestão de acessos externos para portais e aplicativos de monitoramento.</p>
        </div>
    </div>

    <!-- 📊 CARD PRINCIPAL -->
    <div class="card card-outline shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden; border-top: 3px solid #20c997;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title font-weight-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-shield-alt mr-2 text-teal"></i>Portaria de Acessos
            </h3>
            
            <div class="card-tools ml-auto">
                <form action="/customer-sub-users" method="GET" class="d-flex align-items-center">
                    <!-- 🏢 SELETOR DE CLIENTE (ADMIN/GESTOR) -->
                    @if($isAdminLevel)
                    <div class="mr-4 d-flex align-items-center border-right pr-4">
                        <label class="small font-weight-bold text-muted mr-2 mb-0">CLIENTE:</label>
                        <select name="customer_id" class="form-control form-control-sm select2" onchange="this.form.submit()" style="width: 220px; font-weight: bold; border-radius: 6px;">
                            <option value="">TODOS OS CLIENTES</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ $selectedCustomerId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <!-- 🔍 PESQUISAR -->
                    <div class="input-group input-group-sm" style="width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Procurar usuário..." value="{{ $search }}">
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
                        <select name="view" class="form-control form-control-sm" onchange="this.form.submit()" style="width: 130px; font-weight: bold; border-radius: 6px;">
                            <option value="active" {{ $view == 'active' ? 'selected' : '' }}>🟢 ATIVOS</option>
                            <option value="trash" {{ $view == 'trash' ? 'selected' : '' }}>⛔ INATIVOS</option>
                        </select>
                    </div>

                    <!-- ➕ NOVA CREDENCIAL -->
                    <button type="button" class="btn btn-sm btn-teal ml-4 px-3 font-weight-bold shadow-sm text-white" 
                            style="border-radius: 6px; height: 31px; display: flex; align-items: center; background-color: #20c997;" 
                            data-toggle="modal" data-target="#modalNovoAcesso">
                        <i class="fas fa-plus-circle mr-2"></i> NOVA CREDENCIAL
                    </button>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="subUserTable">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02); font-size: 1rem;">
                            <th style="width: 80px;">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'id', 'direction' => ($sort == 'id' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    ID <i class="fas fa-sort{{ $sort == 'id' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th class="text-left px-4">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'name', 'direction' => ($sort == 'name' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    IDENTIFICAÇÃO / USUÁRIO <i class="fas fa-sort{{ $sort == 'name' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th style="width: 180px;">PLATAFORMA / SISTEMA</th>
                            <th style="width: 180px;">
                                <a href="?{{ http_build_query(array_merge(request()->query(), ['sort' => 'external_username', 'direction' => ($sort == 'external_username' && $direction == 'asc') ? 'desc' : 'asc'])) }}" class="text-dark">
                                    USERNAME (APP) <i class="fas fa-sort{{ $sort == 'external_username' ? ($direction == 'asc' ? '-up' : '-down') : '' }} ml-1 opacity-50"></i>
                                </a>
                            </th>
                            <th style="width: 120px;">STATUS</th>
                            <th style="width: 140px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subUsers as $u)
                        <!-- 🏁 LINHA MASTER -->
                        <tr style="transition: background 0.3s; height: 70px;">
                            <td class="align-middle text-center text-muted font-weight-bold small cursor-pointer" data-toggle="collapse" data-target="#row-sub-{{ $u->id }}">{{ $u->id }}</td>
                            <td class="align-middle px-4 cursor-pointer" data-toggle="collapse" data-target="#row-sub-{{ $u->id }}">
                                <div class="d-flex align-items-center">
                                    <div class="mr-3 bg-teal text-white rounded-circle d-flex align-items-center justify-content-center font-weight-bold shadow-sm" style="width: 38px; height: 38px; background: #20c997;">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-weight-bold text-dark" style="font-size: 11pt;">{{ $u->name }}</div>
                                        <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 7.5pt;">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>

                            <td class="text-center align-middle cursor-pointer font-weight-bold" data-toggle="collapse" data-target="#row-sub-{{ $u->id }}">
                                <span class="text-muted small text-uppercase d-block" style="font-size: 0.65rem;">SISTEMA</span>
                                <div class="text-dark">{{ $u->platform->name ?? 'N/A' }}</div>
                            </td>
                            <td class="text-center align-middle cursor-pointer font-weight-bold" data-toggle="collapse" data-target="#row-sub-{{ $u->id }}">
                                <code>{{ $u->external_username }}</code>
                            </td>
                            <td class="text-center align-middle">
                                @if($u->trashed())
                                    <span class="badge badge-danger px-3 py-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">INATIVO</span>
                                @elseif(!$u->email_verified_at)
                                    <span class="badge badge-warning px-3 py-1 text-uppercase text-white" style="font-size: 0.7rem; letter-spacing: 0.5px; background-color: #f39c12;">AGUARDANDO E-MAIL</span>
                                @elseif(!$u->access_validated)
                                    <span class="badge badge-info px-3 py-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">CONTA ATIVADA</span>
                                @else
                                    <span class="badge badge-success px-3 py-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">ACESSO VALIDADO</span>
                                @endif
                            </td>
                            <td class="text-center align-middle px-4">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden; border: 1px solid #dee2e6;">
                                    @if($u->trashed())
                                        <button class="btn btn-light btn-square-sm" onclick="confirmRestore({{ $u->id }}, '{{ $u->name }}')" title="Reativar Acesso">
                                            <i class="fas fa-undo text-success"></i>
                                        </button>
                                        <form id="form-restore-{{ $u->id }}" action="{{ route('customer-sub-users.restore', $u->id) }}" method="POST" class="d-none">@csrf @method('PUT')</form>
                                    @else
                                         <button class="btn btn-light btn-square-sm border-right" onclick="showCredentials(this)" 
                                                data-name="{{ $u->name }}" 
                                                data-customer-code="{{ $u->customer->code ?? 'N/A' }}"
                                                data-platform="{{ $u->platform->name ?? 'INDEPENDENTE' }}"
                                                data-url="{{ $u->platform->url ?? '#' }}"
                                                data-android="{{ $u->platform->app_android_url ?? '' }}"
                                                data-ios="{{ $u->platform->app_ios_url ?? '' }}"
                                                data-username="{{ $u->external_username }}" 
                                                data-password="{{ $u->external_password }}"
                                                title="Ver Carteirinha de Acesso">
                                            <i class="fas fa-id-card text-teal"></i>
                                        </button>

                                         <button class="btn btn-light btn-square-sm border-right" onclick="editSubUser(this)" 
                                                data-id="{{ $u->id }}" data-name="{{ $u->name }}" data-email="{{ $u->email }}"
                                                data-platform="{{ $u->platform_id }}"
                                                data-username="{{ $u->external_username }}" data-customer="{{ $u->customer_id }}"
                                                data-role="{{ $u->role }}" title="Editar Case">
                                            <i class="fas fa-tools text-warning"></i>
                                        </button>
                                        <button class="btn btn-light btn-square-sm" onclick="confirmDelete({{ $u->id }}, '{{ $u->name }}')" title="Inativar">
                                            <i class="fas fa-user-slash text-danger"></i>
                                        </button>
                                    @endif
                                </div>
                                <form id="form-delete-{{ $u->id }}" action="{{ route('customer-sub-users.destroy', $u->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                            </td>
                        </tr>

                        <!-- 🛠️ DETALHE ACORDEÃO -->
                        <tr class="detail-row">
                            <td colspan="6" class="p-0 border-0">
                                <div id="row-sub-{{ $u->id }}" class="collapse" data-parent="#subUserTable">
                                    <div class="bg-light p-4 shadow-inner" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);">
                                        <div class="row align-items-center">
                                            <div class="col-md-3 text-center">
                                                <i class="fas fa-user-lock fa-4x text-teal opacity-20 mb-3"></i>
                                                <h6 class="font-weight-bold text-teal mb-0">SEGURANÇA ATIVA</h6>
                                            </div>
                                            <div class="col-md-9 border-left pl-4">
                                                <div class="row">
                                                    <div class="col-sm-4 mb-3">
                                                        <label class="small text-muted font-weight-bold text-uppercase d-block">Cargo/Role</label>
                                                        <span class="font-weight-bold text-dark">{{ $u->role ?: 'Padrão' }}</span>
                                                    </div>
                                                    <div class="col-sm-4 mb-3 text-center">
                                                        <label class="small text-muted font-weight-bold text-uppercase d-block">E-mail Validado em</label>
                                                        <span class="font-weight-bold text-muted small">---</span>
                                                    </div>
                                                    <div class="col-sm-4 mb-3 text-right">
                                                        <label class="small text-muted font-weight-bold text-uppercase d-block">Cadastrado em</label>
                                                        <span class="font-weight-bold text-dark">{{ $u->created_at->format('d/m/Y') }}</span>
                                                    </div>
                                                    <div class="col-12 mt-2">
                                                        <div class="row bg-white border p-3 rounded" style="border-radius: 12px !important;">
                                                            <div class="col-6">
                                                                <label class="small text-muted font-weight-bold text-uppercase mb-0">Login de Acesso (ERP)</label>
                                                                <div class="h6 mb-0 font-weight-bold">{{ $u->external_username }}</div>
                                                            </div>
                                                            <div class="col-6 text-right">
                                                                <label class="small text-muted font-weight-bold text-uppercase mb-0">Status de Acesso</label>
                                                                <div class="h6 mb-0 text-success"><i class="fas fa-check-circle mr-1"></i> VALIDADA</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                            <td colspan="5" class="text-center py-5">
                                <i class="fas fa-id-card-alt fa-3x text-muted mb-3 opacity-20"></i>
                                <h4 class="text-muted font-weight-bold">Nenhum usuário externo encontrado</h4>
                            </td>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($subUsers->hasPages())
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $subUsers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 🏗️ MODAL NOVO ACESSO -->
<div class="modal fade" id="modalNovoAcesso" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white border-0 py-3" style="background-color: #20c997;">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-lock mr-2"></i>Gerar Credencial Externa</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('customer-sub-users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4 bg-white">
                    <div class="row">
                        <div class="col-12 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Vincular ao Cliente</label>
                            <select name="customer_id" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" required {{ !$isAdminLevel ? 'readonly' : '' }}>
                                @if(!$isAdminLevel)
                                    <option value="{{ auth()->user()->customer_id }}">{{ auth()->user()->customer->name ?? 'Minha Empresa' }}</option>
                                @else
                                    <option value="">Selecione o Cliente...</option>
                                    @foreach($customers as $c)
                                        <option value="{{ $c->id }}" {{ $selectedCustomerId == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->code }})</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-user mr-1"></i> Nome Completo</label>
                            <input type="text" name="name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="Ex: João da Silva" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-desktop mr-1"></i> Plataforma / Sistema</label>
                            <select name="platform_id" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" required>
                                <option value="">Selecione...</option>
                                @foreach($platforms as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-envelope mr-1"></i> E-mail de Contato</label>
                            <input type="email" name="email" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="joao@empresa.com" required>
                        </div>
                        <div class="col-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-id-badge mr-1"></i> Usuário (ID)</label>
                            <input type="text" name="external_username" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="joao.silva" required>
                        </div>
                        <div class="col-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold"><i class="fas fa-key mr-1"></i> Senha Inicial</label>
                            <input type="password" name="external_password" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="******" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn text-white px-4 shadow-sm font-weight-bold" style="border-radius: 8px; background-color: #20c997;">ATIVAR ACESSO</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // 📝 EDITAR CREDENCIAL
    window.editSubUser = function(el) {
        const d = $(el).data();
        
        let platformOpts = '';
        const platforms = {!! json_encode($platforms->mapWithKeys(fn($p) => [$p->id => $p->name])) !!};
        Object.entries(platforms).forEach(([pid, pname]) => {
            platformOpts += `<option value="${pid}" ${pid == d.platform ? 'selected' : ''}>${pname}</option>`;
        });

        Swal.fire({
            title: '<i class="fas fa-user-edit mr-2 text-teal"></i> EDITAR CREDENCIAL',
            width: '500px',
            html: `
                <div class="text-left px-2">
                    <div class="form-group mb-3">
                        <label class="text-xs text-uppercase text-muted font-weight-bold"><i class='fas fa-user mr-1'></i> Nome Completo</label>
                        <input type="text" id="edit_name" class="form-control" value="${d.name}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs text-uppercase text-muted font-weight-bold"><i class='fas fa-desktop mr-1'></i> Plataforma</label>
                        <select id="edit_platform_id" class="form-control">${platformOpts}</select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs text-uppercase text-muted font-weight-bold"><i class='fas fa-envelope mr-1'></i> E-mail</label>
                        <input type="email" id="edit_email" class="form-control" value="${d.email}">
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs text-uppercase text-muted font-weight-bold"><i class='fas fa-id-badge mr-1'></i> Usuário APP</label>
                        <input type="text" id="edit_username" class="form-control" value="${d.username}">
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs text-uppercase text-muted font-weight-bold"><i class='fas fa-key mr-1'></i> Nova Senha (deixe em branco se não quiser trocar)</label>
                        <div class="input-group">
                            <input type="password" id="edit_password" class="form-control" placeholder="*******">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" onclick="var i=$('#edit_password');i.attr('type',i.attr('type')==='password'?'text':'password');$(this).find('i').toggleClass('fa-eye fa-eye-slash')"><i class="fas fa-eye"></i></button>
                            </div>
                        </div>
                    </div>
                </div>`,
            showCancelButton: true,
            confirmButtonText: 'SALVAR ALTERAÇÕES',
            confirmButtonColor: '#20c997',
            preConfirm: () => {
                const data = {
                    name: $('#edit_name').val(),
                    email: $('#edit_email').val(),
                    platform_id: $('#edit_platform_id').val(),
                    external_username: $('#edit_username').val(),
                    external_password: $('#edit_password').val(),
                    customer_id: d.customer,
                    _token: '{{ csrf_token() }}'
                };
                
                return $.ajax({
                    url: '/customer-sub-users/' + d.id,
                    method: 'PUT',
                    data: data
                }).catch(err => Swal.showValidationMessage(err.responseJSON?.message || 'Erro ao atualizar.'));
            }
        }).then(r => r.isConfirmed && location.reload());
    }

    // 🗑️ EXCLUSÃO
    window.confirmDelete = function(id, name) {
        Swal.fire({
            title: 'Excluir ACESSO?',
            text: `A credencial de "${name}" será revogada permanentemente.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Sim, Excluir!'
        }).then((res) => { if(res.isConfirmed) document.getElementById(`form-delete-${id}`).submit(); });
    }

    // ♻️ RESTAURAÇÃO
    window.confirmRestore = function(id, name) {
        Swal.fire({
            title: 'Reativar ACESSO?',
            text: `Deseja restaurar as credenciais de "${name}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Sim, Reativar!'
        }).then((res) => { if(res.isConfirmed) document.getElementById(`form-restore-${id}`).submit(); });
    }

    // 👁️ VER CREDENCIAIS (CARTEIRINHA TÁTICA)
    window.showCredentials = function(el) {
        const d = $(el).data();
        
        let androidBtn = d.android ? `<a href="${d.android}" target="_blank" class="btn btn-dark btn-sm rounded-pill px-3 mr-2 d-inline-flex align-items-center shadow-sm" style="border: 2px solid #3DDC84; background: #1a1a1a;">
                                        <i class="fab fa-google-play mr-2" style="color: #3DDC84; font-size: 1.1rem;"></i>
                                        <span class="font-weight-bold" style="font-size: 0.7rem; letter-spacing: 0.5px; color: white;">APP ANDROID</span>
                                      </a>` : '';
        let iosBtn = d.ios ? `<a href="${d.ios}" target="_blank" class="btn btn-dark btn-sm rounded-pill px-3 d-inline-flex align-items-center shadow-sm" style="border: 2px solid #A2AAAD; background: #1a1a1a;">
                                <i class="fab fa-apple mr-2" style="color: #A2AAAD; font-size: 1.1rem;"></i>
                                <span class="font-weight-bold" style="font-size: 0.7rem; letter-spacing: 0.5px; color: white;">APP IPHONE</span>
                              </a>` : '';

        Swal.fire({
            title: '<i class="fas fa-id-badge text-teal mr-2"></i>CARTÃO DE ACESSO',
            width: '450px',
            background: '#f4f6f9',
            html: `
                <div class="access-card shadow-lg animate__animated animate__flipInY" style="border-radius: 15px; overflow: hidden; background: #fff; border: 1px solid #dee2e6;">
                    <!-- HEADER DA CARTEIRA -->
                    <div class="px-4 py-4 text-center" style="background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);">
                        <div class="mb-3">
                             <img src="{{ asset('img/logo_rastertech.png') }}" style="height: 52px; width: auto; filter: drop-shadow(0 2px 5px rgba(0,0,0,0.1));">
                        </div>
                        <h5 class="text-white font-weight-bold mb-0 text-uppercase" style="letter-spacing: 2px; font-size: 1.6rem;">${d.customerCode}</h5>
                        <div class="badge badge-light px-3 py-1 font-weight-bold mt-1 shadow-sm" style="font-size: 0.7rem; border-radius: 20px; color: #17a2b8;">
                            <i class="fas fa-shield-alt mr-1"></i> CÓDIGO DE SEGURANÇA
                        </div>
                    </div>
                    
                    <!-- CORPO DA CARTEIRA -->
                    <div class="p-4 text-left">
                        <!-- BOTÃO ACESSO WEB (Substituindo Texto) -->
                        <div class="mb-4">
                            <a href="${d.url}" target="_blank" class="btn btn-block shadow-sm font-weight-bold py-3 d-flex align-items-center justify-content-center" 
                               style="background: #20c997; color: white; border-radius: 12px; border: none; font-size: 0.9rem; transition: all 0.3s; transform: scale(1);"
                               onmouseover="this.style.transform='scale(1.02)'; this.style.filter='brightness(1.1)';"
                               onmouseout="this.style.transform='scale(1)';"
                            >
                                <i class="fas fa-desktop mr-2"></i> ACESSAR SISTEMA WEB
                            </a>
                        </div>

                        <div class="bg-light p-3 rounded border mb-4" style="border-radius: 12px !important; border-left: 5px solid #20c997 !important;">
                            <div class="mb-3">
                                <label class="small text-muted font-weight-bold text-uppercase mb-1 d-block"><i class="fas fa-user-circle mr-1 text-teal"></i> USUÁRIO</label>
                                <div class="d-flex align-items-center bg-white border p-2 rounded shadow-sm">
                                    <span class="flex-grow-1 font-weight-bold text-dark" style="font-family: monospace; font-size: 1rem;">${d.username}</span>
                                    <i class="fas fa-copy text-teal cursor-pointer px-2" onclick="copyToClipboard('${d.username}')"></i>
                                </div>
                            </div>
                            <div>
                                <label class="small text-muted font-weight-bold text-uppercase mb-1 d-block"><i class="fas fa-key mr-1 text-teal"></i> SENHA</label>
                                <div class="d-flex align-items-center bg-white border p-2 rounded shadow-sm">
                                    <span class="flex-grow-1 font-weight-bold text-dark" style="font-family: monospace; font-size: 1rem;">${d.password}</span>
                                    <i class="fas fa-copy text-teal cursor-pointer px-2" onclick="copyToClipboard('${d.password}')"></i>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <label class="small text-muted font-weight-bold text-uppercase mb-2 d-block">Baixar Aplicativo</label>
                            <div class="d-flex justify-content-center">
                                ${androidBtn}
                                ${iosBtn}
                            </div>
                            ${(!androidBtn && !iosBtn) ? '<span class="text-muted small italic">Links não configurados na plataforma</span>' : ''}
                        </div>
                    </div>
                    
                    <!-- NOVO RODAPÉ COM MARCA E SLOGAN -->
                    <div class="py-3 bg-light border-top text-center d-flex align-items-center justify-content-center">
                        <div style="width: 8px; height: 8px; background: #20c997; border-radius: 50%; margin-right: 8px; box-shadow: 0 0 5px rgba(32, 201, 151, 0.5);"></div>
                        <small class="text-muted font-weight-bold" style="font-size: 0.65rem; letter-spacing: 0.8px;">
                            RASTERTECH - RASTREAMENTO e MONITORAMENTO VEICULAR
                        </small>
                    </div>
                </div>
            `,
            showConfirmButton: true,
            confirmButtonText: 'SALVAR E FECHAR',
            confirmButtonColor: '#20c997'
        });
    }

    window.copyToClipboard = function(text) {
        navigator.clipboard.writeText(text).then(() => {
            toastr.success('Copiado para a área de transferência!');
        });
    }
</script>
@endpush

<style>
    .btn-square-sm { width: 34px; height: 34px; display: inline-flex; align-items: center; justify-content: center; padding: 0; }
    .shadow-inner { box-shadow: inset 0 2px 4px rgba(0,0,0,0.05); }
    .text-teal { color: #20c997 !important; }
    .bg-teal { background-color: #20c997 !important; }
    
    .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
    .dark-mode code { background: #16213e !important; color: #20c997 !important; border: 1px solid #2d2d44 !important; }
</style>
@endsection
