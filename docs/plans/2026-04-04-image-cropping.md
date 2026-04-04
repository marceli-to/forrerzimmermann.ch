# Image Cropping Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Allow editors to define a focal crop region on media images, stored non-destructively as JSON and applied when Glide serves images.

**Architecture:** A nullable JSON `crop` column on `media` stores `{x, y, w, h}` pixel coordinates. `MediaResource` assembles Glide's `crop=w,h,x,y` param when set. The Blade `<x-media.image>` component accepts a `crop` array and appends the param to every URL it builds. A new `PATCH /crop` endpoint + `MediaCrop.vue` modal (using `vue-advanced-cropper`) lets editors set/clear crops from the `MediaCard` overlay.

**Tech Stack:** Laravel (migrations, Form Requests, Actions, Resources), Vue 3 + Pinia, `vue-advanced-cropper`, Phosphor Icons (`PhCrop`), League Glide 3.1

---

## Task 1: Migration — add `crop` column

**Files:**
- Create: `database/migrations/2026_04_04_000001_add_crop_to_media_table.php`

**Step 1: Create the migration**

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
            $table->json('crop')->nullable()->after('height');
        });
    }

    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->dropColumn('crop');
        });
    }
};
```

**Step 2: Run the migration**

```bash
php artisan migrate
```

Expected: `Migrating: 2026_04_04_000001_add_crop_to_media_table` then `Migrated`.

**Step 3: Commit**

```bash
git add database/migrations/2026_04_04_000001_add_crop_to_media_table.php
git commit -m "feat: add crop column to media table"
```

---

## Task 2: Update `Media` model

**Files:**
- Modify: `app/Models/Media.php`

**Step 1: Add `crop` to `$fillable` and `$casts`**

In `$fillable` array, add `'crop'` after `'height'`:
```php
'crop',
```

In `$casts` array, add:
```php
'crop' => 'array',
```

**Step 2: Verify with tinker**

```bash
php artisan tinker
>>> App\Models\Media::first()->crop
```

Expected: `null` (no crop set yet).

**Step 3: Commit**

```bash
git add app/Models/Media.php
git commit -m "feat: add crop to Media model fillable and casts"
```

---

## Task 3: Update `MediaResource` to include crop and apply it to URLs

**Files:**
- Modify: `app/Http/Resources/MediaResource.php`

**Step 1: Write the failing test**

In `tests/Feature/MediaTest.php`, add:

```php
it('includes crop in media resource', function () {
    $media = Media::factory()->create([
        'crop' => ['x' => 100, 'y' => 50, 'w' => 800, 'h' => 600],
    ]);

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/media')
        ->assertOk()
        ->assertJsonPath('data.0.crop.x', 100)
        ->assertJsonPath('data.0.crop.y', 50)
        ->assertJsonPath('data.0.crop.w', 800)
        ->assertJsonPath('data.0.crop.h', 600);
});

it('appends crop param to thumbnail_url when crop is set', function () {
    $media = Media::factory()->create([
        'file' => 'test-image.jpg',
        'crop' => ['x' => 100, 'y' => 50, 'w' => 800, 'h' => 600],
    ]);

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/media')
        ->assertOk()
        ->assertJsonPath('data.0.thumbnail_url', '/img/uploads/test-image.jpg?w=400&h=400&fit=crop&crop=800,600,100,50');
});

