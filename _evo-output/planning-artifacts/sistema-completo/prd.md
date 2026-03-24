---
stepsCompleted: ['step-01-init', 'step-02-discovery', 'step-02b-vision', 'step-02c-executive-summary', 'step-03-success', 'step-04-journeys', 'step-05-domain', 'step-06-innovation', 'step-07-project-type', 'step-08-scoping', 'step-09-functional', 'step-10-nonfunctional', 'step-11-polish']
inputDocuments: ['_evo-output/planning-artifacts/sistema-completo/product-brief-sistema-rastertech.md']
classification:
  projectType: 'SaaS B2B Integrado / PWA'
  domain: 'Logística, Automotive & IoT'
  complexity: 'Alta'
  projectContext: 'Greenfield'
workflowType: 'prd'
---

# Product Requirements Document - sistema-rastertech

**Author:** Ubiratan
**Date:** 2026-03-23

## Executive Summary

O **sistema-rastertech** é uma plataforma de gestão B2B/B2C para empresas de rastreamento veicular. A plataforma erradica planilhas e sistemas herdados interligando a Matriz (Gestores, Operadores e Estoque) diretamente ao Campo (Instaladores e Motoristas). O sistema resolve o *hardware churn* - sumiço e má gestão de peças (rastreadores e chips GSM) durante seu ciclo logístico. Atua como o alicerce único para o cruzamento entre Cliente, Equipamento, Veículo e Técnico, orquestrados por restrições rígidas de acesso (RBAC).

O grande diferencial tecnológico reside na "Anti-fragilidade": o PWA age como um "Cofre Local" isolado usando o banco de dados nativo do navegador. O técnico emite checklists offline com um sistema de fallback via WhatsApp (Degradação Graciosa) auditado criptograficamente, erradicando perdas logísticas mesmo sob falha plena da rede celular em subsolos fechados.

## Project Classification

*   **Project Type:** SaaS B2B Integrado / Web App Administrativo & PWA para Mobile
*   **Domain:** Logística, Automotive & IoT
*   **Complexity Level:** Alta (Arquitetura orientada a estados offline e gestão rigorosa de estado de hardware atrelado a veículos).
*   **Project Context:** Greenfield (Construção do zero, arquitetura limpa, cloud-agnostic).

## Success Criteria

### User Success
*   **Instalador (Técnico de Campo):** O usuário preenche checklists e anexa provas de serviço em ambientes isolados garantindo 100% da integridade da Ordem de Serviço (O.S).
*   **Gestor de Estoque/Operador (Matriz):** O Gestor substitui planilhas por visões focadas em 'Contextual Disclosure', controlando a transição restrita da vida útil do equipamento com 2 cliques.
*   **Motoristas das Frotas (Clientes Finais):** O motorista executa checklists automotivos B2B de forma rotineira e independente.

### Business Success
*   **Zerar Hardware Churn:** Fim das perdas financeiras de chips GSM ativados em ativos mortos ou rastreadores sucateados invisíveis para o faturamento.
*   **Minimização Logística Operacional:** Rotas em mapa validadas auditivamente contra distâncias cobradas pelos técnicos. 

### Technical Success
*   **Volumetria Estável:** Infraestrutura estrita de Gestão de Ativos (ERP) capaz de gerenciar a base e cadastros atrelada a uma frota meta de **1.000 veículos/peças ativos**, sem acoplamento direto à alta frequência do monitoramento de posicionamento (GPS pings), que reside em plataforma terceira.
*   **SLA de Sincronismo (1 Hora):** Todo relatório do Cofre Offline ganha uma margem de segurança de 1 hora limite, sob a qual o fallback é ativado amigavelmente (ZIP).
*   **Infraestrutura Agnóstica:** Plataforma modularizada instalada de forma autônoma sobre qualquer servidor Ubuntu 22.04 LTS VPS, fugindo do aprisionamento de Big Techs (Vendor Lock-in 0%).

## Product Scope

### MVP (Phase 1 / Lançamento)
O MVP foca na estruturação fundacional da base de hardware (Rastreadores vs Veículos) vinculados às demandas dos operadores e técnicos, abstendo a sobrecarga das automações terceirizadas.
*   **Painel Central ERP:** Gestão total de estoque, controle RBAC de usuários e transições logísticas de hardware.
*   **PWA Instaladores e Motoristas B2B:** Execução offline (IndexedDB), fila visual de atividades e Degradação Graciosa para interações emergenciais (Cancelamento por Botão Sliding e provas Hash-HMAC).
*   **Filas Virtuais Resilientes:** Criação do ambiente estrutural de mensageria (SMS) que armazenará ordens de travamentos, mas sem o disparo em produção, mitigando riscos até a Fase 2.

