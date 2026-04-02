import api from './axios'

export default {
	index: () => api.get('/categories'),
	store: (data) => api.post('/categories', data),
	update: (id, data) => api.put(`/categories/${id}`, data),
	toggle: (id) => api.patch(`/categories/${id}/publish`),
	destroy: (id) => api.delete(`/categories/${id}`),
	storeType: (categoryId, data) => api.post(`/categories/${categoryId}/types`, data),
	updateType: (categoryId, typeId, data) => api.put(`/categories/${categoryId}/types/${typeId}`, data),
	destroyType: (categoryId, typeId) => api.delete(`/categories/${categoryId}/types/${typeId}`),
	reorderTypes: (categoryId, items) => api.patch(`/categories/${categoryId}/types/reorder`, { items }),
}
