// ========================================
// COMPONENTES CUSTOMIZADOS
// ========================================

// AssetCard - Cartão de Ativo
class AssetCard {
    constructor(asset) {
        this.asset = asset;
    }

    render() {
        const statusClasses = {
            'Disponível': 'status-badge-available',
            'Instalado': 'status-badge-installed',
            'Manutenção': 'status-badge-maintenance',
            'Em Retirada': 'status-badge-withdrawal'
        };

        const statusClass = statusClasses[this.asset.status] || 'status-badge-available';
        
        const statusDot = `<span class="status-dot" style="background-color: ${this.getStatusColor()}"></span>`;

        const html = `
            <div class="asset-card" data-asset-id="${this.asset.id}">
                <div class="asset-card-header">
                    <div class="asset-icon">
                        ${this.getAssetIcon()}
                    </div>
                    <div class="asset-info">
                        <div class="asset-title">${this.asset.plate}</div>
                        <div class="asset-details">
                            <span>Tracker: ${this.asset.trackerIMEI.slice(-4)}</span>
                            <span>Chip: ${this.asset.chipNumber.slice(-4)}</span>
                        </div>
                    </div>
                </div>
                <div class="asset-status ${statusClass}">
                    ${statusDot}
                    ${this.asset.status}
                </div>
            </div>
        `;

        const element = document.createElement('div');
        element.innerHTML = html;
        return element.firstChild;
    }

    getAssetIcon() {
        const icons = {
            'vehicle': '🚚',
            'tracker': '📍',
            'chip': '📱'
        };
        return icons[this.asset.type] || '🚗';
    }

    getStatusColor() {
        const colors = {
            'Disponível': '#4CAF50',
            'Instalado': '#2196F3',
            'Manutenção': '#FF9800',
            'Em Retirada': '#F44336'
        };
        return colors[this.asset.status] || '#4CAF50';
    }
}

// SyncStatusIndicator - Indicador de Sincronização
class SyncStatusIndicator {
    constructor(containerId = 'syncStatus') {
        this.container = document.getElementById(containerId);
        this.status = 'synced'; // synced, syncing, offline, error
        this.lastSync = new Date();
    }

    setSynced() {
        this.status = 'synced';
        this.lastSync = new Date();
        this.update();
    }

    setSyncing() {
        this.status = 'syncing';
        this.update();
    }

    setOffline() {
        this.status = 'offline';
        this.update();
    }

    setError() {
        this.status = 'error';
        this.update();
    }

    update() {
        if (!this.container) return;

        const statusClasses = {
            'synced': 'sync-synced',
            'syncing': 'sync-syncing',
            'offline': 'sync-offline',
            'error': 'sync-error'
        };

        const statusTexts = {
            'synced': 'Sincronizado',
            'syncing': 'Sincronizando...',
            'offline': 'Modo Offline',
            'error': 'Erro de Sincronização'
        };

        this.container.className = `sync-status ${statusClasses[this.status]}`;
        
        const textEl = this.container.querySelector('.sync-text');
        if (textEl) {
            textEl.textContent = statusTexts[this.status];
        }
    }

    getLastSyncTime() {
        return this.lastSync.toLocaleTimeString('pt-BR');
    }
}

// OfflineFormManager - Gerenciador de Formulários Offline
class OfflineFormManager {
    constructor(formId) {
        this.form = document.getElementById(formId);
        this.formId = formId;
        this.data = {};
        this.isDirty = false;
        this.isOffline = false;

        if (this.form) {
            this.form.addEventListener('input', () => this.markDirty());
            this.form.addEventListener('change', () => this.markDirty());
        }

        this.loadFromStorage();
    }

    markDirty() {
        this.isDirty = true;
        this.autoSave();
    }

    autoSave() {
        if (!this.isDirty) return;

        const formData = new FormData(this.form);
        this.data = Object.fromEntries(formData);
        
        saveToLocalStorage(`form-${this.formId}`, {
            data: this.data,
            timestamp: new Date().toISOString(),
            isDirty: true
        });
    }

