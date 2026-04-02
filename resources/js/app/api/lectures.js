import api from './axios'

export default {
	index: () => api.get('/lectures'),
	show: (id) => api.get(`/lectures/${id}`),
	store: (data) => api.post('/lectures', data),
	update: (id, data) => api.put(`/lectures/${id}`, data),
	toggle: (id) => api.patch(`/lectures/${id}/publish`),
	destroy: (id) => api.delete(`/lectures/${id}`),
}
