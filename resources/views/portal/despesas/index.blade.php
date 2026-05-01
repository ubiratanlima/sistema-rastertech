@extends('layouts.app')

@section('title', 'Minhas Despesas | Rastertech')

@section('content')
<div class="container-fluid pb-5 screen-area">
    
    <!-- 📄 CABEÇALHO DO MÓDULO (TELA) -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn">
        <div class="col-12 p-0 d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h1 class="m-0 text-bold" style="font-size: 2.2rem;">
                    <i class="fas fa-file-invoice-dollar mr-2 text-orange"></i>Despesas da Frota
                </h1>
                <p class="text-orange mb-0 font-weight-bold" style="font-size: 1.1rem; letter-spacing: 0.5px;">
                    <i class="fas fa-building mr-1"></i> {{ $customer->name ?? 'Rastertech Operacional' }}
                </p>
            </div>
            <div class="mt-3 mt-md-0 d-flex flex-wrap gap-2 w-100-mobile">
                <button onclick="window.print()" class="btn btn-secondary btn-lg shadow-sm px-4 py-3 text-bold mr-2 mb-2 d-none d-md-inline-block" style="border-radius: 12px; font-size: 1.1rem; border: 0;">
                    <i class="fas fa-print mr-2"></i> IMPRIMIR RELATÓRIO
                </button>
                <a href="{{ route('portal.despesas.create') }}" class="btn btn-orange btn-lg shadow-sm px-4 py-3 text-bold mb-2 btn-action-mobile" style="border-radius: 12px; font-size: 1.1rem; background-color: #fd7e14 !important; border: 0; color: white;">
                    <i class="fas fa-plus-circle mr-2"></i> NOVA DESPESA
                </a>
            </div>
        </div>
    </div>

    <!-- 🔍 FILTROS AVANÇADOS -->
    <div class="card shadow-sm border-0 mb-4 animate__animated animate__fadeInDown d-none d-md-block" style="border-radius: 12px;">
        <div class="card-body p-3">
            <form action="{{ route('portal.despesas.index') }}" method="GET" class="row align-items-end m-0">
                
                @if($isAdmin)
                <div class="col-xl-3 col-lg-3 col-md-6 mb-3 mb-lg-0">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1">Cliente</label>
                    <select name="customer_id" class="form-control form-control-sm select2">
                        <option value="">TODOS OS CLIENTES</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ $customerId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif
                
                <div class="col-xl-{{ $isAdmin ? '2' : '4' }} col-lg-{{ $isAdmin ? '2' : '4' }} col-md-6 mb-3 mb-lg-0">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1">Veículo</label>
                    <select name="vehicle_id" class="form-control form-control-sm select2">
                        <option value="">TODOS</option>
                        @foreach($vehicles as $v)
                            <option value="{{ $v->id }}" {{ $vehicleId == $v->id ? 'selected' : '' }}>{{ $v->plate }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-xl-2 col-lg-2 col-md-4 mb-3 mb-md-0 border-left pl-3">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1">Data Inicial</label>
                    <input type="date" name="date_start" class="form-control form-control-sm" value="{{ $dateStart }}">
                </div>

                <div class="col-xl-2 col-lg-2 col-md-4 mb-3 mb-md-0 pr-3">
                    <label class="small font-weight-bold text-muted text-uppercase mb-1">Data Final</label>
                    <input type="date" name="date_end" class="form-control form-control-sm" value="{{ $dateEnd }}">
                </div>

                <div class="col-xl-3 col-lg-3 col-md-4 text-right">
                    <button type="submit" class="btn btn-sm btn-dark px-4 font-weight-bold w-100 shadow-sm" style="height: 38px;">FILTRAR RELATÓRIO</button>
                    <div class="mt-2 text-center d-flex justify-content-between px-1">
                        <a href="javascript:void(0)" onclick="applyQuickDate(0);" class="text-muted small text-decoration-none"><u>Hoje</u></a>
                        <a href="javascript:void(0)" onclick="applyQuickDate(7);" class="text-muted small text-decoration-none"><u>7 Dias</u></a>
                        <a href="javascript:void(0)" onclick="applyQuickDate(30);" class="text-muted small text-decoration-none"><u>Mês</u></a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- 📊 LISTAGEM TÁTICA (TELA) -->
    <div class="row">
        <div class="col-12">
            @if($expenses->isEmpty())
                <div class="text-center py-5 shadow-sm bg-white" style="border-radius: 15px;">
                    <i class="fas fa-receipt fa-4x text-muted opacity-25 mb-3"></i>
                    <p class="text-muted font-italic mb-0">Nenhuma despesa registrada para os filtros selecionados.</p>
                </div>
            @else
                @if($isAdmin)
                    @php 
                        $groupedExpenses = $expenses->groupBy('vehicle.customer.name'); 
                        $index = 0;
                    @endphp
                    
                    <div class="accordion" id="expensesAccordion">
                        @foreach($groupedExpenses as $customerName => $customerExpenses)
                        @php $index++; $customerTotal = $customerExpenses->sum('amount'); @endphp
                        
                        <div class="card shadow-sm border-0 mb-3" style="border-radius: 12px; overflow: hidden;">
                            <div class="card-header bg-white border-bottom-0 p-0" id="heading-{{ $index }}">
                                <button class="btn btn-link btn-block text-left text-dark font-weight-bold p-4 d-flex justify-content-between align-items-center shadow-none text-decoration-none" 
                                        type="button" data-toggle="collapse" data-target="#collapse-{{ $index }}" aria-expanded="true" aria-controls="collapse-{{ $index }}">
                                    <span style="font-size: 1.1rem;"><i class="fas fa-building text-primary mr-2"></i> {{ $customerName ?: 'Cliente Não Identificado' }}</span>
                                    <span class="badge badge-light border px-3 py-2 text-muted" style="font-size: 1rem;">
                                        Total: <strong class="text-dark">R$ {{ number_format($customerTotal, 2, ',', '.') }}</strong>
                                        <i class="fas fa-chevron-down ml-2"></i>
                                    </span>
                                </button>
                            </div>

                            <div id="collapse-{{ $index }}" class="collapse show" aria-labelledby="heading-{{ $index }}" data-parent="#expensesAccordion">
                                <div class="card-body p-0 border-top">
                                    @include('portal.despesas.partials.table', ['expensesList' => $customerExpenses])
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
                        <div class="card-body p-0">
                            @include('portal.despesas.partials.table', ['expensesList' => $expenses])
                        </div>
                    </div>
                @endif
                
                <div class="card shadow border-0 mt-4 bg-dark text-white d-none d-md-block" style="border-radius: 12px;">
                    <div class="card-body p-4 d-flex justify-content-between align-items-center">
                        <h4 class="m-0 text-uppercase text-bold text-orange"><i class="fas fa-calculator mr-2"></i> TOTAL DO PERÍODO</h4>
                        <h2 class="m-0 text-bold text-orange">R$ {{ number_format($totalAmount, 2, ',', '.') }}</h2>
                    </div>
                </div>

            @endif
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- 🖨️ SEÇÃO EXCLUSIVA DE IMPRESSÃO (A4 NF) -->
<!-- ========================================== -->
<div class="print-area">
    <div class="nf-container">
        <!-- CABEÇALHO NOTA FISCAL -->
        <div class="nf-header">
            <div class="nf-logo-area" style="text-align: center; padding: 5px 0;">
                <img src="{{ asset('img/logo_rastertech.png') }}" alt="Rastertech" style="max-height: 45px; max-width: 100%; object-fit: contain;">
            </div>
            <div class="nf-title-area text-center">
                <h3 style="margin: 0 0 5px 0; font-size: 18px; font-weight: bold; text-transform: uppercase;">Demonstrativo de Despesas</h3>
                <span style="font-size: 11px;">Documento Analítico Auxiliar</span>
            </div>
            <div class="nf-meta-area text-right">
                <table style="width: 100%; border: none; font-size: 10px; line-height: 1.2;">
                    <tr><td style="text-align: right;"><strong>Emissão:</strong></td><td style="text-align: left; padding-left: 5px;">{{ now()->format('d/m/Y H:i') }}</td></tr>
                    <tr><td style="text-align: right;"><strong>Página:</strong></td><td style="text-align: left; padding-left: 5px;">1 de 1</td></tr>
                </table>
            </div>
        </div>

        <!-- QUADRO DE INFORMAÇÕES -->
        <div class="nf-box" style="margin-top: 15px;">
            <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
                <tr>
                    <td style="width: 50%; padding: 8px; vertical-align: top; border-right: 1px solid #000;">
                        <strong style="text-decoration: underline;">ATESTADO DE CONFORMIDADE</strong><br>
                        <strong>Sistema Homologado por:</strong> EMBRAET LTDA<br>
                        <strong>Resp. Técnico:</strong> Eng. Computação Ubiratan Lima<br>
                        <strong>Validação Inicial:</strong> 01/03/2026
                    </td>
                    <td style="width: 50%; padding: 8px; vertical-align: top;">
                        <strong style="text-decoration: underline;">FILTROS DO RELATÓRIO</strong><br>
                        <strong>Período Apurado:</strong> {{ $dateStart ? \Carbon\Carbon::parse($dateStart)->format('d/m/Y') : 'Início' }} a {{ $dateEnd ? \Carbon\Carbon::parse($dateEnd)->format('d/m/Y') : 'Hoje' }}<br>
                        <strong>Emitido por:</strong> {{ auth()->user()->name }}<br>
                        @if($isAdmin)
                        <strong>Cliente:</strong> {{ $customerId ? $customers->firstWhere('id', $customerId)->name ?? $customerId : 'Todos os Clientes' }}<br>
                        @endif
                        <strong>Veículo:</strong> {{ $vehicleId ? $vehicles->firstWhere('id', $vehicleId)->plate ?? $vehicleId : 'Todos os Veículos' }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- TABELA DE ITENS -->
        <div style="margin-top: 15px;">
            <table class="nf-table">
                <thead>
                    <tr>
                        <th style="width: 120px;">DATA/HORA</th>
                        <th style="width: 100px;">VEÍCULO</th>
                        <th style="width: 120px;">CATEGORIA</th>
                        <th>DESCRIÇÃO DO GASTO</th>
                        @if($isAdmin)
                        <th>CLIENTE/FROTA</th>
                        @endif
                        <th style="width: 100px; text-align: right;">ODÔMETRO</th>
                        <th style="width: 120px; text-align: right;">VALOR (R$)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $exp)
                    <tr>
                        <td>{{ $exp->created_at->format('d/m/Y') }} <span style="font-size: 9px; color: #555;">{{ $exp->created_at->format('H:i') }}</span></td>
                        <td><strong>{{ $exp->vehicle->plate ?? 'N/A' }}</strong></td>
                        <td style="text-transform: uppercase;">{{ $exp->type }}</td>
                        <td>{{ $exp->description }}</td>
                        @if($isAdmin)
                        <td><small>{{ $exp->vehicle->customer->name ?? 'N/A' }}</small></td>
                        @endif
                        <td style="text-align: right;">{{ number_format($exp->odometer, 0, ',', '.') }} KM</td>
                        <td style="text-align: right;">{{ number_format($exp->amount, 2, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $isAdmin ? '7' : '6' }}" style="text-align: center; padding: 20px;">Nenhuma despesa para exibir neste período.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- TOTALIZADOR -->
        <div class="nf-box" style="margin-top: 15px;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="text-align: right; padding: 15px; font-size: 16px; font-weight: bold; width: 70%;">
                        TOTAL GERAL DO PERÍODO:
                    </td>
                    <td style="text-align: right; padding: 15px; font-size: 20px; font-weight: 900; background-color: #f0f0f0; border-left: 2px solid #000;">
                        R$ {{ number_format($totalAmount, 2, ',', '.') }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- 🏁 FIM DO RELATÓRIO -->
    </div>
</div>

<style>
    /* 📱 ESTILOS DA TELA */
    .text-teal { color: #20c997 !important; }
    .text-orange { color: #fd7e14 !important; }
    .btn-orange:hover { background-color: #e8590c !important; }
    .ripple-effect { transition: all 0.2s; }
    .ripple-effect:hover { opacity: 0.7; transform: scale(1.1); }
    .table-hover tbody tr:hover { background-color: #fffaf5 !important; }
    
    .print-area { display: none; }

    /* 🏷️ PLACA MERCOSUL (VERSÃO REDUZIDA 20%) */
    .mercosul-plate {
        width: 80px; height: 32px; background: #fff; border: 1.5px solid #333; border-radius: 4px;
        position: relative; display: flex; flex-direction: column; justify-content: center; align-items: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin: 0 auto;
    }
    .plate-header {
        width: 100%; height: 8px; background: #003399; color: white; font-size: 0.4rem;
        display: flex; justify-content: space-between; align-items: center; padding: 0 3px;
        position: absolute; top: 0; border-radius: 2px 2px 0 0; font-weight: bold;
    }
    .plate-text { font-family: 'Oswald', sans-serif; font-size: 0.85rem; font-weight: 700; color: #111; letter-spacing: 0.5px; margin-top: 6px; }

    @media (min-width: 768px) {
        .mercosul-plate { width: 100px; height: 40px; border: 2px solid #333; }
        .plate-header { height: 10px; font-size: 0.5rem; padding: 0 5px; }
        .plate-text { font-size: 1.1rem; margin-top: 8px; }
    }

    /* 📱 AJUSTES MOBILE */
    @media (max-width: 768px) {
        .container-fluid { padding-left: 0 !important; padding-right: 0 !important; }
        .card-body { padding: 10px !important; }
        .table th, .table td { padding-left: 5px !important; padding-right: 5px !important; }
        .value-responsive { font-size: 0.88rem !important; }
        .btn-action-mobile { width: 100% !important; font-size: 1rem !important; padding: 12px !important; }
        .w-100-mobile { width: 100% !important; }
    }
</style>

<!-- 🖼️ MODAL DE VISUALIZAÇÃO DE COMPROVANTE (UX RASTERTECH) -->
<div class="modal fade" id="photoModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; background: #1a1a1a;">
            <div class="modal-header border-0 p-3 d-flex justify-content-between align-items-center">
                <h5 class="modal-title text-white font-weight-bold ml-2" id="photoTitle">COMPROVANTE</h5>
                <button type="button" class="close text-white opacity-100" data-dismiss="modal" aria-label="Close" style="outline: none;">
                    <span aria-hidden="true" style="font-size: 2rem;">&times;</span>
                </button>
            </div>
            <div class="modal-body p-0 text-center bg-dark d-flex align-items-center justify-content-center" style="min-height: 300px;">
                <img src="" id="modalPhoto" class="img-fluid animate__animated animate__zoomIn" style="max-height: 80vh; width: auto;">
            </div>
        </div>
    </div>
</div>

<style>
    /* 🖨️ ESTILOS ESTÉTICOS DA IMPRESSÃO CSS (NOTA FISCAL) */
    @media print {
        @page { size: A4; margin: 15mm; }
        
        /* Oculta completamente a interface do site */
        body * { visibility: hidden; }
        .main-header, .main-sidebar, .screen-area, .content-header, .main-footer { display: none !important; margin: 0 !important; padding: 0 !important; width: 0 !important; height: 0 !important;}
        
        body { margin: 0 !important; padding: 0 !important; background: #FFF !important; }
        .content-wrapper { margin: 0 !important; padding: 0 !important; background: #FFF !important; }
        
        /* Exibe apenas o bloco de impressão desenhado acima */
        .print-area, .print-area * {
            visibility: visible;
        }
        
        /* Posiciona o bloco de impressão no canto exato do papel (Reset absoluto) */
        .print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            display: block;
            background: #fff;
            color: #000;
        }

        /* Estrutura NF */
        .nf-container {
            width: 100%;
            font-family: 'Courier New', Courier, monospace;
            color: #000;
        }

        .nf-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid #000;
            padding: 10px;
            border-radius: 4px;
        }

        .nf-logo-area { width: 35%; border-right: 1px solid #000; padding-right: 10px;}
        .nf-title-area { width: 40%; }
        .nf-meta-area { width: 25%; border-left: 1px solid #000; padding-left: 10px;}

        .nf-box {
            border: 2px solid #000;
            border-radius: 4px;
        }

        .nf-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            border: 2px solid #000;
        }

        .nf-table th {
            background-color: #e9ECEf !important;
            -webkit-print-color-adjust: exact;
            border: 1px solid #000;
            padding: 6px;
            text-align: left;
            font-weight: bold;
        }

        .nf-table td {
            border: 1px solid #000;
            padding: 6px;
        }

        .nf-table tbody tr:nth-child(even) {
            background-color: #f9f9f9 !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>

@push('scripts')
<script>
    /**
     * FILTRO RÁPIDO DE DATAS
     */
    function applyQuickDate(days) {
        const endInput = document.querySelector('input[name="date_end"]');
        const startInput = document.querySelector('input[name="date_start"]');
        
        const today = new Date();
        const offset = today.getTimezoneOffset() * 60000;
        const localISOTime = (new Date(today - offset)).toISOString().slice(0, -1);
        
        endInput.value = localISOTime.split('T')[0];
        
        if (days === 0) {
            startInput.value = localISOTime.split('T')[0];
        } else if (days === 7) {
            const past = new Date();
            past.setDate(today.getDate() - 7);
            const pastLocalISO = (new Date(past - offset)).toISOString().slice(0, -1);
            startInput.value = pastLocalISO.split('T')[0];
        } else if (days === 30) {
            const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
            const firstLocalISO = (new Date(firstDay - offset)).toISOString().slice(0, -1);
            startInput.value = firstLocalISO.split('T')[0];
        }
        
        document.querySelector('form').submit();
    }
    
    $(document).ready(function() {
        if($.fn.select2) {
            $('.select2').select2({ theme: 'bootstrap4' });
        }
    });

    /**
     * 🖼️ MOTOR DE VISUALIZAÇÃO DE FOTOS
     */
    function viewPhoto(url, title) {
        $('#modalPhoto').attr('src', url);
        $('#photoTitle').text(title);
        $('#photoModal').modal('show');
    }
</script>
@endpush
@endsection
