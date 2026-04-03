import { createRouter, createWebHistory } from 'vue-router'

import LandingIndex from '@/views/landing/Index.vue'
import LandingForm from '@/views/landing/Form.vue'
import ProjectIndex from '@/views/projects/Index.vue'
import ProjectForm from '@/views/projects/Form.vue'
import TopicIndex from '@/views/topics/Index.vue'
import TopicForm from '@/views/topics/Form.vue'
import AtelierIndex from '@/views/atelier/Index.vue'
import AtelierForm from '@/views/atelier/Form.vue'
import TeamIndex from '@/views/team/Index.vue'
import TeamForm from '@/views/team/Form.vue'
import JobIndex from '@/views/jobs/Index.vue'
import JobForm from '@/views/jobs/Form.vue'
import KontaktForm from '@/views/kontakt/Form.vue'
import MediaIndex from '@/views/media/Index.vue'
import SeoForm from '@/views/seo/Index.vue'

const routes = [
  {
    path: '/dashboard',
    redirect: { name: 'landing.index' },
  },
  {
    path: '/dashboard/landing',
    name: 'landing.index',
    component: LandingIndex,
    meta: { title: 'Startseite' },
  },
  {
    path: '/dashboard/landing/create',
    name: 'landing.create',
    component: LandingForm,
    meta: { title: 'Neuer Slide' },
  },
  {
    path: '/dashboard/landing/:id/edit',
    name: 'landing.edit',
    component: LandingForm,
    meta: { title: 'Slide bearbeiten' },
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
    path: '/dashboard/topics',
    name: 'topics.index',
    component: TopicIndex,
    meta: { title: 'Themen' },
  },
  {
    path: '/dashboard/topics/create',
    name: 'topics.create',
    component: TopicForm,
    meta: { title: 'Neues Thema' },
  },
  {
    path: '/dashboard/topics/:id/edit',
    name: 'topics.edit',
    component: TopicForm,
    meta: { title: 'Thema bearbeiten' },
  },
  {
    path: '/dashboard/atelier',
    name: 'atelier.index',
    component: AtelierIndex,
    meta: { title: 'Atelier' },
  },
  {
    path: '/dashboard/atelier/:id/edit',
    name: 'atelier.edit',
    component: AtelierForm,
    meta: { title: 'Atelier bearbeiten' },
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
    path: '/dashboard/kontakt',
    name: 'kontakt.edit',
    component: KontaktForm,
    meta: { title: 'Kontakt' },
  },
  {
    path: '/dashboard/media',
    name: 'media.index',
    component: MediaIndex,
    meta: { title: 'Media' },
  },
  {
    path: '/dashboard/seo',
    name: 'seo.edit',
    component: SeoForm,
    meta: { title: 'SEO' },
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
