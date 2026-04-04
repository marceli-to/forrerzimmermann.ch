# Test Suite Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Write a complete Pest test suite covering all API controllers, fix broken factories and existing tests, and delete obsolete tests.

**Architecture:** Feature tests hit the real SQLite in-memory DB via HTTP (actingAs + JSON requests). Each controller gets its own test file. Factories provide all test data. No mocking except Storage::fake for file operations.

**Tech Stack:** Pest 3, Laravel 12, SQLite in-memory, RefreshDatabase (via Pest.php global config)

---

## Conventions

- Routes use `uuid` as the route key (not `id`) for all API resources
- Auth: all API routes require `actingAs($this->user)`
- Run all tests: `php artisan test`
- Run single file: `php artisan test tests/Feature/ProjectTest.php`

---

### Task 1: Fix UserFactory

**Files:**
- Modify: `database/factories/UserFactory.php`

Missing `firstname` field causes every test that creates a user to fail with a NOT NULL constraint.

**Step 1: Update the factory**

Replace the `definition()` method:

```php
public function definition(): array
{
    return [
        'firstname' => fake()->firstName(),
        'name' => fake()->lastName(),
        'email' => fake()->unique()->safeEmail(),
        'email_verified_at' => now(),
        'password' => static::$password ??= Hash::make('password'),
        'remember_token' => Str::random(10),
        'role' => 'admin',
    ];
}
```

**Step 2: Run auth tests to verify they pass**

```bash
php artisan test tests/Feature/Auth/
```
Expected: all auth tests pass (2 passed previously, should still pass)

**Step 3: Commit**

```bash
git add database/factories/UserFactory.php
git commit -m "Fix UserFactory missing firstname field"
```

---

### Task 2: Fix ProjectFactory and add missing factories

**Files:**
- Modify: `database/factories/ProjectFactory.php`
- Create: `database/factories/TopicFactory.php`
- Create: `database/factories/LandingSlideFactory.php`
- Create: `database/factories/TeamMemberFactory.php`
- Create: `database/factories/JobListingFactory.php`
- Create: `database/factories/AtelierPageFactory.php`
- Create: `database/factories/ContactFactory.php`
- Create: `database/factories/SeoSettingFactory.php`

**Step 1: Fix ProjectFactory**

Remove stale `name` and `status` fields; align with current `projects` migration:

```php
<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProjectFactory extends Factory
{
    protected $model = Project::class;

    public function definition(): array
    {
        $title = fake()->sentence(3, false);
        $location = fake()->city();

        return [
            'title' => $title,
            'location' => $location,
            'slug' => Str::slug($title . ' ' . $location),
            'subtitle' => fake()->sentence(4),
            'year' => fake()->numberBetween(1990, 2025),
            'description' => fake()->paragraph(),
            'info' => fake()->paragraph(),
            'meta_description' => fake()->sentence(),
            'publish' => false,
            'feature' => false,
            'sort_order' => 0,
            'topic_id' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['publish' => true]);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['feature' => true]);
    }
}
```

**Step 2: Create TopicFactory**

```php
<?php

namespace Database\Factories;

use App\Models\Topic;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TopicFactory extends Factory
{
    protected $model = Topic::class;

    public function definition(): array
    {
        $title = fake()->words(2, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'publish' => false,
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['publish' => true]);
    }
}
```

**Step 3: Create LandingSlideFactory**

```php
<?php

namespace Database\Factories;

use App\Models\LandingSlide;
use Illuminate\Database\Eloquent\Factories\Factory;

class LandingSlideFactory extends Factory
{
    protected $model = LandingSlide::class;

    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['image', 'image_text']),
            'text' => fake()->paragraph(),
            'publish' => false,
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['publish' => true]);
    }

    public function imageOnly(): static
    {
        return $this->state(fn () => ['type' => 'image', 'text' => null]);
    }
}
```

**Step 4: Create TeamMemberFactory**

```php
<?php

namespace Database\Factories;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

class TeamMemberFactory extends Factory
{
    protected $model = TeamMember::class;

    public function definition(): array
    {
        return [
            'firstname' => fake()->firstName(),
            'name' => fake()->lastName(),
            'title' => fake()->jobTitle(),
            'email' => fake()->safeEmail(),
            'cv' => fake()->paragraph(),
            'publish' => false,
            'former' => false,
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['publish' => true]);
    }

    public function former(): static
    {
        return $this->state(fn () => ['former' => true]);
    }
}
```

