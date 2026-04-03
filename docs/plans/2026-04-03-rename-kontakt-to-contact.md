# Rename Kontakt → Contact Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Rename all "Kontakt/kontakt" references to "Contact/contact" across the full stack.

**Architecture:** Pure mechanical rename — no logic changes. PHP backend (controller, route), Vue frontend (api module, store, view, router, sidebar nav).

**Tech Stack:** Laravel, Vue 3, Pinia, Vue Router

---

### Task 1: Rename PHP controller

**Files:**
- Rename: `app/Http/Controllers/Api/KontaktController.php` → `app/Http/Controllers/Api/ContactController.php`
- Modify: `app/Http/Controllers/Api/ContactController.php` — class name only

**Step 1: Rename the file**

```bash
mv app/Http/Controllers/Api/KontaktController.php app/Http/Controllers/Api/ContactController.php
```

**Step 2: Update class name inside the file**

In `app/Http/Controllers/Api/ContactController.php`, change:
```php
class KontaktController extends Controller
```
to:
```php
class ContactController extends Controller
```

**Step 3: Commit**

```bash
git add app/Http/Controllers/Api/ContactController.php
git rm app/Http/Controllers/Api/KontaktController.php
git commit -m "Rename KontaktController to ContactController"
```

---

### Task 2: Update backend route

**Files:**
- Modify: `routes/api.php`

**Step 1: Update import and route prefix**

Change:
```php
use App\Http\Controllers\Api\KontaktController;
```
to:
```php
use App\Http\Controllers\Api\ContactController;
```

Change:
```php
Route::controller(KontaktController::class)
    ->prefix('kontakt')
```
to:
```php
Route::controller(ContactController::class)
    ->prefix('contact')
```

**Step 2: Commit**

```bash
git add routes/api.php
git commit -m "Update contact API route prefix from kontakt to contact"
```

---

### Task 3: Rename and update JS api module

**Files:**
- Rename: `resources/js/app/api/kontakt.js` → `resources/js/app/api/contact.js`

**Step 1: Rename file and update endpoint URLs**

New content of `resources/js/app/api/contact.js`:
```js
import api from './axios'

export default {
	show: () => api.get('/contact'),
	update: (data) => api.put('/contact', data),
}
```

**Step 2: Delete old file**

```bash
rm resources/js/app/api/kontakt.js
```

**Step 3: Commit**

```bash
git add resources/js/app/api/contact.js
git rm resources/js/app/api/kontakt.js
git commit -m "Rename api/kontakt.js to api/contact.js, update endpoint URLs"
```

---

### Task 4: Rename and update Pinia store

**Files:**
- Rename: `resources/js/app/stores/kontakt.js` → `resources/js/app/stores/contact.js`

**Step 1: Create `resources/js/app/stores/contact.js`**

```js
import { defineStore } from 'pinia'
import contactApi from '@/api/contact'

export const useContactStore = defineStore('contact', {
	state: () => ({
		contact: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchContact() {
			this.loading = true
			try {
				const { data } = await contactApi.show()
				this.contact = data.data
			} finally {
				this.loading = false
			}
		},

		async saveContact(form) {
			this.errors = {}
			try {
				await contactApi.update(form)
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

**Step 2: Delete old file**

```bash
rm resources/js/app/stores/kontakt.js
```

**Step 3: Commit**

```bash
git add resources/js/app/stores/contact.js
git rm resources/js/app/stores/kontakt.js
git commit -m "Rename store kontakt to contact, update store id and export name"
```

---

### Task 5: Rename and update Vue view

**Files:**
- Rename dir: `resources/js/app/views/kontakt/` → `resources/js/app/views/contact/`
- Modify: `resources/js/app/views/contact/Form.vue`

**Step 1: Rename directory**

```bash
mv resources/js/app/views/kontakt resources/js/app/views/contact
```

**Step 2: Update store import and usage in `resources/js/app/views/contact/Form.vue`**

Change:
```js
import { useKontaktStore } from '@/stores/kontakt'
```
to:
```js
import { useContactStore } from '@/stores/contact'
```

Change:
```js
const store = useKontaktStore()
```
to:
```js
const store = useContactStore()
```

**Step 3: Commit**

```bash
git add resources/js/app/views/contact/
git rm -r resources/js/app/views/kontakt/
git commit -m "Rename views/kontakt to views/contact, update store import"
```

---

### Task 6: Update Vue Router

**Files:**
- Modify: `resources/js/app/router/index.js`

**Step 1: Update import, path, name and meta**

Change:
```js
import KontaktForm from '@/views/kontakt/Form.vue'
```
to:
```js
import ContactForm from '@/views/contact/Form.vue'
```

Change the route entry:
```js
{
    path: '/dashboard/kontakt',
    name: 'kontakt.edit',
    component: KontaktForm,
    meta: { title: 'Kontakt' },
},
```
to:
```js
{
    path: '/dashboard/contact',
    name: 'contact.edit',
    component: ContactForm,
    meta: { title: 'Contact' },
},
```

**Step 2: Commit**

```bash
git add resources/js/app/router/index.js
git commit -m "Update router: rename kontakt route to contact"
```

---

### Task 7: Update sidebar navigation

**Files:**
- Modify: `resources/js/app/components/layout/AppSidebar.vue`

**Step 1: Update nav item**

Change:
```js
{ name: 'Kontakt', to: '/dashboard/kontakt', icon: PhEnvelope },
```
to:
```js
{ name: 'Contact', to: '/dashboard/contact', icon: PhEnvelope },
```

**Step 2: Commit**

```bash
git add resources/js/app/components/layout/AppSidebar.vue
git commit -m "Update sidebar nav: rename Kontakt to Contact"
```

---

### Task 8: Build and verify

**Step 1: Run the build**

```bash
npm run build
```

Expected: no errors, build succeeds.

**Step 2: Final commit if any build artifacts changed**

```bash
git add public/build/
git commit -m "Rebuild assets after kontakt → contact rename"
```
