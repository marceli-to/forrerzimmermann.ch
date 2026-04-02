# Site Settings

## Overview

Global settings singleton. Holds site-wide defaults and meta descriptions for pages that don't have their own database record (Landing, Projects listing).

Per-page SEO is handled on the respective tables:
- **Projects (detail):** `meta_description` on `projects`
- **Profil, Team, Jobs:** `meta_description` on `atelier_pages`
- **Kontakt:** `meta_description` on `contact`

---

## Database Schema

### `site_settings`

| Field                          | Type        | Notes                                    |
|--------------------------------|-------------|------------------------------------------|
| `id`                           | bigint (PK) |                                          |
| `uuid`                         | uuid        | Route binding key, auto-generated        |
| `site_title`                   | string      | e.g. "forrer zimmermann architektur"     |
| `meta_description`             | string (null)| Global default SEO description          |
| `landing_meta_description`     | string (null)| Meta description for homepage           |
| `projects_meta_description`    | string (null)| Meta description for projects listing   |
| `updated_at`                   | timestamp   |                                          |

### Relations

- **OG Image:** Polymorphic `MorphMany` — one default social sharing image

---

## Sidebar

| Label         | Route                     | Icon     |
|---------------|---------------------------|----------|
| Einstellungen | `/dashboard/settings`     | `PhGear` |

Placed last in the sidebar.

---

## Routes

### Web (Blade shell)

| Name            | Method | Path                    | Component       |
|-----------------|--------|-------------------------|-----------------|
| `settings.edit` | GET    | `/dashboard/settings`   | `SettingsForm`  |

No index view — navigates directly to the edit form.

### API

| Method | Route                          | Action              |
|--------|--------------------------------|----------------------|
| GET    | `/api/dashboard/settings`      | Show settings        |
| PUT    | `/api/dashboard/settings`      | Update settings      |

Single record, no create/delete.

---

## Backend

### Model

`App\Models\SiteSetting`

- Uses traits: `HasUuid`, `HasFactory`
- Fillable: `uuid`, `site_title`, `meta_description`, `landing_meta_description`, `projects_meta_description`
- Relation: `media()` → `MorphMany(Media::class, 'mediable')->orderBy('sort_order')`

### Seeder

A seeder creates the single record on initial setup.

### Controllers

- `App\Http\Controllers\Dashboard\SettingsController` — serves Blade shell
- `App\Http\Controllers\Api\SettingsController` — JSON API (show, update)

### Action Classes

| Action                     | Purpose                            |
|----------------------------|------------------------------------|
| `UpdateSiteSettingsAction` | Update settings, sync og_image     |

### Form Request

`App\Http\Requests\Dashboard\SiteSetting\UpdateSiteSettingRequest`

| Field                        | Rules                        |
|------------------------------|------------------------------|
| `site_title`                 | required, string, max:255    |
| `meta_description`           | nullable, string, max:255    |
| `landing_meta_description`   | nullable, string, max:255    |
| `projects_meta_description`  | nullable, string, max:255    |
| `media`                      | nullable, array              |

### API Resource

`App\Http\Resources\Dashboard\SiteSettingResource`

Returns: `uuid`, `site_title`, `meta_description`, `landing_meta_description`, `projects_meta_description`, `media` (nested `MediaResource`).

---

## Frontend (Vue)

### File Structure

```
resources/js/app/
├── api/
│   └── settings.js             # Axios API service
├── stores/
│   └── settings.js             # Pinia store
└── views/
    └── settings/
        └── Form.vue            # Edit form (no Index view)
```

### API Service (`api/settings.js`)

```js
show:   () => api.get('/settings')
update: (data) => api.put('/settings', data)
```

### Pinia Store (`stores/settings.js`)

State: `settings`, `loading`, `errors`

Actions: `fetchSettings`, `saveSettings`

### Form View (`views/settings/Form.vue`)

- `PageHeader` with title "Einstellungen", save button
- `FormInput` for site_title
- `FormInput` for meta_description (label: "Standard Meta Description")
- `FormInput` for landing_meta_description (label: "Meta Description Startseite")
- `FormInput` for projects_meta_description (label: "Meta Description Projekte")
- `MediaUploader` + `MediaGrid` for OG image
- `MediaEdit` drawer for alt editing

### Router Registration

```js
{ path: '/dashboard/settings', name: 'settings.edit', component: SettingsForm, meta: { title: 'Einstellungen' } }
```

---

## SEO Resolution Order (Public Site)

For any page, meta description resolves as:

1. Page-specific `meta_description` (on model record)
2. Global `meta_description` (from `site_settings`)
3. Empty / omitted

OG image resolves as:

1. First media on the page's model (e.g. project teaser, atelier page image)
2. Global OG image (from `site_settings`)
3. Empty / omitted
