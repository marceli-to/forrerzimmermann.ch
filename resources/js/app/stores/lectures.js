import { defineStore } from 'pinia'
import lecturesApi from '@/api/lectures'

export const useLectureStore = defineStore('lectures', {
	state: () => ({
		lectures: {},
		current: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchLectures() {
			this.loading = true
			try {
				const { data } = await lecturesApi.index()
				this.lectures = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchLecture(id) {
			this.loading = true
			try {
				const { data } = await lecturesApi.show(id)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async saveLecture(form, id = null, media = []) {
			this.errors = {}
			try {
				const payload = { ...form }
				if (media.length) {
					payload.media = media
				}
				if (id) {
					await lecturesApi.update(id, payload)
				} else {
					await lecturesApi.store(payload)
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
				await lecturesApi.toggle(id)
				await this.fetchLectures()
			} catch {}
		},

		async deleteLecture(id) {
			await lecturesApi.destroy(id)
			await this.fetchLectures()
		},
	},
})
