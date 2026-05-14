# 🚨 Runbook: Recuperação de Produção — Rastertech / Portainer / Docker Swarm

**Data do Incidente:** 13/05/2026  
**Sistema:** `rastertech.embraet.com.br`  
**Infraestrutura:** Docker Swarm gerenciado pelo Portainer, proxy reverso Traefik

---

## 🏗️ Arquitetura da Produção

```
Internet → Traefik (proxy reverso + SSL) → Kong (API Gateway) → Nginx (sidecar) → Laravel (PHP-FPM)
                                                                                  ↕
                                                                            PostgreSQL
```

**Rede Docker:** `EmbraNet` (rede externa, gerenciada pelo Traefik)  
**Arquivo de deploy:** `docker-compose.yml` (raiz do repositório)  
**Arquivo local (dev):** `docker-compose.local.yml` ← **NUNCA alterar este para produção**

---

## 🔴 Causa Raiz do Incidente

A instrução `build:` foi removida do `docker-compose.yml` de produção em algum momento anterior, fazendo com que o Portainer rodasse uma **imagem congelada** (antiga) mesmo após atualizações de código.

Ao tentar corrigir isso, foram feitas múltiplas alterações no `docker-compose.yml` que quebraram progressivamente a infraestrutura:

1. Substituição de `configs` do Swarm por `volumes` locais → quebrou o Nginx (name resolution failed)
2. Troca de rede `EmbraNet` por `EmbraNetV2` → desconectou o Traefik (404)
3. Deleção da Stack no Portainer → apagou as variáveis de ambiente (500 / postgres sem senha)

---

## 🔵 Sequência de Erros e Significados

| Erro | O que significa |
|------|----------------|
| `name resolution failed` | Container não encontra outro container na rede. Causa: rede errada no compose, ou `configs` Swarm corrompidos por update parcial |
| `404 page not found` | Traefik recebe a requisição mas não tem rota configurada. Causa: `deploy.labels` ausentes no compose, ou rede errada nas labels |
| `500 Server Error` | Infraestrutura funcionando, mas Laravel falhou. Causa: cache desatualizado, variáveis ausentes, ou DB inacessível |
| `POSTGRES_PASSWORD not specified` | Variáveis de ambiente não foram passadas ao container. Causa: Stack deletada/recriada sem as env vars |

---

## ✅ Solução Aplicada — Passo a Passo

### Etapa 1: Identificar o arquivo correto de referência

O arquivo correto de produção estava no commit:
```
1804bd1c9d9d01f44f42bedcf80aa64ff59c73a9
```
Mensagem: `fix: versao estavel do ambiente local via docker compose cli`

Para extrair o arquivo de um commit específico:
```bash
git show 1804bd1c:docker-compose.yml > docker-compose.yml
```

### Etapa 2: Restaurar o docker-compose.yml correto

O arquivo de produção correto deve conter obrigatoriamente:

```yaml
networks:
  EmbraNet:
    external: true   # ← OBRIGATÓRIO: mesma rede do Traefik

configs:             # ← OBRIGATÓRIO: uso de configs do Swarm (não volumes locais)
  kong_config:
    file: ./docker/volumes/db/kong.yml
  nginx_config_v2:
    file: ./docker/volumes/web/nginx.conf

# No serviço rastertech-kong:
deploy:
  labels:            # ← OBRIGATÓRIO: sem isso o Traefik retorna 404
    - "traefik.enable=true"
    - "traefik.docker.network=EmbraNet"
    - "traefik.http.routers.rastertech.rule=Host(`rastertech.embraet.com.br`)"
    - "traefik.http.routers.rastertech.entrypoints=websecure"
    - "traefik.http.routers.rastertech.rule=Host(`rastertech.embraet.com.br`)"
    - "traefik.http.routers.rastertech.tls=true"
    - "traefik.http.routers.rastertech.tls.certresolver=letsencryptresolver"
    - "traefik.http.services.rastertech.loadbalancer.passHostHeader=true"
    - "traefik.http.services.rastertech.loadbalancer.server.port=8000"
```

