---
stepsCompleted: ['step-01-init', 'step-02-discovery', 'step-03-core-experience', 'step-04-emotional-response', 'step-05-inspiration', 'step-06-design-system', 'step-07-defining-experience', 'step-10-user-journeys', 'step-11-component-strategy', 'step-12-ux-patterns', 'step-13-responsive-accessibility', 'step-14-complete']
inputDocuments: ['_evo-output/planning-artifacts/sistema-completo/prd.md', '_evo-output/planning-artifacts/sistema-completo/product-brief-sistema-rastertech.md']
date: "2026-03-23"
author: Ubiratan
---

# UX Design Specification - sistema-rastertech

**Author:** Ubiratan
**Date:** 2026-03-23

---

<!-- UX design content will be appended sequentially through collaborative workflow steps -->

## Visual Identity & Resources

* **Logotipo Oficial:** ![Rastertech Logo](https://rastertech.com.br/site/wp-content/uploads/2022/10/logo-07-1024x180.png)

## Executive Summary

### Project Vision

O **sistema-rastertech** visa erradicar falhas operacionais e perdas de patrimônio no setor de rastreamento veicular. A visão de design foca na **Imutabilidade e Confiança**: o sistema deve garantir que dados coletados em campo (fotos, checklists e relatos) sejam preservados offline e sincronizados com integridade forense. A interface da Matriz é desenhada para a excelência operativa, tratando cancelamentos como eventos críticos que exigem dupla validação.

### Target Users

*   **Instalador de Campo (Técnico):** Usuário focado em produtividade final. Opera o PWA após a instalação física para registros de O.S. Valoriza o feedback visual de "Trabalho Salvo".
*   **Gestor/Operador (Matriz):** Focado no sucesso da instalação. Recebe dados estruturados para auditoria e controle de estoque de peças (chips/rastreadores).
*   **Motorista de Frota:** Usuário de autoatendimento para checklists rotineiros, exigindo simplicidade extrema e zero atrito técnico.

### Key Design Challenges

*   **Autenticação de Veto (Panic Flow):** Desenvolver um fluxo de cancelamento que combine o *Slider de Confirmação* com uma re-autenticação obrigatória (Documento + Senha), exigindo anexos probatórios de barreiras físicas (ex: foto de garagem descoberta sob chuva).
*   **Fidelidade Fotográfica Offline:** Interface de câmera que restrinja capturas à lente do dispositivo (antifraude) e mostre claramente o progresso do upload/sincronismo em background.
*   **Contextual Disclosure na Matriz:** Painéis que escondam a complexidade do banco de dados e mostrem apenas as tarefas prioritárias ("Instalações Pendentes" e "Cancelamentos em Auditoria").

### Design Opportunities

*   **Gamificação de Sincronismo:** Usar micro-interações para dar ao técnico a satisfação psicológica de que sua comissão está garantida (ex: animação de "Cofre Trancado" ao salvar offline).
*   **Assinatura Digital de ZIP:** UI que demonstre visualmente a diferença entre um relatório sincronizado via API e um pacote de backup manual (ZIP/WhatsApp) auditado por Hash.

## Core User Experience

### Defining Experience

A experiência central do **sistema-rastertech** é a **Efetivação Auditável**. O usuário final (Técnico) deve sentir que sua missão foi cumprida através de um rito de fechamento que vincula digitalmente o hardware, o veículo e a presença física do cliente (via foto do documento). Na Matriz, a experiência é de **Controle Total e Transparência**, onde cada metro rodado e cada peça alocada é rastreável em 2 cliques.

### Platform Strategy

*   **PWA Mobile (Técnico/Motorista):** Focado em captura de mídia (fotos) e preenchimento de checklists. Operação offline prioritária com sincronismo em background.

## Desired Emotional Response

### Core Emotional Goals

Pensando no **sistema-rastertech** como um sistema de gestão de ativos (veículos, rastreadores e chips GSM) com cadastro físico e dados de proprietários, os objetivos emocionais são:

**Respostas Emocionais Desejadas:**
- **Confiança e Segurança** - Usuários devem sentir que o patrimônio (veículos e equipamentos) está sob controle total
- **Organização e Controle** - Eliminação da desorganização, com visibilidade completa do inventário
- **Eficiência Operacional** - Fim da burocracia manual, foco no que realmente importa: rastrear ativos
- **Tranquilidade Administrativa** - Saber que não há perdas de equipamentos ou dados importantes

### Emotional Journey Mapping

**Momentos-Chave da Jornada:**

- **Primeiro Contato:** Expectativa de organização (o sistema promete acabar com planilhas caóticas)
- **Durante o Cadastro:** Foco e método (processo estruturado de vincular veículo + rastreador + chip + proprietário)
- **Após Vinculação Completa:** Realização e controle (ver todos os ativos organizados e rastreáveis)
- **Em Caso de Problemas:** Segurança procedural (soft-deletes e histórico preservam rastreabilidade)
- **Retorno ao Sistema:** Confiança operacional (interface consistente mostra status real de todos os ativos)

### Micro-Emotions

**Estados Emocionais Essenciais:**
- **Confiança vs. Dúvida** - Especialmente importante para dados críticos como proprietários e equipamentos
- **Organização vs. Caos** - Interface que transforma desordem em estrutura visual clara
- **Controle vs. Perda de Controle** - Visibilidade total do ciclo de vida dos equipamentos
- **Eficiência vs. Ineficiência** - Eliminar buscas manuais e cliques desnecessários
- **Segurança vs. Ansiedade** - Garantir que nenhum ativo "desapareça" do sistema

### Emotion-Design Connections

**Implicações para o Design UX:**

- **Para gerar CONFIANÇA:** Interfaces que mostram claramente as vinculações (veículo ↔ rastreador ↔ chip ↔ proprietário)
- **Para criar ORGANIZAÇÃO:** Dashboards com filtros visuais, agrupamentos lógicos, busca inteligente
- **Para promover CONTROLE:** Estados visuais claros (Disponível, Instalado, Em Manutenção), alertas proativos
- **Para proporcionar EFICIÊNCIA:** Autocomplete nos cadastros, validações automáticas, ações em lote
- **Para garantir SEGURANÇA:** Confirmações obrigatórias em desvinculações, backup automático, auditoria visual

## UX Pattern Analysis & Inspiration

### Apps Inspiradoras para os Usuários-Alvo

**Para Instaladores Técnicos:**
- **WhatsApp** - Comunicação rápida, status de mensagens, grupos organizados
- **Google Maps/Waze** - Navegação clara, estimativas de tempo, rotas alternativas
- **Instagram/Facebook** - Compartilhamento de fotos, stories temporários

**Para Gestores/Operadores:**
- **Excel/Google Sheets** - Organização tabular, filtros, fórmulas automáticas
- **Trello/Asana** - Quadros visuais, cards organizados, status claros
- **Bancos digitais (Nubank, Inter)** - Dashboards claros, transações organizadas

**Para Motoristas:**
- **Uber/99** - Status de corrida claros, estimativas de tempo, comunicação simples
- **WhatsApp** - Comunicação direta com passageiros/despachantes

### Padrões UX Transferíveis

**Padrões Aplicáveis ao Sistema Rastertech:**

1. **Status Visual Claro (como Uber/99):**
   - Estados visuais distintos: "Aguardando", "Em Andamento", "Concluído"
   - Cores e ícones que transmitem status imediatamente
   - **Aplicação:** Mostrar status dos equipamentos (Disponível, Instalado, Em Manutenção)

2. **Organização Hierárquica (como Trello):**
   - Agrupamento visual por categorias
   - Drag & drop intuitivo
   - **Aplicação:** Organizar ativos por frota, tipo de equipamento, status

3. **Busca e Filtros Poderosos (como Google Sheets):**
   - Filtros múltiplos simultâneos
   - Busca inteligente com autocomplete
   - **Aplicação:** Encontrar rapidamente veículos, proprietários ou equipamentos

4. **Confirmações Progressivas (como bancos digitais):**
   - Etapas claras em processos críticos
   - Feedback visual em cada passo
   - **Aplicação:** Cadastro de novos ativos ou desvinculações

### Padrões Específicos para Gestão de Ativos

**Inspiração em Sistemas de Inventário:**
- **Interface limpa e focada** (como Notion) - reduzir sobrecarga cognitiva
- **Estados visuais de sincronismo** (como Google Drive) - mostrar quando dados estão salvos
- **Histórico auditável** (como GitHub) - rastrear mudanças e quem fez

**Inspiração em Apps de Campo:**
- **Modo offline confiável** (como Google Maps offline)
- **Captura rápida de dados** (como câmera do Instagram)
- **Sincronização automática** (como WhatsApp web-desktop)

## Design System Choice

### Design System Approach for Sistema Rastertech

**Abordagem Recomendada: Sistema Customizável Híbrido**

Baseado na análise dos requisitos do **sistema-rastertech** - um sistema B2B/B2C de gestão de ativos com PWA mobile offline-first e interface web administrativa - a abordagem híbrida customizável oferece o melhor equilíbrio entre velocidade de desenvolvimento, identidade visual própria e consistência técnica.

### Análise de Requisitos do Projeto

**Fatores Determinantes:**
- **Plataformas:** PWA mobile (offline-first) + interface web administrativa
- **Usuários:** Mix de expertise (técnicos de campo, gestores, motoristas)
- **Complexidade:** Gestão de ativos críticos com requisitos de segurança e auditoria
- **Identidade:** Marca Rastertech estabelecida com logotipo próprio
- **Timeline:** Sistema operacional com foco em eficiência e confiabilidade

### Opções Avaliadas

**1. Sistema Customizado Completo**
- ✅ Identidade visual 100% única
- ❌ Investimento inicial muito alto
- ❌ Desenvolvimento lento para MVP

**2. Sistema Estabelecido (Material Design, Ant Design)**
- ✅ Desenvolvimento rápido e acessibilidade comprovada
- ❌ Menos diferenciação visual
- ❌ Pode parecer "genérico" para usuários corporativos

**3. Sistema Customizável Híbrido (MUI, Chakra UI, Tailwind UI)**
- ✅ Flexibilidade de marca com base sólida
- ✅ Equilíbrio entre velocidade e unicidade
- ✅ Componentes robustos para PWA + web
- ✅ Customização alinhada à identidade Rastertech

### Decisão: Sistema Customizável Híbrido

**Fundação:** Material Design 3 ou similar como base (componentes robustos, acessibilidade comprovada, suporte PWA nativo)

**Customização Rastertech:**
- Tema personalizado com cores e tipografia da marca
- Componentes específicos para gestão de ativos (status de equipamentos, vinculações visuais)
- Padrões de interação otimizados para workflows de campo

**Benefícios para o Sistema Rastertech:**
- **Velocidade:** Componentes prontos aceleram desenvolvimento
- **Consistência:** Base sólida garante experiência uniforme PWA ↔ web
- **Identidade:** Customização preserva marca Rastertech
- **Escalabilidade:** Fácil manutenção e evolução do design system
- **Confiabilidade:** Padrões testados reduzem riscos de UX
*   **Web Desktop (Matriz/Operador):** Focado em densidade de dados e gestão de status de hardware. Central de suporte manual para reset de senhas e monitoramento de alertas.

### Effortless Interactions

*   **Captura de Identidade:** Fluxo de câmera especializado para digitalização rápida de documentos com foto de clientes.
*   **Preenchimento de Relatos:** Campos de texto otimizados para relatos detalhados de finalização de O.S. após a conclusão técnica (com mãos limpas).
*   **Divulgação de Pendências:** A cada login, o técnico visualiza instantaneamente sua fila de "Atendimentos Aceitos", reduzindo o tempo de navegação.

### Critical Success Moments

*   **O "Check Verde" de Prova de Presença:** Quando o técnico anexa a foto do documento do cliente e envia a O.S., sentindo que sua comissão está blindada por provas incontestáveis.
*   **Recuperação Manual:** O momento em que o Suporte reseta a senha de um motorista em segundos no Painel Administrativo, restabelecendo a operação.

### Experience Principles

*   **Identidade Forçada:** Nenhuma tarefa crítica (O.S. ou Cancelamento) avança sem re-autenticação ou prova de identidade física (Documento cliente).
*   **Fidelidade às Provas:** O design prioriza a integridade das fotos e relatos manuscritos como os ativos mais valiosos do sistema.
*   **Controle Hierárquico:** O administrador e o operador são os guardiões das credenciais, mantendo a ordem e mitigando o acesso desautorizado.

## User Journey Flows

### Instalador João - Jornada Offline Completa

**Cenário:** João trabalha na instalação de um rastreador em subsolo comercial sem sinal de internet (3G/4G). Ele precisa completar a Ordem de Serviço (O.S.) offline e garantir que sua comissão seja protegida mesmo em caso de falha prolongada de rede.

**Fluxo Detalhado:**

```mermaid
journey
    title Jornada do Instalador João - Instalação Offline
    section Preparação
        João recebe notificação: "Nova O.S. pendente" --> João abre PWA offline
        João aceita O.S. na fila --> Sistema confirma: "O.S. aceita - Status: Em Andamento"
    section Instalação Física
        João instala rastreador no veículo --> João preenche checklist diagnóstico
        João anexa fotos do processo --> Sistema salva localmente (IndexedDB)
    section Finalização O.S.
        João preenche relatório final --> João assina digitalmente com documento
        Sistema gera hash criptográfico --> João submete O.S. (tenta sincronizar)
    section Contingência
        Rede falha por >1h --> Sistema ativa "Degradação Graciosa"
        Sistema gera pacote ZIP auditado --> João envia via WhatsApp
        Matriz recebe e valida hash --> Comissão protegida juridicamente
```

**Pontos Críticos de Decisão:**
- **Aceitação da O.S.:** João pode recusar se condições não forem adequadas
- **Sincronização:** Sistema tenta por 1 hora, depois ativa fallback
- **Prova de Identidade:** Obrigatória para proteger comissão

**Momentos de Sucesso:**
- **Confirmação Visual:** "O.S. salva offline com integridade garantida"
- **Feedback de Progresso:** Barra de upload simulada mesmo offline
- **Proteção Jurídica:** Hash criptográfico prova submissão válida

### Operador Carlos - Controle de Estoque e Frotas

**Cenário:** Carlos precisa mapear e controlar o estoque de rastreadores e chips GSM, identificando desvios e garantindo que equipamentos não sejam "perdidos" no sistema.

**Fluxo Detalhado:**

```mermaid
journey
    title Jornada do Operador Carlos - Controle de Ativos
    section Análise de Estoque
        Carlos acessa dashboard matriz --> Carlos filtra por "Status: Instalado"
        Sistema mostra mapa de ativos --> Carlos identifica divergências
    section Investigação
        Carlos clica em ativo suspeito --> Sistema mostra histórico completo
        Carlos verifica vínculos: CHIP ↔ RASTREADOR ↔ VEÍCULO --> Carlos confirma desvios
    section Correção
        Carlos inicia "Soft-Delete" --> Sistema solicita justificativa
        Carlos anexa provas documentais --> Sistema marca como "Em Retirada"
        Carlos notifica técnico responsável --> Status atualizado em tempo real
```

**Pontos Críticos de Decisão:**
- **Status do Ativo:** Disponível → Instalado → Em Retirada → Manutenção
- **Justificativa:** Obrigatória para auditoria e compliance LGPD
- **Notificação:** Automática para técnicos afetados

**Momentos de Sucesso:**
- **Visão Consolidada:** Dashboard mostra status de todos os ativos
- **Rastreabilidade:** Histórico completo de mudanças preservado
- **Prevenção de Perdas:** Alertas proativos sobre desvios

### Motorista Pedro e Operadora Camila - Cadastro e Operação Diária

**Cenário:** Camila cadastra uma nova frota e vincula equipamentos. Pedro, motorista da frota, executa checklists diários de forma independente.

**Fluxo Detalhado:**

```mermaid
journey
    title Jornada de Cadastro e Operação - Camila + Pedro
    section Cadastro pela Matriz
        Camila acessa painel administrativo --> Camila cria novo cliente frota
        Camila vincula CHIP ao RASTREADOR --> Sistema valida unicidade
        Camila vincula conjunto ao VEÍCULO --> Camila cria conta motorista
    section Primeiro Acesso Motorista
        Pedro recebe credenciais por email --> Pedro faz primeiro login
        Sistema solicita aceite LGPD --> Pedro configura perfil básico
        Sistema mostra frota atribuída --> Pedro recebe permissões restritas
    section Operação Diária
        Pedro abre app diariamente --> Sistema mostra checklist pendente
        Pedro inspeciona veículo (fluidos/pneus) --> Pedro anexa fotos
        Pedro submete checklist --> Sistema confirma sincronização
```

**Pontos Críticos de Decisão:**
- **Vinculação de Equipamentos:** Validação rigorosa contra duplicidades
- **Permissões Tenant:** Motorista vê apenas sua frota
- **Sincronização Checklist:** Offline-first com upload automático

**Momentos de Sucesso:**
- **Setup Simplificado:** Cadastro em poucos cliques
- **Independência Operacional:** Motorista opera sem suporte
- **Conformidade Automática:** Checklists padronizados reduzem erros

### Journey Patterns

**Padrões de Navegação Identificados:**

1. **Fila Visual de Atividades:** Padrão consistente em PWA (instaladores/motoristas) e web (matriz)
2. **Contextual Disclosure:** Informação revelada progressivamente baseada em permissões
3. **Status-Driven UI:** Interfaces que mudam dinamicamente baseado no estado dos ativos

**Padrões de Decisão:**

1. **Confirmação Dupla:** Para ações críticas (cancelamentos, desvinculações)
2. **Prova Fotográfica:** Obrigatória em mudanças de status físico
3. **Autenticação Contextual:** Re-autenticação baseada na criticidade da ação

**Padrões de Feedback:**

1. **Hash Criptográfico:** Confirmação visual de integridade de dados
2. **Sincronização Visual:** Indicadores claros de status online/offline
3. **Auditoria Transparente:** Histórico visível de todas as mudanças

### Flow Optimization Principles

**Princípios de Otimização:**

1. **Minimização de Cliques:** Jornada crítica do instalador reduzida a 3-4 ações principais
2. **Feedback Imediato:** Confirmação visual em cada passo, mesmo offline
3. **Recuperação Graciosa:** Fallback automático para cenários de falha
4. **Contexto Preservado:** Dados nunca perdidos, sempre recuperáveis
5. **Segurança Transparente:** Proteções técnicas invisíveis ao usuário cotidiano
## Estratégia de Componentes

### Componentes do Sistema de Design

Baseado na escolha do **Material Design 3 híbrido customizável**, temos acesso a uma biblioteca robusta de componentes fundamentais que cobrem a maioria das necessidades básicas do **sistema-rastertech**:

**Componentes Disponíveis (Material Design 3):**
- **Navegação:** App bars, navigation drawers, bottom navigation, tabs
- **Formulários:** Text fields, dropdowns, checkboxes, radio buttons, switches
- **Feedback:** Snackbars, dialogs, tooltips, progress indicators
- **Contêineres:** Cards, sheets, lists, grids
- **Ações:** Buttons (filled, outlined, text), floating action buttons, icon buttons
- **Dados:** Data tables, chips, badges
- **Mídia:** Image components, avatars

**Cobertura para o Sistema Rastertech:**
- ✅ Interface web administrativa (dashboards, formulários, tabelas)
- ✅ PWA mobile básica (navegação, formulários, listas)
- ✅ Estados e feedback padrão
- ❌ Componentes específicos para gestão de ativos
- ❌ Componentes offline-first com sincronização
- ❌ Componentes de auditoria e rastreabilidade

### Componentes Customizados

Analisando as jornadas do usuário e necessidades específicas do sistema, identificamos lacunas que requerem componentes customizados:

#### AssetCard - Cartão de Ativo

**Propósito:** Exibir informações completas de um ativo (veículo, rastreador, chip) com status visual claro e ações contextuais.

**Uso:** Dashboard de controle de ativos, listas de inventário, detalhes de equipamento.

**Anatomia:**
- Header: Ícone do tipo de ativo + nome/ID
- Status Badge: Chip colorido (Disponível, Instalado, Em Retirada, Manutenção)
- Informações Principais: Placa do veículo, IMEI do rastreador, número do chip
- Vinculações Visuais: Linhas conectando CHIP ↔ RASTREADOR ↔ VEÍCULO
- Ações: Menu dropdown com opções contextuais (editar, desvincular, histórico)

**Estados:**
- Default: Ativo normal
- Selecionado: Destaque visual para seleção múltipla
- Offline: Indicador de dados locais não sincronizados
- Erro: Alerta de inconsistência nos dados

**Variantes:**
- Compacto: Para listas densas
- Detalhado: Para visualização individual
- Mini: Para dashboards de overview

**Acessibilidade:** Labels ARIA descrevendo status e ações disponíveis, navegação por teclado no menu.

**Diretrizes de Conteúdo:** Priorizar informações críticas (status, localização), usar ícones consistentes.

**Comportamento de Interação:** Toque/press expande detalhes, swipe para ações rápidas em mobile.

#### SyncStatusIndicator - Indicador de Sincronização

**Propósito:** Mostrar claramente o status de sincronização de dados offline, dando confiança ao usuário sobre integridade das informações.

**Uso:** Header global do app, cards de formulários, áreas de dados críticos.

**Anatomia:**
- Ícone de status (conectado/desconectado/erro)
- Texto descritivo ("Sincronizado", "Salvando offline", "Erro de sincronização")
- Barra de progresso (quando aplicável)
- Timestamp da última sincronização

**Estados:**
- Sincronizado: Verde, ícone de check
- Sincronizando: Azul, spinner animado
- Offline: Amarelo, ícone de nuvem cortada
- Erro: Vermelho, ícone de alerta com retry button

**Variantes:**
- Compacto: Apenas ícone + tooltip
- Detalhado: Ícone + texto + timestamp
- Banner: Barra horizontal para avisos importantes

**Acessibilidade:** Screen reader anuncia mudanças de status automaticamente.

**Diretrizes de Conteúdo:** Mensagens claras e não técnicas ("Seus dados estão seguros offline").

**Comportamento de Interação:** Toque/press mostra detalhes da sincronização, botão retry para tentativas manuais.

#### AuditTrailViewer - Visualizador de Rastreabilidade

**Propósito:** Mostrar histórico completo de mudanças em um ativo, garantindo transparência e auditoria.

**Uso:** Modal de detalhes do ativo, seção de compliance, investigações de desvios.

**Anatomia:**
- Timeline vertical com eventos cronológicos
- Cada entrada: timestamp, usuário, ação, dados alterados
- Filtros: por tipo de ação, período, usuário
- Export: botão para gerar relatório

**Estados:**
- Carregando: Skeleton loading
- Vazio: Mensagem explicativa
- Erro: Estado de falha com retry

**Variantes:**
- Compacto: Lista simples para espaço limitado
- Detalhado: Timeline rica com detalhes expandidos

**Acessibilidade:** Navegação por teclado na timeline, labels descritivas.

**Diretrizes de Conteúdo:** Usar linguagem clara ("João instalou rastreador XYZ em veículo ABC").

**Comportamento de Interação:** Expandir/colapsar detalhes, filtros interativos, paginação para históricos longos.

#### OfflineFormManager - Gerenciador de Formulários Offline

**Propósito:** Gerenciar formulários preenchidos offline com validação automática e feedback de integridade.

**Uso:** Formulários de O.S., checklists, relatórios de campo.

**Anatomia:**
- Header com status de sincronização
- Campos do formulário com validação visual
- Área de anexos (fotos) com preview
- Botão de submit com hash criptográfico

**Estados:**
- Editando: Campos editáveis
- Validando: Spinner durante validação
- Pronto: Formulário completo e validado
- Enviando: Progresso de sincronização

**Variantes:**
- Checklist simples: Para inspeções rápidas
- O.S. completa: Para relatórios detalhados

**Acessibilidade:** Validação em tempo real anunciada por screen reader.

**Diretrizes de Conteúdo:** Campos obrigatórios claramente marcados, mensagens de erro específicas.

**Comportamento de Interação:** Auto-save automático, validação progressiva, confirmação obrigatória antes do submit.

### Estratégia de Implementação de Componentes

**Abordagem Geral:**
- **Base Técnica:** React + TypeScript para PWA, com hooks customizados para offline-first
- **Tokens de Design:** Usar variáveis CSS do Material Design 3 customizadas para Rastertech
- **Padrões de Estado:** Implementar máquina de estados para componentes complexos
- **Testabilidade:** Componentes com props bem definidas e stories no Storybook

**Integração com Sistema de Design:**
- Herdar estilos base do Material Design 3
- Sobrescrever apenas tokens necessários para identidade Rastertech
- Manter compatibilidade com componentes padrão
- Usar composition over inheritance para extensibilidade

**Padrões de Desenvolvimento:**
- Componentes funcionais com hooks
- TypeScript strict para type safety
- Testes unitários + integração
- Documentação Storybook obrigatória

### Roadmap de Implementação

**Fase 1 - Componentes Críticos (Sprint 1-2):**
- AssetCard: Essencial para dashboards e controle de ativos
- SyncStatusIndicator: Fundamental para confiança offline
- OfflineFormManager: Core da experiência de campo

**Fase 2 - Componentes de Apoio (Sprint 3-4):**
- AuditTrailViewer: Para compliance e investigações
- Componentes de navegação customizados para RBAC
- Otimizações de performance para listas grandes

**Fase 3 - Componentes Avançados (Sprint 5+):**
- Dashboards analíticos customizados
- Componentes de mapa integrados
- Funcionalidades avançadas de filtro e busca

**Critérios de Priorização:**
- Impacto na jornada crítica do usuário
- Frequência de uso
- Complexidade de implementação
- Dependências técnicas

## Padrões de Consistência UX

### Hierarquia de Botões

**Quando Usar:** Para ações primárias, secundárias e terciárias em qualquer tela do sistema.

**Design Visual:**
- **Primário (Filled Button):** Azul Rastertech (#1976D2) para ações principais como "Salvar", "Enviar", "Confirmar"
- **Secundário (Outlined Button):** Contorno azul para ações importantes mas não críticas como "Editar", "Visualizar"
- **Terciário (Text Button):** Texto simples para ações opcionais como "Cancelar", "Voltar"
- **Destrutivo (Filled Error):** Vermelho (#D32F2F) para ações irreversíveis como "Excluir", "Desvincular"

**Comportamento:**
- Estados: Default, Hover (elevação sutil), Pressed (feedback tátil), Disabled (opacidade 50%)
- Loading: Spinner integrado para ações assíncronas
- Ordem: Primário à direita, secundário à esquerda (LTR)

**Acessibilidade:** Contraste mínimo 4.5:1, foco visível, labels descritivos.

**Considerações Mobile:** Botões touch-friendly (48dp mínimo), feedback háptico opcional.

**Variações:** Full-width para mobile, compact para densidade desktop.

### Padrões de Feedback

**Quando Usar:** Para comunicar resultados de ações, estados do sistema e orientar usuários.

**Design Visual:**
- **Sucesso:** Verde (#388E3C) com ícone check-circle, fundo claro
- **Erro:** Vermelho (#D32F2F) com ícone error, fundo claro  
- **Aviso:** Laranja (#F57C00) com ícone warning, fundo claro
- **Info:** Azul (#1976D2) com ícone info, fundo claro

**Comportamento:**
- **Snackbar:** Temporário (4-5s) para feedback não crítico, dismissível
- **Banner:** Persistente para avisos importantes, requer ação do usuário
- **Toast:** Flutuante para confirmações rápidas
- **Inline:** Próximo ao campo para validações de formulário

**Acessibilidade:** Screen reader announcements automáticos, foco programático.

**Considerações Mobile:** Posicionamento bottom para não obstruir conteúdo, swipe para dismiss.

**Variações:** Com ação (botão "Tentar Novamente"), sem ação (apenas informativo).

### Padrões de Formulários

**Quando Usar:** Para entrada de dados estruturada em cadastros, edições e configurações.

**Design Visual:**
- Campos obrigatórios marcados com asterisco vermelho
- Labels acima dos campos (Material Design)
- Estados: Default, Focused (borda azul), Error (borda vermelha + mensagem), Success (borda verde)
- Helper text abaixo para orientações

**Comportamento:**
- **Validação:** Tempo real para formato, obrigatória no submit
- **Auto-save:** Para formulários longos, indicador de "Rascunho salvo"
- **Seções:** Agrupamento visual com títulos e divisores
- **Progress:** Barra de progresso para formulários multi-etapa

**Acessibilidade:** Labels associadas, validação anunciada, navegação por teclado.

**Considerações Mobile:** Campos apropriados para touch (select dropdown, date picker), teclado virtual adaptado.

**Variações:** 
- Compacto: Para formulários inline
- Expandido: Para cadastros detalhados
- Offline: Com indicador de sincronização pendente

### Padrões de Navegação

**Quando Usar:** Para movimento entre seções e manutenção de contexto.

**Design Visual:**
- **Bottom Navigation (Mobile PWA):** 4-5 abas principais (Dashboard, Ativos, Ordens, Perfil)
- **Side Navigation (Desktop):** Collapsible com ícones + labels
- **Breadcrumbs:** Para navegação hierárquica profunda
- **Tab Navigation:** Para seções dentro de uma página

**Comportamento:**
- **Estado Ativo:** Destaque visual claro (cor + peso da fonte)
- **Transições:** Suaves entre seções, loading states
- **Contextual:** Menus adaptados por perfil (RBAC)
- **Offline:** Cache de navegação, indicadores de disponibilidade

**Acessibilidade:** Landmarks ARIA, skip links, navegação por teclado.

**Considerações Mobile:** Thumb zone otimizada, swipe gestures para voltar.

**Variações:** 
- Minimal: Para foco em tarefa
- Rich: Para exploração e descoberta

### Padrões de Estados Vazios e Carregamento

**Quando Usar:** Quando não há conteúdo para mostrar ou dados estão carregando.

**Design Visual:**
- **Empty States:** Ilustração amigável + mensagem clara + ação sugerida
- **Loading:** Skeleton screens com placeholders realistas
- **Error States:** Mensagem de erro + botão de retry

**Comportamento:**
- **Empty States:** Orientação contextual ("Cadastre seu primeiro veículo")
- **Loading:** Progresso estimado quando possível
- **Error Recovery:** Múltiplas opções de retry (recarregar, tentar novamente, contato suporte)

**Acessibilidade:** Textos descritivos, alternativas para imagens.

**Considerações Mobile:** Estados touch-friendly, offline-aware.

**Variações:** 
- Primeira vez: Orientação tutorial
- Sem resultados: Sugestões de filtros alternativos
- Erro temporário: Retry automático

### Padrões de Busca e Filtragem

**Quando Usar:** Para localização rápida de ativos, ordens ou dados específicos.

**Design Visual:**
- **Search Bar:** Prominente no topo, com placeholder contextual
- **Filtros:** Collapsible com chips selecionados
- **Resultados:** Lista paginada com destaques de match
- **Sugestões:** Autocomplete inteligente

**Comportamento:**
- **Busca Instantânea:** Resultados em tempo real
- **Filtros Persistentes:** Estado mantido entre sessões
- **Histórico:** Sugestões baseadas em buscas anteriores
- **Offline:** Busca local com indicadores de sincronização

**Acessibilidade:** Labels claras, navegação por teclado nos resultados.

**Considerações Mobile:** Search overlay, filtros bottom sheet.

**Variações:** 
- Simples: Campo único
- Avançada: Múltiplos filtros + operadores

### Padrões de Modal e Overlays

**Quando Usar:** Para ações focadas, confirmações críticas ou conteúdo adicional.

**Design Visual:**
- **Modal:** Centralizado, overlay escuro, bordas arredondadas
- **Bottom Sheet (Mobile):** Slide up, altura variável
- **Tooltip:** Contextual, dismissível
- **Popover:** Ancorado a elemento trigger

**Comportamento:**
- **Confirmação:** Título claro + descrição + botões de ação
- **Dismiss:** Overlay click, ESC key, botão X
- **Foco:** Contained dentro do modal
- **Responsivo:** Bottom sheet em mobile, modal em desktop

**Acessibilidade:** Focus trap, ARIA roles apropriados.

**Considerações Mobile:** Touch gestures, safe area awareness.

**Variações:** 
- Confirmation: Para ações críticas
- Content: Para informações adicionais
- Form: Para entrada de dados modal

### Integração com Sistema de Design

**Complementaridade com Material Design 3:**
- Herdar tokens base (cores, tipografia, espaçamento)
- Customizar apenas para necessidades específicas do domínio
- Manter consistência visual enquanto atendemos requisitos únicos

**Regras de Customização:**
- Usar variáveis CSS para temas Rastertech
- Componentes customizados seguem princípios Material
- Padrões documentados impedem inconsistências
- Design tokens versionados para evolução controlada

## Design Responsivo e Acessibilidade

### Estratégia Responsiva

O **sistema-rastertech** adota uma abordagem **mobile-first** com PWA otimizada, garantindo que a experiência crítica de campo funcione perfeitamente em dispositivos móveis, enquanto aproveita recursos adicionais em telas maiores.

**Estratégia Mobile (PWA Primária):**
- Interface touch-optimized com bottom navigation
- Layouts single-column prioritários
- Componentes adaptados para polegar (thumb zone)
- Funcionalidades offline-first como prioridade

**Estratégia Tablet:**
- Layout híbrido aproveitando espaço extra
- Side navigation opcional para navegação rica
- Multi-column para dashboards de controle
- Toque e gestos otimizados

**Estratégia Desktop:**
- Densidade de informação máxima
- Multi-column layouts para controle de ativos
- Side navigation fixa para eficiência operacional
- Atalhos de teclado e funcionalidades avançadas

**Adaptação Contextual:**
- Instaladores: Mobile-first com foco em captura rápida
- Gestores: Desktop otimizado para controle e dashboards
- Motoristas: Interface simplificada independente do dispositivo

### Estratégia de Breakpoints

Breakpoints customizados baseados no uso real e capacidades dos dispositivos-alvo:

**Breakpoints Definidos:**
- **Mobile Pequeno:** 320px - 480px (smartphones antigos, foco essencial)
- **Mobile:** 481px - 767px (smartphones modernos, PWA completa)
- **Tablet:** 768px - 1023px (tablets, layouts híbridos)
- **Desktop Pequeno:** 1024px - 1439px (laptops, densidade média)
- **Desktop:** 1440px+ (monitores grandes, densidade máxima)

**Lógica de Adaptação:**
- **320-767px:** Single-column, bottom nav, componentes compactos
- **768-1023px:** Two-column possível, side nav opcional
- **1024px+:** Multi-column, side nav fixa, toolbars expandidas

**Mobile-First Implementation:**
- CSS desenvolvido primeiro para mobile
- Progressive enhancement para telas maiores
- Media queries apenas para otimizações, não mudanças drásticas

### Estratégia de Acessibilidade

**Conformidade WCAG 2.1 Nível AA** como padrão mínimo, com aspirações para elementos AAA onde possível. Prioridade especial para usuários com deficiências motoras e visuais, considerando o contexto de trabalho em campo.

**Requisitos Críticos:**
- **Contraste de Cor:** Mínimo 4.5:1 para texto normal, 3:1 para texto grande
- **Navegação por Teclado:** Todos os controles acessíveis via Tab
- **Screen Readers:** Suporte completo a VoiceOver (iOS), TalkBack (Android), NVDA (desktop)
- **Alvos de Toque:** Mínimo 44x44px para elementos interativos
- **Foco Visível:** Indicadores claros de foco em todos os estados

**Recursos Específicos para Rastertech:**
- **Modo Alto Contraste:** Para usuários com baixa visão em ambientes externos
- **Redução de Movimento:** Para usuários com sensibilidade vestibular
- **Zoom até 200%:** Sem perda de funcionalidade
- **Skip Links:** Navegação rápida para áreas principais
- **Landmarks ARIA:** Estrutura semântica clara (navigation, main, complementary)

**Suporte a Tecnologias Assistivas:**
- Leitores de tela para navegação por voz
- Lentes de aumento para detalhes de ativos
- Comandos de voz para ações críticas (experimental)
- Alternativas textuais para ícones e gráficos

### Estratégia de Testes

**Testes Responsivos:**
- **Dispositivos Reais:** Teste em smartphones Android/iOS reais (Samsung, iPhone, dispositivos low-end)
- **Browsers:** Chrome, Firefox, Safari, Edge (últimas 2 versões)
- **Resoluções:** Teste em breakpoints definidos + resoluções populares
- **Performance:** Teste de carregamento em conexões 3G/4G lentas

**Testes de Acessibilidade:**
- **Ferramentas Automatizadas:** WAVE, axe-core, Lighthouse Accessibility
- **Screen Readers:** Teste completo com VoiceOver, TalkBack, NVDA
- **Navegação por Teclado:** Validação de fluxos sem mouse
- **Contraste:** Verificação com simuladores de daltonismo
- **Zoom:** Teste até 200% em diferentes breakpoints

**Testes com Usuários:**
- **Usuários com Deficiências:** Sessões dedicadas com pessoas com deficiência visual/motora
- **Contexto Real:** Testes em campo com instaladores reais
- **Diversidade:** Diferentes idades, experiências técnicas, condições ambientais

**Testes Automatizados:**
- Regressão visual com Percy/Chromatic
- Testes de acessibilidade em CI/CD
- Performance monitoring contínuo

### Diretrizes de Implementação

**Desenvolvimento Responsivo:**
- **Unidades Relativas:** rem, %, vw, vh ao invés de px fixos
- **Media Queries:** Mobile-first com min-width
- **Flexibilidade:** CSS Grid e Flexbox para layouts adaptativos
- **Imagens:** Srcset e sizes para otimização automática
- **Performance:** Lazy loading e otimização de assets

**Implementação de Acessibilidade:**
- **HTML Semântico:** Uso correto de headings, landmarks, roles
- **ARIA Labels:** Descrições claras para elementos complexos
- **Focus Management:** Ordem lógica de tabulação, foco automático apropriado
- **Estados Dinâmicos:** Atualização de ARIA live regions
- **Validação:** Testes automáticos em pipeline de CI/CD

**Padrões de Código:**
- **CSS Custom Properties:** Para tokens de design acessíveis
- **Componentes Acessíveis:** Props obrigatórios para labels e descrições
- **Testes Unitários:** Cobertura de cenários de acessibilidade
- **Documentação:** Guidelines inline para desenvolvedores

**Monitoramento Contínuo:**
- **Analytics de Acessibilidade:** Rastreamento de uso de tecnologias assistivas
- **Feedback Loops:** Canal para reportar problemas de acessibilidade
- **Atualizações Regulares:** Revisão anual de conformidade WCAG