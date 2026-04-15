# 🚜 Dossiê Técnico — Módulo de Missões e Jornadas
**Sistema Rastertech** | Atualizado em: 15/04/2026 | Antigravity AI (pair programming com Ubiratan)

---

## 1. Visão Geral do Módulo

O módulo de **Jornadas** controla o ciclo operacional de um veículo: saída (check-in), uso em campo e retorno (checkout). É composto por três entidades conectadas:

```
portal_drivers  ──┐
vehicles        ──┼──▶  vehicle_checklists  ──▶  vehicle_missions
customers       ──┘         (entry / exit)         (pivô unificador)
```

### Entidades Envolvidas

| Tabela | Papel |
|--------|-------|
| `portal_drivers` | Motorista que realiza a jornada |
| `vehicles` | Veículo utilizado na jornada |
| `vehicle_checklists` | Registros individuais de check-in (`type=entry`) e checkout (`type=exit`) com odômetro, fotos, notas |
| `vehicle_missions` | **Pivô** que une o par entry+exit, calcula status e deslocamento |

---

## 2. Arquitetura da Tabela `vehicle_missions`

```sql
vehicle_missions:
  id             — PK
  customer_id    — FK customers (multi-tenancy)
  vehicle_id     — FK vehicles
  driver_id      — FK portal_drivers (motorista que INICIOU a jornada)
  entry_id       — FK vehicle_checklists (nullable) — checklist do check-in
  exit_id        — FK vehicle_checklists (nullable) — checklist do checkout
  status         — enum: 'open' | 'closed'
  created_at / updated_at
```

### Regras de negócio:
- Uma missão é criada **no momento do check-in** com `entry_id` preenchido e `status = open`
- A missão é fechada **no momento do checkout** com `exit_id` preenchido e `status = closed`
- Um veículo **não pode ter duas missões abertas** simultaneamente
- Deslocamento = `exit.odometer - entry.odometer`
- Duração = `exit.created_at - entry.created_at`

---

## 3. Papéis de Usuário (Roles)

O sistema tem dois perfis que interagem com jornadas:

| Role | Comportamento |
|------|---------------|
| **Motorista** (`driver`) | Vinculado a um `PortalDriver` via `CustomerSubUser`. Só vê e cria jornadas próprias. |
| **Supervisor/Operador** (`admin`, `gestor`, `operator`, `Gerente`, `Administrador`, `Gestor de Operações`) | Vê todas as jornadas do cliente. Pode fazer check-in e checkout por qualquer motorista/veículo. |

Detecção de papel no controller:
```php
$supervisorRoles = ['admin', 'gestor', 'operator', 'Gerente', 'Administrador', 'Gestor de Operações'];
$isSupervisor = in_array($user->role, $supervisorRoles);
```

---

## 4. Fluxo de Dados — Sequência Operacional

### 4.1 Check-in (entry)
**Controller:** `CustomerPortalController::storeChecklistAction()`

```
1. Valida: vehicle_id, driver_id, odometer, notes, photos (5 obrigatórias)
2. Valida odômetro: deve ser igual ao último checkout (tolerância para supervisores)
3. Salva VehicleChecklist com type='entry'
4. Atualiza Vehicle (is_locked=true)
5. Atualiza PortalDriver (last_checklist_at)
6. Cria VehicleMission: entry_id=checklist.id, status='open'
```

### 4.2 Checkout (exit)
**Controller:** `CustomerPortalController::storeChecklistAction()`

```
1. Valida: vehicle_id, driver_id, odometer, notes
   - Se supervisor sem fotos: notes mínimo 30 caracteres
   - Se motorista: fotos obrigatórias (5)
2. Valida odômetro: deve ser >= último check-in
3. Salva VehicleChecklist com type='exit'
4. Atualiza Vehicle (is_locked=false)
5. Atualiza PortalDriver (last_checklist_at)
6. Busca missão aberta: WHERE vehicle_id=? AND status='open'
   - Se encontrada: atualiza exit_id e status='closed'
   - Se NÃO encontrada (checkout administrativo): cria missão nova já closed com entry_id=null
```

