<script setup>
const model = defineModel()

defineProps({
	placeholder: { type: String, default: null },
	id: { type: String, default: null },
	rows: { type: [String, Number], default: 3 },
})
</script>

<template>
	<textarea
		:id="id"
		:rows="rows"
		:placeholder="placeholder"
		v-model="model"
		class="block w-full px-0 py-12 text-sm bg-transparent border-0 border-b border-neutral-300 dark:border-neutral-700 text-neutral-900 dark:text-neutral-100 placeholder:text-neutral-400 dark:placeholder:text-neutral-500 focus:border-neutral-900 dark:focus:border-neutral-400 focus:ring-0 transition-colors duration-200 outline-none resize-none"
	/>
</template>
