<script setup>
import AppSidebar from '@/components/layout/AppSidebar.vue'
import ToastContainer from '@/components/ui/toast/Container.vue'
import ConfirmDialog from '@/components/ui/dialog/ConfirmDialog.vue'
import { useConfirm } from '@/composables/useConfirm'

const { state: confirmState, onConfirm, onCancel } = useConfirm()
</script>

<template>
	<div class="min-h-dvh bg-gray-100 flex">
		<AppSidebar variant="navy" />
		<div class="ml-240 flex-1 p-16">
			<main class="min-h-[calc(100dvh-2rem)] bg-white rounded-2xl pt-24 px-32 pb-32 relative">
				<ToastContainer />
				<slot />
			</main>
		</div>
		<ConfirmDialog
			:open="confirmState.open"
			:title="confirmState.title"
			:message="confirmState.message"
			:confirm-label="confirmState.confirmLabel"
			:cancel-label="confirmState.cancelLabel"
			:destructive="confirmState.destructive"
			@confirm="onConfirm"
			@cancel="onCancel"
		/>
	</div>
</template>
