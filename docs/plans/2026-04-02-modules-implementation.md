# Modules Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Implement all dashboard modules (03-10) for forrerzimmermann.ch: Landing, Topics, Projects (reworked), Atelier, Kontakt, Team (reworked), Jobs (reworked), Settings — plus shared infrastructure (HasUuid trait, updated User model, sidebar).

**Architecture:** Each module follows the established pattern: Migration + Model + Actions + FormRequest + ApiResource + ApiController + WebController(blade shell) + Vue (api service, pinia store, Index/Form views). The existing media system, Uppy uploader, editor, form components, and DataTable are reused. All API routes are session-authenticated under `/api/dashboard/`. All models use UUID for route binding.

**Tech Stack:** Laravel 12, Vue 3, Pinia, Axios, Tailwind CSS 4, Tiptap editor, Phosphor Icons

---

## Task 1: Shared Infrastructure — HasUuid Trait

**Files:**
- Create: `app/Traits/HasUuid.php`

**Step 1: Create the HasUuid trait**

```php
<?php

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

**Step 2: Update existing Media model to use HasUuid**

Modify `app/Models/Media.php`: replace the manual boot uuid logic with the `HasUuid` trait. Remove the custom `boot()` method and `getRouteKeyName()` method since the trait provides both.

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Media extends Model
{
    use HasFactory, HasUuid;

    // ... rest stays the same, but remove boot() and getRouteKeyName()
}
```

**Step 3: Commit**

```
feat: add HasUuid trait and refactor Media model to use it
```

---

## Task 2: Delete Old Migrations & Models (Projects, Team, Jobs)

The old migrations and models have wrong schemas. Delete and replace.

**Files to delete:**
- `database/migrations/2026_02_21_000002_create_projects_table.php`
- `database/migrations/2026_02_22_000004_create_domain_jobs_table.php`
- `database/migrations/2026_02_22_000008_create_team_table.php`

**Step 1: Delete old migration files**

```bash
rm database/migrations/2026_02_21_000002_create_projects_table.php
rm database/migrations/2026_02_22_000004_create_domain_jobs_table.php
rm database/migrations/2026_02_22_000008_create_team_table.php
```

**Step 2: Commit**

```
chore: remove old project/team/job migrations for clean replacement
```

---

## Task 3: Topics Module (Full Stack)

Topics must exist before Projects (FK dependency).

**Files:**
- Create: `database/migrations/2026_04_02_000001_create_topics_table.php`
- Create: `app/Models/Topic.php`
- Create: `app/Actions/Topic/StoreAction.php`
- Create: `app/Actions/Topic/UpdateAction.php`
- Create: `app/Actions/Topic/DeleteAction.php`
- Create: `app/Actions/Topic/ReorderAction.php`
- Create: `app/Http/Requests/Topic/StoreTopicRequest.php`
- Create: `app/Http/Requests/Topic/UpdateTopicRequest.php`
- Create: `app/Http/Resources/TopicResource.php`
- Create: `app/Http/Controllers/Api/TopicController.php`
- Create: `resources/js/app/api/topics.js`
- Create: `resources/js/app/stores/topics.js`
- Create: `resources/js/app/views/topics/Index.vue`
- Create: `resources/js/app/views/topics/Form.vue`
- Modify: `routes/api.php` — add topic routes
- Modify: `resources/js/app/router/index.js` — add topic routes

### Migration

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->after('id');
            $table->string('title');
            $table->string('slug')->unique();
            $table->boolean('publish')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topics');
    }
};
```

### Model

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Topic extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'title', 'slug', 'publish', 'sort_order',
    ];

    protected $casts = [
        'publish' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }
}
```

### Actions

**StoreAction:**
```php
<?php

namespace App\Actions\Topic;

use App\Models\Topic;
use Illuminate\Support\Str;

class StoreAction
{
    public function execute(array $data): Topic
    {
        $data['slug'] = Str::slug($data['title']);
        return Topic::create($data);
    }
}
```

**UpdateAction:**
```php
<?php

namespace App\Actions\Topic;

use App\Models\Topic;
use Illuminate\Support\Str;

class UpdateAction
{
    public function execute(Topic $topic, array $data): Topic
    {
        $data['slug'] = Str::slug($data['title']);
        $topic->update($data);
        return $topic;
    }
}
```

**DeleteAction:**
```php
<?php

namespace App\Actions\Topic;

use App\Models\Topic;

class DeleteAction
{
    public function execute(Topic $topic): void
    {
        $topic->delete();
    }
}
```

**ReorderAction:**
```php
<?php

namespace App\Actions\Topic;

use App\Models\Topic;

class ReorderAction
{
    public function execute(array $items): void
    {
        foreach ($items as $item) {
            Topic::where('uuid', $item['uuid'])->update(['sort_order' => $item['sort_order']]);
        }
    }
}
```

### Form Requests

**StoreTopicRequest:**
```php
<?php

namespace App\Http\Requests\Topic;

use Illuminate\Foundation\Http\FormRequest;

class StoreTopicRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'publish' => 'boolean',
        ];
    }
}
```

**UpdateTopicRequest:** Same rules, same file pattern (separate class, same rules).

### API Resource

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopicResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'slug' => $this->slug,
            'publish' => $this->publish,
            'sort_order' => $this->sort_order,
        ];
    }
}
```

### API Controller

```php
<?php

