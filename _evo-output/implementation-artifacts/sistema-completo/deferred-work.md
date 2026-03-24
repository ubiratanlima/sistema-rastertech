# Deferred Work — Sistema Rastertech Prototype

**Created:** 2026-03-24  
**Source:** Step 04 Adversarial Review — Finding Classification Phase

---

## Deferred Items (Pre-existing, not caused by this change)

### D1: localStorage Quota Management
**Severity:** Medium  
**Component:** demo/js/mock-data.js, saveToLocalStorage() function  
**Description:** No active monitoring or cleanup of localStorage usage. Multiple form submissions accumulate data. Browser quota varies (5-10MB typical), but no fallback strategy exists.

**Impact:** Prototype will fail silently once quota exceeded. Users cannot submit forms offline.

**Recommended Solution:**
- Implement quota check before save: `navigator.storage.estimate()`
- Auto-cleanup old submissions after 30 days
- Fall back to sessionStorage or in-memory queue if localStorage full
- Display warning when usage exceeds 80%

**Estimated Effort:** 2-3 hours (production hardening)  
**Priority for Production:** High  
**Blocker for Demo:** No (unlikely to fill 5MB with demo usage)

---

### D2: Responsive Design Device Testing
**Severity:** Low  
**Component:** demo/css/styles.css, breakpoints at 768px and 480px  
**Description:** CSS breakpoints defined theoretically but not tested on actual devices. Potential layout shifts at edge sizes (e.g., 480px, 600px, 900px).

**Impact:** Layout may break or become hard to use on specific real devices with non-standard sizes.

**Recommended Solution:**
- Test on iPad (768px), iPhone 12 (390px), Samsung Galaxy S21 (360px)
- Add intermediate breakpoints if needed (e.g., 600px, 900px)
- Use device emulation in DevTools but validate on real hardware before launch
- Consider using CSS Grid with `auto-fit` for more responsive layouts

**Estimated Effort:** 4-6 hours (device testing + CSS refinement)  
**Priority for Production:** High  
**Blocker for Demo:** No (emulation is sufficient for prototype validation)

---

### D3: Accessibility (a11y) Validation
**Severity:** Medium  
**Component:** All components (HTML, CSS, JS)  
**Description:** First pass only. Missing ARIA labels, keyboard focus management, screen reader optimization. No testing with accessibility tools (Lighthouse, axe DevTools).

**Impact:** Users with screen readers, voice control, or keyboard-only navigation cannot effectively use the prototype.

**Recommended Solution:**
- Add ARIA labels to buttons: `aria-label="Buscar ativos"`
- Implement focus management for modals (trap focus inside modal)
- Semantic HTML already in place; enhance with `role` attributes where needed
- Test with screen reader (NVDA, JAWS) and axe DevTools
- Ensure all interactive elements are keyboard-accessible (Tab, Enter, Escape)

**Estimated Effort:** 6-8 hours (a11y audit + fixes)  
**Priority for Production:** High  
**Blocker for Demo:** No (sighted keyboard users can still navigate)

---

### D4: XSS Input Sanitization
**Severity:** Low-Medium  
**Component:** demo/js/app.js, showAssetDetails() and showNewAssetForm()  
**Description:** Direct innerHTML injection with asset data. In prototype, all data is from mock-data.js (safe). In production, user input or external data could inject malicious scripts.

**Impact:** In production, an attacker could inject JavaScript through asset properties (e.g., custom asset name with `<script>` tag).

**Recommended Solution:**
- Integrate DOMPurify library for HTML sanitization
- Use `textContent` instead of `innerHTML` where possible
- Validate asset data schema on backend
- Content Security Policy (CSP) header to prevent inline script execution

**Estimated Effort:** 1-2 hours (add DOMPurify)  
**Priority for Production:** High  
**Blocker for Demo:** No (mock data is controlled)

---

### D5: Form Submission Queue & Sync Retry
**Severity:** Medium  
**Component:** demo/js/components.js (OfflineFormManager), demo/js/app.js (submitServiceOrder)  
**Description:** Form data saved to localStorage on offline submit, but queue is not persisted or prioritized. No retry logic if sync fails. Lost on page reload.

**Impact:** If user goes offline, submits form, then loses connection before page reload, submission is lost.

**Recommended Solution:**
- Create submission queue structure: `queue/submission-{timestamp}.json`
- Implement exponential backoff retry (3x before giving up)
- Auto-sync when connection restored (detect via online/offline events)
- Provide queue management UI: view pending submissions, manual retry, delete

**Estimated Effort:** 4-6 hours (queue implementation + sync logic)  
**Priority for Production:** Critical  
**Blocker for Demo:** No (refreshing clears queue, but acceptable for demo)

---

### D6: True Offline-First PWA (Service Workers)
**Severity:** Medium  
**Component:** Entire application  
**Description:** Current implementation simulates offline mode but requires JavaScript to run. True offline-first would use Service Workers for offline cache and background sync.

**Impact:** If main JS fails to load or browser doesn't support JS, prototype is non-functional. No fallback.

**Recommended Solution:**
- Implement Service Worker to cache index.html, styles.css, all JS/images
- Use Cache-First or Network-First strategy per asset
- Implement Background Sync API for form submission retry
- Add install prompt for PWA (add to home screen on mobile)

**Estimated Effort:** 8-10 hours (Service Worker + BackgroundSync implementation)  
**Priority for Production:** High  
**Blocker for Demo:** No (JS-based simulation sufficient for demonstration)

---

## Summary Table

| Item | Category | Severity | Production Priority | Demo Blocker | Est. Hours |
|------|----------|----------|---------------------|--------------|-----------|
| D1: localStorage quota | Storage | Medium | High | No | 2-3 |
| D2: Device testing | UX | Low | High | No | 4-6 |
| D3: Accessibility | Compliance | Medium | High | No | 6-8 |
| D4: XSS sanitization | Security | Low-Med | High | No | 1-2 |
| D5: Sync queue & retry | Core feature | Medium | **Critical** | No | 4-6 |
| D6: PWA Service Workers | Architecture | Medium | High | No | 8-10 |

**Total Deferred Effort:** ~25-35 hours for production hardening

---

## Triage Notes

**For Prototype Release:** All deferred items are acceptable. Prototype is suitable for stakeholder demonstration and UX validation without addressing these items.

**For Production Release:** Address items in this order:
1. **D5** (Sync queue) — Core offline functionality
2. **D1** (Quota) — Data integrity
3. **D6** (PWA) — User experience
4. **D3** (a11y) — Compliance
5. **D4** (XSS) — Security
6. **D2** (Device testing) — QA

---

**Owner:** Product Development Team  
**Last Reviewed:** 2026-03-24  
**Next Review:** After production hardening sprint
