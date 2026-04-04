# Topics Sort Order Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Enable drag-and-drop reordering of Topics in the admin dashboard, persisted via the existing reorder API endpoint.

**Architecture:** The backend infrastructure is already complete (migration, model, ReorderAction, ReorderTopicRequest, route). Three small changes are needed: fix the backend index ordering, add a store action, and wire up the DataTable's draggable-rows mode in the Vue view — mirroring the Landing module pattern exactly.

**Tech Stack:** Laravel (PHP), Vue 3 (Composition API), Pinia, vuedraggable v4

---

### Task 1: Fix backend ordering

**Files:**
- Modify: `app/Http/Controllers/Api/TopicController.php:19`

**Step 1: Change `orderBy('title')` to `orderBy('sort_order')`**

In `index()`, replace:
```php
Topic::orderBy('title')->get()
```
with:
```php
Topic::orderBy('sort_order')->get()
```

**Step 2: Verify in browser / Tinker**

```bash
php artisan tinker --execute="App\Models\Topic::orderBy('sort_order')->get()->pluck('title', 'sort_order');"
```
Expected: topics listed by sort_order value (all 0 initially is fine).

**Step 3: Commit**

```bash
git add app/Http/Controllers/Api/TopicController.php
git commit -m "fix: order topics by sort_order instead of title"
```

---

### Task 2: Add reorderTopics action to Pinia store

**Files:**
- Modify: `resources/js/app/stores/topics.js`

**Step 1: Add the action**

Inside the `actions` object, after `deleteTopic`, add:

```js
async reorderTopics(items) {
    await topicsApi.reorder(items)
},
```

**Step 2: Verify import is already present**

`topicsApi` is already imported at the top of the file — no changes needed there.

**Step 3: Commit**

```bash
git add resources/js/app/stores/topics.js
git commit -m "feat: add reorderTopics action to topics store"
```

---

### Task 3: Wire up draggable reordering in Index.vue

**Files:**
- Modify: `resources/js/app/views/topics/Index.vue`

**Step 1: Add `ref` and `watch` to imports**

Change:
```js
import { onMounted } from 'vue'
```
to:
```js
import { ref, onMounted, watch } from 'vue'
```

**Step 2: Add `draggableTopics` ref and wire up watch + reorder handler**

After the `const { confirm } = useConfirm()` line, add:

```js
const draggableTopics = ref([])
```

Replace the `onMounted` block:
```js
onMounted(() => {
    store.fetchTopics()
})
```
with:
```js
onMounted(async () => {
    await store.fetchTopics()
    draggableTopics.value = [...store.topics]
})

watch(() => store.topics, (val) => {
    draggableTopics.value = [...val]
})

async function handleReorder() {
    const items = draggableTopics.value.map((topic, index) => ({
        uuid: topic.uuid,
        sort_order: index,
    }))
    await store.reorderTopics(items)
}
```

**Step 3: Update the DataTable binding**

Replace:
```html
<DataTable v-else :columns="columns" :rows="store.topics">
```
with:
```html
<DataTable
    v-else
    v-model="draggableTopics"
    :columns="columns"
    :rows="draggableTopics"
    :draggable-rows="true"
    @update:model-value="handleReorder"
>
```

Also update the `v-else-if` empty state check to use `draggableTopics`:
```html
<div v-else-if="store.topics.length === 0" ...>
```
This can stay as-is since it reads from the store, which is fine.

**Step 4: Verify in browser**

- Open Topics index
- Drag a row — it should move
- Reload the page — order should be preserved

**Step 5: Commit**

```bash
git add resources/js/app/views/topics/Index.vue
git commit -m "feat: enable drag-and-drop reordering for topics"
```
