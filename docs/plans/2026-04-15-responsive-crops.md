# Responsive Crops (Desktop/Mobile) Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Allow editors to set separate crop regions for desktop and mobile on the same image, so the frontend can serve optimally cropped images per breakpoint.

**Architecture:** Extend the existing `crop` JSON column from a flat `{x,y,w,h}` structure to a nested `{desktop: {x,y,w,h}, mobile: {x,y,w,h}}`. Mobile is optional — when absent, desktop crop is used for all breakpoints. The `<picture>` element uses `media` attributes to switch between source sets at 768px.

**Tech Stack:** Laravel 11, Vue 3, vue-advanced-cropper, League Glide, Tailwind CSS

---

## Task 1: Artisan command to wrap existing crops

Create a command that migrates all existing flat crop data into the new nested structure.

**Files:**
- Create: `app/Console/Commands/WrapMediaCrops.php`

**Step 1: Create the command**

```php
<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;

class WrapMediaCrops extends Command
{
    protected $signature = 'app:wrap-media-crops';
    protected $description = 'Wrap existing flat crop data into {desktop: {...}} structure';

    public function handle(): void
    {
        $media = Media::whereNotNull('crop')->get();

        $count = 0;
        foreach ($media as $item) {
            $crop = $item->crop;

            // Skip if already in new format
            if (isset($crop['desktop'])) {
                continue;
            }

            // Wrap flat {x,y,w,h} into {desktop: {x,y,w,h}}
            if (isset($crop['x'], $crop['y'], $crop['w'], $crop['h'])) {
                $item->update([
                    'crop' => ['desktop' => $crop],
                ]);
                $count++;
            }
        }

        $this->info("Wrapped {$count} crop(s) into desktop format.");
    }
}
```

**Step 2: Verify**

Run: `php artisan app:wrap-media-crops`
Expected: Reports how many crops were wrapped.

**Step 3: Commit**

```
feat: add artisan command to wrap existing crops into nested format
```

---

## Task 2: Update CropAction and CropMediaRequest

The API now receives a `breakpoint` field (`desktop` or `mobile`) alongside the crop coordinates. The action merges the crop into the correct key of the nested structure.

**Files:**
- Modify: `app/Http/Requests/Media/CropMediaRequest.php`
- Modify: `app/Actions/Media/CropAction.php`

**Step 1: Update the form request**

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
            'breakpoint' => 'required|in:desktop,mobile',
            'x' => 'nullable|integer|min:0|required_with:y,w,h',
            'y' => 'nullable|integer|min:0|required_with:x,w,h',
            'w' => 'nullable|integer|min:1|required_with:x,y,h',
            'h' => 'nullable|integer|min:1|required_with:x,y,w',
        ];
    }
}
```

**Step 2: Update the action**

```php
<?php

namespace App\Actions\Media;

use App\Models\Media;

class CropAction
{
    public function execute(Media $media, array $data): Media
    {
        $breakpoint = $data['breakpoint'];

        $allNull = is_null($data['x']) && is_null($data['y'])
            && is_null($data['w']) && is_null($data['h']);

        $crop = $media->crop ?? [];

        if ($allNull) {
            unset($crop[$breakpoint]);
        } else {
            $crop[$breakpoint] = [
                'x' => $data['x'],
                'y' => $data['y'],
                'w' => $data['w'],
                'h' => $data['h'],
            ];
        }

        $media->update([
            'crop' => empty($crop) ? null : $crop,
        ]);

        return $media;
    }
}
```

**Step 3: Commit**

```
feat: update crop API to support desktop/mobile breakpoints
```

---

## Task 3: Update MediaResource

The thumbnail and preview URLs need to read crop from the `desktop` key of the nested structure.

**Files:**
- Modify: `app/Http/Resources/MediaResource.php`

**Step 1: Update the resource**

In `MediaResource.php`, replace the `$cropParam` logic:

```php
$desktopCrop = $this->crop['desktop'] ?? null;
$cropParam = $desktopCrop && isset($desktopCrop['w'], $desktopCrop['h'], $desktopCrop['x'], $desktopCrop['y'])
    ? '&crop=' . $desktopCrop['w'] . ',' . $desktopCrop['h'] . ',' . $desktopCrop['x'] . ',' . $desktopCrop['y']
    : '';
