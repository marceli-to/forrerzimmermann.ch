<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
	PhArrowRight,
	PhCircle,
} from '@phosphor-icons/vue'
import api from '@/api/axios'
import PageHeader from '@/components/layout/PageHeader.vue'
import MediaCard from '@/components/media/MediaCard.vue'

const router = useRouter()
const data = ref(null)
const loading = ref(true)

onMounted(async () => {
	try {
		const response = await api.get('/')
		data.value = response.data
	} finally {
		loading.value = false
	}
})

function formatSize(bytes) {
	if (!bytes) return '0 B'
	if (bytes < 1024) return bytes + ' B'
	if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB'
	if (bytes < 1024 * 1024 * 1024) return (bytes / (1024 * 1024)).toFixed(1) + ' MB'
	return (bytes / (1024 * 1024 * 1024)).toFixed(2) + ' GB'
}

function timeAgo(dateStr) {
	const now = new Date()
	const date = new Date(dateStr)
	const diff = Math.floor((now - date) / 1000)

	if (diff < 60) return 'gerade eben'
	if (diff < 3600) return `vor ${Math.floor(diff / 60)} Min.`
	if (diff < 86400) return `vor ${Math.floor(diff / 3600)} Std.`
	if (diff < 604800) return `vor ${Math.floor(diff / 86400)} Tagen`
	return date.toLocaleDateString('de-CH')
}
</script>

