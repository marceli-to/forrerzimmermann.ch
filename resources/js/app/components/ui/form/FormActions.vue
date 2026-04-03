<script setup>
defineProps({
	submitLabel: { type: String, default: null },
	cancelLabel: { type: String, default: null },
})

const emit = defineEmits(['cancel'])
</script>

<template>
	<div class="fixed top-16 right-16 z-20 flex items-center gap-12 p-24">
		<slot>
			<button
				v-if="cancelLabel"
				type="button"
				class="text-sm px-16 py-8 rounded-md bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors cursor-pointer"
				@click="emit('cancel')"
			>
				{{ cancelLabel }}
			</button>
			<button
				v-if="submitLabel"
				type="submit"
				class="text-sm px-16 py-8 rounded-md bg-gray-900 text-white hover:bg-gray-800 transition-colors cursor-pointer"
			>
				{{ submitLabel }}
			</button>
		</slot>
	</div>
</template>
