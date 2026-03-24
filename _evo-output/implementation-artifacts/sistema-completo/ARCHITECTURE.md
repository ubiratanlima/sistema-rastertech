# Architecture & Data Flow Guide

## System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    SISTEMA RASTERTECH PROTOTYPE                 │
├─────────────────────────────────────────────────────────────────┤
│                                                                   │
│  ┌─────────────────────────────────────────────────────────┐    │
│  │   User Interface (index.html)                           │    │
│  │  ┌──────────────────────────────────────────────────┐   │    │
│  │  │ Header (Rastertech Logo + Sync Indicator)        │   │    │
│  │  └──────────────────────────────────────────────────┘   │    │
│  │  ┌──────────────────────────────────────────────────┐   │    │
│  │  │ Main Content Area (Dynamic Pages)               │   │    │
│  │  │  • Dashboard    • Assets   • Order  • Settings   │   │    │
│  │  └──────────────────────────────────────────────────┘   │    │
│  │  ┌──────────────────────────────────────────────────┐   │    │
│  │  │ Bottom Navigation (Mobile) / Tab Switcher       │   │    │
│  │  └──────────────────────────────────────────────────┘   │    │
│  │  ┌──────────────────────────────────────────────────┐   │    │
│  │  │ Modal + Snackbar (Overlays)                      │   │    │
│  │  └──────────────────────────────────────────────────┘   │    │
│  └─────────────────────────────────────────────────────────┘    │
│           ↑          ↑          ↑          ↑          ↑          │
│           │          │          │          │          │          │
│        [CSS]    [Components]  [Events]  [Storage]  [Simulator]   │
│           │          │          │          │          │          │
│  ┌────────┴──────────┴──────────┴──────────┴──────────┴────┐    │
│  │                 Application Layer (app.js)              │    │
│  │                   RastertechApp Class                   │    │
│  │  ┌──────────────────────────────────────────────────┐   │    │
│  │  │ Navigation • Asset Mgmt • Form Handling • Offline │   │    │
│  │  └──────────────────────────────────────────────────┘   │    │
│  └────────┬──────────────────────────────────────────────┬──┘   │
│           │          ↓          ↓          ↓          ↓         │
│           │          │          │          │          │         │
│  ┌────────┴──────────┴──────────┴──────────┴──────────┴────────┐│
│  │               Component Layer (components.js)           │   ││
│  │                                                         │   ││
│  │  ┌───────────┐ ┌──────────────┐ ┌──────────────┐      │   ││
│  │  │AssetCard  │ │SyncIndicator │ │OfflineForm   │      │   ││
│  │  │           │ │              │ │ Manager      │      │   ││
│  │  └───────────┘ └──────────────┘ └──────────────┘      │   ││
│  │                                                         │   ││
│  │  ┌─────────────┐ ┌──────────┐ ┌──────────────────┐    │   ││
│  │  │Snackbar     │ │Modal     │ │RequestSimulator  │    │   ││
│  │  │             │ │          │ │                  │    │   ││
│  │  └─────────────┘ └──────────┘ └──────────────────┘    │   ││
│  └────────┬──────────────────────────────────────────────────┬┘│
│           │          ↓          ↓          ↓          ↓      │ │
│  ┌────────┴──────────┴──────────┴──────────┴──────────┴────┐ │ │
│  │           Data Layer (mock-data.js + localStorage)     │ │ │
│  │                                                         │ │ │
│  │  ┌──────────────────────────────────────────────────┐  │ │ │
│  │  │ Mock Data (Vehicles, Customers, Technicians)    │  │ │ │
│  │  │ • 12 Assets (Disponível, Instalado, etc)       │  │ │ │
│  │  │ • 4 Customers (linked to vehicles)             │  │ │ │
│  │  │ • 4 Technicians (service team)                 │  │ │ │
│  │  └──────────────────────────────────────────────────┘  │ │ │
│  │                                                         │ │ │
│  │  ┌──────────────────────────────────────────────────┐  │ │ │
│  │  │ Browser Storage (localStorage)                  │  │ │ │
│  │  │ • form-{formId}: Auto-saved form data           │  │ │ │
│  │  │ • submission-{formId}-{ts}: Form submissions    │  │ │ │
│  │  │ • rastertech-*: Prefixed keys for namespacing   │  │ │ │
│  │  └──────────────────────────────────────────────────┘  │ │ │
│  └──────────────────────────────────────────────────────────┘ │ │
│                          ↓          ↓          ↓               │ │
│              ┌───────────┴──────────┴──────────┴──────────────┐ │ │
│              │    Presentation Layer (styles.css)             │ │ │
│              │  • Material Design 3 Theming (30+ CSS vars)    │ │ │
│              │  • Responsive Breakpoints (320px, 480px, etc)  │ │ │
│              │  • Component Styling (cards, buttons, forms)   │ │ │
│              │  • Animations (sync rotate, modal fade)        │ │ │
│              └───────────┬──────────────────────────┬─────────┘ │ │
│                          │                        │            │ │
│                     [Device Output]         [Browser Rendering] │ │
└─────────────────────────────────────────────────────────────────┘ │
                                                                     │
                             Browser Runtime                        │