namespace App\Http\Controllers\Api;

use App\Actions\Topic\DeleteAction;
use App\Actions\Topic\ReorderAction;
use App\Actions\Topic\StoreAction;
use App\Actions\Topic\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Topic\StoreTopicRequest;
use App\Http\Requests\Topic\UpdateTopicRequest;
use App\Http\Resources\TopicResource;
use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index()
    {
        return TopicResource::collection(
            Topic::orderBy('sort_order')->orderBy('created_at', 'desc')->get()
        );
    }

    public function store(StoreTopicRequest $request)
    {
        $topic = (new StoreAction)->execute($request->validated());
        return new TopicResource($topic);
    }

    public function show(Topic $topic)
    {
        return new TopicResource($topic);
    }

    public function update(UpdateTopicRequest $request, Topic $topic)
    {
        $topic = (new UpdateAction)->execute($topic, $request->validated());
        return new TopicResource($topic);
    }

    public function toggle(Topic $topic)
    {
        $topic->update(['publish' => !$topic->publish]);
        return new TopicResource($topic);
    }

    public function destroy(Topic $topic)
    {
        (new DeleteAction)->execute($topic);
        return response()->json(null, 204);
    }

    public function reorder(Request $request)
    {
        (new ReorderAction)->execute($request->items);
        return response()->json(['message' => 'ok']);
    }
}
```

### API Routes (add to `routes/api.php`)

```php
Route::controller(TopicController::class)
    ->prefix('topics')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::patch('/reorder', 'reorder');
        Route::get('/{topic}', 'show');
        Route::put('/{topic}', 'update');
        Route::patch('/{topic}/publish', 'toggle');
        Route::delete('/{topic}', 'destroy');
    });
```

### Vue API Service (`resources/js/app/api/topics.js`)

```js
import api from './axios'

export default {
    index: () => api.get('/topics'),
    show: (id) => api.get(`/topics/${id}`),
    store: (data) => api.post('/topics', data),
    update: (id, data) => api.put(`/topics/${id}`, data),
    toggle: (id) => api.patch(`/topics/${id}/publish`),
    destroy: (id) => api.delete(`/topics/${id}`),
    reorder: (items) => api.patch('/topics/reorder', { items }),
}
```

### Pinia Store (`resources/js/app/stores/topics.js`)

```js
import { defineStore } from 'pinia'
import topicsApi from '@/api/topics'

export const useTopicStore = defineStore('topics', {
    state: () => ({
        topics: [],
        current: null,
        loading: false,
        errors: {},
    }),

    actions: {
        async fetchTopics() {
            this.loading = true
            try {
                const { data } = await topicsApi.index()
                this.topics = data.data
            } finally {
                this.loading = false
            }
        },

        async fetchTopic(id) {
            this.loading = true
            try {
                const { data } = await topicsApi.show(id)
                this.current = data.data
            } finally {
                this.loading = false
            }
        },

        async saveTopic(form, id = null) {
            this.errors = {}
            try {
                if (id) {
                    await topicsApi.update(id, form)
                } else {
                    await topicsApi.store(form)
                }
                return true
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors
                }
                return false
            }
        },

        async toggle(id) {
            const topic = this.topics.find(t => t.uuid === id)
            if (topic) topic.publish = !topic.publish
            try {
                const { data } = await topicsApi.toggle(id)
                const idx = this.topics.findIndex(t => t.uuid === id)
                if (idx !== -1) this.topics[idx] = data.data
            } catch {
                if (topic) topic.publish = !topic.publish
            }
        },

        async deleteTopic(id) {
            await topicsApi.destroy(id)
            this.topics = this.topics.filter(t => t.uuid !== id)
        },
    },
})
```

### Index View (`resources/js/app/views/topics/Index.vue`)

Follow exact pattern from existing `projects/Index.vue` but:
- Title: "Themen", button: "Neues Thema"
- Columns: title, publish, actions
- Routes: `topics.create`, `topics.edit`
- Store: `useTopicStore`, identifier: `uuid` (not `id`)
- Empty: "Noch keine Themen vorhanden."
- Delete confirm: "Thema löschen", message with `topic.title`
- Drag-and-drop reorder via store

### Form View (`resources/js/app/views/topics/Form.vue`)

Follow existing `projects/Form.vue` but simpler:
- Title: "Neues Thema" / "Thema bearbeiten"
- Fields: `FormInput` for title only
- No media, no editor
- Store: `useTopicStore`
- Identifier: `uuid`

### Router Registration (add to `resources/js/app/router/index.js`)

```js
import TopicIndex from '@/views/topics/Index.vue'
import TopicForm from '@/views/topics/Form.vue'