**Step 5: Create JobListingFactory**

```php
<?php

namespace Database\Factories;

use App\Models\JobListing;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobListingFactory extends Factory
{
    protected $model = JobListing::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'text' => fake()->paragraphs(3, true),
            'publish' => false,
            'sort_order' => 0,
        ];
    }

    public function published(): static
    {
        return $this->state(fn () => ['publish' => true]);
    }
}
```

**Step 6: Create AtelierPageFactory**

```php
<?php

namespace Database\Factories;

use App\Models\AtelierPage;
use Illuminate\Database\Eloquent\Factories\Factory;

class AtelierPageFactory extends Factory
{
    protected $model = AtelierPage::class;

    public function definition(): array
    {
        return [
            'slug' => fake()->unique()->slug(2),
            'title' => fake()->sentence(3),
            'text' => fake()->paragraph(),
            'publish' => false,
        ];
    }

    public function profil(): static
    {
        return $this->state(fn () => ['slug' => 'profil']);
    }
}
```

**Step 7: Create ContactFactory**

```php
<?php

namespace Database\Factories;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContactFactory extends Factory
{
    protected $model = Contact::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'address' => fake()->address(),
            'email' => fake()->companyEmail(),
            'phone' => fake()->phoneNumber(),
            'maps_url' => 'https://maps.google.com/?q=' . urlencode(fake()->address()),
            'imprint' => fake()->paragraph(),
            'publish' => true,
        ];
    }
}
```

**Step 8: Create SeoSettingFactory**

```php
<?php

namespace Database\Factories;

use App\Models\SeoSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

class SeoSettingFactory extends Factory
{
    protected $model = SeoSetting::class;

    public function definition(): array
    {
        return [
            'landing_meta_description' => fake()->sentence(),
            'projects_meta_description' => fake()->sentence(),
            'werkliste_meta_description' => fake()->sentence(),
            'profile_meta_description' => fake()->sentence(),
            'team_meta_description' => fake()->sentence(),
            'jobs_meta_description' => fake()->sentence(),
            'contact_meta_description' => fake()->sentence(),
        ];
    }
}
```

**Step 9: Run existing tests to verify factories don't error**

```bash
php artisan test tests/Feature/Auth/
```
Expected: passes

**Step 10: Commit**

```bash
git add database/factories/
git commit -m "Fix ProjectFactory and add missing model factories"
```

---

### Task 3: Clean up Pest.php and delete obsolete test files

**Files:**
- Modify: `tests/Pest.php`
- Delete: `tests/Feature/DashboardTest.php` (tests deleted controller)

**Step 1: Clean up Pest.php**

Remove the unused `something()` function stub:

```php
<?php

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});
```

**Step 2: Delete DashboardTest**

```bash
rm tests/Feature/DashboardTest.php
```

**Step 3: Commit**

```bash
git add tests/Pest.php
git rm tests/Feature/DashboardTest.php
git commit -m "Remove obsolete DashboardTest and clean up Pest.php"
```

---

### Task 4: Rewrite ProjectTest

**Files:**
- Modify: `tests/Feature/ProjectTest.php`

Key fixes: routes use `uuid` not `id`; `store` returns 201; `year` is required; slug is generated from title+location.

**Step 1: Rewrite the file**