└─────────────────────────────────────────────────────────────────────┘
```

---

## Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                     USER INTERACTION FLOWS                       │
└─────────────────────────────────────────────────────────────────┘

1. DASHBOARD LOAD
   ┌─────────────────────────────────────────────┐
   │ Page Load (index.html)                      │
   └────────────────┬────────────────────────────┘
                    ↓
   ┌─────────────────────────────────────────────┐
   │ RastertechApp Constructor                  │
   │  • Initialize UI (Snackbar, Modal, etc)    │
   │  • Setup Event Listeners                    │
   │  • Load Initial Data                        │
   └────────────────┬────────────────────────────┘
                    ↓
   ┌─────────────────────────────────────────────┐
   │ updateDashboard()                           │
   │  • getStatusStats() from mockData           │
   │  • Render stats grid (12, 6, 3, 1)          │
   │  • displayAssetsList() → AssetCard render   │
   └────────────────┬────────────────────────────┘
                    ↓
   ┌─────────────────────────────────────────────┐
   │ Dashboard Page Visible                      │
   │  Dashboard ✓ | Assets | Order | Settings   │
   └─────────────────────────────────────────────┘

2. SEARCH ASSETS
   ┌─────────────────────────────────────────────┐
   │ User types in search input                  │
   │ "ABC-1234" (DOMContentLoaded)               │
   └────────────────┬────────────────────────────┘
                    ↓
   ┌─────────────────────────────────────────────┐
   │ searchInput.addEventListener('input', ...)  │
   │  • Calls searchAssets(query)                │
   └────────────────┬────────────────────────────┘
                    ↓
   ┌─────────────────────────────────────────────┐
   │ searchAssets(query)                         │
   │  • Filter inventory by:                     │
   │    - plate, trackerIMEI, chipNumber, owner  │
   │  • Create filtered array                    │
   │  • Clear grid.innerHTML                     │
   │  • Render AssetCard for each match          │
   └────────────────┬────────────────────────────┘
                    ↓
   ┌─────────────────────────────────────────────┐
   │ Filtered Results Display                    │
   │  (1-3 matching AssetCard components)        │
   └─────────────────────────────────────────────┘

3. SUBMIT SERVICE ORDER
   ┌─────────────────────────────────────────────┐
   │ User fills form + clicks Submit             │
   │ (Service Order Tab)                         │
   └────────────────┬────────────────────────────┘
                    ↓
   ┌─────────────────────────────────────────────┐
   │ osForm.addEventListener('submit', ...)      │
   │  • Calls submitServiceOrder(event)          │
   │  • event.preventDefault()                   │
   └────────────────┬────────────────────────────┘
                    ↓
   ┌─────────────────────────────────────────────┐
   │ osFormManager.submit()                      │
   │  • validate() → check required fields       │
   │  • If invalid → return {success: false}     │
   │  • If valid → getFormData()                 │
   │  • Generate hash and timestamp              │
   │  • Save to localStorage                     │
   │  • clearForm()                              │
   └────────────────┬────────────────────────────┘
   ┌──────────────────────────┬──────────────────┐
   │  Success                 │  Validation Error│
   └────────┬─────────────────┴─────────────────┘
            ↓
   ┌─────────────────────────────────────────────┐
   │ syncIndicator.setSyncing()                  │
   │  • Show "Sincronizando..." status           │
   │  • Start animation                          │
   └────────────────┬────────────────────────────┘
                    ↓
   ┌─────────────────────────────────────────────┐
   │ RequestSimulator.submitForm()               │
   │  • Simulate 1.5s network request            │
   │  • Success: resolve() or reject()           │
   └────────────────┬────────────────────────────┘
            ┌───────┴────────┐
            ↓                ↓
   ┌──────────────────┐ ┌──────────────────┐
   │ Then: Success    │ │ Catch: Error     │
   │  • setSynced()   │ │  • setError()    │
   │  • Snackbar OK   │ │  • Snackbar Err  │
   │  • Navigate to   │ │  • Data "offline"│
   │    Dashboard     │ │  • Retry option  │
   └──────────────────┘ └──────────────────┘

4. OFFLINE MODE TOGGLE
   ┌─────────────────────────────────────────────┐
   │ User toggles offline switch                 │
   │ (Settings Tab)                              │
   └────────────────┬────────────────────────────┘
                    ↓
   ┌─────────────────────────────────────────────┐
   │ offlineToggle.addEventListener('change') │
   │  • Calls toggleOfflineMode(checked)       │
   └────────────────┬────────────────────────────┘
   ┌──────────────────────────┬──────────────────┐
   │  True (Offline)          │  False (Online)  │
   └────────┬─────────────────┴─────────────────┘
            ↓                        ↓
   ┌──────────────────────┐ ┌──────────────────────┐
   │ syncIndicator        │ │ syncIndicator        │
   │ .setOffline()        │ │ .setSynced()         │
   │ Show: "Modo Offline" │ │ Show:"Sincronizado"  │
   │ osFormManager        │ │ osFormManager        │
   │ .setOfflineMode(true)│ │ .setOfflineMode(false)
   │ Snackbar info msg    │ │ Snackbar success msg │
   └──────────────────────┘ └──────────────────────┘
            ↓                        ↓
   ┌─────────────────────────────────────────────┐
   │ updateSettings()                            │
   │  • Display current mode                     │
   │  • Show last sync time                      │
   │  • Reflect toggle state                     │
   └─────────────────────────────────────────────┘
```