// Routes:
{ path: '/dashboard/topics', name: 'topics.index', component: TopicIndex, meta: { title: 'Themen' } },
{ path: '/dashboard/topics/create', name: 'topics.create', component: TopicForm, meta: { title: 'Neues Thema' } },
{ path: '/dashboard/topics/:id/edit', name: 'topics.edit', component: TopicForm, meta: { title: 'Thema bearbeiten' } },
```

**Commit:** `feat: add Topics module (full stack)`

---

## Task 4: Projects Module (Reworked)

**Files:**
- Create: `database/migrations/2026_04_02_000002_create_projects_table.php`
- Modify: `app/Models/Project.php` — new schema with HasUuid, topic relation, feature flag
- Modify: `app/Actions/Project/StoreAction.php` — slug from title+location
- Modify: `app/Actions/Project/UpdateAction.php`
- Modify: `app/Actions/Project/DeleteAction.php`
- Create: `app/Actions/Project/ReorderAction.php`
- Modify: `app/Http/Requests/Project/StoreProjectRequest.php` — new fields
- Modify: `app/Http/Requests/Project/UpdateProjectRequest.php`
- Modify: `app/Http/Resources/ProjectResource.php` — uuid-based, add topic
- Modify: `app/Http/Controllers/Api/ProjectController.php` — add feature toggle, reorder
- Modify: `resources/js/app/api/projects.js` — add feature, reorder
- Modify: `resources/js/app/stores/projects.js` — uuid-based, add feature toggle
- Modify: `resources/js/app/views/projects/Index.vue` — new columns
- Modify: `resources/js/app/views/projects/Form.vue` — new fields
- Modify: `routes/api.php` — add feature toggle and reorder routes

### Migration

```php
Schema::create('projects', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique()->after('id');
    $table->string('title');
    $table->string('location')->nullable();
    $table->string('slug')->unique();
    $table->string('subtitle')->nullable();
    $table->integer('year');
    $table->text('description')->nullable();
    $table->text('info')->nullable();
    $table->string('meta_description')->nullable();
    $table->boolean('publish')->default(false);
    $table->boolean('feature')->default(false);
    $table->integer('sort_order')->default(0);
    $table->foreignId('topic_id')->nullable()->constrained('topics')->nullOnDelete();
    $table->timestamps();
});
```

### Model

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Project extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'title', 'location', 'slug', 'subtitle', 'year',
        'description', 'info', 'meta_description',
        'publish', 'feature', 'sort_order', 'topic_id',
    ];

    protected $casts = [
        'publish' => 'boolean',
        'feature' => 'boolean',
        'year' => 'integer',
        'sort_order' => 'integer',
    ];

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    public function teaser(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->where('is_teaser', true);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(Topic::class);
    }
}
```

### StoreAction — slug from title + location

```php
<?php

namespace App\Actions\Project;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Project;
use Illuminate\Support\Str;

class StoreAction
{
    public function execute(array $data): Project
    {
        $media = $data['media'] ?? [];
        unset($data['media']);

        $slugParts = [$data['title']];
        if (!empty($data['location'])) {
            $slugParts[] = $data['location'];
        }
        $data['slug'] = Str::slug(implode(' ', $slugParts));

        $project = Project::create($data);

        if (!empty($media)) {
            (new AttachMediaAction)->execute($media, $project);
        }

        return $project;
    }
}
```

### UpdateAction

```php
<?php

namespace App\Actions\Project;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\Project;
use Illuminate\Support\Str;

class UpdateAction
{
    public function execute(Project $project, array $data): Project
    {
        $media = $data['media'] ?? [];
        unset($data['media']);

        $slugParts = [$data['title']];
        if (!empty($data['location'])) {
            $slugParts[] = $data['location'];
        }
        $data['slug'] = Str::slug(implode(' ', $slugParts));

        $project->update($data);

        if (!empty($media)) {
            (new AttachMediaAction)->execute($media, $project);
        }

        return $project;
    }
}
```

### ReorderAction

```php
<?php

namespace App\Actions\Project;

use App\Models\Project;

class ReorderAction
{
    public function execute(array $items): void
    {
        foreach ($items as $item) {
            Project::where('uuid', $item['uuid'])->update(['sort_order' => $item['sort_order']]);
        }
    }
}
```

### StoreProjectRequest

```php
<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'year' => 'required|integer',
            'description' => 'nullable|string',
            'info' => 'nullable|string',
            'meta_description' => 'nullable|string|max:255',
            'publish' => 'boolean',
            'feature' => 'boolean',
            'topic_id' => 'nullable|exists:topics,id',
            'media' => 'nullable|array',
            'media.*.uuid' => 'required|string',
            'media.*.file' => 'required|string',
            'media.*.original_name' => 'required|string',
            'media.*.mime_type' => 'required|string',
            'media.*.size' => 'required|integer',
            'media.*.width' => 'nullable|integer',
            'media.*.height' => 'nullable|integer',
            'media.*.alt' => 'nullable|string|max:255',
            'media.*.caption' => 'nullable|string|max:255',
        ];
    }
}
```

### ProjectResource

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
            'location' => $this->location,
            'slug' => $this->slug,
            'subtitle' => $this->subtitle,
            'year' => $this->year,
            'description' => $this->description,
            'info' => $this->info,
            'meta_description' => $this->meta_description,
            'publish' => $this->publish,
            'feature' => $this->feature,
            'sort_order' => $this->sort_order,
            'topic' => new TopicResource($this->whenLoaded('topic')),
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
```

### API Controller — add feature toggle and reorder

```php
<?php

namespace App\Http\Controllers\Api;

