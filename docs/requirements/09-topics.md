# Topics (Themen)

## Overview

Topics are a simple lookup table used to categorize projects. Referenced via `topic_id` on the projects table. Used for future grouping/filtering on the Werkliste.

---

## Database Schema

### `topics`

| Field        | Type        | Notes                                          |
|--------------|-------------|-------------------------------------------------|
| `id`         | bigint (PK) |                                                 |
| `uuid`       | uuid        | Route binding key, auto-generated               |
| `title`      | string      | Required                                        |
| `slug`       | string      | Auto-generated from title, unique               |
| `publish`    | boolean     | Visibility toggle, default `false`              |
| `sort_order` | integer     | Display ordering, default `0`                   |
| `created_at` | timestamp   |                                                 |
| `updated_at` | timestamp   |                                                 |

### Relations

- **Projects:** `HasMany` → `Project` (via `topic_id`)

---

## Sidebar

| Label  | Route                  | Icon      |
|--------|------------------------|-----------|
| Themen | `/dashboard/topics`    | `PhTag`   |

Placed directly after Projekte.

---

## Routes

### Web (Blade shell)

| Name             | Method | Path                             | Component     |
|------------------|--------|----------------------------------|---------------|
| `topics.index`   | GET    | `/dashboard/topics`              | `TopicIndex`  |
| `topics.create`  | GET    | `/dashboard/topics/create`       | `TopicForm`   |
| `topics.edit`    | GET    | `/dashboard/topics/:id/edit`     | `TopicForm`   |

### API

| Method | Route                                  | Action                  |
|--------|----------------------------------------|-------------------------|
| GET    | `/api/dashboard/topics`                | List all topics         |
| POST   | `/api/dashboard/topics`                | Create topic            |
| GET    | `/api/dashboard/topics/{topic}`        | Show single topic       |
| PUT    | `/api/dashboard/topics/{topic}`        | Update topic            |
| DELETE | `/api/dashboard/topics/{topic}`        | Delete topic            |
| PATCH  | `/api/dashboard/topics/{topic}/publish`| Toggle publish          |
| PATCH  | `/api/dashboard/topics/reorder`        | Batch reorder           |

---

## Backend

### Model

`App\Models\Topic`

- Uses traits: `HasUuid`, `HasFactory`
- Casts: `publish` (boolean)
- Fillable: `uuid`, `title`, `slug`, `publish`, `sort_order`
- Relation: `projects()` → `HasMany(Project::class)`

### Controllers

- `App\Http\Controllers\Dashboard\TopicController` — serves Blade shell for index/create/edit
- `App\Http\Controllers\Api\TopicController` — JSON API (index, show, store, update, destroy, publish toggle, reorder)

### Action Classes

| Action                | Purpose                        |
|-----------------------|--------------------------------|
| `StoreTopicAction`    | Generate slug, create topic    |
| `UpdateTopicAction`   | Regenerate slug if needed, update topic |
| `DeleteTopicAction`   | Delete topic (nullify `topic_id` on projects) |
| `ReorderTopicsAction` | Batch update `sort_order`      |

### Form Request

`App\Http\Requests\Dashboard\Topic\StoreTopicRequest`

| Field   | Rules                        |
|---------|------------------------------|
| `title` | required, string, max:255    |
| `publish`| boolean                     |

### API Resource

`App\Http\Resources\Dashboard\TopicResource`

Returns: `uuid`, `title`, `slug`, `publish`, `sort_order`.

---

## Frontend (Vue)

### File Structure

```
resources/js/app/
├── api/
│   └── topics.js               # Axios API service
├── stores/
│   └── topics.js               # Pinia store
└── views/
    └── topics/
        ├── Index.vue           # List view
        └── Form.vue            # Create/edit form
```

### API Service (`api/topics.js`)

```js
index:   () => api.get('/topics')
show:    (id) => api.get(`/topics/${id}`)
store:   (data) => api.post('/topics', data)
update:  (id, data) => api.put(`/topics/${id}`, data)
toggle:  (id) => api.patch(`/topics/${id}/publish`)
destroy: (id) => api.delete(`/topics/${id}`)
reorder: (items) => api.patch('/topics/reorder', { items })
```

### Pinia Store (`stores/topics.js`)

State: `topics[]`, `current`, `loading`, `errors`

Actions: `fetchTopics`, `fetchTopic`, `saveTopic`, `toggle`, `deleteTopic`

### Index View (`views/topics/Index.vue`)

- `PageHeader` with title "Themen" and "Neues Thema" button
- `DataTable` with columns: title, publish status, actions (edit, delete, publish toggle)
- Drag-and-drop reorder support
- Empty state: "Noch keine Themen vorhanden."

### Form View (`views/topics/Form.vue`)

- `PageHeader` with title "Neues Thema" / "Thema bearbeiten", cancel + save buttons
- `FormInput` for title

### Router Registration

```js
{ path: '/dashboard/topics',            name: 'topics.index',  component: TopicIndex, meta: { title: 'Themen' } }
{ path: '/dashboard/topics/create',     name: 'topics.create', component: TopicForm,  meta: { title: 'Neues Thema' } }
{ path: '/dashboard/topics/:id/edit',   name: 'topics.edit',   component: TopicForm,  meta: { title: 'Thema bearbeiten' } }
```

---

## Frontend Behavior (Public Site)

Not directly visible as its own page. Used for grouping/filtering projects on the Werkliste (future feature).
