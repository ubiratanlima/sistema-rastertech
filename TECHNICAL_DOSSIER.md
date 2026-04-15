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

### 5.1 Doutrina da Neutralidade de Infraestrutura (Port 8000 Conflict Resolution)
**DIRETIVA OBRIGATÓRIA**: Para evitar o "Drift de URL" causado pelo mapeamento de portas (ex: Kong na Porta 8000 vs Browser), é **proibido** o uso de `route()` ou `url()` para assets/links internos. 
- Use exclusivamente **Caminhos Relativos** (Ex: `/devices`). 
- Todos os paginadores devem injetar `->withPath('/devices')` para neutralizar a raiz da URL.

### 5.2 Padrão Universal de Listagens 1.0 (The Golden Layout)
**DIRETIVA OBRIGATÓRIA**: Todas as listagens táticas devem seguir milimetricamente este Blueprint:
- **Títulos (H1)**: `text-bold`, Tamanho `2.2rem` (Desktop) / `1.55rem` (Mobile). Texto secundário em `text-muted`.
- **Card Principal**: `card-outline card-primary` com `border-0`. Header em `bg-transparent px-4 py-3`.
- **Card Title**: `text-bold`, Tamanho `1.1rem`.
- **Botões de Ação Superior**: Altura fixa de **31px** (`btn-sm`). Seletor de visão com `ml-5`.
- **Anatomia da Tabela**:
    - **Header**: `text-uppercase font-weight-bold`, Background `rgba(0,0,0,0.02)`.
    - **Células ID**: `text-muted`, centralizado, small text, **sem negrito**.
    - **Identificador Principal (IMEI/Chip)**: `text-primary`, **font-weight-bold**, centralizado.
    - **Conetividade (SIM/ICCID)**: `text-pink`, **font-weight-bold**, layout pixel-perfect.
    - **Células de Metadados (Modelo/Cliente)**: `text-dark`, peso normal, alinhamento centralizado.
    - **Ações**: Coluna sempre centralizada (`text-center`). Grupo unificado (`btn-group`) com ícones `fa-lg`.
- **Doutrina de Dados (Data Casting Standard)**:
    - **Casting Mandatário**: Campos de data não nativos (ex: `cancelled_at`) devem ser obrigatoriamente definidos em `protected $casts` no Modelo Eloquent como `datetime`.
    - **Engenharia vs Remendo**: Proibido usar `Carbon::parse()` ou formatação manual de strings na View. O dado deve chegar ao Blade já como um objeto funcional (Carbon).
- **Protocolo de Botões e Ações (The Action Suite)**:
    - **👁️ Dossiê (Info/Cyan)**: Classe `text-info`. Acesso a metadados e histórico técnico.
    - **🛠️ Gestão (Warning/Yellow)**: Classe `text-warning`. Edição de hardware, IMEI e vínculos.
    - **🚫 Power (Danger/Red)**: Classe `text-danger`. Gatilho de inativação (Soft-Delete) com auditoria.
    - **♻️ Undo (Success/Green)**: Classe `text-success`. Restauração de ativos da lixeira.
- **Ordenação Tática (Sorting)**:
    - **Links**: Uso de `request()->fullUrlWithQuery()` para manter filtros ativos.
    - **Dados Numéricos (ID)**: Ícones `fa-sort-numeric-down` / `fa-sort-numeric-up-alt`.
    - **Dados Textuais (Strings)**: Ícones `fa-sort-alpha-down` / `fa-sort-alpha-up-alt`.
    - **Estado Inativo**: Ícone `fa-sort-amount-down text-muted` para colunas não ordenadas.
- **Componentes**: Placa Mercosul estilizada para identificação de veículos.

