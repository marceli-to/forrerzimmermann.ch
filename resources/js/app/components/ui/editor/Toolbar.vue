<script setup>
import { ref, computed, nextTick } from 'vue'
import { PhTextB, PhListBullets, PhListNumbers, PhLink } from '@phosphor-icons/vue'
import AppDialog from '../dialog/AppDialog.vue'
import FormGroup from '../form/FormGroup.vue'
import FormLabel from '../form/FormLabel.vue'
import FormInput from '../form/FormInput.vue'
import FormSelect from '../form/FormSelect.vue'
import FormCheckbox from '../form/FormCheckbox.vue'
import FormButton from '../form/FormButton.vue'

const props = defineProps({
	editor: { type: Object, required: true },
})

const showDialog = ref(false)
const linkType = ref('url')
const linkProtocol = ref('https://')
const linkUrl = ref('')
const linkTitle = ref('')
const linkTarget = ref(false)

const typeOptions = [
	{ value: 'url', label: 'URL' },
	{ value: 'email', label: 'E-Mail' },
	{ value: 'tel', label: 'Telefon' },
]

const protocolOptions = [
	{ value: 'https://', label: 'https://' },
	{ value: 'http://', label: 'http://' },
]

const isEditing = computed(() => props.editor.isActive('link'))

const placeholder = computed(() => {
	if (linkType.value === 'email') return 'info@example.com'
	if (linkType.value === 'tel') return '+41 00 000 00 00'
	return 'www.example.com'
})

function openDialog() {
	const attrs = props.editor.getAttributes('link')

	if (attrs.href) {
		const href = attrs.href

		if (href.startsWith('mailto:')) {
			linkType.value = 'email'
			linkUrl.value = href.replace('mailto:', '')
			linkProtocol.value = 'https://'
		} else if (href.startsWith('tel:')) {
			linkType.value = 'tel'
			linkUrl.value = href.replace('tel:', '')
			linkProtocol.value = 'https://'
		} else {
			linkType.value = 'url'
			if (href.startsWith('http://')) {
				linkProtocol.value = 'http://'
				linkUrl.value = href.replace('http://', '')
			} else {
				linkProtocol.value = 'https://'
				linkUrl.value = href.replace('https://', '')
			}
		}

		linkTitle.value = attrs.title || ''
		linkTarget.value = attrs.target === '_blank'
	} else {
		linkType.value = 'url'
		linkProtocol.value = 'https://'
		linkUrl.value = ''
		linkTitle.value = ''
		linkTarget.value = false
	}

	showDialog.value = true
}

function buildHref() {
	const value = linkUrl.value.trim()
	if (!value) return ''

	if (linkType.value === 'email') return `mailto:${value}`
	if (linkType.value === 'tel') return `tel:${value}`
	return `${linkProtocol.value}${value}`
}

function applyLink() {
	const href = buildHref()
	if (!href) return

	const linkAttrs = { href }

	if (linkTitle.value.trim()) {
		linkAttrs.title = linkTitle.value.trim()
	} else {
		linkAttrs.title = null
	}

	if (linkType.value === 'url' && linkTarget.value) {
		linkAttrs.target = '_blank'
	} else {
		linkAttrs.target = null
	}

	props.editor.chain().focus().extendMarkRange('link').setLink(linkAttrs).run()
	closeDialog()
}

function removeLink() {
	props.editor.chain().focus().extendMarkRange('link').unsetLink().run()
	closeDialog()
}

function closeDialog() {
	showDialog.value = false
	props.editor.commands.focus()
}
</script>

<template>
	<div class="border-b border-gray-200 dark:border-warm-700 bg-gray-50 dark:bg-warm-800">

		<div class="flex items-center gap-2">

			<button
				type="button"
				class="p-8 rounded transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
				:class="editor.isActive('bold') ? 'text-gray-900 dark:text-warm-100 bg-gray-200 dark:bg-warm-700' : 'text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100'"
				title="Bold"
				@click="editor.chain().focus().toggleBold().run()">
				<PhTextB :size="16" weight="light" />
			</button>

			<button
				type="button"
				class="p-8 rounded transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
				:class="editor.isActive('bulletList') ? 'text-gray-900 dark:text-warm-100 bg-gray-200 dark:bg-warm-700' : 'text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100'"
				title="Liste"
				@click="editor.chain().focus().toggleBulletList().run()">
				<PhListBullets :size="16" weight="light" />
			</button>

			<button
				type="button"
				class="p-8 rounded transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
				:class="editor.isActive('orderedList') ? 'text-gray-900 dark:text-warm-100 bg-gray-200 dark:bg-warm-700' : 'text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100'"
				title="Nummerierte Liste"
				@click="editor.chain().focus().toggleOrderedList().run()">
				<PhListNumbers :size="16" weight="light" />
			</button>

			<button
				type="button"
				class="p-8 rounded transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-gray-200 dark:focus-visible:ring-warm-700"
				:class="editor.isActive('link') ? 'text-gray-900 dark:text-warm-100 bg-gray-200 dark:bg-warm-700' : 'text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100'"
				title="Link"
				@click="openDialog">
				<PhLink :size="16" weight="light" />
			</button>
		</div>

		<AppDialog :open="showDialog" title="Link" size="md" @close="closeDialog">

			<div class="flex flex-col gap-16">

				<FormGroup>
					<FormLabel>Typ</FormLabel>
					<FormSelect v-model="linkType" :options="typeOptions" placeholder="Typ wählen" />
				</FormGroup>

				<FormGroup>
					<FormLabel>{{ linkType === 'email' ? 'E-Mail-Adresse' : linkType === 'tel' ? 'Telefonnummer' : 'URL' }}</FormLabel>
					<div v-if="linkType === 'url'" class="flex gap-8">
						<div class="w-110 shrink-0">
							<FormSelect v-model="linkProtocol" :options="protocolOptions" placeholder="" />
						</div>
						<FormInput v-model="linkUrl" :placeholder="placeholder" />
					</div>
					<FormInput v-else v-model="linkUrl" :placeholder="placeholder" />
				</FormGroup>

				<FormGroup>
					<FormLabel>Titel (optional)</FormLabel>
					<FormInput v-model="linkTitle" placeholder="Titel" />
				</FormGroup>

				<FormCheckbox v-if="linkType === 'url'" v-model="linkTarget">
					In neuem Fenster öffnen
				</FormCheckbox>

			</div>

			<template #footer>
				<div class="flex items-center gap-8">
					<FormButton variant="primary" @click="applyLink">Übernehmen</FormButton>
					<FormButton v-if="isEditing" variant="secondary" @click="removeLink">Entfernen</FormButton>
					<FormButton variant="secondary" @click="closeDialog">Abbrechen</FormButton>
				</div>
			</template>

		</AppDialog>

	</div>
</template>