use App\Actions\Project\DeleteAction;
use App\Actions\Project\ReorderAction;
use App\Actions\Project\StoreAction;
use App\Actions\Project\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Project\StoreProjectRequest;
use App\Http\Requests\Project\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::with('topic')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request)
    {
        $project = (new StoreAction)->execute($request->validated());
        return new ProjectResource($project->load('topic'));
    }

    public function show(Project $project)
    {
        return new ProjectResource($project->load(['media', 'topic']));
    }

    public function update(UpdateProjectRequest $request, Project $project)
    {
        $project = (new UpdateAction)->execute($project, $request->validated());
        return new ProjectResource($project->load(['media', 'topic']));
    }

    public function toggle(Project $project)
    {
        $project->update(['publish' => !$project->publish]);
        return new ProjectResource($project);
    }

    public function feature(Project $project)
    {
        $project->update(['feature' => !$project->feature]);
        return new ProjectResource($project);
    }

    public function destroy(Project $project)
    {
        (new DeleteAction)->execute($project);
        return response()->json(null, 204);
    }

    public function reorder(Request $request)
    {
        (new ReorderAction)->execute($request->items);
        return response()->json(['message' => 'ok']);
    }
}
```

### API Routes (replace existing project routes in `routes/api.php`)

```php
Route::controller(ProjectController::class)
    ->prefix('projects')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::patch('/reorder', 'reorder');
        Route::get('/{project}', 'show');
        Route::put('/{project}', 'update');
        Route::patch('/{project}/publish', 'toggle');
        Route::patch('/{project}/feature', 'feature');
        Route::delete('/{project}', 'destroy');
    });
```

### Vue API Service — add feature and reorder

```js
import api from './axios'

