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
    </style>
    @stack('styles')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- ⚓ NAVBAR -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light border-bottom-0 shadow-sm">
        <ul class="navbar-nav">
            <li class="nav-item"><a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a></li>
        </ul>
        <ul class="navbar-nav ml-auto">
            <!-- 🌗 TOGGLE DARK MODE -->
            <li class="nav-item">
                <a class="nav-link" href="#" id="dark-mode-toggle" title="Alternar Modo Escuro/Claro">
                    <i class="fas fa-moon"></i>
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
    <aside class="main-sidebar sidebar-dark-primary elevation-4" id="main-sidebar">
        <a href="/" class="brand-link" id="brand-logo-container">
            <i class="fas fa-satellite-dish ml-3 mr-2 text-primary"></i>
            <span class="brand-text font-weight-bold">RASTERTECH</span>
        </a>
        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center">
                <div class="image"><img src="https://ui-avatars.com/api/?name=Ubiratan&background=00ff88&color=1a1a2e" class="img-circle elevation-2 profile-img" alt="User Image"></div>
                <div class="info">
                    <a href="#" class="d-block text-bold" data-toggle="modal" data-target="#modalPerfil">Ubiratan</a>
                    <span class="small text-muted">Gestor de Operações</span>
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
                    <li class="nav-header">GESTÃO</li>
                    <li class="nav-item">
                        <a href="/customers" class="nav-link {{ request()->is('customers*') ? 'active' : '' }}"><i class="nav-icon fas fa-users"></i><p>Clientes / Frotas</p></a>
                    </li>
                    <li class="nav-item">
                        <a href="/fleets" class="nav-link {{ request()->is('fleets*') ? 'active' : '' }}"><i class="nav-icon fas fa-truck-moving"></i><p>Frotas</p></a>
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
                    <img src="https://ui-avatars.com/api/?name=Ubiratan&background=00ff88&color=1a1a2e" class="img-circle mb-3" style="width: 80px;">
                    <h5 class="text-bold">Ubiratan</h5>
                    <button class="btn btn-outline-primary btn-block btn-sm mt-3">Editar Perfil</button>
                    <button class="btn btn-outline-danger btn-block btn-sm mt-2">Sair</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.1/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>

<script>
    $(function() {
        const body = $('body');
        const navbar = $('.main-header');
        const sidebar = $('#main-sidebar');
        const toggleBtn = $('#dark-mode-toggle');
        const icon = toggleBtn.find('i');

        // 🧠 MEMÓRIA DE CORES: Recupera a preferência salva
        if (localStorage.getItem('raster_theme') === 'dark') {
            enableDarkMode();
        }

        toggleBtn.on('click', function(e) {
            e.preventDefault();
            if (body.hasClass('dark-mode')) {
                disableDarkMode();
            } else {
                enableDarkMode();
            }
        });

        function enableDarkMode() {
            body.addClass('dark-mode');
            navbar.removeClass('navbar-white navbar-light').addClass('navbar-dark navbar-black');
            sidebar.removeClass('sidebar-light-primary').addClass('sidebar-dark-primary');
            icon.removeClass('fa-moon').addClass('fa-sun');
            localStorage.setItem('raster_theme', 'dark');
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: 'dark' } }));
        }

        function disableDarkMode() {
            body.removeClass('dark-mode');
            navbar.removeClass('navbar-dark navbar-black').addClass('navbar-white navbar-light');
            sidebar.removeClass('sidebar-dark-primary').addClass('sidebar-light-primary');
            icon.removeClass('fa-sun').addClass('fa-moon');
            localStorage.setItem('raster_theme', 'light');
            window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: 'light' } }));
        }
    });
</script>

@stack('scripts')
</body>
</html>
</body>
</html>
