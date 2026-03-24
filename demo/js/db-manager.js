/**
 * DatabaseManager - SQLite wrapper para persistência offline
 * Usa sql.js (SQLite compilado para WebAssembly)
 */
class DatabaseManager {
    constructor() {
        this.db = null;
        this.SQL = null;
        this.isInitialized = false;
        this.dbFile = 'rastertech-demo.db';
    }

    /**
     * Inicializa o banco de dados SQLite
     */
    async init() {
        try {
            // Carrega sql.js dinamicamente
            if (!window.SQL) {
                await this.loadSQLJS();
            }
            this.SQL = window.SQL;

            // Tenta carregar DB existente do localStorage
            const savedDbData = localStorage.getItem(`rastertech-${this.dbFile}`);
            if (savedDbData) {
                // Carrega DB existente
                const dbArray = this.base64ToUint8Array(savedDbData);
                this.db = new this.SQL.Database(dbArray);
            } else {
                // Cria novo DB
                this.db = new this.SQL.Database();
                await this.createSchema();
                await this.seedMockData();
            }

            this.isInitialized = true;
            console.log('✅ DatabaseManager: SQLite inicializado com sucesso');

        } catch (error) {
            console.error('❌ DatabaseManager: Erro ao inicializar SQLite:', error);
            throw error;
        }
    }

