@extends('layouts.app')

@section('title', 'Gestão de Chips (SIM Cards)')

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
                <i class="fas fa-sim-card mr-2 text-primary"></i>Gestão de Chips
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-sim-card mr-1 text-primary"></i>Inventário SIM
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block">Controle de conectividade, estoque e operadoras.</p>
        </div>
        <div class="col-4 col-sm-6 text-right p-0 pr-sm-2">
            <button class="btn btn-primary shadow-sm px-3 py-2" style="border-radius: 8px; font-weight: 600;" data-toggle="modal" data-target="#modalNovoChip">
                <i class="fas fa-plus mr-sm-2"></i>
                <span class="d-none d-sm-inline">Novo Chip</span>
            </button>
        </div>
    </div>

    <!-- 🛠️ TABELA CAMALEÃO -->
    <div class="card card-outline card-primary shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex align-items-center">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-list mr-2 text-primary"></i>Conectividade Ativa
            </h3>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive" style="overflow-x: hidden;">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr class="text-center font-weight-bold text-uppercase" style="background-color: rgba(0,0,0,0.02);">
                            <th class="d-none d-md-table-cell">ID</th>
                            <th class="d-none d-lg-table-cell text-left px-4">ICCID</th>
                            <th class="text-left px-4">NÚMERO</th>
                            <th class="d-none d-md-table-cell">OPERADORA</th>
                            <th class="d-none d-md-table-cell text-center">PROPRIETÁRIO</th>
                            <th class="d-none d-md-table-cell text-center">EQUIPAMENTO</th>
                            <th class="d-md-none">VÍNCULO</th>
                            <th class="d-none d-sm-table-cell">STATUS</th>
                            <th style="width: 140px;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sims as $sim)
                        <tr>
                            <td class="text-center align-middle d-none d-md-table-cell text-muted">{{ $sim->id }}</td>
                            <td class="align-middle d-none d-lg-table-cell px-4">
                                <span class="text-pink">{{ $sim->iccid ?? 'N/A' }}</span>
                            </td>
                            <td class="align-middle px-4">
                                <div class="text-primary">{{ $sim->phone_number ?? '---' }}</div>
                                <div class="d-block d-md-none text-muted">ICCID: {{ \Illuminate\Support\Str::limit($sim->iccid, 8) }}</div>
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="badge badge-light border px-2 py-1 text-uppercase font-weight-normal">
                                    {{ $sim->operator }}
                                </span>
                            </td>

                            <!-- 🖥️ VISÃO DESKTOP: VÍNCULOS -->
                            <td class="align-middle d-none d-md-table-cell text-center">
                                {{ $sim->customer_name ?? 'ESTOQUE' }}
                            </td>
                            <td class="text-center align-middle d-none d-md-table-cell">
                                <span class="text-indigo">{{ $sim->rtech_code ?? '---' }}</span>
                            </td>

                            <!-- 📱 VISÃO MOBILE: RESUMIDA -->
                            <td class="align-middle d-md-none">
                                {{ \Illuminate\Support\Str::limit($sim->customer_name ?? 'ESTOQUE', 10) }}
                            </td>

                            <td class="text-center align-middle d-none d-sm-table-cell">
                                <span class="badge {{ $sim->status === 'active' ? 'bg-success' : 'bg-warning' }} px-3 py-1 shadow-sm">
                                    {{ $sim->status === 'active' ? 'ATIVO' : 'ESTOQUE' }}
                                </span>
                            </td>
                            <td class="text-center align-middle">
                                <div class="btn-group shadow-sm" style="border-radius: 8px; overflow: hidden;">
                                    <button class="btn btn-light btn-square border-right" title="Raio-X" onclick="showChipDetails('{{ $sim->iccid }}', '{{ $sim->phone_number }}', '{{ $sim->operator }}', '{{ $sim->customer_name ?? 'ESTOQUE' }}', '{{ $sim->rtech_code ?? '---' }}')">
                                        <i class="fas fa-eye fa-lg text-info"></i>
                                    </button>
                                    <button class="btn btn-light btn-square border-right" title="Editar"><i class="fas fa-tools fa-lg text-warning"></i></button>
                                    <form action="{{ route('sim-cards.destroy', $sim->id) }}" method="POST" class="m-0" onsubmit="return confirm('Deseja realmente inativar este chip?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-light btn-square" title="Excluir"><i class="fas fa-trash fa-lg text-danger"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-sim-card fa-3x text-muted mb-3"></i>
                                <h4 class="text-muted">Nenhum chip encontrado</h4>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($sims->hasPages())
        <div class="card-footer bg-transparent border-0 py-3 text-right">
            {{ $sims->links() }}
        </div>
        @endif
    </div>

