<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useTopicStore } from '@/stores/topics'
import { useToast } from '@/composables/useToast'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import FormError from '@/components/ui/form/FormError.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const route = useRoute()
const router = useRouter()
const store = useTopicStore()
const toast = useToast()

const isEdit = computed(() => !!route.params.id)

const form = ref({
	title: '',
})

onMounted(async () => {
	if (isEdit.value) {
		await store.fetchTopic(route.params.id)
		if (store.current) {
			form.value = {
				title: store.current.title || '',
			}
		}
	}
})

async function handleSubmit() {
	const success = await store.saveTopic(
		form.value,
		isEdit.value ? route.params.id : null
	)

	if (success) {
		toast.success(isEdit.value ? 'Thema aktualisiert' : 'Thema erstellt')
		router.push({ name: 'topics.index' })
	} else if (Object.keys(store.errors).length) {
		toast.error('Bitte überprüfen Sie das Formular')
	}
}
</script>

<template>
	<div>
		<PageHeader :title="isEdit ? 'Thema bearbeiten' : 'Neues Thema'">
			<FormButton variant="secondary" @click="router.push({ name: 'topics.index' })">
				Abbrechen
			</FormButton>
			<FormButton @click="handleSubmit">
				{{ isEdit ? 'Aktualisieren' : 'Erstellen' }}
			</FormButton>
		</PageHeader>

		<div v-if="store.loading" class="text-sm text-gray-400">
			Laden...
		</div>

		<form v-else class="max-w-4xl" @submit.prevent="handleSubmit">
			<FormGroup>
				<FormLabel for="title">Titel *</FormLabel>
				<FormInput id="title" v-model="form.title" />
				<FormError :message="store.errors.title" />
			</FormGroup>
		</form>
	</div>
</template>
