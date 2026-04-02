import api from './axios'

export default {
	index: () => api.get('/projects'),
	show: (id) => api.get(`/projects/${id}`),
	store: (data) => api.post('/projects', data),
	update: (id, data) => api.put(`/projects/${id}`, data),
	toggle: (id) => api.patch(`/projects/${id}/publish`),
	destroy: (id) => api.delete(`/projects/${id}`),
	categories: () => api.get('/projects/categories'),
}