    loadFromStorage() {
        const saved = getFromLocalStorage(`form-${this.formId}`);
        if (saved) {
            this.data = saved.data;
            this.restoreFormData();
            this.isDirty = saved.isDirty;
        }
    }

    restoreFormData() {
        if (!this.form) return;

        Object.keys(this.data).forEach(key => {
            const element = this.form.elements[key];
            if (element) {
                if (element.type === 'checkbox') {
                    element.checked = this.data[key] === 'on' || this.data[key] === 'true';
                } else if (element.type === 'radio') {
                    const radio = this.form.querySelector(`input[name="${key}"][value="${this.data[key]}"]`);
                    if (radio) radio.checked = true;
                } else {
                    element.value = this.data[key];
                }
            }
        });
    }

    validate() {
        if (!this.form) return { valid: false, errors: [] };

        const errors = [];
        const required = this.form.querySelectorAll('[required]');

        // Clear all error borders first
        required.forEach(input => {
            input.style.borderColor = '';
        });

        // Then validate and mark errors
        required.forEach(input => {
            if (!input.value.trim()) {
                errors.push(`${input.name} é obrigatório`);
                input.style.borderColor = '#D32F2F';
            }
        });

        return {
            valid: errors.length === 0,
            errors: errors
        };
    }

    getFormData() {
        if (!this.form) return {};
        
        const formData = new FormData(this.form);
        const data = Object.fromEntries(formData);
        
        // Processar checkboxes
        const checkboxes = this.form.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(cb => {
            data[cb.name] = cb.checked;
        });

        return data;
    }

    submit() {
        const validation = this.validate();
        if (!validation.valid) {
            return {
                success: false,
                errors: validation.errors
            };
        }

        const data = this.getFormData();
        const submission = {
            data: data,
            timestamp: new Date().toISOString(),
            hash: this.generateHash(data),
            isOffline: this.isOffline
        };

        saveToLocalStorage(`submission-${this.formId}-${Date.now()}`, submission);
        this.clearForm();

        return {
            success: true,
            submission: submission
        };
    }

    clearForm() {
        if (!this.form) return;
        
        this.form.reset();
        this.isDirty = false;
        this.data = {};
        localStorage.removeItem(`form-${this.formId}`);
    }

    generateHash(data) {
        // Simulação simples de hash (em produção, usar função criptográfica real)
        const str = JSON.stringify(data);
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            const char = str.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash; // Convert to 32bit integer
        }
        return Math.abs(hash).toString(16);
    }

    setOfflineMode(isOffline) {
        this.isOffline = isOffline;
    }
}

// Snackbar - Notificações
class Snackbar {
    constructor(containerId = 'snackbar') {
        this.container = document.getElementById(containerId);
        this.timeout = null;
    }

    show(message, type = 'default', duration = 3000) {
        if (!this.container) return;

        this.container.textContent = message;
        this.container.className = `snackbar show ${type}`;

        if (this.timeout) clearTimeout(this.timeout);

        this.timeout = setTimeout(() => {
            this.container.classList.remove('show');
        }, duration);
    }

    success(message, duration = 3000) {
        this.show(message, 'success', duration);
    }

    error(message, duration = 4000) {
        this.show(message, 'error', duration);
    }

    info(message, duration = 3000) {
        this.show(message, 'default', duration);
    }
}

// Modal Helper
class Modal {
    constructor(modalId = 'assetModal') {
        this.modal = document.getElementById(modalId);
        this.modalBody = document.getElementById('modalBody');
        this.closeBtn = this.modal?.querySelector('.modal-close');

        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.close());
        }

        if (this.modal) {
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) this.close();
            });
        }
    }

    open(content) {
        if (!this.modal) return;
        
        if (typeof content === 'string') {
            this.modalBody.innerHTML = content;
        } else {
            this.modalBody.innerHTML = '';
            this.modalBody.appendChild(content);
        }

        this.modal.classList.add('show');
        document.body.style.overflow = 'hidden';
    }

    close() {
        if (!this.modal) return;
        
        this.modal.classList.remove('show');
        document.body.style.overflow = '';
    }
}

