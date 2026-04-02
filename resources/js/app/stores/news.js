import { defineStore } from 'pinia'
import newsApi from '@/api/news'

export const useNewsStore = defineStore('news', {
	state: () => ({
		news: [],
		current: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchNews() {
			this.loading = true
			try {
				const { data } = await newsApi.index()
				this.news = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchNewsItem(id) {
			this.loading = true
			try {
				const { data } = await newsApi.show(id)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async saveNews(form, id = null, media = []) {
			this.errors = {}
			try {
				const payload = { ...form }
				if (media.length) {
					payload.media = media
				}
				if (id) {
					await newsApi.update(id, payload)
				} else {
					await newsApi.store(payload)
				}
				return true
			} catch (error) {
				if (error.response?.status === 422) {
					this.errors = error.response.data.errors
				}
				return false
			}
		},

		async deleteNews(id) {
			await newsApi.destroy(id)
			this.news = this.news.filter(n => n.id !== id)
		},
	},
})
