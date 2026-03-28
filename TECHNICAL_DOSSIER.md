# 🛰️ Sistema Rastertech - Dossiê Técnico Enterprise

Este documento descreve a arquitetura, infraestrutura e o DNA de dados da plataforma Rastertech.

## 🏗️ 1. Infraestrutura (The Stack)
O sistema utiliza uma arquitetura containerizada via Docker, integrando o Laravel 10 (PHP 8.2) com um ecossistema Supabase Enterprise (Embraet branded).

- **Gateway de API**: Kong 2.8.1 (Porta 8000)
- **Database**: PostgreSQL 15.8 (Porta 5432)
- **Cache & Queues**: Redis Alpine
- **App Engine**: PHP 8.2-FPM + Nginx
- **Observabilidade**: Vector (Logs centralizados em JSON)

## 🧬 2. Arquitetura de Dados (Unified Schema)
Utilizamos uma abordagem de **Migração Mestra Unificada** (`2026_03_30_...`) para garantir a estabilidade das chaves estrangeiras.

### Entidades Principais:
- **`Devices`**: Rastreadores. Coração da telemetria. Vinculado a Modelos, Chips e Clientes.
- **`GsmCards`**: Chips GSM. Armazena as APNs (Arqia, Vivo) e credenciais de rede.
- **`DeviceModels`**: Onde reside o "Cérebro" dos comandos SMS.
- **`Platforms`**: Servidores de destino IP/Porta onde os rastreadores reportam.
- **`Customers`**: Gestão Multi-Tenant (inclui o Cliente "Estoque Geral").
- **`CustomerSubUsers`**: Perfil de Motoristas vs Operadores.

## 📡 3. Lógica de Ativação SMS
O sistema gera comandos automaticamente unindo:
`Plataforma (IP) + Device (Porta) + GsmCard (APN/User/Pass) + DeviceModel (Commands)`.

## 🛠️ 4. Comandos de Manutenção (Ubuntu/WSL)
- **Subir Stack**: `docker-compose up -d`
- **Log do Banco**: `docker logs rastertech-db`
- **Zerar Banco**: `docker exec -it rastertech-app php artisan migrate:fresh`
- **Alimentar Iniciais**: `docker exec -it rastertech-app php artisan db:seed` (Em breve!)

## 5. Padrões de Robustez Operacional

### 5.1 Doutrina da Neutralidade de Infraestrutura
**DIRETIVA OBRIGATÓRIA**: Proibido o uso de `route()` ou `url()` para assets/links internos. Use caminhos relativos (Ex: `/sim-cards`). Todos os paginadores devem usar `->withPath()`.

### 5.2 Motor de Consulta Universal
Sempre inicializar consultas de inventário com `withTrashed()` para garantir a visibilidade de ativos inativos/estornados.

### 5.3 Protocolo de Comando Docker (MEMÓRIA DE APRENDIZADO)
**DIRETIVA OBRIGATÓRIA**: Para execuções de comandos `artisan` dentro da infraestrutura Rastertech, o comando padrão e único é:
`docker exec -it rastertech-app php artisan [comando]`

---

## 6. Log de Evolução de Aprendizado (AI Memory)
1. **[2026-03-27]** Ghost Assets: Resolvido com `withTrashed()`.
2. **[2026-03-28]** Docker Command Drift: Registrada a necessidade de usar `docker exec -it` em vez de `docker-compose exec` para garantir compatibilidade com o ambiente de produção do usuário.

| Data | Erro | Causa | Solução |
| :--- | :--- | :--- | :--- |
| 28/03/26 | Class Not Found (Provider/Customer) | Modelos não importados no Controlador | Adicionado `use App\Models\...` ao topo do controlador |
| 28/03/26 | Erro de Stack do Blade | Duplicidade de `@endpush` no footer da View | Removido `@endpush` excedente |
| 28/03/26 | Falha no Registro (Campos PIN/PUK) | Colunas inexistentes no Banco de Dados (Migração não rodada) | Criação da migração e registro do protocolo Docker |
| 28/03/26 | Erro de Comando (Docker Drift) | Tentativa de uso de docker-compose em vez de docker exec | **REGISTRADO NA MEMÓRIA**: Usar sempre `docker exec -it rastertech-app php artisan` |
| 28/03/26 | Ordenação Unidirecional (Single Click) | Operador `+` de união no PHP ignorando novos parâmetros | Substituído por `array_merge()` no SimCardController |
| 28/03/26 | Chips sem Visibilidade (Estoque) | Filtro binário (Ativo/Inativo) ocultando status `inactive` | Implementado Filtro Tri-Estado (Ativo/Estoque/Lixeira) |
| 28/03/26 | Erro Interno (Check Violation) | Constraint `gsm_cards_status_check` bloqueando o status `canceled` | Nova Migração + Seeder para liberar status `canceled` no PGSQL |
| 28/03/26 | Coluna de Cliente Inexistente | Tabela `gsm_cards` original não possuía vínculo direto com Clientes | Adição da coluna `customer_id` via Migração/Seeder e Integração Eloquent |
| 28/03/26 | Perda de Dados (Cancelamento) | Fechamento acidental do modal limpava o motivo digitado | Implementado motor de **Draft Persistence** via `localStorage` |
---
**Status**: Fase 1 (Arquitetura & Infraestrutura) - CONCLUÍDA.
**Status de Estabilidade**: BLINDAGEM DE PORTA E REGISTROS ATIVA.
**Autor**: Antigravity AI (Pair Programming com Ubiratan).

