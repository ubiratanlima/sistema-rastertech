// ========================================
// DADOS FICTÍCIOS REALISTAS
// ========================================

const mockData = {
    // Ativos do sistema
    assets: [
        {
            id: 'ASSET-001',
            name: 'Veículo ABC-1234',
            type: 'vehicle',
            plate: 'ABC-1234',
            trackerIMEI: '352045089804842',
            chipNumber: '1234567890123456',
            status: 'Instalado',
            installDate: '2026-03-15',
            customer: 'Transportes Silva LTDA',
            lastSync: '2026-03-24T14:30:00Z',
            owner: 'João Silva'
        },
        {
            id: 'ASSET-002',
            name: 'Veículo BCD-5678',
            type: 'vehicle',
            plate: 'BCD-5678',
            trackerIMEI: '352045089804843',
            chipNumber: '1234567890123457',
            status: 'Instalado',
            installDate: '2026-01-10',
            customer: 'Logística Express SA',
            lastSync: '2026-03-24T14:15:00Z',
            owner: 'Carlos Oliveira'
        },
        {
            id: 'ASSET-003',
            name: 'Veículo CDE-9012',
            type: 'vehicle',
            plate: 'CDE-9012',
            trackerIMEI: '352045089804844',
            chipNumber: '1234567890123458',
            status: 'Disponível',
            installDate: null,
            customer: 'Não Vinculado',
            lastSync: null,
            owner: null
        },
        {
            id: 'ASSET-004',
            name: 'Veículo DEF-3456',
            type: 'vehicle',
            plate: 'DEF-3456',
            trackerIMEI: '352045089804845',
            chipNumber: '1234567890123459',
            status: 'Manutenção',
            installDate: '2025-12-01',
            customer: 'Transportes Silva LTDA',
            lastSync: '2026-03-20T10:00:00Z',
            owner: 'Pedro Santos'
        },
        {
            id: 'ASSET-005',
            name: 'Veículo EFG-7890',
            type: 'vehicle',
            plate: 'EFG-7890',
            trackerIMEI: '352045089804846',
            chipNumber: '1234567890123460',
            status: 'Instalado',
            installDate: '2026-02-20',
            customer: 'Frota Nordeste Inc',
            lastSync: '2026-03-24T13:45:00Z',
            owner: 'Amanda Costa'
        },
        {
            id: 'ASSET-006',
            name: 'Veículo FGH-1234',
            type: 'vehicle',
            plate: 'FGH-1234',
            trackerIMEI: '352045089804847',
            chipNumber: '1234567890123461',
            status: 'Disponível',
            installDate: null,
            customer: 'Não Vinculado',
            lastSync: null,
            owner: null
        },
        {
            id: 'ASSET-007',
            name: 'Veículo GHI-5678',
            type: 'vehicle',
            plate: 'GHI-5678',
            trackerIMEI: '352045089804848',
            chipNumber: '1234567890123462',
            status: 'Instalado',
            installDate: '2026-03-01',
            customer: 'Centro Logístico Brasil',
            lastSync: '2026-03-24T14:20:00Z',
            owner: 'Mariana Silva'
        },
        {
            id: 'ASSET-008',
            name: 'Veículo HIJ-9012',
            type: 'vehicle',
            plate: 'HIJ-9012',
            trackerIMEI: '352045089804849',
            chipNumber: '1234567890123463',
            status: 'Em Retirada',
            installDate: '2025-06-15',
            customer: 'Transportes Silva LTDA',
            lastSync: '2026-03-18T09:30:00Z',
            owner: 'Roberto Lima'
        },
        {
            id: 'ASSET-009',
            name: 'Veículo IJK-3456',
            type: 'vehicle',
            plate: 'IJK-3456',
            trackerIMEI: '352045089804850',
            chipNumber: '1234567890123464',
            status: 'Disponível',
            installDate: null,
            customer: 'Não Vinculado',
            lastSync: null,
            owner: null
        },
        {
            id: 'ASSET-010',
            name: 'Veículo JKL-7890',
            type: 'vehicle',
            plate: 'JKL-7890',
            trackerIMEI: '352045089804851',
            chipNumber: '1234567890123465',
            status: 'Instalado',
            installDate: '2025-11-20',
            customer: 'Logística Express SA',
            lastSync: '2026-03-24T14:00:00Z',
            owner: 'Fernanda Rocha'
        },
        {
            id: 'ASSET-011',
            name: 'Veículo KLM-1234',
            type: 'vehicle',
            plate: 'KLM-1234',
            trackerIMEI: '352045089804852',
            chipNumber: '1234567890123466',
            status: 'Instalado',
            installDate: '2026-03-10',
            customer: 'Frota Nordeste Inc',
            lastSync: '2026-03-24T14:25:00Z',
            owner: 'Gustavo Martins'
        },
        {
            id: 'ASSET-012',
            name: 'Veículo LMN-5678',
            type: 'vehicle',
            plate: 'LMN-5678',
            trackerIMEI: '352045089804853',
            chipNumber: '1234567890123467',
            status: 'Disponível',
            installDate: null,
            customer: 'Não Vinculado',
            lastSync: null,
            owner: null
        }
    ],

    // Técnicos instaladores
    technicians: [
        { id: 'TECH-001', name: 'João Silva', email: 'joao@rastertech.com', status: 'active' },
        { id: 'TECH-002', name: 'Carlos Oliveira', email: 'carlos@rastertech.com', status: 'active' },
        { id: 'TECH-003', name: 'Pedro Santos', email: 'pedro@rastertech.com', status: 'active' },
        { id: 'TECH-004', name: 'Amanda Costa', email: 'amanda@rastertech.com', status: 'active' }
    ],

    // Clientes/Empresas
    customers: [
        {
            id: 'CUST-001',
            name: 'Transportes Silva LTDA',
            type: 'fleet_operator',
            vehicles: ['ABC-1234', 'DEF-3456', 'HIJ-9012']
        },
        {
            id: 'CUST-002',
            name: 'Logística Express SA',
            type: 'fleet_operator',
            vehicles: ['BCD-5678', 'JKL-7890']
        },
        {
            id: 'CUST-003',
            name: 'Frota Nordeste Inc',
            type: 'fleet_operator',
            vehicles: ['EFG-7890', 'KLM-1234']
        },
        {
            id: 'CUST-004',
            name: 'Centro Logístico Brasil',
            type: 'fleet_operator',
            vehicles: ['GHI-5678']
        }
    ],

    // Ordens de Serviço (Histórico)
    serviceOrders: [
        {
            id: 'OS-2026-03-001',
            plate: 'ABC-1234',
            trackerIMEI: '352045089804842',
            technician: 'João Silva',
            date: '2026-03-15',
            status: 'completed',
            notes: 'Instalação realizada com sucesso. Cliente presente durante o procedimento.'
        },
        {
            id: 'OS-2026-03-002',
            plate: 'BCD-5678',
            trackerIMEI: '352045089804843',
            technician: 'Carlos Oliveira',
            date: '2026-01-10',
            status: 'completed',
            notes: 'Veículo em bom estado. Rastreador calibrado corretamente.'
        },
        {
            id: 'OS-2026-03-003',
            plate: 'EFG-7890',
            trackerIMEI: '352045089804846',
            technician: 'Pedro Santos',
            date: '2026-02-20',
            status: 'completed',
            notes: 'Instalação concluída. Testes de conectividade OK.'
        }
    ],

    // Estados de status
    statuses: [
        { value: 'Disponível', label: 'Disponível', color: '#4CAF50' },
        { value: 'Instalado', label: 'Instalado', color: '#2196F3' },
        { value: 'Manutenção', label: 'Em Manutenção', color: '#FF9800' },
        { value: 'Em Retirada', label: 'Em Retirada', color: '#F44336' }
    ]
};