export default {
    index: () => api.get('/projects'),
    show: (id) => api.get(`/projects/${id}`),
    store: (data) => api.post('/projects', data),
    update: (id, data) => api.put(`/projects/${id}`, data),
    toggle: (id) => api.patch(`/projects/${id}/publish`),
    feature: (id) => api.patch(`/projects/${id}/feature`),
    destroy: (id) => api.delete(`/projects/${id}`),
    reorder: (items) => api.patch('/projects/reorder', { items }),
}
```

### Pinia Store — uuid-based, add feature toggle

Update `resources/js/app/stores/projects.js`:
- All identifiers use `uuid` (not `id`)
- Add `toggleFeature` action (same pattern as `toggle`)

### Index View updates

- Columns: thumbnail (from media teaser), title, location, year, publish, feature, actions
- Use `row.uuid` for all identifiers
- Add feature toggle button (star icon `PhStar`)

### Form View updates

- Remove `name`, `status`, `competition` fields
- Add: `subtitle` (FormInput), `meta_description` (FormInput), `feature` (FormCheckbox)
- Add: `topic_id` (FormSelect, options loaded from topics API)
- `year` is required (not nullable)
- Use `uuid` as identifier

**Commit:** `feat: rework Projects module with new schema`

---

## Task 5: Landing Module (Full Stack)

**Files:**
- Create: `database/migrations/2026_04_02_000003_create_landing_slides_table.php`
- Create: `app/Models/LandingSlide.php`
- Create: `app/Actions/Landing/StoreAction.php`
- Create: `app/Actions/Landing/UpdateAction.php`
- Create: `app/Actions/Landing/DeleteAction.php`
- Create: `app/Actions/Landing/ReorderAction.php`
- Create: `app/Http/Requests/Landing/StoreLandingSlideRequest.php`
- Create: `app/Http/Requests/Landing/UpdateLandingSlideRequest.php`
- Create: `app/Http/Resources/LandingSlideResource.php`
- Create: `app/Http/Controllers/Api/LandingController.php`
- Create: `resources/js/app/api/landing.js`
- Create: `resources/js/app/stores/landing.js`
- Create: `resources/js/app/views/landing/Index.vue`
- Create: `resources/js/app/views/landing/Form.vue`
- Modify: `routes/api.php`
- Modify: `resources/js/app/router/index.js`

### Migration

```php
Schema::create('landing_slides', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique()->after('id');
    $table->enum('type', ['image', 'image_text']);
    $table->text('text')->nullable();
    $table->boolean('publish')->default(false);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

### Model

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class LandingSlide extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'type', 'text', 'publish', 'sort_order',
    ];

    protected $casts = [
        'publish' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

### Actions

Follow same pattern as Topics but with media attach/sync:
- **StoreAction:** Create slide, attach media
- **UpdateAction:** Update slide, attach new media
- **DeleteAction:** Delete slide + clean up media (same pattern as Project DeleteAction)
- **ReorderAction:** Batch update sort_order by uuid

### Form Request

```php
'type' => 'required|in:image,image_text',
'text' => 'nullable|required_if:type,image_text|string',
'publish' => 'boolean',
'media' => 'nullable|array',
// + media.* sub-rules
```

### API Resource

Returns: `uuid`, `type`, `text`, `publish`, `sort_order`, `media` (nested).

### API Controller

Full CRUD + publish toggle + reorder. Same pattern as TopicController but with media loading.

### API Routes

```php
Route::controller(LandingController::class)
    ->prefix('landing')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::patch('/reorder', 'reorder');
        Route::get('/{slide}', 'show');
        Route::put('/{slide}', 'update');
        Route::patch('/{slide}/publish', 'toggle');
        Route::delete('/{slide}', 'destroy');
    });
```

### Vue Frontend

- **api/landing.js:** index, show, store, update, toggle, destroy, reorder
- **stores/landing.js:** Same pattern as projects store, identifier `uuid`
- **views/landing/Index.vue:**
  - Title: "Startseite", button: "Neuer Slide"
  - Columns: thumbnail, type, publish, actions
  - Drag-and-drop reorder
  - Empty: "Noch keine Slides vorhanden."
- **views/landing/Form.vue:**
  - Title: "Neuer Slide" / "Slide bearbeiten"
  - FormSelect for type: `[{ value: 'image', label: 'Bild' }, { value: 'image_text', label: 'Bild + Text' }]`
  - Editor (Tiptap) for text — conditionally shown when type is `image_text`
  - MediaUploader + MediaGrid for single image
  - MediaEdit drawer

### Router

```js
{ path: '/dashboard/landing', name: 'landing.index', component: LandingIndex, meta: { title: 'Startseite' } },
{ path: '/dashboard/landing/create', name: 'landing.create', component: LandingForm, meta: { title: 'Neuer Slide' } },
{ path: '/dashboard/landing/:id/edit', name: 'landing.edit', component: LandingForm, meta: { title: 'Slide bearbeiten' } },
```

**Commit:** `feat: add Landing module (homepage slides)`

---

## Task 6: Atelier Module (Full Stack)

**Files:**
- Create: `database/migrations/2026_04_02_000004_create_atelier_pages_table.php`
- Create: `app/Models/AtelierPage.php`
- Create: `app/Actions/Atelier/UpdateAction.php`
- Create: `app/Http/Requests/Atelier/UpdateAtelierPageRequest.php`
- Create: `app/Http/Resources/AtelierPageResource.php`
- Create: `app/Http/Controllers/Api/AtelierController.php`
- Create: `resources/js/app/api/atelier.js`
- Create: `resources/js/app/stores/atelier.js`
- Create: `resources/js/app/views/atelier/Index.vue`
- Create: `resources/js/app/views/atelier/Form.vue`
- Modify: `routes/api.php`
- Modify: `resources/js/app/router/index.js`
- Modify: `database/seeders/DatabaseSeeder.php` — seed 3 fixed records

### Migration

```php
Schema::create('atelier_pages', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique()->after('id');
    $table->string('slug')->unique();
    $table->string('title')->nullable();
    $table->text('text')->nullable();
    $table->string('meta_description')->nullable();
    $table->boolean('publish')->default(false);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

### Model

Uses `HasUuid`, `HasFactory`. Fillable: all fields. Has `media()` morphMany.

### Seeder (add to DatabaseSeeder)

```php
use App\Models\AtelierPage;

AtelierPage::firstOrCreate(['slug' => 'profil'], ['sort_order' => 0]);
AtelierPage::firstOrCreate(['slug' => 'team'], ['sort_order' => 1]);
AtelierPage::firstOrCreate(['slug' => 'jobs'], ['sort_order' => 2]);
```

### Actions

Only **UpdateAction** — no create/delete. Handles media attach.

### Form Request

```php
'title' => 'nullable|string|max:255',
'text' => 'nullable|string',
'meta_description' => 'nullable|string|max:255',
'publish' => 'boolean',
'media' => 'nullable|array',
// + media.* sub-rules
```

### API Resource

Returns: `uuid`, `slug`, `title`, `text`, `meta_description`, `publish`, `sort_order`, `media`.

### API Controller

Only: index, show, update, publish toggle. No create/delete/reorder.

### API Routes

```php
Route::controller(AtelierController::class)
    ->prefix('atelier')
    ->group(function () {
        Route::get('/', 'index');
        Route::get('/{page}', 'show');
        Route::put('/{page}', 'update');
        Route::patch('/{page}/publish', 'toggle');
    });
```

### Vue Frontend

- **api/atelier.js:** index, show, update, toggle (no store/destroy/reorder)
- **stores/atelier.js:** pages[], current, loading, errors. Actions: fetchPages, fetchPage, savePage, toggle
- **views/atelier/Index.vue:**
  - Title: "Atelier" (no create button)
  - Simple list: page name (capitalize slug: Profil/Team/Jobs), publish status, edit link
  - No drag-and-drop
- **views/atelier/Form.vue:**
  - Title based on slug: "Profil bearbeiten" / "Team bearbeiten" / "Jobs bearbeiten"
  - Conditional fields based on slug:
    - `profil`: title, text (Editor), meta_description, media
    - `team`: meta_description, media
    - `jobs`: meta_description, media

### Router

```js
{ path: '/dashboard/atelier', name: 'atelier.index', component: AtelierIndex, meta: { title: 'Atelier' } },
{ path: '/dashboard/atelier/:id/edit', name: 'atelier.edit', component: AtelierForm, meta: { title: 'Atelier bearbeiten' } },
```

**Commit:** `feat: add Atelier module (page content for Profil/Team/Jobs)`

---

## Task 7: Kontakt Module (Full Stack)

**Files:**
- Create: `database/migrations/2026_04_02_000005_create_contact_table.php`
- Create: `app/Models/Contact.php`
- Create: `app/Actions/Contact/UpdateAction.php`
- Create: `app/Http/Requests/Contact/UpdateContactRequest.php`
- Create: `app/Http/Resources/ContactResource.php`
- Create: `app/Http/Controllers/Api/KontaktController.php`
- Create: `resources/js/app/api/kontakt.js`
- Create: `resources/js/app/stores/kontakt.js`
- Create: `resources/js/app/views/kontakt/Form.vue`
- Modify: `routes/api.php`
- Modify: `resources/js/app/router/index.js`
- Modify: `database/seeders/DatabaseSeeder.php` — seed single record

### Migration

```php
Schema::create('contact', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique()->after('id');
    $table->string('name');
    $table->string('address');
    $table->string('email');
    $table->string('phone');
    $table->string('maps_url')->nullable();
    $table->text('imprint')->nullable();
    $table->string('meta_description')->nullable();
    $table->boolean('publish')->default(false);
    $table->timestamps();
});
```

### Model

Uses `HasUuid`. No media. Fillable: all fields.

### Seeder

```php
use App\Models\Contact;

