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
		class="block w-full px-0 pt-12 pb-8 text-sm bg-transparent border-0 border-b border-neutral-300 dark:border-neutral-700 text-neutral-900 dark:text-neutral-100 focus:border-neutral-900 dark:focus:border-neutral-400 focus:ring-0 transition-colors duration-200 outline-none disabled:opacity-40 disabled:cursor-not-allowed"
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
