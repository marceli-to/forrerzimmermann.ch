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
		class="block w-full px-12 py-12 border border-gray-200 rounded-md bg-white text-sm transition-all focus:outline-none focus:ring-2 focus:ring-gray-200 focus:border-gray-300 resize-none"
	/>
</template>
