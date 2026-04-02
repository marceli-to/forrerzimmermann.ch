import { defineStore } from 'pinia'
import booksApi from '@/api/books'

export const useBookStore = defineStore('books', {
	state: () => ({
		books: [],
		current: null,
		loading: false,
		errors: {},
	}),

	actions: {
		async fetchBooks() {
			this.loading = true
			try {
				const { data } = await booksApi.index()
				this.books = data.data
			} finally {
				this.loading = false
			}
		},

		async fetchBook(id) {
			this.loading = true
			try {
				const { data } = await booksApi.show(id)
				this.current = data.data
			} finally {
				this.loading = false
			}
		},

		async saveBook(form, id = null, media = []) {
			this.errors = {}
			try {
				const payload = { ...form }
				if (media.length) {
					payload.media = media
				}
				if (id) {
					await booksApi.update(id, payload)
				} else {
					await booksApi.store(payload)
				}
				return true
			} catch (error) {
				if (error.response?.status === 422) {
					this.errors = error.response.data.errors
				}
				return false
			}
		},

		async toggle(id) {
			const book = this.books.find(b => b.id === id)
			if (book) book.publish = !book.publish
			try {
				const { data } = await booksApi.toggle(id)
				const idx = this.books.findIndex(b => b.id === id)
				if (idx !== -1) this.books[idx] = data.data
			} catch {
				if (book) book.publish = !book.publish
			}
		},

		async deleteBook(id) {
			await booksApi.destroy(id)
			this.books = this.books.filter(b => b.id !== id)
		},
	},
})
