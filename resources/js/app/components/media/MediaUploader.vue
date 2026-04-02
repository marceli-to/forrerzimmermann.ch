<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { PhPlus, PhUploadSimple } from '@phosphor-icons/vue'
import Uppy from '@uppy/core'
import XHRUpload from '@uppy/xhr-upload'
import German from '@uppy/locales/lib/de_DE'

const props = defineProps({
	compact: { type: Boolean, default: false },
	accept: { type: String, default: 'image/*' },
	maxFiles: { type: Number, default: null },
})

const fileTypes = {
	'image/*': {
		extensions: ['.jpg', '.jpeg', '.png', '.webp', '.gif'],
		label: 'Bilder hinzufügen',
		hint: 'JPG, PNG, WebP, GIF — max. 50 MB',
	},
	'video/*': {
		extensions: ['.mp4', '.mov', '.webm'],
		label: 'Videos hinzufügen',
		hint: 'MP4, MOV, WebM — max. 50 MB',
	},
}

const activeType = fileTypes[props.accept] || fileTypes['image/*']

const emit = defineEmits(['uploaded'])

const fileInput = ref(null)
const isDragging = ref(false)
const uploading = ref(false)
const progress = ref(0)
let uppy = null

onMounted(() => {
	const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content

	uppy = new Uppy({
		locale: German,
		autoProceed: true,
		restrictions: {
			allowedFileTypes: activeType.extensions,
			maxFileSize: 51200 * 1024,
			maxNumberOfFiles: props.maxFiles,
		},
	})

	uppy.use(XHRUpload, {
		endpoint: '/api/dashboard/media/upload',
		fieldName: 'file',
		headers: {
			'X-CSRF-TOKEN': csrfToken,
			'Accept': 'application/json',
			'X-Requested-With': 'XMLHttpRequest',
		},
	})

	uppy.on('upload', () => {
		uploading.value = true
		progress.value = 0
	})

	uppy.on('progress', (value) => {
		progress.value = value
	})

	uppy.on('upload-success', (file, response) => {
		emit('uploaded', response.body.data)
		uppy.removeFile(file.id)
	})

	uppy.on('complete', () => {
		uploading.value = false
		progress.value = 0
	})
})

onBeforeUnmount(() => {
	if (uppy) uppy.destroy()
})

function onDrop(e) {
	isDragging.value = false
	const files = e.dataTransfer?.files
	if (files) addFiles(files)
}

function onFileSelect(e) {
	const files = e.target?.files
	if (files) addFiles(files)
	if (fileInput.value) fileInput.value.value = ''
}

function addFiles(fileList) {
	for (const file of fileList) {
		try {
			uppy.addFile({ name: file.name, type: file.type, data: file })
		} catch (err) {}
	}
}
</script>

<template>
	<div>
		<!-- Compact: slim add bar -->
		<div
			v-if="compact"
			class="border border-neutral-200 dark:border-neutral-800 transition-colors duration-150 cursor-pointer hover:border-neutral-400 dark:hover:border-neutral-600 bg-white dark:bg-neutral-900"
			:class="isDragging ? '!border-neutral-900 dark:!border-neutral-400 !bg-neutral-50 dark:!bg-neutral-800' : ''"
			@click="fileInput?.click()"
			@dragover.prevent="isDragging = true"
			@dragleave.prevent="isDragging = false"
			@drop.prevent="onDrop"
		>
			<div class="flex items-center justify-center gap-8 py-24">
				<PhPlus :size="14" weight="light" class="text-neutral-400" />
				<span class="text-xs text-neutral-500">{{ activeType.label }}</span>
				<span class="text-xxs text-neutral-400 ml-4">{{ activeType.hint }}</span>
			</div>
		</div>

		<!-- Full: drop zone -->
		<div
			v-else
			class="border border-neutral-200 dark:border-neutral-800 transition-colors duration-150 cursor-pointer hover:border-neutral-400 dark:hover:border-neutral-600 bg-white dark:bg-neutral-900"
			:class="isDragging ? '!border-neutral-900 dark:!border-neutral-400 !bg-neutral-50 dark:!bg-neutral-800' : ''"
			@click="fileInput?.click()"
			@dragover.prevent="isDragging = true"
			@dragleave.prevent="isDragging = false"
			@drop.prevent="onDrop"
		>
			<div class="flex flex-col items-center justify-center py-[8rem] px-24">
				<PhUploadSimple :size="24" weight="light" class="text-neutral-400 mb-12" />
				<p class="text-xs text-neutral-500">
					<span class="text-neutral-900 dark:text-neutral-100 underline decoration-neutral-300 dark:decoration-neutral-600 underline-offset-4">Dateien auswählen</span>
					oder hierhin ziehen
				</p>
				<p class="text-xxs text-neutral-400 mt-6">{{ activeType.hint }}</p>
			</div>
		</div>

		<!-- Progress -->
		<div v-if="uploading" class="mt-6">
			<div class="h-2 bg-neutral-200 dark:bg-neutral-800 overflow-hidden">
				<div class="h-full bg-neutral-900 dark:bg-neutral-100 transition-all duration-300" :style="{ width: progress + '%' }" />
			</div>
		</div>

		<input
			ref="fileInput"
			type="file"
			:multiple="!maxFiles || maxFiles > 1"
			:accept="activeType.extensions.join(',')"
			class="hidden"
			@change="onFileSelect"
		/>
	</div>
</template>
