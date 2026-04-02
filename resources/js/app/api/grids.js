import api from './axios'

export default {
	layouts: (projectId) => api.get(`/projects/${projectId}/grids/layouts`),
	index: (projectId) => api.get(`/projects/${projectId}/grids`),
	store: (projectId, layoutKey) => api.post(`/projects/${projectId}/grids`, { layout_key: layoutKey }),
	destroy: (projectId, gridId) => api.delete(`/projects/${projectId}/grids/${gridId}`),
	reorder: (projectId, items) => api.patch(`/projects/${projectId}/grids/reorder`, { items }),
	storeItem: (projectId, gridId, data) => api.post(`/projects/${projectId}/grids/${gridId}/items`, data),
	destroyItem: (projectId, gridId, itemId) => api.delete(`/projects/${projectId}/grids/${gridId}/items/${itemId}`),
}
