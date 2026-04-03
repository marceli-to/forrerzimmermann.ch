<script setup>
import draggable from 'vuedraggable'

const props = defineProps({
	columns: { type: Array, required: true },
	rows: { type: Array, required: true },
	draggableRows: { type: Boolean, default: false },
	clickableRows: { type: Boolean, default: false },
})

const model = defineModel()
</script>

<template>
	<div class="-mx-4 -my-2 overflow-x-auto whitespace-nowrap sm:-mx-6 lg:-mx-8">
		<div class="inline-block min-w-full px-4 py-2 align-middle sm:px-6 lg:px-8">
			<table class="w-full text-sm">
				<thead>
					<tr class="text-left border-b border-gray-900/10 dark:border-warm-700/50">
						<th
							v-for="col in columns"
							:key="col.key"
							class="py-12 text-xs font-medium text-gray-400 dark:text-warm-500 whitespace-nowrap"
							:class="[
								col.class || '',
								col.align === 'right' ? 'text-right' : '',
							]"
						>
							{{ col.label }}
						</th>
					</tr>
				</thead>
				<draggable
					v-if="draggableRows"
					v-model="model"
					tag="tbody"
					item-key="id"
					ghost-class="opacity-30"
					animation="150"
				>
					<template #item="{ element: row, index }">
						<tr class="border-b border-gray-900/6 dark:border-warm-700/40 hover:bg-gray-50 dark:hover:bg-warm-800 cursor-move">
							<td
								v-for="col in columns"
								:key="col.key"
								class="py-16 first:pl-4 last:pr-4"
								:class="[
									col.class || '',
									col.align === 'right' ? 'text-right' : '',
									col.primary ? 'text-gray-900 dark:text-warm-100' : 'text-gray-400 dark:text-warm-500 text-sm',
									row.publish === false && col.key !== 'actions' ? 'opacity-40' : '',
								]"
							>
								<slot :name="'cell-' + col.key" :row="row" :value="row[col.key]">
									{{ typeof row[col.key] === 'string' || typeof row[col.key] === 'number' ? row[col.key] : '' }}
								</slot>
							</td>
						</tr>
					</template>
				</draggable>
				<tbody v-else>
					<tr
						v-for="(row, index) in rows"
						:key="row.id ?? index"
						class="border-b border-gray-900/6 dark:border-warm-700/40 hover:bg-gray-50 dark:hover:bg-warm-800"
						:class="clickableRows ? 'cursor-pointer' : ''"
					>
						<td
							v-for="col in columns"
							:key="col.key"
							class="py-16 first:pl-4 last:pr-4"
							:class="[
								col.class || '',
								col.align === 'right' ? 'text-right' : '',
								col.primary ? 'text-gray-900 dark:text-warm-100' : 'text-gray-400 dark:text-warm-500 text-sm',
								row.publish === false && col.key !== 'actions' ? 'opacity-40' : '',
							]"
						>
							<slot :name="'cell-' + col.key" :row="row" :value="row[col.key]">
								{{ typeof row[col.key] === 'string' || typeof row[col.key] === 'number' ? row[col.key] : '' }}
							</slot>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</template>