```php
<?php

use App\Models\Project;
use App\Models\Topic;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all projects', function () {
    Project::factory()->count(3)->create();

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/projects')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('creates a project and generates slug from title and location', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/projects', [
            'title' => 'New Project',
            'location' => 'Zurich',
            'year' => 2024,
        ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'New Project')
        ->assertJsonPath('data.slug', 'new-project-zurich');

    expect(Project::count())->toBe(1);
});

it('generates slug from title only when location is absent', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/projects', [
            'title' => 'Solo Project',
            'year' => 2023,
        ])
        ->assertCreated()
        ->assertJsonPath('data.slug', 'solo-project');
});

it('validates required fields on create', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/projects', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'year']);
});

it('shows a single project', function () {
    $project = Project::factory()->create(['title' => 'My Project']);

    $this->actingAs($this->user)
        ->getJson("/api/dashboard/projects/{$project->uuid}")
        ->assertOk()
        ->assertJsonPath('data.title', 'My Project');
});

it('updates a project', function () {
    $project = Project::factory()->create();

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/projects/{$project->uuid}", [
            'title' => 'Updated Title',
            'year' => 2020,
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'Updated Title');
});

it('attaches a topic on create', function () {
    $topic = Topic::factory()->create();

    $response = $this->actingAs($this->user)
        ->postJson('/api/dashboard/projects', [
            'title' => 'Project with Topic',
            'year' => 2022,
            'topic_id' => $topic->uuid,
        ])
        ->assertCreated();

    expect($response->json('data.topic.uuid'))->toBe($topic->uuid);
});

it('toggles publish state', function () {
    $project = Project::factory()->create(['publish' => false]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/projects/{$project->uuid}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', true);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/projects/{$project->uuid}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', false);
});

it('toggles feature state', function () {
    $project = Project::factory()->create(['feature' => false]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/projects/{$project->uuid}/feature")
        ->assertOk()
        ->assertJsonPath('data.feature', true);
});

it('reorders projects', function () {
    $a = Project::factory()->create(['sort_order' => 0]);
    $b = Project::factory()->create(['sort_order' => 1]);

    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/projects/reorder', [
            'items' => [
                ['uuid' => $a->uuid, 'sort_order' => 1],
                ['uuid' => $b->uuid, 'sort_order' => 0],
            ],
        ])
        ->assertOk();

    expect($a->fresh()->sort_order)->toBe(1)
        ->and($b->fresh()->sort_order)->toBe(0);
});

it('rejects reorder with invalid uuid', function () {
    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/projects/reorder', [
            'items' => [
                ['uuid' => 'does-not-exist', 'sort_order' => 0],
            ],
        ])
        ->assertUnprocessable();
});

it('deletes a project', function () {
    $project = Project::factory()->create();

    $this->actingAs($this->user)
        ->deleteJson("/api/dashboard/projects/{$project->uuid}")
        ->assertNoContent();

    expect(Project::count())->toBe(0);
});

it('requires authentication', function () {
    $this->getJson('/api/dashboard/projects')->assertUnauthorized();
});
```

**Step 2: Run the test**

```bash
php artisan test tests/Feature/ProjectTest.php
```
Expected: all pass

**Step 3: Commit**

```bash
git add tests/Feature/ProjectTest.php
git commit -m "Rewrite ProjectTest to match current model and routes"
```

---

### Task 5: Rewrite MediaTest

**Files:**
- Modify: `tests/Feature/MediaTest.php`

Key fixes: routes use `uuid`; remove the "prevents deletion of attached media" test (no such guard exists in the controller); keep file upload/delete/teaser/og tests.

**Step 1: Rewrite the file**

