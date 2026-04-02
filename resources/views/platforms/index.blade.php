@extends('layouts.app')

@section('title', 'Gestão de Plataformas')

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
                <i class="fas fa-server mr-2 text-info"></i>Plataformas
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-server mr-1 text-info"></i>Sistemas
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block">Gerenciamento de servidores, IPs e URLs de rastreamento.</p>
        </div>
        <div class="col-4 col-sm-6 text-right p-0 pr-sm-2">
            <button class="btn btn-info shadow-sm px-3 py-2 text-white" style="border-radius: 8px; font-weight: 600;" data-toggle="modal" data-target="#modalNovaPlataforma">
                <i class="fas fa-plus mr-sm-2"></i>
                <span class="d-none d-sm-inline">Nova Plataforma</span>
            </button>
        </div>
    </div>

    <!-- 🛠️ TABELA DE REDE -->
    <div class="card card-outline card-info shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-network-wired mr-2 text-info"></i>Configurações de Rede
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02);">
                            <th class="text-left px-4">SISTEMA</th>
                            <th>IP DO SERVIDOR</th>
                            <th class="d-none d-md-table-cell">URL DE ACESSO</th>
                            <th class="d-none d-md-table-cell">DISPOSITIVOS</th>
                            <th style="width: 120px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($platforms as $platform)
                        <tr>
                            <td class="align-middle px-4">
                                <div class="text-info">{{ $platform->name }}</div>
                                <div class="text-muted">{{ $platform->supplier_name ?? 'Próprio' }}</div>
                            </td>
                            <td class="text-center align-middle font-italic">
                                <span>{{ $platform->server_ip }}</span>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                @if($platform->url)
                                    <a href="{{ $platform->url }}" target="_blank" class="text-info">
                                        {{ Str::limit($platform->url, 25) }} <i class="fas fa-external-link-alt ml-1"></i>
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="badge badge-light border">{{ $platform->devices_count }} ativos</span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <button class="btn btn-light btn-square border-right" title="Editar"><i class="fas fa-tools fa-lg text-warning"></i></button>
                                    <form action="{{ route('platforms.destroy', $platform->id) }}" method="POST" class="m-0" onsubmit="return confirm('Inativar esta plataforma?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-square" title="Excluir"><i class="fas fa-trash fa-lg text-danger"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Nenhuma plataforma registrada no radar técnico.</td>
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
                <h5 class="modal-title font-weight-bold"><i class="fas fa-server mr-2"></i>Mapear Servidor</h5>
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
                            <label class="text-xs text-uppercase text-muted font-weight-bold text-success"><i class="fab fa-android mr-1"></i> Link Google Play</label>
                            <input type="url" name="app_android_url" class="form-control form-control-sm border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="https://play.google.com/...">
                        </div>
                        <div class="col-6 form-group mb-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold text-info"><i class="fab fa-apple mr-1"></i> Link Apple Store</label>
                            <input type="url" name="app_ios_url" class="form-control form-control-sm border-0 shadow-sm" style="background: #f8f9fa; border-radius: 8px;" placeholder="https://apps.apple.com/...">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-link text-muted" data-dismiss="modal">CANCELAR</button>
                    <button type="submit" class="btn btn-info px-4 shadow-sm font-weight-bold text-white" style="border-radius: 8px;">ATIVAR SERVIDOR</button>
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
    .dark-mode code { background: #16213e; color: #00d2ff; padding: 2px 6px; border-radius: 4px; border: 1px solid #2d2d44; }
    .dark-mode .modal-content { background: #1a1a2e; border: 1px solid #2d2d44; }
    .dark-mode .modal-body input { background: #16213e !important; color: #fff !important; border: 1px solid #2d2d44 !important; }
    .dark-mode .modal-footer { background: #16213e !important; }
    
    .btn-group .btn { padding: 8px 12px; }
    .animate__animated { --animate-duration: 0.6s; }
</style>
@endsection