```

The rest of the `toArray` method stays the same.

**Step 2: Commit**

```
feat: update MediaResource to read crop from nested desktop key
```

---

## Task 4: Update Image.php component for responsive crops

When a mobile crop exists, the `<picture>` element needs two sets of sources: one for mobile (`max-width: 767px`) and one for desktop (`min-width: 768px`).

**Files:**
- Modify: `app/View/Components/Media/Image.php`

**Step 1: Rewrite the component**

Replace the entire class with:

```php
<?php

namespace App\View\Components\Media;

use App\Models\Media;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Image extends Component
{
    public string $src;
    public string $alt;
    public int $width;
    public int $height;
    public ?array $desktopCrop;
    public ?array $mobileCrop;
    public float $aspectRatio;
    public float $mobileAspectRatio;
    public string $fit;
    public int $quality;
    public array $formats;
    public string $class;
    public string $loading;
    public string $sizes;
    public array $sources = [];
    public array $mobileSources = [];
    public string $fallbackUrl;
    public bool $hasResponsiveCrop;

    protected const WIDTHS = [480, 640, 768, 1024, 1280, 1440, 1600, 1920];

    public function __construct(
        Media $media,
        string $sizes = '100vw',
        int $maxWidth = 1600,
        ?string $alt = null,
        string $fit = 'crop',
        int $quality = 90,
        array $formats = ['avif', 'webp', 'jpg'],
        string $class = '',
        string $loading = 'lazy',
    ) {
        $this->src = 'uploads/' . $media->file;
        $this->alt = $alt ?? $media->alt ?? '';
        $this->fit = $fit;
        $this->quality = $quality;
        $this->formats = $formats;
        $this->class = $class;
        $this->loading = $loading;
        $this->sizes = $sizes;

        // Extract desktop/mobile crops from nested structure
        $this->desktopCrop = $media->crop['desktop'] ?? null;
        $this->mobileCrop = $media->crop['mobile'] ?? null;
        $this->hasResponsiveCrop = $this->mobileCrop !== null;

        // Desktop aspect ratio
        if ($this->desktopCrop && isset($this->desktopCrop['w'], $this->desktopCrop['h'])) {
            $this->aspectRatio = $this->desktopCrop['h'] / $this->desktopCrop['w'];
        } else {
            $baseWidth = $media->width ?? 1;
            $baseHeight = $media->height ?? 1;
            $this->aspectRatio = $baseHeight / $baseWidth;
        }

        // Mobile aspect ratio
        if ($this->mobileCrop && isset($this->mobileCrop['w'], $this->mobileCrop['h'])) {
            $this->mobileAspectRatio = $this->mobileCrop['h'] / $this->mobileCrop['w'];
        } else {
            $this->mobileAspectRatio = $this->aspectRatio;
        }

        // Largest width for img width/height attributes
        $widths = array_values(array_filter(self::WIDTHS, fn ($w) => $w <= $maxWidth));
        $this->width = end($widths) ?: $maxWidth;
        $this->height = (int) round($this->width * $this->aspectRatio);

        $this->buildSources($widths);
    }

    protected function buildSources(array $widths): void
    {
        if ($this->hasResponsiveCrop) {
            // Mobile sources (small widths only)
            $mobileWidths = array_values(array_filter($widths, fn ($w) => $w <= 768));
            foreach ($this->formats as $format) {
                if ($format === 'jpg' || $format === 'jpeg') {
                    continue;
                }
                $this->mobileSources[] = [
                    'srcset' => $this->buildSrcset($format, $mobileWidths, $this->mobileCrop, $this->mobileAspectRatio),
                    'type' => $this->getMimeType($format),
                    'sizes' => $this->sizes,
                    'media' => '(max-width: 767px)',
                ];
            }
        }

        // Desktop sources (with media query only when mobile crop exists)
        foreach ($this->formats as $format) {
            if ($format === 'jpg' || $format === 'jpeg') {
                continue;
            }

            $source = [
                'srcset' => $this->buildSrcset($format, $widths, $this->desktopCrop, $this->aspectRatio),
                'type' => $this->getMimeType($format),
                'sizes' => $this->sizes,
            ];

            if ($this->hasResponsiveCrop) {
                $source['media'] = '(min-width: 768px)';
            }

            $this->sources[] = $source;
        }

        $this->fallbackUrl = $this->buildUrl('jpg', $this->width, $this->height, $this->desktopCrop);
    }

    protected function buildSrcset(string $format, array $widths, ?array $crop, float $aspectRatio): string
    {
        $parts = [];
        foreach ($widths as $w) {
            $h = (int) round($w * $aspectRatio);
            $parts[] = $this->buildUrl($format, $w, $h, $crop) . ' ' . $w . 'w';
        }

        return implode(', ', $parts);
    }

    protected function buildUrl(string $format, int $width, int $height, ?array $crop = null): string
    {
        $params = [
            'w=' . $width,
            'h=' . $height,
            'fit=' . $this->fit,
        ];

        if ($crop && $this->fit === 'crop' && isset($crop['w'], $crop['h'], $crop['x'], $crop['y'])) {
            $params[] = 'crop=' . $crop['w'] . ',' . $crop['h'] . ',' . $crop['x'] . ',' . $crop['y'];
        }

        $params[] = 'fm=' . $format;
        $params[] = 'q=' . $this->quality;

        return '/img/' . $this->src . '?' . implode('&', $params);
    }

    protected function getMimeType(string $format): string
    {
        return match ($format) {
            'avif' => 'image/avif',
            'webp' => 'image/webp',
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            default => 'image/jpeg',
        };
    }

    public function render(): View|Closure|string
    {
        return view('components.media.image');
    }
}
```

**Step 2: Commit**

```
feat: add responsive crop support to Image component
```

---

## Task 5: Update image.blade.php template

Render mobile sources before desktop sources in the `<picture>` element. Add `media` attribute when present.

**Files:**
- Modify: `resources/views/components/media/image.blade.php`

**Step 1: Update the template**

```blade
<picture>
	@foreach($mobileSources as $source)
		<source
			srcset="{{ $source['srcset'] }}"
			type="{{ $source['type'] }}"
			sizes="{{ $source['sizes'] }}"
			media="{{ $source['media'] }}"
		>
	@endforeach

	@foreach($sources as $source)
		<source
			srcset="{{ $source['srcset'] }}"
			type="{{ $source['type'] }}"
			sizes="{{ $source['sizes'] }}"
			@if(isset($source['media'])) media="{{ $source['media'] }}" @endif
		>
	@endforeach

	<img
		src="{{ $fallbackUrl }}"
		alt="{{ $alt }}"
		width="{{ $width }}"
		height="{{ $height }}"
		@if($class) class="{{ $class }}" @endif
		loading="{{ $loading }}"
		{{ $attributes }}
	>
