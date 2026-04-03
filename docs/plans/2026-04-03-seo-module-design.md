# SEO Module Design

Date: 2026-04-03

## Overview

Replace the existing `SiteSetting` module with a dedicated SEO module. A single `SeoSetting` record holds global OG defaults and per-page meta descriptions for all public pages. Projects manage their own OG data via existing fields and a new `is_og` flag on media.

## Public Pages

- Landing
- Projects (listing)
- Werkliste
- Project (show)
- Profile
- Team
- Jobs
- Contact

## Data Model

### `seo_settings` table (new, single row, seeded)

| Column | Type | Notes |
|--------|------|-------|
| `og_title` | string, nullable | Global OG title fallback |
| `og_description` | string, nullable | Global OG description fallback |
| `landing_meta_description` | string, nullable | |
| `projects_meta_description` | string, nullable | |
| `werkliste_meta_description` | string, nullable | |
| `profile_meta_description` | string, nullable | |
| `team_meta_description` | string, nullable | |
| `jobs_meta_description` | string, nullable | |
| `contact_meta_description` | string, nullable | |

OG image stored via morphMany `Media` relationship (existing pattern).

### `media` table (existing)

Add `is_og boolean default false` — flags which media item is the OG image for its parent record.

### `Project` model (existing)

Keep `meta_description`. No new columns needed.

- OG title: generated from `title + location`
- OG description: `meta_description`
- OG image: media where `is_og = true`

### `SiteSetting` model

Remove entirely: model, migration, resource, controller, request, action.

## Backend

- Migration: create `seo_settings`, add `is_og` to `media`
- `SeoSetting` model with `HasUuid`, morphMany media
- `SeoSettingResource`
- `UpdateSeoSettingRequest` (all fields nullable, string, max:255 except descriptions max:500)
- `SeoController` with `show` and `update` methods
- `UpdateAction` in `app/Actions/Seo/`
- Route: `GET /api/seo`, `PUT /api/seo`
- View composer binding `$seo` (SeoSetting) to all public blade views

## Frontend (Blade)

The public `<head>` layout receives `$seo` via a view composer. Renders:

```html
<!-- Meta description: page-specific or project-specific -->
<meta name="description" content="...">

<!-- Global OG (overridden on project detail pages) -->
<meta property="og:title" content="...">
<meta property="og:description" content="...">
<meta property="og:image" content="...">
```

**Fallback logic per page:**

| Page | `<meta description>` source | OG override |
|------|-----------------------------|-------------|
| Landing | `seo.landing_meta_description` | global |
| Projects listing | `seo.projects_meta_description` | global |
| Werkliste | `seo.werkliste_meta_description` | global |
| Project show | `project.meta_description` | title+location / meta_description / is_og media |
| Profile | `seo.profile_meta_description` | global |
| Team | `seo.team_meta_description` | global |
| Jobs | `seo.jobs_meta_description` | global |
| Contact | `seo.contact_meta_description` | global |

## CMS (Vue)

Sidebar entry "SEO" replaces "Einstellungen".

Single form with two sections:

**Open Graph**
- `og_title` text input
- `og_description` textarea
- Image upload (single image, stored with `is_og = true` via morphMany)

**Page Descriptions**
- 7 textareas, one per public page (landing, projects, werkliste, profile, team, jobs, contact)

**Project form**
- Media items gain an `is_og` toggle to designate the OG image for that project
