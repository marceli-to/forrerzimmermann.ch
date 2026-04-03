# SEO Module Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Replace the `SiteSetting` module with a clean `SeoSetting` module that handles global OG defaults, per-page meta descriptions for all public pages, and a per-project OG image flag on media.

**Architecture:** Single-row `seo_settings` table (seeded); `is_og` boolean added to `media` table mirroring the existing `is_teaser` pattern. Blade public head receives `$seo` via a view composer. CMS Vue app gets a new `seo` store/api/view replacing `settings`.

**Tech Stack:** Laravel 11, Blade, Vue 3 (Composition API), Pinia, PHP Actions pattern, Laravel Resources.

---

### Task 1: Database migrations

**Files:**
- Create: `database/migrations/2026_04_03_000001_create_seo_settings_table.php`
- Create: `database/migrations/2026_04_03_000002_add_is_og_to_media_table.php`
- Drop: `database/migrations/2026_04_02_000008_create_site_settings_table.php` — delete this file after confirming the new migration covers the same rollback path

**Step 1: Create seo_settings migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('site_settings');

        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('og_title')->nullable();
            $table->string('og_description')->nullable();
            $table->string('landing_meta_description')->nullable();
            $table->string('projects_meta_description')->nullable();
            $table->string('werkliste_meta_description')->nullable();
            $table->string('profile_meta_description')->nullable();
            $table->string('team_meta_description')->nullable();
            $table->string('jobs_meta_description')->nullable();
            $table->string('contact_meta_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
```

**Step 2: Create is_og migration**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->boolean('is_og')->default(false)->after('is_teaser');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('is_og');
        });
    }
};
```

**Step 3: Delete old site_settings migration file**

```bash
rm database/migrations/2026_04_02_000008_create_site_settings_table.php
```

**Step 4: Run migrations**

```bash
php artisan migrate:fresh --seed
```

Expected: No errors, `seo_settings` table created, `media.is_og` column added.

**Step 5: Commit**

```bash
git add database/migrations/
git commit -m "feat: add seo_settings table and is_og column on media"
```

---

### Task 2: SeoSetting model and seeder

**Files:**
- Create: `app/Models/SeoSetting.php`
- Modify: `app/Models/Media.php`
- Modify: `database/seeders/DatabaseSeeder.php`
- Delete: `app/Models/SiteSetting.php`

**Step 1: Create SeoSetting model**

```php
<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class SeoSetting extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'uuid', 'og_title', 'og_description',
        'landing_meta_description', 'projects_meta_description',
        'werkliste_meta_description', 'profile_meta_description',
        'team_meta_description', 'jobs_meta_description',
        'contact_meta_description',
    ];

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
```

**Step 2: Add `is_og` to Media model**

In `app/Models/Media.php`, add `'is_og'` to `$fillable` and `$casts`:

```php
// $fillable — add after 'is_teaser':
'is_og',

// $casts — add after 'is_teaser' => 'boolean':
'is_og' => 'boolean',
```

**Step 3: Update DatabaseSeeder**

Replace `SiteSetting::firstOrCreate(...)` with:

```php
use App\Models\SeoSetting;

SeoSetting::firstOrCreate(['id' => 1]);
```

Remove the `use App\Models\SiteSetting;` import.

**Step 4: Delete SiteSetting model**

```bash
rm app/Models/SiteSetting.php
```

**Step 5: Run seeder to verify**

```bash
php artisan migrate:fresh --seed
```

Expected: No errors, one row in `seo_settings`.

**Step 6: Commit**

```bash
git add app/Models/ database/seeders/
git commit -m "feat: add SeoSetting model, update Media model with is_og"
```

---

### Task 3: Backend — Resource, Request, Action, Controller

**Files:**
- Create: `app/Http/Resources/SeoSettingResource.php`
- Create: `app/Http/Requests/Seo/UpdateSeoSettingRequest.php`
- Create: `app/Actions/Seo/UpdateAction.php`
- Create: `app/Http/Controllers/Api/SeoController.php`
- Delete: `app/Http/Resources/SiteSettingResource.php`
- Delete: `app/Http/Requests/Settings/UpdateSiteSettingRequest.php`
- Delete: `app/Actions/Settings/UpdateAction.php`
- Delete: `app/Http/Controllers/Api/SettingsController.php`

**Step 1: Create SeoSettingResource**

```php
<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeoSettingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'og_title' => $this->og_title,
            'og_description' => $this->og_description,
            'landing_meta_description' => $this->landing_meta_description,
            'projects_meta_description' => $this->projects_meta_description,
            'werkliste_meta_description' => $this->werkliste_meta_description,
            'profile_meta_description' => $this->profile_meta_description,
            'team_meta_description' => $this->team_meta_description,
            'jobs_meta_description' => $this->jobs_meta_description,
            'contact_meta_description' => $this->contact_meta_description,
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
```

**Step 2: Create UpdateSeoSettingRequest**

```php
<?php

namespace App\Http\Requests\Seo;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSeoSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'og_title' => 'nullable|string|max:255',
            'og_description' => 'nullable|string|max:500',
            'landing_meta_description' => 'nullable|string|max:500',
            'projects_meta_description' => 'nullable|string|max:500',
            'werkliste_meta_description' => 'nullable|string|max:500',
            'profile_meta_description' => 'nullable|string|max:500',
            'team_meta_description' => 'nullable|string|max:500',
            'jobs_meta_description' => 'nullable|string|max:500',
            'contact_meta_description' => 'nullable|string|max:500',
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

**Step 3: Create Seo UpdateAction**

```php
<?php

namespace App\Actions\Seo;

use App\Actions\Media\AttachAction as AttachMediaAction;
use App\Models\SeoSetting;

class UpdateAction
{
    public function execute(SeoSetting $seo, array $data): SeoSetting
    {
        $media = $data['media'] ?? [];
        unset($data['media']);

        $seo->update($data);

        if (!empty($media)) {
            (new AttachMediaAction)->execute($media, $seo);
        }

        return $seo;
    }
}
```

**Step 4: Create SeoController**

```php
<?php

namespace App\Http\Controllers\Api;

use App\Actions\Seo\UpdateAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Seo\UpdateSeoSettingRequest;
use App\Http\Resources\SeoSettingResource;
use App\Models\SeoSetting;

class SeoController extends Controller
{
    public function show()
    {
        return new SeoSettingResource(SeoSetting::with('media')->firstOrFail());
    }

    public function update(UpdateSeoSettingRequest $request)
    {
        $seo = SeoSetting::firstOrFail();
        $seo = (new UpdateAction)->execute($seo, $request->validated());
        return new SeoSettingResource($seo->load('media'));
    }
}
```

**Step 5: Delete old Settings backend files**

```bash
rm app/Http/Resources/SiteSettingResource.php
rm app/Http/Requests/Settings/UpdateSiteSettingRequest.php
rm app/Actions/Settings/UpdateAction.php
rm app/Http/Controllers/Api/SettingsController.php
rmdir app/Http/Requests/Settings
rmdir app/Actions/Settings
```

**Step 6: Commit**

```bash
git add app/Http/Resources/SeoSettingResource.php \
        app/Http/Requests/Seo/ \
        app/Actions/Seo/ \
        app/Http/Controllers/Api/SeoController.php
git commit -m "feat: add SeoController, SeoSettingResource, UpdateSeoSettingRequest, UpdateAction"
```

---

### Task 4: Add SetOgAction and route for is_og toggle

**Files:**
- Create: `app/Actions/Media/SetOgAction.php`
- Modify: `app/Http/Controllers/Api/MediaController.php`
- Modify: `routes/api.php`

**Step 1: Create SetOgAction** (mirrors `SetTeaserAction`)

```php
<?php

namespace App\Actions\Media;

use App\Models\Media;

class SetOgAction
{
    public function execute(Media $media): Media
    {
        $wasOg = $media->is_og;

        Media::where('mediable_type', $media->mediable_type)
            ->where('mediable_id', $media->mediable_id)
            ->update(['is_og' => false]);

        if (!$wasOg) {
            $media->update(['is_og' => true]);
        }

        return $media->refresh();
    }
}
```

**Step 2: Add `og` method to MediaController**

Add import at top:
```php
use App\Actions\Media\SetOgAction;
```

Add method:
```php
public function og(Media $media)
{
    $media = (new SetOgAction)->execute($media);
    return new MediaResource($media);
}
```

**Step 3: Add `is_og` to MediaResource**

In `app/Http/Resources/MediaResource.php`, add after `'is_teaser'`:
```php
'is_og' => $this->is_og,
```

**Step 4: Add route in `routes/api.php`**

In the media routes group, add after the teaser route:
```php
Route::patch('/{media}/og', 'og');
```

**Step 5: Add api method in `resources/js/app/api/media.js`**

```js
og: (uuid) => api.patch(`/media/${uuid}/og`),
```

**Step 6: Commit**

```bash
git add app/Actions/Media/SetOgAction.php \
        app/Http/Controllers/Api/MediaController.php \
        app/Http/Resources/MediaResource.php \
        routes/api.php \
        resources/js/app/api/media.js
git commit -m "feat: add is_og toggle for media (SetOgAction, route, resource)"
```

---

### Task 5: Update API routes — swap settings for seo

**Files:**
- Modify: `routes/api.php`

**Step 1: Replace SettingsController with SeoController**

Remove:
```php
use App\Http\Controllers\Api\SettingsController;
```

Add:
```php
use App\Http\Controllers\Api\SeoController;
```

Replace the settings route group:
```php
Route::controller(SeoController::class)
    ->prefix('seo')
    ->group(function () {
        Route::get('/', 'show');
        Route::put('/', 'update');
    });
```

**Step 2: Verify**

```bash
php artisan route:list | grep seo
```

Expected: `GET /api/dashboard/seo` and `PUT /api/dashboard/seo`.

**Step 3: Commit**

```bash
git add routes/api.php
git commit -m "feat: replace settings route with seo route"
```

---

### Task 6: Vue — seo api, store, view

**Files:**
- Create: `resources/js/app/api/seo.js`
- Create: `resources/js/app/stores/seo.js`
- Create: `resources/js/app/views/seo/Index.vue`
- Delete: `resources/js/app/api/settings.js`
- Delete: `resources/js/app/stores/settings.js`
- Delete: `resources/js/app/views/settings/Index.vue` (and directory)

**Step 1: Create seo api**

```js
import api from './axios'

export default {
    show: () => api.get('/seo'),
    update: (data) => api.put('/seo', data),
}
```

**Step 2: Create seo store**

```js
import { defineStore } from 'pinia'
import seoApi from '@/api/seo'

export const useSeoStore = defineStore('seo', {
    state: () => ({
        seo: null,
        loading: false,
        errors: {},
    }),

    actions: {
        async fetchSeo() {
            this.loading = true
            try {
                const { data } = await seoApi.show()
                this.seo = data.data
            } finally {
                this.loading = false
            }
        },

        async saveSeo(form) {
            this.errors = {}
            try {
                await seoApi.update(form)
                return true
            } catch (error) {
                if (error.response?.status === 422) {
                    this.errors = error.response.data.errors
                }
                return false
            }
        },
    },
})
```

**Step 3: Create seo view**

```vue
<script setup>
import { ref, onMounted } from 'vue'
import { useSeoStore } from '@/stores/seo'
import { useMediaStore } from '@/stores/media'
import { useToast } from '@/composables/useToast'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormTextarea from '@/components/ui/form/FormTextarea.vue'
import FormError from '@/components/ui/form/FormError.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const store = useSeoStore()
const mediaStore = useMediaStore()
const toast = useToast()
const editingMedia = ref(null)

const form = ref({
    og_title: '',
    og_description: '',
    landing_meta_description: '',
    projects_meta_description: '',
    werkliste_meta_description: '',
    profile_meta_description: '',
    team_meta_description: '',
    jobs_meta_description: '',
    contact_meta_description: '',
})

onMounted(async () => {
    mediaStore.setItems([])
    await store.fetchSeo()
    if (store.seo) {
        const s = store.seo
        form.value = {
            og_title: s.og_title || '',
            og_description: s.og_description || '',
            landing_meta_description: s.landing_meta_description || '',
            projects_meta_description: s.projects_meta_description || '',
            werkliste_meta_description: s.werkliste_meta_description || '',
            profile_meta_description: s.profile_meta_description || '',
            team_meta_description: s.team_meta_description || '',
            jobs_meta_description: s.jobs_meta_description || '',
            contact_meta_description: s.contact_meta_description || '',
        }
        mediaStore.setItems(s.media || [])
    }
})

async function handleSubmit() {
    const tempMedia = mediaStore.tempItems.map(item => ({
        uuid: item.uuid,
        file: item.file,
        original_name: item.original_name,
        mime_type: item.mime_type,
        size: item.size,
        width: item.width,
        height: item.height,
        alt: item.alt || null,
        caption: item.caption || null,
    }))

    const payload = { ...form.value }
    if (tempMedia.length) {
        payload.media = tempMedia
    }

    const success = await store.saveSeo(payload)
    if (success) {
        toast.success('SEO-Einstellungen aktualisiert')
    } else if (Object.keys(store.errors).length) {
        toast.error('Bitte überprüfen Sie das Formular')
    }
}

function onUploaded(media) { mediaStore.addItem(media) }
function onEditMedia(media) { editingMedia.value = media }
async function onSaveMedia({ uuid, data }) {
    const success = await mediaStore.updateItem(uuid, data)
    if (success) editingMedia.value = null
}
async function onDeleteMedia(media) { await mediaStore.deleteItem(media.uuid) }
function onReorderMedia(items) { mediaStore.reorder(items) }
</script>

<template>
    <div>
        <PageHeader title="SEO" />

        <div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
            Laden...
        </div>

        <form v-else class="flex flex-col gap-24" @submit.prevent="handleSubmit">

            <FormGroup>
                <FormLabel for="og_title">OG Titel</FormLabel>
                <FormInput id="og_title" v-model="form.og_title" />
                <FormError :message="store.errors.og_title" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="og_description">OG Beschreibung</FormLabel>
                <FormTextarea id="og_description" v-model="form.og_description" />
                <FormError :message="store.errors.og_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel>OG Image</FormLabel>
                <div class="mt-8 flex flex-col gap-16">
                    <MediaUploader v-if="!mediaStore.items.length" @uploaded="onUploaded" />
                    <MediaGrid
                        v-if="mediaStore.items.length"
                        :items="mediaStore.items"
                        @edit="onEditMedia"
                        @delete="onDeleteMedia"
                        @reorder="onReorderMedia"
                    />
                </div>
            </FormGroup>

            <FormGroup>
                <FormLabel for="landing_meta_description">Meta Description – Startseite</FormLabel>
                <FormTextarea id="landing_meta_description" v-model="form.landing_meta_description" />
                <FormError :message="store.errors.landing_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="projects_meta_description">Meta Description – Projekte</FormLabel>
                <FormTextarea id="projects_meta_description" v-model="form.projects_meta_description" />
                <FormError :message="store.errors.projects_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="werkliste_meta_description">Meta Description – Werkliste</FormLabel>
                <FormTextarea id="werkliste_meta_description" v-model="form.werkliste_meta_description" />
                <FormError :message="store.errors.werkliste_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="profile_meta_description">Meta Description – Profil</FormLabel>
                <FormTextarea id="profile_meta_description" v-model="form.profile_meta_description" />
                <FormError :message="store.errors.profile_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="team_meta_description">Meta Description – Team</FormLabel>
                <FormTextarea id="team_meta_description" v-model="form.team_meta_description" />
                <FormError :message="store.errors.team_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="jobs_meta_description">Meta Description – Stellen</FormLabel>
                <FormTextarea id="jobs_meta_description" v-model="form.jobs_meta_description" />
                <FormError :message="store.errors.jobs_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="contact_meta_description">Meta Description – Kontakt</FormLabel>
                <FormTextarea id="contact_meta_description" v-model="form.contact_meta_description" />
                <FormError :message="store.errors.contact_meta_description" />
            </FormGroup>

            <FormActions submitLabel="Speichern" />
        </form>

        <MediaEdit
            :media="editingMedia"
            @close="editingMedia = null"
            @save="onSaveMedia"
        />
    </div>
</template>
```

**Step 4: Delete old settings files**

```bash
rm resources/js/app/api/settings.js
rm resources/js/app/stores/settings.js
rm resources/js/app/views/settings/Index.vue
rmdir resources/js/app/views/settings
```

**Step 5: Commit**

```bash
git add resources/js/app/api/seo.js \
        resources/js/app/stores/seo.js \
        resources/js/app/views/seo/
git commit -m "feat: add SEO vue api, store, and form view"
```

---

### Task 7: Update router and sidebar

**Files:**
- Modify: `resources/js/app/router/index.js`
- Modify: `resources/js/app/components/layout/AppSidebar.vue`

**Step 1: Update router**

Replace the settings import and route:

```js
// Remove:
import SettingsForm from '@/views/settings/Index.vue'

// Add:
import SeoForm from '@/views/seo/Index.vue'
```

Replace the route:
```js
{
    path: '/dashboard/seo',
    name: 'seo.edit',
    component: SeoForm,
    meta: { title: 'SEO' },
},
```

Remove the old settings route:
```js
// Remove:
{
    path: '/dashboard/settings',
    name: 'settings.edit',
    component: SettingsForm,
    meta: { title: 'Einstellungen' },
},
```

**Step 2: Update AppSidebar navigation**

Replace the Einstellungen entry:
```js
// Remove:
{ name: 'Einstellungen', to: '/dashboard/settings', icon: PhGear },

// Add:
{ name: 'SEO', to: '/dashboard/seo', icon: PhGear },
```

**Step 3: Commit**

```bash
git add resources/js/app/router/index.js \
        resources/js/app/components/layout/AppSidebar.vue
git commit -m "feat: update router and sidebar to use SEO module"
```

---

### Task 8: Add is_og toggle to media store and project form

**Files:**
- Modify: `resources/js/app/stores/media.js`
- Modify: `resources/js/app/components/media/MediaGrid.vue`
- Modify: `resources/js/app/components/media/MediaCard.vue`
- Modify: `resources/js/app/views/projects/Form.vue`

**Step 1: Add `setOg` action to media store**

In `resources/js/app/stores/media.js`, add after `setTeaser`:

```js
async setOg(uuid) {
    const item = this.items.find(i => i.uuid === uuid)
    const wasOg = item?.is_og

    if (item?._temp) {
        this.items = this.items.map(i => ({
            ...i,
            is_og: wasOg ? false : i.uuid === uuid,
        }))
        return
    }

    await mediaApi.og(uuid)
    this.items = this.items.map(i => ({
        ...i,
        is_og: wasOg ? false : i.uuid === uuid,
    }))
},
```

**Step 2: Add `og` emit to MediaCard**

In `MediaCard.vue`, add `'og'` to `defineEmits`:
```js
const emit = defineEmits(['edit', 'delete', 'teaser', 'og', 'click'])
```

Add the OG button alongside the teaser button (only shown when `showOg` prop is true):
```vue
// Add prop:
showOg: { type: Boolean, default: false },
isOg: { type: Boolean, default: false },

// Add button in overlay (after teaser button):
<button v-if="showOg" type="button" class="text-white/70 hover:text-white transition-colors cursor-pointer" title="Als OG Image setzen" @click.stop="emit('og', media)">
    <PhImage :size="18" :weight="isOg ? 'fill' : 'light'" />
</button>
```

Add the import at top of script:
```js
import { PhTrash, PhPencil, PhStar, PhImage } from '@phosphor-icons/vue'
```

Add badge for OG state — after the teaser badge or combined:
```vue
<div
    v-if="isOg"
    class="absolute top-0 right-0 bg-gray-900 text-white text-[9px] font-medium tracking-wide uppercase px-6 py-3 leading-none"
>
    OG
</div>
```

**Step 3: Pass `showOg`/`isOg`/`@og` through MediaGrid**

In `MediaGrid.vue`:

Add to `defineEmits`:
```js
const emit = defineEmits(['edit', 'delete', 'reorder', 'teaser', 'og'])
```

Add props:
```js
showOg: { type: Boolean, default: false },
```

Pass to MediaCard:
```vue
:showOg="showOg"
:isOg="element.is_og"
@og="emit('og', $event)"
```

**Step 4: Wire up in projects Form.vue**

In `projects/Form.vue`, update the MediaGrid call:
```vue
<MediaGrid
    v-if="mediaStore.items.length"
    :items="mediaStore.items"
    :showOg="true"
    @edit="onEditMedia"
    @delete="onDeleteMedia"
    @reorder="onReorderMedia"
    @teaser="onSetTeaser"
    @og="onSetOg"
/>
```

Add handler:
```js
function onSetOg(media) { mediaStore.setOg(media.uuid) }
```

**Step 5: Commit**

```bash
git add resources/js/app/stores/media.js \
        resources/js/app/components/media/MediaCard.vue \
        resources/js/app/components/media/MediaGrid.vue \
        resources/js/app/views/projects/Form.vue
git commit -m "feat: add is_og toggle to media store, MediaCard, MediaGrid, and project form"
```

---

### Task 9: Blade public head — render meta tags via view composer

**Files:**
- Create: `app/View/Composers/SeoComposer.php`
- Modify: `app/Providers/AppServiceProvider.php`
- Modify: `resources/views/components/layout/guest.blade.php`

**Step 1: Create SeoComposer**

```php
<?php

namespace App\View\Composers;

use App\Models\SeoSetting;
use Illuminate\View\View;

class SeoComposer
{
    public function compose(View $view): void
    {
        $view->with('seo', SeoSetting::with('media')->first());
    }
}
```

**Step 2: Register composer in AppServiceProvider**

In `app/Providers/AppServiceProvider.php`, add to the `boot` method:

```php
use Illuminate\Support\Facades\View;
use App\View\Composers\SeoComposer;

View::composer('components.layout.guest', SeoComposer::class);
```

**Step 3: Update guest.blade.php to render meta and OG tags**

Replace the current `<head>` content:

```blade
<!DOCTYPE html>
<html lang="de" class="h-full scroll-smooth">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ config('app.name') }}</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
@if (!empty($metaDescription))
<meta name="description" content="{{ $metaDescription }}">
@endif
@if ($seo?->og_title)
<meta property="og:title" content="{{ $ogTitle ?? $seo->og_title }}">
@endif
@if ($seo?->og_description)
<meta property="og:description" content="{{ $ogDescription ?? $seo->og_description }}">
@endif
@php $ogImage = $ogImage ?? $seo?->media->first()?->file @endphp
@if ($ogImage)
<meta property="og:image" content="{{ url('uploads/' . $ogImage) }}">
@endif
<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
<link rel="icon" type="image/svg+xml" href="/favicon.svg" />
<link rel="shortcut icon" href="/favicon.ico" />
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
@vite(['resources/css/app.css'])
</head>
<body class="h-full font-sans antialiased">
    {{ $slot }}
