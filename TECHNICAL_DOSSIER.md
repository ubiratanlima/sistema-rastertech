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

---
**Status**: Fase 1 (Arquitetura & Infraestrutura) - CONCLUÍDA.
**Autor**: Antigravity AI (Pair Programming com Ubiratan).
