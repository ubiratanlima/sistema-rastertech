# 🛰️ RASTERTECH ENTERPRISE - LOG DE INCIDENTES E SOLUCOES
> **Documento de Auditoria Tecnica e Infraestrutura**
> 
---

**Status Final do Módulo**: ✅ **HOMOLOGADO**
- Registro Massivo/Single: Funcional
- Recuperação de Estado (LocalStorage): Ativo
- Gestão de Fornecedores In-Wizard: Operacional
- Segurança (PIN/PUK): Migrado e Integrado ao Eloquent
- Roteamento: POST /sim-cards normalizado
> *Data: 26 de Marco de 2026*

---

## 1. INCIDENTE: Exposicao Cibernetica e Vicio da Porta 8000
### 🚩 Problema:
O sistema permitia acesso direto via `http://localhost:8000`, expondo a arvore de diretorios raiz do projeto (Vulnerabilidade de Directory Listing) e gerando links incorretos no menu.

### 🔍 Causa Raiz:
Existiam processos "zumbis" rodando no sistema operacional Windows (servidores locais persistentes) que sequestraram a porta 8000 e ignoravam as regras de seguranca do Docker.

### ✅ Solucao:
1. **Identificacao**: Uso do comando `netstat -ano | findstr :8000` para localizar os PIDs (Process IDs) invasores.
2. **Neutralizacao**: Execucao do comando `taskkill /F /PID [NUMERO]` no PowerShell para encerrar os processos zumbis.
3. **Blindagem**: Remocao de qualquer mapeamento de porta 8000 no `docker-compose.yml`, forcando o trafego exclusivamente pela Porta 80 segura (Gateway Kong).

---

## 2. INCIDENTE: Erro 404 - "Nothing matches the given URI"
### 🚩 Problema:
Ao clicar nos menus (Clientes, Chips), o Gateway Kong retornava erro 404, mesmo com as rotas existindo no Laravel.

### 🔍 Causa Raiz:
O Gateway estava configurado com o parametro `strip_path: true` por padrao, o que fazia com que ele "limpasse" a URL (removendo o /sim-cards) antes de entregar o pedido ao Laravel. O Laravel recebia apenas um pedido vazio (`/`) e voltava ao Dashboard.

### ✅ Solucao:
1. **Configuracao de Proxy**: Alteracao do `kong.yml` definindo `strip_path: false`.
2. **Atualizacao de Rotas**: Adicao explicita dos novos caminhos (`/sim-cards`, `/customers`, `/fleets`) na lista de autorizacao do Gateway.

---

## 3. INCIDENTE: Links de Paginacao com Porta Incorreta
### 🚩 Problema:
Mesmo navegando via Porta 80, os botoes de proxima pagina (1, 2, 3...) continuavam gerando links com `:8000`.

### 🔍 Causa Raiz:
O motor de paginacao do Laravel (`->links()`) capturava a porta interna do Docker em vez da porta externa mapeada, ignorando a configuracao global da aplicacao.

### ✅ Solucao:
1. **Doutrinacao de Provider**: Injecao da regra `URL::forceRootUrl` no `AppServiceProvider.php`.
2. **Caminhos Relativos Forcados**: Aplicacao do metodo `->withPath('/caminho-da-tela')` nos controladores, garantindo que a paginacao utilize apenas caminhos relativos ao dominio atual do usuario.

---

## 4. INCIDENTE: SQLSTATE[42703] - Erros de Coluna no Banco
### 🚩 Problema:
Telas de Clientes e Frotas travavam com erro de banco de dados ao tentar exibir os registros.

### 🔍 Causa Raiz:
Divergencia tecnica: o codigo buscava colunas (`email`, `status`, `number`) que possuiam nomes diferentes ou nao existiam na migracao unificada do banco de dados.

