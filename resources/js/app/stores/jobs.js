import { defineStore } from 'pinia'
import jobsApi from '@/api/jobs'

export const useJobStore = defineStore('jobs', {
	state: () => ({
		jobs: [],
		current: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchJobs() {
			this.loading = true
			try {
				const { data } = await jobsApi.index()
				this.jobs = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchJob(id) {
			this.loading = true
			try {
				const { data } = await jobsApi.show(id)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async saveJob(form, id = null, media = []) {
			this.errors = {}
			try {
				const payload = { ...form }
				if (media.length) {
					payload.media = media
				}
				if (id) {
					await jobsApi.update(id, payload)
				} else {
					await jobsApi.store(payload)
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
			const job = this.jobs.find(j => j.id === id)
			if (job) job.publish = !job.publish
			try {
				const { data } = await jobsApi.toggle(id)
				const idx = this.jobs.findIndex(j => j.id === id)
				if (idx !== -1) this.jobs[idx] = data.data
			} catch {
				if (job) job.publish = !job.publish
			}
		},

		async deleteJob(id) {
			await jobsApi.destroy(id)
			this.jobs = this.jobs.filter(j => j.id !== id)
		},
	},
})