> ⚠️ **ATENÇÃO**: A missão fantasia com `entry_id=null` e `status=closed` é o caminho de fallback para checkouts administrativos legítimos (sem check-in prévio). A view trata isso exibindo "Baixa Administrativa".

---

## 5. Bugs Corrigidos em 15/04/2026

### 🐛 Bug #1 — Missão não fechada no checkout (customer_id mismatch)

**Arquivo:** `app/Http/Controllers/Portal/CustomerPortalController.php`  
**Método:** `storeChecklistAction()`  
**Sintoma:** Após o checkout, a listagem ainda exibia "EM CAMPO" ao invés de "FINALIZADO".

**Causa:** A query que buscava a missão aberta filtrava por `customer_id`:
```php
// ❌ ANTES — filtro duplo causava missão não encontrada
$openMission = VehicleMission::where('vehicle_id', $request->vehicle_id)
    ->where('customer_id', $vehicle->customer_id)  // ← bug
    ->where('status', 'open')
    ->first();
```
Quando o operador (com `customer_id` diferente do motorista) fazia o checkout, o `customer_id` da query era o do veículo, mas a missão foi criada com o `customer_id` do motorista — causando mismatch.

**Correção:**
```php
// ✅ DEPOIS — vehicle_id + status é suficiente e único
$openMission = VehicleMission::where('vehicle_id', $request->vehicle_id)
    ->where('status', 'open')
    ->orderBy('created_at', 'desc')
    ->first();
```

---

### 🐛 Bug #2 — Checkout do operador trocava o veículo (vehicle_id ignorado)

**Arquivo:** `app/Http/Controllers/Portal/CustomerPortalController.php`  
**Método:** `createChecklist()`  
**Sintoma:** Ao clicar em "CHECKOUT" na tela de show de um check-in (ex: RTH-2026), o formulário abria com RTH-2028 (o veículo com o entry mais recente no banco).

**Causa:** Para supervisores, a query não filtrava por veículo:
```php
// ❌ ANTES — supervisor pegava o entry mais recente de QUALQUER veículo
$exitQuery = VehicleChecklist::where('type', 'entry');
if (!$isSupervisor) {
    $exitQuery->where('driver_id', $driver?->id);
}
// sem filtro de vehicle_id para supervisor!
$activeJourney = $exitQuery->orderBy('created_at', 'desc')->first();
```

O botão CHECKOUT na `show.blade.php` já passava `vehicle_id` na URL, mas o controller ignorava.

**Correção:**
```php
// ✅ DEPOIS — vehicle_id da request tem prioridade máxima
if ($request->filled('vehicle_id')) {
    $exitQuery->where('vehicle_id', $request->vehicle_id);
} elseif (!$isSupervisor) {
    $exitQuery->where('driver_id', $driver?->id);
}
```

---

### 🐛 Bug #3 — Coluna "DESLOCAMENTO" mostrava "EM CAMPO" para missões fechadas sem entry

**Arquivo:** `resources/views/portal/verificacoes/index.blade.php`  
**Sintoma:** Missões com `entry_id=null` e `status=closed` (checkout administrativo) exibiam "EM CAMPO" ao invés de "FINALIZADO".

**Causa:** A condição da view checava ambos os relacionamentos:
```php
// ❌ ANTES — falha se entry_id=null
@if($mission->entryChecklist && $mission->exitChecklist)
    // FINALIZADO
@else
    // EM CAMPO ← aparecia mesmo com status=closed
@endif
```

**Correção:** Adicionado `@elseif` para tratar o caso de baixa administrativa:
```php
// ✅ DEPOIS
@if($mission->entryChecklist && $mission->exitChecklist)
    // FINALIZADO com KM e duração calculados
@elseif($mission->status === 'closed')
    // FINALIZADO — Baixa Administrativa (sem KM)
@else
    // EM CAMPO
@endif
```

---

## 6. Features Adicionadas em 15/04/2026

### ✅ Feature #1 — Navegação Bidirecional entre Check-in e Checkout

**Arquivo:** `resources/views/portal/verificacoes/show.blade.php`

Na tela de detalhes de um checklist, agora aparecem botões contextuais:

