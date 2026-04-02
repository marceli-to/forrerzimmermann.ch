import { defineStore } from 'pinia'
import projectsApi from '@/api/projects'

export const useProjectStore = defineStore('projects', {
	state: () => ({
		projects: [],
		current: null,
		categories: [],
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchProjects() {
			this.loading = true
			try {
				const { data } = await projectsApi.index()
				this.projects = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchProject(id) {
			this.loading = true
			try {
				const { data } = await projectsApi.show(id)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchCategories() {
			const { data } = await projectsApi.categories()
			this.categories = data.data
		},

		async saveProject(form, id = null, media = []) {
			this.errors = {}
			try {
				const payload = { ...form }
				if (media.length) {
					payload.media = media
				}
				if (id) {
					await projectsApi.update(id, payload)
				} else {
					await projectsApi.store(payload)
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
			const project = this.projects.find(p => p.id === id)
			if (project) project.publish = !project.publish
			try {
				const { data } = await projectsApi.toggle(id)
				const idx = this.projects.findIndex(p => p.id === id)
				if (idx !== -1) this.projects[idx] = data.data
			} catch {
				if (project) project.publish = !project.publish
			}
		},

		async deleteProject(id) {
			await projectsApi.destroy(id)
			this.projects = this.projects.filter(p => p.id !== id)
		},
	},
})