### ✅ Solucao:
1. **Sincronizacao de Schema**: Ajuste cirurgico nos Controladores para buscar apenas colunas reais (`document`, `name`).
2. **Padronizacao de Campo**: Nomeacao tecnica de `number` para `phone_number` no vinculo de Chips e Veiculos.

---

---

## 5. INCIDENTE: Interface de Acoes e Fluxo de Inativacao (Chips)
### 🚩 Problema:
Os botoes de acao (Visualizar, Editar, Inativar) foram inicialmente unificados em um unico "Dossie Tatico", o que gerou lentidao operacional e confusao na experiencia do usuario, que prefere acesso direto as acoes manuais.

### 🔍 Causa Raiz:
Tentativa de "Super-Modernizacao"| 28/03/26 | Erro de Comando (Docker Drift) | Tentativa de uso de docker-compose em vez de docker exec | **REGISTRADO NA MEMÓRIA**: Usar sempre `docker exec -it rastertech-app php artisan` |
| 28/03/26 | HTTP 405 Method Not Supported | Rota `POST /sim-cards` não definida no `web.php` | Adicionado `Route::post('/sim-cards', ...)` ao arquivo de rotas |
| 28/03/26 | UX UI (+ no meio da lista) | Botão de adição aparecia em todas as linhas massivas | Ajustado `updateUI()` para exibir apenas em `.gsm-row:last` |
1. **Restauracao de Coluna Tripla**: Retorno ao layout de 3 botoes individuais (Olho para Dossie, Ferramentas para Edicao, Power para Inativacao).
2. **Modularizacao de Logica**: Separacao completa das funcoes de visualizacao tática (`openTacticalDossier`) e edicao manual (`openEditFormManual`), permitindo rapidez no ajuste de dados.
3. **Inativacao Silenciosa (AJAX)**: Implementacao de exclusao em segundo plano para evitar recarregamento de pagina.
4. **Painel de Atencao Master**: Criacao de um alerta personalizado via SweetAlert2 (Fundo amarelo suave, texto preto e Triangulo de Atencao FA5) para exibir mensagens de bloqueio (ex: chips vinculados a rastreadores) de forma clara e profissional.

## 6. INCIDENTE: Falha de Persistência na Edição Tática (AJAX)
### 🚩 Problema:
Ao tentar editar os dados de um chip (Telefone, Operadora, Status), o formulário não salvava as alterações no banco de dados.

### 🔍 Causa Raiz:
1. **Conflito de Porta (8000)**: Instabilidade no `fetch` nativo devido ao redirecionamento forçado de domínio no ambiente local.
2. **Crash de View**: Vulnerabilidade de acesso a propriedade `role` em objeto nulo quando a sessão de usuário expirava.

### ✅ Solução:
1. **Frontend**: Migração para `$.ajax` (jQuery) com `_token` explícito, garantindo envio via `PUT` nativo no formato `json`.
2. **Backend**: Implementação de logging (`Log::info`) no `update` para auditoria e tratativa de erros com JSON estruturado.
3. **UX**: Inclusão do ícone de Triângulo de Alerta tático no título do modal, conforme solicitado.

---

# 🛡️ INCIDENTE: CHIPS INATIVADOS "DESAPARECIDOS" (ID 495)
### 🚩 Problema:
Após a inativação tática (Soft Delete), os chips sumiam tanto da visão de "Ativos" quanto da visão de "Inativos". Isso ocorria porque o escopo global do Laravel ocultava os registros deletados das consultas normais, tornando-os invisíveis para o filtro binário.

### ✅ Solução (Motor de Consulta Universal):
A estrutura de consulta foi re-arquitetada para **neutralizar o escopo automático** e dar controle total ao controlador:

1.  **withTrashed() como Base**: A consulta agora inicia com `GsmCard::withTrashed()`, removendo o "filtro de invisibilidade" do SoftDelete no início do builder.
2.  **Filtro Manual de Visão**: 
    - No modo **Ativo**, forçamos `whereNull('deleted_at')` + `where('status', 'active')`.
    - No modo **Inativo**, forçamos `(status != active OR deleted_at is not null)`.
