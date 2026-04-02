import { defineStore } from 'pinia'
import gridsApi from '@/api/grids'

export const useGridStore = defineStore('grids', {
	state: () => ({
		grids: [],
		layouts: [],
		loading: false,
	}),

	actions: {
		setGrids(grids) {
			this.grids = grids || []
		},

		async fetchLayouts(projectId) {
			const { data } = await gridsApi.layouts(projectId)
			this.layouts = data.data
		},

		async fetchGrids(projectId) {
			this.loading = true
			try {
				const { data } = await gridsApi.index(projectId)
				this.grids = data.data
			} finally {
				this.loading = false
			}
		},

		async addGrid(projectId, layoutKey) {
			const { data } = await gridsApi.store(projectId, layoutKey)
			this.grids.push(data.data)
		},

		async removeGrid(projectId, gridId) {
			await gridsApi.destroy(projectId, gridId)
			this.grids = this.grids.filter(g => g.id !== gridId)
		},

		async reorderGrids(projectId, grids) {
			this.grids = grids
			const items = grids.map((g, index) => ({
				id: g.id,
				sort_order: index,
			}))
			await gridsApi.reorder(projectId, items)
		},

		async assignItem(projectId, gridId, { media_id, position }) {
			const { data } = await gridsApi.storeItem(projectId, gridId, { media_id, position })
			const grid = this.grids.find(g => g.id === gridId)
			if (grid) {
				// Remove existing item at this position
				grid.items = grid.items.filter(i => i.position !== position)
				grid.items.push(data.data)
				grid.items.sort((a, b) => a.position - b.position)
			}
		},

		async removeItem(projectId, gridId, itemId) {
			await gridsApi.destroyItem(projectId, gridId, itemId)
			const grid = this.grids.find(g => g.id === gridId)
			if (grid) {
				grid.items = grid.items.filter(i => i.id !== itemId)
			}
		},
	},
})