```php
<?php

use App\Models\Media;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    Storage::fake('public');
});

it('lists all media', function () {
    Media::factory()->count(5)->create();

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/media')
        ->assertOk()
        ->assertJsonCount(5, 'data');
});

it('uploads an image to temp storage', function () {
    $file = UploadedFile::fake()->image('photo.jpg', 800, 600);

    $response = $this->actingAs($this->user)
        ->postJson('/api/dashboard/media/upload', ['file' => $file])
        ->assertOk()
        ->assertJsonStructure(['data' => ['uuid', 'file', 'original_name', 'width', 'height', '_temp']]);

    expect($response->json('data._temp'))->toBeTrue();

    Storage::disk('public')->assertExists('temp/' . $response->json('data.file'));
});

it('rejects non-image uploads', function () {
    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->actingAs($this->user)
        ->postJson('/api/dashboard/media/upload', ['file' => $file])
        ->assertUnprocessable();
});

it('updates media alt and caption', function () {
    $media = Media::factory()->create(['alt' => '', 'caption' => '']);

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/media/{$media->uuid}", [
            'alt' => 'A nice photo',
            'caption' => 'Taken in Zurich',
        ])
        ->assertOk()
        ->assertJsonPath('data.alt', 'A nice photo')
        ->assertJsonPath('data.caption', 'Taken in Zurich');
});

it('deletes media and removes file from storage', function () {
    Storage::disk('public')->put('uploads/test.jpg', 'fake-content');
    $media = Media::factory()->create(['file' => 'test.jpg']);

    $this->actingAs($this->user)
        ->deleteJson("/api/dashboard/media/{$media->uuid}")
        ->assertNoContent();

    expect(Media::count())->toBe(0);
    Storage::disk('public')->assertMissing('uploads/test.jpg');
});

it('reorders media', function () {
    $a = Media::factory()->create(['sort_order' => 0]);
    $b = Media::factory()->create(['sort_order' => 1]);

    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/media/reorder', [
            'items' => [
                ['uuid' => $a->uuid, 'sort_order' => 1],
                ['uuid' => $b->uuid, 'sort_order' => 0],
            ],
        ])
        ->assertOk();

    expect($a->fresh()->sort_order)->toBe(1)
        ->and($b->fresh()->sort_order)->toBe(0);
});

it('rejects reorder with unknown uuid', function () {
    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/media/reorder', [
            'items' => [['uuid' => 'bad-uuid', 'sort_order' => 0]],
        ])
        ->assertUnprocessable();
});

it('toggles teaser on', function () {
    $project = Project::factory()->create();
    $media = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_teaser' => false,
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/teaser")
        ->assertOk()
        ->assertJsonPath('data.is_teaser', true);
});

it('toggles teaser off when already set', function () {
    $project = Project::factory()->create();
    $media = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_teaser' => true,
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/teaser")
        ->assertOk()
        ->assertJsonPath('data.is_teaser', false);
});

it('only allows one teaser per entity', function () {
    $project = Project::factory()->create();
    $first = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_teaser' => true,
    ]);
    $second = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_teaser' => false,
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$second->uuid}/teaser")
        ->assertOk()
        ->assertJsonPath('data.is_teaser', true);

    expect($first->fresh()->is_teaser)->toBeFalse();
});

it('toggles og image on', function () {
    $project = Project::factory()->create();
    $media = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_og' => false,
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/og")
        ->assertOk()
        ->assertJsonPath('data.is_og', true);
});

it('only allows one og image per entity', function () {
    $project = Project::factory()->create();
    $first = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_og' => true,
    ]);
    $second = Media::factory()->create([
        'mediable_type' => Project::class,
        'mediable_id' => $project->id,
        'is_og' => false,
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$second->uuid}/og")
        ->assertOk();

    expect($first->fresh()->is_og)->toBeFalse();
});

it('requires authentication for media', function () {
    $this->getJson('/api/dashboard/media')->assertUnauthorized();
});
```

**Step 2: Run the test**

```bash
php artisan test tests/Feature/MediaTest.php
```
Expected: all pass

**Step 3: Commit**

```bash
git add tests/Feature/MediaTest.php
git commit -m "Rewrite MediaTest to match current routes and behaviour"
```

---

### Task 6: Write TopicTest

**Files:**
- Create: `tests/Feature/TopicTest.php`

**Step 1: Create the file**

```php
<?php

use App\Models\Topic;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all topics ordered by title', function () {
    Topic::factory()->create(['title' => 'Wohnbau']);
    Topic::factory()->create(['title' => 'Gewerbe']);

    $response = $this->actingAs($this->user)
        ->getJson('/api/dashboard/topics')
        ->assertOk()
        ->assertJsonCount(2, 'data');

    expect($response->json('data.0.title'))->toBe('Gewerbe')
        ->and($response->json('data.1.title'))->toBe('Wohnbau');
});

it('creates a topic and generates a slug', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/topics', ['title' => 'Öffentliche Bauten'])
        ->assertCreated()
        ->assertJsonPath('data.title', 'Öffentliche Bauten')
        ->assertJsonPath('data.slug', 'offentliche-bauten');

    expect(Topic::count())->toBe(1);
});

it('validates title is required on create', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/topics', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title']);
});

it('shows a single topic', function () {
    $topic = Topic::factory()->create(['title' => 'Umbau']);

    $this->actingAs($this->user)
        ->getJson("/api/dashboard/topics/{$topic->uuid}")
        ->assertOk()
        ->assertJsonPath('data.title', 'Umbau');
});

it('updates a topic and regenerates slug', function () {
    $topic = Topic::factory()->create(['title' => 'Old Title']);

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/topics/{$topic->uuid}", ['title' => 'New Title'])
        ->assertOk()
        ->assertJsonPath('data.title', 'New Title')
        ->assertJsonPath('data.slug', 'new-title');
});

it('toggles publish state', function () {
    $topic = Topic::factory()->create(['publish' => false]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/topics/{$topic->uuid}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', true);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/topics/{$topic->uuid}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', false);
});

it('reorders topics', function () {
    $a = Topic::factory()->create(['sort_order' => 0]);
    $b = Topic::factory()->create(['sort_order' => 1]);

    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/topics/reorder', [
            'items' => [
                ['uuid' => $a->uuid, 'sort_order' => 1],
                ['uuid' => $b->uuid, 'sort_order' => 0],
            ],
        ])
        ->assertOk();

    expect($a->fresh()->sort_order)->toBe(1)
        ->and($b->fresh()->sort_order)->toBe(0);
});

it('rejects reorder with invalid uuid', function () {
    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/topics/reorder', [
            'items' => [['uuid' => 'not-real', 'sort_order' => 0]],
        ])
        ->assertUnprocessable();
});

it('deletes a topic', function () {
    $topic = Topic::factory()->create();

    $this->actingAs($this->user)
        ->deleteJson("/api/dashboard/topics/{$topic->uuid}")
        ->assertNoContent();

    expect(Topic::count())->toBe(0);
});

it('requires authentication', function () {
    $this->getJson('/api/dashboard/topics')->assertUnauthorized();
});
```

