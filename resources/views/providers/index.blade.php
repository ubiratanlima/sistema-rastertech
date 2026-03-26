@extends('layouts.app')

@section('title', 'Gestão de Fornecedores')

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
                <i class="fas fa-industry mr-2 text-primary"></i>Fornecedores
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-industry mr-1 text-primary"></i>Parceiros
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block">Gestão de parceiros de hardware, conectividade e serviços.</p>
        </div>
        <div class="col-4 col-sm-6 text-right p-0 pr-sm-2">
            <button class="btn btn-primary shadow-sm px-3 py-2" style="border-radius: 8px; font-weight: 600;" data-toggle="modal" data-target="#modalNovoFornecedor">
                <i class="fas fa-plus mr-sm-2"></i>
                <span class="d-none d-sm-inline">Novo Fornecedor</span>
            </button>
        </div>
    </div>

    <!-- 🛠️ TABELA DE INFRAESTRUTURA -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-list mr-2 text-primary"></i>Base de Fornecimento
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center text-sm" style="background-color: rgba(0,0,0,0.02);">
                            <th class="text-left px-4">NOME DO PARCEIRO</th>
                            <th>TIPO</th>
                            <th class="d-none d-md-table-cell">HARDWARE</th>
                            <th class="d-none d-md-table-cell">FROTA/CHIPS</th>
                            <th style="width: 120px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($providers as $provider)
                        <tr>
                            <td class="align-middle px-4">
                                <div class="text-bold text-primary">{{ $provider->name }}</div>
                            </td>
                            <td class="text-center align-middle">
                                <span class="badge {{ $provider->type == 'hardware' ? 'bg-info' : ($provider->type == 'connectivity' ? 'bg-success' : 'bg-secondary') }} px-2 py-1 text-uppercase">
                                    {{ $provider->type }}
                                </span>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="badge badge-light border">{{ $provider->devices_count }} un</span>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="badge badge-light border">{{ $provider->gsm_cards_count }} un</span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <button class="btn btn-xs btn-light border-right" title="Editar"><i class="fas fa-edit text-warning"></i></button>
                                    <form action="{{ route('providers.destroy', $provider->id) }}" method="POST" onsubmit="return confirm('Inativar este fornecedor? Esta ação é irreversível.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-xs btn-light" title="Remover"><i class="fas fa-trash text-danger"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Nenhum fornecedor cadastrado na base técnica.</td>
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
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title font-weight-bold"><i class="fas fa-industry mr-2"></i>Cadastrar Parceiro</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('providers.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="form-group mb-3">
                        <label class="text-xs text-uppercase text-muted font-weight-bold">Nome da Empresa</label>
                        <input type="text" name="name" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="Ex: Queclink, Vivo, RasterTech Cloud" required>
                    </div>
                    <div class="form-group mb-0">
                        <label class="text-xs text-uppercase text-muted font-weight-bold">Tipo de Fornecimento</label>
                        <select name="type" class="form-control form-control-lg border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" required>
                            <option value="hardware">Hardware (Rastreadores)</option>
                            <option value="connectivity">Conectividade (SIM Cards)</option>
                            <option value="software">Software / Serviços</option>
                        </select>
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

<style>
    /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
    .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
    .dark-mode .btn-light { background: #1a1a2e; border-color: #2d2d44; color: #fff; }
    .dark-mode .btn-light:hover { background: #2d2d44; }
    .dark-mode .modal-content { background: #1a1a2e; border: 1px solid #2d2d44; }
    .dark-mode .modal-body input, .dark-mode .modal-body select { background: #16213e !important; color: #fff !important; border: 1px solid #2d2d44 !important; }
    .dark-mode .modal-footer { background: #16213e !important; }
    
    .btn-group .btn { padding: 8px 12px; }
    .animate__animated { --animate-duration: 0.6s; }
</style>
@endsection
