# Forrer Zimmermann Architektur — Backend Module Specification

## Global: Media Table (Polymorphic)

All images across the site are managed through a single polymorphic media table.

### `media`

| Field             | Type        | Notes                                        |
|-------------------|-------------|----------------------------------------------|
| `id`              | bigint (PK) |                                              |
| `uuid`            | uuid        | Public-facing identifier                     |
| `mediable_id`     | bigint      | Polymorphic: owning record ID               |
| `mediable_type`   | string      | Polymorphic: owning model class              |
| `collection`      | string      | Grouping key, e.g. `hero`, `gallery`, `plan`, `portrait`, `atelier` |
| `filename`        | string      | Stored filename                              |
| `original_filename` | string    | Original upload name                         |
| `mime_type`       | string      |                                              |
| `disk`            | string      | Storage disk reference                       |
| `path`            | string      | Path on disk                                 |
| `width`           | int (null)  | Pixel width (images only)                    |
| `height`          | int (null)  | Pixel height (images only)                   |
| `size`            | bigint      | File size in bytes                           |
| `alt`             | string (null) | Alt text (translatable if needed)          |
| `caption`         | string (null) | Caption / Bildlegende (used for plans)     |
| `sort_order`      | int         | Ordering within the collection               |
| `created_at`      | timestamp   |                                              |
| `updated_at`      | timestamp   |                                              |

**Collections used throughout the site:**

- `hero` — Home carousel slides
- `gallery` — Project photo galleries
- `plan` — Project floor plans / drawings (these get a visible caption)
- `portrait` — Team member photos
- `atelier` — Atelier section images (Profil, Team, Jobs each have their own image)

---

## Module 1: Home Carousel

The homepage features a full-width carousel that auto-advances and supports manual arrow navigation. Each slide can be either a full-bleed image or an image+text combination.

### `home_slides`

| Field             | Type        | Notes                                        |
|-------------------|-------------|----------------------------------------------|
| `id`              | bigint (PK) |                                              |
| `type`            | enum        | `image_only`, `image_text`                   |
| `heading`         | text (null) | Large text block (only for `image_text` type)|
| `link_url`        | string (null) | Optional link target                       |
| `is_published`    | boolean     | Visibility toggle                            |
| `sort_order`      | int         | Slide ordering                               |
| `created_at`      | timestamp   |                                              |
| `updated_at`      | timestamp   |                                              |

**Relations:**
- Has one or more `media` (collection `hero`). For `image_text`, the image sits left (~65% width), text right. For `image_only`, the image is centered/full-width.

---

## Module 2: Projects (Projekte)

Projects are the core content type. They appear in two views:

1. **Auswahl** (Selection) — curated grid of featured projects, 4 columns
2. **Werkliste** (Work list) — complete chronological list, text only

Projects that are not linked (i.e. have no detail page content) appear greyed out in the grid.

### `projects`

| Field                 | Type         | Notes                                        |
|-----------------------|--------------|----------------------------------------------|
| `id`                  | bigint (PK)  |                                              |
| `slug`                | string       | URL slug, unique                             |
| `title`               | string       | Project name, e.g. "Nietengasse, Zürich"     |
| `subtitle`            | string (null)| Short descriptor, e.g. "Instandsetzung 2024" |
| `year`                | int          | Project year (for Werkliste sorting/grouping)|
| `description`         | text (null)  | Full project description (rich text)         |
| `is_featured`         | boolean      | Show in Auswahl grid                         |
| `is_published`        | boolean      | Has detail page content / is linked          |
| `sort_order_auswahl`  | int (null)   | Ordering in the Auswahl grid                 |
| `sort_order_werkliste`| int (null)   | Ordering in the Werkliste                    |
| `created_at`          | timestamp    |                                              |
| `updated_at`          | timestamp    |                                              |

### `project_details`

Structured metadata shown on the project text tab. Stored as key-value pairs to stay flexible (the design shows varying fields per project).

| Field         | Type        | Notes                                        |
|---------------|-------------|----------------------------------------------|
| `id`          | bigint (PK) |                                              |
| `project_id`  | bigint (FK) | → `projects.id`                              |
| `label`       | string      | e.g. "Status", "Gesamtbaukosten", "Leistungen", "Auftragsart", "Termine", "Auftraggeber" |
| `value`       | text        | e.g. "Ausgeführt", "CHF 2.31 Mio. (BKP 1–9)"|
| `sort_order`  | int         | Display ordering                             |

### `project_collaborators`

The team/partners listed at the bottom of the project text view.

| Field         | Type        | Notes                                        |
|---------------|-------------|----------------------------------------------|
| `id`          | bigint (PK) |                                              |
| `project_id`  | bigint (FK) | → `projects.id`                              |
| `role`        | string      | e.g. "Bauingenieur", "Haustechnik", "Elektroingenieur", "Bauphysik" |
| `name`        | string      | e.g. "HKP Bauingenieure AG"                  |
| `sort_order`  | int         | Display ordering                             |

**Project media relations:**
- Collection `gallery` — photos shown in the Bilder tab carousel (with left/right arrows)
- Collection `plan` — floor plans / drawings (shown in same carousel, but with a visible caption underneath, e.g. "Grundriss 1.OG")

**Project detail view behavior:**
- Opens in a **layer/overlay** without the main logo and navigation
- Top bar: project title (left), prev/next project arrows + close button (right)
- Bottom tabs: **Bilder** (images) | **Text** (description + details)
- Images navigate with left/right arrows on the sides

---

## Module 3: Atelier