</body>
</html>
```

The layout accepts optional `$metaDescription`, `$ogTitle`, `$ogDescription`, `$ogImage` variables passed from individual page controllers. When not passed, it falls back to global `$seo` values.

**Step 4: Update `routes/web.php` landing route to pass meta**

The landing page uses `Route::view()` currently which doesn't pass data. Convert to a controller or pass via `Route::view` with data:

```php
Route::get('/', function () {
    $seo = \App\Models\SeoSetting::first();
    return view('pages.landing', [
        'metaDescription' => $seo?->landing_meta_description,
    ]);
})->name('page.landing');
```

**Step 5: Commit**

```bash
git add app/View/ \
        app/Providers/AppServiceProvider.php \
        resources/views/components/layout/guest.blade.php \
        routes/web.php
git commit -m "feat: render meta/OG tags in blade head via SeoComposer"
```

---

### Task 10: Smoke test end-to-end

**Step 1: Migrate and seed fresh**

```bash
php artisan migrate:fresh --seed
```

**Step 2: Check no broken references to SiteSetting**

```bash
grep -r "SiteSetting\|site_settings\|settings_store\|useSettingsStore\|/dashboard/settings" \
    app/ resources/ routes/ database/ \
    --include="*.php" --include="*.js" --include="*.vue" --include="*.blade.php"
```

Expected: No matches.

**Step 3: Build frontend assets**

```bash
npm run build
```

Expected: No errors.

**Step 4: Manual checks**

- Visit `/dashboard/seo` — form loads, save works
- Visit `/` — view source, confirm `<meta name="description">` renders when set
- On a project, toggle the OG star on a media item — confirm it persists

**Step 5: Final commit**

```bash
git add -A
git commit -m "chore: SEO module complete — replace SiteSetting with SeoSetting"
```
