<script setup>
import { ref, provide, computed } from 'vue'

const props = defineProps({
	modelValue: { type: String, default: null },
	tabs: { type: Array, required: true },
})

const emit = defineEmits(['update:modelValue'])

const activeTab = computed({
	get: () => props.modelValue || props.tabs[0]?.key,
	set: (val) => emit('update:modelValue', val),
})

provide('activeTab', activeTab)
</script>

<template>
	<div>
		<div class="flex gap-32 mb-32">
			<button
				v-for="tab in tabs"
				:key="tab.key"
				type="button"
				class="pb-4 text-xs font-medium uppercase tracking-[0.08em] transition-colors cursor-pointer"
				:class="activeTab === tab.key
					? 'text-neutral-900 border-b border-neutral-900'
					: 'text-neutral-400 hover:text-neutral-600'"
				@click="activeTab = tab.key"
			>
				{{ tab.label }}
			</button>
		</div>
		<div>
			<slot :active-tab="activeTab" />
		</div>
	</div>
</template>
