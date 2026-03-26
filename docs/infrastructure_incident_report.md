# 🛰️ RELATÓRIO TÉCNICO: ESTABILIZAÇÃO DE INFRAESTRUTURA RASTERTECH
## 📅 Data: 26 de Março de 2026 | Versão: 1.0.2

Este documento cataloga os incidentes críticos ocorridos durante a fase de transição para o modelo de domínio dinâmico e as soluções permanentes aplicadas para garantir a portabilidade total do sistema.

---

### 🚨 INCIDENTE 01: O "Fantasma" do Domínio Rastertech-Web
**Sintoma:** Ao navegar na paginação de dispositivos, os links eram gerados como `http://rastertech-web/devices?page=2`, tornando o acesso impossível via navegador Windows (`localhost`).

*   **Causa Raiz:** O Laravel, dentro do contêiner Docker, assumia o nome do serviço (`rastertech-web`) como sua `APP_URL` padrão.
*   **Tentativa Falha:** Forçar `URL::forceRootUrl(http://localhost)` no `AppServiceProvider`. Isso resolveu a paginação, mas quebrou a comunicação de segurança do Supabase Auth.
*   **Solução Ecológica:** 
    1.  Uso de **Caminhos Relativos** (`/devices`) no Blade do Laravel.
    2.  Configuração de **Trusted Proxies** (`*`) no Middleware, permitindo que o Laravel descubra o domínio do navegador de forma orgânica e segura sem "mentir" para o sistema.

---

### 🚨 INCIDENTE 02: Loop de Reinicialização do Supabase Auth
**Sintoma:** O contêiner `supabase-auth` entrava em estado "Restarting" continuamente após mudanças no `.env`.

*   **Causa Raiz:** O serviço `GoTrue` (motor do Auth) usava a variável `${APP_URL}` para se auto-configurar. Ao mudar para `localhost`, o contêiner tentava se conectar a si mesmo internamente, gerando um erro fatal de validação de API.
*   **Solução Ecológica (Desacoplamento):**
    1.  Criação da variável interna **`APP_URL_SUPABASE`** no `.env`, apontando para a rede interna do Docker (`rastertech-web`).
    2.  Injeção direta dessa variável no `docker-compose.yml`, separando a identidade visual do sistema (`localhost`) da identidade técnica interna dos contêineres.

---

### 🚨 INCIDENTE 03: Erro Fatal de Migração (UUID vs TEXT)
**Sintoma:** O LOG do Supabase apresentava o erro: `ERROR: operator does not exist: uuid = text (SQLSTATE 42883)` e o serviço recusava o "Power On".

*   **Causa Raiz:** Incompatibilidade entre migrations legadas da imagem Supabase e o **Postgres 15+**. O Postgres novo exige que comparações entre IDs (UUID) e Texto sejam explícitas.
*   **Solução Ecológica (DNA do Banco):**
    1.  Criação de um **Patch de Compatibilidade** (`CREATE CAST`) no script de inicialização do banco de dados (`zz-setup-embraet.sql`).
    2.  Isso garante que qualquer instalação virgem do sistema (na sua VPS ou em outro PC) já nasça com a "tradução" automática de tipos habilitada, eliminando a dependência de intervenção manual.

---

### 🏆 RESUMO DAS MELHORES PRÁTICAS APRENDIDAS:
1.  **Nunca "Chumbar" (Hardcode)**: Endereços de rede devem ser desacoplados entre o que o usuário vê (Acesso Público) e o que o servidor fala internamente (Acesso Técnico).
2.  **DNA da Infraestrutura**: Correções estruturais de banco de dados devem morar em arquivos `.sql` de setup, não apenas em comandos manuais de terminal que se perdem no tempo.
3.  **Logs são a Única Verdade**: Sem a leitura do diário de erros do Docker, o diagnóstico vira adivinhação.

---
> **Estado Atual do Projeto:** 🟢 ESTÁVEL | 🚀 PRONTO PARA PRODUÇÃO
