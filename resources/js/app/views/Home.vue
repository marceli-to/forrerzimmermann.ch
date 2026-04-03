<script setup>
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import {
	PhBuildings,
	PhImage,
	PhUsers,
	PhBriefcase,
} from '@phosphor-icons/vue'
import api from '@/api/axios'

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


const quickLinks = [
	{ label: 'Projekte', to: '/dashboard/projects', icon: PhBuildings },
	{ label: 'Medien', to: '/dashboard/media', icon: PhImage },
	{ label: 'Team', to: '/dashboard/team', icon: PhUsers },
	{ label: 'Stellen', to: '/dashboard/jobs', icon: PhBriefcase },
]
</script>

<template>
	<div>
		<div v-if="loading" class="text-sm text-gray-400">Laden...</div>

		<template v-else-if="data">

			<!-- Greeting -->
			<div class="mb-40">
				<p class="text-xs text-gray-400 uppercase tracking-[0.15em] mb-8">Willkommen</p>
				<h1 class="text-2xl font-light text-gray-900 tracking-tight">Hallo, {{ data.user }}</h1>
			</div>

			<!-- Quick-nav tiles -->
			<div class="grid grid-cols-2 lg:grid-cols-4 gap-6 mb-40">
				<button
					v-for="link in quickLinks"
					:key="link.to"
					class="group flex flex-col items-start gap-16 border border-neutral-200 rounded-xl p-20 hover:border-neutral-400 transition-colors cursor-pointer text-left"
					@click="router.push(link.to)"
				>
					<component :is="link.icon" :size="20" weight="light" class="text-gray-400 group-hover:text-gray-900 transition-colors" />
					<span class="text-sm text-gray-900">{{ link.label }}</span>
				</button>
			</div>


		</template>
	</div>
</template>