**Step 2: Run**

```bash
php artisan test tests/Feature/TopicTest.php
```
Expected: all pass

**Step 3: Commit**

```bash
git add tests/Feature/TopicTest.php
git commit -m "Add TopicTest"
```

---

### Task 7: Write LandingTest

**Files:**
- Create: `tests/Feature/LandingTest.php`

**Step 1: Create the file**

```php
<?php

use App\Models\LandingSlide;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all slides ordered by sort_order', function () {
    LandingSlide::factory()->create(['sort_order' => 1]);
    LandingSlide::factory()->create(['sort_order' => 0]);

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/landing')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates an image slide', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/landing', ['type' => 'image'])
        ->assertCreated()
        ->assertJsonPath('data.type', 'image');
});

it('creates an image_text slide', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/landing', [
            'type' => 'image_text',
            'text' => 'Some overlay text',
        ])
        ->assertCreated()
        ->assertJsonPath('data.type', 'image_text')
        ->assertJsonPath('data.text', 'Some overlay text');
});

it('requires text when type is image_text', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/landing', ['type' => 'image_text'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['text']);
});

it('validates type is required', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/landing', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['type']);
});

it('shows a single slide', function () {
    $slide = LandingSlide::factory()->create(['type' => 'image']);

    $this->actingAs($this->user)
        ->getJson("/api/dashboard/landing/{$slide->uuid}")
        ->assertOk()
        ->assertJsonPath('data.type', 'image');
});

it('updates a slide', function () {
    $slide = LandingSlide::factory()->create(['type' => 'image', 'text' => null]);

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/landing/{$slide->uuid}", [
            'type' => 'image_text',
            'text' => 'Updated text',
        ])
        ->assertOk()
        ->assertJsonPath('data.text', 'Updated text');
});

it('toggles publish state', function () {
    $slide = LandingSlide::factory()->create(['publish' => false]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/landing/{$slide->uuid}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', true);
});

it('reorders slides', function () {
    $a = LandingSlide::factory()->create(['sort_order' => 0]);
    $b = LandingSlide::factory()->create(['sort_order' => 1]);

    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/landing/reorder', [
            'items' => [
                ['uuid' => $a->uuid, 'sort_order' => 1],
                ['uuid' => $b->uuid, 'sort_order' => 0],
            ],
        ])
        ->assertOk();

    expect($a->fresh()->sort_order)->toBe(1)
        ->and($b->fresh()->sort_order)->toBe(0);
});

it('rejects reorder with invalid uuid', function () {
    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/landing/reorder', [
            'items' => [['uuid' => 'bad', 'sort_order' => 0]],
        ])
        ->assertUnprocessable();
});

it('deletes a slide', function () {
    $slide = LandingSlide::factory()->create();

    $this->actingAs($this->user)
        ->deleteJson("/api/dashboard/landing/{$slide->uuid}")
        ->assertNoContent();

    expect(LandingSlide::count())->toBe(0);
});

it('requires authentication', function () {
    $this->getJson('/api/dashboard/landing')->assertUnauthorized();
});
```

**Step 2: Run**

```bash
php artisan test tests/Feature/LandingTest.php
```
Expected: all pass

**Step 3: Commit**

```bash
git add tests/Feature/LandingTest.php
git commit -m "Add LandingTest"
```

---

### Task 8: Write TeamTest

**Files:**
- Create: `tests/Feature/TeamTest.php`

**Step 1: Create the file**