### Growth Features (Phase 2 / Post-MVP)
*   **Integração Fisco-Financeira:** Plug-in direto via API terceira para absorver toda a automação tributária e de emissão de boletos via Parceiros.
*   **Assinatura Digital B2B (DocuSeal):** Interligação com contratos digitais de embarque logístico integrando validação formal eletrônica.
*   **Gateway SMS Telecom:** Fluxo de despacho ativado enviando as "Filas de SMS" da Rastertech aos servidores parceiros.

### Vision (Phase 3)
*   **Analytics de Manutenção:** Exportação de escore analítico de manutenção de risco de frota logado por hardware para indústrias securitárias.

## User Journeys

### 1. João (Instalador Terceirizado) - Escopo Offline
* **Situação:** Trabalha na instalação de um rastreador em subsolo comercial sem sinal de internet (3G/4G).
* **Jornada:** João atualiza o PWA nativamente offline, anexando métricas e preenchendo o diagnóstico O.S de fechamento.
* **Clímax / Edge Case:** Com falha fatal de sinal prolongada por mais de 1 Hora, o PWA ativa a "Degradação Graciosa" criptografada provando, via pacote WhatsApp, sua submissão jurídica da OS, protegendo sua comissão salarial.

### 2. Carlos (Operador de Ativos/Matriz) - Mapeamento de Frotas
* **Situação:** A carteira de Excel sobre os rastreadores do Cliente B2B sucumbiu e causou divergência de estoque.
* **Jornada e Clímax:** Carlos usa o ERP focado em 'Contextual Disclosure', notando gargalos de hardwares atrelados sem faturamento. Ele rastreia vínculos, decreta o "Soft-Delete" de transição e converte o status do rastreador para "Em Retirada", mitigando fraudes e estancando pagamentos irreais da carteira GSM.

### 3. Pedro (Motorista de Frota) e Camila (Cadastradora MATRIZ)
* **Jornada de Inserção Base:** Camila (Operadora) cria clientes de frota e cruza as restrições logísticas vinculando [CHIP -> Rastreador -> Placa]. O motorista subordinado dessa equipe recebe permissões restritas em seu app, executando o checklist de fluídos/pneus de forma auto-suficiente na primeira hora da manhã, isentando o HelpDesk de ligações inúteis diárias.

## Innovation & Domain Dynamics

* **Prevenção Contundente de API Choke (Caching Cron-Jobs):** Sendo um ecossistema API-First na Fase 2, as quedas do Módulo Fiscal terceiro não devem travar o faturamento. Um cron-job tático de 10 minutos tentará religar faturas retidas em fila sem prejudicar as visualizações administrativas locais.
* **Degradação Auditável (Hash Criptografado):** Cancelamentos offline e provas fotográficas recebem um Checksum selado garantindo "Data-Integrity" à Rastertech se repassadas pelos aplicativos sociais externos (WhatsApp) quando a rede externa morrer.
* **RBAC Multi-Tenant Tático:** Estrutura isolada onde Operadores não podem escalar acessos de gestores; contudo, agem como base de suporte cadastrando motoristas aos próprios donos de frotas atrelados que sentirem dificuldades de acesso primário em seu painel Tenant-Isolado.
* **Compliance Setorial:** Total sujeição ao padrão "Não Deletável" da LGPD e logística. Uso obrigatório de "Soft-Deletes" na base inteira preservando histórico técnico de fraude do equipamento. 

## Functional Requirements

### 1. Gestão de Identidade e Acessos (Identity & RBAC)
* FR1: O Gestor (Administrador Supremo) detém permissão onipotente para gerenciar todos os níveis hierárquicos, incluindo a criação de novos Operadores da Matriz.
* FR2: O Operador (Nível Operacional Base) pode criar Clientes Frotistas e Técnicos Instaladores e Motoristas (como suporte).
* FR3: A Empresa Cliente B2B (Frotista) recebe painel reduzido com acesso isolado puramente para gerir os seus Motoristas.
* FR4: Gestores e Operadores podem realizar a exclusão branda (Soft Delete / Anonimização) das contas criadas.
* FR5: O Motorista e o Técnico Instalador realizam autenticação padrão restrita utilizando E-mail e Senha, concordando imperativamente com Termos de Uso/LGPD no primeiro acesso.
* FR6: O Sistema restringe a visão ativa do Motorista unicamente ao seu próprio raio de atividade (Tenant-Isolado).

