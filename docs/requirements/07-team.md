# Team Members

## Overview

Team members are displayed on the Atelier > Team sub-page. Each member shows name, title, email, and an expandable CV. Former employees are listed under a collapsible "Ehemalige Mitarbeitende" section.

---

## Database Schema

### `team_members`

| Field        | Type        | Notes                                          |
|--------------|-------------|-------------------------------------------------|
| `id`         | bigint (PK) |                                                 |
| `uuid`       | uuid        | Route binding key, auto-generated               |
| `firstname`  | string      | Required                                        |
| `name`       | string      | Required (last name)                            |
| `title`      | string (null)| Professional title, e.g. "Architektin MSc ETH" |
| `email`      | string (null)| e.g. "kzi@forrerzimmermann.ch"                 |
| `cv`         | text (null) | Rich text, expandable on public site            |
| `publish`    | boolean     | Visibility toggle, default `false`              |
| `former`     | boolean     | Listed under "Ehemalige Mitarbeitende", default `false` |
| `sort_order` | integer     | Display ordering, default `0`                   |
| `created_at` | timestamp   |                                                 |
| `updated_at` | timestamp   |                                                 |

### Relations

- **Media:** Polymorphic `MorphMany` — one portrait image per member

---

## Sidebar

| Label | Route                | Icon      |
|-------|----------------------|-----------|
| Team  | `/dashboard/team`    | `PhUsers` |

---

## Routes

### Web (Blade shell)

| Name           | Method | Path                           | Component    |
|----------------|--------|--------------------------------|--------------|
| `team.index`   | GET    | `/dashboard/team`              | `TeamIndex`  |
| `team.create`  | GET    | `/dashboard/team/create`       | `TeamForm`   |
| `team.edit`    | GET    | `/dashboard/team/:id/edit`     | `TeamForm`   |

### API

| Method | Route                                   | Action                  |
|--------|-----------------------------------------|-------------------------|
| GET    | `/api/dashboard/team`                   | List all members        |
| POST   | `/api/dashboard/team`                   | Create member           |
| GET    | `/api/dashboard/team/{member}`          | Show single member      |
| PUT    | `/api/dashboard/team/{member}`          | Update member           |
| DELETE | `/api/dashboard/team/{member}`          | Delete member           |
| PATCH  | `/api/dashboard/team/{member}/publish`  | Toggle publish          |
| PATCH  | `/api/dashboard/team/reorder`           | Batch reorder           |

---

## Backend

### Model

`App\Models\TeamMember`

- Uses traits: `HasUuid`, `HasFactory`
- Casts: `publish` (boolean), `former` (boolean)
- Fillable: `uuid`, `firstname`, `name`, `title`, `email`, `cv`, `publish`, `former`, `sort_order`
- Relation: `media()` → `MorphMany(Media::class, 'mediable')->orderBy('sort_order')`

### Controllers

- `App\Http\Controllers\Dashboard\TeamController` — serves Blade shell for index/create/edit
- `App\Http\Controllers\Api\TeamController` — JSON API (index, show, store, update, destroy, publish toggle, reorder)

### Action Classes

| Action                    | Purpose                                      |
|---------------------------|----------------------------------------------|
| `StoreTeamMemberAction`   | Create member, attach media                  |
| `UpdateTeamMemberAction`  | Update member, sync media                    |
| `DeleteTeamMemberAction`  | Delete member + clean up media               |
| `ReorderTeamMembersAction`| Batch update `sort_order`                    |

### Form Request

`App\Http\Requests\Dashboard\TeamMember\StoreTeamMemberRequest`

| Field       | Rules                        |
|-------------|------------------------------|
| `firstname` | required, string, max:255    |
| `name`      | required, string, max:255    |
| `title`     | nullable, string, max:255    |
| `email`     | nullable, string, email      |
| `cv`        | nullable, string             |
| `publish`   | boolean                      |
| `former`    | boolean                      |
| `media`     | nullable, array              |

### API Resource

`App\Http\Resources\Dashboard\TeamMemberResource`

Returns: `uuid`, `firstname`, `name`, `title`, `email`, `cv`, `publish`, `former`, `sort_order`, `media` (nested `MediaResource`).

---

## Frontend (Vue)

### File Structure

```
resources/js/app/
├── api/
│   └── team.js                 # Axios API service
├── stores/
│   └── team.js                 # Pinia store
└── views/
    └── team/
        ├── Index.vue           # List view
        └── Form.vue            # Create/edit form
```

### API Service (`api/team.js`)

```js
index:   () => api.get('/team')
show:    (id) => api.get(`/team/${id}`)
store:   (data) => api.post('/team', data)
update:  (id, data) => api.put(`/team/${id}`, data)
toggle:  (id) => api.patch(`/team/${id}/publish`)
destroy: (id) => api.delete(`/team/${id}`)
reorder: (items) => api.patch('/team/reorder', { items })
```

### Pinia Store (`stores/team.js`)

State: `members[]`, `current`, `loading`, `errors`

Actions: `fetchMembers`, `fetchMember`, `saveMember`, `toggle`, `deleteMember`

### Index View (`views/team/Index.vue`)

- `PageHeader` with title "Team" and "Neues Mitglied" button
- `DataTable` with columns: portrait thumbnail, name (firstname + name), title, former badge, publish status, actions (edit, delete, publish toggle)
- Drag-and-drop reorder support
- Empty state: "Noch keine Mitglieder vorhanden."

### Form View (`views/team/Form.vue`)

- `PageHeader` with title "Neues Mitglied" / "Mitglied bearbeiten", cancel + save buttons
- `FormInput` for firstname, name, title, email
- `FormCheckbox` for former
- `Editor` (Tiptap) for cv
- `MediaUploader` + `MediaGrid` for portrait image
- `MediaEdit` drawer for alt editing

### Router Registration

```js
{ path: '/dashboard/team',            name: 'team.index',  component: TeamIndex, meta: { title: 'Team' } }
{ path: '/dashboard/team/create',     name: 'team.create', component: TeamForm,  meta: { title: 'Neues Mitglied' } }
{ path: '/dashboard/team/:id/edit',   name: 'team.edit',   component: TeamForm,  meta: { title: 'Mitglied bearbeiten' } }
```

---

## Frontend Behavior (Public Site)

- Displayed on Atelier > Team sub-page, right side (~35%)
- Active members listed first (ordered by `sort_order`), filtered by `publish = true`
- Former members (`former = true`) listed under collapsible "Ehemalige Mitarbeitende" section
- Each member shows: name, title, email
- CV expands/collapses inline (accordion)