</picture>
```

**Step 2: Commit**

```
feat: render mobile/desktop sources in picture element
```

---

## Task 6: Update MediaCrop.vue with Desktop/Mobile toggle

Add a breakpoint toggle to the crop dialog. Each breakpoint has independent crop state. Switching breakpoints loads/saves that breakpoint's crop. If a breakpoint has no crop yet, the cropper starts uncropped.

**Files:**
- Modify: `resources/js/app/components/media/MediaCrop.vue`

**Step 1: Rewrite the component**

```vue
<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from 'vue'
import { PhX, PhDesktop, PhDeviceMobile } from '@phosphor-icons/vue'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

const props = defineProps({
  media: { type: Object, default: null },
})

const emit = defineEmits(['close', 'save'])

const isOpen = ref(false)
const cropperRef = ref(null)
const aspectRatio = ref(null)
const breakpoint = ref('desktop')

// Store crops per breakpoint while the dialog is open
const crops = ref({ desktop: null, mobile: null })

const aspectOptions = [
  { label: 'Frei', value: null },
  { label: '3:2', value: 3 / 2 },
  { label: '4:3', value: 4 / 3 },
  { label: '1:1', value: 1 },
]

watch(() => props.media, (val) => {
  isOpen.value = !!val
  breakpoint.value = 'desktop'
  aspectRatio.value = null
  if (val) {
    crops.value = {
      desktop: val.crop?.desktop || null,
      mobile: val.crop?.mobile || null,
    }
  } else {
    crops.value = { desktop: null, mobile: null }
  }
}, { immediate: true })

