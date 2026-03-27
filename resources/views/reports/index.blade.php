@extends('layouts.app')

@section('title', 'Inteligência de Frota')

@section('content')
<div class="container-fluid">
    <!-- ⚓ CABEÇALHO PADRÃO OURO (8:4) -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center overflow-hidden flex-nowrap">
        <div class="col-8 col-sm-6 p-0 p-sm-2">
            <h1 class="m-0 text-bold d-none d-sm-block" style="font-size: 2.2rem;">
                <i class="fas fa-chart-line mr-2 text-warning"></i>Inteligência de Dados
            </h1>
            <h1 class="m-0 text-bold d-block d-sm-none" style="font-size: 1.55rem; white-space: nowrap; letter-spacing: -1.5px;">
                <i class="fas fa-chart-line mr-1 text-warning"></i>Insights
            </h1>
            <p class="text-muted mb-0 d-none d-sm-block">Auditoria geral de inventário e saúde operacional.</p>
        </div>
        <div class="col-4 col-sm-6 text-right p-0 pr-sm-2">
            <a href="{{ route('reports.index', ['export' => 'pdf']) }}" target="_blank" class="btn btn-dark shadow-sm px-3 py-2" style="border-radius: 8px; font-weight: 600;">
                <i class="fas fa-file-pdf mr-sm-2 text-warning"></i>
                <span class="d-none d-sm-inline">Gerar Relatório Profissional</span>
            </a>
        </div>
    </div>

    <!-- 🛠️ NAVEGAÇÃO DE AUDITORIA (PILLS) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap" style="gap: 10px;">
                <a href="{{ route('reports.index', ['type' => 'inventory']) }}" class="btn {{ $type === 'inventory' ? 'btn-warning' : 'btn-outline-dark' }} shadow-sm" style="border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-boxes mr-2"></i>RESUMO GERAL
                </a>
                <a href="{{ route('reports.index', ['type' => 'chips']) }}" class="btn {{ $type === 'chips' ? 'btn-warning' : 'btn-outline-dark' }} shadow-sm" style="border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-sim-card mr-2"></i>AUDITORIA DE CHIPS
                </a>
                <a href="{{ route('reports.index', ['type' => 'vehicles']) }}" class="btn {{ $type === 'vehicles' ? 'btn-warning' : 'btn-outline-dark' }} shadow-sm" style="border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-car mr-2"></i>AUDITORIA DE VEÍCULOS
                </a>
                <a href="{{ route('reports.index', ['type' => 'customers']) }}" class="btn {{ $type === 'customers' ? 'btn-warning' : 'btn-outline-dark' }} shadow-sm" style="border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-users mr-2"></i>CARTEIRA DE CLIENTES
                </a>
                <a href="{{ route('reports.index', ['type' => 'users']) }}" class="btn {{ $type === 'users' ? 'btn-warning' : 'btn-outline-dark' }} shadow-sm" style="border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-user-shield mr-2"></i>USUÁRIOS INTERNOS
                </a>
                <a href="{{ route('reports.index', ['type' => 'sub_users']) }}" class="btn {{ $type === 'sub_users' ? 'btn-warning' : 'btn-outline-dark' }} shadow-sm" style="border-radius: 8px; font-weight: 600;">
                    <i class="fas fa-user-tag mr-2"></i>SUB-USUÁRIOS
                </a>
            </div>
        </div>
    </div>

    <!-- 🔍 BARRA DE FILTROS DINÂMICA -->
    <div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeIn" style="border-radius: 12px; background: #f8f9fa;">
        <div class="card-body py-3">
            <form action="{{ route('reports.index') }}" method="GET" class="row align-items-end">
                <input type="hidden" name="type" value="{{ $type }}">
                
                @if($type === 'chips')
                    <div class="col-md-2 form-group mb-0">
                        <label class="small font-weight-bold">OPERADORA</label>
                        <select name="operator" class="form-control form-control-sm border-0 shadow-sm">
                            <option value="">Todas</option>
                            <option value="Vivo" {{ request('operator') == 'Vivo' ? 'selected' : '' }}>VIVO</option>
                            <option value="Tim" {{ request('operator') == 'Tim' ? 'selected' : '' }}>TIM</option>
                            <option value="Claro" {{ request('operator') == 'Claro' ? 'selected' : '' }}>CLARO</option>
                        </select>
                    </div>
                    <div class="col-md-2 form-group mb-0">
                        <label class="small font-weight-bold">DDD</label>
                        <input type="text" name="ddd" class="form-control form-control-sm border-0 shadow-sm" placeholder="Ex: 11" value="{{ request('ddd') }}">
                    </div>
                    <div class="col-md-3 form-group mb-0">
                        <label class="small font-weight-bold">VÍNCULO</label>
                        <select name="linked" class="form-control form-control-sm border-0 shadow-sm">
                            <option value="">Todos</option>
                            <option value="yes" {{ request('linked') == 'yes' ? 'selected' : '' }}>Com Rastreador</option>
                            <option value="no" {{ request('linked') == 'no' ? 'selected' : '' }}>Livres (Sem vínculo)</option>
                        </select>
                    </div>
                @elseif($type === 'customers' || $type === 'users' || $type === 'sub_users')
                    <div class="col-md-4 form-group mb-0">
                        <label class="small font-weight-bold">BUSCAR POR NOME</label>
                        <input type="text" name="search" class="form-control form-control-sm border-0 shadow-sm" placeholder="Digite o nome..." value="{{ request('search') }}">
                    </div>
                @endif

                <div class="col-md-2 mb-0">
                    <button type="submit" class="btn btn-sm btn-primary btn-block font-weight-bold shadow-sm">
                        <i class="fas fa-filter mr-1"></i>APLICAR FILTRO
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($type === 'inventory')
    <!-- 📊 INDICADORES DE ELITE -->
    <div class="row animate__animated animate__fadeInUp">
        <div class="col-12 col-md-3 mb-3">
            <div class="card card-outline card-warning shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body text-center p-4">
                    <div class="text-muted text-xs text-uppercase font-weight-bold mb-2">Total de Clientes</div>
                    <h2 class="text-bold m-0" style="font-size: 2.5rem;">{{ $stats['customers_total'] }}</h2>
                    <div class="small text-success mt-2"><i class="fas fa-building mr-1"></i>Contas Ativas</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-3">
            <div class="card card-outline card-success shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body text-center p-4">
                    <div class="text-muted text-xs text-uppercase font-weight-bold mb-2">Veículos em Frota</div>
                    <h2 class="text-bold m-0 text-success" style="font-size: 2.5rem;">{{ $stats['vehicles_total'] }}</h2>
                    <div class="small text-muted mt-2">Monitoramento Total</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-3">
            <div class="card card-outline card-info shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body text-center p-4">
                    <div class="text-muted text-xs text-uppercase font-weight-bold mb-2">Aparelhos Ativos</div>
                    <h2 class="text-bold m-0 text-info" style="font-size: 2.5rem;">{{ $stats['devices_active'] }}</h2>
                    <div class="small text-muted mt-2">De um total de {{ $stats['devices_total'] }}</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3 mb-3">
            <div class="card card-outline card-indigo shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body text-center p-4">
                    <div class="text-muted text-xs text-uppercase font-weight-bold mb-2">Chips Conectados</div>
                    <h2 class="text-bold m-0" style="font-size: 2.5rem; color: #6610f2;">{{ $stats['sims_active'] }}</h2>
                    <div class="small text-muted mt-2">Estoque de {{ $stats['sims_total'] - $stats['sims_active'] }} livres</div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 🛠️ TABELA DE AUDITORIA DINÂMICA -->
    <div class="card card-outline card-dark shadow-sm border-0 mt-2 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3 d-flex justify-content-between align-items-center">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-clipboard-list mr-2 text-warning"></i>
                @if($type === 'chips') Tabela de Chips @elseif($type === 'vehicles') Tabela de Veículos @elseif($type === 'customers') Tabela de Clientes @else Sumário de Inventário @endif
            </h3>
            <a href="{{ route('reports.index', array_merge(request()->all(), ['export' => 'pdf'])) }}" target="_blank" class="btn btn-sm btn-dark" style="border-radius: 6px;">
                <i class="fas fa-file-pdf mr-1 text-warning"></i>Exportar Auditoria A4
            </a>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    @if($type === 'chips')
                        <tr class="text-sm">
                            <th class="px-4 text-left">ICCID</th>
                            <th>NÚMERO</th>
                            <th class="d-none d-md-table-cell">OPERADORA</th>
                            <th class="d-none d-md-table-cell">PROPRIETÁRIO</th>
                            <th class="d-none d-md-table-cell">EQUIPAMENTO</th>
                            <th class="d-md-none">VÍNCULO</th>
                            <th class="text-center">AÇÕES</th>
                        </tr>
                    @elseif($type === 'vehicles')
                        <tr class="text-sm">
                            <th class="px-4">PLACA</th>
                            <th>MODELO</th>
                            <th>CLIENTE</th>
                            <th>DATA CADASTRO</th>
                        </tr>
                    @elseif($type === 'customers')
                        <tr class="text-sm">
                            <th class="px-4">NOME / RAZÃO SOCIAL</th>
                            <th>DOCUMENTO</th>
                            <th class="text-center">AÇÕES</th>
                        </tr>
                    @elseif($type === 'users')
                        <tr class="text-sm">
                            <th class="px-4">USUÁRIO</th>
                            <th>EMAIL</th>
                            <th>TIPO / PATENTE</th>
                        </tr>
                    @elseif($type === 'sub_users')
                        <tr class="text-sm">
                            <th class="px-4">SUB-USUÁRIO</th>
                            <th>EMPRESA VINCULADA</th>
                            <th>ACESSO EM</th>
                        </tr>
                    @else
                        <tr class="text-center text-sm">
                            <th class="text-left px-4">CATEGORIA</th>
                            <th>DISPONÍVEL</th>
                            <th>EM USO</th>
                            <th>TOTAL</th>
                            <th style="width: 200px;">BALANÇO</th>
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @if($type === 'inventory')
                    <tr>
                        <td class="px-4"><i class="fas fa-sim-card mr-2 text-indigo"></i>Cartões SIM</td>
                        <td class="text-center">{{ $stats['sims_total'] - $stats['sims_active'] }}</td>
                        <td class="text-center text-bold">{{ $stats['sims_active'] }}</td>
                        <td class="text-center">{{ $stats['sims_total'] }}</td>
                        <td class="align-middle px-3">
                            <div class="progress shadow-sm" style="height: 10px; border-radius: 5px;">
                                @php $simPerc = $stats['sims_total'] > 0 ? ($stats['sims_active'] / $stats['sims_total'] * 100) : 0; @endphp
                                <div class="progress-bar bg-indigo" style="width: {{ $simPerc }}%; border-radius: 5px; background-color: #6610f2;"></div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-4"><i class="fas fa-microchip mr-2 text-info"></i>Equipamentos</td>
                        <td class="text-center">{{ $stats['devices_total'] - $stats['devices_active'] }}</td>
                        <td class="text-center text-bold text-info">{{ $stats['devices_active'] }}</td>
                        <td class="text-center">{{ $stats['devices_total'] }}</td>
                        <td class="align-middle px-3">
                            <div class="progress shadow-sm" style="height: 10px; border-radius: 5px;">
                                @php $devPerc = $stats['devices_total'] > 0 ? ($stats['devices_active'] / $stats['devices_total'] * 100) : 0; @endphp
                                <div class="progress-bar bg-info" style="width: {{ $devPerc }}%; border-radius: 5px;"></div>
                            </div>
                        </td>
                    </tr>
                    @else
                        @forelse($data as $item)
                            @if($type === 'chips')
                                <tr class="text-sm">
                                    <td class="px-4 text-bold py-3 text-primary">
                                        <i class="fas fa-sim-card mr-1 d-none d-md-inline"></i>{{ $item->iccid }}
                                    </td>
                                    <td class="py-3">{{ $item->phone_number }}</td>
                                    <td class="py-3 d-none d-md-table-cell"><span class="badge badge-outline-info border-info text-info">{{ $item->operator }}</span></td>
                                    
                                    <!-- 🖥️ VISÃO DESKTOP/MÉDIA: CLIENTE & CÓDIGO -->
                                    <td class="py-3 d-none d-md-table-cell">
                                        <span class="text-bold text-dark text-xs">{{ $item->device->customer->name ?? 'ESTOQUE' }}</span>
                                    </td>
                                    <td class="py-3 d-none d-md-table-cell text-center">
                                        <span class="text-indigo text-bold">{{ $item->device->model_description ?? '---' }}</span>
                                    </td>

                                    <!-- 📱 VISÃO MOBILE: RESUMIDA -->
                                    <td class="py-3 d-table-cell d-md-none">
                                        <span class="text-muted" title="{{ $item->device->customer->name ?? 'ESTOQUE' }}">
                                            {{ \Illuminate\Support\Str::limit($item->device->customer->name ?? 'ESTOQUE', 10) }}
                                        </span>
                                    </td>

                                    <td class="py-3 text-center">
                                        <button class="btn btn-sm btn-rastertech shadow-sm" onclick="showChipDetails('{{ $item->iccid }}', '{{ $item->phone_number }}', '{{ $item->operator }}', '{{ $item->device->customer->name ?? 'ESTOQUE' }}', '{{ $item->device->model_description ?? '---' }}')">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            @elseif($type === 'vehicles')
                                <tr class="text-sm">
                                    <td class="px-4 text-bold">{{ $item->plate }}</td>
                                    <td>{{ $item->brand }} {{ $item->model }}</td>
                                    <td>{{ $item->customer->name ?? 'N/A' }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @elseif($type === 'customers')
                                <tr class="text-sm">
                                    <td class="px-4 text-bold">{{ $item->name }}</td>
                                    <td>{{ $item->document }}</td>
                                    <td class="text-center"><i class="fas fa-search text-muted"></i></td>
                                </tr>
                            @elseif($type === 'users')
                                <tr class="text-sm">
                                    <td class="px-4 text-bold">{{ $item->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td><span class="badge badge-primary">{{ strtoupper($item->type ?? 'OPERADOR') }}</span></td>
                                </tr>
                            @elseif($type === 'sub_users')
                                <tr class="text-sm">
                                    <td class="px-4 text-bold">{{ $item->name }}</td>
                                    <td>{{ $item->customer->name ?? 'RESTRITO' }}</td>
                                    <td>{{ $item->created_at->format('d/m/Y') }}</td>
                                </tr>
                            @endif
                        @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted">Nenhum registro encontrado para esta auditoria.</td></tr>
                        @endforelse
                    @endif
                </tbody>
            </table>
        </div>
    </div>
                </tbody>
            </table>
        </div>
    </div>
</div>


    <!-- 🏢 MODAL DE DETALHES DO CHIP (RAIO-X RASTERTECH) -->
    <div class="modal fade" id="modalChipDetails" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;">
                <div class="modal-header bg-dark text-white border-0" style="border-radius: 15px 15px 0 0;">
                    <h5 class="modal-title font-weight-bold"><i class="fas fa-sim-card mr-2 text-warning"></i>Dossiê do Chip</h5>
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
        /* 🎨 PADRÃO RASTERTECH DE BOTÕES (DNA ADMINISTRATIVO) */
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
            letter-spacing: 0.5px;
        }
        .btn-rastertech:hover { 
            background: #e0a800; 
            color: #212529; 
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,193,7,0.3);
        }

        /* 🌒 AJUSTES DE TABELA */
        .table th { border-top: none; vertical-align: middle; }
        .text-indigo { color: #6610f2; }
        
        /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
        .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
        .dark-mode .progress { background: #16213e !important; }
        .dark-mode .card { background: #1a1a2e; }
        
        .card-indigo { border-top: 3px solid #6610f2 !important; }
        .animate__animated { --animate-duration: 0.6s; }
        
        @media print {
            .main-sidebar, .main-header, .btn, .barra-filtros { display: none !important; }
            .content-wrapper { padding: 0 !important; margin: 0 !important; }
        }
    </style>

    <script>
        function showChipDetails(iccid, phone, operator, customer, code) {
            const body = `
                <div class="mb-3 p-3 bg-light rounded shadow-sm border-left" style="border-left: 4px solid #ffc107 !important;">
                    <label class="small text-muted mb-1 d-block font-weight-bold">ICCID (IDENTIFICAÇÃO ÚNICA)</label>
                    <div class="h6 font-weight-bold text-dark mb-0">${iccid}</div>
                </div>
                <div class="row">
                    <div class="col-6 mb-3">
                        <label class="small text-muted mb-1 d-block font-weight-bold">LINHA / NÚMERO</label>
                        <div class="font-weight-bold">${phone || 'N/A'}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <label class="small text-muted mb-1 d-block font-weight-bold">OPERADORA</label>
                        <div class="badge badge-warning px-3 font-weight-bold">${operator || 'DESCONHECIDA'}</div>
                    </div>
                </div>
                <div class="mb-0 p-3" style="border: 2px dashed #ddd; border-radius: 10px; background: #fff;">
                    <label class="small text-muted mb-1 d-block font-weight-bold">VÍNCULO OPERACIONAL</label>
                    <div class="font-weight-bold text-dark mb-1"><i class="fas fa-user-tie mr-2 text-warning"></i>${customer}</div>
                    <div class="font-weight-bold" style="color: #6610f2;"><i class="fas fa-microchip mr-2"></i>${code}</div>
                </div>
            `;
            document.getElementById('modalChipBody').innerHTML = body;
            $('#modalChipDetails').modal('show');
        }
    </script>
@endsection