// Funções auxiliares
function getAssetsByStatus(status) {
    return mockData.assets.filter(asset => asset.status === status);
}

function getAssetByPlate(plate) {
    return mockData.assets.find(asset => asset.plate === plate);
}

// ========================================
// USUÁRIOS MOCKADOS PARA AUTENTICAÇÃO
// ========================================

mockData.users = [
    {
        id: 'USR-001',
        email: 'joao@instalador.com',
        password: 'joao123', // Em produção seria hash
        role: 'technician',
        name: 'João Silva',
        avatar: '👷',
        status: 'active',
        description: 'Instalador de Campo - Técnico terceirizado'
    },
    {
        id: 'USR-002',
        email: 'carlos@operador.com',
        password: 'carlos123',
        role: 'operator',
        name: 'Carlos Santos',
        avatar: '👨‍💼',
        status: 'active',
        description: 'Operador de Matriz - Controle de ativos'
    },
    {
        id: 'USR-003',
        email: 'pedro@motorista.com',
        password: 'pedro123',
        role: 'driver',
        name: 'Pedro Oliveira',
        avatar: '🚚',
        status: 'active',
        description: 'Motorista de Frota - Checklists diários'
    },
    {
        id: 'USR-004',
        email: 'camila@cadastradora.com',
        password: 'camila123',
        role: 'admin',
        name: 'Camila Costa',
        avatar: '👩‍💻',
        status: 'active',
        description: 'Cadastradora - Gestão de cadastros'
    }
];

