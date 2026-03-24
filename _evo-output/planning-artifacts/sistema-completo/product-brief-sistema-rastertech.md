---
stepsCompleted: [1, 2, 3, 4, 5]
inputDocuments: []
date: "2026-03-22"
author: Ubiratan
---

# Product Brief: sistema-rastertech

<!-- Content will be appended sequentially through collaborative workflow steps -->

## Executive Summary

O **sistema-rastertech** é uma plataforma de gestão completa (ERP/CRM) desenvolvida especificamente para o setor de rastreamento veicular. Ele unifica e simplifica toda a cadeia operacional: desde o cadastro rápido de clientes e veículos até o controle rigoroso do ciclo de vida de rastreadores e chips GSM, passando pela gestão de ordens de serviço e de instalações com pleno funcionamento offline. Ao transformar a experiência do usuário através de um portal de autoatendimento ágil e um Cartão Interativo Digital (facilitando acesso aos apps, credenciais e painel), e oferecer aos gestores um painel rápido, sem burocracias e com envio de comandos SMS integrados, o sistema-rastertech substitui planilhas descentralizadas e fluxos lentos por uma operação à prova de falhas.

---

## Core Vision

### Problem Statement

A operação de uma empresa de rastreamento veicular envolve a sincronização de múltiplos componentes críticos (veículos, equipamentos rastreadores, chips GSM e clientes). Atualmente, esse gerenciamento é feito de forma pulverizada e manual, utilizando bancos de dados simples mesclados a planilhas eletrônicas. Esse cenário gera retrabalho, perda de rastreabilidade (especialmente na segurança da reciclagem e manutenção de chips e equipamentos após cancelamentos) e um fluxo de cadastro excessivamente moroso.

### Problem Impact

A falta de centralização atrasa o atendimento ao cliente e o suporte técnico. Gestores perdem tempo navegando por múltiplos menus apenas para cruzar os dados de um cliente com o chip e o rastreador de seu veículo. Em campo, instaladores sem internet não conseguem reportar dados em tempo real. Sem um histórico centralizado ou controle de status de sucata/manutenção, torna-se fácil enviar equipamentos com problemas para a campo ou não diagnosticar reincidências nos serviços.

### Why Existing Solutions Fall Short

As soluções existentes sofrem de "burocracia de cliques" e falta de adaptação ao ambiente real de campo (garagens sem sinal). Os gestores precisam percorrer várias telas para alcançar um dado ou usar plataformas externas para envio de SMS. Além disso, falham em não oferecer uma experiência self-service focada onde o cliente consegue ver seus contratos e gerenciar as instalações com rapidez sem consumir o time de suporte.

### Proposed Solution

O **sistema-rastertech** proporciona um ambiente consolidado com funcionamento impecável ponta a ponta. Através de um aplicativo (PWA) de operação rápida e armazenamento local (cache offline para instaladores enviarem fotos retroativamente), o trabalho não para nunca. No back-office, os equipamentos contam com ciclo de vida rigoroso (Disponível, Instalado, Em Manutenção, Descartado) garantindo total qualidade antes do reuso. 

### Key Differentiators

O verdadeiro diferencial competitivo reside na agilidade de campo aliada à automação do atendimento:
1. **Hub/Email de Onboarding Seguro:** Após assinatura, o cliente ganha acesso imediato via onboarding dinâmico por email com todo o seu kit (links diretos para baixar o APP, login para o portal web, e botões diretos de chat/WhatsApp).
2. **Portal do Cliente Seguro e Self-Service:** Um acesso via navegador onde o cliente cadastra documentos do veículo, agenda instalações e audita sua contratação (com edições protegidas via fluxos de aprovação/tickets).
3. **App Instalador Antifragilidade (Offline):** Instaladores em oficinas sem sinal de internet geram fotos, reportam horários e completam checklists, e o sistema sincroniza automaticamente as evidências temporizadas quando reconectam.
4. **Ciclo de Vida do Rastreador à Prova de Falhas:** Cancelamento de contrato gera o "Retorno para Manutenção/Revisão", e o gestor precisa aplicar baterias de testes até que a peça volte ao status definido para reuso com 100% de qualidade.
5. **Comandos SMS e Histórico na Ponta dos Dedos:** Cópia, cola e disparo de comandos SMS diretamente da janela do cliente, tudo gerando um minucioso log para análises estratégicas.

---

## Target Users

### Primary Users

#### 1. Cliente Final (B2C & B2B)
**Contexto:** Proprietários pessoa física (1 veículo) ou gestores logísticos de transportadoras (frota inteira). A dor e o objetivo central são idênticos independentemente da escala.
**Problema:** Necessitam de visibilidade rápida, envio de documentos e suporte do seu rastreamento sem burocracia ou longas filas de espera no telefone.
**Visão de Sucesso:** Uma integração fluida por meio do "Cartão de Acesso Interativo" recebido logo após a assinatura. Eles se cadastram e agendam serviços no portal Self-Service, e sentem a tranquilidade de proteger seu patrimônio com transparência total via web ou aplicativo.

