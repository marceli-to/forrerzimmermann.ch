# Media System (Existing)

The media system is already implemented and shared across all content modules. This document describes the current state.

---

## Database Schema

### `media`

| Field           | Type              | Notes                                      |
|-----------------|-------------------|--------------------------------------------|
| `id`            | bigint (PK)       |                                            |
| `uuid`          | uuid (unique)     | Route binding key, auto-generated          |
| `mediable_type` | string (nullable) | Polymorphic: owning model class            |
| `mediable_id`   | bigint (nullable) | Polymorphic: owning record ID              |
| `file`          | string            | Stored filename (slugified + random suffix)|
| `original_name` | string (nullable) | Original upload filename                   |
| `mime_type`     | string (nullable) | e.g. `image/jpeg`, `video/mp4`             |
| `size`          | bigint (nullable) | File size in bytes                         |
| `alt`           | string (nullable) | Alt text                                   |
| `caption`       | string (nullable) | Caption (e.g. for floor plans)             |
| `width`         | int (nullable)    | Pixel width                                |
| `height`        | int (nullable)    | Pixel height                               |
| `is_teaser`     | boolean           | Featured/teaser flag (one per parent)      |
| `sort_order`    | integer           | Ordering within parent, default 0          |
| `created_at`    | timestamp         |                                            |
| `updated_at`    | timestamp         |                                            |

---

## Upload Flow

Two-stage process:

1. **Temporary upload** — file goes to `storage/app/public/temp/` via Uppy (chunked, drag-and-drop)
2. **Attachment** — when parent record is saved, `AttachMediaAction` moves file from `temp/` to `uploads/` and creates the `media` record with polymorphic binding

---

## Polymorphic Relationship

Any model can have media via `MorphMany`:

```php
public function media(): MorphMany
{
    return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
}
```

Teaser shortcut (one per parent):

```php
public function teaser(): MorphMany
{
    return $this->morphMany(Media::class, 'mediable')->where('is_teaser', true);
}
```

---

## Image Processing (Glide)

On-demand image transformation via League\Glide 3.1 (ImageMagick driver).

**Route:** `GET /img/{path}?{params}`

| Param | Purpose                          |
|-------|----------------------------------|
| `w`   | Width in pixels                  |
| `h`   | Height in pixels                 |
| `fit` | Crop mode (crop, contain, max)   |
| `fm`  | Format (avif, webp, jpg, png)    |
| `q`   | Quality (0–100)                  |

Cache: `storage/app/.glide-cache/`, 1-year expiry.

---

## API Endpoints

**Prefix:** `/api/dashboard/media` (authenticated)

| Method | Route              | Purpose                        |
|--------|--------------------|--------------------------------|
| GET    | `/`                | List all media                 |
| POST   | `/upload`          | Upload to temp                 |
| PUT    | `/{media}`         | Update alt/caption             |
| DELETE | `/{media}`         | Delete (unattached only)       |
| PATCH  | `/reorder`         | Batch reorder                  |
| PATCH  | `/{media}/teaser`  | Toggle teaser status           |

**Upload validation:** `jpg, jpeg, png, webp, gif, mp4, mov, webm` — max 50 MB.

**Delete protection:** Returns 422 if media is attached to a model.

---

## Action Classes

| Action              | Purpose                                                  |
|---------------------|----------------------------------------------------------|
| `UploadAction`      | Save file to temp, extract dimensions, return metadata   |
| `AttachAction`      | Move from temp to uploads, create media records          |
| `UpdateAction`      | Update alt/caption                                       |
| `DeleteAction`      | Delete file + record (only if unattached)                |
| `ReorderAction`     | Batch update sort_order                                  |
| `SetTeaserAction`   | Toggle is_teaser (clears others on same parent first)    |

---

## Frontend Components (Vue 3)

| Component         | Purpose                                         |
|-------------------|-------------------------------------------------|
| `MediaUploader`   | Uppy-based drag-and-drop upload with progress   |
| `MediaGrid`       | Draggable grid for reordering, 6-column layout  |
| `MediaCard`       | Thumbnail with edit/delete/teaser actions        |
| `MediaEdit`       | Modal drawer for alt + caption editing           |
| `GridMediaPicker` | Modal for selecting existing media               |

**State:** Pinia store (`media.js`) manages items, upload state, and API calls.

---

## Blade Components

- **`<x-media.image>`** — Renders responsive `<picture>` with AVIF/WebP/JPEG sources and Glide URLs
- **`<x-media.slideshow>`** — Swiper-based carousel with info panel

---

## Storage Paths

| Path                          | Purpose                    |
|-------------------------------|----------------------------|
| `storage/app/public/temp/`    | Temporary uploads          |
| `storage/app/public/uploads/` | Permanent media files      |
| `storage/app/.glide-cache/`   | Glide transformation cache |

---

## Note for Forrer Zimmermann

No `collection` field is needed. Media is scoped by its polymorphic parent — each model (HomeSlide, Project, TeamMember, etc.) owns its own media. The `is_teaser` flag and `caption` field cover the remaining distinctions.