### 2. Gestão de Patrimônio e Entidades (Asset Lifecycle)
* FR7: O Gestor/Operador pode criar, ler e editar ativos vitais no inventário (Tags: CHIP GSM e Rastreador GPS).
* FR8: O Gestor/Operador pode associar um CHIP GSM exclusivamente e unicamente ao IMEI de um Rastreador físico.
* FR9: O Gestor/Operador pode associar a dupla de peças armada à placa de um Veículo atrelado a um Contrato B2B.
* FR10: O Sistema deve bloquear tentativas lógicas de duplicidade, impedindo locações paralelas cruzadas de hardware em uso.
* FR11: O Gestor/Operador pode engatilhar a alteração do status físico transitório (Disponível, Instalado, Em Retirada, Manutenção).
* FR12: O Sistema proíbe peremptoriamente o "Hard Delete" absoluto de hardwares exigindo seu Soft-delete e uso de labels finais orgânicas.

### 3. Operação de Campo PWA e Falhas Nativas (Field Tasks)
* FR13: O Instalador pode "Aceitar" ou "Recusar" Atendimentos preestabelecidos em sua fila restrita de PWA.
* FR14: O Instalador e o Motorista podem preencher formulários imutáveis (Checklist ou O.S) anexando fotos limitadas.
* FR15: O Motorista interage e lista suas últimas 5 atividades engajadas por meio de uma fila fifo visual interativa.
* FR16: O Instalador e o Motorista podem alterar e retificar campos do formulário apenas num prazo estrito de *1 Hora* pós-emissão, onde o sistema os selará como read-only em seguida.
* FR17: O Instalador deve reportar e processar a abortagem local da O.S acionando o botão restritivo tranca dupla e declarando tipificações criminais/operacionais fotográficas atreladas.

### 4. Integração Resiliente (Comm & Failsafe Base)
* FR18: O Instalador pode submeter e reter formulários operacionais abertos desconectados da rede (Offline Base).
* FR19: O Sistema ativa notificação "SLA Expirado" e permite ao Técnico compilar/exportar seus logs em pacote formato ZIP.
* FR20: O Sistema processa push-notifications baseadas em browser alertando sobre as "Pendências Fila O.S" e alarma a Dashboard Matriz operante dos desvios de Cancelamento-Campo.

## Non-Functional Requirements

### 1. Resiliência de Campo (Offline-First Reliability)
* NFR1: O PWA do Instalador deve garantir a execução de **100% das tarefas primárias** com armazenamento blindado (IndexedDB) absolutamente sem latência visível originadas por Network.
* NFR2: O mecanismo de exportação/fallback ZIP deve ser ativado visualmente pelo Client aos **exatos 60 minutos** após a O.S engasgar isolada nas filas restritas de subida ao backend.

### 2. Segurança Forense (Security & Compliance)
* NFR3: O PWA Offline deve computar uma assinatura Local Hash do pacote ZIP do Failsafe gerado, exigindo Tolerância Zero (0%) na conferência cruzada pela Matriz.
* NFR4: 100% da operação atrelada à entidades B2B de hardwares deve mascarar exclusões através de metadados binários estritos de 'Soft-Delete', blindando passivos para a LGPD e fiscalizações tributárias.

### 3. Portabilidade Sistêmica (Infrastructure Agnosticism)
* NFR5: O Backend Rest, o Banco SQL e o Dashboard devem ser instanciados/provisionados por Linux/Docker viabilizando "Cloud Independence" nativa primária.
* NFR6: As construções de Dia 1 não toleram nenhum "Vendor-Lock In" em suas tecnologias cruciais para logísticas base (Execução orgânica sobre um servidor/VPS puro hosteando padrão Ubuntu 22.04 LTS).

### 4. Integração Base e Robustez
* NFR7: As filas transacionais voltadas ao Phase 2 (Cargas Contábeis Ext.) requerem mecanismos de "Retry Exponencial Tático" (Timeout fixos 10 minutos) que absorvam falhas crônicas externas silenciosamente para o Operador logado.
