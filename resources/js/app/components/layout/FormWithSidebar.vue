<template>
	<div class="flex gap-32 items-start">
		<div class="flex-1 min-w-0">
			<slot />
		</div>
		<aside class="w-280 shrink-0 border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 p-24 sticky top-24">
			<slot name="sidebar" />
		</aside>
	</div>
</template>