function switchBreakpoint(bp) {
  // Save current cropper state before switching
  saveCropperState()
  breakpoint.value = bp
  aspectRatio.value = null
}

function saveCropperState() {
  if (!cropperRef.value) return
  const { coordinates } = cropperRef.value.getResult()
  crops.value[breakpoint.value] = {
    x: Math.round(coordinates.left),
    y: Math.round(coordinates.top),
    w: Math.round(coordinates.width),
    h: Math.round(coordinates.height),
  }
}

function close() {
  isOpen.value = false
  emit('close')
}

function onKeydown(e) {
  if (e.key === 'Escape' && isOpen.value) {
    document.activeElement?.blur()
    close()
  }
}

onMounted(() => document.addEventListener('keydown', onKeydown))
onUnmounted(() => document.removeEventListener('keydown', onKeydown))

function handleSave() {
  // Save current breakpoint state first
  saveCropperState()

  emit('save', {
    uuid: props.media.uuid,
    breakpoint: breakpoint.value,
    crop: crops.value[breakpoint.value],
  })
  isOpen.value = false
}

function handleClear() {
  emit('save', {
    uuid: props.media.uuid,
    breakpoint: breakpoint.value,
    crop: { x: null, y: null, w: null, h: null },
  })
  isOpen.value = false
}

const defaultPosition = computed(() => {
  const crop = crops.value[breakpoint.value]
  if (!crop) return undefined
  return { left: crop.x, top: crop.y }
})

const defaultSize = computed(() => {
  const crop = crops.value[breakpoint.value]
  if (!crop) return undefined
  return { width: crop.w, height: crop.h }
})

