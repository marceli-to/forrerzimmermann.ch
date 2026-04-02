# Atelier (Page Content)

## Overview

The Atelier section has three sub-pages — **Profil**, **Team**, **Jobs** — each with a consistent layout: image left (~65%), content right. Navigated via bottom tabs on the public site.

This module manages **page-level content only** (image, title, text). Team members and job listings are separate modules.

These are fixed records — no create/delete, edit only.

---

## Database Schema

### `atelier_pages`

| Field        | Type        | Notes                                          |
|--------------|-------------|-------------------------------------------------|
| `id`         | bigint (PK) |                                                 |
| `uuid`       | uuid        | Route binding key, auto-generated               |
| `slug`       | string      | Fixed: `profil`, `team`, `jobs`. Unique.        |
| `title`      | string (null)| Page heading (used by Profil)                  |
| `text`       | text (null) | Rich text (used by Profil)                      |
| `meta_description` | string (null) | SEO meta description                      |
| `publish`    | boolean     | Visibility toggle, default `false`              |
| `sort_order` | integer     | Tab ordering, default `0`                       |
| `created_at` | timestamp   |                                                 |
| `updated_at` | timestamp   |                                                 |

### Fields per Page

| Page   | title | text | image |
|--------|-------|------|-------|
| Profil | yes   | yes  | yes   |
| Team   | —     | —    | yes   |
| Jobs   | —     | —    | yes   |

### Relations

- **Media:** Polymorphic `MorphMany` — one image per page

---

## Sidebar

| Label   | Route                   | Icon           |
|---------|-------------------------|----------------|
| Atelier | `/dashboard/atelier`    | `PhHouseSimple`|

---

## Routes

### Web (Blade shell)

| Name            | Method | Path                             | Component        |
|-----------------|--------|----------------------------------|------------------|
| `atelier.index` | GET    | `/dashboard/atelier`             | `AtelierIndex`   |
| `atelier.edit`  | GET    | `/dashboard/atelier/:id/edit`    | `AtelierForm`    |

### API

| Method | Route                                      | Action              |
|--------|--------------------------------------------|----------------------|
| GET    | `/api/dashboard/atelier`                   | List all pages       |
| GET    | `/api/dashboard/atelier/{page}`            | Show single page     |
| PUT    | `/api/dashboard/atelier/{page}`            | Update page          |
| PATCH  | `/api/dashboard/atelier/{page}/publish`    | Toggle publish       |

No create, delete, or reorder endpoints — fixed records.

---

## Backend

### Model

`App\Models\AtelierPage`

- Uses traits: `HasUuid`, `HasFactory`
- Casts: `publish` (boolean)
- Fillable: `uuid`, `slug`, `title`, `text`, `meta_description`, `publish`, `sort_order`
- Relation: `media()` → `MorphMany(Media::class, 'mediable')->orderBy('sort_order')`

### Seeder

A seeder creates the three fixed records on initial setup:

```php
AtelierPage::create(['slug' => 'profil', 'sort_order' => 0]);
AtelierPage::create(['slug' => 'team', 'sort_order' => 1]);
AtelierPage::create(['slug' => 'jobs', 'sort_order' => 2]);
```

### Controllers

- `App\Http\Controllers\Dashboard\AtelierController` — serves Blade shell for index/edit
- `App\Http\Controllers\Api\AtelierController` — JSON API (index, show, update, publish toggle)

### Action Classes

| Action                   | Purpose                              |
|--------------------------|--------------------------------------|
| `UpdateAtelierPageAction`| Update page content, sync media      |

### Form Request

`App\Http\Requests\Dashboard\AtelierPage\UpdateAtelierPageRequest`

| Field    | Rules                        |
|----------|------------------------------|
| `title`  | nullable, string, max:255    |
| `text`   | nullable, string             |
| `meta_description` | nullable, string, max:255 |
| `publish`| boolean                      |
| `media`  | nullable, array              |

### API Resource

`App\Http\Resources\Dashboard\AtelierPageResource`

Returns: `uuid`, `slug`, `title`, `text`, `meta_description`, `publish`, `sort_order`, `media` (nested `MediaResource`).

---

## Frontend (Vue)

### File Structure

```
resources/js/app/
├── api/
│   └── atelier.js              # Axios API service
├── stores/
│   └── atelier.js              # Pinia store
└── views/
    └── atelier/
        ├── Index.vue           # List of three pages
        └── Form.vue            # Edit form
```

### API Service (`api/atelier.js`)

```js
index:   () => api.get('/atelier')
show:    (id) => api.get(`/atelier/${id}`)
update:  (id, data) => api.put(`/atelier/${id}`, data)
toggle:  (id) => api.patch(`/atelier/${id}/publish`)
```

### Pinia Store (`stores/atelier.js`)

State: `pages[]`, `current`, `loading`, `errors`

Actions: `fetchPages`, `fetchPage`, `savePage`, `toggle`

### Index View (`views/atelier/Index.vue`)

- `PageHeader` with title "Atelier" (no create button)
- Simple list/table showing: page name (Profil, Team, Jobs), publish status, edit link
- No drag-and-drop — fixed order

### Form View (`views/atelier/Form.vue`)

- `PageHeader` with title "Profil bearbeiten" / "Team bearbeiten" / "Jobs bearbeiten", cancel + save buttons
- Conditional fields based on `slug`:
  - **Profil:** `FormInput` for title, `Editor` (Tiptap) for text, `FormInput` for meta_description, `MediaUploader` + `MediaGrid` for image
  - **Team:** `FormInput` for meta_description, `MediaUploader` + `MediaGrid` for image
  - **Jobs:** `FormInput` for meta_description, `MediaUploader` + `MediaGrid` for image
- `MediaEdit` drawer for alt/caption editing

### Router Registration

```js
{ path: '/dashboard/atelier',           name: 'atelier.index', component: AtelierIndex, meta: { title: 'Atelier' } }
{ path: '/dashboard/atelier/:id/edit',  name: 'atelier.edit',  component: AtelierForm,  meta: { title: 'Atelier bearbeiten' } }
```

---

## Frontend Behavior (Public Site)

- Three sub-pages under Atelier, navigated via bottom tabs: **Profil** | **Team** | **Jobs**
- Consistent layout: image left (~65%), content right (~35%)
- **Profil:** title + rich text
- **Team:** team member listing (from team members module)
- **Jobs:** job listings (from jobs module)
- Pages with `publish = false` are hidden from navigation