### 5.3 Protocolo de Auditoria e Integridade
**DIRETIVA OBRIGATÓRIA**:
- Ações destrutivas (Inativação/Cancelamento) exigem justificativa (mínimo 5 caracteres).
- Desvínculos de hardware/veículo exigem "Motivo da Desinstalação".
- Uso de `whereDoesntHave('relação')` em vez de `whereNull` para busca relacional reversa.
- Consultas de inventário SEMPRE com `withTrashed()`.

### 5.4 Protocolo de Comando Docker
Para execuções `artisan`, o comando padrão e único é:
`docker exec -it rastertech-app php artisan [comando]`

---

## 6. Log de Evolução de Aprendizado (AI Memory)
1. **[2026-03-28]** Drift de URL (Port 8000): Identificado que o helper `route()` gera URLs absolutas que quebram em ambientes com Gateway Kong (Porta 8000). Solução: Caminhos Relativos.
2. **[2026-03-28]** Drift de Validação: Resolvido o erro de booleano no AJAX enviando `1/0`.
3. **[2026-03-28]** Integridade Rigorosa: Identificada a restrição `NOT NULL` do banco para `customer_id`.
4. **[2026-03-28]** Drift Temporário (String Casting): Resolvida falha de execução de `format()` em campos de data não nativos através do uso de `protected $casts` no modelo.

| Data | Incidente | Solução Técnica |
| :--- | :--- | :--- |
| 28/03/26 | String Casting Failure | Implementação mandatória de `protected $casts` no modelo `Device`. |
| 28/03/26 | URL/Route Drift (Port 8000) | Abandono de `route()` em favor de caminhos relativos em todo o ecossistema. |
| 28/03/26 | Not null violation (Customer) | Mapeamento do "Estoque Geral" para ID real de cliente. |
| 28/03/26 | Boolean Validation Drift | Normalização de sinal de desvínculo para Inteiro (1/0) no JS. |
| 28/03/26 | Class Not Found (Controller) | Importação manual de modelos ausentes. |
| 28/03/26 | Invisible Stock Chips | Implementado Filtro Tri-Estado no seletor de visão. |
| 28/03/26 | Loss of Audit Context | Implementado Draft Persistence via `localStorage`. |
---
**Status**: Fase 1 (Arquitetura & Infraestrutura) - CONCLUÍDA.
**Status de Estabilidade**: BLINDAGEM DE PORTA E REGISTROS ATIVA.
**Autor**: Antigravity AI (Pair Programming com Ubiratan).

---

## 7. Log de Sessão — 15/04/2026 (Módulo de Missões)

> Documentação detalhada em: `MISSOES_JORNADAS_DOSSIER.md`

### Bugs Resolvidos:

| Data | Bug | Arquivo | Solução |
| :--- | :--- | :--- | :--- |
| 15/04/26 | Checkout não fechava a missão (EM CAMPO persistia) | `CustomerPortalController` | Removido filtro `customer_id` da query de missão aberta — `vehicle_id + status='open'` é suficiente |
| 15/04/26 | Supervisor no checkout trocava o veículo selecionado | `CustomerPortalController` | Adicionado `vehicle_id` da request como filtro prioritário em `createChecklist()` |
| 15/04/26 | Coluna DESLOCAMENTO mostrava "EM CAMPO" em baixas administrativas | `verificacoes/index.blade.php` | Adicionado `@elseif($mission->status === 'closed')` para missões sem entry_id |

### Features Adicionadas:

| Data | Feature | Arquivo |
| :--- | :--- | :--- |
| 15/04/26 | Navegação bidirecional check-in ↔ checkout na tela de detalhes | `verificacoes/show.blade.php` |
| 15/04/26 | Badge "JORNADA FINALIZADA" clicável (volta para listagem) | `verificacoes/show.blade.php` |

### Incidente de Dados Resolvido:
- Missão #7 (fantasma, RTH-2028) e Checklist #9 (checkout errado) foram deletados via tinker
- Estado do banco restaurado: RTH-2026 (Missão #5) e RTH-2028 (Missão #6) — ambas closed ✅

