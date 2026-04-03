<template>
	<div class="flex gap-32 items-start">
		<div class="flex-1 min-w-0">
			<slot />
		</div>
		<aside class="w-280 shrink-0 border border-neutral-200 bg-white p-24 sticky top-24">
			<slot name="sidebar" />
		</aside>
	</div>
</template>