| Situação | Botões |
|----------|--------|
| Check-in com jornada **ativa** | `[CHECKOUT]` + badge `EM JORNADA ATIVA` |
| Check-in com jornada **finalizada** | `[Ver Checkout →]` + badge `JORNADA FINALIZADA` (clicável) |
| Checkout | `[← Ver Check-in]` + badge `JORNADA FINALIZADA` (clicável) |

**Lógica:**
```php
// Encontra a missão vinculada ao checklist (por entry_id ou exit_id)
$mission = \App\Models\VehicleMission::where('entry_id', $checklist->id)
    ->orWhere('exit_id', $checklist->id)
    ->first();

// ID do checklist "par"
$pairedChecklistId = $isEntry ? $mission->exit_id : $mission->entry_id;
```

**Badge "JORNADA FINALIZADA"** é agora um link clicável que volta para `/portal/verificacoes` sem alterar o visual.

---

## 7. Correção de Dados — Incidente 15/04/2026

Devido à combinação dos Bugs #1 e #2, foi gerado um cenário de dados corrompidos:

**O que ocorreu:**
1. Motorista fez check-in no RTH-2026 → Missão #5 criada (entry_id=6, status=open)
2. Operador clicou em CHECKOUT no RTH-2026 → **Bug #2** redirecionou formulário para RTH-2028
3. Operador enviou o formulário → checkout salvo para RTH-2028 (errado) → Checklist #9 criado
4. **Bug #1** impediu que Missão #5 fosse encontrada → Missão #7 criada como fallback (entry_id=null, exit_id=9)
5. Operador repetiu o checkout corretamente (após correções) → Missão #5 fechada com exit_id=10 ✅

**Registros fantasma deletados:**
```php
App\Models\VehicleMission::find(7)->delete();   // Missão fantasma RTH-2028
App\Models\VehicleChecklist::find(9)->delete(); // Checkout errado (RTH-2028)
```

**Estado final limpo:**
- RTH-2026: Missão #5 → entry_id=6, exit_id=10, status=closed ✅
- RTH-2028: Missão #6 → entry_id=7, exit_id=8, status=closed ✅

---

## 8. Arquivos Modificados nesta Sessão

| Arquivo | Tipo | O que mudou |
|---------|------|------------|
| `app/Http/Controllers/Portal/CustomerPortalController.php` | Controller | Bug #1: removido `customer_id` do filtro de missão aberta no checkout |
| `app/Http/Controllers/Portal/CustomerPortalController.php` | Controller | Bug #2: adicionado `vehicle_id` como filtro prioritário no `createChecklist` |
| `resources/views/portal/verificacoes/index.blade.php` | View | Bug #3: adicionado `@elseif($mission->status === 'closed')` na coluna DESLOCAMENTO |
| `resources/views/portal/verificacoes/show.blade.php` | View | Feature: navegação bidirecional check-in ↔ checkout + badge clicável |

---

## 9. Pendências e Próximos Passos

| Prioridade | Tarefa |
|-----------|--------|
| 🔴 Alta | Criar **Painel de Missões Administrativo** (rota `/missoes`, controller, view) — listagem unificada com filtros por cliente/veículo/motorista/status |
| 🟡 Média | Implementar **alertas de CNH** — SMS automático para motoristas com CNH próxima ao vencimento |
| 🟡 Média | Criar **Seeders** — banco vazio no install limpo, sem dados de demonstração |
| 🟢 Baixa | Implementar **testes automatizados** — especialmente para o fluxo missão: check-in → checkout → fechamento |

---

## 10. Padrão de URL do Portal

> ⚠️ **ATENÇÃO para agentes IA**: Não usar o helper `route()` para links internos do portal. Usar caminhos relativos.

```
/portal/verificacoes            → listagem de missões
/portal/verificacoes/nova/entry → formulário de check-in
/portal/verificacoes/nova/exit  → formulário de checkout (aceita ?vehicle_id=X)
/portal/verificacoes/{id}       → detalhes de um checklist individual
/portal/despesas                → listagem de despesas
/portal/instalador              → portal do instalador
```
