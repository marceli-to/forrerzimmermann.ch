# Page Transitions Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Add a uniform ~250ms crossfade between every same-origin page navigation on the public Blade-rendered site, using the native View Transitions API.

**Architecture:** CSS-only. A new partial under `resources/css/partials/` adds `@view-transition { navigation: auto; }` and tunes the default `::view-transition-old(root)` / `::view-transition-new(root)` pseudo-elements. The partial is imported from `resources/css/site.css`. No JS, no routing changes, no Blade changes. Browsers without support (Firefox today) fall back silently to instant navigation.

**Tech Stack:** Laravel 12 Blade, Vite, Tailwind v4, native View Transitions API.

**Reference:** Design at `docs/plans/2026-04-30-page-transitions-design.md`.

---

### Task 1: Add the page-transitions CSS partial

**Files:**
- Create: `resources/css/partials/page-transitions.css`
- Modify: `resources/css/site.css` (insert one `@import` line beside the other partial imports)

**Step 1: Create the partial**

Create `resources/css/partials/page-transitions.css` with exactly:

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

Notes:
- `@view-transition { navigation: auto }` opts the document into cross-document view transitions for same-origin same-document-type navigations. The browser handles snapshot/cleanup automatically.
- The default user-agent animations on `::view-transition-old(root)` / `::view-transition-new(root)` already produce a crossfade. We are only overriding `animation-duration` and `animation-timing-function` — leave everything else (animation-name, fill-mode, etc.) untouched so the default crossfade keyframes still apply.

**Step 2: Wire the partial into the public stylesheet**

In `resources/css/site.css`, add the import alongside the existing partials block (after `utils.css`):

```css
@import "./partials/fonts.css";
@import "./partials/scrollbar.css";
@import "./partials/spacing.css";
@import "./partials/utils.css";
@import "./partials/page-transitions.css";
```

Only the new line is added. Do not reorder or modify other imports.

**Step 3: Verify the build**

Run: `npm run build`
Expected: Build completes with no errors. The new `@view-transition` at-rule should not produce warnings (Tailwind v4 / Vite passes unknown at-rules through to the output CSS).

If a warning appears about an unknown at-rule, it can be ignored as long as the rule is preserved in the built output. To confirm, search the build output:

Run: `grep -r "view-transition" public/build/assets 2>/dev/null | head -5`
Expected: At least one match showing `@view-transition` and the pseudo-element rules.

**Step 4: Manual verification in the browser**

Run: `npm run dev` (in one terminal) and `php artisan serve` (in another), or use the existing local dev setup.

In a Chromium browser (Chrome/Edge 111+) or Safari 18+:
1. Visit `/` (landing).
2. Click through to `/projekte/auswahl`.
3. Open a project, navigate `/bilder` ↔ `/text`.
4. Navigate to `/atelier/profil`, `/atelier/team`, `/atelier/jobs`, `/kontakt`.

Expected: A smooth ~250ms crossfade between every page. No flicker, no layout jank, no console errors.

In Firefox: navigation should be instant (no transition, no errors). This is the accepted fallback.

If the crossfade is too fast/slow or the easing feels wrong, tweak `animation-duration` (try 200–350ms) and `animation-timing-function` (try `ease`, `ease-out`, or `cubic-bezier(0.4, 0, 0.2, 1)`) in the partial and rebuild.

**Step 5: Confirm dashboard SPA is unaffected**

The dashboard uses `resources/css/app.css`, not `site.css`. The partial is only imported from `site.css`, so the SPA is untouched. Visit `/dashboard` and confirm it behaves exactly as before (no transitions on internal route changes — the SPA uses Vue Router, which is out of scope).

**Step 6: Commit**

```bash
git add resources/css/partials/page-transitions.css resources/css/site.css
git commit -m "feat(site): add page transitions via View Transitions API"
```

---

### Task 2: Sanity check — no regressions

**Step 1: Click through every public route**

Walk every route in `routes/web.php`:
- `/` (landing)
- `/projekte/auswahl`
- `/projekte/werkliste`
- `/projekte/{slug}/bilder` (pick any project)
- `/projekte/{slug}/text`
- `/atelier/profil`
- `/atelier/team`
- `/atelier/jobs`
- `/kontakt`

For each, confirm:
- Page renders correctly.
- Crossfade plays on entry (Chrome/Safari).
- Swiper slideshows still initialize and operate.
- Alpine logo / shy header still works.
- No console errors.

**Step 2: Test with browser back/forward**

Navigate forward through 3–4 pages, then use browser back/forward buttons. The crossfade should play on history navigation as well (the `navigation: auto` rule covers this).

**Step 3: No commit needed if nothing changes**

If a regression appears, debug and commit the fix as a separate commit.

---

## Done criteria

- `resources/css/partials/page-transitions.css` exists with the rules above.
- `resources/css/site.css` imports it.
- `npm run build` succeeds.
- Crossfade visible on every public route navigation in Chrome/Safari.
- Firefox falls back to instant navigation without errors.
- Dashboard SPA behavior unchanged.
- One commit on master: `feat(site): add page transitions via View Transitions API`.
