# Dashboard Requirements: Overview

## Project

Dashboard for **weberbrunner.ch** - a portfolio website for weberbrunner architekten.

## Scope

Full CRUD management for:
- Projects (with attributes, media, categories, statuses)
- Team members (with CV/biography)
- Jobs
- Talks/Lectures
- Awards
- Jury roles
- Network/Partners
- Locations (Zürich, Berlin, etc.)

---

## Tech Stack

| Layer | Technology |
|-------|------------|
| Backend | Laravel 12 |
| Frontend Views | Blade |
| Interactive Components | Vue.js 3 |
| State Management | Pinia |
| HTTP Client | Axios |
| CSS | Tailwind CSS 4 |
| Database | MySQL |
| Auth | Laravel Breeze (Blade) |
| File Uploads | Uppy (chunked uploads) |
| Image Processing | League Glide (existing) |

---

## Architecture Principles

### Do

- **Policies & Middleware** for authorization and route protection
- **API Resources** for consistent JSON responses
- **Form Request classes** for validation
- **Action classes** for business logic (single responsibility)
- **Slim controllers** (delegate to actions/services)
- **Service classes** only when genuinely needed
- **Components everywhere** (Blade + Vue)
- **Inline Tailwind classes** - avoid large CSS files

### Don't

- Bloated `app.css` with hundreds of custom classes
- Fat controllers with business logic
- Direct Eloquent queries in controllers
- Validation logic in controllers

---

## URL Structure

- Dashboard: `/dashboard`
- API: `/api/dashboard/*` (session-based auth, no tokens)

---

## Route Naming Convention

```
dashboard.{entity}.index    GET     /dashboard/{entity}
dashboard.{entity}.create   GET     /dashboard/{entity}/create
dashboard.{entity}.store    POST    /dashboard/{entity}
dashboard.{entity}.edit     GET     /dashboard/{entity}/{uuid}/edit
dashboard.{entity}.update   PUT     /dashboard/{entity}/{uuid}
dashboard.{entity}.destroy  DELETE  /dashboard/{entity}/{uuid}
```

API routes follow same pattern under `/api/dashboard/`.

**Note:** All routes use UUID for identification, not integer IDs. See "UUID Pattern" below.

---

## Route Definition Pattern

Use Laravel's controller groups for cleaner route definitions:

```php
// routes/web.php

Route::prefix('dashboard')
    ->middleware(['auth'])
    ->group(function () {

        // Projects
        Route::controller(ProjectController::class)
            ->prefix('projects')
            ->name('dashboard.projects.')
            ->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::get('/{project}/edit', 'edit')->name('edit');
            });

        // Team, Jobs, etc. follow same pattern...
    });
```

```php
// routes/api.php

Route::prefix('dashboard')
    ->middleware(['auth'])
    ->group(function () {

        // Projects API
        Route::controller(Api\ProjectController::class)
            ->prefix('projects')
            ->group(function () {
                Route::get('/', 'index');
                Route::post('/', 'store');
                Route::get('/{project}', 'show');
                Route::put('/{project}', 'update');
                Route::delete('/{project}', 'destroy');
                Route::post('/{project}/restore', 'restore');
                Route::patch('/reorder', 'reorder');
            });

        // Nested resources
        Route::controller(Api\ProjectAttributeController::class)
            ->prefix('projects/{project}/attributes')
            ->group(function () {
                Route::post('/', 'store');
                Route::put('/{attribute}', 'update');
                Route::delete('/{attribute}', 'destroy');
                Route::patch('/reorder', 'reorder');
            });

    });
```

**Benefits:**
- Controller only specified once per group
- Cleaner, more readable route files
- Easy to see all routes for an entity at a glance

---

## Folder Structure (proposed)

```
app/
├── Actions/
│   └── Dashboard/
│       ├── Project/
│       │   ├── StoreProjectAction.php
│       │   ├── UpdateProjectAction.php
│       │   └── DeleteProjectAction.php
│       ├── Team/
│       ├── Job/
│       └── ...
├── Http/
│   ├── Controllers/
│   │   └── Dashboard/
│   │       ├── ProjectController.php
│   │       ├── TeamController.php
│   │       └── ...
│   ├── Requests/
│   │   └── Dashboard/
│   │       ├── Project/
│   │       │   ├── StoreProjectRequest.php
│   │       │   └── UpdateProjectRequest.php
│   │       └── ...
│   └── Resources/
│       └── Dashboard/
│           ├── ProjectResource.php
│           ├── TeamResource.php
│           └── ...
├── Models/
├── Policies/
│   ├── ProjectPolicy.php
│   ├── TeamPolicy.php
│   └── ...
├── Traits/
│   ├── HasUuid.php
│   └── Sortable.php
└── Services/
    └── (only if needed)

resources/
├── js/
│   ├── dashboard/
│   │   ├── app.js
│   │   ├── components/
│   │   │   ├── forms/
│   │   │   ├── tables/
│   │   │   ├── media/
│   │   │   └── ui/
│   │   ├── composables/
│   │   ├── stores/
│   │   └── views/
│   │       ├── projects/
│   │       ├── team/
│   │       └── ...
└── views/
    └── dashboard/
        ├── layouts/
        └── pages/
```

---

## Shared Features

These features apply to multiple entities:

| Feature | Description |
|---------|-------------|
| Soft Deletes | Trash/restore functionality |
| Drag & Drop Sorting | Reorder items with `sort_order` field |
| Activity Log | Track changes (who, what, when) |

See `07-shared-features.md` for details.

---

## Database Conventions

- Use `snake_case` for table/column names
- Timestamps on all tables (`created_at`, `updated_at`)
- Soft deletes where specified (`deleted_at`)
- `uuid` column on all entities (for external exposure)
- `sort_order` integer for sortable entities
- `publish` boolean for publishable content
- `slug` unique string for URL-friendly identifiers

---

## UUID Pattern

All entities use UUIDs for external identification (API responses, routes). Integer IDs remain as primary keys for internal database relations.

### Why

- Prevents enumeration attacks (can't guess `/api/projects/2`, `/api/projects/3`)
- No information leakage about record counts
- Future-proof for public-facing modules

### Implementation

**Trait:**
```php
// app/Traits/HasUuid.php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    protected static function bootHasUuid(): void
    {
        static::creating(fn ($model) => $model->uuid ??= Str::uuid());
    }

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }
}
```

**Migration pattern:**
```php
$table->uuid('uuid')->unique()->after('id');
```

**Model usage:**
```php
class Project extends Model
{
    use HasUuid, SoftDeletes, Sortable;
}
```

**API Resource:**
```php
// Always expose uuid, never expose integer id
public function toArray($request): array
{
    return [
        'uuid' => $this->uuid,
        'title' => $this->title,
        // ...
    ];
}
```

### Entities with UUID

All content entities:
- User
- Project, Category, Status, ProjectAttribute
- TeamMember, TeamMemberBio
- Job, Talk, Award, Jury, NetworkEntry
- Location
- Media

---

## API Response Format

### Success (single item)
```json
{
  "data": { ... }
}
```

### Success (collection)
```json
{
  "data": [ ... ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 100
  }
}
```

### Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": ["The title field is required."]
  }
}
```

---

## Language

Single language: **German**

No i18n/translation system required.
