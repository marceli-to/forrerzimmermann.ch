import { defineStore } from 'pinia'
import pressApi from '@/api/press'

export const usePressStore = defineStore('press', {
	state: () => ({
		press: {},
		current: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchPress() {
			this.loading = true
			try {
				const { data } = await pressApi.index()
				this.press = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchPressItem(id) {
			this.loading = true
			try {
				const { data } = await pressApi.show(id)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async savePress(form, id = null, media = []) {
			this.errors = {}
			try {
				const payload = { ...form }
				if (media.length) {
					payload.media = media
				}
				if (id) {
					await pressApi.update(id, payload)
				} else {
					await pressApi.store(payload)
				}
				return true
			} catch (error) {
				if (error.response?.status === 422) {
					this.errors = error.response.data.errors
				}
				return false
			}
		},

		async toggle(id) {
			try {
				await pressApi.toggle(id)
				await this.fetchPress()
			} catch {}
		},

		async deletePress(id) {
			await pressApi.destroy(id)
			await this.fetchPress()
		},
	},
})
