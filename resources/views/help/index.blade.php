@extends('layouts.app')

@section('title', 'Manual de Operações')

@section('content')
<div class="container-fluid">
    <!-- 🛰️ CABEÇALHO TÁTICO -->
    <div class="row m-0 mb-4 animate__animated animate__fadeIn align-items-center">
        <div class="col-sm-12 p-0 p-sm-2">
            <h1 class="m-0 text-bold" style="font-size: 2.2rem;">
                <i class="fas fa-book-reader mr-2 text-primary"></i>Central de Inteligência
            </h1>
            <p class="text-muted mb-0">Guia oficial de comando e operações táticas Rastertech.</p>
        </div>
    </div>

    <div class="row mt-4 h-100">
        <!-- 📑 NAVEGAÇÃO RÁPIDA -->
        <div class="col-md-3">
            <div class="card card-outline card-primary shadow-sm border-0 sticky-top" style="top: 20px; border-radius: 12px;">
                <div class="card-header border-0 bg-transparent">
                    <h3 class="card-title text-bold mb-0">Sumário do Comando</h3>
                </div>
                <div class="card-body p-0">
                    <nav class="nav flex-column help-nav">
                        <a class="nav-link py-3 px-4 border-bottom active" href="#intro"><i class="fas fa-info-circle mr-2"></i>Introdução</a>
                        <a class="nav-link py-3 px-4 border-bottom" href="#patentes"><i class="fas fa-id-badge mr-2"></i>Hierarquia (Patentes)</a>
                        <a class="nav-link py-3 px-4 border-bottom" href="#dashboard"><i class="fas fa-tachometer-alt mr-2"></i>Dashboard (Comando)</a>
                        <a class="nav-link py-3 px-4 border-bottom" href="#logistica"><i class="fas fa-sim-card mr-2"></i>Logística & Hardware</a>
                        <a class="nav-link py-3 px-4 border-bottom" href="#gestao"><i class="fas fa-users mr-2"></i>Gestão & Clientes</a>
                        <a class="nav-link py-3 px-4 border-bottom" href="#atendimento"><i class="fas fa-headset mr-2"></i>Central de Atendimento</a>
                        <a class="nav-link py-3 px-4 border-bottom" href="#tecnica"><i class="fas fa-comment-dots mr-2"></i>Engenharia Técnica</a>
                        <a class="nav-link py-3 px-4" href="#admin"><i class="fas fa-user-shield mr-2"></i>Administração</a>
                    </nav>
                </div>
            </div>
        </div>

        <!-- 📖 CONTEÚDO DO MANUAL -->
        <div class="col-md-9 pb-5">
            
            <!-- ℹ️ INTRODUÇÃO -->
            <div id="intro" class="card shadow-sm border-0 mb-4 animate__animated animate__fadeInUp" style="border-radius: 12px;">
                <div class="card-body p-4 p-md-5">
                    <h2 class="text-bold text-primary mb-4">Manual de Operações Rastertech</h2>
                    <p class="lead text-muted">Bem-vindo ao centro de operações! Este manual foi projetado para capacitar todos os membros da equipe com o conhecimento necessário para operar o ecossistema Rastertech no **Padrão Ouro**.</p>
                    <div class="alert alert-info border-0 shadow-sm mt-4" style="background: rgba(0, 123, 255, 0.05); border-left: 5px solid #007bff !important;">
                        <i class="fas fa-satellite mr-2 text-primary"></i> <strong>Operação Unificada:</strong> O sistema integra telemetria, logística e gestão de clientes em uma única plataforma táctica.
                    </div>
                </div>
            </div>

            <!-- 🛡️ HIERARQUIA -->
            <div id="patentes" class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header border-0 bg-transparent px-4 pt-4">
                    <h3 class="text-bold m-0"><i class="fas fa-id-badge mr-2 text-primary"></i>1. Hierarquia de Comando</h3>
                </div>
                <div class="card-body p-4 px-md-5 pt-2">
                    <p class="text-muted">O sistema opera sob uma estrutura de 5 patentes oficiais, garantindo que cada oficial tenha as ferramentas exatas para sua missão:</p>
                    <div class="row mt-4">
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded border-left border-primary" style="height: 100%;">
                                <h6 class="text-bold text-primary text-uppercase mb-1">Administrador</h6>
                                <p class="small mb-0">Poder total de comando. Gestão de usuários, configurações e auditoria total do sistema.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded border-left border-info" style="height: 100%;">
                                <h6 class="text-bold text-info text-uppercase mb-1">Gerente</h6>
                                <p class="small mb-0">Controle operacional quase total. Gestão de frotas, clientes e supervisão tática.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded border-left border-warning" style="height: 100%;">
                                <h6 class="text-bold text-warning text-uppercase mb-1">Suporte Técnico</h6>
                                <p class="small mb-0">Especialista em Atendimento. Visualização de ativos e auxílio técnico direto ao cliente.</p>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="p-3 bg-light rounded border-left border-success" style="height: 100%;">
                                <h6 class="text-bold text-success text-uppercase mb-1">Técnico Instalador</h6>
                                <p class="small mb-0">Atuação em campo. Acesso direto ao Checklist Operacional e Portal do Cliente.</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="p-3 bg-light rounded border-left border-dark" style="height: 100%;">
                                <h6 class="text-bold text-dark text-uppercase mb-1">Cliente</h6>
                                <p class="small mb-0">Acesso exclusivo ao Portal do Cliente para visualização de frotas e motoristas próprios.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🛰️ DASHBOARD -->
            <div id="dashboard" class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header border-0 bg-transparent px-4 pt-4">
                    <h3 class="text-bold m-0"><i class="fas fa-tachometer-alt mr-2 text-success"></i>2. Comando de Operações (Dashboard)</h3>
                </div>
                <div class="card-body p-4 px-md-5 pt-2">
                    <p class="text-muted text-justify">O Dashboard é o ponto de partida de todas as sessões. Ele fornece indicadores globais da infraestrutura:</p>
                    <ul class="text-muted mt-3">
                        <li class="mb-2"><strong>Total de Equipamentos:</strong> Contagem real de hardwares cadastrados.</li>
                        <li class="mb-2"><strong>Chips GSM:</strong> Status da conectividade em tempo real.</li>
                        <li class="mb-2"><strong>Mapa Operacional:</strong> Visualização geográfica das últimas transmissões de telemetria.</li>
                    </ul>
                </div>
            </div>

            <!-- 📦 LOGÍSTICA -->
            <div id="logistica" class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header border-0 bg-transparent px-4 pt-4">
                    <h3 class="text-bold m-0"><i class="fas fa-sim-card mr-2 text-indigo"></i>3. Logística & Hardware</h3>
                </div>
                <div class="card-body p-4 px-md-5 pt-2">
                    <p class="text-muted">A base tática do sistema reside na correta gestão dos insumos tecnológicos:</p>
                    <ol class="text-muted mt-3">
                        <li class="mb-3"><strong>Cartões SIM (Chips):</strong> Registro cuidadoso de operadoras e números. O status permite identificar se o chip está em estoque, ativo em um cliente ou em manutenção.</li>
                        <li class="mb-3"><strong>Equipamentos (Hardware):</strong> Cadastro via IMEI. Todo dispositivo deve ser vinculado a um Modelo e estar associado a um chip para operação plena.</li>
                    </ol>
                </div>
            </div>

            <!-- 🏗️ GESTÃO -->
            <div id="gestao" class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header border-0 bg-transparent px-4 pt-4">
                    <h3 class="text-bold m-0"><i class="fas fa-users mr-2 text-info"></i>4. Gestão de Clientes e Frotas</h3>
                </div>
                <div class="card-body p-4 px-md-5 pt-2">
                    <p class="text-muted">Este módulo conecta a tecnologia ao usuário final:</p>
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="p-3 border rounded text-center" style="height: 100%;">
                                <i class="fas fa-user-friends fa-2x mb-3 text-info"></i>
                                <h6 class="text-bold">Clientes</h6>
                                <p class="small text-muted mb-0">Cadastro de empresas ou pessoas físicas com dossiers táticos.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded text-center" style="height: 100%;">
                                <i class="fas fa-car fa-2x mb-3 text-info"></i>
                                <h6 class="text-bold">Veículos</h6>
                                <p class="small text-muted mb-0">Vinculação de placas e equipamentos aos proprietários.</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded text-center" style="height: 100%;">
                                <i class="fas fa-key fa-2x mb-3 text-info"></i>
                                <h6 class="text-bold">Credenciais APPs</h6>
                                <p class="small text-muted mb-0">Gestão de acesso para as lojas de aplicativos (Android/iOS).</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 🎧 ATENDIMENTO -->
            <div id="atendimento" class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header border-0 bg-transparent px-4 pt-4">
                    <h3 class="text-bold m-0"><i class="fas fa-headset mr-2 text-warning"></i>5. Central de Atendimento</h3>
                </div>
                <div class="card-body p-4 px-md-5 pt-2">
                    <p class="text-muted">Especializada no suporte técnico avançado. Aqui, o oficial pode:</p>
                    <ul class="text-muted mt-3">
                        <li class="mb-2">Visualizar a saúde técnica de todos os dispositivos de um cliente.</li>
                        <li class="mb-2">Identificar rapidamente problemas de sinal (Chip) ou ignição (Veículo).</li>
                        <li class="mb-2">Utilizar o sistema de acordeon para suporte ágil.</li>
                    </ul>
                </div>
            </div>

            <!-- ⌨️ TÉCNICA -->
            <div id="tecnica" class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header border-0 bg-transparent px-4 pt-4">
                    <h3 class="text-bold m-0"><i class="fas fa-comment-dots mr-2 text-primary"></i>6. Engenharia Técnica (SMS)</h3>
                </div>
                <div class="card-body p-4 px-md-5 pt-2">
                    <p class="text-muted font-italic">Este módulo é reservado para operações de alta tecnicidade.</p>
                    <p class="text-muted">A **Central de Comandos SMS** permite configurar os equipamentos remotamente. Cada comando deve ser validado pelo Modelo de Equipamento correspondente.</p>
                    <div class="alert alert-warning py-2 mt-3" style="font-size: 0.9rem;">
                        <i class="fas fa-exclamation-triangle mr-2"></i> <strong>Aviso:</strong> Comandos enviados incorretamente podem interromper a telemetria do veículo.
                    </div>
                </div>
            </div>

            <!-- 🛡️ ADMIN -->
            <div id="admin" class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
                <div class="card-header border-0 bg-transparent px-4 pt-4">
                    <h3 class="text-bold m-0"><i class="fas fa-user-shield mr-2 text-danger"></i>7. Administração & Usuários Internos</h3>
                </div>
                <div class="card-body p-4 px-md-5 pt-2 pb-5">
                    <p class="text-muted">Gestão estratégica de acesso. O Administrador deve garantir que:</p>
                    <ul class="text-muted mt-3">
                        <li class="mb-2">Cada funcionário possua apenas uma conta individual.</li>
                        <li class="mb-2">O gênero seja selecionado corretamente para a geração de avatares automáticos.</li>
                        <li class="mb-2">A alteração de patentes seja feita apenas sob autorização da diretoria.</li>
                    </ul>
                </div>
            </div>
            
        </div>
    </div>
