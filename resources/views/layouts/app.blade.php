<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rastertech | ERP Telemetria</title>
    
    <!-- AdminLTE CSS & Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">

    <style>
        :root { --primary-cyber: #00ff88; --dark-depth: #1a1a2e; }
        .nav-link.active { background-color: var(--primary-cyber) !important; color: #1a1a2e !important; }
        .profile-img { width: 35px; height: 35px; object-fit: cover; border: 2px solid var(--primary-cyber); }

        /* 🛰️ DINAMISMO DO LOGO RASTERTECH */
        .brand-link { height: 60px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 0 !important; }
        .brand-image-raster { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1)); }
        
        /* MODO EXPANDIDO: LOGO COMPLETO */
        .sidebar-mini:not(.sidebar-collapse) .brand-image-raster { width: 180px !important; height: auto !important; object-fit: contain; object-position: left; margin-left: 15px; }
        .sidebar-mini:not(.sidebar-collapse) .brand-text { display: none !important; } /* A escrita já está na imagem */

        /* MODO COLAPSADO: APENAS EMBLEMA */
        .sidebar-collapse .brand-image-raster { width: 38px !important; height: 38px !important; object-fit: cover; object-position: left; margin-left: 0 !important; border-radius: 8px; }
        .sidebar-collapse .brand-link { justify-content: center; }
    </style>
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed {{ (auth()->check() && auth()->user()->theme == 'dark') ? 'dark-mode' : '' }}">
<div class="wrapper">

    <!-- ⚓ NAVBAR -->
    <nav class="main-header navbar navbar-expand {{ (auth()->check() && auth()->user()->theme == 'dark') ? 'navbar-dark navbar-black' : 'navbar-white navbar-light' }} border-bottom-0 shadow-sm">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <!-- 🌗 TOGGLE DARK MODE -->
            <li class="nav-item">
                <a class="nav-link" href="#" id="dark-mode-toggle" title="Alternar Modo Escuro/Claro">
                    <i class="fas {{ (auth()->check() && auth()->user()->theme == 'dark') ? 'fa-sun' : 'fa-moon' }}"></i>
                </a>
            </li>
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#"><i class="far fa-bell"></i><span class="badge badge-warning navbar-badge">15</span></a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header font-weight-bold">Central de Alertas</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item small text-muted text-center">Ver todas</a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- 🏗️ SIDEBAR -->
    <aside class="main-sidebar {{ (auth()->check() && auth()->user()->theme == 'dark') ? 'sidebar-dark-primary' : 'sidebar-light-primary' }} elevation-4" id="main-sidebar">
        <a href="/" class="brand-link">
            <img src="{{ asset('img/logo_rastertech.png') }}" alt="Rastertech Logo" class="brand-image-raster">
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
                <div class="image"><img src="https://ui-avatars.com/api/?name={{ urlencode(optional(auth()->user())->name ?? 'Visitante') }}&background=00ff88&color=1a1a2e" class="img-circle elevation-2 profile-img" alt="User Image"></div>
                <div class="info">
                    <a href="#" class="d-block text-bold" data-toggle="modal" data-target="#modalPerfil">{{ optional(auth()->user())->name ?? 'Usuário' }}</a>
                    <span class="small text-muted">{{ optional(auth()->user())->role ?? 'N/A' }}</span>
                </div>
            </div>
            <nav class="mt-2 text-sm text-uppercase font-weight-bold">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="/" class="nav-link {{ request()->is('/') ? 'active' : '' }}"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a>
                    </li>
                    <li class="nav-header">LOGÍSTICA</li>
                    <li class="nav-item">
                        <a href="/sim-cards" class="nav-link {{ request()->is('sim-cards*') ? 'active' : '' }}"><i class="nav-icon fas fa-sim-card"></i><p>Cartões SIM</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="/devices" class="nav-link {{ request()->is('devices*') ? 'active' : '' }}"><i class="nav-icon fas fa-microchip"></i><p>Equipamentos</p></a>
                    </li>
                    <li class="nav-header">GESTÃO</li>
                    <li class="nav-item">
                        <a href="/customers" class="nav-link {{ request()->is('customers*') ? 'active' : '' }}"><i class="nav-icon fas fa-users"></i><p>Clientes / Frotas</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="/fleets" class="nav-link {{ request()->is('fleets*') ? 'active' : '' }}"><i class="nav-icon fas fa-truck-moving"></i><p>Veículos</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="/missoes" class="nav-link {{ request()->is('missoes*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-route text-teal"></i>
                            <p>Missões em Campo</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="/customer-sub-users" class="nav-link {{ request()->is('customer-sub-users*') ? 'active' : '' }}"><i class="nav-icon fas fa-users-cog"></i><p>Credenciais APPs</p></a>
                    </li>

                    <li class="nav-header">DEPARTAMENTO TÉCNICO</li>
                    <li class="nav-item">
                        <a href="/portal" class="nav-link {{ (request()->is('portal') || (request()->is('portal/*') && !request()->is('portal/verificacoes*') && !request()->is('portal/despesas*') && !request()->is('portal/instalador*'))) ? 'bg-indigo text-white shadow' : '' }}"><i class="nav-icon fas fa-user-shield"></i><p>PORTAL DO CLIENTE</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="/providers" class="nav-link {{ request()->is('providers*') ? 'active' : '' }}"><i class="nav-icon fas fa-industry"></i><p>Fornecedores</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="/platforms" class="nav-link {{ request()->is('platforms*') ? 'active' : '' }}"><i class="nav-icon fas fa-server"></i><p>Plataformas</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="/device-models" class="nav-link {{ request()->is('device-models*') ? 'active' : '' }}"><i class="nav-icon fas fa-microchip"></i><p>Modelos RTECH</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="/device-commands" class="nav-link {{ request()->is('device-commands*') ? 'active' : '' }}"><i class="nav-icon fas fa-comment-dots"></i><p>Comandos SMS</p></a>
                    </li>

                    @php $userRole = auth()->check() ? auth()->user()->role : 'guest'; @endphp
                    @if(in_array($userRole, ['admin', 'gestor', 'operador', 'atendente', 'Suporte Técnico', 'Gerente', 'Administrador', 'Gestor de Operações', 'guest']))
                    <li class="nav-header">ATENDIMENTOS</li>
                    <li class="nav-item">
                        <a href="/support/customers" class="nav-link {{ request()->is('support/customers*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-headset text-warning"></i>
                            <p>Clientes Ativos</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.installations.index') }}" class="nav-link {{ request()->is('admin/installations*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-check-double text-success"></i>
                            <p>Validação</p>
                        </a>
                    </li>
                    @endif

                    <!-- 🚜 MÓDULO DO MOTORISTA (VERIFICAÇÕES & DESPESAS) -->
                    <li class="nav-header">MOTORISTA</li>
                    <li class="nav-item">
                        <a href="{{ route('portal.verificacoes.index') }}" class="nav-link {{ request()->is('portal/verificacoes*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-check text-teal"></i>
                            <p>CHECK-IN/OUT</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('portal.despesas.index') }}" class="nav-link {{ request()->is('portal/despesas*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-file-invoice-dollar text-orange"></i>
                            <p>Despesas</p>
                        </a>
                    </li>

                    <!-- 🔧 MÓDULO DO INSTALADOR (VISTORIAS TÉCNICAS) -->
                    <li class="nav-header">INSTALADOR</li>
                    <li class="nav-item">
                        <a href="{{ route('portal.instalador.index') }}" class="nav-link {{ request()->is('portal/instalador*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tools text-primary"></i>
                            <p>Instalações</p>
                        </a>
                    </li>

                    <li class="nav-header">ADMINISTRAÇÃO</li>
                    <li class="nav-item">
                        <a href="/users" class="nav-link {{ request()->is('users*') ? 'active' : '' }}"><i class="nav-icon fas fa-user-shield"></i><p>Usuários Internos</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="/reports" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}"><i class="nav-icon fas fa-file-invoice"></i><p>Relatórios</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}"><i class="nav-icon fas fa-cog"></i><p>Configurações</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('help') }}" class="nav-link {{ request()->is('help*') ? 'active' : '' }} text-info">
                            <i class="nav-icon fas fa-question-circle shadow-sm"></i>
                            <p>Manual de Operações</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- 📈 CONTENT -->
    <div class="content-wrapper p-3">
        @yield('content')
    </div>

    <!-- 🛡️ FOOTER -->
    <footer class="main-footer text-muted border-top-0">
        <strong>Copyright &copy; 2026 <a href="#" class="text-primary">Rastertech</a>.</strong>
        Todos os direitos reservados.
        <div class="float-right d-none d-sm-inline-block"><b>Versão</b> 1.1.0-Stable</div>
    </footer>

    <!-- 👤 MODAL PERFIL -->
    <div class="modal fade" id="modalPerfil" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">
                <div class="modal-body text-center p-4">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(optional(auth()->user())->name ?? 'Visitante') }}&background=00ff88&color=1a1a2e" class="img-circle mb-3" style="width: 80px;">
                    <h5 class="text-bold">{{ optional(auth()->user())->name ?? 'Usuário' }}</h5>
                    <p class="text-muted small mb-0">{{ optional(auth()->user())->role ?? 'N/A' }}</p>
                    <button class="btn btn-outline-primary btn-block btn-sm mt-3">Editar Perfil</button>
                    <button class="btn btn-outline-danger btn-block btn-sm mt-2" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sair</button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
    $(function() {
        const body = $('body');
        const navbar = $('.main-header');
        const sidebar = $('#main-sidebar');

        // 🔥 COMANDO DE TEMA: Captura o clique de forma robusta e imediata
        $(document).on('click', '#dark-mode-toggle', function(e) {
            e.preventDefault();
            const btn = $(this);
            const icon = btn.find('i');

            if (body.hasClass('dark-mode')) {
                // MUDAR PARA TEMA CLARO 🌙 (Aparecer Lua para poder escurecer depois)
                body.removeClass('dark-mode');
                navbar.removeClass('navbar-dark navbar-black').addClass('navbar-white navbar-light');
                sidebar.removeClass('sidebar-dark-primary').addClass('sidebar-light-primary');
                icon.removeClass('fa-sun').addClass('fa-moon');
                saveThemeProgressively('light');
                window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: 'light' } }));
            } else {
                // MUDAR PARA TEMA ESCURO ☀️ (Aparecer Sol para poder clarear depois)
                body.addClass('dark-mode');
                navbar.removeClass('navbar-white navbar-light').addClass('navbar-dark navbar-black');
                sidebar.removeClass('sidebar-light-primary').addClass('sidebar-dark-primary');
                icon.removeClass('fa-moon').addClass('fa-sun');
                saveThemeProgressively('dark');
                window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: 'dark' } }));
            }
        });

        function saveThemeProgressively(theme) {
            fetch('{{ route("user.update-theme") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ theme: theme })
            });

            // Sincroniza o localStorage como redundância secundária
            localStorage.setItem('raster_theme', theme);
        }
    });
</script>

@stack('scripts')
</body>
</html>
