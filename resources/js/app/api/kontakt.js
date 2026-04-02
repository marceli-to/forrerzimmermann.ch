import api from './axios'

export default {
	show: () => api.get('/kontakt'),
	update: (data) => api.put('/kontakt', data),
}
