<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatório de Auditoria Rastertech</title>
    <style>
        @page { size: A4; margin: 0; }
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 0; background: #eee; }
        
        .page {
            width: 210mm;
            min-height: 297mm;
            padding: 20mm;
            margin: 10mm auto;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            position: relative;
        }

        .header { text-align: center; border-bottom: 2px solid #007bff; padding-bottom: 15px; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #007bff; text-transform: uppercase; font-size: 24px; }
        .header p { margin: 5px 0 0; color: #666; font-size: 12px; }
        
        .stats-grid { width: 100%; margin-bottom: 30px; border-collapse: collapse; }
        .stats-card { background: #f8f9fa; border: 1px solid #dee2e6; padding: 15px; text-align: center; width: 25%; }
        .stats-label { font-size: 10px; text-transform: uppercase; color: #888; margin-bottom: 5px; }
        .stats-value { font-size: 18px; font-weight: bold; color: #222; }

        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #f1f3f5; color: #495057; text-transform: uppercase; font-size: 10px; padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6; }
        td { padding: 10px; border-bottom: 1px solid #eee; font-size: 11px; }
        
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #aaa; border-top: 1px solid #eee; padding-top: 10px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="page">
        <div class="header">
        <h1>RASTERTECH OPERATIONAL SYSTEMS</h1>
        <p>
            @if($type === 'chips') Auditoria Detalhada de Chips (SIM Cards)
            @elseif($type === 'vehicles') Auditoria Geral de Veículos (Frota)
            @elseif($type === 'customers') Relatório de Carteira de Clientes
            @elseif($type === 'users') Auditoria de Usuários Internos (Base)
            @elseif($type === 'sub_users') Auditoria de Sub-Usuários (Acessos)
            @else Auditoria Geral de Inventário e Saúde Operacional
            @endif
        </p>
        <p style="font-size: 10px;">ID do Documento: RT-{{ strtoupper($type) }}-{{ date('YmdHi') }} | Gerado em {{ date('d/m/Y H:i') }}</p>
    </div>

    @if($type === 'inventory')
        <table class="stats-grid">
            <tr>
                <td class="stats-card"><div class="stats-label">TOTAL CHIPS</div><div class="stats-value">{{ number_format($stats['sims_total'], 0, ',', '.') }}</div></td>
                <td class="stats-card"><div class="stats-label">DISPOSITIVOS</div><div class="stats-value">{{ number_format($stats['devices_total'], 0, ',', '.') }}</div></td>
                <td class="stats-card"><div class="stats-label">VEÍCULOS</div><div class="stats-value">{{ number_format($stats['vehicles_total'], 0, ',', '.') }}</div></td>
                <td class="stats-card"><div class="stats-label">CLIENTES</div><div class="stats-value">{{ number_format($stats['customers_total'], 0, ',', '.') }}</div></td>
            </tr>
        </table>
    @endif

    <h2 style="font-size: 14px; color: #444; margin-bottom: 10px; border-left: 4px solid #007bff; padding-left: 10px;">
        @if($type === 'chips') LISTAGEM DE CONECTIVIDADE
        @elseif($type === 'vehicles') LISTAGEM DE ATIVOS (FROTA)
        @elseif($type === 'customers') LISTAGEM DE CLIENTES ATIVOS
        @elseif($type === 'users') LISTAGEM DE USUÁRIOS DO SISTEMA
        @elseif($type === 'sub_users') LISTAGEM DE SUB-CONTAS (CLIENTES)
        @else RESUMO DE ATIVIDADE OPERACIONAL
        @endif
    </h2>

    <table>
        <thead>
            @if($type === 'chips')
                <tr>
                    <th>ICCID</th>
                    <th>NÚMERO</th>
                    <th>OPERADORA</th>
                    <th>STATUS</th>
                </tr>
            @elseif($type === 'vehicles')
                <tr>
                    <th>PLACA</th>
                    <th>VEÍCULO</th>
                    <th>CLIENTE TITULAR</th>
                    <th>CADASTRO</th>
                </tr>
            @elseif($type === 'customers')
                <tr>
                    <th>NOME / RAZÃO SOCIAL</th>
                    <th>DOCUMENTO</th>
                    <th>DATA ADESÃO</th>
                </tr>
            @elseif($type === 'users')
                <tr>
                    <th>NOME COMPLETO</th>
                    <th>EMAIL OPERACIONAL</th>
                    <th>TIPO / PATENTE</th>
                </tr>
            @elseif($type === 'sub_users')
                <tr>
                    <th>NOME DO ACESSO</th>
                    <th>EMPRESA TITULAR</th>
                    <th>VÍNCULO EM</th>
                </tr>
            @else
                <tr>
                    <th>INDICADOR</th>
                    <th>VALOR ATUAL</th>
                    <th>STATUS DE SAÚDE</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @if($type === 'inventory')
                <tr>
                    <td>Chips em Operação</td>
                    <td>{{ $stats['sims_active'] ?? 0 }} unidades</td>
                    <td style="color: green">SAUDÁVEL</td>
                </tr>
                <tr>
                    <td>Rastreadores On-line</td>
                    <td>{{ $stats['devices_active'] }} unidades</td>
                    <td style="color: green">OPERACIONAL</td>
                </tr>
            @else
                @foreach($data as $item)
                    @if($type === 'chips')
                        <tr>
                            <td>{{ $item->iccid }}</td>
                            <td>{{ $item->phone_number }}</td>
                            <td>{{ $item->operator }}</td>
                            <td>{{ strtoupper($item->status) }}</td>
                        </tr>
                    @elseif($type === 'vehicles')
                        <tr>
                            <td>{{ $item->plate }}</td>
                            <td>{{ $item->brand }} {{ $item->model }}</td>
                            <td>{{ $item->customer->name ?? 'N/A' }}</td>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @elseif($type === 'customers')
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->document }}</td>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @elseif($type === 'users')
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ strtoupper($item->type ?? 'OPERADOR') }}</td>
                        </tr>
                    @elseif($type === 'sub_users')
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->customer->name ?? 'RESTRITO' }}</td>
                            <td>{{ $item->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>

    <div style="margin-top: 40px; border: 1px dashed #ccc; padding: 20px; border-radius: 8px;">
        <h3 style="margin-top: 0; font-size: 14px;">📝 NOTA DE AUDITORIA:</h3>
        <p style="font-size: 11px; color: #666; line-height: 1.5;">
            Este documento representa o estado digital da infraestrutura Rastertech no momento da sua geração. Os dados aqui contidos são extraídos diretamente do banco de dados central e auditados para fins de conformidade operacional.
        </p>
    </div>

        <div class="footer">
            Rastertech ERP v1.0 - Segurança, Inteligência e Telemetria de Elite.
        </div>
    </div>

    <!-- 🖨️ COMANDO FLUTUANTE DE IMPRESSÃO -->
    <div class="no-print" style="position: fixed; top: 20px; right: 20px; z-index: 9999;">
        <button onclick="window.print()" style="background: #007bff; color: white; border: none; padding: 12px 25px; border-radius: 30px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 15px rgba(0,0,0,0.2); display: flex; align-items: center; gap: 10px;">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            IMPRIMIR / SALVAR PDF
        </button>
    </div>

    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .page { margin: 0; box-shadow: none; border: none; }
        }
    </style>
    <script>
        // Auto-print se solicitado por query string
        if(window.location.search.includes('print=true')){
            window.print();
        }
    </script>
</body>
</html>