```php
<?php

use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->user = User::factory()->create();
    Storage::fake('public');
});

it('lists all team members', function () {
    TeamMember::factory()->count(3)->create();

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/team')
        ->assertOk()
        ->assertJsonCount(3, 'data');
});

it('creates a team member', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/team', [
            'firstname' => 'Anna',
            'name' => 'Müller',
        ])
        ->assertCreated()
        ->assertJsonPath('data.firstname', 'Anna')
        ->assertJsonPath('data.name', 'Müller');

    expect(TeamMember::count())->toBe(1);
});

it('validates required fields on create', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/team', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['firstname', 'name']);
});

it('shows a single team member', function () {
    $member = TeamMember::factory()->create(['firstname' => 'Peter']);

    $this->actingAs($this->user)
        ->getJson("/api/dashboard/team/{$member->uuid}")
        ->assertOk()
        ->assertJsonPath('data.firstname', 'Peter');
});

it('updates a team member', function () {
    $member = TeamMember::factory()->create(['firstname' => 'Old']);

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/team/{$member->uuid}", [
            'firstname' => 'New',
            'name' => $member->name,
        ])
        ->assertOk()
        ->assertJsonPath('data.firstname', 'New');
});

it('toggles publish state', function () {
    $member = TeamMember::factory()->create(['publish' => false]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/team/{$member->uuid}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', true);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/team/{$member->uuid}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', false);
});

it('reorders team members', function () {
    $a = TeamMember::factory()->create(['sort_order' => 0]);
    $b = TeamMember::factory()->create(['sort_order' => 1]);

    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/team/reorder', [
            'items' => [
                ['uuid' => $a->uuid, 'sort_order' => 1],
                ['uuid' => $b->uuid, 'sort_order' => 0],
            ],
        ])
        ->assertOk();

    expect($a->fresh()->sort_order)->toBe(1)
        ->and($b->fresh()->sort_order)->toBe(0);
});

it('rejects reorder with invalid uuid', function () {
    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/team/reorder', [
            'items' => [['uuid' => 'bad', 'sort_order' => 0]],
        ])
        ->assertUnprocessable();
});

it('deletes a team member and their media files', function () {
    Storage::disk('public')->put('uploads/photo.jpg', 'fake');
    $member = TeamMember::factory()->create();
    $member->media()->create([
        'uuid' => \Illuminate\Support\Str::uuid(),
        'file' => 'photo.jpg',
        'original_name' => 'photo.jpg',
        'mime_type' => 'image/jpeg',
        'size' => 1000,
        'sort_order' => 0,
    ]);

    $this->actingAs($this->user)
        ->deleteJson("/api/dashboard/team/{$member->uuid}")
        ->assertNoContent();

    expect(TeamMember::count())->toBe(0);
    Storage::disk('public')->assertMissing('uploads/photo.jpg');
});

it('requires authentication', function () {
    $this->getJson('/api/dashboard/team')->assertUnauthorized();
});
```

**Step 2: Run**

```bash
php artisan test tests/Feature/TeamTest.php
```
Expected: all pass

**Step 3: Commit**

```bash
git add tests/Feature/TeamTest.php
git commit -m "Add TeamTest"
```

---

### Task 9: Write JobTest

**Files:**
- Create: `tests/Feature/JobTest.php`

**Step 1: Create the file**

