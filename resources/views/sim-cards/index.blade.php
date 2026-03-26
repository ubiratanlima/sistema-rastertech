@extends('layouts.app')

@section('title', 'Gestão de Chips (SIM Cards)')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 animate__animated animate__fadeIn">
        <div class="col-sm-6">
            <h1 class="m-0 text-bold"><i class="fas fa-sim-card mr-2 text-primary"></i>Cartões SIM</h1>
            <p class="text-muted">Controle de conectividade, estoque e operadoras.</p>
        </div>
        <div class="col-sm-6 text-right">
            <button class="btn btn-success shadow-sm btn-lg px-4" data-toggle="modal" data-target="#modalNovoChip">
                <i class="fas fa-plus mr-2"></i>Cadastrar Novo Chip
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4 animate__animated animate__flipInX" role="alert" style="background: linear-gradient(90deg, #00b09b, #96c93d); color: white;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close text-white" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Atenção:</strong>
            <ul class="mb-0 mt-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Tabela de Inventário -->
    <div class="card card-outline card-primary shadow-lg border-0 animate__animated animate__fadeInUp">
        <div class="card-header border-0 bg-transparent">
            <h3 class="card-title text-bold mt-2"><i class="fas fa-list mr-2"></i>Inventário de Conectividade</h3>
            <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 250px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Buscar ICCID ou Número...">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr class="bg-dark text-white">
                            <th>ID</th>
                            <th>ICCID</th>
                            <th>NÚMERO</th>
                            <th>OPERADORA</th>
                            <th>IMEI VINCULADO</th>
                            <th>CLIENTE</th>
                            <th>STATUS</th>
                            <th class="text-center">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sims as $sim)
                        <tr>
                            <td class="text-bold">{{ $sim->id }}</td>
                            <td><code class="text-pink">{{ $sim->iccid ?? 'N/A' }}</code></td>
                            <td class="text-bold text-primary">{{ $sim->phone_number ?? '---' }}</td>
                            <td>
                                <span class="badge badge-info px-3 py-2 shadow-sm">
                                    <i class="fas fa-signal mr-1"></i> {{ $sim->operator ?? 'DESCONHECIDA' }}
                                </span>
                            </td>
                            <td>
                                @if($sim->imei_vincidulado)
                                    <span class="text-success text-bold"><i class="fas fa-link mr-1"></i> {{ $sim->imei_vincidulado }}</span>
                                @else
                                    <span class="text-muted italic small"><i class="fas fa-box-open mr-1"></i> Em Estoque</span>
                                @endif
                            </td>
                            <td>{{ $sim->customer_name ?? '---' }}</td>
                            <td>
                                @php
                                    $statusMapping = [
                                        'active' => ['class' => 'btn-success', 'label' => 'ATIVO'],
                                        'inactive' => ['class' => 'btn-warning', 'label' => 'ESTOQUE'],
                                        'suspended' => ['class' => 'btn-danger', 'label' => 'SUSPENSO'],
                                    ];
                                    $currentStatus = $statusMapping[$sim->status] ?? ['class' => 'btn-secondary', 'label' => 'DESCONHECIDO'];
                                @endphp
                                <button class="btn btn-xs {{ $currentStatus['class'] }} text-bold px-2" style="cursor: default; pointer-events: none;">
                                    {{ $currentStatus['label'] }}
                                </button>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-primary" title="Editar"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-sm btn-info" title="Vincular"><i class="fas fa-link"></i></button>
                                    <button class="btn btn-sm btn-danger" title="Excluir"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="fas fa-info-circle fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Nenhum chip encontrado</h4>
                                <p>Cadastre o seu primeiro Cartão SIM para começar a gerenciar.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer clearfix bg-transparent border-0">
            <div class="float-right pagination-relative">
                {{ $sims->links() }}
            </div>
        </div>
    </div>

    <!-- 📟 MODAL: NOVO CHIP -->
    <div class="modal fade" id="modalNovoChip" tabindex="-1" role="dialog" aria-labelledby="modalNovoChipLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; overflow: hidden;">
                <div class="modal-header bg-dark text-white border-0">
                    <h5 class="modal-title font-weight-bold" id="modalNovoChipLabel">
                        <i class="fas fa-sim-card mr-2 text-success"></i> Registrar Novo Chip
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="/sim-cards" method="POST">
                    @csrf
                    <div class="modal-body bg-light p-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase">Número ICCID (Serial)</label>
                            <input type="text" name="iccid" class="form-control border-0 shadow-sm" placeholder="Ex: 8955..." required style="border-radius: 8px; height: 45px;">
                            <small class="text-muted">Identificador único do chip físico.</small>
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase">Número da Linha (Opcional)</label>
                            <input type="text" name="phone_number" class="form-control border-0 shadow-sm" placeholder="Ex: 11999998888" style="border-radius: 8px; height: 45px;">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-muted text-uppercase">Operadora</label>
                                    <select name="operator" class="form-control border-0 shadow-sm" required style="border-radius: 8px; height: 45px;">
                                        <option value="Vivo">Vivo</option>
                                        <option value="Claro">Claro</option>
                                        <option value="Tim">Tim</option>
                                        <option value="Oi">Oi</option>
                                        <option value="Arqia">Arqia</option>
                                        <option value="Transatel">Transatel</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-muted text-uppercase">Status Inicial</label>
                                    <select name="status" class="form-control border-0 shadow-sm" required style="border-radius: 8px; height: 45px;">
                                        <option value="inactive">ESTOQUE</option>
                                        <option value="active">ATIVO</option>
                                        <option value="suspended">SUSPENSO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-white p-3">
                        <button type="button" class="btn btn-link text-muted font-weight-bold" data-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-success px-4 shadow-sm font-weight-bold" style="border-radius: 8px;">SALVAR INVENTÁRIO</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    /* Estilos para o Dark Mode do Sistema */
    body.dark-mode .card { background: #1a1a2e; border-top: 3px solid #00ff88 !important; }
    body.dark-mode .table td { color: #e0e0e0; border-color: #2d2d44; vertical-align: middle; }
    body.dark-mode .table thead th { background: #0f3460; border-color: #2d2d44; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 1px; }
    body.dark-mode .card-header { border-bottom: 1px solid #2d2d44; }
    
    .pagination-relative ul { margin-bottom: 0; }
    .badge { font-size: 0.85rem; border-radius: 4px; }
    code.text-pink { background: #fff0f5; padding: 2px 5px; border-radius: 4px; color: #e83e8c; font-weight: bold; }
    body.dark-mode code.text-pink { background: #16213e; color: #ff007f; border: 1px solid #33213e; }
    
    .btn-xs { padding: 1px 5px; font-size: 0.75rem; }
    .animate__animated { --animate-duration: 0.8s; }
</style>
@endsection
