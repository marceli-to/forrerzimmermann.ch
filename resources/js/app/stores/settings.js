import { defineStore } from 'pinia'
import settingsApi from '@/api/settings'

export const useSettingsStore = defineStore('settings', {
	state: () => ({
		settings: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchSettings() {
			this.loading = true
			try {
				const { data } = await settingsApi.show()
				this.settings = data.data
			} finally {
				this.loading = false
			}
		},

		async saveSettings(form) {
			this.errors = {}
			try {
				await settingsApi.update(form)
				return true
			} catch (error) {
				if (error.response?.status === 422) {
					this.errors = error.response.data.errors
				}
				return false
			}
		},
	},
})