#### 2. Instalador / Técnico de Campo (Interno e Terceirizado)
**Contexto:** Trabalha de forma móvel, frequentemente em garagens, subsolos ou rodovias sem acesso adequado à internet.
**Problema:** Sofre com a vulnerabilidade a reclamações de clientes pós-instalação e perde a visibilidade do cômputo financeiro de seus serviços prestados (acerto de contas).
**Visão de Sucesso:** Através de um aplicativo PWA offline, faz o checklist fotográfico e anexa a foto do documento com o cliente/responsável segurando-o para finalizar a OS, garantindo proteção jurídica. A interface exibe imediatamente uma "Carteira Virtual" ao estilo extrato bancário, detalhando serviços prestados e motivos de pendências em aprovações financeiras.

#### 3. Gestor (Comercial, Operacional e de Estoque)
**Contexto:** O tomador de decisões que orquestra a central técnica e comercial.
**Problema:** Sofre com estoques descontrolados de chips/rastreadores, perda de itens e dificuldades em mensurar peças "boas" contra sucatas invisíveis na operação.
**Visão de Sucesso:** Um dashboard consolidado com foco em rastreabilidade macro de estoque e comercial. Ele identifica os status dos ciclos de vida do maquinário, focando exclusivamente na aprovação de reuso, envio de SMS veloz e liberação de orçamentos ou faturamentos complexos.

### Secondary Users

#### 4. Operador (Atendente de Suporte Nível 1)
**Contexto:** Atende ponta a ponta na frente online de Suporte Técnico. É o contato principal da Matriz interagindo tanto com o Técnico (despachando O.S.) quanto com o Cliente.
**Problema:** Ferramentas lentas gerenciam os despachos junto com atendimento no mesmo painel, resultando em extrema sobrecarga cognitiva, perda de controle de estoque que retorna da rua e desorganização dos técnicos.
**Visão de Sucesso:** Uma interface incrivelmente clara e dividida visualmente entre a "Fila de Despacho Operacional" e a "Fila de Suporte/Atendimento". O Operador emite Alertas/PUSH direto no celular do técnico para despachar o serviço e também atua ativamente sinalizando equipamentos devolvidos como "Em Manutenção/Triagem" no mesmo instante que retornam à base. Atua protegido por permissões (RBAC) que evitam a destruição de dados sem engessar a velocidade na resolução rotineira.

#### 5. Motorista da Frota (Via Cliente)
**Contexto:** Colaborador que conduz os veículos B2B e fará o preenchimento diário do uso da frota.
**Problema:** Vulnerabilidade a ser responsabilizado por defeitos na lataria ou mecânica deixados pelo condutor anterior do turno na mesma frota.
**Visão de Sucesso:** Uma aba específica do sistema onde, usando o PWA, ele preenche o fluxo de checklist (óleo, água, pneus) como prova inquestionável do estado de saída e chegada do veículo.

### User Journey

* **Descoberta:** O Cliente Final assina o contrato. Imediatamente recebe o Cartão Interativo Digital em PDF.
* **Autoatendimento Invertido:** Acessa o portal, insere seus dados e documentações e agenda a janela ideal de serviço.
* **Despacho Dinâmico:** O Operador na central cadastra os rastreadores e chips virgens. Vincula-os e despacha a Ordem de Serviço em tempo real via **Alerta PUSH** para o Instalador.
* **Trincheira Operacional (Offline):** O Instalador entra na garagem onde a frota está (sem sinal de internet). Abre seu App, instala, faz checklist fotográfico e força o assinante a comprovar fisicamente segurando um documento.
* **Sincronização & Recompensa à Prova de Falhas:** O Instalador pega um sinal de 4G na estrada. A OS sincroniza automaticamente em background de forma idempotente (garantindo que não duplique recebimentos se a conexão piscar). O Saldo financeiro do serviço cai na sua Conta/Carteira virtual na mesma hora, "Aguardando Faturamento".
* **Rotina do Motorista (Checklist Preventivo):** O Motorista do cliente logo de manhã faz login no mesmo sistema e abre o PWA filtrado para o perfil dele. Executa o Checklist da frota e fica legalmente resguardado sobre os danos operacionais antes de seguir rota.
* **Manutenção & Gestão Integradas:** Quando ocorre recolhimento de material da frota, seja por cancelamento ou defeito, o equipamento chega às mãos da base. No primeiro contato, o Operador (ou Instalador) lança a peça no sistema com status primário 'Em Manutenção/Revisão'. O Gestor Comercial monitora tudo através de seu dashboard, focando no macro, disparando comandos SMS de homologação e ditando com punho de ferro quando a sucata é liberada para voltar à operação.

---

## Success Metrics

O sucesso do **sistema-rastertech** será validado pela eficiência da operação de campo aliada à centralização de hardware da matriz. O foco principal não está em métricas de vaidade atreladas a cliques, mas sim em lucro real, economia com logística do instalador e controle inabalável do ciclo de vida dos rastreadores.

### Business Objectives

