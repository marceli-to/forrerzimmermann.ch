<script setup>
import { ref, watch } from 'vue'
import { PhX } from '@phosphor-icons/vue'

const props = defineProps({
	open: { type: Boolean, default: false },
	title: { type: String, default: null },
	size: { type: String, default: 'sm' }, // sm, md, lg
})

const emit = defineEmits(['close'])

const visible = ref(false)
const mounted = ref(false)

const sizes = {
	sm: 'max-w-[420px]',
	md: 'max-w-[560px]',
	lg: 'max-w-[720px]',
}

watch(() => props.open, (val) => {
	if (val) {
		mounted.value = true
		requestAnimationFrame(() => {
			visible.value = true
		})
	} else {
		visible.value = false
		setTimeout(() => {
			mounted.value = false
		}, 200)
	}
}, { immediate: true })

function close() {
	visible.value = false
	setTimeout(() => emit('close'), 200)
}
</script>

<template>
	<Teleport to="body">
		<div v-if="mounted" class="fixed inset-0 z-50">
			<!-- Backdrop -->
			<Transition name="fade">
				<div
					v-if="visible"
					class="absolute inset-0 bg-black/40"
					@click="close"
				/>
			</Transition>

			<!-- Panel -->
			<div
				class="absolute top-0 right-0 bottom-0 w-full bg-white shadow-xl transition-transform duration-200 ease-out flex flex-col"
				:class="[sizes[size], visible ? 'translate-x-0' : 'translate-x-full']"
			>
				<!-- Header -->
				<div class="flex items-center justify-between px-24 py-20 border-b border-neutral-200">
					<slot name="header">
						<h3 class="text-sm font-semibold text-neutral-900">{{ title }}</h3>
					</slot>
					<button
						type="button"
						class="size-28 flex items-center justify-center text-neutral-400 hover:text-neutral-900 transition-colors"
						@click="close"
					>
						<PhX :size="16" weight="light" />
					</button>
				</div>

				<!-- Content -->
				<div class="flex-1 overflow-y-auto">
					<slot />
				</div>

				<!-- Footer -->
				<div v-if="$slots.footer" class="px-24 py-16 border-t border-neutral-200">
					<slot name="footer" />
				</div>
			</div>
		</div>
	</Teleport>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
	transition: opacity 0.2s ease;
}
.fade-enter-from,
.fade-leave-to {
	opacity: 0;
}
</style>
