import { defineStore } from 'pinia'
import categoriesApi from '@/api/categories'

export const useCategoryStore = defineStore('categories', {
	state: () => ({
		categories: [],
		loading: false,
	}),

	actions: {
		async fetchCategories() {
			this.loading = true
			try {
				const { data } = await categoriesApi.index()
				this.categories = data.data
			} finally {
				this.loading = false
			}
		},

		async addCategory(name, publish = true) {
			const { data } = await categoriesApi.store({ name, publish })
			this.categories.push(data.data)
			return data.data
		},

		async updateCategory(id, payload) {
			const { data } = await categoriesApi.update(id, payload)
			const idx = this.categories.findIndex(c => c.id === id)
			if (idx !== -1) this.categories[idx] = data.data
		},

		async toggle(id) {
			const cat = this.categories.find(c => c.id === id)
			if (cat) cat.publish = !cat.publish
			try {
				const { data } = await categoriesApi.toggle(id)
				const idx = this.categories.findIndex(c => c.id === id)
				if (idx !== -1) this.categories[idx] = data.data
			} catch {
				if (cat) cat.publish = !cat.publish
			}
		},

		async deleteCategory(id) {
			await categoriesApi.destroy(id)
			this.categories = this.categories.filter(c => c.id !== id)
		},

		async addType(categoryId, payload) {
			const { data } = await categoriesApi.storeType(categoryId, payload)
			const cat = this.categories.find(c => c.id === categoryId)
			if (cat) cat.types.push(data.data)
			return data.data
		},

		async updateType(categoryId, typeId, payload) {
			const { data } = await categoriesApi.updateType(categoryId, typeId, payload)
			const cat = this.categories.find(c => c.id === categoryId)
			if (cat) {
				const idx = cat.types.findIndex(t => t.id === typeId)
				if (idx !== -1) cat.types[idx] = data.data
			}
		},

		async deleteType(categoryId, typeId) {
			await categoriesApi.destroyType(categoryId, typeId)
			const cat = this.categories.find(c => c.id === categoryId)
			if (cat) cat.types = cat.types.filter(t => t.id !== typeId)
		},

		async reorderTypes(categoryId, types) {
			const cat = this.categories.find(c => c.id === categoryId)
			if (cat) cat.types = types
			const items = types.map((t, i) => ({ id: t.id, sort_order: i }))
			await categoriesApi.reorderTypes(categoryId, items)
		},
	},
})