<template>
	<div>
		<PageHeader title="Dashboard" />

		<div v-if="loading" class="text-sm text-neutral-400">Laden...</div>

		<template v-else-if="data">

			<!-- Stats -->
			<div data-uidotsh-pick="Stat cards" class="contents">

				<!-- Option 1: Current bordered cards -->
				<div data-uidotsh-option="Bordered cards (current)" class="contents">
					<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-32">
						<div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 p-20 hover:border-neutral-400 dark:hover:border-neutral-600 transition-colors">
							<div class="mb-12">
								<span class="text-xxs font-medium text-neutral-400 dark:text-neutral-500 uppercase tracking-[0.15em] truncate">Projekte</span>
							</div>
							<div class="text-2xl font-light text-neutral-900 dark:text-white tracking-tight tabular-nums">{{ data.stats.projects_total }}</div>
							<div class="text-xxs text-neutral-400 dark:text-neutral-500 mt-4">
								{{ data.stats.projects_published }} publiziert · {{ data.stats.projects_draft }} Entwürfe
							</div>
						</div>

						<div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 p-20 hover:border-neutral-400 dark:hover:border-neutral-600 transition-colors">
							<div class="mb-12">
								<span class="text-xxs font-medium text-neutral-400 dark:text-neutral-500 uppercase tracking-[0.15em] truncate">Publiziert</span>
							</div>
							<div class="text-2xl font-light text-emerald-600 tracking-tight tabular-nums">{{ data.stats.projects_published }}</div>
							<div class="text-xxs text-neutral-400 dark:text-neutral-500 mt-4">
								{{ data.stats.projects_total > 0 ? Math.round(data.stats.projects_published / data.stats.projects_total * 100) : 0 }}% aller Projekte
							</div>
						</div>

						<div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 p-20 hover:border-neutral-400 dark:hover:border-neutral-600 transition-colors">
							<div class="mb-12">
								<span class="text-xxs font-medium text-neutral-400 dark:text-neutral-500 uppercase tracking-[0.15em] truncate">Medien</span>
							</div>
							<div class="text-2xl font-light text-neutral-900 dark:text-white tracking-tight tabular-nums">{{ data.stats.media_total }}</div>
							<div class="text-xxs text-neutral-400 dark:text-neutral-500 mt-4">Bilder in der Bibliothek</div>
						</div>

						<div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 p-20 hover:border-neutral-400 dark:hover:border-neutral-600 transition-colors">
							<div class="mb-12">
								<span class="text-xxs font-medium text-neutral-400 dark:text-neutral-500 uppercase tracking-[0.15em] truncate">Speicher</span>
							</div>
							<div class="text-2xl font-light text-neutral-900 dark:text-white tracking-tight tabular-nums">{{ formatSize(data.stats.media_size) }}</div>
							<div class="text-xxs text-neutral-400 dark:text-neutral-500 mt-4">Gesamtgrösse aller Medien</div>
						</div>
					</div>
				</div>

				<!-- Option 2: Warm divider stats -->
				<div data-uidotsh-option="Warm divider stats" class="contents" hidden>
					<div class="grid grid-cols-2 lg:grid-cols-4 mb-32">
						<div class="p-20 pr-20">
							<div class="mb-12">
								<span class="text-xxs font-medium text-warm-400 uppercase tracking-[0.15em] truncate">Projekte</span>
							</div>
							<div class="text-2xl font-light text-warm-900 tracking-tight tabular-nums">{{ data.stats.projects_total }}</div>
							<div class="text-xxs text-warm-400 mt-4">
								{{ data.stats.projects_published }} publiziert · {{ data.stats.projects_draft }} Entwürfe
							</div>
						</div>

						<div class="p-20 border-l border-warm-900/8 [&:nth-child(2n)]:max-lg:border-l-0 max-lg:[&:nth-child(n+3)]:border-t max-lg:[&:nth-child(n+3)]:border-warm-900/8 lg:border-l lg:border-warm-900/8">
							<div class="mb-12">
								<span class="text-xxs font-medium text-warm-400 uppercase tracking-[0.15em] truncate">Publiziert</span>
							</div>
							<div class="text-2xl font-light text-navy tracking-tight tabular-nums">{{ data.stats.projects_published }}</div>
							<div class="text-xxs text-warm-400 mt-4">
								{{ data.stats.projects_total > 0 ? Math.round(data.stats.projects_published / data.stats.projects_total * 100) : 0 }}% aller Projekte
							</div>
						</div>

						<div class="p-20 max-lg:border-t max-lg:border-warm-900/8 lg:border-l lg:border-warm-900/8">
							<div class="mb-12">
								<span class="text-xxs font-medium text-warm-400 uppercase tracking-[0.15em] truncate">Medien</span>
							</div>
							<div class="text-2xl font-light text-warm-900 tracking-tight tabular-nums">{{ data.stats.media_total }}</div>
							<div class="text-xxs text-warm-400 mt-4">Bilder in der Bibliothek</div>
						</div>

						<div class="p-20 max-lg:border-t max-lg:border-warm-900/8 lg:border-l lg:border-warm-900/8">
							<div class="mb-12">
								<span class="text-xxs font-medium text-warm-400 uppercase tracking-[0.15em] truncate">Speicher</span>
							</div>
							<div class="text-2xl font-light text-warm-900 tracking-tight tabular-nums">{{ formatSize(data.stats.media_size) }}</div>
							<div class="text-xxs text-warm-400 mt-4">Gesamtgrösse aller Medien</div>
						</div>
					</div>
				</div>

				<!-- Option 3: Warm well stats -->
				<div data-uidotsh-option="Warm well stats" class="contents" hidden>
					<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-32">
						<div class="bg-warm-100 p-20">
							<div class="mb-12">
								<span class="text-xxs font-medium text-warm-500 uppercase tracking-[0.15em] truncate">Projekte</span>
							</div>
							<div class="text-2xl font-light text-warm-900 tracking-tight tabular-nums">{{ data.stats.projects_total }}</div>
							<div class="text-xxs text-warm-400 mt-4">
								{{ data.stats.projects_published }} publiziert · {{ data.stats.projects_draft }} Entwürfe
							</div>
						</div>

						<div class="bg-warm-100 p-20">
							<div class="mb-12">
								<span class="text-xxs font-medium text-warm-500 uppercase tracking-[0.15em] truncate">Publiziert</span>
							</div>
							<div class="text-2xl font-light text-navy tracking-tight tabular-nums">{{ data.stats.projects_published }}</div>
							<div class="text-xxs text-warm-400 mt-4">
								{{ data.stats.projects_total > 0 ? Math.round(data.stats.projects_published / data.stats.projects_total * 100) : 0 }}% aller Projekte
							</div>
						</div>

						<div class="bg-warm-100 p-20">
							<div class="mb-12">
								<span class="text-xxs font-medium text-warm-500 uppercase tracking-[0.15em] truncate">Medien</span>
							</div>
							<div class="text-2xl font-light text-warm-900 tracking-tight tabular-nums">{{ data.stats.media_total }}</div>
							<div class="text-xxs text-warm-400 mt-4">Bilder in der Bibliothek</div>
						</div>

						<div class="bg-warm-100 p-20">
							<div class="mb-12">
								<span class="text-xxs font-medium text-warm-500 uppercase tracking-[0.15em] truncate">Speicher</span>
							</div>
							<div class="text-2xl font-light text-warm-900 tracking-tight tabular-nums">{{ formatSize(data.stats.media_size) }}</div>
							<div class="text-xxs text-warm-400 mt-4">Gesamtgrösse aller Medien</div>
						</div>
					</div>
				</div>

			</div>

			<!-- Two columns: Recent projects + Recent media -->
			<div data-uidotsh-pick="Content sections" class="contents">

				<!-- Option 1: Current bordered sections -->
				<div data-uidotsh-option="Bordered sections (current)" class="contents">
					<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
						<div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800">
							<div class="flex items-center justify-between px-20 py-16 border-b border-neutral-100 dark:border-neutral-800">
								<h2 class="text-xs font-medium text-neutral-900 dark:text-white uppercase">Letzte Projekte</h2>
								<button
									class="text-xxs text-neutral-400 hover:text-neutral-900 dark:hover:text-white flex items-center gap-4 transition-colors cursor-pointer"
									@click="router.push({ name: 'projects.index' })"
								>
									Alle anzeigen
									<PhArrowRight :size="10" />
								</button>
							</div>
							<div v-if="data.recent_projects.length === 0" class="px-20 py-24 text-sm text-neutral-400">
								Noch keine Projekte.
							</div>
							<div v-else>
								<div
									v-for="(project, index) in data.recent_projects"
									:key="project.id"
									class="flex items-center gap-12 px-20 py-12 hover:bg-neutral-50 dark:hover:bg-neutral-800 transition-colors cursor-pointer"
									:class="{ 'border-t border-neutral-100 dark:border-neutral-800': index > 0 }"
									@click="router.push({ name: 'projects.edit', params: { id: project.id } })"
								>
									<PhCircle
										:size="8"
										weight="fill"
										:class="project.publish ? 'text-emerald-500' : 'text-neutral-300 dark:text-neutral-600'"
									/>
									<div class="flex-1 min-w-0">
										<div class="text-sm text-neutral-900 dark:text-neutral-100 truncate">{{ project.title }}</div>
									</div>
									<div class="text-xxs text-neutral-400 whitespace-nowrap tabular-nums">{{ timeAgo(project.updated_at) }}</div>
								</div>
							</div>
						</div>

						<div class="bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800">
							<div class="flex items-center justify-between px-20 py-16 border-b border-neutral-100 dark:border-neutral-800">
								<h2 class="text-xs font-medium text-neutral-900 dark:text-white uppercase">Letzte Uploads</h2>
								<button
									class="text-xxs text-neutral-400 hover:text-neutral-900 dark:hover:text-white flex items-center gap-4 transition-colors cursor-pointer"
									@click="router.push({ name: 'media.index' })"
								>
									Alle anzeigen
									<PhArrowRight :size="10" />
								</button>
							</div>
							<div v-if="data.recent_media.length === 0" class="px-20 py-24 text-sm text-neutral-400">
								Noch keine Medien.
							</div>
							<div v-else class="p-12">
								<div class="grid grid-cols-4 gap-6">
									<MediaCard
										v-for="media in data.recent_media"
										:key="media.uuid"
										:media="media"
										:showInfo="true"
										:showOverlay="false"
										class="cursor-pointer"
										@click="router.push({ name: 'media.index' })"
									/>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Option 2: Warm minimal sections -->
				<div data-uidotsh-option="Warm minimal sections" class="contents" hidden>
					<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
						<div>
							<div class="flex items-center justify-between pb-16 border-b border-warm-900/8">
								<h2 class="text-xs font-medium text-warm-900 uppercase tracking-[0.08em]">Letzte Projekte</h2>
								<button
									class="text-xxs text-warm-400 hover:text-warm-900 flex items-center gap-4 transition-colors cursor-pointer"
									@click="router.push({ name: 'projects.index' })"
								>
									Alle anzeigen
									<PhArrowRight :size="10" />
								</button>
							</div>
							<div v-if="data.recent_projects.length === 0" class="py-24 text-sm text-warm-400">
								Noch keine Projekte.
							</div>
							<div v-else>
								<div
									v-for="(project, index) in data.recent_projects"
									:key="project.id"
									class="flex items-center gap-12 py-12 hover:bg-warm-100 px-12 -mx-12 transition-colors cursor-pointer"
									:class="{ 'border-t border-warm-900/5': index > 0 }"
									@click="router.push({ name: 'projects.edit', params: { id: project.id } })"
								>
									<PhCircle
										:size="8"
										weight="fill"
										:class="project.publish ? 'text-navy' : 'text-warm-300'"
									/>
									<div class="flex-1 min-w-0">
										<div class="text-sm text-warm-900 truncate">{{ project.title }}</div>
									</div>
									<div class="text-xxs text-warm-400 whitespace-nowrap tabular-nums">{{ timeAgo(project.updated_at) }}</div>
								</div>
							</div>
						</div>

						<div>
							<div class="flex items-center justify-between pb-16 border-b border-warm-900/8">
								<h2 class="text-xs font-medium text-warm-900 uppercase tracking-[0.08em]">Letzte Uploads</h2>
								<button
									class="text-xxs text-warm-400 hover:text-warm-900 flex items-center gap-4 transition-colors cursor-pointer"
									@click="router.push({ name: 'media.index' })"
								>
									Alle anzeigen
									<PhArrowRight :size="10" />
								</button>
							</div>
							<div v-if="data.recent_media.length === 0" class="py-24 text-sm text-warm-400">
								Noch keine Medien.
							</div>
							<div v-else class="pt-12">
								<div class="grid grid-cols-4 gap-6">
									<MediaCard
										v-for="media in data.recent_media"
										:key="media.uuid"
										:media="media"
										:showInfo="true"
										:showOverlay="false"
										class="cursor-pointer"
										@click="router.push({ name: 'media.index' })"
									/>
								</div>
							</div>
						</div>
					</div>
				</div>

				<!-- Option 3: Warm well sections -->
				<div data-uidotsh-option="Warm well sections" class="contents" hidden>
					<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
						<div class="bg-warm-100 p-20">
							<div class="flex items-center justify-between pb-16 border-b border-warm-900/8">
								<h2 class="text-xs font-medium text-warm-800 uppercase tracking-[0.08em]">Letzte Projekte</h2>
								<button
									class="text-xxs text-warm-400 hover:text-warm-800 flex items-center gap-4 transition-colors cursor-pointer"
									@click="router.push({ name: 'projects.index' })"
								>
									Alle anzeigen
									<PhArrowRight :size="10" />
								</button>
							</div>
							<div v-if="data.recent_projects.length === 0" class="py-24 text-sm text-warm-400">
								Noch keine Projekte.
							</div>
							<div v-else>
								<div
									v-for="(project, index) in data.recent_projects"
									:key="project.id"
									class="flex items-center gap-12 py-12 transition-colors cursor-pointer"
									:class="{ 'border-t border-warm-900/5': index > 0 }"
									@click="router.push({ name: 'projects.edit', params: { id: project.id } })"
								>
									<PhCircle
										:size="8"
										weight="fill"
										:class="project.publish ? 'text-navy' : 'text-warm-300'"
									/>
									<div class="flex-1 min-w-0">
										<div class="text-sm text-warm-800 truncate">{{ project.title }}</div>
									</div>
									<div class="text-xxs text-warm-400 whitespace-nowrap tabular-nums">{{ timeAgo(project.updated_at) }}</div>
								</div>
							</div>
						</div>

						<div class="bg-warm-100 p-20">
							<div class="flex items-center justify-between pb-16 border-b border-warm-900/8">
								<h2 class="text-xs font-medium text-warm-800 uppercase tracking-[0.08em]">Letzte Uploads</h2>
								<button
									class="text-xxs text-warm-400 hover:text-warm-800 flex items-center gap-4 transition-colors cursor-pointer"
									@click="router.push({ name: 'media.index' })"
								>
									Alle anzeigen
									<PhArrowRight :size="10" />
								</button>
							</div>
							<div v-if="data.recent_media.length === 0" class="py-24 text-sm text-warm-400">
								Noch keine Medien.
							</div>
							<div v-else class="pt-12">
								<div class="grid grid-cols-4 gap-6">
									<MediaCard
										v-for="media in data.recent_media"
										:key="media.uuid"
										:media="media"
										:showInfo="true"
										:showOverlay="false"
										class="cursor-pointer"
										@click="router.push({ name: 'media.index' })"
									/>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>

		</template>
	</div>
</template>
