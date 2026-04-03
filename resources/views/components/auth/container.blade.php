<x-layout.guest>
	<div class="min-h-dvh flex">
		<div class="hidden lg:flex lg:w-1/2 bg-gray-100 items-end p-48">
			<div class="text-gray-900">
				<x-icons.logo class="w-192" />
			</div>
		</div>
		<div class="w-full lg:w-1/2 bg-white flex items-center justify-center px-32 py-48">
			<div class="w-full max-w-sm">
				<div class="lg:hidden mb-32 text-gray-900">
					<x-icons.logo class="w-120" />
				</div>
				{{ $slot }}
			</div>
		</div>
	</div>
</x-layout.guest>