**Visão Estratégica Mensal e Comparativa:**
*   **Controle de Hardware:** Redução implacável no custo com perdas de ativos de hardware (*hardware churn* perdido). O objetivo de negócios primário é acabar com o "desaparecimento" de chips e módulos GPS e mitigar a recompra desnecessária destes.
*   **Otimização Logística:** Diminuir drasticamente os custos operacionais (gasolina, fretes, tempo morto) controlando a distância média rodada e quantidade de viagens improdutivas dos instaladores terceirizados e internos.
*   **Qualidade Tecnológica e Fornecedores:** Mapear perfeitamente quais modelos de GPS e chips mais quebram para cortar modelos ruins ou renegociar lotes defeituosos antes das reinstalações.

### Key Performance Indicators

**Nota de Mensuração:** Todos os KPIs abaixo devem ser obrigatoriamente aferidos mensalmente, mas apresentados aos Gestores gerando um comparativo lado-a-lado (rolling window) com os últimos 3 meses, para rastreio de tendências.

1.  **Tempo Gasto nas Instalações (SLA de Campo):** Tempo medido estritamente pelos *Timestamps offline* criptografados gerados localmente no App do Instalador (do início ao último clique), blindando o SLA dele contra atrasos provocados por falta de rede ou demoras de sincronização (4G/Wi-Fi).
2.  **Otimização de Distância Percorrida (Heatmap de Rotas):** Km percorrido pelo técnico capturado nativamente por telemetria GPS invisível pelo App, mitigando preenchimento manual. Será atrelado a comparação com a "Rota Ideal" via API, acusando ineficiência ou adulteração na quilometragem faturada.
3.  **Volume e Localidade de Atendimentos:** Heatmap e quantidade absoluta de serviços (Novas Instalações vs Manutenções e O.S corretivas) cruzados geograficamente para evitar idas múltiplas ao mesmo local.
4.  **Taxa de Rotatividade (Instalados x Desinstalados):** O "Churn de Equipamento". Métrica vital que confronta o hardware virando lucro versus o que está voltando do campo sob estômago de cancelamento.
5.  **Taxa de Reabertura / Falhas por Tipo (Failure Rate por Modelo):** Identificação de marcas de GPS e chipes com problemas reincidentes. É obrigatório e fundamental cruzar esse KPI de falha de hardware com o **ID do Instalador** da primeira O.S., evitando assim punir marcas ou fornecedores quando o "defeito" proveio de uma má prática técnica de emenda no chicote elétrico do veículo.

---

## MVP Scope

### Core Features

O Escopo vital do nosso Lançamento (Dia 1) foca exclusivamente em unificar a dor logística, entregar valor ao cliente através da auto-operação e garantir estoques perfeitos, dividindo o mesmo sistema PWA responsivo em diferentes filtros de visão por login:
*   **Painel Central (Gestor e Operador):** Dashboard inteligente com controle do ciclo de vida de hardware, acionamento via "Push Notifications", e **disparo de comandos SMS com trava anti-clique**, gerando status visual de 'Na Fila da Operadora' para evitar redundância.
*   **Acesso do Cliente:** Portal self-service provido via Hub de Onboarding para envio vitalício de documentação da frota e agendamentos diretos.
*   **O App do Instalador (PWA Offline):** Cache poderoso com fotos de comprovação jurídica e "Carteira Financeira", engatilhado com um **Mecanismo Failsafe (Backup Local/Exportar P/ WhatsApp)** para garantir 100% que as provas do instalador não sumam em caso de erro pesado de cache do navegador celular.
*   **O App do Motorista (PWA):** O motorista executa o "Checklist Preventivo de Diagnóstico" (Nível de óleo, água, batidas na lataria, etc) antes do veículo sair para rota.

### Out of Scope for MVP

O que teremos a coragem de **não** fazer na Versão 1.0 para conseguirmos entregar o sistema no prazo e sem perder o foco na estabilidade:
*   Rastreamento contínuo silenciado do celular do Motorista usando API de background (substituindo o hardware nativo). O rastreamento neste momento continua focando apenas nos Hardwares de GPS implantados.
*   Emissão profunda de Notas Fiscais Nativas e contabilidades avançadas dentro da plataforma.
*   Automações supercomplexas de WhatsApp bot para substituir imediatamente a "Atendende/Operador" no primeiro contato.

### MVP Success Criteria

A versão 1.0 será considerada um "Sucesso Total" no momento em que a empresa **abandonar 100% o uso de planilhas independentes** para mapear em quem aquele chip ou hardware está atrelado. O sistema deve acelerar a comprovação financeira em campo e reduzir as horas mortas de logística (viagens inúteis expostas pelo heatmap). Se o Operador achar os dados com "apenas poucos cliques", os critérios e os KPIs foram atingidos.

### Future Vision

O *sistema-rastertech* iniciará como um monstro em gestão, mas no futuro, evoluirá de ERP para um verdadeiro **Hub de Rastreamento Híbrido Multicanal**. A Fase 2 abraçará a Telemetria Móvel (usando o app do motorista para monitorar a frota temporária sem necessidade de fios). Em 2 anos, os "Checklists Saudáveis de Óleo feitas pelo motorista" e os "Heatmaps da Matriz" poderão ser revendidos como uma pontuação inteligente para Seguradoras parceiras, barateando a taxa do nosso Cliente Final.
