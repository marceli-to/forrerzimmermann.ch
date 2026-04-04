<script setup>
import { ref, watch, computed } from 'vue'
import { Cropper } from 'vue-advanced-cropper'
import 'vue-advanced-cropper/dist/style.css'

const props = defineProps({
  media: { type: Object, default: null },
})

const emit = defineEmits(['close', 'save'])

const isOpen = ref(false)
const cropperRef = ref(null)
const aspectRatio = ref(null)

const aspectOptions = [
  { label: 'Frei', value: null },
  { label: '16:9', value: 16 / 9 },
  { label: '4:3', value: 4 / 3 },
  { label: '1:1', value: 1 },
]

watch(() => props.media, (val) => {
  isOpen.value = !!val
  aspectRatio.value = null
}, { immediate: true })

function close() {
  isOpen.value = false
  emit('close')
}

function handleSave() {
  if (!cropperRef.value) return
  const { coordinates } = cropperRef.value.getResult()
  emit('save', {
    uuid: props.media.uuid,
    crop: {
      x: Math.round(coordinates.left),
      y: Math.round(coordinates.top),
      w: Math.round(coordinates.width),
      h: Math.round(coordinates.height),
    },
  })
  isOpen.value = false
}

function handleClear() {
  emit('save', {
    uuid: props.media.uuid,
    crop: { x: null, y: null, w: null, h: null },
  })
  isOpen.value = false
}

const defaultPosition = computed(() => {
  if (!props.media?.crop) return undefined
  return {
    left: props.media.crop.x,
    top: props.media.crop.y,
  }
})

const defaultSize = computed(() => {
  if (!props.media?.crop) return undefined
  return {
    width: props.media.crop.w,
    height: props.media.crop.h,
  }
})
</script>

<template>
  <Teleport to="body">
    <div
      v-if="isOpen"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/70"
      @click.self="close"
    >
      <div class="bg-white dark:bg-warm-900 rounded-lg shadow-xl w-full max-w-3xl mx-16 flex flex-col max-h-[90vh]">
        <!-- Header -->
        <div class="flex items-center justify-between px-24 py-16 border-b border-gray-200 dark:border-warm-700">
          <h2 class="text-sm font-medium text-gray-900 dark:text-warm-100">Bild zuschneiden</h2>
          <button type="button" class="text-gray-400 hover:text-gray-600 dark:text-warm-500 dark:hover:text-warm-300 cursor-pointer" @click="close">
            &times;
          </button>
        </div>

        <!-- Aspect ratio controls -->
        <div class="flex gap-8 px-24 py-12 border-b border-gray-100 dark:border-warm-800">
          <button
            v-for="opt in aspectOptions"
            :key="opt.label"
            type="button"
            class="text-xs px-10 py-4 rounded border transition-colors cursor-pointer"
            :class="aspectRatio === opt.value
              ? 'bg-gray-900 text-white border-gray-900 dark:bg-warm-100 dark:text-warm-900 dark:border-warm-100'
              : 'border-gray-300 text-gray-600 hover:border-gray-500 dark:border-warm-600 dark:text-warm-400'"
            @click="aspectRatio = opt.value"
          >
            {{ opt.label }}
          </button>
        </div>

        <!-- Cropper -->
        <div class="flex-1 overflow-hidden bg-neutral-900 p-16 min-h-0">
          <Cropper
            v-if="media"
            ref="cropperRef"
            :src="media.original_url"
            :stencil-props="{ aspectRatio }"
            :default-position="defaultPosition"
            :default-size="defaultSize"
            class="max-h-[50vh]"
          />
        </div>

        <!-- Footer -->
        <div class="flex items-center justify-between px-24 py-16 border-t border-gray-200 dark:border-warm-700">
          <button
            type="button"
            class="text-sm text-gray-400 dark:text-warm-500 hover:text-gray-600 dark:hover:text-warm-300 transition-colors cursor-pointer"
            @click="handleClear"
          >
            Zuschnitt entfernen
          </button>
          <div class="flex gap-12">
            <button
              type="button"
              class="text-sm inline-flex items-center justify-center rounded-md px-16 py-8 bg-gray-900 dark:bg-warm-100 text-white dark:text-warm-900 hover:bg-gray-800 dark:hover:bg-warm-200 transition-colors cursor-pointer"
              @click="handleSave"
            >
              Speichern
            </button>
            <button
              type="button"
              class="text-sm inline-flex items-center justify-center rounded-md px-16 py-8 bg-gray-100 dark:bg-warm-800 text-gray-700 dark:text-warm-300 hover:bg-gray-200 dark:hover:bg-warm-700 transition-colors cursor-pointer"
              @click="close"
            >
              Abbrechen
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>