it('does not append crop param when crop is null', function () {
    $media = Media::factory()->create([
        'file' => 'test-image.jpg',
        'crop' => null,
    ]);

    $this->actingAs($this->user)
        ->getJson('/api/dashboard/media')
        ->assertOk()
        ->assertJsonPath('data.0.thumbnail_url', '/img/uploads/test-image.jpg?w=400&h=400&fit=crop');
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --filter "includes crop in media resource|appends crop param|does not append crop"
```

Expected: 3 failures.

**Step 3: Update `MediaResource::toArray()`**

Replace the `toArray` method in `app/Http/Resources/MediaResource.php`:

```php
public function toArray(Request $request): array
{
    $cropParam = $this->crop
        ? '&crop=' . $this->crop['w'] . ',' . $this->crop['h'] . ',' . $this->crop['x'] . ',' . $this->crop['y']
        : '';

    return [
        'id' => $this->id,
        'uuid' => $this->uuid,
        'file' => $this->file,
        'original_name' => $this->original_name,
        'mime_type' => $this->mime_type,
        'size' => $this->size,
        'alt' => $this->alt,
        'caption' => $this->caption,
        'width' => $this->width,
        'height' => $this->height,
        'crop' => $this->crop,
        'orientation' => $this->orientation,
        'is_teaser' => $this->is_teaser,
        'is_og' => $this->is_og,
        'sort_order' => $this->sort_order,
        'original_url' => '/uploads/' . $this->file,
        'thumbnail_url' => '/img/uploads/' . $this->file . '?w=400&h=400&fit=crop' . $cropParam,
        'preview_url' => '/img/uploads/' . $this->file . '?w=800&fit=max',
    ];
}
```

Note: Glide's `crop` param order is `width,height,x,y` — not `x,y,w,h`.

**Step 4: Run tests to verify they pass**

```bash
php artisan test --filter "includes crop in media resource|appends crop param|does not append crop"
```

Expected: 3 passing.

**Step 5: Commit**

```bash
git add app/Http/Resources/MediaResource.php tests/Feature/MediaTest.php
git commit -m "feat: expose crop in MediaResource, apply to thumbnail_url"
```

---

## Task 4: `CropMediaRequest` + `CropAction` + route + controller method

**Files:**
- Create: `app/Http/Requests/Media/CropMediaRequest.php`
- Create: `app/Actions/Media/CropAction.php`
- Modify: `app/Http/Controllers/Api/MediaController.php`
- Modify: `routes/api.php`

**Step 1: Write the failing tests**

In `tests/Feature/MediaTest.php`, add:

```php
it('sets crop on media', function () {
    $media = Media::factory()->create(['crop' => null]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/crop", [
            'x' => 100, 'y' => 50, 'w' => 800, 'h' => 600,
        ])
        ->assertOk()
        ->assertJsonPath('data.crop.x', 100)
        ->assertJsonPath('data.crop.y', 50)
        ->assertJsonPath('data.crop.w', 800)
        ->assertJsonPath('data.crop.h', 600);

    expect($media->fresh()->crop)->toBe(['x' => 100, 'y' => 50, 'w' => 800, 'h' => 600]);
});

it('clears crop on media when null values sent', function () {
    $media = Media::factory()->create([
        'crop' => ['x' => 100, 'y' => 50, 'w' => 800, 'h' => 600],
    ]);

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/crop", [
            'x' => null, 'y' => null, 'w' => null, 'h' => null,
        ])
        ->assertOk()
        ->assertJsonPath('data.crop', null);

    expect($media->fresh()->crop)->toBeNull();
});

it('rejects invalid crop values', function () {
    $media = Media::factory()->create();

    $this->actingAs($this->user)
        ->patchJson("/api/dashboard/media/{$media->uuid}/crop", [
            'x' => 'bad', 'y' => 50, 'w' => 800, 'h' => 600,
        ])
        ->assertUnprocessable();
});
```

**Step 2: Run tests to verify they fail**

```bash
php artisan test --filter "sets crop on media|clears crop on media|rejects invalid crop"
```

Expected: 3 failures (404 on missing route).

**Step 3: Create `CropMediaRequest`**

```php
<?php

namespace App\Http\Requests\Media;

use Illuminate\Foundation\Http\FormRequest;

class CropMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'x' => 'nullable|integer|min:0',
            'y' => 'nullable|integer|min:0',
            'w' => 'nullable|integer|min:1',
            'h' => 'nullable|integer|min:1',
        ];
    }
}
```

**Step 4: Create `CropAction`**

```php
<?php

namespace App\Actions\Media;

use App\Models\Media;