// Request Simulator (para simular requests de rede)
class RequestSimulator {
    static async simulateRequest(delay = 1000, shouldFail = false) {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                if (shouldFail) {
                    reject(new Error('Simulação de erro de rede'));
                } else {
                    resolve({ success: true, timestamp: new Date().toISOString() });
                }
            }, delay);
        });
    }

    static async submitForm(formData, delay = 1500) {
        return this.simulateRequest(delay, false);
    }

    static async fetchAssets(delay = 1000) {
        return this.simulateRequest(delay, false);
    }
}

// ========================================
// COMPONENTES DE AUTENTICAÇÃO E CADASTRO
// ========================================

// AuthModal - Modal de Autenticação
class AuthModal {
    constructor() {
        this.modal = null;
        this.selectedRole = null;
        this.onLoginCallback = null;
        this.createModal();
    }

    createModal() {
        console.log('🏗️ Criando modal de autenticação...');
        const modalHtml = `
            <div id="authModal" class="modal auth-modal">
                <div class="modal-content auth-modal-content">
                    <div class="modal-header">
                        <h2>Acesso ao Sistema RasterTech</h2>
                        <span class="modal-close">&times;</span>
                    </div>
                    <div class="modal-body">
                        <div class="user-selection">
                            <h3>Selecione seu perfil de usuário:</h3>
                            <div class="user-cards">
                                <div class="user-card" data-role="technician">
                                    <div class="user-avatar">👷</div>
                                    <div class="user-info">
                                        <h4>João Silva</h4>
                                        <p>Instalador de Campo</p>
                                        <small>Técnico terceirizado</small>
                                    </div>
                                </div>
                                <div class="user-card" data-role="operator">
                                    <div class="user-avatar">👨‍💼</div>
                                    <div class="user-info">
                                        <h4>Carlos Santos</h4>
                                        <p>Operador de Matriz</p>
                                        <small>Controle de ativos</small>
                                    </div>
                                </div>
                                <div class="user-card" data-role="driver">
                                    <div class="user-avatar">🚚</div>
                                    <div class="user-info">
                                        <h4>Pedro Oliveira</h4>
                                        <p>Motorista de Frota</p>
                                        <small>Checklists diários</small>
                                    </div>
                                </div>
                                <div class="user-card" data-role="admin">
                                    <div class="user-avatar">👩‍💻</div>
                                    <div class="user-info">
                                        <h4>Camila Costa</h4>
                                        <p>Cadastradora</p>
                                        <small>Gestão de cadastros</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form id="loginForm" class="login-form hidden">
                            <div class="form-group">
                                <label for="loginEmail">Email:</label>
                                <input type="email" id="loginEmail" required>
                            </div>
                            <div class="form-group">
                                <label for="loginPassword">Senha:</label>
                                <input type="password" id="loginPassword" required>
                            </div>
                            <div class="form-actions">
                                <button type="button" class="btn-secondary" id="backToSelection">Voltar</button>
                                <button type="submit" class="btn-primary">Entrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);
        this.modal = document.getElementById('authModal');
        console.log('✅ Modal criado no DOM:', !!this.modal);
        this.setupEventListeners();
        console.log('✅ Event listeners configurados');
    }

    setupEventListeners() {
        console.log('🔧 Configurando event listeners do modal...');

        // Fechar modal
        const closeBtn = this.modal.querySelector('.modal-close');
        console.log('🔍 Botão fechar encontrado:', !!closeBtn);
        closeBtn.addEventListener('click', () => this.close());

        // Seleção de usuário
        const userCards = this.modal.querySelectorAll('.user-card');
        console.log('🔍 Cartões de usuário encontrados:', userCards.length);
        userCards.forEach(card => {
            card.addEventListener('click', (e) => {
                const role = e.currentTarget.dataset.role;
                console.log('👆 Cartão clicado:', role);
                this.selectUser(role);
            });
        });

        // Formulário de login
        const loginForm = document.getElementById('loginForm');
        console.log('🔍 Formulário de login encontrado:', !!loginForm);
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            console.log('📝 Formulário submetido');
            this.handleLogin();
        });

        // Voltar para seleção
        const backBtn = document.getElementById('backToSelection');
        console.log('🔍 Botão voltar encontrado:', !!backBtn);
        backBtn.addEventListener('click', () => this.showUserSelection());

        console.log('✅ Event listeners configurados');
    }

    selectUser(role) {
        console.log('👤 Selecionando usuário com role:', role);
        this.selectedRole = role;
        this.showLoginForm();

        // Preenche email baseado no role selecionado
        const emailInput = document.getElementById('loginEmail');
        const userEmails = {
            'technician': 'joao@instalador.com',
            'operator': 'carlos@operador.com',
            'driver': 'pedro@motorista.com',
            'admin': 'camila@cadastradora.com'
        };
        const email = userEmails[role] || '';
        console.log('📧 Preenchendo email:', email);
        emailInput.value = email;
    }

    showUserSelection() {
        console.log('👥 Mostrando seleção de usuários...');
        this.modal.querySelector('.user-selection').classList.remove('hidden');
        document.getElementById('loginForm').classList.add('hidden');
    }

    showLoginForm() {
        console.log('🔐 Mostrando formulário de login...');
        this.modal.querySelector('.user-selection').classList.add('hidden');
        document.getElementById('loginForm').classList.remove('hidden');
    }

    async handleLogin() {
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        console.log('🎯 AuthModal handleLogin chamado com:', { email, password });
        console.log('🔗 Callback definido?', !!this.onLoginCallback);

        if (this.onLoginCallback) {
            try {
                console.log('📤 Chamando callback de login...');
                const result = await this.onLoginCallback({
                    username: email,
                    password: password
                });
                console.log('✅ Callback executado com sucesso:', result);
            } catch (error) {
                console.error('💥 Erro no callback de login:', error);
                alert('Erro na autenticação. Tente novamente.');
            }
        } else {
            console.error('❌ Nenhum callback de login definido');
            alert('Erro: callback de login não definido.');
        }
    }

    show(onLoginCallback) {
        console.log('🎭 AuthModal.show chamado com callback:', !!onLoginCallback);
        this.onLoginCallback = onLoginCallback;
        if (this.modal) {
            console.log('📱 Mostrando modal de autenticação...');
            this.modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            this.showUserSelection();
            console.log('✅ Modal de autenticação exibido');
        } else {
            console.error('❌ Modal não encontrado');
        }
    }

    close() {
        console.log('🚪 Fechando modal de autenticação...');
        if (this.modal) {
            this.modal.classList.remove('show');
            document.body.style.overflow = '';
            console.log('✅ Modal fechado');
        }
    }

    hide() {
        console.log('🚪 Escondendo modal de autenticação (alias para close)...');
        this.close();
    }
}

// RegistrationForm - Formulário de Cadastro
class RegistrationForm {
    constructor(containerId, onSuccess) {
        this.container = document.getElementById(containerId);
        this.onSuccess = onSuccess;
        this.currentStep = 1;
        this.formData = {};
        this.createForm();
    }

    createForm() {
        const formHtml = `
            <div class="registration-form">
                <div class="registration-header">
                    <h3>Cadastro de Usuário</h3>
                    <div class="step-indicator">
                        <span class="step ${this.currentStep === 1 ? 'active' : ''}">1</span>
                        <span class="step ${this.currentStep === 2 ? 'active' : ''}">2</span>
                        <span class="step ${this.currentStep === 3 ? 'active' : ''}">3</span>
                    </div>
                </div>

                <form id="registrationForm" class="form-steps">
                    <!-- Step 1: Tipo de Usuário -->
                    <div class="form-step" data-step="1">
                        <h4>Selecione o tipo de usuário:</h4>
                        <div class="user-type-selection">
                            <label class="user-type-option">
                                <input type="radio" name="userType" value="technician" required>
                                <div class="user-type-card">
                                    <div class="user-type-icon">👷</div>
                                    <div class="user-type-info">
                                        <h5>Instalador</h5>
                                        <p>Técnico de campo terceirizado</p>
                                    </div>
                                </div>
                            </label>
                            <label class="user-type-option">
                                <input type="radio" name="userType" value="operator" required>
                                <div class="user-type-card">
                                    <div class="user-type-icon">👨‍💼</div>
                                    <div class="user-type-info">
                                        <h5>Operador</h5>
                                        <p>Funcionário da matriz</p>
                                    </div>
                                </div>
                            </label>
                            <label class="user-type-option">
                                <input type="radio" name="userType" value="driver" required>
                                <div class="user-type-card">
                                    <div class="user-type-icon">🚚</div>
                                    <div class="user-type-info">
                                        <h5>Motorista</h5>
                                        <p>Usuário de frota</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Step 2: Dados Pessoais -->
                    <div class="form-step hidden" data-step="2">
                        <h4>Dados Pessoais:</h4>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="regName">Nome Completo:</label>
                                <input type="text" id="regName" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="regEmail">Email:</label>
                                <input type="email" id="regEmail" name="email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="regPhone">Telefone:</label>
                                <input type="tel" id="regPhone" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label for="regDocument">CPF:</label>
                                <input type="text" id="regDocument" name="document" required>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Confirmação -->
                    <div class="form-step hidden" data-step="3">
                        <h4>Confirme os dados:</h4>
                        <div class="confirmation-summary">
                            <div class="summary-item">
                                <strong>Tipo:</strong> <span id="confirmType"></span>
                            </div>
                            <div class="summary-item">
                                <strong>Nome:</strong> <span id="confirmName"></span>
                            </div>
                            <div class="summary-item">
                                <strong>Email:</strong> <span id="confirmEmail"></span>
                            </div>
                            <div class="summary-item">
                                <strong>Telefone:</strong> <span id="confirmPhone"></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="regPassword">Crie uma senha:</label>
                            <input type="password" id="regPassword" name="password" required minlength="6">
                        </div>
                        <div class="form-group">
                            <label for="regPasswordConfirm">Confirme a senha:</label>
                            <input type="password" id="regPasswordConfirm" name="passwordConfirm" required>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-secondary" id="prevStep" disabled>Anterior</button>
                        <button type="button" class="btn-primary" id="nextStep">Próximo</button>
                        <button type="submit" class="btn-primary hidden" id="submitRegistration">Cadastrar</button>
                    </div>
                </form>
            </div>
        `;

        this.container.innerHTML = formHtml;
        this.setupEventListeners();
    }

    setupEventListeners() {
        const form = document.getElementById('registrationForm');
        const nextBtn = document.getElementById('nextStep');
        const prevBtn = document.getElementById('prevStep');
        const submitBtn = document.getElementById('submitRegistration');

        // Navegação entre steps
        nextBtn.addEventListener('click', () => this.nextStep());
        prevBtn.addEventListener('click', () => this.prevStep());

        // Submissão do formulário
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitRegistration();
        });

        // Validação de senha em tempo real
        const passwordConfirm = document.getElementById('regPasswordConfirm');
        passwordConfirm.addEventListener('input', () => this.validatePasswords());
    }

    nextStep() {
        if (!this.validateCurrentStep()) {
            return;
        }

        if (this.currentStep < 3) {
            this.currentStep++;
            this.updateSteps();
        }
    }

    prevStep() {
        if (this.currentStep > 1) {
            this.currentStep--;
            this.updateSteps();
        }
    }

    updateSteps() {
        // Esconde todos os steps
        document.querySelectorAll('.form-step').forEach(step => {
            step.classList.add('hidden');
        });

        // Mostra step atual
        const currentStepEl = document.querySelector(`.form-step[data-step="${this.currentStep}"]`);
        if (currentStepEl) {
            currentStepEl.classList.remove('hidden');
        }

        // Atualiza indicadores
        document.querySelectorAll('.step').forEach((step, index) => {
            if (index + 1 === this.currentStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });

        // Atualiza botões
        const prevBtn = document.getElementById('prevStep');
        const nextBtn = document.getElementById('nextStep');
        const submitBtn = document.getElementById('submitRegistration');

        prevBtn.disabled = this.currentStep === 1;
        nextBtn.classList.toggle('hidden', this.currentStep === 3);
        submitBtn.classList.toggle('hidden', this.currentStep !== 3);

        // Se é o step 3, atualiza confirmação
        if (this.currentStep === 3) {
            this.updateConfirmation();
        }
    }

    validateCurrentStep() {
        const currentStepEl = document.querySelector(`.form-step[data-step="${this.currentStep}"]`);
        const requiredFields = currentStepEl.querySelectorAll('[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                field.style.borderColor = '#D32F2F';
                isValid = false;
            } else {
                field.style.borderColor = '';
            }
        });

        // Validações específicas
        if (this.currentStep === 3) {
            isValid = this.validatePasswords() && isValid;
        }

        return isValid;
    }

    validatePasswords() {
        const password = document.getElementById('regPassword').value;
        const confirm = document.getElementById('regPasswordConfirm').value;
        const confirmField = document.getElementById('regPasswordConfirm');

        if (password !== confirm) {
            confirmField.style.borderColor = '#D32F2F';
            return false;
        } else {
            confirmField.style.borderColor = '';
            return true;
        }
    }

    updateConfirmation() {
        // Coleta dados dos steps anteriores
        const formData = new FormData(document.getElementById('registrationForm'));
        const data = Object.fromEntries(formData);

        // Mapeia tipos para labels
        const typeLabels = {
            'technician': 'Instalador',
            'operator': 'Operador',
            'driver': 'Motorista'
        };

        document.getElementById('confirmType').textContent = typeLabels[data.userType] || data.userType;
        document.getElementById('confirmName').textContent = data.name || '';
        document.getElementById('confirmEmail').textContent = data.email || '';
        document.getElementById('confirmPhone').textContent = data.phone || '';
    }

    async submitRegistration() {
        if (!this.validateCurrentStep()) {
            return;
        }

        try {
            const formData = new FormData(document.getElementById('registrationForm'));
            const data = Object.fromEntries(formData);

            // Cria ID único
            const userId = `USR-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;

            // Prepara dados do usuário
            const userData = {
                id: userId,
                email: data.email,
                password_hash: window.databaseManager.hashPassword(data.password),
                role: data.userType,
                name: data.name,
                status: 'active',
                created_at: Date.now()
            };

            // Insere no banco
            window.databaseManager.run(
                'INSERT INTO users (id, email, password_hash, role, name, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
                [userData.id, userData.email, userData.password_hash, userData.role, userData.name, userData.status, userData.created_at]
            );

            // Adiciona à queue de sincronização
            window.syncQueue.add('create', 'users', userId, userData);

            // Callback de sucesso
            if (this.onSuccess) {
                this.onSuccess(userData);
            }

            alert('Usuário cadastrado com sucesso!');

            // Reseta formulário
            this.reset();

        } catch (error) {
            console.error('Erro no cadastro:', error);
            alert('Erro no cadastro. Tente novamente.');
        }
    }

    reset() {
        this.currentStep = 1;
        this.formData = {};
        document.getElementById('registrationForm').reset();
        this.updateSteps();
    }
}