Contact::firstOrCreate(
    ['id' => 1],
    [
        'name' => 'Forrer Zimmermann Architekten GmbH',
        'address' => 'Badenerstrasse 370, CH-8004 Zürich',
        'email' => 'mail@forrerzimmermann.ch',
        'phone' => '+41 44 548 90 01',
    ]
);
```

### Actions

Only **UpdateAction** — simple update, no media.

### Form Request

```php
'name' => 'required|string|max:255',
'address' => 'required|string|max:255',
'email' => 'required|string|email',
'phone' => 'required|string|max:255',
'maps_url' => 'nullable|string|url',
'imprint' => 'nullable|string',
'meta_description' => 'nullable|string|max:255',
'publish' => 'boolean',
```

### API Resource

Returns: `uuid`, `name`, `address`, `email`, `phone`, `maps_url`, `imprint`, `meta_description`, `publish`.

### API Controller

Only show + update. No index/create/delete.

### API Routes

```php
Route::controller(KontaktController::class)
    ->prefix('kontakt')
    ->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'update');
    });
```

### Vue Frontend

- **api/kontakt.js:** show, update (no index/store/destroy)
- **stores/kontakt.js:** contact, loading, errors. Actions: fetchContact, saveContact
- **views/kontakt/Form.vue:** (no Index view)
  - Title: "Kontakt", save button only (no cancel — singleton)
  - FormInputs: name, address, email, phone, maps_url
  - Editor (Tiptap) for imprint
  - FormInput for meta_description

### Router

```js
{ path: '/dashboard/kontakt', name: 'kontakt.edit', component: KontaktForm, meta: { title: 'Kontakt' } },
```

**Commit:** `feat: add Kontakt module (contact singleton)`

---

## Task 8: Team Module (Reworked)

**Files:**
- Create: `database/migrations/2026_04_02_000006_create_team_members_table.php`
- Modify: `app/Models/TeamMember.php` — new schema with HasUuid, `title` instead of role/position, add `former`
- Modify: `app/Actions/Team/StoreAction.php`
- Modify: `app/Actions/Team/UpdateAction.php`
- Modify: `app/Actions/Team/DeleteAction.php`
- Create: `app/Actions/Team/ReorderAction.php`
- Modify: `app/Http/Requests/Team/StoreTeamRequest.php`
- Modify: `app/Http/Requests/Team/UpdateTeamRequest.php`
- Modify: `app/Http/Resources/TeamMemberResource.php`
- Modify: `app/Http/Controllers/Api/TeamController.php` — use uuid, add reorder action
- Modify: `resources/js/app/api/team.js`
- Modify: `resources/js/app/stores/team.js` — uuid-based
- Modify: `resources/js/app/views/team/Index.vue` — new columns, former badge
- Modify: `resources/js/app/views/team/Form.vue` — new fields

### Migration

```php
Schema::create('team_members', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique()->after('id');
    $table->string('firstname');
    $table->string('name');
    $table->string('title')->nullable();
    $table->string('email')->nullable();
    $table->text('cv')->nullable();
    $table->boolean('publish')->default(false);
    $table->boolean('former')->default(false);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

### Model

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TeamMember extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'firstname', 'name', 'title', 'email', 'cv',
        'publish', 'former', 'sort_order',
    ];

    protected $casts = [
        'publish' => 'boolean',
        'former' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

Note: Remove `protected $table = 'team';` — new table is `team_members` (Laravel convention).

### Form Request

```php
'firstname' => 'required|string|max:255',
'name' => 'required|string|max:255',
'title' => 'nullable|string|max:255',
'email' => 'nullable|string|email',
'cv' => 'nullable|string',
'publish' => 'boolean',
'former' => 'boolean',
'media' => 'nullable|array',
// + media.* sub-rules
```

### API Resource

Returns: `uuid`, `firstname`, `name`, `title`, `email`, `cv`, `publish`, `former`, `sort_order`, `media`.

### API Controller

Full CRUD + toggle + reorder. Use `ReorderAction` with uuid. Route model binding uses `TeamMember $member` parameter name.

### API Routes (replace existing team routes)

```php
Route::controller(TeamController::class)
    ->prefix('team')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::patch('/reorder', 'reorder');
        Route::get('/{member}', 'show');
        Route::put('/{member}', 'update');
        Route::patch('/{member}/publish', 'toggle');
        Route::delete('/{member}', 'destroy');
    });
```

### Vue Frontend

- Update `api/team.js` — same endpoints, no changes needed
- Update `stores/team.js` — change all `id` references to `uuid`
- Update `views/team/Index.vue`:
  - Columns: portrait thumbnail, name (firstname + name), title, former badge, publish, actions
  - Use `uuid` for identifiers
- Update `views/team/Form.vue`:
  - Remove: role, position, phone fields
  - Add: `title` (replaces role), `former` (FormCheckbox)
  - Use `uuid` for identifier

**Commit:** `feat: rework Team module with new schema`

---

## Task 9: Jobs Module (Reworked)

**Files:**
- Create: `database/migrations/2026_04_02_000007_create_job_listings_table.php`
- Modify: `app/Models/Job.php` → rename to `app/Models/JobListing.php`
- Modify: `app/Actions/Job/StoreAction.php`
- Modify: `app/Actions/Job/UpdateAction.php`
- Modify: `app/Actions/Job/DeleteAction.php`
- Create: `app/Actions/Job/ReorderAction.php`
- Modify: `app/Http/Requests/Job/StoreJobRequest.php` → rename to `StoreJobListingRequest.php`
- Modify: `app/Http/Requests/Job/UpdateJobRequest.php` → rename to `UpdateJobListingRequest.php`
- Modify: `app/Http/Resources/JobResource.php` → rename to `JobListingResource.php`
- Modify: `app/Http/Controllers/Api/JobController.php`
- Modify: `resources/js/app/api/jobs.js`
- Modify: `resources/js/app/stores/jobs.js` — uuid-based
- Modify: `resources/js/app/views/jobs/Index.vue`
- Modify: `resources/js/app/views/jobs/Form.vue` — text field instead of lead+info, no media

### Migration

```php
Schema::create('job_listings', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique()->after('id');
    $table->string('title');
    $table->text('text')->nullable();
    $table->boolean('publish')->default(false);
    $table->integer('sort_order')->default(0);
    $table->timestamps();
});
```

### Model — JobListing

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobListing extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'title', 'text', 'publish', 'sort_order',
    ];

    protected $casts = [
        'publish' => 'boolean',
        'sort_order' => 'integer',
    ];
}
```

No media relation — page image is managed on atelier page with slug `jobs`.

### Actions

Simple CRUD — no media. StoreAction creates, UpdateAction updates, DeleteAction deletes, ReorderAction reorders by uuid.

### Form Request

```php
'title' => 'required|string|max:255',
'text' => 'nullable|string',
'publish' => 'boolean',
```

### API Resource

Returns: `uuid`, `title`, `text`, `publish`, `sort_order`.

### API Controller

Full CRUD + toggle + reorder. Uses `JobListing $job` for route binding.

### API Routes (replace existing job routes)

```php
Route::controller(JobController::class)
    ->prefix('jobs')
    ->group(function () {
        Route::get('/', 'index');
        Route::post('/', 'store');
        Route::patch('/reorder', 'reorder');
        Route::get('/{job}', 'show');
        Route::put('/{job}', 'update');
        Route::patch('/{job}/publish', 'toggle');
        Route::delete('/{job}', 'destroy');
    });