The Atelier section has three sub-pages, each with a consistent layout: image left (~65%), text content right. Navigated via bottom tabs: **Profil** | **Team** | **Jobs**.

### `atelier_pages`

| Field         | Type        | Notes                                        |
|---------------|-------------|----------------------------------------------|
| `id`          | bigint (PK) |                                              |
| `slug`        | string      | `profil`, `team`, `jobs` (fixed set)         |
| `title`       | string      | Page heading                                 |
| `body`        | text        | Rich text content                            |
| `sort_order`  | int         | Tab ordering                                 |
| `updated_at`  | timestamp   |                                              |

**Relations:**
- Has one or more `media` (collection `atelier`)

---

## Module 4: Team Members

Displayed on the Atelier > Team sub-page. Each member shows name, title, email, and an expandable CV (Lebenslauf). Former employees are listed under a collapsible "Ehemalige Mitarbeitende" section.

### `team_members`

| Field             | Type        | Notes                                        |
|-------------------|-------------|----------------------------------------------|
| `id`              | bigint (PK) |                                              |
| `first_name`      | string      |                                              |
| `last_name`       | string      |                                              |
| `title`           | string      | Professional title, e.g. "Architektin MSc ETH" |
| `email`           | string      | e.g. "kzi@forrerzimmermann.ch"               |
| `is_active`       | boolean     | `false` = listed under "Ehemalige Mitarbeitende" |
| `has_cv`          | boolean     | Whether CV accordion is shown                |
| `sort_order`      | int         | Display ordering                             |
| `created_at`      | timestamp   |                                              |
| `updated_at`      | timestamp   |                                              |

### `team_member_cv_entries`

The expandable Lebenslauf entries, shown as a timeline.

| Field             | Type        | Notes                                        |
|-------------------|-------------|----------------------------------------------|
| `id`              | bigint (PK) |                                              |
| `team_member_id`  | bigint (FK) | → `team_members.id`                          |
| `period`          | string      | e.g. "2012–2016"                             |
| `description`     | string      | e.g. "Adrian Streich Architekten, Zürich"    |
| `sort_order`      | int         | Chronological ordering                       |

**Relations:**
- Team members share the team photo (one image for all). This could be handled as a `media` on the `atelier_pages` record with slug `team`, or as a site-wide setting.

---

## Module 5: Job Listings

Displayed on the Atelier > Jobs sub-page. The design shows a single listing but the module should support multiple.

### `job_listings`

| Field             | Type        | Notes                                        |
|-------------------|-------------|----------------------------------------------|
| `id`              | bigint (PK) |                                              |
| `title`           | string      | e.g. "Praktikumstelle"                       |
| `body`            | text        | Rich text job description                    |
| `is_published`    | boolean     | Visibility toggle                            |
| `sort_order`      | int         | Display ordering                             |
| `created_at`      | timestamp   |                                              |
| `updated_at`      | timestamp   |                                              |

*Note: The Jobs sub-page image comes from the `atelier_pages` media. Job listings themselves are text-only content blocks displayed on the right side.*

---

## Module 6: Contact (Kontakt)

The contact page shows a map (Google Maps embed) on the left and company info on the right. Mostly static but should be editable.

### `settings` (or a dedicated `contact_info` single)

| Field             | Type        | Notes                                        |
|-------------------|-------------|----------------------------------------------|
| `company_name`    | string      | "Forrer Zimmermann Architekten GmbH"         |
| `street`          | string      | "Badenerstrasse 370"                         |
| `zip_city`        | string      | "CH–8004 Zürich"                             |
| `phone`           | string      | "+41 44 548 90 01"                           |
| `email`           | string      | "mail@forrerzimmermann.ch"                   |
| `map_lat`         | decimal     | Latitude for Google Maps embed               |
| `map_lng`         | decimal     | Longitude for Google Maps embed              |
| `impressum`       | text (null) | Expandable Impressum content                 |

---

## Module 7: Navigation & Global Settings

### `navigation`

The main nav is fixed: **Projekte** | **Atelier** | **Kontakt**, with the logo linking to Home. The active page is highlighted in a distinct color (purple/magenta in the design). This can be hardcoded in the frontend.

### `site_settings` (global)

| Field                 | Type        | Notes                                    |
|-----------------------|-------------|------------------------------------------|
| `site_title`          | string      | "forrer zimmermann architektur"          |
| `meta_description`    | string      | Default SEO description                  |
| `og_image`            | media       | Default social sharing image             |

---

## Interaction & UI Behavior Notes

These don't affect the data model directly but are important for the backend API/response structure:

1. **Projekte grid hover state**: On rollover, the project title and subtitle turn purple/magenta — purely frontend CSS, no data needed.
2. **Greyed-out projects**: Projects where `is_published = false` appear visually muted in the grid. They have a thumbnail but no link.
3. **Project overlay**: The project detail opens as a layer. The prev/next arrows cycle through projects in the current list order. The backend should provide the prev/next project slugs.
4. **Werkliste future option**: The design notes a future sorting option by "Jahr" (year) and "Thema" (theme). Consider adding a `theme` or `category` field to `projects` later, or use a tags/categories relation when needed.
5. **Carousel auto-advance**: Home carousel auto-rotates and supports arrow navigation — frontend only, but the backend should return slides in `sort_order`.
6. **Plan captions**: Only floor plans/drawings get a visible caption below the image. This is handled by the `caption` field on the `media` record (collection `plan`).
7. **CV accordion**: Lebenslauf opens/closes inline on the Team page — frontend toggle, data from `team_member_cv_entries`.
