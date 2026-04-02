import { defineStore } from 'pinia'
import contentApi from '@/api/content'

export const useContentStore = defineStore('content', {
	state: () => ({
		items: [],
		current: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchItems() {
			this.loading = true
			try {
				const { data } = await contentApi.index()
				this.items = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchItem(id) {
			this.loading = true
			try {
				const { data } = await contentApi.show(id)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async saveItem(form, id = null, media = []) {
			this.errors = {}
			try {
				const payload = { ...form }
				if (media.length) {
					payload.media = media
				}
				if (id) {
					await contentApi.update(id, payload)
				} else {
					await contentApi.store(payload)
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
			const item = this.items.find(i => i.id === id)
			if (item) item.publish = !item.publish
			try {
				const { data } = await contentApi.toggle(id)
				const idx = this.items.findIndex(i => i.id === id)
				if (idx !== -1) this.items[idx] = data.data
			} catch {
				if (item) item.publish = !item.publish
			}
		},
	},
})