class CropAction
{
    public function execute(Media $media, array $data): Media
    {
        $allNull = is_null($data['x']) && is_null($data['y'])
            && is_null($data['w']) && is_null($data['h']);

        $media->update([
            'crop' => $allNull ? null : [
                'x' => $data['x'],
                'y' => $data['y'],
                'w' => $data['w'],
                'h' => $data['h'],
            ],
        ]);

        return $media;
    }
}
```

**Step 5: Add `crop` method to `MediaController`**

Add import at the top:
```php
use App\Actions\Media\CropAction;
use App\Http\Requests\Media\CropMediaRequest;
```

Add method to `MediaController`:
```php
public function crop(CropMediaRequest $request, Media $media)
{
    $media = (new CropAction)->execute($media, $request->validated());

    return new MediaResource($media);
}
```

**Step 6: Add route in `routes/api.php`**

Inside the media route group, after the `og` route:
```php
Route::patch('/{media}/crop', 'crop');
```

**Step 7: Run tests to verify they pass**

```bash
php artisan test --filter "sets crop on media|clears crop on media|rejects invalid crop"
```

Expected: 3 passing.

**Step 8: Run the full test suite**

```bash
php artisan test
```

Expected: all passing.

**Step 9: Commit**

```bash
git add app/Http/Requests/Media/CropMediaRequest.php \
        app/Actions/Media/CropAction.php \
        app/Http/Controllers/Api/MediaController.php \
        routes/api.php \
        tests/Feature/MediaTest.php
git commit -m "feat: add crop endpoint, CropAction, CropMediaRequest"
```

---

## Task 5: Update Blade `<x-media.image>` component to accept and apply crop

**Files:**
- Modify: `app/View/Components/Media/Image.php`

**Step 1: Add `crop` parameter to constructor**

Add to the constructor signature after `string $loading = 'lazy'`:
```php
?array $crop = null,
```

Add assignment in constructor body after `$this->loading = $loading;`:
```php
$this->crop = $crop;
```

Add property declaration after `public string $loading;`:
```php
public ?array $crop;
```

**Step 2: Update `buildUrl()` to append crop param**

Replace the `buildUrl` method:

```php
public function buildUrl(string $format = null, ?int $width = null, ?int $height = null): string
{
    $params = [];
    $useWidth = $width ?? $this->width;
    $useHeight = $height ?? $this->height;

    if ($useWidth) {
        $params[] = 'w=' . $useWidth;
    }

    if ($useHeight) {
        $params[] = 'h=' . $useHeight;
    }

    if ($this->fit) {
        $params[] = 'fit=' . $this->fit;
    }

    if ($this->crop && $this->fit === 'crop') {
        $params[] = 'crop=' . $this->crop['w'] . ',' . $this->crop['h'] . ',' . $this->crop['x'] . ',' . $this->crop['y'];
    }

    if ($format) {
        $params[] = 'fm=' . $format;
    }

    $params[] = 'q=' . $this->quality;

    $queryString = implode('&', $params);

    return '/img/' . $this->src . ($queryString ? '?' . $queryString : '');
}
```

Note: Crop only appends when `fit=crop` (the default). For `fit=max` or `fit=contain`, crop doesn't apply.

**Step 3: Manually test in a Blade view**

Find any view that uses `<x-media.image>` and temporarily pass a hard-coded crop to confirm the URL renders correctly:
```blade
<x-media.image
  :src="'uploads/' . $media->file"
  :crop="['x' => 100, 'y' => 50, 'w' => 800, 'h' => 600]"
  :width="800"
  :height="600"
/>
```

Inspect the rendered HTML — the `src` and `srcset` attributes should contain `&crop=800,600,100,50`.

**Step 4: Commit**

```bash
git add app/View/Components/Media/Image.php
git commit -m "feat: add crop support to x-media.image Blade component"
```

---

## Task 6: Install `vue-advanced-cropper`

**Step 1: Install the package**

```bash
npm install vue-advanced-cropper
```

**Step 2: Verify install**

```bash
node -e "require('vue-advanced-cropper'); console.log('ok')"
```

Expected: `ok`

**Step 3: Commit**

```bash
git add package.json package-lock.json
git commit -m "chore: install vue-advanced-cropper"
```

---

## Task 7: Add `crop` API method to `media.js` API module

**Files:**
- Modify: `resources/js/app/api/media.js`

**Step 1: Add `crop` method**

Add after `og`:
```js
crop: (uuid, data) => api.patch(`/media/${uuid}/crop`, data),
```

**Step 2: Commit**

```bash
git add resources/js/app/api/media.js
git commit -m "feat: add crop method to media API module"
```

---

## Task 8: Add `setCrop` action to `media.js` Pinia store

**Files:**
- Modify: `resources/js/app/stores/media.js`

**Step 1: Add `setCrop` action**

Add after `setOg`:
```js
async setCrop(uuid, cropData) {
    const index = this.items.findIndex(i => i.uuid === uuid)
    if (index === -1) return

    const item = this.items[index]

    if (item._temp) {
        this.items[index] = { ...item, crop: cropData }
        return
    }

    const { data: response } = await mediaApi.crop(uuid, cropData)
    this.items[index] = response.data
},
```

**Step 2: Commit**

```bash
git add resources/js/app/stores/media.js
git commit -m "feat: add setCrop action to media Pinia store"
```

---

## Task 9: Create `MediaCrop.vue` modal component

**Files:**
- Create: `resources/js/app/components/media/MediaCrop.vue`

The component opens as a modal (not a drawer — needs width for the cropper). Use a simple modal overlay pattern consistent with the existing UI.

```vue
<script setup>
import { ref, watch, computed } from 'vue'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

