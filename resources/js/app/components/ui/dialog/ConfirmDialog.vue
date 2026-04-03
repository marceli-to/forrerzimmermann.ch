<script setup>
import AppDialog from './AppDialog.vue'
import FormButton from '@/components/ui/form/FormButton.vue'

const props = defineProps({
	open: { type: Boolean, default: false },
	title: { type: String, default: 'Bestätigen' },
	message: { type: String, default: 'Sind Sie sicher?' },
	confirmLabel: { type: String, default: 'Bestätigen' },
	cancelLabel: { type: String, default: 'Abbrechen' },
	destructive: { type: Boolean, default: false },
})

const emit = defineEmits(['confirm', 'cancel'])
</script>

<template>
	<AppDialog :open="open" :title="title" @close="emit('cancel')">
		<p class="text-sm text-neutral-600">{{ message }}</p>

		<template #footer>
			<div class="flex justify-between">
				<FormButton variant="secondary" @click="emit('cancel')">
					{{ cancelLabel }}
				</FormButton>
				<FormButton
					:class="destructive ? '!bg-red-500 !border-red-500 hover:!bg-red-600 hover:!border-red-600 active:!bg-red-700 active:!border-red-700' : ''"
					@click="emit('confirm')"
				>
					{{ confirmLabel }}
				</FormButton>
			</div>
		</template>
	</AppDialog>
</template>
