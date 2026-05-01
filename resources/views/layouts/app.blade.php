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
        .bg-pink { background-color: #e83e8c !important; }
        .nav-link.active { background-color: var(--primary-cyber) !important; color: #1a1a2e !important; }
        .profile-img { width: 39px; height: 39px; object-fit: cover; }

        /* 🛰️ DINAMISMO DO LOGO RASTERTECH */
        .brand-link { height: 60px; display: flex; align-items: center; justify-content: center; overflow: hidden; padding: 0 !important; }
        .brand-image-raster { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1)); }
        
        /* MODO EXPANDIDO: LOGO COMPLETO */
        .sidebar-mini:not(.sidebar-collapse) .brand-image-raster { width: 230px !important; height: auto !important; object-fit: contain; object-position: left; }
        .sidebar-mini:not(.sidebar-collapse) .brand-text { display: none !important; } /* A escrita já está na imagem */

        /* MODO COLAPSADO: APENAS EMBLEMA */
        .sidebar-collapse .brand-image-raster { width: 50px !important; height: 50px !important; object-fit: cover; object-position: left; margin-left: 0 !important; border-radius: 8px; }
        .sidebar-collapse .brand-link { justify-content: center; }
        /* 🇧🇷 ESTILO PLACA MERCOSUL GLOBAL */
        .mercosul-plate { 
            display: inline-flex; 
            flex-direction: column; 
            background: #fff; 
            border: 1.5px solid #000; 
            border-radius: 4px; 
            overflow: hidden; 
            min-width: 100px; 
            line-height: 1; 
            vertical-align: middle; 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-decoration: none !important;
        }
        .mercosul-header { 
            background: #003399; 
            color: #fff; 
            font-size: 0.4rem; 
            text-align: center; 
            padding: 1px 0; 
            font-weight: 800; 
            letter-spacing: 1px; 
            border-bottom: 0.5px solid #000; 
        }
        .mercosul-body { 
            color: #000; 
            font-size: 1.1rem; 
            text-align: center; 
            padding: 3px 8px; 
            font-weight: bold; 
            font-family: 'Roboto Mono', monospace; 
            letter-spacing: -0.5px; 
        }
        .dark-mode .mercosul-plate { border-color: #fff; }
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
            <div class="user-panel mt-3 pb-3 mb-3 d-flex align-items-center" 
                 style="cursor: pointer; transition: all 0.3s; border-radius: 8px;" 
                 onmouseover="this.style.background='rgba(0,0,0,0.05)'" 
                 onmouseout="this.style.background='transparent'"
                 data-toggle="modal" data-target="#modalPerfil">
                <div class="image">
                    @if(optional(auth()->user())->image)
                        <img src="{{ asset('storage/' . auth()->user()->image) }}" class="img-circle elevation-2 profile-img" alt="User Image" style="width: 39px; height: 39px; object-fit: cover;">
                    @else
                        <div class="img-circle elevation-2 profile-img d-flex align-items-center justify-content-center {{ optional(auth()->user())->gender === 'Feminino' ? 'bg-pink' : 'bg-primary' }}" style="width: 39px; height: 39px;">
                            <i class="fas {{ optional(auth()->user())->gender === 'Feminino' ? 'fa-user' : 'fa-user-tie' }} text-white" style="font-size: 1.1rem;"></i>
                        </div>
                    @endif
                </div>
                <div class="info">
                    <span class="d-block text-bold text-dark-mode-fix">
                        {{ explode(' ', optional(auth()->user())->name ?? 'Usuário')[0] }}
                    </span>
                    <span class="small text-muted">{{ optional(auth()->user())->role ?? 'N/A' }}</span>
                    @php
                        $systemRoles = ['Administrador', 'Gerente', 'Suporte', 'Instalador'];
                        $userRole = auth()->user()->role ?? '';
                        $isSystemUser = in_array($userRole, $systemRoles);
                    @endphp
                    @if(!$isSystemUser && auth()->user()->customer_id)
                        <div class="small text-muted font-weight-bold text-uppercase" style="font-size: 0.65rem;">
                            <i class="fas fa-building mr-1"></i> {{ auth()->user()->customer->name ?? 'Minha Empresa' }}
                        </div>
                    @endif
                </div>
            </div>
            <nav class="mt-2 text-sm text-uppercase font-weight-bold">
                @php 
                    $userRole = strtolower(auth()->check() ? auth()->user()->role ?? '' : 'guest'); 
                    if ($userRole === 'gestor') $userRole = 'gerente';
                    if ($userRole === 'administrador') $userRole = 'admin';
                @endphp
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    
                    @if(in_array($userRole, ['admin', 'gerente', 'suporte']))
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
                    @endif

                    @if(in_array($userRole, ['admin', 'gerente', 'cliente', 'autorizado', 'suporte']))
                    <li class="nav-header">DEPARTAMENTO TÉCNICO</li>
                    @endif
                    
                    @if(in_array($userRole, ['admin', 'gerente', 'cliente', 'autorizado']))
                    <li class="nav-item">
                        <a href="/portal" class="nav-link {{ (request()->is('portal') || (request()->is('portal/*') && !request()->is('portal/verificacoes*') && !request()->is('portal/despesas*') && !request()->is('portal/instalador*'))) ? 'bg-indigo text-white shadow' : '' }}"><i class="nav-icon fas fa-user-shield"></i><p>PORTAL DO CLIENTE</p></a>
                    </li>
                    @endif

                    @if(in_array($userRole, ['admin', 'gerente', 'suporte', 'cliente', 'autorizado']))
                    <li class="nav-item">
                        <a href="/customer-sub-users" class="nav-link {{ request()->is('customer-sub-users*') ? 'active' : '' }}"><i class="nav-icon fas fa-users-cog"></i><p>Credenciais APPs</p></a>
                    </li>
                    @endif

                    @if(in_array($userRole, ['admin', 'gerente', 'suporte']))
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
                    @if(in_array($userRole, ['admin', 'gerente', 'motorista']))
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
                    @endif

                    <!-- 🔧 MÓDULO DO INSTALADOR (VISTORIAS TÉCNICAS) -->
                    @if(in_array($userRole, ['admin', 'gerente', 'suporte', 'instalador']))
                    <li class="nav-header">INSTALADOR</li>
                    <li class="nav-item">
                        <a href="{{ route('portal.instalador.index') }}" class="nav-link {{ request()->is('portal/instalador*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-tools text-primary"></i>
                            <p>Instalações</p>
                        </a>
                    </li>
                    @endif

                    @if(in_array($userRole, ['admin', 'gerente', 'suporte']))
                    <li class="nav-header">ADMINISTRAÇÃO</li>
                    @endif
                    
                    @if(in_array($userRole, ['admin', 'gerente']))
                    <li class="nav-item">
                        <a href="/users" class="nav-link {{ request()->is('users*') ? 'active' : '' }}"><i class="nav-icon fas fa-user-shield"></i><p>Usuários Internos</p></a>
                    </li>
                    @endif
                    
                    @if(in_array($userRole, ['admin', 'gerente', 'suporte']))
                    <li class="nav-item">
                        <a href="/reports" class="nav-link {{ request()->is('reports*') ? 'active' : '' }}"><i class="nav-icon fas fa-file-invoice"></i><p>Relatórios</p></a>
                    </li>
                    @endif
                    
                    @if($userRole === 'admin')
                    <li class="nav-item">
                        <a href="{{ route('settings.index') }}" class="nav-link {{ request()->is('settings*') ? 'active' : '' }}"><i class="nav-icon fas fa-cog"></i><p>Configurações</p></a>
                    </li>
                    @endif
                    
                    @if(in_array($userRole, ['admin', 'gerente', 'suporte']))
                    <li class="nav-item">
                        <a href="{{ route('help') }}" class="nav-link {{ request()->is('help*') ? 'active' : '' }} text-info">
                            <i class="nav-icon fas fa-question-circle shadow-sm"></i>
                            <p>Manual de Operações</p>
                        </a>
                    </li>
                    @endif
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
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
                <!-- 👁️ VISUALIZAÇÃO PADRÃO -->
                <div class="modal-body text-center p-4" id="perfil-view-mode">
                    @if(optional(auth()->user())->image)
                        <img src="{{ asset('storage/' . auth()->user()->image) }}" class="img-circle mb-3 border border-dark mx-auto" style="width: 90px; height: 90px; object-fit: cover;">
                    @else
                        <div class="img-circle mb-3 border border-dark mx-auto d-flex align-items-center justify-content-center {{ optional(auth()->user())->gender === 'Feminino' ? 'bg-pink' : 'bg-primary' }}" style="width: 90px; height: 90px;">
                            <i class="fas {{ optional(auth()->user())->gender === 'Feminino' ? 'fa-user' : 'fa-user-tie' }} text-white" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    <h5 class="text-bold text-dark">{{ optional(auth()->user())->name ?? 'Usuário' }}</h5>
                    <p class="text-muted small mb-0 font-weight-bold"><i class="fas fa-id-badge mr-1 text-primary"></i> {{ optional(auth()->user())->role ?? 'N/A' }}</p>
                    <hr>
                    <button class="btn btn-outline-primary btn-block mt-3" onclick="document.getElementById('perfil-view-mode').style.display='none'; document.getElementById('perfil-edit-mode').style.display='block';"><i class="fas fa-edit mr-1"></i> Editar Perfil</button>
                    <button class="btn btn-outline-danger btn-block mt-2" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fas fa-sign-out-alt mr-1"></i> Sair do Sistema</button>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
                
                <!-- ✏️ MODO DE EDIÇÃO -->
                <div class="modal-body p-4" id="perfil-edit-mode" style="display: none; background: #f8f9fa;">
                    <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2 border-light">
                        <h5 class="text-bold mb-0 text-dark" style="font-size: 1.1rem;"><i class="fas fa-user-edit text-primary mr-2"></i>Editar Meu Perfil</h5>
                        <button type="button" class="close text-danger" onclick="document.getElementById('perfil-edit-mode').style.display='none'; document.getElementById('perfil-view-mode').style.display='block';" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    
                    <form action="{{ route('portal.profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block" onclick="document.getElementById('profile_image_input').click();" style="cursor: pointer;" title="Clique para trocar a foto">
                                <img src="{{ optional(auth()->user())->image ? asset('storage/' . auth()->user()->image) : '' }}" class="img-circle border border-primary shadow-sm {{ optional(auth()->user())->image ? '' : 'd-none' }}" style="width: 90px; height: 90px; object-fit: cover; transition: 0.3s;" id="profile_image_preview">
                                <div id="profile_image_placeholder" class="img-circle border border-primary shadow-sm align-items-center justify-content-center {{ optional(auth()->user())->image ? 'd-none' : 'd-flex' }} {{ optional(auth()->user())->gender === 'Feminino' ? 'bg-pink' : 'bg-primary' }}" style="width: 90px; height: 90px;">
                                    <i class="fas {{ optional(auth()->user())->gender === 'Feminino' ? 'fa-user' : 'fa-user-tie' }} text-white" style="font-size: 3rem;"></i>
                                </div>
                                <span class="badge badge-primary position-absolute" style="bottom: 0; right: 0; border-radius: 50%; padding: 6px;"><i class="fas fa-camera"></i></span>
                            </div>
                            <input type="file" name="image" id="profile_image_input" class="d-none" accept="image/*" onchange="document.getElementById('profile_image_preview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('profile_image_preview').classList.remove('d-none'); document.getElementById('profile_image_placeholder').classList.remove('d-flex'); document.getElementById('profile_image_placeholder').classList.add('d-none');">
                            <p class="text-muted small mt-2 mb-0 font-italic">Formatos suportados: JPG, PNG (Max 5MB)</p>
                        </div>

                        <div class="form-group mb-3 text-left">
                            <label class="small font-weight-bold text-muted text-uppercase mb-1">Nome Completo</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text bg-white border-right-0"><i class="fas fa-user text-primary"></i></span></div>
                                <input type="text" name="name" class="form-control border-left-0 pl-0" value="{{ optional(auth()->user())->name }}" required>
                            </div>
                        </div>

                        <div class="form-group mb-3 text-left">
                            <label class="small font-weight-bold text-muted text-uppercase mb-1">E-mail de Acesso</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text bg-white border-right-0"><i class="fas fa-envelope text-primary"></i></span></div>
                                <input type="email" name="email" class="form-control border-left-0 pl-0" value="{{ optional(auth()->user())->email }}" required>
                            </div>
                        </div>

                        <div class="form-group mb-4 text-left">
                            <label class="small font-weight-bold text-muted text-uppercase mb-1">Resetar Senha <span class="text-xs text-info font-weight-normal">(Deixe em branco para manter)</span></label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text bg-white border-right-0"><i class="fas fa-lock text-primary"></i></span></div>
                                <input type="password" name="password" id="profile_password" class="form-control border-left-0 border-right-0 pl-0" placeholder="••••••••" minlength="8">
                                <div class="input-group-append">
                                    <span class="input-group-text bg-white border-left-0" style="cursor: pointer;" onclick="const pInput = document.getElementById('profile_password'); const pIcon = this.querySelector('i'); if (pInput.type === 'password') { pInput.type = 'text'; pIcon.className = 'fas fa-eye-slash text-primary'; } else { pInput.type = 'password'; pIcon.className = 'fas fa-eye text-muted'; }">
                                        <i class="fas fa-eye text-muted"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-light border btn-sm flex-fill mr-2" onclick="document.getElementById('perfil-edit-mode').style.display='none'; document.getElementById('perfil-view-mode').style.display='block';">Cancelar</button>
                            <button type="submit" class="btn btn-primary btn-sm flex-fill"><i class="fas fa-save mr-1"></i> Salvar Perfil</button>
                        </div>
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