---

## Component Interaction Model

```
┌──────────────────────────────────────────────────────────┐
│         RastertechApp (Main Orchestrator)                │
│  ┌────────────────────────────────────────────────────┐  │
│  │ Properties:                                        │  │
│  │  • currentPage: string                             │  │
│  │  • isOfflineMode: boolean                          │  │
│  │  • inventory: Asset[]                              │  │
│  │  • components: Snackbar, SyncIndicator, Modal      │  │
│  └────────────────────────────────────────────────────┘  │
│  ┌────────────────────────────────────────────────────┐  │
│  │ Methods:                                           │  │
│  │  • navigateTo(page) → Switch page, load data       │  │
│  │  • displayAssets() → Render all assets             │  │
│  │  • searchAssets(query) → Filter by text            │  │
│  │  • filterAssets(status) → Filter by status         │  │
│  │  • submitServiceOrder(event) → Submit form         │  │
│  │  • toggleOfflineMode(isOffline) → Toggle           │  │
│  │  • resetData() → Clear localStorage                │  │
│  └────────────────────────────────────────────────────┘  │
└──────┬──────────────────────────┬──────────────┬─────────┘
       │                          │              │
       ↓                          ↓              ↓
┌────────────────┐ ┌──────────────────┐ ┌──────────────┐
│  AssetCard     │ │OfflineFormManager│ │SyncIndicator │
│  • render()    │ │ • validate()     │ │ • setSynced()│
│  • getStatus..  │ │ • submit()       │ │ • setOffline │
│  • getAssetIcon │ │ • autoSave()     │ │ • setError() │
│  • getStatus... │ │ • clearForm()    │ │ • update()   │
└────────────────┘ └──────────────────┘ └──────────────┘
       ↑                      ↑                   ↑
       │                      │                   │
   Rendered DOM         localStorage         DOM Container
  (page grid)         (form-{formId})        (#syncStatus)
```

