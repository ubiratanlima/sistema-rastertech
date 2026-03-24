---
title: 'Validação Visual do UX Design - Protótipo RasterTech'
type: 'feature'
created: '2026-03-24'
status: 'in-progress'
baseline_commit: 'HEAD'
context: ['_evo-output/planning-artifacts/sistema-completo/ux-design-specification.md', '_evo-output/planning-artifacts/sistema-completo/prd.md']
---

# Validação Visual do UX Design - Protótipo RasterTech

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** O protótipo atual demonstra apenas componentes técnicos (AssetCard, SyncStatusIndicator, OfflineFormManager), mas não permite validar as jornadas de usuário definidas na especificação UX, impossibilitando avaliar se o design atende às necessidades reais dos 4 tipos de usuário.

**Approach:** Transformar o protótipo em uma validação visual completa do UX design, implementando autenticação, cadastros, SQLite e jornadas offline realistas conforme especificado na UX design specification.

## Boundaries & Constraints

**Always:**
- Seguir os 4 tipos de usuário definidos: João (Instalador), Carlos (Operador), Pedro (Motorista), Camila (Cadastradora)
- Implementar autenticação RBAC baseada nas permissões definidas no PRD
- Usar SQLite no navegador para persistência real (não localStorage)
- Simular jornadas offline realistas com sincronização em background
- Manter compatibilidade com Material Design 3 customizado
- Garantir responsividade mobile-first

**Ask First:**
- Mudanças na arquitetura de navegação atual
- Adição de bibliotecas externas além do sql.js
- Modificações no design visual dos componentes existentes

**Never:**
- Implementar backend real ou APIs
- Usar dados reais de usuários/clientes
- Alterar componentes existentes sem validação visual
- Ignorar padrões de acessibilidade definidos
- Quebrar funcionalidade existente do protótipo

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| HAPPY_PATH | Usuário acessa protótipo | Sistema solicita autenticação | Redirecionamento para login |
| LOGIN_SUCCESS | Credenciais válidas de técnico | Dashboard com permissões de técnico | N/A |
| LOGIN_INVALID | Credenciais incorretas | Mensagem de erro, retry habilitado | Tentativas limitadas (3) |
| OFFLINE_MODE | Rede indisponível durante uso | Interface mostra status offline, permite continuar | Dados salvos localmente, sincronização automática |
| CADASTRO_SUCCESS | Formulário completo de cliente | Cliente criado, vinculação possível | N/A |
| SYNC_CONFLICT | Dados locais conflitam com servidor | Interface mostra conflito, permite resolução | Merge manual ou override |
| SQLITE_CORRUPTION | Arquivo SQLite corrompido | Sistema recria schema, preserva dados possíveis | Backup automático, notificação de perda |

</frozen-after-approval>

## Code Map

- `demo/index.html` -- Estrutura HTML principal + novas páginas de login/cadastro
- `demo/js/app.js` -- Classe RastertechApp + autenticação + navegação RBAC
- `demo/js/components.js` -- AuthModal, RegistrationForm + componentes existentes
- `demo/js/mock-data.js` -- Dados mockados + schema de usuários
- `demo/js/db-manager.js` -- NOVO: SQLite wrapper + migração
- `demo/js/sync-queue.js` -- NOVO: Queue de sincronização offline
- `demo/css/styles.css` -- Estilos para login, cadastro, estados offline

## Tasks & Acceptance

**Execution:**
- [ ] `demo/js/db-manager.js` -- Criar DatabaseManager com SQLite + schema -- Substituir localStorage por persistência real
- [ ] `demo/js/sync-queue.js` -- Implementar SyncQueue com retry logic -- Simular sincronização offline realista
- [ ] `demo/js/components.js` -- Adicionar AuthModal e RegistrationForm -- Componentes para autenticação e cadastros
- [ ] `demo/js/mock-data.js` -- Expandir com usuários mockados (4 tipos) -- Dados para simulação de RBAC
- [ ] `demo/js/app.js` -- Implementar sistema de autenticação RBAC -- Controle de acesso baseado em roles
- [ ] `demo/index.html` -- Adicionar páginas de login e cadastro -- Telas visuais para validação UX
- [ ] `demo/css/styles.css` -- Estilizar componentes de auth/cadastro -- Manter consistência Material Design 3
- [ ] `demo/js/app.js` -- Integrar jornadas offline realistas -- Simular experiências de João/Carlos/Pedro

**Acceptance Criteria:**
- Given usuário não autenticado acessa protótipo, when abre aplicação, then vê tela de login com 4 tipos de usuário
- Given técnico logado, when navega dashboard, then vê apenas funcionalidades permitidas para seu role
- Given rede offline, when submete formulário, then dados salvos localmente e enfileirados para sync
- Given operador cadastra cliente, when preenche formulário, then cliente criado e vinculável a ativos
- Given dados em conflito, when reconecta rede, then interface permite resolução manual
- Given SQLite corrompido, when reinicia app, then sistema recria schema e preserva dados possíveis

## Design Notes

**Autenticação RBAC:**
- Modal de login mostra 4 avatares (João/Carlos/Pedro/Camila) com roles visuais
- Após login, header mostra usuário atual + role + botão logout
- Navegação bottom filtra abas baseado em permissões (ex: motorista não vê "Cadastro")

**SQLite Schema:**
```sql
-- Usuários com roles
CREATE TABLE users (
    id TEXT PRIMARY KEY,
    email TEXT UNIQUE,
    password_hash TEXT,
    role TEXT, -- 'technician', 'operator', 'driver', 'admin'
    name TEXT,
    status TEXT DEFAULT 'active',
    created_at INTEGER,
    synced INTEGER DEFAULT 0
);

-- Ativos com vinculações
CREATE TABLE assets (
    id TEXT PRIMARY KEY,
    plate TEXT,
    tracker_imei TEXT,
    chip_number TEXT,
    status TEXT, -- 'available', 'installed', 'maintenance', 'retiring'
    owner_id TEXT,
    created_at INTEGER,
    synced INTEGER DEFAULT 0
);

-- Queue de sincronização offline
CREATE TABLE sync_queue (
    id TEXT PRIMARY KEY,
    operation TEXT, -- 'create', 'update', 'delete'
    table_name TEXT,
    record_id TEXT,
    payload TEXT, -- JSON
    created_at INTEGER,
    retry_count INTEGER DEFAULT 0,
    status TEXT DEFAULT 'pending'
);
```

**Jornadas Offline:**
- João (técnico): simula instalação em subsolo → formulário O.S. offline → geração de hash → fila para WhatsApp
- Carlos (operador): dashboard de controle → investigação de desvios → soft-delete com justificativa
- Pedro (motorista): checklist diário → fotos obrigatórias → submissão offline

## Verification

**Commands:**
- `cd demo && python -m http.server 8000` -- expected: Servidor inicia sem erros
- `npm test` -- expected: Todos os testes de componentes passam (se existirem)

**Manual checks:**
- Login funciona com 4 tipos de usuário diferentes
- Cadastro cria usuários no SQLite e persiste após refresh
- Modo offline permite submissões e mostra status de sync
- Navegação RBAC oculta funcionalidades por role
- Interface responsiva funciona em mobile/desktop