```php
<?php

use App\Models\JobListing;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all job listings', function () {
    JobListing::factory()->count(2)->create();

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/jobs')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('creates a job listing', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/jobs', [
            'title' => 'Architekt 80%',
            'text' => 'Wir suchen eine engagierte Person.',
        ])
        ->assertCreated()
        ->assertJsonPath('data.title', 'Architekt 80%');

    expect(JobListing::count())->toBe(1);
});

it('validates required fields on create', function () {
    $this->actingAs($this->user)
        ->postJson('/api/dashboard/jobs', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'text']);
});

it('shows a single job listing', function () {
    $job = JobListing::factory()->create(['title' => 'My Job']);

    $this->actingAs($this->user)
        ->getJson("/api/dashboard/jobs/{$job->uuid}")
        ->assertOk()
        ->assertJsonPath('data.title', 'My Job');
});

it('updates a job listing', function () {
    $job = JobListing::factory()->create(['title' => 'Old Title']);

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/jobs/{$job->uuid}", [
            'title' => 'New Title',
            'text' => $job->text,
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'New Title');
});

it('toggles publish state', function () {
    $job = JobListing::factory()->create(['publish' => false]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/jobs/{$job->uuid}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', true);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/jobs/{$job->uuid}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', false);
});

it('reorders job listings', function () {
    $a = JobListing::factory()->create(['sort_order' => 0]);
    $b = JobListing::factory()->create(['sort_order' => 1]);

    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/jobs/reorder', [
            'items' => [
                ['uuid' => $a->uuid, 'sort_order' => 1],
                ['uuid' => $b->uuid, 'sort_order' => 0],
            ],
        ])
        ->assertOk();

    expect($a->fresh()->sort_order)->toBe(1)
        ->and($b->fresh()->sort_order)->toBe(0);
});

it('rejects reorder with invalid uuid', function () {
    $this->actingAs($this->user)
        ->patchJson('/api/dashboard/jobs/reorder', [
            'items' => [['uuid' => 'bad', 'sort_order' => 0]],
        ])
        ->assertUnprocessable();
});

it('deletes a job listing', function () {
    $job = JobListing::factory()->create();

    $this->actingAs($this->user)
        ->deleteJson("/api/dashboard/jobs/{$job->uuid}")
        ->assertNoContent();

    expect(JobListing::count())->toBe(0);
});

it('requires authentication', function () {
    $this->getJson('/api/dashboard/jobs')->assertUnauthorized();
});
```

**Step 2: Run**

```bash
php artisan test tests/Feature/JobTest.php
```
Expected: all pass

**Step 3: Commit**

```bash
git add tests/Feature/JobTest.php
git commit -m "Add JobTest"
```

---

### Task 10: Write AtelierTest

**Files:**
- Create: `tests/Feature/AtelierTest.php`

Note: no store/destroy/reorder routes exist for atelier. Pages are seeded, not created via API.

**Step 1: Create the file**

```php
<?php

use App\Models\AtelierPage;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('lists all atelier pages ordered by id', function () {
    AtelierPage::factory()->create(['slug' => 'profil']);
    AtelierPage::factory()->create(['slug' => 'team']);

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/atelier')
        ->assertOk()
        ->assertJsonCount(2, 'data');
});

it('shows a single atelier page', function () {
    $page = AtelierPage::factory()->create(['slug' => 'profil', 'title' => 'Über uns']);

    $this->actingAs($this->user)
        ->getJson("/api/dashboard/atelier/{$page->uuid}")
        ->assertOk()
        ->assertJsonPath('data.title', 'Über uns');
});

it('updates an atelier page', function () {
    $page = AtelierPage::factory()->create(['slug' => 'team', 'title' => 'Old']);

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/atelier/{$page->uuid}", [
            'title' => 'New Title',
            'text' => 'Some text',
        ])
        ->assertOk()
        ->assertJsonPath('data.title', 'New Title');
});

it('requires title and text for profil page', function () {
    $page = AtelierPage::factory()->profil()->create();

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/atelier/{$page->uuid}", [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'text']);
});

it('does not require title and text for non-profil pages', function () {
    $page = AtelierPage::factory()->create(['slug' => 'team']);

    $this->actingAs($this->user)
        ->putJson("/api/dashboard/atelier/{$page->uuid}", [])
        ->assertOk();
});

it('toggles publish state', function () {
    $page = AtelierPage::factory()->create(['publish' => false]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/atelier/{$page->uuid}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', true);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/atelier/{$page->uuid}/publish")
        ->assertOk()
        ->assertJsonPath('data.publish', false);
});

it('does not expose sort_order or meta_description in response', function () {
    $page = AtelierPage::factory()->create();

    $response = $this->actingAs($this->user)
        ->getJson("/api/dashboard/atelier/{$page->uuid}")
        ->assertOk();

    expect($response->json('data'))->not->toHaveKey('sort_order')
        ->and($response->json('data'))->not->toHaveKey('meta_description');
});

it('requires authentication', function () {
    $this->getJson('/api/dashboard/atelier')->assertUnauthorized();
});
```

**Step 2: Run**

```bash
php artisan test tests/Feature/AtelierTest.php
```
Expected: all pass

**Step 3: Commit**

```bash
git add tests/Feature/AtelierTest.php
git commit -m "Add AtelierTest"
```

---

### Task 11: Write ContactTest

**Files:**
- Create: `tests/Feature/ContactTest.php`

Note: Contact is a singleton — show and update only, no uuid in route.

**Step 1: Create the file**