// ========================================
// INICIALIZAÇÃO DO SISTEMA DE BANCO DE DADOS
// ========================================

// Instâncias globais
window.databaseManager = null;
window.syncQueue = null;

// Inicialização assíncrona do sistema
async function initializeDatabaseSystem() {
    try {
        console.log('Inicializando sistema de banco de dados...');

        // Inicializa DatabaseManager
        window.databaseManager = new DatabaseManager();
        await window.databaseManager.init();

        // Inicializa SyncQueue
        window.syncQueue = new SyncQueue(window.databaseManager);

        console.log('Sistema de banco de dados inicializado com sucesso!');

        // Log dos logins disponíveis
        console.log('=== LOGINS DISPONÍVEIS PARA TESTE ===');
        mockData.users.forEach(user => {
            console.log(`${user.name} (${user.role}): ${user.email} / ${user.password}`);
        });
        console.log('=====================================');

    } catch (error) {
        console.error('Erro ao inicializar sistema de banco de dados:', error);
        alert('Erro ao inicializar o sistema. Recarregue a página.');
    }
}

// Função auxiliar para converter dados antigos para o novo formato
function migrateLegacyData() {
    // Migra dados do localStorage para SQLite se necessário
    const legacyAssets = localStorage.getItem('rastertech-assets');
    if (legacyAssets && window.databaseManager) {
        try {
            const assets = JSON.parse(legacyAssets);
            console.log('Migrando dados legados para SQLite...');

            assets.forEach(asset => {
                // Converte formato antigo para novo
                const newAsset = {
                    id: asset.id || `ASSET-${Date.now()}`,
                    plate: asset.plate,
                    tracker_imei: asset.trackerIMEI || asset.trackerIMEI,
                    chip_number: asset.chipNumber || asset.chipNumber,
                    status: asset.status,
                    owner_id: asset.owner ? findUserIdByName(asset.owner) : null,
                    created_at: asset.installDate ? new Date(asset.installDate).getTime() : Date.now()
                };

                try {
                    window.databaseManager.run(
                        'INSERT OR IGNORE INTO assets (id, plate, tracker_imei, chip_number, status, owner_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)',
                        [newAsset.id, newAsset.plate, newAsset.tracker_imei, newAsset.chip_number, newAsset.status, newAsset.owner_id, newAsset.created_at]
                    );
                } catch (e) {
                    console.warn('Erro ao migrar asset:', asset.id, e);
                }
            });

            // Remove dados legados após migração
            localStorage.removeItem('rastertech-assets');
            console.log('Migração concluída!');

        } catch (error) {
            console.error('Erro na migração de dados:', error);
        }
    }
}

function findUserIdByName(name) {
    const user = mockData.users.find(u => u.name === name);
    return user ? user.id : null;
}

// Inicializa quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    initializeDatabaseSystem().then(() => {
        migrateLegacyData();
    });
});

function getAssetByIMEI(imei) {
    return mockData.assets.find(asset => asset.trackerIMEI === imei);
}

function getTechnicianById(id) {
    return mockData.technicians.find(tech => tech.id === id);
}

function getCustomerById(id) {
    return mockData.customers.find(cust => cust.id === id);
}

function getStatusStats() {
    return {
        total: mockData.assets.length,
        installed: getAssetsByStatus('Instalado').length,
        available: getAssetsByStatus('Disponível').length,
        maintenance: getAssetsByStatus('Manutenção').length,
        withdrawal: getAssetsByStatus('Em Retirada').length
    };
}

// Simulação de sincronização
function simulateSync() {
    const now = new Date();
    return {
        timestamp: now.toISOString(),
        synced: true,
        itemsCount: Math.floor(Math.random() * 10) + 1
    };
}

// Salvar dados no localStorage
function saveToLocalStorage(key, data) {
    try {
        localStorage.setItem(`rastertech-${key}`, JSON.stringify(data));
        return true;
    } catch (e) {
        console.error('Erro ao salvar dados:', e);
        return false;
    }
}

// Recuperar dados do localStorage
function getFromLocalStorage(key) {
    try {
        const data = localStorage.getItem(`rastertech-${key}`);
        return data ? JSON.parse(data) : null;
    } catch (e) {
        console.error('Erro ao recuperar dados:', e);
        return null;
    }
}

// Limpar dados do localStorage
function clearLocalStorage() {
    const keys = Object.keys(localStorage);
    keys.forEach(key => {
        if (key.startsWith('rastertech-')) {
            localStorage.removeItem(key);
        }
    });
}
