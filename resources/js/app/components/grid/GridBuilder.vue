<script setup>
import { ref, computed, onMounted } from 'vue'
import draggable from 'vuedraggable'
import { PhList, PhGridFour } from '@phosphor-icons/vue'
import { useGridStore } from '@/stores/grids'
import { useMediaStore } from '@/stores/media'
import GridLayoutSelector from './GridLayoutSelector.vue'
import GridRow from './GridRow.vue'
import GridListItem from './GridListItem.vue'
import GridMediaPicker from './GridMediaPicker.vue'

const props = defineProps({
	projectId: { type: [Number, String], required: true },
})

const gridStore = useGridStore()
const mediaStore = useMediaStore()

const viewMode = ref('grid')
const pickerVisible = ref(false)
const pickerContext = ref(null)

const imageMedia = computed(() =>
	mediaStore.items.filter(i => i.type === 'image')
)

const dragGrids = computed({
	get: () => gridStore.grids,
	set: (value) => gridStore.reorderGrids(props.projectId, value),
})

function getLayoutConfig(key) {
	return gridStore.layouts.find(l => l.key === key) || null
}

onMounted(async () => {
	await gridStore.fetchLayouts(props.projectId)
	await gridStore.fetchGrids(props.projectId)
})

async function onAddGrid(layoutKey) {
	await gridStore.addGrid(props.projectId, layoutKey)
}

async function onDeleteGrid(grid) {
	await gridStore.removeGrid(props.projectId, grid.id)
}

function onAddMedia({ gridId, position }) {
	pickerContext.value = { gridId, position }
	pickerVisible.value = true
}

async function onSelectMedia(media) {
	if (!pickerContext.value) return
	const { gridId, position } = pickerContext.value
	await gridStore.assignItem(props.projectId, gridId, {
		media_id: media.id,
		position,
	})
	pickerContext.value = null
}

async function onRemoveMedia({ gridId, item }) {
	await gridStore.removeItem(props.projectId, gridId, item.id)
}

function onClosePicker() {
	pickerVisible.value = false
	pickerContext.value = null
}
</script>

<template>
	<div>
		<!-- Header with layout selector and view toggle -->
		<div class="">
			<p class="text-xxs font-medium uppercase tracking-[0.08em] text-neutral-500 dark:text-neutral-400 mb-12">
				Zeile hinzufügen
			</p>
			<GridLayoutSelector
				:layouts="gridStore.layouts"
				@add="onAddGrid"
			/>
      <div class="flex justify-end mb-6">
        <button
          v-if="gridStore.grids.length && viewMode === 'grid'"
          type="button"
          class="mt-12 p-4 text-neutral-400 hover:text-neutral-600 transition-colors cursor-pointer"
          title="Listenansicht (Sortieren)"
          @click="viewMode = 'list'"
        >
          <PhList :size="20" weight="light" />
        </button>

        <button
          v-else-if="gridStore.grids.length && viewMode === 'list'"
          type="button"
          class="mt-12 p-4 text-neutral-400 hover:text-neutral-600 transition-colors cursor-pointer"
          title="Rasteransicht"
          @click="viewMode = 'grid'"
        >
          <PhGridFour :size="20" weight="light" />
        </button>
      </div>
		</div>

		<!-- Loading -->
		<div v-if="gridStore.loading" class="text-sm text-neutral-400">
			Laden...
		</div>

		<!-- Grid view (editing, no drag sorting) -->
		<template v-else-if="dragGrids.length">
			<div v-if="viewMode === 'grid'" class="flex flex-col gap-16">
				<GridRow
					v-for="grid in dragGrids"
					:key="grid.id"
					:grid="grid"
					:layout-config="getLayoutConfig(grid.layout_key)"
					@delete="onDeleteGrid"
					@add-media="onAddMedia"
					@remove-media="onRemoveMedia"
				/>
			</div>

			<!-- List view (compact rows with drag sorting) -->
			<draggable
				v-else
				v-model="dragGrids"
				item-key="id"
				handle=".grid-drag-handle"
				ghost-class="opacity-30"
				drag-class="!shadow-none"
				animation="150"
				class="flex flex-col gap-12"
			>
				<template #item="{ element }">
					<GridListItem
						:grid="element"
						@delete="onDeleteGrid"
					/>
				</template>
			</draggable>
		</template>

		<!-- Media picker modal -->
		<GridMediaPicker
			:media="imageMedia"
			:visible="pickerVisible"
			@select="onSelectMedia"
			@close="onClosePicker"
		/>
	</div>
</template>
