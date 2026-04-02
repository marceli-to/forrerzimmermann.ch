# Landing (Homepage Carousel)

## Overview

The homepage features a full-width carousel that auto-advances and supports manual arrow navigation. Each slide is either a full-bleed image or an image with rich text content (image left ~65%, text right).

---

## Database Schema

### `landing_slides`

| Field        | Type        | Notes                                    |
|--------------|-------------|------------------------------------------|
| `id`         | bigint (PK) |                                          |
| `uuid`       | uuid        | Route binding key, auto-generated        |
| `type`       | enum        | `image`, `image_text`                    |
| `text`       | text (null) | Rich text, only for `image_text` type    |
| `publish`    | boolean     | Visibility toggle, default `false`       |
| `sort_order` | integer     | Slide ordering, default `0`              |
| `created_at` | timestamp   |                                          |
| `updated_at` | timestamp   |                                          |

### Relations

- **Media:** Polymorphic `MorphMany` — one image per slide

---

## Sidebar

| Label       | Route                   | Icon        |
|-------------|-------------------------|-------------|
| Startseite  | `/dashboard/landing`    | `PhHouse`   |

Placed as first item in the first (unlabelled) navigation group.

---

## Routes

### Web (Blade shell)

| Name              | Method | Path                              | Component     |
|-------------------|--------|-----------------------------------|---------------|
| `landing.index`   | GET    | `/dashboard/landing`              | `LandingIndex`|
| `landing.create`  | GET    | `/dashboard/landing/create`       | `LandingForm` |
| `landing.edit`    | GET    | `/dashboard/landing/:id/edit`     | `LandingForm` |

### API

| Method | Route                             | Action                  |
|--------|-----------------------------------|-------------------------|
| GET    | `/api/dashboard/landing`          | List all slides         |
| POST   | `/api/dashboard/landing`          | Create slide            |
| GET    | `/api/dashboard/landing/{slide}`  | Show single slide       |
| PUT    | `/api/dashboard/landing/{slide}`  | Update slide            |
| DELETE | `/api/dashboard/landing/{slide}`  | Delete slide            |
| PATCH  | `/api/dashboard/landing/{slide}/publish` | Toggle publish   |
| PATCH  | `/api/dashboard/landing/reorder`  | Batch reorder           |

---

## Backend

### Model

`App\Models\LandingSlide`

- Uses traits: `HasUuid`, `HasFactory`
- Casts: `publish` (boolean)
- Fillable: `uuid`, `type`, `text`, `publish`, `sort_order`
- Relation: `media()` → `MorphMany(Media::class, 'mediable')->orderBy('sort_order')`

### Controllers

- `App\Http\Controllers\Dashboard\LandingController` — serves Blade shell for index/create/edit
- `App\Http\Controllers\Api\LandingController` — JSON API (index, show, store, update, destroy, publish toggle, reorder)

### Action Classes

| Action                    | Purpose                                   |
|---------------------------|-------------------------------------------|
| `StoreLandingSlideAction` | Validate, create slide, attach media      |
| `UpdateLandingSlideAction`| Validate, update slide, sync media        |
| `DeleteLandingSlideAction`| Delete slide + detach/clean up media      |
| `ReorderLandingSlidesAction` | Batch update `sort_order`              |

### Form Request

`App\Http\Requests\Dashboard\LandingSlide\StoreLandingSlideRequest`

| Field       | Rules                                       |
|-------------|---------------------------------------------|
| `type`      | required, in: `image`, `image_text`         |
| `text`      | nullable, required_if type is `image_text`  |
| `publish`   | boolean                                     |
| `media`     | nullable, array                             |

### API Resource

`App\Http\Resources\Dashboard\LandingSlideResource`

Returns: `uuid`, `type`, `text`, `publish`, `sort_order`, `media` (nested `MediaResource`).

---

## Frontend (Vue)

### File Structure

```
resources/js/app/
├── api/
│   └── landing.js              # Axios API service
├── stores/
│   └── landing.js              # Pinia store
└── views/
    └── landing/
        ├── Index.vue           # Sortable list view
        └── Form.vue            # Create/edit form
```

### API Service (`api/landing.js`)

```js
index:   () => api.get('/landing')
show:    (id) => api.get(`/landing/${id}`)
store:   (data) => api.post('/landing', data)
update:  (id, data) => api.put(`/landing/${id}`, data)
toggle:  (id) => api.patch(`/landing/${id}/publish`)
destroy: (id) => api.delete(`/landing/${id}`)
reorder: (items) => api.patch('/landing/reorder', { items })
```

### Pinia Store (`stores/landing.js`)

State: `slides[]`, `current`, `loading`, `errors`

Actions: `fetchSlides`, `fetchSlide`, `saveSlide`, `toggle`, `deleteSlide`

### Index View (`views/landing/Index.vue`)

- `PageHeader` with title "Startseite" and "Neuer Slide" button
- `DataTable` with columns: thumbnail, type, publish status, actions (edit, delete, publish toggle)
- Drag-and-drop reorder support
- Empty state: "Noch keine Slides vorhanden."

### Form View (`views/landing/Form.vue`)

- `PageHeader` with title "Neuer Slide" / "Slide bearbeiten", cancel + save buttons
- `FormSelect` for type (`image` / `image_text`)
- `MediaUploader` + `MediaGrid` for single image upload
- `Editor` (Tiptap) for rich text — conditionally shown when type is `image_text`
- `MediaEdit` drawer for alt/caption editing

### Router Registration

```js
{ path: '/dashboard/landing',            name: 'landing.index',  component: LandingIndex,  meta: { title: 'Startseite' } }
{ path: '/dashboard/landing/create',     name: 'landing.create', component: LandingForm,   meta: { title: 'Neuer Slide' } }
{ path: '/dashboard/landing/:id/edit',   name: 'landing.edit',   component: LandingForm,   meta: { title: 'Slide bearbeiten' } }
```

---

## Frontend Behavior (Public Site)

- Auto-advances on a timer
- Left/right arrow navigation
- Slides returned in `sort_order`, filtered by `publish = true`
- `image` type: centered full-width image
- `image_text` type: image left (~65%), rich text right (~35%)
