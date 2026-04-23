@extends('layouts.app')

@section('title', 'Comando de Frota | Rastertech')

@section('content')
<div class="container-fluid" id="portal-container">
    <!-- ⚓ TOPO ESTRATÉGICO (INDICADORES TÁTICOS) -->
    <div class="row mb-4 animate__animated animate__fadeIn">
        <div class="col-12 col-md-4 mb-3" onclick="loadComponent('suporte')" style="cursor: pointer;">
            <div class="card bg-warning shadow-sm border-0 h-100 hover-zoom" style="border-radius: 12px; transition: transform 0.2s;">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <div class="text-uppercase font-weight-bold opacity-75 mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">SUPORTE RTECH</div>
                        <h3 class="text-bold m-0">CHAT SUPORTE</h3>
                    </div>
                    <i class="fas fa-headset fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3" onclick="loadComponent('motoristas')" style="cursor: pointer;">
            <div class="card bg-teal shadow-sm border-0 h-100 hover-zoom text-white" style="border-radius: 12px; background-color: #20c997 !important; transition: transform 0.2s;">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <div class="text-uppercase font-weight-bold opacity-75 mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">LOGÍSTICA TOTAL</div>
                        <h3 class="text-bold m-0">MOTORISTAS</h3>
                    </div>
                    <i class="fas fa-id-card fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-4 mb-3" onclick="loadComponent('veiculos')" style="cursor: pointer;">
            <div class="card bg-dark shadow-sm border-0 h-100 hover-zoom text-white" style="border-radius: 12px; transition: transform 0.2s;">
                <div class="card-body d-flex align-items-center justify-content-between p-4">
                    <div>
                        <div class="text-uppercase font-weight-bold opacity-75 mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">SITUAÇÃO DE FROTA</div>
                        <h3 class="text-bold m-0">VEÍCULOS</h3>
                    </div>
                    <i class="fas fa-truck-moving fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- 🛡️ BARRA DE FILTRO TÁTICO (EXCLUSIVA PARA ADMIN / GESTÃO) -->
    @if($isAdminLevel)
    <div class="row mb-5">
        <div class="col-12">
            <div class="card shadow-sm border-0" style="border-radius: 12px; background: #f8f9fa;">
                <div class="card-body p-4">
                    <div class="row align-items-end">
                        <div class="col-md-5 mb-3 mb-md-0">
                            <label class="text-xs text-uppercase text-muted font-weight-bold d-block mb-2">Selecione o Cliente para Monitorar</label>
                            <select id="select-customer" class="form-control form-control-lg border-0 shadow-sm" style="border-radius: 8px;">
                                <option value="">--- ESCOLHA UM CLIENTE ---</option>
                                @foreach($customers as $c)
                                    <option value="{{ $c->id }}" {{ session('portal_customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-7 d-flex">
                            <button class="btn btn-teal btn-lg flex-fill mr-2 py-3 shadow-sm font-weight-bold driver-btn" 
                                    style="border-radius: 10px; background-color: #20c997 !important; border: 0; display: {{ session('portal_customer_id') ? 'block' : 'none' }}; color: white;"
                                    onclick="loadComponent('motoristas')">
                                <i class="fas fa-id-card mr-2"></i>MOSTRAR MOTORISTAS
                            </button>
                            <button class="btn btn-dark btn-lg flex-fill py-3 shadow-sm font-weight-bold vehicle-btn" 
                                    style="border-radius: 10px; border: 0; display: {{ session('portal_customer_id') ? 'block' : 'none' }};"
                                    onclick="loadComponent('veiculos')">
                                <i class="fas fa-truck mr-2"></i>MOSTRAR VEÍCULOS
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- 📊 CENTRAL DINÂMICA (Feeling PWA) -->
    <div class="card shadow-sm border-0 animate__animated animate__fadeInUp" style="border-radius: 12px; min-height: 500px;">
        <div class="card-header bg-transparent border-0 px-4 pt-4 d-flex justify-content-between align-items-center">
            <h4 class="m-0 text-bold" id="component-title">Monitoramento de Veículos</h4>
            <div class="d-flex" style="gap: 10px;">
                <button class="btn btn-sm btn-light border" onclick="loadComponent('perfil')" title="Minha Conta">
                    <i class="fas fa-user-cog mr-1"></i><span class="d-none d-sm-inline">Meu Perfil</span>
                </button>
            </div>
        </div>
        <div class="card-body p-4" id="portal-content">
            <!-- Loader Inicial -->
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status"></div>
                <p class="mt-3 text-muted">Carregando painel operacional...</p>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-zoom:hover { transform: scale(1.02); }
    .opacity-75 { opacity: 0.75; }
    .opacity-50 { opacity: 0.5; }
    .text-xs { font-size: 0.75rem; }
    
    /* 🌓 ADAPTAÇÃO DARK MODE RASTERTECH */
    .dark-mode .card { background: #1a1a2e; }
    .dark-mode .btn-light { background: #16213e; border-color: #2d2d44 !important; color: #fff; }
</style>

@endsection

@push('scripts')
<script>
    /**
     * AJAX LOADER (PWA ENGINE)
     * Gerencia a troca de "telas" sem recarregar o layout.
     */
    function loadComponent(name, params = '') {
        const contentArea = $('#portal-content');
        const titleArea = $('#component-title');
        
        // Loader visual
        contentArea.html('<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>');

        // Mapeamento de Títulos
        const titles = {
            'veiculos': 'Monitoramento de Veículos',
            'motoristas': 'Gestão de Motoristas & CNH',
            'perfil': 'Minha Conta Rastertech',
            'suporte': 'Central de Triagem RTech',
            'checklist': 'Terminal de Checklist'
        };

        titleArea.text(titles[name] || 'Painel do Cliente');

        // Requisição AJAX para buscar o componente
        const url = `/portal/view/${name}` + (params ? `?${params}` : '');
        $.get(url, function(html) {
            contentArea.html(html).addClass('animate__animated animate__fadeIn');
        }).fail(function() {
            contentArea.html('<div class="alert alert-danger">Erro ao carregar o módulo operacional.</div>');
        });
    }

    // Inicializa com a lista de veículos
    $(document).ready(function() {
        loadComponent('veiculos');
    });
    /**
     * SELETOR DE CLIENTE (ALTERAÇÃO DE CONTEXTO)
     */
    $('#select-customer').change(function() {
        const id = $(this).val();
        if (id) {
            // Atualiza a sessão via recarregamento de contexto (index)
            window.location.href = `/portal?customer_id=${id}`;
        } else {
            $('.driver-btn, .vehicle-btn').fadeOut();
        }
    });

    // Inicia com o componente de motoristas se já houver um cliente na sessão
    @if(session('portal_customer_id'))
        $(document).ready(function() {
            // loadComponent('motoristas'); // Opcional: já carregar ao entrar
        });
    @endif
</script>
@endpush
