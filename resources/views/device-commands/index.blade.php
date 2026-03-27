@extends('layouts.app')

@section('title', 'Gestão de Comandos SMS')

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
                <i class="fas fa-comment-dots mr-2 text-indigo"></i>Comandos SMS
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-comment-dots mr-1 text-indigo"></i>Scripts
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block">Biblioteca de templates para automação de hardware.</p>
        </div>
        <div class="col-4 col-sm-6 text-right p-0 pr-sm-2">
            <button class="btn btn-indigo shadow-sm px-3 py-2 text-white" style="border-radius: 8px; font-weight: 600; background-color: #6610f2; border-color: #6610f2;" data-toggle="modal" data-target="#modalNovoComando">
                <i class="fas fa-plus mr-sm-2"></i>
                <span class="d-none d-sm-inline">Novo Template</span>
            </button>
        </div>
    </div>

    <!-- 🛠️ TABELA DE AUTOMAÇÃO -->
    <div class="card card-outline shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden; border-top: 3px solid #6610f2;">
        <div class="card-header border-0 bg-transparent px-4 py-3">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-terminal mr-2 text-indigo"></i>Biblioteca de Configuração
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center text-sm" style="background-color: rgba(0,0,0,0.02);">
                            <th class="text-left px-4">MODELO</th>
                            <th class="text-left">DESCRIÇÃO</th>
                            <th class="d-none d-md-table-cell">TEMPLATE SMS</th>
                            <th>ORDEM</th>
                            <th style="width: 100px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commands as $command)
                        <tr>
                            <td class="align-middle px-4">
                                <span class="badge badge-indigo text-white px-2 py-1" style="background-color: #6610f2;">
                                    {{ $command->deviceModel->name }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="text-bold">{{ $command->description }}</div>
                            </td>
                            <td class="align-middle d-none d-md-table-cell">
                                <span class="p-2 bg-light border rounded text-dark d-block">
                                    {{ $command->command_template }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <button class="btn btn-light btn-square border-right" title="Editar"><i class="fas fa-tools fa-lg text-warning"></i></button>
                                    <form action="{{ route('device-commands.destroy', $command->id) }}" method="POST" class="m-0" onsubmit="return confirm('Deseja realmente excluir este comando?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-square" title="Excluir"><i class="fas fa-trash fa-lg text-danger"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Nenhum script técnico cadastrado na automação.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($commands->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
            {{ $commands->links() }}
        </div>
        @endif
    </div>
</div>

<!-- 🏗️ MODAL NOVO COMANDO -->
<div class="modal fade" id="modalNovoComando" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header text-white border-0 py-3" style="background-color: #6610f2;">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-comment-medical mr-2"></i>Novo Script SMS</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('device-commands.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row">
                        <div class="col-12 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Modelo de Rastreador</label>
                            <select name="device_model_id" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" required>
                                <option value="">Selecione o Hardware...</option>
                                @foreach($deviceModels as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->manufacturer }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-8 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Descrição do Comando</label>
                            <input type="text" name="description" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="Ex: Configurar IP/DNS" required>
                        </div>
                        <div class="col-4 form-group mb-3">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Ordem</label>
                            <input type="number" name="execution_order" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" value="1" required>
                        </div>
                        <div class="col-12 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold">Código SMS (Template)</label>
                            <textarea name="command_template" class="form-control border-0 shadow-sm" rows="3" style="background: #f8f9fa; border-radius: 8px; font-family: monospace;" placeholder="Ex: *server,0,123.45.67.89,8000#" required></textarea>
                            <small class="text-muted">Use o formato real que o rastreador espera receber.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn text-white px-4 shadow-sm font-weight-bold" style="border-radius: 8px; background-color: #6610f2;">CADASTRAR SCRIPT</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
    .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
    .dark-mode code { background: #1a1a2e !important; color: #a5a5ff !important; border: 1px solid #2d2d44 !important; }
    .dark-mode .modal-content { background: #1a1a2e; border: 1px solid #2d2d44; }
    .dark-mode .modal-body input, .dark-mode .modal-body select, .dark-mode .modal-body textarea { 
        background: #16213e !important; color: #fff !important; border: 1px solid #2d2d44 !important; 
    }
    .dark-mode .modal-footer { background: #16213e !important; }
    
    .text-indigo { color: #6610f2 !important; }
    .animate__animated { --animate-duration: 0.6s; }
</style>
@endsection