// Force re-mount cropper when breakpoint changes
const cropperKey = computed(() => `${props.media?.uuid}-${breakpoint.value}`)
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/30"
      @click.self="close"
    >
      <div class="bg-white dark:bg-warm-900 rounded-2xl shadow-xl w-full max-w-3xl mx-16 flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex items-center justify-between px-24 pt-16">
          <h2 class="text-sm font-medium text-gray-900 dark:text-warm-100">Bild zuschneiden</h2>
          <div class="flex items-center gap-4">
            <!-- Desktop/Mobile toggle -->
            <div class="flex items-center border border-gray-200 dark:border-warm-700 rounded-md overflow-hidden mr-8">
              <button
                type="button"
                class="flex items-center gap-4 text-xs px-10 py-4 transition-colors cursor-pointer"
                :class="breakpoint === 'desktop'
                  ? 'bg-gray-900 text-white dark:bg-warm-100 dark:text-warm-900'
                  : 'text-gray-600 hover:text-gray-900 dark:text-warm-400 dark:hover:text-warm-100'"
                @click="switchBreakpoint('desktop')"
              >
                <PhDesktop :size="14" weight="light" />
                Desktop
              </button>
              <button
                type="button"
                class="flex items-center gap-4 text-xs px-10 py-4 transition-colors cursor-pointer"
                :class="breakpoint === 'mobile'
                  ? 'bg-gray-900 text-white dark:bg-warm-100 dark:text-warm-900'
                  : 'text-gray-600 hover:text-gray-900 dark:text-warm-400 dark:hover:text-warm-100'"
                @click="switchBreakpoint('mobile')"
              >
                <PhDeviceMobile :size="14" weight="light" />
                Mobile
              </button>
            </div>
            <button type="button" class="text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer" @click="close">
              <PhX :size="16" weight="light" />
            </button>
          </div>
        </div>

        <!-- Cropper -->
        <div class="flex-1 overflow-hidden bg-white dark:bg-warm-900 p-16 min-h-0 [&_.vue-advanced-cropper]:!bg-gray-100 dark:[&_.vue-advanced-cropper]:!bg-warm-800">
          <Cropper
            v-if="media"
            ref="cropperRef"
            :key="cropperKey"
            :src="media.original_url"
            :stencil-props="{ aspectRatio }"
            :default-position="defaultPosition"
            :default-size="defaultSize"
            class="h-[60vh]"
            image-restriction="fit-area"
          />
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between px-24 pb-16">
          <div class="flex items-center gap-8">
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
            <button
              type="button"
              class="text-xs px-10 py-4 text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 transition-colors cursor-pointer"
              @click="handleClear"
            >
              Zurücksetzen
            </button>
          </div>
          <div class="flex gap-12">
            <button
              type="button"
              class="text-sm inline-flex items-center justify-center rounded-md px-16 py-8 bg-gray-100 dark:bg-warm-800 text-gray-700 dark:text-warm-300 hover:bg-gray-200 dark:hover:bg-warm-700 transition-colors cursor-pointer"
              @click="close"
            >
              Abbrechen
            </button>
            <button
              type="button"
              class="text-sm inline-flex items-center justify-center rounded-md px-16 py-8 bg-gray-900 dark:bg-warm-100 text-white dark:text-warm-900 hover:bg-gray-800 dark:hover:bg-warm-200 transition-colors cursor-pointer"
              @click="handleSave"
            >
              Speichern
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>
```

**Step 2: Commit**

```
feat: add desktop/mobile toggle to crop dialog
```

---

## Task 7: Update MediaGrid and media store

The `handleCropSave` in MediaGrid needs to pass `breakpoint` to the store. The store's `setCrop` method needs to send `breakpoint` to the API and merge the crop locally.

**Files:**
- Modify: `resources/js/app/components/media/MediaGrid.vue`
- Modify: `resources/js/app/stores/media.js`

**Step 1: Update MediaGrid.vue**

Change the `handleCropSave` function:

```js
async function handleCropSave({ uuid, breakpoint, crop }) {
  try {
    await store.setCrop(uuid, breakpoint, crop)
  } finally {
    cropMedia.value = null
  }
}
```

**Step 2: Update media store's `setCrop` action**

```js
async setCrop(uuid, breakpoint, cropData) {
    const index = this.items.findIndex(i => i.uuid === uuid)
    if (index === -1) return false

    const item = this.items[index]
    const allNull = cropData.x === null && cropData.y === null
        && cropData.w === null && cropData.h === null

    // Build the merged crop object
    const existingCrop = item.crop || {}
    let newCrop
    if (allNull) {
        newCrop = { ...existingCrop }
        delete newCrop[breakpoint]
        if (Object.keys(newCrop).length === 0) newCrop = null
    } else {
        newCrop = { ...existingCrop, [breakpoint]: cropData }
    }

    if (item._temp) {
        this.items[index] = { ...item, crop: newCrop }
        return true
    }

    this.errors = {}
    try {
        const { data: response } = await mediaApi.crop(uuid, { breakpoint, ...cropData })
        this.items[index] = response.data
        return true
    } catch (error) {
        if (error.response?.status === 422) {
            this.errors = error.response.data.errors
        }
        return false
    }
},
```

**Step 3: Commit**

```
feat: pass breakpoint through crop save flow
```

---

## Task 8: Build frontend assets and verify

**Step 1: Build**

Run: `npm run build`

**Step 2: Verify in browser**

- Open the CMS, go to a landing slide with an image
- Click crop — should see Desktop/Mobile toggle in header
- Set a desktop crop, switch to mobile, set a different crop
- Save and verify the crop JSON in the database has both keys
- Check the public landing page — inspect `<picture>` element, verify mobile sources appear with `media="(max-width: 767px)"`

**Step 3: Commit**

```
feat: build frontend assets for responsive crops
```
