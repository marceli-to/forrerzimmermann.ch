import api from './axios'

export default {
	index: () => api.get('/content'),
	show: (id) => api.get(`/content/${id}`),
	store: (data) => api.post('/content', data),
	update: (id, data) => api.put(`/content/${id}`, data),
	toggle: (id) => api.patch(`/content/${id}/publish`),
}
