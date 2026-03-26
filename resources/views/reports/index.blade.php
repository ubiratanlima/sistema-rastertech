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
            <button class="btn btn-dark shadow-sm px-3 py-2" style="border-radius: 8px; font-weight: 600;" onclick="window.print()">
                <i class="fas fa-print mr-sm-2"></i>
                <span class="d-none d-sm-inline">Gerar Relatório</span>
            </button>
        </div>
    </div>

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

    <!-- 🛠️ TABELA DE AUDITORIA RAPIDA -->
    <div class="card card-outline card-dark shadow-sm border-0 mt-2 animate__animated animate__fadeInUp" style="border-radius: 12px; overflow: hidden;">
        <div class="card-header border-0 bg-transparent px-4 py-3">
            <h3 class="card-title text-bold mb-0" style="font-size: 1.1rem;">
                <i class="fas fa-clipboard-check mr-2 text-warning"></i>Sumário de Inventário
            </h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr class="text-center text-sm">
                        <th class="text-left px-4">CATEGORIA</th>
                        <th>DISPONÍVEL</th>
                        <th>EM USO</th>
                        <th>TOTAL</th>
                        <th style="width: 200px;">BALANÇO</th>
                    </tr>
                </thead>
                <tbody>
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
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
    .dark-mode .table td { border-color: rgba(255,255,255,0.05); color: #e0e0e0; }
    .dark-mode .progress { background: #16213e !important; }
    .dark-mode .card { background: #1a1a2e; }
    
    .card-indigo { border-top: 3px solid #6610f2 !important; }
    .animate__animated { --animate-duration: 0.6s; }
    
    @media print {
        .main-sidebar, .main-header, .btn { display: none !important; }
        .content-wrapper { padding: 0 !important; margin: 0 !important; }
    }
</style>
@endsection
