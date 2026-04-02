# Projects (Projekte)

## Overview

Projects are the core content type. They appear in two views:

1. **Auswahl** (Selection) — curated grid of featured projects, 4 columns. Projects without a detail page (`publish = false`) appear greyed out.
2. **Werkliste** (Work list) — complete chronological list sorted by `year`, text only. Future: grouping by year or topic.

The project detail opens as an overlay with two tabs: **Bilder** (image carousel) and **Text** (description + info).

---

## Database Schema

### `projects`

| Field        | Type         | Notes                                          |
|--------------|--------------|-------------------------------------------------|
| `id`         | bigint (PK)  |                                                 |
| `uuid`       | uuid         | Route binding key, auto-generated               |
| `title`      | string       | Required, used for slug                         |
| `location`   | string (null)| Part of slug if available                       |
| `slug`       | string       | Auto-generated from title + location, unique    |
| `subtitle`   | string (null)| e.g. "Instandsetzung 2024"                      |
| `year`       | integer      | Required, for Werkliste sorting/grouping        |
| `description`| text (null)  | Rich text, shown on Text tab                    |
| `info`       | text (null)  | Rich text, covers details + collaborators       |
| `meta_description` | string (null) | SEO meta description for detail page      |
| `publish`    | boolean      | Has detail page, default `false`                |
| `feature`    | boolean      | Show in Auswahl grid, default `false`           |
| `sort_order` | integer      | Ordering in Auswahl grid, default `0`           |
| `topic_id`   | bigint (null)| FK → `topics.id`                                |
| `created_at` | timestamp    |                                                 |
| `updated_at` | timestamp    |                                                 |

### Slug Generation

Composed from `title` + `location` (if available):
- "Nietengasse" + "Zürich" → `nietengasse-zuerich`
- "Wohnüberbauung" + null → `wohnueberbauung`

### Relations

- **Media:** Polymorphic `MorphMany` — images and plans, no distinction. `caption` available on any media record.
- **Topic:** `BelongsTo` → `Topic` (nullable, separate module)

---

## Sidebar

| Label    | Route                    | Icon          |
|----------|--------------------------|---------------|
| Projekte | `/dashboard/projects`    | `PhBuildings` |

Placed in the first (unlabelled) navigation group, after Startseite.

---

## Routes

### Web (Blade shell)

| Name              | Method | Path                               | Component      |
|-------------------|--------|-------------------------------------|----------------|
| `projects.index`  | GET    | `/dashboard/projects`               | `ProjectIndex` |
| `projects.create` | GET    | `/dashboard/projects/create`        | `ProjectForm`  |
| `projects.edit`   | GET    | `/dashboard/projects/:id/edit`      | `ProjectForm`  |

### API

| Method | Route                                | Action                  |
|--------|--------------------------------------|-------------------------|
| GET    | `/api/dashboard/projects`            | List all projects       |
| POST   | `/api/dashboard/projects`            | Create project          |
| GET    | `/api/dashboard/projects/{project}`  | Show single project     |
| PUT    | `/api/dashboard/projects/{project}`  | Update project          |
| DELETE | `/api/dashboard/projects/{project}`  | Delete project          |
| PATCH  | `/api/dashboard/projects/{project}/publish` | Toggle publish   |
| PATCH  | `/api/dashboard/projects/{project}/feature` | Toggle feature   |
| PATCH  | `/api/dashboard/projects/reorder`    | Batch reorder           |

---

## Backend

### Model

`App\Models\Project`

- Uses traits: `HasUuid`, `HasFactory`
- Casts: `publish` (boolean), `feature` (boolean), `year` (integer)
- Fillable: `uuid`, `title`, `location`, `slug`, `subtitle`, `year`, `description`, `info`, `meta_description`, `publish`, `feature`, `sort_order`, `topic_id`
- Relations:
  - `media()` → `MorphMany(Media::class, 'mediable')->orderBy('sort_order')`
  - `topic()` → `BelongsTo(Topic::class)`

### Controllers

- `App\Http\Controllers\Dashboard\ProjectController` — serves Blade shell for index/create/edit
- `App\Http\Controllers\Api\ProjectController` — JSON API (index, show, store, update, destroy, publish toggle, feature toggle, reorder)

### Action Classes

