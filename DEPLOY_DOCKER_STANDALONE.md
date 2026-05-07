# 🐳 Guia de Deploy: Docker Standalone (Local ou VPS Direto)

Este guia descreve como subir o sistema usando apenas o terminal, sem depender da interface do Portainer.

## 1. Requisitos Iniciais
Certifique-se de que a rede global do sistema existe:
```bash
docker network create EmbraNet
```

## 2. Subindo o Sistema do Zero
Navegue até a pasta do projeto e execute:
```bash
docker-compose up -d --build
```
*O `--build` garante que o Docker leia seu código local e crie a imagem atualizada.*

## 3. Comandos de Manutenção Frequentes

### Entrar no terminal do App:
```bash
docker exec -it $(docker ps -qf "name=rastertech-app") bash
```

### Atualizar Banco de Dados (Migrations):
```bash
docker exec -it $(docker ps -qf "name=rastertech-app") php artisan migrate --force
```

### Limpar Cache e Otimizar:
```bash
docker exec -it $(docker ps -qf "name=rastertech-app") php artisan optimize
```

### Ver Logs em Tempo Real:
```bash
docker-compose logs -f rastertech-app
```

## 4. Atualização de Código (Git)
Se você alterou arquivos e quer que o Docker reflita isso:
1. `git pull origin main`
2. `docker-compose up -d --build` (Para reconstruir a imagem com o código novo)
3. `docker exec -it $(docker ps -qf "name=rastertech-app") php artisan migrate --force`

---
*Rastertech Command Center - Operações Standalone.*