<style>
    .btn-square {
        width: 42px;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    <!-- 📟 MODAL: REGISTRO PREMIUM -->
    <div class="modal fade animate__animated animate__fadeIn" id="modalNovoChip" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <div class="modal-header bg-primary text-white border-0">
                    <h5 class="modal-title font-weight-bold">
                        <i class="fas fa-plus-circle mr-2"></i> Registrar Novo Chip
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <form action="/sim-cards" method="POST">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase">Número ICCID (Serial)</label>
                            <input type="text" name="iccid" class="form-control" placeholder="8955..." required style="height: 45px;">
                        </div>
                        <div class="form-group mb-3">
                            <label class="font-weight-bold small text-muted text-uppercase">Número da Linha</label>
                            <input type="text" name="phone_number" class="form-control" placeholder="11999998888" style="height: 45px;">
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-muted text-uppercase">Operadora</label>
                                    <select name="operator" class="form-control" required style="height: 45px;">
                                        <option value="Vivo">Vivo</option>
                                        <option value="Claro">Claro</option>
                                        <option value="Tim">Tim</option>
                                        <option value="Arqia">Arqia</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="font-weight-bold small text-muted text-uppercase">Status</label>
                                    <select name="status" class="form-control" required style="height: 45px;">
                                        <option value="inactive">ESTOQUE</option>
                                        <option value="active">ATIVO</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-3">
                        <button type="button" class="btn btn-link text-muted" data-dismiss="modal">CANCELAR</button>
                        <button type="submit" class="btn btn-primary px-4 shadow-sm font-weight-bold">SALVAR CHIP</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- 🏢 MODAL DE DETALHES DO CHIP (RAIO-X RASTERTECH) -->
    <div class="modal fade" id="modalChipDetails" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-dark text-white border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-sim-card mr-2 text-warning"></i>Dossiê Logístico</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body p-4" id="modalChipBody">
                    <!-- Dinâmico via JS -->
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-dark shadow-sm px-4" style="border-radius: 8px; font-weight: 600;" data-dismiss="modal">FECHAR</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* 🎨 DESIGN SYSTEM RASTERTECH (BOTÕES PADRONIZADOS) */
        .btn-rastertech { 
            background: #ffc107; 
            color: #212529; 
            border-radius: 8px; 
            font-weight: 700; 
            transition: all 0.3s ease;
            border: none;
            padding: 8px 18px;
            text-transform: uppercase;
            font-size: 0.75rem;
        }
        .btn-rastertech:hover { 
            background: #e0a800; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,193,7,0.3);
        }

        .text-indigo { color: #6610f2; }

        /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
        .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
        .dark-mode .btn-light { background: #1a1a2e; border-color: #2d2d44; color: #fff; }
        .dark-mode .btn-light:hover { background: #2d2d44; }
        .dark-mode code.text-pink { background: #16213e; color: #ff007f; border: 1px solid #33213e; }
        .dark-mode .modal-content { background: #1a1a2e; color: #fff; }
        
        .btn-group .btn { padding: 10px 14px; }
        .animate__animated { --animate-duration: 0.6s; }
        code.text-pink { background: #fff0f5; padding: 2px 5px; border-radius: 4px; color: #e83e8c; }
    </style>

    <script>
        function showChipDetails(iccid, phone, operator, customer, code) {
            const body = `
                <div class="mb-3 p-3 bg-light rounded shadow-sm border-left" style="border-left: 4px solid #ffc107 !important;">
                    <label class="small text-muted mb-1 d-block font-weight-bold text-uppercase">SERIAL ICCID</label>
                    <div class="h6 font-weight-bold text-dark mb-0">${iccid}</div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small text-muted mb-1 d-block font-weight-bold">NÚMERO LINHA</label>
                        <div class="font-weight-bold">${phone || 'SEM NÚMERO'}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small text-muted mb-1 d-block font-weight-bold">OPERADORA</label>
                        <div class="badge badge-warning px-3">${operator}</div>
                    </div>
                </div>
                <div class="mb-0 p-3" style="border: 2px dashed #ddd; border-radius: 10px; background: #fff;">
                    <label class="small text-muted mb-1 d-block font-weight-bold">POSSE OPERACIONAL</label>
                    <div class="font-weight-bold text-dark mb-1"><i class="fas fa-user-tie mr-2 text-warning"></i>${customer}</div>
                    <div class="font-weight-bold text-indigo"><i class="fas fa-barcode mr-2"></i>${code}</div>
                </div>
            `;
            document.getElementById('modalChipBody').innerHTML = body;
            $('#modalChipDetails').modal('show');
        }
    </script>
@endsection
