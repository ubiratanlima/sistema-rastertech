---
stepsCompleted: [1, 2, 3, 4, 5, 6, 7, 8]
workflowType: 'architecture'
lastStep: 8
status: 'complete'
completedAt: '2026-03-24'
inputDocuments: ['_evo-output/planning-artifacts/sistema-completo/prd.md', '_evo-output/planning-artifacts/sistema-completo/ux-design-specification.md', '_evo-output/planning-artifacts/sistema-completo/product-brief-sistema-rastertech.md']
project_name: 'sistema-rastertech'
user_name: 'Ubiratan'
---

# Architecture Decision Document - sistema-rastertech

_This document builds collaboratively through step-by-step discovery. Sections are appended as we work through each architectural decision together._

## Project Context Analysis (V2)

### Requirements Overview

**Functional Requirements:**
Análise de 28 REs operativas focadas no ciclo de vida de ativos e operações de campo. A arquitetura deve suportar o estado "Disconnected" como padrão para o PWA e "High Availability" para a Matriz, utilizando o ecossistema Laravel para lógica e Supabase para dados.

**Non-Functional Requirements:**
*   **Stack Principal:** Laravel 10+, PHP 8.2-FPM e Supabase (Self-hosted).
*   **UI/UX Pattern:** Estética **AdminLTE** para painéis administrativos e PWA.
*   **Storage:** Gestão de fotos e documentos via Supabase Storage local com políticas de segurança.
*   **Infra:** Orquestração Docker-Compose completa (Nginx, Redis, PostgreSQL, GoTrue, PostgREST).
*   **Auditabilidade:** Trilha de auditoria baseada em triggers SQL e logs forenses.

**Scale & Complexity:**
*   **Primary domain:** Full-stack (Laravel + Supabase + PWA)
*   **Complexity level:** Alta (Sincronismo Offline Realtime + Auditoria Jurídica)
*   **Estimated architectural components:** ~15 (Containers do Supabase, API Laravel, Sync Engine, Redis, Nginx).

### Technical Constraints & Dependencies

*   **Docker Base:** Inspirado no projeto `AXIS`, mas expandido para o stack BaaS do Supabase (Self-hosted em Ubuntu 22.04).
*   **Base AdminLTE:** Integração do Laravel Blade com o layout clássico do AdminLTE para máxima produtividade administrativa.
*   **Resiliência:** Filas (Queues) rodando em containers separados para processamento de fotos e alertas críticos.

### Cross-Cutting Concerns Identified

*   **Auditoria Forense SQL:** Uso das capacidades nativas do Postgres para rastrear mudanças históricas de hardware de forma imutável.
*   **Segurança de Mídia:** Fotos assinadas com HMAC e protegidas por RLS no Supabase Storage.
*   **Integridade Referencial:** Vínculos rígidos entre Chips, Rastreadores e Veículos para evitar "ativos fantasmas".
