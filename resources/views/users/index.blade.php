@extends('layouts.app')

@section('title', 'Gestão de Administradores')

@section('content')
<div class="container-fluid">
    <!-- 🔔 ALERTAS DE OPERAÇÃO -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible animate__animated animate__fadeInDown">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-check"></i> Sucesso!</h5>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible animate__animated animate__shakeX">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            <h5><i class="icon fas fa-ban"></i> Bloqueio!</h5>
            {{ session('error') }}
        </div>
    @endif

    <!-- ⚓ CABEÇALHO PADRÃO OURO (8:4) -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center overflow-hidden flex-nowrap">
        <div class="col-8 col-sm-6 p-0 p-sm-2">
            <h1 class="m-0 text-bold d-none d-sm-block" style="font-size: 2.2rem;">
                <i class="fas fa-user-shield mr-2 text-primary"></i>Controle Central
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-user-shield mr-1 text-primary"></i>Donos
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block">Gestão de administradores e comandantes do sistema.</p>
        </div>
        <div class="col-4 col-sm-6 text-right p-0 pr-sm-2">
            <button class="btn btn-primary shadow-sm px-3 py-2" style="border-radius: 8px; font-weight: 600;" data-toggle="modal" data-target="#modalNovoUsuario">
                <i class="fas fa-plus mr-sm-2"></i>
                <span class="d-none d-sm-inline">Novo Comandante</span>
            </button>
        </div>
    </div>

    <!-- 🛠️ TABELA DE COMANDO -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-users mr-2 text-primary"></i>Equipe Administrativa
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center text-sm" style="background-color: rgba(0,0,0,0.02);">
                            <th class="text-left px-4">ADMIMISTRADOR</th>
                            <th class="text-left">CARGO</th>
                            <th class="text-left">E-MAIL</th>
                            <th style="width: 100px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td class="align-middle px-4">
                                <div class="d-flex align-items-center">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random&color=fff" class="img-circle mr-3 border" style="width: 35px; height: 35px;">
                                    <div>
                                        <div class="text-bold {{ auth()->id() == $user->id ? 'text-primary' : '' }}">
                                            {{ $user->name }}
                                            @if(auth()->id() == $user->id)
                                                <span class="badge badge-primary text-xs ml-1">VOCÊ</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle text-left">
                                <span class="badge badge-light border text-uppercase font-weight-bold" style="letter-spacing: 0.5px;">{{ $user->role ?? 'NÃO DEFINIDO' }}</span>
                            </td>
                            <td class="align-middle text-left">
                                <span class="text-muted">{{ $user->email }}</span>
                            </td>
                            <td class="text-center align-middle">
                                <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Remover acesso administrativo deste usuário?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger shadow-sm" style="border-radius: 6px;" {{ auth()->id() == $user->id ? 'disabled' : '' }}>
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="text-center py-5 text-muted">Nenhum administrador detectado no quartel-general.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($users->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 🏗️ MODAL NOVO USUÁRIO -->
<div class="modal fade" id="modalNovoUsuario" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-user-plus mr-2"></i>Novo Comandante</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-xs text-uppercase text-muted font-weight-bold">Nome Completo</label>
                        <input type="text" name="name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="Ex: Ubiratan Silva" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs text-uppercase text-muted font-weight-bold">E-mail Corporativo</label>
                        <input type="email" name="email" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="nome@rastertech.com" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="text-xs text-uppercase text-muted font-weight-bold">Patente / Cargo</label>
                        <select name="role" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" required>
                            <option value="">Selecione a função...</option>
                            <option value="Administrador Master">Administrador Master</option>
                            <option value="Técnico de Campo">Técnico de Campo</option>
                            <option value="Suporte ao Cliente">Suporte ao Cliente</option>
                            <option value="Gestor Comercial">Gestor Comercial</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Senha ADM</label>
                            <input type="password" name="password" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="******" required>
                        </div>
                        <div class="col-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Confirmar</label>
                            <input type="password" name="password_confirmation" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="******" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">ATIVAR ACESSO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
    .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
    .dark-mode .modal-content { background: #1a1a2e; border: 1px solid #2d2d44; }
    .dark-mode .modal-body input { background: #16213e !important; color: #fff !important; border: 1px solid #2d2d44 !important; }
    .dark-mode .modal-footer { background: #16213e !important; }
    
    .animate__animated { --animate-duration: 0.6s; }
</style>
@endsection