const props = defineProps({
  media: { type: Object, default: null },
})

const emit = defineEmits(['close', 'save'])

const isOpen = ref(false)
const cropperRef = ref(null)
const aspectRatio = ref(null) // null = free crop

const aspectOptions = [
  { label: 'Frei', value: null },
  { label: '16:9', value: 16 / 9 },
  { label: '4:3', value: 4 / 3 },
  { label: '1:1', value: 1 },
]

watch(() => props.media, (val) => {
  isOpen.value = !!val
  aspectRatio.value = null
}, { immediate: true })

function close() {
  isOpen.value = false
  emit('close')
}

function handleSave() {
  const { coordinates } = cropperRef.value.getResult()
  emit('save', {
    uuid: props.media.uuid,
    crop: {
      x: Math.round(coordinates.left),
      y: Math.round(coordinates.top),
      w: Math.round(coordinates.width),
      h: Math.round(coordinates.height),
    },
  })
  isOpen.value = false
}

function handleClear() {
  emit('save', {
    uuid: props.media.uuid,
    crop: { x: null, y: null, w: null, h: null },
  })
  isOpen.value = false
}

const defaultPosition = computed(() => {
  if (!props.media?.crop) return undefined
  return {
    left: props.media.crop.x,
    top: props.media.crop.y,
  }
})

