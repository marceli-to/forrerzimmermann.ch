import { createRouter, createWebHistory } from 'vue-router'

import Home from '@/views/Home.vue'
import ProjectIndex from '@/views/projects/Index.vue'
import ProjectForm from '@/views/projects/Form.vue'
import MediaIndex from '@/views/media/Index.vue'
import SettingsIndex from '@/views/settings/Index.vue'
import CategoryForm from '@/views/settings/CategoryForm.vue'
import AwardIndex from '@/views/awards/Index.vue'
import AwardForm from '@/views/awards/Form.vue'
import BookIndex from '@/views/books/Index.vue'
import BookForm from '@/views/books/Form.vue'
import ContentIndex from '@/views/content/Index.vue'
import ContentForm from '@/views/content/Form.vue'
import JobIndex from '@/views/jobs/Index.vue'
import JobForm from '@/views/jobs/Form.vue'
import LectureIndex from '@/views/lectures/Index.vue'
import LectureForm from '@/views/lectures/Form.vue'
import NewsIndex from '@/views/news/Index.vue'
import NewsForm from '@/views/news/Form.vue'
import PressIndex from '@/views/press/Index.vue'
import PressForm from '@/views/press/Form.vue'
import TeamIndex from '@/views/team/Index.vue'
import TeamForm from '@/views/team/Form.vue'

const routes = [
  {
    path: '/dashboard',
    name: 'home',
    component: Home,
    meta: { title: 'Dashboard' },
  },
  {
    path: '/dashboard/projects',
    name: 'projects.index',
    component: ProjectIndex,
    meta: { title: 'Projekte' },
  },
  {
    path: '/dashboard/projects/create',
    name: 'projects.create',
    component: ProjectForm,
    meta: { title: 'Neues Projekt' },
  },
  {
    path: '/dashboard/projects/:id/edit',
    name: 'projects.edit',
    component: ProjectForm,
    meta: { title: 'Projekt bearbeiten' },
  },
  {
    path: '/dashboard/media',
    name: 'media.index',
    component: MediaIndex,
    meta: { title: 'Media' },
  },
  {
    path: '/dashboard/settings',
    name: 'settings.index',
    component: SettingsIndex,
    meta: { title: 'Einstellungen' },
  },
  {
    path: '/dashboard/settings/categories/create',
    name: 'settings.categories.create',
    component: CategoryForm,
    meta: { title: 'Neue Kategorie' },
  },
  {
    path: '/dashboard/settings/categories/:id/edit',
    name: 'settings.categories.edit',
    component: CategoryForm,
    meta: { title: 'Kategorie bearbeiten' },
  },
  {
    path: '/dashboard/awards',
    name: 'awards.index',
    component: AwardIndex,
    meta: { title: 'Auszeichnungen' },
  },
  {
    path: '/dashboard/awards/create',
    name: 'awards.create',
    component: AwardForm,
    meta: { title: 'Neue Auszeichnung' },
  },
  {
    path: '/dashboard/awards/:id/edit',
    name: 'awards.edit',
    component: AwardForm,
    meta: { title: 'Auszeichnung bearbeiten' },
  },
  {
    path: '/dashboard/books',
    name: 'books.index',
    component: BookIndex,
    meta: { title: 'Bücher' },
  },
  {
    path: '/dashboard/books/create',
    name: 'books.create',
    component: BookForm,
    meta: { title: 'Neues Buch' },
  },
  {
    path: '/dashboard/books/:id/edit',
    name: 'books.edit',
    component: BookForm,
    meta: { title: 'Buch bearbeiten' },
  },
  {
    path: '/dashboard/content',
    name: 'content.index',
    component: ContentIndex,
    meta: { title: 'Inhalte' },
  },
  {
    path: '/dashboard/content/create',
    name: 'content.create',
    component: ContentForm,
    meta: { title: 'Neuer Inhalt' },
  },
  {
    path: '/dashboard/content/:id/edit',
    name: 'content.edit',
    component: ContentForm,
    meta: { title: 'Inhalt bearbeiten' },
  },
  {
    path: '/dashboard/jobs',
    name: 'jobs.index',
    component: JobIndex,
    meta: { title: 'Stellen' },
  },
  {
    path: '/dashboard/jobs/create',
    name: 'jobs.create',
    component: JobForm,
    meta: { title: 'Neue Stelle' },
  },
  {
    path: '/dashboard/jobs/:id/edit',
    name: 'jobs.edit',
    component: JobForm,
    meta: { title: 'Stelle bearbeiten' },
  },
  {
    path: '/dashboard/lectures',
    name: 'lectures.index',
    component: LectureIndex,
    meta: { title: 'Vorträge' },
  },
  {
    path: '/dashboard/lectures/create',
    name: 'lectures.create',
    component: LectureForm,
    meta: { title: 'Neuer Vortrag' },
  },
  {
    path: '/dashboard/lectures/:id/edit',
    name: 'lectures.edit',
    component: LectureForm,
    meta: { title: 'Vortrag bearbeiten' },
  },
  {
    path: '/dashboard/news',
    name: 'news.index',
    component: NewsIndex,
    meta: { title: 'News' },
  },
  {
    path: '/dashboard/news/create',
    name: 'news.create',
    component: NewsForm,
    meta: { title: 'Neue News' },
  },
  {
    path: '/dashboard/news/:id/edit',
    name: 'news.edit',
    component: NewsForm,
    meta: { title: 'News bearbeiten' },
  },
  {
    path: '/dashboard/press',
    name: 'press.index',
    component: PressIndex,
    meta: { title: 'Presse' },
  },
  {
    path: '/dashboard/press/create',
    name: 'press.create',
    component: PressForm,
    meta: { title: 'Neuer Presseeintrag' },
  },
  {
    path: '/dashboard/press/:id/edit',
    name: 'press.edit',
    component: PressForm,
    meta: { title: 'Presseeintrag bearbeiten' },
  },
  {
    path: '/dashboard/team',
    name: 'team.index',
    component: TeamIndex,
    meta: { title: 'Team' },
  },
  {
    path: '/dashboard/team/create',
    name: 'team.create',
    component: TeamForm,
    meta: { title: 'Neues Mitglied' },
  },
  {
    path: '/dashboard/team/:id/edit',
    name: 'team.edit',
    component: TeamForm,
    meta: { title: 'Mitglied bearbeiten' },
  },
]

const router = createRouter({
  history: createWebHistory(),
  routes,
})

router.afterEach((to) => {
  const appName = document.title.split('–').pop()?.trim() || 'CMS'
  document.title = to.meta.title
    ? `${to.meta.title} – ${appName}`
    : appName
})

export default router