```

### Vue Frontend

- Update `stores/jobs.js` — use `uuid` identifiers
- Update `views/jobs/Index.vue` — columns: title, publish, actions. Use `uuid`.
- Update `views/jobs/Form.vue`:
  - Remove `lead` and `info` fields
  - Add `text` field with Editor (Tiptap)
  - Remove media entirely
  - Use `uuid` as identifier

Delete old model file `app/Models/Job.php` after creating `JobListing.php`.

**Commit:** `feat: rework Jobs module as JobListing with new schema`

---

## Task 10: Settings Module (Full Stack)

**Files:**
- Create: `database/migrations/2026_04_02_000008_create_site_settings_table.php`
- Create: `app/Models/SiteSetting.php`
- Create: `app/Actions/Settings/UpdateAction.php`
- Create: `app/Http/Requests/Settings/UpdateSiteSettingRequest.php`
- Create: `app/Http/Resources/SiteSettingResource.php`
- Create: `app/Http/Controllers/Api/SettingsController.php`
- Create: `resources/js/app/api/settings.js`
- Create: `resources/js/app/stores/settings.js`
- Modify: `resources/js/app/views/settings/Index.vue` → rewrite as Form
- Modify: `routes/api.php`
- Modify: `database/seeders/DatabaseSeeder.php`

### Migration

```php
Schema::create('site_settings', function (Blueprint $table) {
    $table->id();
    $table->uuid('uuid')->unique()->after('id');
    $table->string('site_title');
    $table->string('meta_description')->nullable();
    $table->string('landing_meta_description')->nullable();
    $table->string('projects_meta_description')->nullable();
    $table->timestamp('updated_at')->nullable();
});
```

Note: No `created_at` — only `updated_at` for singletons is fine. Actually keep both for consistency with timestamps().

```php
$table->timestamps();
```

### Model

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SiteSetting extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'site_title', 'meta_description',
        'landing_meta_description', 'projects_meta_description',
    ];

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

### Seeder

```php
use App\Models\SiteSetting;

