# Job Listings (Stellen)

## Overview

Job listings are displayed on the Atelier > Jobs sub-page. Text-only content blocks shown on the right side. The page image comes from the atelier jobs page record.

---

## Database Schema

### `job_listings`

| Field        | Type        | Notes                                          |
|--------------|-------------|-------------------------------------------------|
| `id`         | bigint (PK) |                                                 |
| `uuid`       | uuid        | Route binding key, auto-generated               |
| `title`      | string      | Required, e.g. "Praktikumstelle"                |
| `text`       | text (null) | Rich text job description                       |
| `publish`    | boolean     | Visibility toggle, default `false`              |
| `sort_order` | integer     | Display ordering, default `0`                   |
| `created_at` | timestamp   |                                                 |
| `updated_at` | timestamp   |                                                 |

### Relations

None. No media — page image is managed on the atelier page with slug `jobs`.

---

## Sidebar

| Label   | Route                | Icon           |
|---------|----------------------|----------------|
| Stellen | `/dashboard/jobs`    | `PhBriefcase`  |

---

## Routes

### Web (Blade shell)

| Name          | Method | Path                          | Component   |
|---------------|--------|-------------------------------|-------------|
| `jobs.index`  | GET    | `/dashboard/jobs`             | `JobIndex`  |
| `jobs.create` | GET    | `/dashboard/jobs/create`      | `JobForm`   |
| `jobs.edit`   | GET    | `/dashboard/jobs/:id/edit`    | `JobForm`   |

### API

| Method | Route                                  | Action                  |
|--------|----------------------------------------|-------------------------|
| GET    | `/api/dashboard/jobs`                  | List all listings       |
| POST   | `/api/dashboard/jobs`                  | Create listing          |
| GET    | `/api/dashboard/jobs/{job}`            | Show single listing     |
| PUT    | `/api/dashboard/jobs/{job}`            | Update listing          |
| DELETE | `/api/dashboard/jobs/{job}`            | Delete listing          |
| PATCH  | `/api/dashboard/jobs/{job}/publish`    | Toggle publish          |
| PATCH  | `/api/dashboard/jobs/reorder`          | Batch reorder           |

---

## Backend

### Model

`App\Models\JobListing`

- Uses traits: `HasUuid`, `HasFactory`
- Casts: `publish` (boolean)
- Fillable: `uuid`, `title`, `text`, `publish`, `sort_order`

### Controllers

- `App\Http\Controllers\Dashboard\JobController` — serves Blade shell for index/create/edit
- `App\Http\Controllers\Api\JobController` — JSON API (index, show, store, update, destroy, publish toggle, reorder)

### Action Classes

| Action                    | Purpose                        |
|---------------------------|--------------------------------|
| `StoreJobListingAction`   | Create listing                 |
| `UpdateJobListingAction`  | Update listing                 |
| `DeleteJobListingAction`  | Delete listing                 |
| `ReorderJobListingsAction`| Batch update `sort_order`      |

### Form Request

`App\Http\Requests\Dashboard\JobListing\StoreJobListingRequest`

| Field   | Rules                        |
|---------|------------------------------|
| `title` | required, string, max:255    |
| `text`  | nullable, string             |
| `publish`| boolean                     |

### API Resource

`App\Http\Resources\Dashboard\JobListingResource`

Returns: `uuid`, `title`, `text`, `publish`, `sort_order`.

---

## Frontend (Vue)

### File Structure

```
resources/js/app/
├── api/
│   └── jobs.js                 # Axios API service
├── stores/
│   └── jobs.js                 # Pinia store
└── views/
    └── jobs/
        ├── Index.vue           # List view
        └── Form.vue            # Create/edit form
```

### API Service (`api/jobs.js`)

```js
index:   () => api.get('/jobs')
show:    (id) => api.get(`/jobs/${id}`)
store:   (data) => api.post('/jobs', data)
update:  (id, data) => api.put(`/jobs/${id}`, data)
toggle:  (id) => api.patch(`/jobs/${id}/publish`)
destroy: (id) => api.delete(`/jobs/${id}`)
reorder: (items) => api.patch('/jobs/reorder', { items })
```

### Pinia Store (`stores/jobs.js`)

State: `jobs[]`, `current`, `loading`, `errors`

Actions: `fetchJobs`, `fetchJob`, `saveJob`, `toggle`, `deleteJob`

### Index View (`views/jobs/Index.vue`)

- `PageHeader` with title "Stellen" and "Neue Stelle" button
- `DataTable` with columns: title, publish status, actions (edit, delete, publish toggle)
- Drag-and-drop reorder support
- Empty state: "Noch keine Stellen vorhanden."

### Form View (`views/jobs/Form.vue`)

- `PageHeader` with title "Neue Stelle" / "Stelle bearbeiten", cancel + save buttons
- `FormInput` for title
- `Editor` (Tiptap) for text

### Router Registration

```js
{ path: '/dashboard/jobs',            name: 'jobs.index',  component: JobIndex, meta: { title: 'Stellen' } }
{ path: '/dashboard/jobs/create',     name: 'jobs.create', component: JobForm,  meta: { title: 'Neue Stelle' } }
{ path: '/dashboard/jobs/:id/edit',   name: 'jobs.edit',   component: JobForm,  meta: { title: 'Stelle bearbeiten' } }
```

---

## Frontend Behavior (Public Site)

- Displayed on Atelier > Jobs sub-page, right side (~35%)
- Listings ordered by `sort_order`, filtered by `publish = true`
- Each listing shows title + rich text body
