---
title: 'Ensaio Visual Sistema Rastertech'
type: 'feature'
created: '2026-03-24'
status: 'ready-for-testing'
baseline_commit: 'HEAD'
context: ['_evo-output/planning-artifacts/sistema-completo/ux-design-specification.md', '_evo-output/planning-artifacts/sistema-completo/prd.md']
---

# Ensaio Visual Sistema Rastertech

<frozen-after-approval reason="human-owned intent — do not modify unless human renegotiates">

## Intent

**Problem:** Não temos uma visualização prática do sistema-rastertech funcionando, dificultando a validação do design UX e a comunicação com stakeholders sobre como o sistema se comportará no mundo real.

**Approach:** Criar um protótipo HTML/JS interativo que simula as principais funcionalidades do sistema com dados fictícios realistas, permitindo interação e validação visual do design.

## Boundaries & Constraints

**Always:** 
- Usar dados fictícios mas realistas que representem cenários típicos de rastreamento veicular
- Implementar apenas funcionalidades críticas definidas no UX design (AssetCard, SyncStatusIndicator, OfflineFormManager)
- Seguir os padrões de design estabelecidos na especificação UX
- Garantir responsividade mobile-first

**Ask First:** 
- Adição de funcionalidades não previstas no UX design
- Mudanças significativas no layout ou interação
- Integração com bibliotecas externas não essenciais

**Never:** 
- Implementar funcionalidades backend ou persistência real
- Usar dados reais de clientes ou veículos
- Criar funcionalidades não documentadas no PRD ou UX design
- Ignorar padrões de acessibilidade definidos

## I/O & Edge-Case Matrix

| Scenario | Input / State | Expected Output / Behavior | Error Handling |
|----------|--------------|---------------------------|----------------|
| HAPPY_PATH | Usuário acessa dashboard | Exibe lista de ativos com status atualizados | N/A |
| OFFLINE_MODE | Conexão perdida durante uso | Interface mostra status offline, permite continuar | Dados salvos localmente, sincronização automática ao reconectar |
| FORM_SUBMIT | Preenchimento completo de checklist | Formulário validado, dados "enviados", confirmação visual | Campos obrigatórios destacados, mensagens específicas de erro |
| ASSET_SEARCH | Busca por placa ou IMEI | Resultados filtrados em tempo real | "Nenhum resultado encontrado" para buscas vazias |
| SYNC_ERROR | Falha na sincronização simulada | Indicador de erro, opção de retry | Retry automático após 5s, botão manual de retry |

</frozen-after-approval>

## Code Map

- `demo/index.html` -- Página principal do protótipo com estrutura HTML
- `demo/css/styles.css` -- Estilos CSS seguindo design system Material
- `demo/js/app.js` -- Lógica principal da aplicação e estado
- `demo/js/mock-data.js` -- Dados fictícios realistas
- `demo/js/components.js` -- Componentes customizados (AssetCard, etc.)

## Tasks & Acceptance

**Execution:**
- [ ] `demo/index.html` -- Criar estrutura HTML responsiva -- Base da interface do protótipo
- [ ] `demo/css/styles.css` -- Implementar estilos Material Design -- Consistência visual com especificação UX
- [ ] `demo/js/mock-data.js` -- Gerar dados fictícios realistas -- Simulação de cenários reais de rastreamento
- [ ] `demo/js/components.js` -- Criar componentes customizados -- AssetCard, SyncStatusIndicator, OfflineFormManager
- [ ] `demo/js/app.js` -- Implementar lógica de interação -- Estados offline, formulários, navegação

**Acceptance Criteria:**
- Given usuário abre o protótipo, when interage com dashboard, then vê lista de ativos com status visuais claros
- Given formulário preenchido, when submete offline, then dados são "salvos" e sincronização simulada funciona
- Given busca por ativo, when digita placa, then resultados aparecem em tempo real
- Given conexão perdida, when continua uso, then interface mostra status offline e permite continuar
- Given protótipo carregado, when redimensiona tela, then layout se adapta responsivamente

## Design Notes

O protótipo seguirá estritamente os padrões definidos na especificação UX:
- Hierarquia de botões: primário (azul), secundário (outlined), terciário (text)
- Feedback consistente: snackbars para ações, banners para estados importantes
- Componentes customizados implementados como especificado
- Navegação mobile-first com bottom navigation
- Estados offline simulados com indicadores visuais claros

## Verification

**Commands:**
- `cd demo && python -m http.server 8000` -- expected: Servidor inicia sem erros
- `open http://localhost:8000` -- expected: Protótipo carrega e funciona interativamente

**Manual checks:**
- Verificar responsividade em diferentes tamanhos de tela
- Testar navegação por teclado e screen reader básico
- Validar que dados fictícios parecem realistas
- Confirmar que interações seguem padrões UX definidos

---

## Spec Change Log

### Iteration 1 (2026-03-24) — Post-Review Patches Applied

**Trigger:** Step 04 Adversarial Review identified two trivial patches affecting code robustness.

**Amendments:**
1. **demo/js/components.js** — Enhanced form validation error clearing. Pre-clear all error borders before validation to prevent stale visual state from prior attempts.
   - **What was amended:** OfflineFormManager.validate() method
   - **Known-bad state avoided:** Form field error borders remaining red after successful validation attempt
   - **KEEP instruction:** Validation still properly highlights required empty fields immediately

2. **demo/js/app.js** — Added error handling and guards in component initialization.
   - **What was amended:** initializeUI() with try-catch and osFormManager null-check; submitServiceOrder() with guard clause
   - **Known-bad state avoided:** Silent failures if DOM elements not found; form submission errors when osFormManager doesn't exist
   - **KEEP instruction:** All components still initialize correctly; graceful degradation when optional elements missing

**Findings Classification:** 2 patches (trivial), 4 deferred (pre-existing), 0 intent gaps, 0 bad specs. Approved for testing.