SiteSetting::firstOrCreate(
    ['id' => 1],
    ['site_title' => 'forrer zimmermann architektur']
);
```

### Actions

**UpdateAction** — update settings, attach new media (for OG image).

### Form Request

```php
'site_title' => 'required|string|max:255',
'meta_description' => 'nullable|string|max:255',
'landing_meta_description' => 'nullable|string|max:255',
'projects_meta_description' => 'nullable|string|max:255',
'media' => 'nullable|array',
// + media.* sub-rules
```

### API Resource

Returns: `uuid`, `site_title`, `meta_description`, `landing_meta_description`, `projects_meta_description`, `media`.

### API Controller

Only show + update.

### API Routes

```php
Route::controller(SettingsController::class)
    ->prefix('settings')
    ->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'update');
    });
```

### Vue Frontend

- **api/settings.js:** show, update
- **stores/settings.js:** settings, loading, errors. Actions: fetchSettings, saveSettings
- **views/settings/Form.vue:** (rewrite existing Index.vue)
  - Title: "Einstellungen", save button
  - FormInput: site_title, meta_description ("Standard Meta Description"), landing_meta_description ("Meta Description Startseite"), projects_meta_description ("Meta Description Projekte")
  - MediaUploader + MediaGrid for OG image
  - MediaEdit drawer

### Router

Keep existing route: `{ path: '/dashboard/settings', name: 'settings.edit', ... }` — rename from `settings.index` to `settings.edit`.

**Commit:** `feat: add Settings module (site settings singleton)`

---

## Task 11: Sidebar & Router Wiring

**Files:**
- Modify: `resources/js/app/components/layout/AppSidebar.vue`
- Modify: `resources/js/app/router/index.js`

### Sidebar Navigation

```js
import {
    PhBriefcase,
    PhBuildings,
    PhEnvelope,
    PhGear,
    PhHouse,
    PhHouseSimple,
    PhSignOut,
    PhTag,
    PhUsers,
} from '@phosphor-icons/vue'

const navigation = [
    {
        items: [
            { name: 'Startseite', to: '/dashboard/landing', icon: PhHouse },
            { name: 'Projekte', to: '/dashboard/projects', icon: PhBuildings },
            { name: 'Themen', to: '/dashboard/topics', icon: PhTag },
        ],
    },
    {
        items: [
            { name: 'Atelier', to: '/dashboard/atelier', icon: PhHouseSimple },
            { name: 'Team', to: '/dashboard/team', icon: PhUsers },
            { name: 'Stellen', to: '/dashboard/jobs', icon: PhBriefcase },
        ],
    },
    {
        items: [
            { name: 'Kontakt', to: '/dashboard/kontakt', icon: PhEnvelope },
        ],
    },
    {
        items: [
            { name: 'Einstellungen', to: '/dashboard/settings', icon: PhGear },
        ],
    },
]
```

### Router

Add all new route imports and route definitions. Rename `settings.index` to `settings.edit`. Ensure all module routes are registered.

**Commit:** `feat: update sidebar navigation and router with all modules`

---

## Task 12: User Model Enhancement

**Files:**
- Create: `database/migrations/2026_04_02_000009_update_users_table.php`
- Modify: `app/Models/User.php` — add HasUuid, role, firstname
- Modify: `database/seeders/DatabaseSeeder.php` — update seeder with firstname, role

### Migration

```php
Schema::table('users', function (Blueprint $table) {
    $table->uuid('uuid')->unique()->after('id');
    $table->string('firstname')->after('uuid');
    $table->enum('role', ['admin', 'editor', 'viewer'])->default('admin')->after('email');
    $table->softDeletes();
});

// Rename 'name' column stays as is (last name)
```

### Model

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuid, Notifiable, SoftDeletes;

    protected $fillable = [
        'uuid', 'firstname', 'name', 'email', 'password', 'role',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

### Seeder update

```php
User::firstOrCreate(
    ['email' => 'm@marceli.to'],
    [
        'firstname' => 'Marcel',
        'name' => 'Stadelmann',
        'password' => Hash::make('7aq31rr23'),
        'role' => 'admin',
    ]
);
```

**Commit:** `feat: enhance User model with uuid, firstname, role, soft deletes`

---

## Task 13: Run Migrations & Seed

**Step 1: Run fresh migration + seed**

```bash
php artisan migrate:fresh --seed
```

**Step 2: Verify all tables exist**

```bash
php artisan tinker --execute="echo implode(', ', Schema::getTableListing())"
```

Expected: `users, password_reset_tokens, sessions, cache, cache_locks, jobs, job_batches, failed_jobs, media, topics, projects, landing_slides, atelier_pages, contact, team_members, job_listings, site_settings`

**Step 3: Verify seeders**

```bash
php artisan tinker --execute="echo App\Models\AtelierPage::count() . ' atelier pages, ' . App\Models\Contact::count() . ' contact, ' . App\Models\SiteSetting::count() . ' settings'"
```

Expected: `3 atelier pages, 1 contact, 1 settings`

**Commit:** no commit needed (runtime step)

---

## Task 14: Build & Smoke Test

**Step 1: Build frontend**

```bash
npm run build
```

**Step 2: Verify routes**

```bash
php artisan route:list --path=api/dashboard
```

Expected: All API endpoints for all modules listed.

**Step 3: Manual smoke test**

Visit `/dashboard` and verify:
- Sidebar shows all navigation items
- Each module page loads without errors
- Create/edit forms render correctly
- API endpoints respond

**Commit:** no commit (verification step)