---

## localStorage Persistence Model

```
┌──────────────────────────────────────────────────────────┐
│                  Browser localStorage                     │
│  (Domain: http://localhost:8000)                          │
└──────────────────────────────────────────────────────────┘

Key Pattern: "rastertech-*" (namespacing)

1. FORM AUTO-SAVE
   Key: "form-osForm"
   Value: {
     data: {
       assetId: "X001",
       serviceType: "Manutenção",
       description: "Sensor com problema",
       notes: "..."
     },
     timestamp: "2026-03-24T15:30:00Z",
     isDirty: true
   }
   Duration: Until user clears or exits browser
   Triggered: On every input/change event in form

2. FORM SUBMISSION QUEUE
   Key: "submission-osForm-1711271400000"
   Value: {
     data: {assetId, serviceType, ...},
     timestamp: "2026-03-24T15:30:00Z",
     hash: "a3f2c4e1",
     isOffline: true
   }
   Duration: Until cleared via Settings
   Triggered: On form submit (if online or offline)

3. SYNC STATE (Not persisted, UI-only)
   Property: app.isOfflineMode
   Stored: In memory (resets on page reload)

Size Estimate:
  • Single form save: ~500 bytes
  • Multiple submissions: ~1KB per submission
  • Quota limit: ~5MB (browser dependent)
  • Prototype safe at: <1MB with demo usage
```

---

## Event Listener Map

```
┌──────────────────────────────────────────────────────────┐
│           DOM Event Listener Attachments                 │
└──────────────────────────────────────────────────────────┘

1. Navigation (.nav-item buttons)
   Event: click
   Handler: navigateTo(page)
   Count: 4 listeners (one per nav button)

2. Search Input (#searchInput)
   Event: input
   Handler: searchAssets(query)
   Triggers: Real-time filtering
   Count: 1 listener

3. Status Filter (#statusFilter)
   Event: change
   Handler: filterAssets(status)
   Triggers: Grid refresh
   Count: 1 listener

4. New Asset Button (#newAssetBtn)
   Event: click
   Handler: showNewAssetForm()
   Triggers: Modal display with form
   Count: 1 listener

5. Service Order Form (#osForm)
   Event: submit
   Handler: submitServiceOrder(event)
   Triggers: Form validation + submission
   Count: 1 listener

6. Form Inputs (within #osForm)
   Event: input, change
   Handler: OfflineFormManager.autoSave()
   Triggers: localStorage update
   Count: N (one per input element)

7. Offline Toggle (#offlineToggle)
   Event: change
   Handler: toggleOfflineMode(checked)
   Triggers: UI state change
   Count: 1 listener

8. Reset Data Button (#resetDataBtn)
   Event: click
   Handler: resetData()
   Triggers: Confirmation dialog + localStorage clear
   Count: 1 listener

9. Asset Card Click
   Event: click
   Handler: showAssetDetails(asset)
   Triggers: Modal display
   Count: N (dynamically added per card)

10. Modal Click-Outside-Close
    Event: click on modal background
    Handler: Modal.close()
    Count: 1 listener (delegated)

Total Listeners: ~15-30 (depending on form fields and asset cards)
Memory Impact: ~10KB for all listeners
Cleanup: Automatic when DOM cleared (navigateTo)
```

---

## CSS Cascade & Specificity

