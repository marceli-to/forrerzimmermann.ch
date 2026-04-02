import api from './axios'

export default {
	show: () => api.get('/settings'),
	update: (data) => api.put('/settings', data),
}