</div>

<style>
    .help-nav .nav-link { color: #6c757d; font-weight: 500; transition: all 0.3s; }
    .help-nav .nav-link:hover { background: rgba(0, 123, 255, 0.05); color: #007bff; padding-left: 1.8rem !important; }
    .help-nav .nav-link.active { background: #007bff; color: white !important; }
    
    html { scroll-behavior: smooth; }
    
    .sticky-top { transition: top 0.3s ease-in-out; }
    
    .card h3 { font-size: 1.4rem; }
    .card h2 { font-size: 1.8rem; }
    
    /* 🌓 DARK MODE SUPPORT */
    .dark-mode .bg-light { background-color: #1a1a2e !important; }
    .dark-mode .help-nav .nav-link:hover { background-color: rgba(0, 123, 100, 0.1); color: #00ff88; }
    .dark-mode .card { background-color: #16213e; }
    .dark-mode .nav-link.active { background: #00ff88; color: #1a1a2e !important; }
    .dark-mode .text-muted { color: #b2bac2 !important; }
    .dark-mode .border { border-color: #2e3b5e !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('.card[id]');
    const navLinks = document.querySelectorAll('.help-nav .nav-link');

    window.addEventListener('scroll', () => {
        let current = '';
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= (sectionTop - 150)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href').includes(current)) {
                link.classList.add('active');
            }
        });
    });
});
</script>
@endsection
