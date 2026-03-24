/**
 * SyncQueue - Gerenciador de sincronização offline
 * Gerencia queue de operações pendentes para sincronização com backend
 */
class SyncQueue {
    constructor(databaseManager) {
        this.db = databaseManager;
        this.isOnline = navigator.onLine;
        this.isProcessing = false;
        this.retryDelays = [1000, 2000, 4000, 8000]; // Exponential backoff
        this.maxRetries = 3;

        // Monitora status de conectividade
        window.addEventListener('online', () => {
            console.log('SyncQueue: Conexão restaurada');
            this.isOnline = true;
            this.triggerSync();
        });

        window.addEventListener('offline', () => {
            console.log('SyncQueue: Conexão perdida');
            this.isOnline = false;
        });
    }

    /**
     * Adiciona operação à queue
     */
    add(operation, tableName, recordId, payload) {
        const syncId = this.db.addToSyncQueue(operation, tableName, recordId, payload);
        console.log(`SyncQueue: Operação ${operation} adicionada à queue (ID: ${syncId})`);

        // Tenta sincronizar imediatamente se online
        if (this.isOnline && !this.isProcessing) {
            setTimeout(() => this.triggerSync(), 100);
        }

        return syncId;
    }

    /**
     * Dispara sincronização manual
     */
    triggerSync() {
        if (!this.isOnline || this.isProcessing) {
            return;
        }

        this.processQueue();
    }

    /**
     * Processa a queue de sincronização
     */
    async processQueue() {
        if (this.isProcessing || !this.isOnline) {
            return;
        }

        this.isProcessing = true;
        console.log('SyncQueue: Iniciando processamento da queue...');

        try {
            const pendingItems = this.db.getPendingSyncItems();

            if (pendingItems.length === 0) {
                console.log('SyncQueue: Queue vazia');
                this.isProcessing = false;
                return;
            }

            console.log(`SyncQueue: Processando ${pendingItems.length} itens`);

            // Processa em lotes para não sobrecarregar
            const batchSize = 5;
            for (let i = 0; i < pendingItems.length; i += batchSize) {
                const batch = pendingItems.slice(i, i + batchSize);
                await this.processBatch(batch);

                // Pequena pausa entre lotes
                await this.delay(200);
            }

        } catch (error) {
            console.error('SyncQueue: Erro no processamento da queue:', error);
        } finally {
            this.isProcessing = false;
        }
    }

    /**
     * Processa um lote de itens da queue
     */
    async processBatch(batch) {
        const promises = batch.map(item => this.processItem(item));
        await Promise.allSettled(promises);
    }

    /**
     * Processa um item individual da queue
     */
    async processItem(item) {
        try {
            // Marca como processando
            this.db.updateSyncItemStatus(item.id, 'processing');

            // Simula chamada para API (mock)
            const success = await this.simulateApiCall(item);

            if (success) {
                // Sucesso - marca como completado
                this.db.updateSyncItemStatus(item.id, 'completed');
                console.log(`SyncQueue: Item ${item.id} sincronizado com sucesso`);
            } else {
                // Falha - verifica se deve tentar novamente
                await this.handleSyncFailure(item);
            }

        } catch (error) {
            console.error(`SyncQueue: Erro ao processar item ${item.id}:`, error);
            await this.handleSyncFailure(item, error.message);
        }
    }

    /**
     * Simula chamada para API (mock para demonstração)
     */
    async simulateApiCall(item) {
        // Simula latência de rede
        await this.delay(Math.random() * 1500 + 500);

        // Simula falha aleatória (20% de chance)
        if (Math.random() < 0.2) {
            throw new Error('Simulated network error');
        }

        // Simula resposta da API
        return {
            success: true,
            data: item.payload
        };
    }

    /**
     * Trata falha de sincronização
     */
    async handleSyncFailure(item, errorMessage = 'Unknown error') {
        const retryCount = item.retry_count + 1;

        if (retryCount >= this.maxRetries) {
            // Máximo de tentativas atingido
            this.db.updateSyncItemStatus(item.id, 'failed', `Max retries exceeded: ${errorMessage}`);
            console.warn(`SyncQueue: Item ${item.id} falhou definitivamente após ${retryCount} tentativas`);
        } else {
            // Agenda retry com backoff exponencial
            const delay = this.retryDelays[Math.min(retryCount - 1, this.retryDelays.length - 1)];
            this.db.updateSyncItemStatus(item.id, 'pending', `Retry ${retryCount}/${this.maxRetries}: ${errorMessage}`);

            console.log(`SyncQueue: Item ${item.id} será retentado em ${delay}ms (tentativa ${retryCount})`);

            setTimeout(() => {
                if (this.isOnline) {
                    this.processItem(item);
                }
            }, delay);
        }
    }

    /**
     * Utilitário para delay
     */
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    /**
     * Retorna status da queue
     */
    getStatus() {
        const pendingItems = this.db.getPendingSyncItems();
        const failedItems = this.db.query('SELECT * FROM sync_queue WHERE status = ?', ['failed']);

        return {
            isOnline: this.isOnline,
            isProcessing: this.isProcessing,
            pendingCount: pendingItems.length,
            failedCount: failedItems.length,
            totalQueued: pendingItems.length + failedItems.length
        };
    }

    /**
     * Força retry de itens falhados
     */
    retryFailedItems() {
        const failedItems = this.db.query('SELECT * FROM sync_queue WHERE status = ?', ['failed']);

        failedItems.forEach(item => {
            this.db.updateSyncItemStatus(item.id, 'pending', 'Manual retry');
        });

        console.log(`SyncQueue: ${failedItems.length} itens falhados marcados para retry`);

        if (failedItems.length > 0 && this.isOnline) {
            setTimeout(() => this.triggerSync(), 100);
        }
    }

    /**
     * Limpa itens antigos da queue (mais de 7 dias)
     */
    cleanupOldItems() {
        const sevenDaysAgo = Date.now() - (7 * 24 * 60 * 60 * 1000);

        this.db.run('DELETE FROM sync_queue WHERE status IN (?, ?) AND created_at < ?',
                   ['completed', 'failed', sevenDaysAgo]);

        console.log('SyncQueue: Itens antigos limpos da queue');
    }
}

// Exporta para uso global
window.SyncQueue = SyncQueue;