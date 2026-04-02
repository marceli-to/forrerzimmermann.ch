import api from './axios'

export default {
	index: () => api.get('/books'),
	show: (id) => api.get(`/books/${id}`),
	store: (data) => api.post('/books', data),
	update: (id, data) => api.put(`/books/${id}`, data),
	toggle: (id) => api.patch(`/books/${id}/publish`),
	destroy: (id) => api.delete(`/books/${id}`),
	reorder: (items) => api.patch('/books/reorder', { items }),
}
