# Image Cropping Design

Date: 2026-04-04

## Goal

Allow editors to define a focal crop region on images stored in the `media` table. The crop is stored non-destructively and applied when Glide serves images — the original file is never modified.

## Approach

- Crop region stored as a nullable JSON column (`crop`) on the `media` table
- Format: `{"x": 0, "y": 0, "w": 500, "h": 500}` — pixel values relative to the original image
- Glide's `crop` param format is `width,height,x,y` — assembled at render time
- Crop applies wherever `<x-media.image>` is used (all Glide URLs it builds), not just thumbnails

## Data Layer

### Migration

Add nullable JSON `crop` column to `media` table.

### Model

- Add `crop` to `$fillable`
- Cast `crop` as `array`

### MediaResource

- Expose `crop` in the JSON response
- When `crop` is set, append Glide `crop` param to `thumbnail_url` (and `preview_url` where `fit=crop` is used)

## Backend

### New endpoint

`PATCH /api/dashboard/media/{media}/crop`

- Accepts `{x, y, w, h}` as integers, all nullable (null clears the crop)
- Validates via a `CropMediaRequest`
- Delegates to a new `CropAction`
- Returns updated `MediaResource`

### CropAction

Sets or clears the `crop` JSON on the media record.

## Blade Component

`<x-media.image>` gets an optional `crop` parameter (nullable array). When set, `buildUrl()` appends `&crop=w,h,x,y` to every Glide URL it constructs.

Usage:
```blade
<x-media.image
  :src="'uploads/' . $media->file"
  :crop="$media->crop"
  :width="800"
  :height="600"
/>
```

## Frontend (Vue)

### New component: `MediaCrop.vue`

- Modal containing a `vue-advanced-cropper` instance
- Loads the full original image (`original_url`)
- Optional aspect ratio presets: free, 16:9, 4:3, 1:1
- Save, Cancel, and Clear crop buttons
- On save: calls `setCrop` store action with pixel coordinates

### MediaCard changes

- Add `PhCrop` icon to the hover overlay (alongside existing edit/delete/teaser/og icons)
- Emits `crop` event to parent

### Pinia store (`media.js`)

- New `setCrop(uuid, cropData)` action
- Calls `PATCH /api/dashboard/media/{uuid}/crop`
- Updates the item in state with the returned resource

### MediaEdit drawer

Unchanged — alt/caption editing stays separate.

## Out of scope

- Per-context crops (teaser vs. og vs. gallery)
- Destructive cropping
- Cropping for temp (unattached) media
