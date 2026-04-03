<script setup>
import { ref, nextTick } from 'vue'
import { PhTextB, PhListBullets, PhListNumbers, PhLink, PhX } from '@phosphor-icons/vue'

const props = defineProps({
	editor: { type: Object, required: true },
})

const showLinkInput = ref(false)
const linkUrl = ref('')
const linkInput = ref(null)

function openLinkInput() {
	linkUrl.value = props.editor.getAttributes('link').href || ''
	showLinkInput.value = true
	nextTick(() => linkInput.value?.focus())
}

function applyLink() {
	const url = linkUrl.value.trim()
	if (url) {
		props.editor.chain().focus().extendMarkRange('link').setLink({ href: url }).run()
	} else {
		props.editor.chain().focus().extendMarkRange('link').unsetLink().run()
	}
	closeLinkInput()
}

function removeLink() {
	props.editor.chain().focus().extendMarkRange('link').unsetLink().run()
	closeLinkInput()
}

function closeLinkInput() {
	showLinkInput.value = false
	linkUrl.value = ''
	props.editor.commands.focus()
}
</script>

<template>
	<div class="relative border-b border-gray-200 dark:border-warm-700 bg-gray-50 dark:bg-warm-800">

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
				@click="openLinkInput">
				<PhLink :size="16" weight="light" />
			</button>
		</div>

		<!-- Link input overlay -->
		<div
			v-if="showLinkInput"
			class="absolute left-0 right-0 top-full z-10 flex items-center gap-8 border border-gray-200 dark:border-warm-700 bg-white dark:bg-warm-900 p-8">

			<input
				ref="linkInput"
				v-model="linkUrl"
				type="url"
				placeholder="https://..."
				class="flex-1 px-0 py-6 text-xs bg-transparent text-gray-900 dark:text-warm-100 dark:placeholder:text-warm-600 border-0 border-b border-gray-200 dark:border-warm-700 focus:border-gray-900 dark:focus:border-warm-400 focus:ring-0 outline-none"
				@keydown.enter.prevent="applyLink"
				@keydown.escape.prevent="closeLinkInput" />

			<button
				type="button"
				class="bg-gray-900 dark:bg-warm-100 text-white dark:text-warm-900 text-[11px] font-medium px-12 py-6 rounded-md"
				@click="applyLink">
				Übernehmen
			</button>

			<button
				v-if="editor.isActive('link')"
				type="button"
				class="border border-gray-200 dark:border-warm-700 text-gray-700 dark:text-warm-300 text-[11px] font-medium px-12 py-6 rounded-md hover:border-gray-900 dark:hover:border-warm-400"
				@click="removeLink">
				Entfernen
			</button>

			<button
				type="button"
				class="text-gray-400 dark:text-warm-500 hover:text-gray-900 dark:hover:text-warm-100 p-4 transition-colors"
				title="Abbrechen"
				@click="closeLinkInput">
				<PhX :size="14" weight="light" />
			</button>
		</div>

	</div>
</template>
