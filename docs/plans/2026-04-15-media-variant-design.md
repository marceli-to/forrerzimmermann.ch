# Media Variant (Desktop/Mobile) Design

**Date:** 2026-04-15
**Status:** Approved

## Summary

Add a `variant` enum field to the media table (`desktop` default, `mobile`) so editors can upload separate images per breakpoint. The public site renders both in a single `<picture>` element with media queries. The CMS exposes variant selection via a dropdown in the existing media edit drawer.

## Database

- New `variant` enum column on `media` table: values `desktop`, `mobile`, default `desktop`

## Backend

- Media model: add `variant` to `$fillable`
- MediaResource: expose `variant`
- HasMediaRules: validate `media.*.variant`
- AttachAction: persist `variant`
- UpdateAction: accept `variant` changes
- Media model scopes: `scopeDesktop`, `scopeMobile`

## Frontend (CMS)

- MediaEdit drawer: "Variante" select (Desktop/Mobile) above the alt text field
- MediaCard: "Mobile" badge when `variant === 'mobile'` (same style as Teaser/OG badges)

## Public Rendering

- Image blade component: optional `$mobileMedia` parameter
- When present: mobile sources with `(max-width: 767px)`, desktop sources with `(min-width: 768px)`
- When absent: single set of sources (current behaviour)
- Blade templates query for mobile variant and pass it if it exists
