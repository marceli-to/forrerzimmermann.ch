<script setup>
import { ref, onMounted } from 'vue'
import { useKontaktStore } from '@/stores/kontakt'
import { useToast } from '@/composables/useToast'
import Editor from '@/components/ui/editor/Editor.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormButton from '@/components/ui/form/FormButton.vue'
import FormError from '@/components/ui/form/FormError.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const store = useKontaktStore()
const toast = useToast()

const form = ref({
	name: '',
	address: '',
	email: '',
	phone: '',
	maps_url: '',
	imprint: '',
	meta_description: '',
})

onMounted(async () => {
	await store.fetchContact()
	if (store.contact) {
		const c = store.contact
		form.value = {
			name: c.name || '',
			address: c.address || '',
			email: c.email || '',
			phone: c.phone || '',
			maps_url: c.maps_url || '',
			imprint: c.imprint || '',
			meta_description: c.meta_description || '',
		}
	}
})

async function handleSubmit() {
	const success = await store.saveContact(form.value)
	if (success) {
		toast.success('Kontakt aktualisiert')
	} else if (Object.keys(store.errors).length) {
		toast.error('Bitte überprüfen Sie das Formular')
	}
}
</script>

<template>
	<div>
		<PageHeader title="Kontakt">
			<FormButton @click="handleSubmit">
				Speichern
			</FormButton>
		</PageHeader>

		<div v-if="store.loading" class="text-sm text-gray-400">
			Laden...
		</div>

		<form v-else class="max-w-4xl" @submit.prevent="handleSubmit">
			<FormGroup>
				<FormLabel for="name">Name *</FormLabel>
				<FormInput id="name" v-model="form.name" />
				<FormError :message="store.errors.name" />
			</FormGroup>

			<FormGroup>
				<FormLabel for="address">Adresse *</FormLabel>
				<FormInput id="address" v-model="form.address" />
				<FormError :message="store.errors.address" />
			</FormGroup>

			<div class="grid grid-cols-2 gap-24">
				<FormGroup>
					<FormLabel for="email">E-Mail *</FormLabel>
					<FormInput id="email" v-model="form.email" />
					<FormError :message="store.errors.email" />
				</FormGroup>
				<FormGroup>
					<FormLabel for="phone">Telefon *</FormLabel>
					<FormInput id="phone" v-model="form.phone" />
					<FormError :message="store.errors.phone" />
				</FormGroup>
			</div>

			<FormGroup>
				<FormLabel for="maps_url">Google Maps URL</FormLabel>
				<FormInput id="maps_url" v-model="form.maps_url" />
				<FormError :message="store.errors.maps_url" />
			</FormGroup>

			<FormGroup>
				<FormLabel>Impressum</FormLabel>
				<div class="mt-8">
					<Editor v-model="form.imprint" />
				</div>
				<FormError :message="store.errors.imprint" />
			</FormGroup>

			<FormGroup>
				<FormLabel for="meta_description">Meta Description</FormLabel>
				<FormInput id="meta_description" v-model="form.meta_description" />
				<FormError :message="store.errors.meta_description" />
			</FormGroup>
		</form>
	</div>
</template>
