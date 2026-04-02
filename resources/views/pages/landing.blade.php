<x-layout.guest>
	<div class="min-h-full flex flex-col">

		{{-- Hero --}}
		<div class="flex-1 flex flex-col items-center justify-center px-6 py-24 relative overflow-hidden">

			{{-- Grid pattern background --}}
			<div class="absolute inset-0 opacity-[0.04]"
				style="background-image: linear-gradient(rgba(0,0,0,.5) 1px, transparent 1px), linear-gradient(90deg, rgba(0,0,0,.5) 1px, transparent 1px); background-size: 60px 60px;">
			</div>

			<div class="relative z-10 text-center max-w-xl">
				<div class="text-neutral-400 text-xs tracking-[0.3em] uppercase mb-6">Content Management</div>
				<h1 class="text-5xl sm:text-6xl font-light tracking-tight text-neutral-900 mb-6">
					{{ config('app.name', 'CMS') }}
				</h1>
				<p class="text-base text-neutral-500 leading-relaxed mb-12">
					Inhalte verwalten, Medien organisieren, Projekte pflegen — alles an einem Ort.
				</p>
				<a href="{{ route('login') }}"
					class="inline-flex items-center justify-center px-10 py-3.5 bg-neutral-900 text-white text-sm font-medium tracking-wide hover:bg-neutral-800 active:bg-neutral-950 transition-all duration-200">
					Anmelden
				</a>
			</div>

		</div>

		{{-- Footer --}}
		<footer class="px-6 py-6 text-center">
			<p class="text-xs text-neutral-400 tracking-wide">&copy; {{ date('Y') }} {{ config('app.name', 'CMS') }}</p>
		</footer>

	</div>
</x-layout.guest>