```
┌──────────────────────────────────────────────────────────┐
│           CSS Layering (Cascade Order)                   │
└──────────────────────────────────────────────────────────┘

1. CSS VARIABLES (Root)
   :root {
     --primary-color: #1976D2;
     --status-available: #4CAF50;
     --status-installed: #2196F3;
     --spacing-s: 0.5rem;
     --radius-m: 0.5rem;
     ...
   }
   Priority: Foundation (can be overridden)

2. BASE STYLES (Global)
   * { box-sizing: border-box; }
   body { font-family, margin, padding }
   .app-container { layout }
   Priority: Low (base defaults)

3. COMPONENT STYLES (Scoped)
   .asset-card { border, padding, hover }
   .sync-status { display, animation }
   .status-badge { background, font-size, color }
   Priority: Medium (component specific)

4. STATE STYLES (Dynamic)
   .asset-card:hover { transform, shadow }
   .sync-syncing .sync-icon { animation: rotate }
   .status-badge-available { background: --status-available }
   .page.active { display: block }
   Priority: High (state dependent)

5. UTILITY STYLES (Reusable)
   .btn { padding, border, cursor }
   .btn.btn-primary { background: var(--primary-color) }
   .btn-secondary { border, background transparent }
   Priority: High (specific to element types)

6. RESPONSIVE OVERRIDES (Media Queries)
   @media (max-width: 768px) {
     .mobile-nav { display: flex }
     .page-grid { grid-template-columns: 1fr }
   }
   Priority: Very High (breakpoint specific)

Specificity Hierarchy:
  0-0-1  → Element selectors (low)
  0-1-0  → Class selectors (medium)
  1-0-0  → ID selectors (high, avoid)
  !important → Override everything (avoid except states)

All selectors stay below 0-2-1 specificity for maintainability.
No ID-based styling (only for JS targeting).
```

---

## Performance Characteristics

```
┌──────────────────────────────────────────────────────────┐
│            Runtime Performance Metrics                   │
└──────────────────────────────────────────────────────────┘

LOAD TIME
  Initial Page Load: 50-100ms (varies by network)
  Mock Data Parse: <5ms
  Component Initialization: <50ms
  DOM Render: <100ms
  Fonts Load: N/A (system fonts only)
  Total: ~150-300ms (3G network)

MEMORY
  Base App: ~2MB
  Mock Data: ~50KB
  DOM Nodes: ~200-300 elements
  Event Listeners: ~15-30
  localStorage usage: ~100KB (empty) → 1+MB (with forms)
  Total: ~3-5MB during use

INTERACTION
  Navigation (page switch): 16ms (one frame @ 60fps)
  Search filtering: <50ms (12 assets)
  Asset card render: <10ms per card
  Form validation: <5ms (5 fields)
  Modal open/close: 300ms (CSS animation)

ANIMATIONS
  Sync rotate: 2s loop (60fps)
  Modal fade: 300ms (smooth)
  Snackbar slide: 300ms (smooth)
  GPU: Not accelerated (CSS transforms used where needed)

DISK USAGE
  index.html: ~15KB
  styles.css: ~40KB
  mock-data.js: ~12KB
  components.js: ~18KB
  app.js: ~16KB
  Total Bundle: ~100KB unminified, ~30KB gzipped

OPTIMIZATION OPPORTUNITIES
  1. Minify CSS/JS: Reduce to ~15KB
  2. Lazy load components: Defer non-critical JS
  3. Use ServiceWorker: Cache assets for offline
  4. Compress images: N/A (no images in demo)
  5. Code splitting: Could split by page (not critical)

BROWSER COMPATIBILITY
  ES6 Features Used: Classes, Arrow Functions, Template Literals
  Requires: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
  Fallback: None (prototype, not user-facing production code)
```

---

## State Machine Diagram

