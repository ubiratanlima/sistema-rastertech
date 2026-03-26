# 🛰️ RASTERTECH ENTERPRISE - LOG DE INCIDENTES E SOLUCOES
> **Documento de Auditoria Tecnica e Infraestrutura**
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

> **Status da Estabilizacao: CONCLUIDA (100%)**
> *Sistema blindado, estavel e pronto para a proxima fase de desenvolvimento.*