3.  **Busca Multi-DADOS**: Expandi a busca para incluir `ICCID` e `Número da Linha` além do nome do cliente, garantindo que o chip possa ser encontrado mesmo quando desvinculado de um dispositivo.
4.  **Ambiguidade Zero**: Prefixamos todas as colunas de ordenação e filtros (ex: `gsm_cards.status`) para evitar colisões silenciosas com tabelas de dispositivos ou clientes nos Joins.

> **Status da Estabilização: BLINDADO (100%)**
> *Agora, chips inativados aparecem com o selo vermelho "INATIVADO" e podem ser restaurados instantaneamente.*

---

## 🏗️ LOG DE EVOLUÇÃO E CORREÇÕES (28/03/2026)

### 🚩 Incidente: Bug da Ordenação Unidirecional (Single Click)
- **Problema**: O clique nas colunas da tabela (`ID`, `ICCID`, etc.) só mudava para `ASC` na primeira vez. Cliques subsequentes não invertiam para `DESC`.
- **Causa**: Uso do operador de união de arrays (`+`) no PHP. Se a chave `sort` ou `direction` já existisse na URL, o operador `+` ignorava os novos valores.
- **Solução**: Substituído por `array_merge(request()->query(), [...])`. A função `merge` força a sobreposição dos parâmetros, garantindo a alternância correta entre ASC/DESC.

### 📦 Melhoria: Filtro de Visão Tri-Estado (Ativo/Estoque/Lixeira)
- **Necessidade**: Chips cadastrados como "Estoque" (status `inactive`) não eram visíveis nos filtros binários anteriores.
- **Solução**: Implementado um `select` de visão (`view`) no cabeçalho:
    - `🟢 ATIVOS`: `status = active` & `deleted_at = null`.
    - `📦 ESTOQUE`: `status != active` & `deleted_at = null`.
    - `🔴 INATIVOS`: `deleted_at != null` (SoftDeletes).
- **Impacto**: Visibilidade total do inventário, distinguindo ativos operacionais de ativos em estoque.

### 🛡️ Incidente: Regressão de JS em Edições Incrementais (Botões Pararam)
- **Problema**: Após edições consecutivas, os botões de ação (Olho, Ferramentas, Power) pararam de funcionar.
- **Causa**: Acúmulo de funções duplicadas e erros de sintaxe (parênteses não fechados) no bloco de `<script>` causados por substituições parciais mal-sucedidas.
| 28/03/26 | Botões de Ação Pararam (Regressão JS) | Acúmulo de duplicidades e erros de sintaxe em scripts | Restauração Estrutural e Unificação de Scripts no Blade |
| 28/03/26 | Erro Interno (Check Violation) | Constraint `gsm_cards_status_check` bloqueando o status `canceled` | Nova Migração + Seeder para liberar status `canceled` no PGSQL |
| 28/03/26 | Coluna de Cliente Inexistente | Tabela `gsm_cards` original não possuía vínculo direto com Clientes | Adição da coluna `customer_id` via Migração/Seeder e Integração Eloquent |
| 28/03/26 | Perda de Dados (Cancelamento) | Fechamento acidental do modal limpava o motivo digitado | Implementado motor de **Draft Persistence** via `localStorage` |
- **Solução**: Realizada uma **Restauração Estrutural** do JavaScript. Removidas as triplicatas do Wizard de Cadastro e unificadas todas as funções globais em um bloco `@push('scripts')` limpo e validado.
- **Aprendizado**: Para arquivos Blade grandes com muitos scripts, prefira substituir o bloco de script inteiro em vez de fazer edições parciais repetitivas para evitar desalinhamento de chaves `{}`.

---

