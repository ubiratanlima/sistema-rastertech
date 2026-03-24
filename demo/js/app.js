// ========================================
// APLICAÇÃO PRINCIPAL - SISTEMA RASTERTECH
// ========================================

class RastertechApp {
    constructor() {
        this.currentPage = 'dashboard';
        this.isOfflineMode = false;
        this.inventory = [...mockData.assets];
        this.currentUser = null; // Usuário logado atualmente
        this.authModal = null; // Modal de autenticação

        this.initializeApp();
    }

    async initializeApp() {
        console.log('🚀 Inicializando aplicação Rastertech...');
        this.initializeUI();
        console.log('✅ UI inicializada');
        this.setupEventListeners();
        console.log('✅ Event listeners configurados');

        try {
            console.log('🗄️ Inicializando banco de dados...');
            await this.initDatabase();
            console.log('✅ Banco de dados inicializado');

            console.log('🔍 Verificando autenticação...');
            this.checkAuthentication();
            console.log('✅ Aplicação inicializada com sucesso');
        } catch (error) {
            console.error('❌ Erro na inicialização:', error);
            this.snackbar.error('Erro ao inicializar aplicação');
        }
    }

    // ========================================
    // AUTENTICAÇÃO E AUTORIZAÇÃO (RBAC)
    // ========================================