const defaultSize = computed(() => {
  if (!props.media?.crop) return undefined
  return {
    width: props.media.crop.w,
    height: props.media.crop.h,
  }
})
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
      @click.self="close"
    >
      <div class="bg-white dark:bg-warm-900 rounded-lg shadow-xl w-full max-w-3xl mx-16 flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex items-center justify-between px-24 py-16 border-b border-gray-200 dark:border-warm-700">
          <h2 class="text-sm font-medium text-gray-900 dark:text-warm-100">Bild zuschneiden</h2>
          <button type="button" class="text-gray-400 hover:text-gray-600 dark:text-warm-500 dark:hover:text-warm-300 cursor-pointer" @click="close">
            &times;
          </button>
        </div>

        <!-- Aspect ratio controls -->
        <div class="flex gap-8 px-24 py-12 border-b border-gray-100 dark:border-warm-800">
          <button
            v-for="opt in aspectOptions"
            :key="opt.label"
            type="button"
            class="text-xs px-10 py-4 rounded border transition-colors cursor-pointer"
            :class="aspectRatio === opt.value
              ? 'bg-gray-900 text-white border-gray-900 dark:bg-warm-100 dark:text-warm-900 dark:border-warm-100'
              : 'border-gray-300 text-gray-600 hover:border-gray-500 dark:border-warm-600 dark:text-warm-400'"
            @click="aspectRatio = opt.value"
          >
            {{ opt.label }}
          </button>
        </div>

        <!-- Cropper -->
        <div class="flex-1 overflow-hidden bg-neutral-900 p-16 min-h-0">
          <Cropper
            v-if="media"
            ref="cropperRef"
            :src="media.original_url"
            :stencil-props="{ aspectRatio }"
            :default-position="defaultPosition"
            :default-size="defaultSize"
            class="max-h-[50vh]"
          />
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between px-24 py-16 border-t border-gray-200 dark:border-warm-700">
          <button
            type="button"
            class="text-sm text-gray-400 dark:text-warm-500 hover:text-gray-600 dark:hover:text-warm-300 transition-colors cursor-pointer"
            @click="handleClear"
          >
            Zuschnitt entfernen
          </button>
          <div class="flex gap-12">
            <button
              type="button"
              class="text-sm inline-flex items-center justify-center rounded-md px-16 py-8 bg-gray-900 dark:bg-warm-100 text-white dark:text-warm-900 hover:bg-gray-800 dark:hover:bg-warm-200 transition-colors cursor-pointer"
              @click="handleSave"
            >
              Speichern
            </button>
            <button
              type="button"
              class="text-sm inline-flex items-center justify-center rounded-md px-16 py-8 bg-gray-100 dark:bg-warm-800 text-gray-700 dark:text-warm-300 hover:bg-gray-200 dark:hover:bg-warm-700 transition-colors cursor-pointer"
              @click="close"
            >
              Abbrechen
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>
```

**Step 2: Build and check for errors**

```bash
npm run build 2>&1 | tail -20
```

Expected: no errors mentioning `MediaCrop` or `vue-advanced-cropper`.

**Step 3: Commit**

```bash
git add resources/js/app/components/media/MediaCrop.vue
git commit -m "feat: add MediaCrop modal component with vue-advanced-cropper"
```

---

## Task 10: Add crop button to `MediaCard` and wire up `MediaCrop` in `MediaGrid`

**Files:**
- Modify: `resources/js/app/components/media/MediaCard.vue`
- Modify: `resources/js/app/components/media/MediaGrid.vue`

**Step 1: Add `PhCrop` icon and `crop` emit to `MediaCard`**

In the `<script setup>` import line, add `PhCrop`:
```js
import { PhTrash, PhPencil, PhStar, PhImage, PhCrop } from '@phosphor-icons/vue'
```

Add `crop` to `defineEmits`:
```js
const emit = defineEmits(['edit', 'delete', 'teaser', 'og', 'crop', 'click'])
```

Add a `hasCrop` prop (optional, for showing the button only on images):
```js
hasCrop: { type: Boolean, default: false },
```

In the overlay actions div, add the crop button after the edit button:
```html
<button v-if="hasCrop" type="button" class="text-white/70 hover:text-white transition-colors cursor-pointer" title="Zuschneiden" @click.stop="emit('crop', media)">
  <PhCrop :size="18" weight="light" />
</button>
```

**Step 2: Wire up `MediaCrop` in `MediaGrid.vue`**

Read `MediaGrid.vue` first to understand the existing edit/delete wiring, then:

- Import `MediaCrop` and `useMediaStore`
- Add `cropMedia` ref (the item being cropped, or null)
- Add `<MediaCrop :media="cropMedia" @close="cropMedia = null" @save="handleCropSave" />`
- Add `handleCropSave`:
```js
async function handleCropSave({ uuid, crop }) {
  await store.setCrop(uuid, crop)
  cropMedia.value = null
}
```
- Pass `has-crop` and `@crop="cropMedia = $event"` to `<MediaCard>` (only for image mime types)

**Step 3: Build and verify**

```bash
npm run build 2>&1 | tail -20
```

Expected: clean build.

**Step 4: Commit**

```bash
git add resources/js/app/components/media/MediaCard.vue \
        resources/js/app/components/media/MediaGrid.vue
git commit -m "feat: add crop button to MediaCard, wire MediaCrop in MediaGrid"
```

---

## Task 11: Final verification

**Step 1: Run the full test suite**

```bash
php artisan test
```

Expected: all passing.

**Step 2: Run a full build**

```bash
npm run build
```

Expected: no errors or warnings.

**Step 3: Manual smoke test**

1. Open a project with images in the dashboard
2. Hover a MediaCard — confirm the crop icon appears
3. Click the crop icon — confirm `MediaCrop` modal opens with the image
4. Draw a crop region, click Save
5. Confirm the `thumbnail_url` in the network response now contains `&crop=w,h,x,y`
6. Click the crop icon again — confirm the previous crop region is pre-selected
7. Click "Zuschnitt entfernen" — confirm `crop` returns to `null` in the response
8. Use `<x-media.image :crop="$media->crop" ...>` in a Blade view — inspect HTML to confirm `&crop=` appears in `srcset`
