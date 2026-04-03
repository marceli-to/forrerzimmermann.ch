<script setup>
const model = defineModel()

defineProps({
	id: { type: String, default: null },
	options: { type: Array, default: () => [] }, // [{ value, label }]
	placeholder: { type: String, default: '– Auswählen –' },
	disabled: { type: Boolean, default: false },
})
</script>

<template>
	<select
		:id="id"
		v-model="model"
		:disabled="disabled"
		class="block w-full px-12 py-10 border border-gray-200 dark:border-warm-700 rounded-md appearance-none bg-white dark:bg-warm-800 text-sm dark:text-warm-100 transition-all focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-warm-700 focus:border-gray-300 dark:focus:border-warm-600 disabled:opacity-40 disabled:cursor-not-allowed"
	>
		<option :value="null">{{ placeholder }}</option>
		<option
			v-for="option in options"
			:key="option.value"
			:value="option.value"
		>
			{{ option.label }}
		</option>
	</select>
</template>