    /**
     * Carrega sql.js dinamicamente
     */
    async loadSQLJS() {
        console.log('📦 Carregando sql.js...');

        // Verificar se já está carregado
        if (window.SQL) {
            console.log('✅ sql.js já carregado');
            return;
        }

        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/sql.js/1.8.0/sql-wasm.js';

            script.onload = () => {
                console.log('📜 Script sql.js carregado, aguardando initSqlJs...');

                // Aguardar initSqlJs estar disponível
                const checkInitSqlJs = () => {
                    if (window.initSqlJs) {
                        console.log('🔧 initSqlJs encontrado, inicializando...');
                        window.SQL = window.initSqlJs();
                        if (window.SQL) {
                            console.log('✅ sql.js inicializado com sucesso');
                            resolve();
                        } else {
                            reject(new Error('Falha ao inicializar sql.js'));
                        }
                    } else {
                        console.log('⏳ Aguardando initSqlJs...');
                        setTimeout(checkInitSqlJs, 100);
                    }
                };

                checkInitSqlJs();
            };

            script.onerror = (error) => {
                console.error('❌ Erro ao carregar sql.js:', error);
                reject(new Error('Falha ao carregar sql.js do CDN'));
            };

            document.head.appendChild(script);
        });
    }

    /**
     * Cria o schema do banco de dados
     */
    async createSchema() {
        const schema = `
            -- Usuários com roles RBAC
            CREATE TABLE IF NOT EXISTS users (
                id TEXT PRIMARY KEY,
                email TEXT UNIQUE NOT NULL,
                password_hash TEXT NOT NULL,
                role TEXT NOT NULL CHECK (role IN ('technician', 'operator', 'driver', 'admin')),
                name TEXT NOT NULL,
                status TEXT DEFAULT 'active' CHECK (status IN ('active', 'inactive')),
                created_at INTEGER NOT NULL,
                synced INTEGER DEFAULT 0
            );

            -- Ativos (veículos, rastreadores, chips)
            CREATE TABLE IF NOT EXISTS assets (
                id TEXT PRIMARY KEY,
                plate TEXT,
                tracker_imei TEXT,
                chip_number TEXT,
                status TEXT DEFAULT 'available' CHECK (status IN ('available', 'installed', 'maintenance', 'retiring')),
                owner_id TEXT,
                created_at INTEGER NOT NULL,
                synced INTEGER DEFAULT 0,
                FOREIGN KEY (owner_id) REFERENCES users (id)
            );

            -- Ordens de Serviço
            CREATE TABLE IF NOT EXISTS service_orders (
                id TEXT PRIMARY KEY,
                asset_id TEXT NOT NULL,
                technician_id TEXT NOT NULL,
                description TEXT,
                status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'in_progress', 'completed', 'cancelled')),
                created_at INTEGER NOT NULL,
                completed_at INTEGER,
                synced INTEGER DEFAULT 0,
                FOREIGN KEY (asset_id) REFERENCES assets (id),
                FOREIGN KEY (technician_id) REFERENCES users (id)
            );

            -- Queue de sincronização offline
            CREATE TABLE IF NOT EXISTS sync_queue (
                id TEXT PRIMARY KEY,
                operation TEXT NOT NULL CHECK (operation IN ('create', 'update', 'delete')),
                table_name TEXT NOT NULL,
                record_id TEXT NOT NULL,
                payload TEXT NOT NULL, -- JSON
                created_at INTEGER NOT NULL,
                retry_count INTEGER DEFAULT 0,
                status TEXT DEFAULT 'pending' CHECK (status IN ('pending', 'processing', 'completed', 'failed')),
                error_message TEXT
            );

            -- Índices para performance
            CREATE INDEX IF NOT EXISTS idx_assets_status ON assets(status);
            CREATE INDEX IF NOT EXISTS idx_assets_owner ON assets(owner_id);
            CREATE INDEX IF NOT EXISTS idx_service_orders_asset ON service_orders(asset_id);
            CREATE INDEX IF NOT EXISTS idx_service_orders_technician ON service_orders(technician_id);
            CREATE INDEX IF NOT EXISTS idx_sync_queue_status ON sync_queue(status);
        `;

        this.db.run(schema);
    }

    /**
     * Insere dados mockados para demonstração
     */
    async seedMockData() {
        console.log('🌱 Inserindo dados mockados...');
        const now = Date.now();

        // Usuários mockados (4 tipos conforme UX)
        const users = [
            {
                id: 'USR-001',
                email: 'joao@instalador.com',
                password_hash: this.hashPassword('joao123'),
                role: 'technician',
                name: 'João Silva',
                status: 'active',
                created_at: now
            },
            {
                id: 'USR-002',
                email: 'carlos@operador.com',
                password_hash: this.hashPassword('carlos123'),
                role: 'operator',
                name: 'Carlos Santos',
                status: 'active',
                created_at: now
            },
            {
                id: 'USR-003',
                email: 'pedro@motorista.com',
                password_hash: this.hashPassword('pedro123'),
                role: 'driver',
                name: 'Pedro Oliveira',
                status: 'active',
                created_at: now
            },
            {
                id: 'USR-004',
                email: 'camila@cadastradora.com',
                password_hash: this.hashPassword('camila123'),
                role: 'admin',
                name: 'Camila Costa',
                status: 'active',
                created_at: now
            }
        ];

        // Ativos mockados
        const assets = [
            { id: 'AST-001', plate: 'ABC-1234', tracker_imei: '123456789012345', chip_number: '11987654321', status: 'available', owner_id: null, created_at: now },
            { id: 'AST-002', plate: 'DEF-5678', tracker_imei: '987654321098765', chip_number: '11987654322', status: 'installed', owner_id: 'USR-003', created_at: now },
            { id: 'AST-003', plate: 'GHI-9012', tracker_imei: '456789012345678', chip_number: '11987654323', status: 'maintenance', owner_id: 'USR-003', created_at: now },
            { id: 'AST-004', plate: 'JKL-3456', tracker_imei: '789012345678901', chip_number: '11987654324', status: 'retiring', owner_id: 'USR-003', created_at: now }
        ];

        // Insere usuários
        const userStmt = this.db.prepare('INSERT OR REPLACE INTO users (id, email, password_hash, role, name, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
        users.forEach(user => {
            userStmt.run([user.id, user.email, user.password_hash, user.role, user.name, user.status, user.created_at]);
        });
        userStmt.free();

        console.log('✅ Dados mockados inseridos com sucesso');

        // Insere ativos
        const assetStmt = this.db.prepare('INSERT OR REPLACE INTO assets (id, plate, tracker_imei, chip_number, status, owner_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
        assets.forEach(asset => {
            assetStmt.run([asset.id, asset.plate, asset.tracker_imei, asset.chip_number, asset.status, asset.owner_id, asset.created_at]);
        });
        assetStmt.free();

        console.log('DatabaseManager: Dados mockados inseridos');
    }

    /**
     * Hash simples de senha (apenas para demo)
     */
    hashPassword(password) {
        // Hash simples mas consistente para demo - NÃO usar em produção
        let hash = 5381; // Valor inicial (número primo)
        for (let i = 0; i < password.length; i++) {
            const char = password.charCodeAt(i);
            hash = ((hash << 5) + hash) + char; // hash * 33 + char
        }
        // Garante que seja sempre positivo e consistente
        return Math.abs(hash).toString();
    }

    /**
     * Executa uma query SELECT
     */
    query(sql, params = []) {
        if (!this.isInitialized) {
            throw new Error('DatabaseManager: DB não inicializado');
        }

        try {
            const stmt = this.db.prepare(sql);
            const results = [];

            if (params.length > 0) {
                stmt.bind(params);
            }

            while (stmt.step()) {
                results.push(stmt.getAsObject());
            }

            stmt.free();
            return results;
        } catch (error) {
            console.error('DatabaseManager: Erro na query:', sql, error);
            throw error;
        }
    }

    /**
     * Executa uma query INSERT/UPDATE/DELETE
     */
    run(sql, params = []) {
        if (!this.isInitialized) {
            throw new Error('DatabaseManager: DB não inicializado');
        }

        try {
            const stmt = this.db.prepare(sql);
            if (params.length > 0) {
                stmt.bind(params);
            }
            stmt.step();
            stmt.free();
        } catch (error) {
            console.error('DatabaseManager: Erro ao executar:', sql, error);
            throw error;
        }
    }

    /**
     * Autentica usuário
     */
    authenticateUser(email, password) {
        console.log('🔐 Tentando autenticar:', email);
        const hashedPassword = this.hashPassword(password);

        try {
            const users = this.query('SELECT * FROM users WHERE email = ? AND password_hash = ? AND status = ?', [email, hashedPassword, 'active']);

            if (users.length > 0) {
                console.log('✅ Usuário autenticado:', users[0].name);
                return users[0];
            }

            console.log('❌ Credenciais inválidas para:', email);
            return null;
        } catch (error) {
            console.error('💥 Erro na autenticação:', error);
            throw error;
        }
    }

    /**
     * Busca usuário por ID
     */
    getUserById(userId) {
        const users = this.query('SELECT * FROM users WHERE id = ?', [userId]);
        return users.length > 0 ? users[0] : null;
    }

    /**
     * Lista todos os ativos
     */
    getAllAssets() {
        return this.query('SELECT * FROM assets ORDER BY created_at DESC');
    }

    /**
     * Busca ativos por filtros
     */
    searchAssets(filters = {}) {
        let sql = 'SELECT * FROM assets WHERE 1=1';
        const params = [];

        if (filters.status) {
            sql += ' AND status = ?';
            params.push(filters.status);
        }

        if (filters.owner_id) {
            sql += ' AND owner_id = ?';
            params.push(filters.owner_id);
        }

        if (filters.search) {
            sql += ' AND (plate LIKE ? OR tracker_imei LIKE ? OR chip_number LIKE ?)';
            const searchTerm = `%${filters.search}%`;
            params.push(searchTerm, searchTerm, searchTerm);
        }

        sql += ' ORDER BY created_at DESC';
        return this.query(sql, params);
    }

    /**
     * Adiciona item à queue de sincronização
     */
    addToSyncQueue(operation, tableName, recordId, payload) {
        const id = `SYNC-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
        const now = Date.now();

        this.run(
            'INSERT INTO sync_queue (id, operation, table_name, record_id, payload, created_at) VALUES (?, ?, ?, ?, ?, ?)',
            [id, operation, tableName, recordId, JSON.stringify(payload), now]
        );

        return id;
    }

    /**
     * Busca itens pendentes na queue de sincronização
     */
    getPendingSyncItems() {
        return this.query('SELECT * FROM sync_queue WHERE status = ? ORDER BY created_at ASC', ['pending']);
    }

    /**
     * Atualiza status de item na queue
     */
    updateSyncItemStatus(syncId, status, errorMessage = null) {
        if (errorMessage) {
            this.run('UPDATE sync_queue SET status = ?, error_message = ?, retry_count = retry_count + 1 WHERE id = ?',
                    [status, errorMessage, syncId]);
        } else {
            this.run('UPDATE sync_queue SET status = ? WHERE id = ?', [status, syncId]);
        }
    }

    /**
     * Persiste o banco no localStorage
     */
    saveToStorage() {
        if (!this.isInitialized) return;

        try {
            const dbData = this.db.export();
            const base64Data = this.uint8ArrayToBase64(dbData);
            localStorage.setItem(`rastertech-${this.dbFile}`, base64Data);
        } catch (error) {
            console.error('DatabaseManager: Erro ao salvar no localStorage:', error);
        }
    }

    /**
     * Converte Uint8Array para base64
     */
    uint8ArrayToBase64(array) {
        let binary = '';
        const bytes = new Uint8Array(array);
        for (let i = 0; i < bytes.byteLength; i++) {
            binary += String.fromCharCode(bytes[i]);
        }
        return btoa(binary);
    }

    /**
     * Converte base64 para Uint8Array
     */
    base64ToUint8Array(base64) {
        const binary = atob(base64);
        const bytes = new Uint8Array(binary.length);
        for (let i = 0; i < binary.length; i++) {
            bytes[i] = binary.charCodeAt(i);
        }
        return bytes;
    }

    /**
     * Fecha o banco de dados
     */
    close() {
        if (this.db) {
            this.saveToStorage();
            this.db.close();
            this.db = null;
            this.isInitialized = false;
        }
    }
}

// Exporta para uso global
window.DatabaseManager = DatabaseManager;