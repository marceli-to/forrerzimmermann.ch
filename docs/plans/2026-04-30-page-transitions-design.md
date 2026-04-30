# Page Transitions — Design

Date: 2026-04-30
Scope: Public site (Blade routes). Dashboard SPA unaffected.

## Goal

Add a subtle ~250ms crossfade between page navigations on the public site. Aesthetic polish only — no behavioral or architectural change.

## Approach

Use the native **View Transitions API** for cross-document navigations. CSS-only, zero JavaScript, no build or routing changes.

The browser handles the navigation snapshot, fade, and cleanup. Unsupported browsers (Firefox as of 2026-04-30) fall back to instant navigation with no breakage.

## Behavior

- Applies uniformly to every same-origin navigation on the public site.
- ~250ms crossfade, gentle easing.
- No special cases (project ↔ project, `/bilder` ↔ `/text`, etc. all use the same transition).
- Dashboard routes (`/dashboard/*`) are an SPA and not affected.

## Implementation

Two CSS additions to the public site stylesheet:

```css
@view-transition {
  navigation: auto;
}

::view-transition-old(root),
::view-transition-new(root) {
  animation-duration: 250ms;
  animation-timing-function: ease;
}
```

That's it. The default `::view-transition-old/new(root)` pseudos already crossfade — we're only tuning duration and easing.

## Files touched

- `resources/css/site.css` (or the equivalent public site stylesheet entry) — add the two rules above.

## Files NOT touched

- Routes, controllers, Blade views, Alpine modules, Swiper init.
- Dashboard SPA.

## Browser support

| Browser   | Behavior            |
|-----------|---------------------|
| Chrome/Edge 111+ | Crossfade        |
| Safari 18+       | Crossfade        |
| Firefox          | Instant nav (fallback) |

Accepted: Firefox falls back silently. No polyfill.

## Risk

Negligible. Feature is purely additive; if the rule is wrong or the browser lacks support, navigation behaves as it does today.

## Out of scope

- Per-route or per-element transitions (e.g., morphing the logo, persisting media across pages).
- A JS shim for Firefox.
- Transitions inside the dashboard SPA.