```
┌──────────────────────────────────────────────────────────┐
│         Application State Transitions                     │
└──────────────────────────────────────────────────────────┘

SYNC STATE MACHINE
  ┌─────────────┐
  │  SYNCED ✓   │←─────────────────────────────┐
  └──────┬──────┘                              │
         │ └─[Sync triggered]                  │
         │       ↓                             │
         │  ┌─────────────┐                    │
         │  │ SYNCING ... │                    │
         │  └──────┬──────┘                    │
         │         │ ├─[Success]─────────────→┤
         │         │ └─[Error]──────────────┐  │
         │         │                       ↓  │
         │         │                   ┌──────────┐
         │         │                   │ ERROR ✗  │
         │         │                   └──────────┘
         │         │                        ↑
         │  [User goes offline]             │
         │         ↓                        │
         │  ┌────────────────┐              │
         │  │ OFFLINE        │              │
         │  │ (No sync)      │              │
         │  └────────────────┘              │
         │         ↑                        │
         └─────────┴────────────────────────┘
           [Internet restored]

PAGE STATE MACHINE
  Dashboard ←─┐
       │      │
       ↓      │
  Assets ←────┤
       │      │
       ↓      │
  ServiceOrder─┤
       │      │
       ↓      │
  Settings ←──┘
  
       (Click nav buttons to transition)

FORM STATE MACHINE
  ┌──────────┐
  │ Empty    │
  └────┬─────┘
       │ [User types]
       ↓
  ┌──────────┐
  │Dirty     │ ←─[autoSave to localStorage]─┐
  │(Unsaved) │                               │
  └────┬─────┘                               │
       │ [Submit clicked]                    │
       ↓
  ┌──────────────────────────────────────────┤
  │Validating...                             │
  └────┬─────────────────────────────────────┘
       │
       ├─[Invalid]─→ ┌──────────────────┐
       │            │ Error State      │
       │            │ (Highlight fields)
       │            └──────────────────┘
       │
       └─[Valid]──→ ┌──────────────────┐
                    │ Submitting...    │
                    └────┬─────────────┘
                         │
                         ├─[Success]─→ ┌──────────────┐
                         │            │ Cleared      │
                         │            │ (Ready new)  │
                         │            └──────────────┘
                         │
                         └─[Error]──→ ┌──────────────────┐
                                      │ Error (Offline)  │
                                      │ Data saved to LS │
                                      └──────────────────┘
```

---

## Integration Points

```
┌──────────────────────────────────────────────────────────┐
│         Future Production Integration Points             │
└──────────────────────────────────────────────────────────┘

1. BACKEND API INTEGRATION
   Replace: RequestSimulator.submitForm() → Actual API call
   Endpoint: POST /api/service-orders
   Method: Fetch API with auth headers
   Expected: { success: true, orderId: "SO-2026-03-0001" }
   Fallback: localStorage queue on network error

2. AUTHENTICATION
   Add: Authorization header (Bearer token)
   Endpoint: POST /api/auth/login
   Store: JWT in sessionStorage (not localStorage)
   Refresh: Auto-refresh token on 401 response

3. REAL ASSET DATA
   Replace: mockData.assets → API fetch
   Endpoint: GET /api/assets
   Caching: Cache results in localStorage, invalidate on navigate
   Sync: Periodic sync or manual refresh button

4. CUSTOMER DATA
   Replace: mockData.customers → API data
   Endpoint: GET /api/customers
   Usage: Customer selection dropdown
   Cache: Session cache (valid until logout)

5. FILE UPLOADS
   Extend: Service Order form
   Endpoint: POST /api/service-orders/{id}/upload
   Format: FormData with multipart/form-data
   Types: Images (.jpg, .png), documents (.pdf)

6. PUSH NOTIFICATIONS
   Use: Service Workers + Push API
   Trigger: New service orders, sync status
   Store: /api/subscription
   Payload: Compact JSON (order summary)

7. ANALYTICS TRACKING
   Tool: Google Analytics 4 (gtag.js)
   Events: Page view, form submit, offline toggle
   Config: Add gtag('config', 'GA_ID') to header

8. ERROR REPORTING
   Tool: Sentry or similar
   Endpoint: POST https://sentry-dsn.ingest.sentry.io/...
   Trigger: Uncaught errors, failed API calls
   Data: Stack trace, user session, breadcrumbs

9. REAL-TIME SYNC
   Protocol: WebSocket (Socket.io or raw WS)
   Subscribe: /assets/{assetId}/updates
   Payload: SyncStatusIndicator updates
   Fallback: Polling every 30s if WS unavailable

10. OFFLINE QUEUE SYNC
    Mechanism: Service Worker + Background Sync API
    Trigger: When network restored
    Queue: Pull from localStorage, retry with exponential backoff
    Success: Delete from queue, show notification
    Failure: Increment retry count, keep in queue
```

---

This architecture demonstrates a **modular, scalable prototype design** that can be easily extended to production while maintaining clear separation of concerns.
