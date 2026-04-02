# Kontakt (Contact Page)

## Overview

The contact page shows a map on the left and company info on the right. This is a singleton — one fixed record, edit only.

---

## Database Schema

### `contact`

| Field        | Type        | Notes                                          |
|--------------|-------------|-------------------------------------------------|
| `id`         | bigint (PK) |                                                 |
| `uuid`       | uuid        | Route binding key, auto-generated               |
| `name`       | string      | e.g. "Forrer Zimmermann Architekten GmbH"       |
| `address`    | string      | e.g. "Badenerstrasse 370, CH-8004 Zürich"       |
| `email`      | string      | e.g. "mail@forrerzimmermann.ch"                 |
| `phone`      | string      | e.g. "+41 44 548 90 01"                         |
| `maps_url`   | string (null)| Google Maps embed/link URL                     |
| `imprint`    | text (null) | Rich text (Impressum)                           |
| `meta_description` | string (null) | SEO meta description                      |
| `publish`    | boolean     | Visibility toggle, default `false`              |
| `created_at` | timestamp   |                                                 |
| `updated_at` | timestamp   |                                                 |

### Relations

None.

---

## Sidebar

| Label   | Route                  | Icon            |
|---------|------------------------|-----------------|
| Kontakt | `/dashboard/kontakt`   | `PhEnvelope`    |

---

## Routes

### Web (Blade shell)

| Name            | Method | Path                   | Component       |
|-----------------|--------|------------------------|-----------------|
| `kontakt.edit`  | GET    | `/dashboard/kontakt`   | `KontaktForm`   |

No index view — navigates directly to the edit form.

### API

| Method | Route                         | Action              |
|--------|-------------------------------|----------------------|
| GET    | `/api/dashboard/kontakt`      | Show contact         |
| PUT    | `/api/dashboard/kontakt`      | Update contact       |

No create, delete, list, or reorder endpoints — single fixed record.

---

## Backend

### Model

`App\Models\Contact`

- Uses traits: `HasUuid`, `HasFactory`
- Casts: `publish` (boolean)
- Fillable: `uuid`, `name`, `address`, `email`, `phone`, `maps_url`, `imprint`, `meta_description`, `publish`

### Seeder

A seeder creates the single record on initial setup.

### Controllers

- `App\Http\Controllers\Dashboard\KontaktController` — serves Blade shell
- `App\Http\Controllers\Api\KontaktController` — JSON API (show, update)

### Action Classes

| Action                | Purpose              |
|-----------------------|----------------------|
| `UpdateContactAction` | Update contact info  |

### Form Request

`App\Http\Requests\Dashboard\Contact\UpdateContactRequest`

| Field      | Rules                        |
|------------|------------------------------|
| `name`     | required, string, max:255    |
| `address`  | required, string, max:255    |
| `email`    | required, string, email      |
| `phone`    | required, string, max:255    |
| `maps_url` | nullable, string, url        |
| `imprint`  | nullable, string             |
| `meta_description` | nullable, string, max:255 |
| `publish`  | boolean                      |

### API Resource

`App\Http\Resources\Dashboard\ContactResource`

Returns: `uuid`, `name`, `address`, `email`, `phone`, `maps_url`, `imprint`, `meta_description`, `publish`.

---

## Frontend (Vue)

### File Structure

```
resources/js/app/
├── api/
│   └── kontakt.js              # Axios API service
├── stores/
│   └── kontakt.js              # Pinia store
└── views/
    └── kontakt/
        └── Form.vue            # Edit form (no Index view)
```

### API Service (`api/kontakt.js`)

```js
show:   () => api.get('/kontakt')
update: (data) => api.put('/kontakt', data)
```

### Pinia Store (`stores/kontakt.js`)

State: `contact`, `loading`, `errors`

Actions: `fetchContact`, `saveContact`

### Form View (`views/kontakt/Form.vue`)

- `PageHeader` with title "Kontakt", save button
- `FormInput` for name, address, email, phone, maps_url
- `Editor` (Tiptap) for imprint
- `FormInput` for meta_description
- No cancel button (singleton, nowhere to navigate back to)

### Router Registration

```js
{ path: '/dashboard/kontakt', name: 'kontakt.edit', component: KontaktForm, meta: { title: 'Kontakt' } }
```

---

## Frontend Behavior (Public Site)

- Map on the left (Google Maps, using `maps_url`)
- Company info on the right: name, address, phone, email
- Expandable Impressum section (from `imprint` rich text)
