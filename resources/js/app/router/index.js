import { createRouter, createWebHistory } from 'vue-router'

import Home from '@/views/Home.vue'
import ProjectIndex from '@/views/projects/Index.vue'
import ProjectForm from '@/views/projects/Form.vue'
import MediaIndex from '@/views/media/Index.vue'
import SettingsIndex from '@/views/settings/Index.vue'
import JobIndex from '@/views/jobs/Index.vue'
import JobForm from '@/views/jobs/Form.vue'
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