    async initDatabase() {
        try {
            console.log('🗄️ Inicializando sistema de dados...');

            // Por enquanto, vamos usar uma versão simplificada sem SQLite
            // para testar se a autenticação funciona
            this.mockDatabaseInit();

            console.log('✅ Sistema de dados inicializado');
        } catch (error) {
            console.error('Erro ao inicializar sistema de dados:', error);
            this.snackbar.error('Erro ao inicializar sistema de dados');
        }
        }
    }

    mockDatabaseInit() {
        // Dados mockados em memória para teste
        this.users = [
            {
                id: 'USR-001',
                email: 'joao@instalador.com',
                password_hash: this.hashPassword('joao123'),
                role: 'technician',
                name: 'João Silva',
                status: 'active'
            },
            {
                id: 'USR-002',
                email: 'carlos@operador.com',
                password_hash: this.hashPassword('carlos123'),
                role: 'operator',
                name: 'Carlos Santos',
                status: 'active'
            },
            {
                id: 'USR-003',
                email: 'pedro@motorista.com',
                password_hash: this.hashPassword('pedro123'),
                role: 'driver',
                name: 'Pedro Oliveira',
                status: 'active'
            },
            {
                id: 'USR-004',
                email: 'camila@cadastradora.com',
                password_hash: this.hashPassword('camila123'),
                role: 'admin',
                name: 'Camila Costa',
                status: 'active'
            }
        ];

        console.log('👥 Usuários mockados carregados:', this.users.length);
        console.log('📋 Credenciais de teste:');
        console.log('  - Técnico: joao@instalador.com / joao123');
        console.log('  - Operador: carlos@operador.com / carlos123');
        console.log('  - Motorista: pedro@motorista.com / pedro123');
        console.log('  - Admin: camila@cadastradora.com / camila123');
    }

    hashPassword(password) {
        // Hash simples mas consistente para demo
        let hash = 5381;
        for (let i = 0; i < password.length; i++) {
            const char = password.charCodeAt(i);
            hash = ((hash << 5) + hash) + char;
        }
        const result = Math.abs(hash).toString();
        console.log(`🔐 Hashing "${password}" -> "${result}"`);
        return result;
    }

    authenticateUser(email, password) {
        console.log('🔐 Tentando autenticar:', email);
        console.log('👥 Usuários disponíveis:', this.users.map(u => ({ email: u.email, hash: u.password_hash })));

        const hashedPassword = this.hashPassword(password);
        console.log('🔑 Hash calculado:', hashedPassword);

        const user = this.users.find(u =>
            u.email === email &&
            u.password_hash === hashedPassword &&
            u.status === 'active'
        );

        if (user) {
            console.log('✅ Usuário autenticado:', user.name);
            return user;
        }

        // Debug: mostrar usuário encontrado e comparação
        const foundUser = this.users.find(u => u.email === email);
        if (foundUser) {
            console.log('🔍 Usuário encontrado:', foundUser);
            console.log('🔍 Hash esperado:', foundUser.password_hash);
            console.log('🔍 Hash fornecido:', hashedPassword);
            console.log('🔍 Hashes iguais?', foundUser.password_hash === hashedPassword);
        } else {
            console.log('❌ Usuário não encontrado com email:', email);
        }

        console.log('❌ Credenciais inválidas');
        return null;
    }

    async checkAuthentication() {
        console.log('🔍 Verificando autenticação...');
        try {
            // Verificar se há usuário salvo no localStorage
            const savedUser = localStorage.getItem('currentUser');
            console.log('💾 Usuário salvo no localStorage:', savedUser ? 'sim' : 'não');
            if (savedUser) {
                this.currentUser = JSON.parse(savedUser);
                console.log('👤 Usuário logado automaticamente:', this.currentUser.name);
                this.loadInitialData();
                return;
            }

            // Se não há usuário logado, mostrar modal de autenticação
            console.log('🔐 Mostrando modal de autenticação');
            this.showAuthModal();
        } catch (error) {
            console.error('❌ Erro ao verificar autenticação:', error);
            this.showAuthModal();
        }
    }

    showAuthModal() {
        console.log('🎭 Criando modal de autenticação...');
        if (!this.authModal) {
            console.log('🔧 Instanciando AuthModal...');
            this.authModal = new AuthModal();
            console.log('✅ AuthModal criado');
        }

        console.log('📤 Chamando authModal.show com callback...');
        this.authModal.show(async (credentials) => {
            console.log('🔑 Credenciais recebidas:', credentials);
            try {
                const user = this.authenticateUser(credentials.username, credentials.password);

                if (user) {
                    this.currentUser = user;
                    localStorage.setItem('currentUser', JSON.stringify(user));
                    this.authModal.hide();
                    this.snackbar.success(`Bem-vindo, ${user.name}!`);
                    console.log('🔐 Login realizado com sucesso:', user.name, `(${user.role})`);
                    this.loadInitialData();
                } else {
                    this.snackbar.error('Usuário ou senha incorretos');
                }
            } catch (error) {
                console.error('💥 Erro durante autenticação:', error);
                this.snackbar.error('Erro durante autenticação');
            }
        });
    }

    logout() {
        console.log('🚪 Realizando logout...');
        this.currentUser = null;
        localStorage.removeItem('currentUser');
        this.snackbar.info('Logout realizado com sucesso');
        console.log('🚪 Logout realizado');
        this.showAuthModal();
    }

    // Verificar permissões baseado no papel do usuário
    hasPermission(permission) {
        console.log('🔐 Verificando permissão:', permission, 'para usuário:', this.currentUser?.name);
        if (!this.currentUser) {
            console.log('❌ Nenhum usuário logado');
            return false;
        }

        const rolePermissions = {
            'technician': ['read_assets', 'update_assets', 'create_service_orders', 'read_service_orders'],
            'operator': ['read_assets', 'create_service_orders', 'read_service_orders'],
            'driver': ['read_assets', 'read_service_orders'],
            'admin': ['read_assets', 'update_assets', 'create_service_orders', 'read_service_orders', 'manage_users', 'system_config']
        };

        const hasPerm = rolePermissions[this.currentUser.role]?.includes(permission) || false;
        console.log('🔐 Permissão', permission, 'para role', this.currentUser.role, ':', hasPerm);
        return hasPerm;
    }

    // ========================================
    // INICIALIZAÇÃO DA UI
    // ========================================

    initializeUI() {
        console.log('🎨 Inicializando componentes da UI...');
        // Componentes globais
        try {
            this.snackbar = new Snackbar();
            console.log('✅ Snackbar criado');
            this.syncIndicator = new SyncStatusIndicator();
            console.log('✅ SyncIndicator criado');
            this.modal = new Modal();
            console.log('✅ Modal criado');

            const osForm = document.getElementById('osForm');
            if (osForm) {
                this.osFormManager = new OfflineFormManager('osForm');
                console.log('✅ OfflineFormManager criado');
            } else {
                console.warn('Warning: osForm element not found. Offline form manager disabled.');
                this.osFormManager = null;
            }
        } catch (error) {
            console.error('UI initialization error:', error);
            // Create minimal snackbar for error notification
            if (this.snackbar) {
                this.snackbar.error('Erro ao inicializar interface');
            }
        }
    }

    setupEventListeners() {
        console.log('🔧 Configurando event listeners da aplicação...');
        // Navegação
        document.querySelectorAll('.nav-item').forEach(btn => {
            btn.addEventListener('click', (e) => this.navigateTo(e.target.closest('.nav-item').dataset.page));
        });
        console.log('✅ Event listeners de navegação configurados');

        // Busca
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => this.searchAssets(e.target.value));
            console.log('✅ Event listener de busca configurado');
        }

        // Filtros
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => this.filterAssets(e.target.value));
            console.log('✅ Event listener de filtro configurado');
        }

        // Novo Ativo
        const newAssetBtn = document.getElementById('newAssetBtn');
        if (newAssetBtn) {
            newAssetBtn.addEventListener('click', () => this.showNewAssetForm());
            console.log('✅ Event listener de novo ativo configurado');
        }

        // Formulário O.S.
        const osForm = document.getElementById('osForm');
        if (osForm) {
            osForm.addEventListener('submit', (e) => this.submitServiceOrder(e));
            console.log('✅ Event listener de formulário O.S. configurado');
        }

        // Modo Offline
        const offlineToggle = document.getElementById('offlineToggle');
        if (offlineToggle) {
            offlineToggle.addEventListener('change', (e) => this.toggleOfflineMode(e.target.checked));
            console.log('✅ Event listener de modo offline configurado');
        }

        // Limpar Dados
        const resetBtn = document.getElementById('resetDataBtn');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => this.resetData());
            console.log('✅ Event listener de reset configurado');
        }
    }

    loadInitialData() {
        console.log('📊 Carregando dados iniciais...');
        this.updateUserInterface();
        this.updateDashboard();
        this.updateLastSync();
        console.log('✅ Dados iniciais carregados');
    }

    updateUserInterface() {
        console.log('👤 Atualizando interface do usuário...');
        // Atualizar header com informações do usuário
        const userInfo = document.getElementById('userInfo');
        console.log('🔍 Elemento userInfo encontrado:', !!userInfo);
        if (userInfo && this.currentUser) {
            userInfo.innerHTML = `
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <div style="text-align: right;">
                        <div style="font-weight: 600; font-size: 0.875rem;">${this.currentUser.name}</div>
                        <div style="font-size: 0.75rem; color: #666; text-transform: capitalize;">${this.currentUser.role}</div>
                    </div>
                    <button id="logoutBtn" class="btn btn-outline" style="font-size: 0.75rem; padding: 0.25rem 0.75rem;">
                        Sair
                    </button>
                </div>
            `;

            // Adicionar event listener para logout
            document.getElementById('logoutBtn').addEventListener('click', () => this.logout());
            console.log('✅ Interface do usuário atualizada');
        } else {
            console.log('⚠️ Usuário não logado ou elemento userInfo não encontrado');
        }

        // Configurar navegação baseada em permissões
        this.configureNavigation();
    }

    configureNavigation() {
        console.log('🧭 Configurando navegação baseada em permissões...');
        const navItems = document.querySelectorAll('.nav-item');
        console.log('🔍 Itens de navegação encontrados:', navItems.length);

        navItems.forEach(item => {
            const page = item.dataset.page;
            console.log('📄 Verificando página:', page);
            let hasAccess = true;

            // Definir permissões por página
            const pagePermissions = {
                'dashboard': ['read_assets'], // Todos têm acesso ao dashboard
                'assets': ['read_assets'],
                'service-order': ['create_service_orders'],
                'settings': ['system_config'] // Apenas admin
            };

            if (pagePermissions[page]) {
                hasAccess = pagePermissions[page].some(permission => this.hasPermission(permission));
                console.log('🔐 Página', page, 'acesso:', hasAccess);
            }

            if (!hasAccess) {
                item.style.display = 'none';
                console.log('🚫 Página', page, 'ocultada');
            } else {
                item.style.display = 'flex';
                console.log('✅ Página', page, 'mostrada');
            }
        });
    }

    // Navegação
    navigateTo(page) {
        console.log('🧭 Navegando para página:', page);
        // Remover ativo anterior
        document.querySelector('.page.active')?.classList.remove('active');
        document.querySelector('.nav-item.active')?.classList.remove('active');

        // Ativar nova página
        const pageElement = document.getElementById(`${page}Page`);
        const navButton = document.querySelector(`.nav-item[data-page="${page}"]`);

        if (pageElement) {
            pageElement.classList.add('active');
            console.log('✅ Página ativada:', page);
        } else {
            console.log('❌ Página não encontrada:', page);
        }
        if (navButton) {
            navButton.classList.add('active');
            console.log('✅ Botão de navegação ativado:', page);
        }

        this.currentPage = page;

        // Carregar dados específicos da página
        if (page === 'dashboard') this.updateDashboard();
        if (page === 'assets') this.displayAssets();
        if (page === 'settings') this.updateSettings();
    }

    // Dashboard
    updateDashboard() {
        console.log('📊 Atualizando dashboard...');
        const stats = getStatusStats();
        console.log('📈 Estatísticas calculadas:', stats);
        document.getElementById('totalAssets').textContent = stats.total;
        document.getElementById('installedAssets').textContent = stats.installed;
        document.getElementById('availableAssets').textContent = stats.available;
        document.getElementById('maintenanceAssets').textContent = stats.maintenance;

        this.displayAssetsList();
        console.log('✅ Dashboard atualizado');
    }

    displayAssetsList() {
        console.log('📋 Exibindo lista de ativos...');
        const assetsList = document.getElementById('assetsList');
        if (!assetsList) {
            console.log('❌ Elemento assetsList não encontrado');
            return;
        }

        assetsList.innerHTML = '';

        const installedAssets = this.inventory
            .filter(a => a.status === 'Instalado')
            .slice(0, 5);

        console.log('🔍 Ativos instalados encontrados:', installedAssets.length);

        if (installedAssets.length === 0) {
            assetsList.innerHTML = '<div style="padding: 2rem; text-align: center; color: #666;">Nenhum ativo instalado</div>';
            console.log('ℹ️ Nenhum ativo instalado para exibir');
            return;
        }

        installedAssets.forEach(asset => {
            const card = new AssetCard(asset);
            const element = card.render();
            element.addEventListener('click', () => this.showAssetDetails(asset));
            assetsList.appendChild(element);
        });

        console.log('✅ Lista de ativos exibida');
    }

    // Gestão de Ativos
    displayAssets() {
        console.log('📦 Exibindo ativos...');
        const grid = document.getElementById('assetsGrid');
        if (!grid) {
            console.log('❌ Grid de ativos não encontrado');
            return;
        }

        grid.innerHTML = '';

        console.log('🔍 Total de ativos:', this.inventory.length);
        this.inventory.forEach(asset => {
            const card = new AssetCard(asset);
            const element = card.render();
            element.addEventListener('click', () => this.showAssetDetails(asset));
            grid.appendChild(element);
        });

        console.log('✅ Ativos exibidos');
    }

    filterAssets(status) {
        console.log('🔍 Filtrando ativos por status:', status);
        const grid = document.getElementById('assetsGrid');
        if (!grid) {
            console.log('❌ Grid de ativos não encontrado');
            return;
        }

        let filtered = this.inventory;
        if (status) {
            filtered = this.inventory.filter(a => a.status === status);
            console.log('📊 Ativos filtrados:', filtered.length);
        } else {
            console.log('📊 Todos os ativos:', filtered.length);
        }

        grid.innerHTML = '';

        if (filtered.length === 0) {
            grid.innerHTML = '<div style="grid-column: 1/-1; padding: 2rem; text-align: center; color: #666;">Nenhum ativo encontrado</div>';
            console.log('ℹ️ Nenhum ativo encontrado');
            return;
        }

        filtered.forEach(asset => {
            const card = new AssetCard(asset);
            const element = card.render();
            element.addEventListener('click', () => this.showAssetDetails(asset));
            grid.appendChild(element);
        });

        console.log('✅ Ativos filtrados exibidos');
    }

    searchAssets(query) {
        console.log('🔎 Pesquisando ativos:', query);
        const grid = document.getElementById('assetsGrid');
        if (!grid) {
            console.log('❌ Grid de ativos não encontrado');
            return;
        }

        const lowerQuery = query.toLowerCase();
        const filtered = this.inventory.filter(asset =>
            asset.plate.toLowerCase().includes(lowerQuery) ||
            asset.trackerIMEI.includes(query) ||
            asset.chipNumber.includes(query) ||
            asset.owner?.toLowerCase().includes(lowerQuery)
        );

        console.log('📊 Resultados da pesquisa:', filtered.length);

        grid.innerHTML = '';

        if (filtered.length === 0) {
            grid.innerHTML = '<div style="grid-column: 1/-1; padding: 2rem; text-align: center; color: #666;">Nenhum resultado encontrado</div>';
            console.log('ℹ️ Nenhum resultado encontrado');
            return;
        }

        filtered.forEach(asset => {
            const card = new AssetCard(asset);
            const element = card.render();
            element.addEventListener('click', () => this.showAssetDetails(asset));
            grid.appendChild(element);
        });

        console.log('✅ Resultados da pesquisa exibidos');
    }

    showAssetDetails(asset) {
        console.log('📄 Mostrando detalhes do ativo:', asset.plate);
        const html = `
            <h2 style="margin-bottom: 1rem;">${asset.plate}</h2>
            <div style="margin-bottom: 1.5rem;">
                <h3 style="font-size: 0.875rem; color: #666; margin-bottom: 0.5rem; text-transform: uppercase;">Informações</h3>
                <div style="background-color: #f5f5f5; padding: 1rem; border-radius: 0.5rem;">
                    <p><strong>Status:</strong> ${asset.status}</p>
                    <p><strong>Tracker IMEI:</strong> ${asset.trackerIMEI}</p>
                    <p><strong>Chip GSM:</strong> ${asset.chipNumber}</p>
                    <p><strong>Cliente:</strong> ${asset.customer}</p>
                    <p><strong>Proprietário:</strong> ${asset.owner || '-'}</p>
                    <p><strong>Data de Instalação:</strong> ${asset.installDate ? new Date(asset.installDate).toLocaleDateString('pt-BR') : '-'}</p>
                    ${asset.lastSync ? `<p><strong>Última Sincronização:</strong> ${new Date(asset.lastSync).toLocaleString('pt-BR')}</p>` : ''}
                </div>
            </div>
            <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                <button class="btn btn-primary" onclick="app.editAsset('${asset.id}')">Editar</button>
                <button class="btn btn-secondary" onclick="app.modal.close()">Fechar</button>
            </div>
        `;

        this.modal.open(html);
        console.log('✅ Detalhes do ativo mostrados');
    }

    editAsset(assetId) {
        console.log('✏️ Editando ativo:', assetId);
        this.snackbar.info('Funcionalidade de edição em desenvolvimento');
    }

    showNewAssetForm() {
        console.log('➕ Mostrando formulário de novo ativo...');
        const html = `
            <h2 style="margin-bottom: 1rem;">Novo Ativo</h2>
            <form style="display: flex; flex-direction: column; gap: 1rem;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Placa do Veículo</label>
                    <input type="text" placeholder="ABC-1234" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Tracker IMEI</label>
                    <input type="text" placeholder="352045089804842" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 0.375rem;">
                </div>
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 0.5rem;">Chip GSM</label>
                    <input type="text" placeholder="1234567890123456" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 0.375rem;">
                </div>
                <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                    <button type="button" class="btn btn-primary" onclick="app.snackbar.success('Ativo criado com sucesso!'); app.modal.close();">Criar</button>
                    <button type="button" class="btn btn-secondary" onclick="app.modal.close();">Cancelar</button>
                </div>
            </form>
        `;

        this.modal.open(html);
        console.log('✅ Formulário de novo ativo mostrado');
    }

    // Ordem de Serviço
    submitServiceOrder(event) {
        console.log('📝 Enviando ordem de serviço...');
        event.preventDefault();

        // Guard: check if osFormManager was initialized
        if (!this.osFormManager) {
            this.snackbar.error('Formulário de O.S. não está disponível');
            console.log('❌ Formulário O.S. não disponível');
            return;
        }

        const result = this.osFormManager.submit();
        console.log('📋 Resultado da validação:', result);

        if (!result.success) {
            this.snackbar.error('Preencha todos os campos obrigatórios');
            console.log('❌ Campos obrigatórios não preenchidos');
            return;
        }

        // Simular sync
        this.syncIndicator.setSyncing();
        console.log('🔄 Iniciando sincronização...');

        RequestSimulator.submitForm(result.submission)
            .then(() => {
                this.syncIndicator.setSynced();
                this.snackbar.success('❎ O.S. enviada com sucesso!');
                console.log('✅ O.S. enviada com sucesso');
                setTimeout(() => {
                    this.navigateTo('dashboard');
                }, 1500);
            })
            .catch(() => {
                this.syncIndicator.setError();
                this.snackbar.error('Erro ao enviar O.S. Dados salvos offline.');
                console.log('❌ Erro ao enviar O.S.');
            });
    }

    // Modo Offline
    toggleOfflineMode(isOffline) {
        console.log('🔄 Alternando modo offline:', isOffline);
        this.isOfflineMode = isOffline;
        this.osFormManager.setOfflineMode(isOffline);

        if (isOffline) {
            this.syncIndicator.setOffline();
            this.snackbar.info('Modo offline ativado. Dados serão salvos localmente.');
            console.log('📴 Modo offline ativado');
        } else {
            this.syncIndicator.setSynced();
            this.snackbar.success('Modo online ativado.');
            console.log('📶 Modo online ativado');
        }

        this.updateSettings();
    }

    // Configurações
    updateSettings() {
        console.log('⚙️ Atualizando configurações...');
        const currentMode = document.getElementById('currentMode');
        const lastSync = document.getElementById('lastSync');
        const offlineToggle = document.getElementById('offlineToggle');

        if (currentMode) {
            currentMode.textContent = this.isOfflineMode ? 'Offline' : 'Online';
            console.log('📡 Modo atualizado:', currentMode.textContent);
        }

        if (lastSync && this.syncIndicator) {
            lastSync.textContent = this.syncIndicator.getLastSyncTime();
            console.log('⏰ Última sincronização atualizada:', lastSync.textContent);
        }

        if (offlineToggle) {
            offlineToggle.checked = this.isOfflineMode;
            console.log('🔄 Toggle offline atualizado:', this.isOfflineMode);
        }
    }

    updateLastSync() {
        console.log('🔄 Iniciando atualização de sincronização...');
        setInterval(() => {
            if (this.currentPage === 'settings') {
                this.updateSettings();
            }
        }, 60000); // Atualizar a cada minuto
        console.log('✅ Atualização de sincronização configurada');
    }

    // Dados
    resetData() {
        console.log('🗑️ Resetando dados...');
        if (confirm('Tem certeza que deseja limpar todos os dados da demo?')) {
            clearLocalStorage();
            this.osFormManager.clearForm();
            this.snackbar.success('Dados da demo foram limpos');
            console.log('✅ Dados resetados');
        } else {
            console.log('❌ Reset cancelado pelo usuário');
        }
    }
}

// Inicializar aplicação quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.app = new RastertechApp();
    console.log('🚀 Sistema Rastertech iniciado com sucesso!');
});
