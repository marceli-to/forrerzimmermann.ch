import api from './axios'

export default {
	index: () => api.get('/press'),
	show: (id) => api.get(`/press/${id}`),
	store: (data) => api.post('/press', data),
	update: (id, data) => api.put(`/press/${id}`, data),
	toggle: (id) => api.patch(`/press/${id}/publish`),
	destroy: (id) => api.delete(`/press/${id}`),
}
