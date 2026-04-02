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
	<div class="relative border-b border-neutral-200 dark:border-neutral-700 bg-neutral-50 dark:bg-neutral-800">

		<div class="flex items-center gap-2">

			<button
				type="button"
				class="p-8 transition-colors"
				:class="editor.isActive('bold') ? 'text-neutral-900 dark:text-white bg-neutral-200 dark:bg-neutral-700' : 'text-neutral-400 hover:text-neutral-900 dark:hover:text-white'"
				title="Bold"
				@click="editor.chain().focus().toggleBold().run()">
				<PhTextB :size="16" weight="light" />
			</button>

			<button
				type="button"
				class="p-8 transition-colors"
				:class="editor.isActive('bulletList') ? 'text-neutral-900 dark:text-white bg-neutral-200 dark:bg-neutral-700' : 'text-neutral-400 hover:text-neutral-900 dark:hover:text-white'"
				title="Liste"
				@click="editor.chain().focus().toggleBulletList().run()">
				<PhListBullets :size="16" weight="light" />
			</button>

			<button
				type="button"
				class="p-8 transition-colors"
				:class="editor.isActive('orderedList') ? 'text-neutral-900 dark:text-white bg-neutral-200 dark:bg-neutral-700' : 'text-neutral-400 hover:text-neutral-900 dark:hover:text-white'"
				title="Nummerierte Liste"
				@click="editor.chain().focus().toggleOrderedList().run()">
				<PhListNumbers :size="16" weight="light" />
			</button>

			<button
				type="button"
				class="p-8 transition-colors"
				:class="editor.isActive('link') ? 'text-neutral-900 dark:text-white bg-neutral-200 dark:bg-neutral-700' : 'text-neutral-400 hover:text-neutral-900 dark:hover:text-white'"
				title="Link"
				@click="openLinkInput">
				<PhLink :size="16" weight="light" />
			</button>
		</div>

		<!-- Link input overlay -->
		<div
			v-if="showLinkInput"
			class="absolute left-0 right-0 top-full z-10 flex items-center gap-8 border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 p-8">

			<input
				ref="linkInput"
				v-model="linkUrl"
				type="url"
				placeholder="https://..."
				class="flex-1 px-0 py-6 text-xs bg-transparent text-neutral-900 dark:text-neutral-100 border-0 border-b border-neutral-300 dark:border-neutral-600 focus:border-neutral-900 dark:focus:border-neutral-400 focus:ring-0 outline-none"
				@keydown.enter.prevent="applyLink"
				@keydown.escape.prevent="closeLinkInput" />

			<button
				type="button"
				class="bg-neutral-900 dark:bg-white text-white dark:text-neutral-900 text-[11px] font-medium tracking-wide px-12 py-6"
				@click="applyLink">
				Übernehmen
			</button>

			<button
				v-if="editor.isActive('link')"
				type="button"
				class="border border-neutral-300 dark:border-neutral-600 text-neutral-900 dark:text-neutral-100 text-[11px] font-medium tracking-wide px-12 py-6 hover:border-neutral-900 dark:hover:border-neutral-400"
				@click="removeLink">
				Entfernen
			</button>

			<button
				type="button"
				class="text-neutral-400 hover:text-neutral-900 dark:hover:text-white p-4 transition-colors"
				title="Abbrechen"
				@click="closeLinkInput">
				<PhX :size="14" weight="light" />
			</button>
		</div>

	</div>
</template>
