<script setup>
import { watch } from 'vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Link from '@tiptap/extension-link'
import Toolbar from './Toolbar.vue'

const props = defineProps({
	modelValue: { type: String, default: '' },
})

const emit = defineEmits(['update:modelValue'])

const editor = useEditor({
	content: props.modelValue,
	extensions: [
		StarterKit.configure({
			heading: false,
			codeBlock: false,
			blockquote: false,
			code: false,
			horizontalRule: false,
			link: false,
		}),
		Link.configure({
			openOnClick: false,
		}),
	],
	onUpdate({ editor }) {
		emit('update:modelValue', editor.getHTML())
	},
})

watch(() => props.modelValue, (value) => {
	if (!editor.value) return
	if (editor.value.getHTML() === value) return
	editor.value.commands.setContent(value, false)
})
</script>

<template>
	<div class="editor">
		<Toolbar v-if="editor" :editor="editor" />
		<EditorContent :editor="editor" />
	</div>
</template>
