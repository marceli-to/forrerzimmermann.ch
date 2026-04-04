# Jobs Sort Order Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task.

**Goal:** Enable drag-and-drop reordering of Job listings in the admin dashboard, persisted via the existing reorder API endpoint.

**Architecture:** The backend is fully complete (sort_order field, ReorderAction, ReorderJobRequest, reorder route, jobsApi.reorder()). Two frontend changes needed: add a `reorderJobs()` store action, then wire up the DataTable's draggable-rows mode in Index.vue — identical pattern to Topics.

**Tech Stack:** Laravel (PHP), Vue 3 (Composition API), Pinia, vuedraggable v4

---

### Task 1: Add reorderJobs action to Pinia store

**Files:**
- Modify: `resources/js/app/stores/jobs.js`

**Step 1: Add the action**

Inside the `actions` object, after `deleteJob`, add:

```js
async reorderJobs(items) {
    await jobsApi.reorder(items)
},
```

`jobsApi` is already imported. `jobsApi.reorder(items)` already exists in `resources/js/app/api/jobs.js`.

**Step 2: Verify with Read**

Read the file and confirm `reorderJobs` appears after `deleteJob`.

**Step 3: Commit**

```bash
git add resources/js/app/stores/jobs.js
git commit -m "feat: add reorderJobs action to jobs store"
```

---

### Task 2: Wire up draggable reordering in Jobs Index.vue

**Files:**
- Modify: `resources/js/app/views/jobs/Index.vue`

**Step 1: Add `ref` and `watch` to Vue imports**

Change:
```js
import { onMounted } from 'vue'
```
to:
```js
import { ref, onMounted, watch } from 'vue'
```

**Step 2: Add `draggableJobs` ref and wire up watch + reorder handler**

After `const { confirm } = useConfirm()`, add:

```js
const draggableJobs = ref([])
```

Replace the `onMounted` block:
```js
onMounted(() => {
    store.fetchJobs()
})
```
with:
```js
onMounted(async () => {
    await store.fetchJobs()
    draggableJobs.value = [...store.jobs]
})

watch(() => store.jobs, (val) => {
    draggableJobs.value = [...val]
})

async function handleReorder() {
    const items = draggableJobs.value.map((job, index) => ({
        uuid: job.uuid,
        sort_order: index,
    }))
    await store.reorderJobs(items)
}
```

**Step 3: Update the DataTable binding**

Replace:
```html
<DataTable v-else :columns="columns" :rows="store.jobs">
```
with:
```html
<DataTable
    v-else
    v-model="draggableJobs"
    :columns="columns"
    :rows="draggableJobs"
    :draggable-rows="true"
    @update:model-value="handleReorder"
>
```

Also update the empty state check — change:
```html
<div v-else-if="store.jobs.length === 0"
```
This can stay as-is since it reads from the store, which is correct.

**Step 4: Verify with Read**

Read the final file and confirm:
- `draggableJobs` ref exists
- `handleReorder` maps to `{ uuid, sort_order: index }` and calls `store.reorderJobs(items)`
- DataTable has `v-model`, `:rows="draggableJobs"`, `:draggable-rows="true"`, `@update:model-value="handleReorder"`

**Step 5: Commit**

```bash
git add resources/js/app/views/jobs/Index.vue
git commit -m "feat: enable drag-and-drop reordering for jobs"
```