## 7. INCIDENTE: Conflito de Cache de Sessão em Ambientes Multi-Tenant
### 🚩 Problema:
Usuários logados em diferentes instâncias de clientes recebiam dados de outros clientes após o logout/login rápido.
### 🔍 Causa Raiz:
O driver de sessão `file` estava compartilhando o mesmo prefixo de arquivo no volume Docker persistente.
### ✅ Solução:
1. **Isolamento**: Alterado `SESSION_DRIVER` para `redis` com prefixo dinâmico baseado no `APP_NAME`.
2. **Limpeza**: Adicionado comando `php artisan session:clear` no script de deploy.

---

## 8. INCIDENTE: SQLSTATE[23514] - Violação de CHECK Constraint (Status 'canceled')
### 🚩 Problema:
O sistema retornava "Erro interno ao salvar dados" (500) ao tentar mudar o status de um chip para `canceled`, mesmo com a coluna existindo e o Eloquent configurado.

### 🔍 Causa Raiz:
A tabela `gsm_cards` no PostgreSQL possui uma trava de integridade nativa (`CHECK CONSTRAINT`) que restringia a coluna `status` apenas aos valores `active` e `inactive`. Qualquer valor novo injetado via código (como `canceled`) era bloqueado pelo banco de dados antes mesmo da gravação.

### ✅ Solução:
1. **Identificação**: Análise do `DETAIL` no `laravel.log` que apontava para `violates check constraint "gsm_cards_status_check"`.
2. **Correção de Schema**: Criação de migração para dropar a restrição antiga e criar uma nova incluindo o status `canceled`.
3. **Bypass de Emergência**: Uso de `db:seed` com o `StatusConstraintSeeder` para forçar a atualização em ambientes onde o `migrate` padrão falha por volume do Docker.

---

# 🛡️ DIRETIVA DE ANÁLISE OBRIGATÓRIA: CHECK CONSTRAINTS (DB-LAYER)
### 📜 Regra Mandatória:
Sempre que for adicionado um novo estado (status) a uma coluna existente em tabelas legadas ou consolidadas do PostgreSQL:
1. **Investigar Constraints**: Verificar se a coluna possui uma `CHECK CONSTRAINT`.
2. **Migração de Alinhamento**: Se houver restrição, a migração **DEVE** explicitamente dropar e recriar a constraint com os novos valores.
3. **Validação**: Garantir que o `in:active,inactive,...` do Laravel esteja espelhado no banco.

---

# 🛡️ DOUTRINA DA NEUTRALIDADE DE INFRAESTRUTURA (OBRIGATÓRIO)
### 🚩 Problema Recorrente:
O uso de auxiliares do Laravel como `route()` ou `pagination()` gera URLs absolutas baseadas no `APP_URL` do `.env`. Isso faz com que a porta `:8000` (comum em ambientes locais como WSL/Docker) seja removida dos links, quebrando a navegação.

### 📜 Diretiva Mandatória:
A partir de agora, **todas as intervenções de código** devem seguir os seguintes pilares de neutralidade:

1.  **Caminhos Relativos no Blade**: Nunca utilizar `route()` ou `url()` para links internos ou ações de formulário. Use caminhos relativos diretos (ex: `action="/sim-cards"` ao invés de `action="{{ route('sim-cards.index') }}"`).
2.  **Query-Strings Neutras**: Para filtros, ordenação e toggles, utilize caminhos relativos que preservem a URI atual (ex: `href="?status=inactive"`).
3.  **Paginação Localizada**: Todo método `paginate()` no controlador **DEVE** conter obrigatoriamente um `->withPath('/caminho-da-tela')` para neutralizar a geração de URLs absolutas.
4.  **Check de Pré-Voo**: Antes de finalizar qualquer tarefa que envolva links, submissões ou navegação, verifique se não há vazamento de porta `:8000` ou uso de `route()`.

> **Status da Diretiva: ATIVA E MANDATÓRIA (100%)**
> *Compromisso com o funcionamento blindado e profissional em qualquer porta ou ambiente.*
