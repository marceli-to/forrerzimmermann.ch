<script setup>
const model = defineModel()

defineProps({
	id: { type: String, default: null },
})
</script>

<template>
	<label class="inline-flex items-center gap-10 cursor-pointer group">
		<span class="relative flex items-center justify-center size-14">
			<input
				:id="id"
				type="checkbox"
				v-model="model"
				class="peer appearance-none size-14 border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-800 checked:bg-neutral-900 dark:checked:bg-neutral-100 checked:border-neutral-900 dark:checked:border-neutral-100 focus:ring-0 focus:ring-offset-0 cursor-pointer transition-colors"
			/>
			<svg
				class="absolute pointer-events-none text-white dark:text-neutral-900 opacity-0 peer-checked:opacity-100 transition-opacity"
				width="10" height="10" viewBox="0 0 10 10" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
			>
				<polyline points="1.5 5.5 4 8 8.5 2.5" />
			</svg>
		</span>
		<span class="text-sm text-neutral-600 dark:text-neutral-400 group-hover:text-neutral-900 dark:group-hover:text-neutral-200 transition-colors select-none">
			<slot />
		</span>
	</label>
</template>
