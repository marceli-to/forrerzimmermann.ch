import api from './axios'

export default {
	index: () => api.get('/news'),
	show: (id) => api.get(`/news/${id}`),
	store: (data) => api.post('/news', data),
	update: (id, data) => api.put(`/news/${id}`, data),
	destroy: (id) => api.delete(`/news/${id}`),
}
