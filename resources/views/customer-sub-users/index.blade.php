@extends('layouts.app')

@section('title', 'Gestão de Acessos')

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

    <!-- ⚓ CABEÇALHO PADRÃO OURO (8:4) -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center overflow-hidden flex-nowrap">
        <div class="col-8 col-sm-6 p-0 p-sm-2">
            <h1 class="m-0 text-bold d-none d-sm-block" style="font-size: 2.2rem;">
                <i class="fas fa-users-cog mr-2 text-teal"></i>Credenciais de Aplicativos
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-users-cog mr-1 text-teal"></i>Credenciais
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block">Gerenciamento de acessos secundários e aplicativos de monitoramento.</p>
        </div>
        <div class="col-4 col-sm-6 text-right p-0 pr-sm-2">
            <button class="btn btn-teal shadow-sm px-3 py-2 text-white" style="border-radius: 8px; font-weight: 600; background-color: #20c997; border-color: #20c997;" data-toggle="modal" data-target="#modalNovoAcesso">
                <i class="fas fa-user-plus mr-sm-2"></i>
                <span class="d-none d-sm-inline">Novo Acesso</span>
            </button>
        </div>
    </div>

    <!-- 🛠️ TABELA DE PORTARIA -->
    <div class="card card-outline shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden; border-top: 3px solid #20c997;">
        <div class="card-header border-0 bg-transparent px-4 py-3">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-id-card mr-2 text-teal"></i>Usuários do Portal
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center text-sm" style="background-color: rgba(0,0,0,0.02);">
                            <th class="text-left px-4">USUÁRIO</th>
                            <th class="text-left">CADERNO / CLIENTE</th>
                            <th class="d-none d-md-table-cell">USERNAME</th>
                            <th class="d-none d-md-table-cell">LOGIN EXTERNO</th>
                            <th style="width: 100px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subUsers as $user)
                        <tr>
                            <td class="align-middle px-4">
                                <div class="text-bold text-teal">{{ $user->name }}</div>
                                <small class="text-muted text-xs">{{ $user->email }}</small>
                            </td>
                            <td class="align-middle">
                                <span class="badge badge-light border px-2 py-1">
                                    <i class="fas fa-briefcase mr-1 text-muted"></i>{{ $user->customer->name }}
                                </span>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span>{{ $user->external_username }}</span>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="badge {{ $user->external_password ? 'badge-success' : 'badge-danger' }} px-2 py-1">
                                    {{ $user->external_password ? 'CONFIGURADO' : 'PENDENTE' }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <form action="{{ route('customer-sub-users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Inativar este acesso permanentemente?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-xs btn-outline-danger shadow-sm" style="border-radius: 6px;"><i class="fas fa-user-slash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Nenhum acesso externo registrado no ERP.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($subUsers->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
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
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Vincular ao Cliente</label>
                            <select name="customer_id" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" required>
                                <option value="">Selecione o Cliente...</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Nome Completo</label>
                            <input type="text" name="name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="Ex: João da Silva (Motorista)" required>
                        </div>
                        <div class="col-12 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">E-mail de Contato</label>
                            <input type="email" name="email" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="joao@empresa.com" required>
                        </div>
                        <div class="col-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Usuário (ID)</label>
                            <input type="text" name="external_username" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="joao.silva" required>
                        </div>
                        <div class="col-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Senha Inicial</label>
                            <input type="password" name="external_password" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="******" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn text-white px-4 shadow-sm font-weight-bold" style="border-radius: 8px; background-color: #20c997;">ATIVAR ACESSO</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
    .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
    .dark-mode code { background: #16213e !important; color: #20c997 !important; border: 1px solid #2d2d44 !important; }
    .dark-mode .modal-content { background: #1a1a2e; border: 1px solid #2d2d44; }
    .dark-mode .modal-body input, .dark-mode .modal-body select { 
        background: #16213e !important; color: #fff !important; border: 1px solid #2d2d44 !important; 
    }
    .dark-mode .modal-footer { background: #16213e !important; }
    
    .text-teal { color: #20c997 !important; }
    .animate__animated { --animate-duration: 0.6s; }
</style>
@endsection