### Etapa 3: Commit e push do arquivo correto

```bash
git add docker-compose.yml
git commit -m "fix: restauracao do docker-compose.yml ao estado estavel"
git push
```

### Etapa 4: Recriar a Stack no Portainer

> ⚠️ **Só delete a Stack se o Update não resolver.** Deletar apaga as variáveis de ambiente.

**Se fizer Update (preferido):**
1. Portainer → Stacks → rastertech → Editor
2. Marcar **Re-pull image and redeploy**
3. Clicar **Update the stack**

**Se precisar deletar e recriar:**
1. Portainer → Stacks → rastertech → **Delete this stack**
2. Aguardar 30 segundos
3. Portainer → Stacks → **+ Add stack**
4. Nome: `rastertech`, Build method: Repository
5. URL do Git, branch `main`, Compose path: `docker-compose.yml`
6. **OBRIGATÓRIO:** Adicionar todas as variáveis de ambiente (ver Etapa 5)
7. **Deploy the stack**

### Etapa 5: Variáveis de ambiente obrigatórias no Portainer

> As variáveis abaixo DEVEM estar configuradas na Stack. Elas se perdem ao deletar a Stack.

```
APP_NAME=Sistema-Rastertech
APP_ENV=production
APP_KEY=<base64:...>
APP_DEBUG=false
APP_URL=https://rastertech.embraet.com.br
APP_URL_SUPABASE=https://rastertech.embraet.com.br
DB_CONNECTION=pgsql
DB_HOST=rastertech-db
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=<senha>
JWT_SECRET=<secret>
KONG_USER=<usuario>
KONG_PASS=<senha>
TRUSTED_PROXIES=*
```

> 💡 **Dica:** Salve essas variáveis em um gerenciador de senhas seguro fora do servidor.

### Etapa 6: Limpar cache do Laravel após restauração

Se o sistema subir com erro 500 após restauração, acesse o container da app pelo Portainer:

1. Portainer → Containers → `rastertech_rastertech-app.*`
2. Clique no container → **>_ Console** → Connect
3. Execute:
```bash
php artisan optimize:clear && php artisan optimize
```

---

## 🟢 Procedimento Normal de Atualização (sem incidente)

Para atualizar o sistema normalmente após mudanças de código:

```bash
# 1. No terminal local
git add .
git commit -m "feat: descricao da mudança"
git push
```

```
# 2. No Portainer
Stacks → rastertech → Editor → marcar "Re-pull image and redeploy" → Update the stack
```

**Os dados do banco NÃO são perdidos** — o volume `rastertech-db-data` é persistente e independente da Stack.

---

## ❌ O Que NUNCA Fazer

- **Nunca** remover a seção `configs:` do `docker-compose.yml` e substituir por `volumes` locais (`.:/var/www`) — isso quebra o Nginx no Swarm
- **Nunca** trocar `EmbraNet` por outra rede — isso desconecta o Traefik
- **Nunca** adicionar `build:` ao `docker-compose.yml` de produção sem um pipeline CI/CD configurado — o Portainer não consegue compilar a imagem
- **Nunca** alterar o `docker-compose.yml` de produção sem testar em homologação
- **Nunca** deletar a Stack sem antes exportar/salvar as variáveis de ambiente

---

## 📁 Arquivos Críticos

| Arquivo | Função |
|---------|--------|
| `docker-compose.yml` | Deploy de **produção** via Portainer |
| `docker-compose.local.yml` | Deploy de **desenvolvimento** local |
| `docker/volumes/db/kong.yml` | Configuração de rotas do Kong |
| `docker/volumes/web/nginx.conf` | Configuração do Nginx (sidecar) |
| `app/Providers/AppServiceProvider.php` | Forçar HTTPS em produção |
| `.env` | Variáveis locais (nunca vai ao Git) |

---

*Documento gerado em 13/05/2026 após incidente de produção.*
