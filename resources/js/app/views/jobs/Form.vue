<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useJobStore } from '@/stores/jobs'
import { useToast } from '@/composables/useToast'
import Editor from '@/components/ui/editor/Editor.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import FormError from '@/components/ui/form/FormError.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const route = useRoute()
const router = useRouter()
const store = useJobStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)

const form = ref({
	title: '',
	text: '',
})

onMounted(async () => {
	if (isEdit.value) {
		await store.fetchJob(route.params.id)
		if (store.current) {
			form.value = {
				title: store.current.title || '',
				text: store.current.text || '',
			}
		}
	}
})

async function handleSubmit() {
	const success = await store.saveJob(
		form.value,
		isEdit.value ? route.params.id : null
	)

	if (success) {
		toast.success(isEdit.value ? 'Stelle aktualisiert' : 'Stelle erstellt')
		router.push({ name: 'jobs.index' })
	} else if (Object.keys(store.errors).length) {
		toast.error('Bitte überprüfen Sie das Formular')
	}
}
</script>

<template>
	<div>
		<PageHeader :title="isEdit ? 'Stelle bearbeiten' : 'Neue Stelle'">
			<FormButton variant="secondary" @click="router.push({ name: 'jobs.index' })">
				Abbrechen
			</FormButton>
			<FormButton @click="handleSubmit">
				{{ isEdit ? 'Aktualisieren' : 'Erstellen' }}
			</FormButton>
		</PageHeader>

		<div v-if="store.loading" class="text-sm text-neutral-400">
			Laden...
		</div>

		<form v-else class="max-w-4xl" @submit.prevent="handleSubmit">
			<FormGroup>
				<FormLabel for="title">Titel *</FormLabel>
				<FormInput id="title" v-model="form.title" />
				<FormError :message="store.errors.title" />
			</FormGroup>

			<FormGroup>
				<FormLabel>Beschreibung</FormLabel>
				<div class="mt-8">
					<Editor v-model="form.text" />
				</div>
				<FormError :message="store.errors.text" />
			</FormGroup>
		</form>
	</div>
</template>