| Action                  | Purpose                                      |
|-------------------------|----------------------------------------------|
| `StoreProjectAction`    | Validate, generate slug, create project, attach media |
| `UpdateProjectAction`   | Validate, regenerate slug if needed, update project, sync media |
| `DeleteProjectAction`   | Delete project + detach/clean up media       |
| `ReorderProjectsAction` | Batch update `sort_order`                    |

### Form Request

`App\Http\Requests\Dashboard\Project\StoreProjectRequest`

| Field        | Rules                                    |
|--------------|------------------------------------------|
| `title`      | required, string, max:255                |
| `location`   | nullable, string, max:255                |
| `subtitle`   | nullable, string, max:255                |
| `year`       | required, integer                        |
| `description`| nullable, string                         |
| `info`       | nullable, string                         |
| `meta_description` | nullable, string, max:255          |
| `publish`    | boolean                                  |
| `feature`    | boolean                                  |
| `topic_id`   | nullable, exists:topics,id               |
| `media`      | nullable, array                          |

### API Resource

`App\Http\Resources\Dashboard\ProjectResource`

Returns: `uuid`, `title`, `location`, `slug`, `subtitle`, `year`, `description`, `info`, `meta_description`, `publish`, `feature`, `sort_order`, `topic` (nested), `media` (nested `MediaResource`).

---

## Frontend (Vue)

### File Structure

```
resources/js/app/
├── api/
│   └── projects.js             # Axios API service
├── stores/
│   └── projects.js             # Pinia store
└── views/
    └── projects/
        ├── Index.vue           # List view
        └── Form.vue            # Create/edit form
```

### API Service (`api/projects.js`)

```js
index:   () => api.get('/projects')
show:    (id) => api.get(`/projects/${id}`)
store:   (data) => api.post('/projects', data)
update:  (id, data) => api.put(`/projects/${id}`, data)
toggle:  (id) => api.patch(`/projects/${id}/publish`)
feature: (id) => api.patch(`/projects/${id}/feature`)
destroy: (id) => api.delete(`/projects/${id}`)
reorder: (items) => api.patch('/projects/reorder', { items })
```

### Pinia Store (`stores/projects.js`)

State: `projects[]`, `current`, `loading`, `errors`

Actions: `fetchProjects`, `fetchProject`, `saveProject`, `toggle`, `toggleFeature`, `deleteProject`

### Index View (`views/projects/Index.vue`)

- `PageHeader` with title "Projekte" and "Neues Projekt" button
- `DataTable` with columns: thumbnail, title, location, year, publish status, feature status, actions (edit, delete, publish toggle)
- Drag-and-drop reorder support
- Empty state: "Noch keine Projekte vorhanden."

### Form View (`views/projects/Form.vue`)

- `PageHeader` with title "Neues Projekt" / "Projekt bearbeiten", cancel + save buttons
- `FormInput` for title, location, subtitle
- `FormInput` (number) for year
- `FormSelect` for topic (loaded from topics API, nullable)
- `Editor` (Tiptap) for description
- `Editor` (Tiptap) for info
- `FormInput` for meta_description
- `FormCheckbox` for publish, feature
- `MediaUploader` + `MediaGrid` for images
- `MediaEdit` drawer for alt/caption editing

### Router Registration

```js
{ path: '/dashboard/projects',           name: 'projects.index',  component: ProjectIndex, meta: { title: 'Projekte' } }
{ path: '/dashboard/projects/create',    name: 'projects.create', component: ProjectForm,  meta: { title: 'Neues Projekt' } }
{ path: '/dashboard/projects/:id/edit',  name: 'projects.edit',   component: ProjectForm,  meta: { title: 'Projekt bearbeiten' } }
```

---

## Frontend Behavior (Public Site)

### Auswahl (Selection Grid)

- 4-column grid of featured projects (`feature = true`), ordered by `sort_order`
- Each item shows teaser image, title, subtitle
- Hover: title/subtitle turn purple/magenta
- Projects with `publish = false` appear greyed out (thumbnail visible, no link)

### Werkliste (Work List)

- Chronological text list, sorted by `year` descending
- Future: grouping by year or topic

### Project Detail (Overlay)

- Opens as layer/overlay, no main logo/navigation visible
- Top bar: project title + location (left), prev/next arrows + close button (right)
- Prev/next cycles through projects in current list order — API provides prev/next slugs
- Bottom tabs: **Bilder** | **Text**
- **Bilder tab:** Image carousel with left/right arrow navigation. `caption` shown below image when filled.
- **Text tab:** Description (rich text) + info (rich text)