```php
<?php

use App\Models\Contact;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->contact = Contact::factory()->create();
});

it('shows the contact record', function () {
    $this->actingAs($this->user)
        ->getJson('/api/dashboard/contact')
        ->assertOk()
        ->assertJsonPath('data.name', $this->contact->name)
        ->assertJsonPath('data.email', $this->contact->email);
});

it('does not expose meta_description in response', function () {
    $response = $this->actingAs($this->user)
        ->getJson('/api/dashboard/contact')
        ->assertOk();

    expect($response->json('data'))->not->toHaveKey('meta_description');
});

it('updates the contact record', function () {
    $this->actingAs($this->user)
        ->putJson('/api/dashboard/contact', [
            'name' => 'Updated Name GmbH',
            'address' => 'Musterstrasse 1, 8000 Zürich',
            'email' => 'info@example.com',
            'phone' => '+41 44 000 00 00',
        ])
        ->assertOk()
        ->assertJsonPath('data.name', 'Updated Name GmbH');

    expect($this->contact->fresh()->name)->toBe('Updated Name GmbH');
});

it('validates required fields on update', function () {
    $this->actingAs($this->user)
        ->putJson('/api/dashboard/contact', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name', 'address', 'email', 'phone']);
});

it('validates email format', function () {
    $this->actingAs($this->user)
        ->putJson('/api/dashboard/contact', [
            'name' => 'Test',
            'address' => 'Test',
            'email' => 'not-an-email',
            'phone' => '123',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('validates maps_url must be a url when provided', function () {
    $this->actingAs($this->user)
        ->putJson('/api/dashboard/contact', [
            'name' => 'Test',
            'address' => 'Test',
            'email' => 'test@example.com',
            'phone' => '123',
            'maps_url' => 'not-a-url',
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['maps_url']);
});

it('requires authentication', function () {
    $this->getJson('/api/dashboard/contact')->assertUnauthorized();
});
```

**Step 2: Run**

```bash
php artisan test tests/Feature/ContactTest.php
```
Expected: all pass

**Step 3: Commit**

```bash
git add tests/Feature/ContactTest.php
git commit -m "Add ContactTest"
```

---

### Task 12: Write SeoTest

**Files:**
- Create: `tests/Feature/SeoTest.php`

Note: SeoSetting is a singleton — show and update only.

**Step 1: Create the file**

```php
<?php

use App\Models\SeoSetting;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->seo = SeoSetting::factory()->create();
});

it('shows the seo settings', function () {
    $this->actingAs($this->user)
        ->getJson('/api/dashboard/seo')
        ->assertOk()
        ->assertJsonStructure(['data' => [
            'uuid',
            'landing_meta_description',
            'projects_meta_description',
            'werkliste_meta_description',
            'profile_meta_description',
            'team_meta_description',
            'jobs_meta_description',
            'contact_meta_description',
        ]]);
});

it('updates seo meta descriptions', function () {
    $this->actingAs($this->user)
        ->putJson('/api/dashboard/seo', [
            'landing_meta_description' => 'Architekturbüro in Zürich',
            'projects_meta_description' => 'Unsere Projekte',
        ])
        ->assertOk()
        ->assertJsonPath('data.landing_meta_description', 'Architekturbüro in Zürich');

    expect($this->seo->fresh()->landing_meta_description)->toBe('Architekturbüro in Zürich');
});

it('accepts all fields as nullable', function () {
    $this->actingAs($this->user)
        ->putJson('/api/dashboard/seo', [])
        ->assertOk();
});

it('enforces max 500 chars on meta descriptions', function () {
    $this->actingAs($this->user)
        ->putJson('/api/dashboard/seo', [
            'landing_meta_description' => str_repeat('a', 501),
        ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['landing_meta_description']);
});

it('requires authentication', function () {
    $this->getJson('/api/dashboard/seo')->assertUnauthorized();
});
```

**Step 2: Run**

```bash
php artisan test tests/Feature/SeoTest.php
```
Expected: all pass

**Step 3: Commit**

```bash
git add tests/Feature/SeoTest.php
git commit -m "Add SeoTest"
```

---

### Task 13: Final full suite run and commit

**Step 1: Run all tests**

```bash
php artisan test
```
Expected: all tests pass, 0 failures

**Step 2: Commit if any files were missed**

```bash
git status
git add -A  # only if no sensitive files present
git commit -m "Complete test suite — all controllers covered"
```
