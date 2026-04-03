<script setup>
import { ref, watch, onMounted } from 'vue'
import { PhX } from '@phosphor-icons/vue'

const props = defineProps({
	open: { type: Boolean, default: false },
	title: { type: String, default: null },
	size: { type: String, default: 'sm' }, // sm, md, lg
})

const emit = defineEmits(['close'])

const dialogRef = ref(null)

const sizes = {
	sm: 'max-w-360',
	md: 'max-w-480',
	lg: 'max-w-640',
}

watch(() => props.open, (val) => {
	if (val) {
		dialogRef.value?.showModal()
	} else {
		dialogRef.value?.close()
	}
})

function onClose() {
	emit('close')
}
</script>

<template>
	<dialog
		ref="dialogRef"
		class="p-0 m-auto bg-white backdrop:bg-black/40 w-full"
		:class="sizes[size]"
		@close="onClose"
		@click.self="onClose"
	>
		<div class="p-24">
			<!-- Header -->
			<div v-if="title || $slots.header" class="flex items-start justify-between mb-20">
				<slot name="header">
					<h2 class="text-sm font-medium text-neutral-900">{{ title }}</h2>
				</slot>
				<button
					type="button"
					class="text-neutral-400 hover:text-neutral-900 transition-colors cursor-pointer -mt-2 -mr-2"
					@click="onClose"
				>
					<PhX :size="16" weight="light" />
				</button>
			</div>

			<!-- Body -->
			<div>
				<slot />
			</div>

			<!-- Footer -->
			<div v-if="$slots.footer" class="mt-24">
				<slot name="footer" />
			</div>
		</div>
	</dialog>
</template>
