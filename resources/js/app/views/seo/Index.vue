<script setup>
import { ref, onMounted } from 'vue'
import { useSeoStore } from '@/stores/seo'
import { useMediaStore } from '@/stores/media'
import { useToast } from '@/composables/useToast'
import MediaUploader from '@/components/media/MediaUploader.vue'
import MediaGrid from '@/components/media/MediaGrid.vue'
import MediaEdit from '@/components/media/MediaEdit.vue'
import PageHeader from '@/components/layout/PageHeader.vue'
import FormActions from '@/components/ui/form/FormActions.vue'
import FormLabel from '@/components/ui/form/FormLabel.vue'
import FormInput from '@/components/ui/form/FormInput.vue'
import FormTextarea from '@/components/ui/form/FormTextarea.vue'
import FormError from '@/components/ui/form/FormError.vue'
import FormGroup from '@/components/ui/form/FormGroup.vue'

const store = useSeoStore()
const mediaStore = useMediaStore()
const toast = useToast()
const editingMedia = ref(null)

const form = ref({
    og_title: '',
    og_description: '',
    landing_meta_description: '',
    projects_meta_description: '',
    werkliste_meta_description: '',
    profile_meta_description: '',
    team_meta_description: '',
    jobs_meta_description: '',
    contact_meta_description: '',
})

onMounted(async () => {
    mediaStore.setItems([])
    await store.fetchSeo()
    if (store.seo) {
        const s = store.seo
        form.value = {
            og_title: s.og_title || '',
            og_description: s.og_description || '',
            landing_meta_description: s.landing_meta_description || '',
            projects_meta_description: s.projects_meta_description || '',
            werkliste_meta_description: s.werkliste_meta_description || '',
            profile_meta_description: s.profile_meta_description || '',
            team_meta_description: s.team_meta_description || '',
            jobs_meta_description: s.jobs_meta_description || '',
            contact_meta_description: s.contact_meta_description || '',
        }
        mediaStore.setItems(s.media || [])
    }
})

async function handleSubmit() {
    const tempMedia = mediaStore.tempItems.map(item => ({
        uuid: item.uuid,
        file: item.file,
        original_name: item.original_name,
        mime_type: item.mime_type,
        size: item.size,
        width: item.width,
        height: item.height,
        alt: item.alt || null,
        caption: item.caption || null,
    }))

    const payload = { ...form.value }
    if (tempMedia.length) {
        payload.media = tempMedia
    }

    const success = await store.saveSeo(payload)
    if (success) {
        toast.success('SEO-Einstellungen aktualisiert')
    } else if (Object.keys(store.errors).length) {
        toast.error('Bitte überprüfen Sie das Formular')
    }
}

function onUploaded(media) { mediaStore.addItem(media) }
function onEditMedia(media) { editingMedia.value = media }
async function onSaveMedia({ uuid, data }) {
    const success = await mediaStore.updateItem(uuid, data)
    if (success) editingMedia.value = null
}
async function onDeleteMedia(media) { await mediaStore.deleteItem(media.uuid) }
function onReorderMedia(items) { mediaStore.reorder(items) }
</script>

<template>
    <div>
        <PageHeader title="SEO" />

        <div v-if="store.loading" class="text-sm text-gray-400 dark:text-warm-500">
            Laden...
        </div>

        <form v-else class="flex flex-col gap-24" @submit.prevent="handleSubmit">

            <FormGroup>
                <FormLabel for="og_title">OG Titel</FormLabel>
                <FormInput id="og_title" v-model="form.og_title" />
                <FormError :message="store.errors.og_title" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="og_description">OG Beschreibung</FormLabel>
                <FormTextarea id="og_description" v-model="form.og_description" />
                <FormError :message="store.errors.og_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel>OG Image</FormLabel>
                <div class="mt-8 flex flex-col gap-16">
                    <MediaUploader v-if="!mediaStore.items.length" @uploaded="onUploaded" />
                    <MediaGrid
                        v-if="mediaStore.items.length"
                        :items="mediaStore.items"
                        @edit="onEditMedia"
                        @delete="onDeleteMedia"
                        @reorder="onReorderMedia"
                    />
                </div>
            </FormGroup>

            <FormGroup>
                <FormLabel for="landing_meta_description">Meta Description – Startseite</FormLabel>
                <FormTextarea id="landing_meta_description" v-model="form.landing_meta_description" />
                <FormError :message="store.errors.landing_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="projects_meta_description">Meta Description – Projekte</FormLabel>
                <FormTextarea id="projects_meta_description" v-model="form.projects_meta_description" />
                <FormError :message="store.errors.projects_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="werkliste_meta_description">Meta Description – Werkliste</FormLabel>
                <FormTextarea id="werkliste_meta_description" v-model="form.werkliste_meta_description" />
                <FormError :message="store.errors.werkliste_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="profile_meta_description">Meta Description – Profil</FormLabel>
                <FormTextarea id="profile_meta_description" v-model="form.profile_meta_description" />
                <FormError :message="store.errors.profile_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="team_meta_description">Meta Description – Team</FormLabel>
                <FormTextarea id="team_meta_description" v-model="form.team_meta_description" />
                <FormError :message="store.errors.team_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="jobs_meta_description">Meta Description – Stellen</FormLabel>
                <FormTextarea id="jobs_meta_description" v-model="form.jobs_meta_description" />
                <FormError :message="store.errors.jobs_meta_description" />
            </FormGroup>

            <FormGroup>
                <FormLabel for="contact_meta_description">Meta Description – Kontakt</FormLabel>
                <FormTextarea id="contact_meta_description" v-model="form.contact_meta_description" />
                <FormError :message="store.errors.contact_meta_description" />
            </FormGroup>

            <FormActions submitLabel="Speichern" />
        </form>

        <MediaEdit
            :media="editingMedia"
            @close="editingMedia = null"
            @save="onSaveMedia"
        />
    </div>
</template>
