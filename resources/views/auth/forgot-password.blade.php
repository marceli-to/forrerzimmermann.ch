<x-layout.guest>
	<div class="min-h-full flex">

		{{-- Left: Brand panel --}}
		<div class="hidden lg:flex lg:w-1/2 bg-neutral-900 relative overflow-hidden items-end p-16">
			<div class="relative z-10 text-white">
				<x-icons.logo class="w-48" />
			</div>
		</div>

		{{-- Right: Form --}}
		<div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
			<div class="w-full max-w-sm">

				<div class="lg:hidden mb-16 text-neutral-900">
					<x-icons.logo class="w-36" />
				</div>

				<h1 class="text-lg font-medium text-neutral-900 mb-1">Passwort zurücksetzen</h1>
				<p class="text-sm text-neutral-500 mb-10">Geben Sie Ihre E-Mail-Adresse ein und wir senden Ihnen einen Link zum Zurücksetzen.</p>

				@if (session('status'))
					<div class="mb-6 p-3 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200">
						{{ session('status') }}
					</div>
				@endif

				<form method="POST" action="{{ route('password.email') }}" class="space-y-6">
					@csrf

					<div>
						<x-form.label for="email">E-Mail</x-form.label>
						<x-form.input
							type="email"
							name="email"
							:value="old('email')"
							required
							autofocus
						/>
						<x-form.error name="email" />
					</div>

					<div class="flex items-center justify-between pt-4">
						<x-form.link :href="route('login')">Zurück zum Login</x-form.link>
						<x-form.button>Link senden</x-form.button>
					</div>
				</form>

			</div>
		</div>

	</div>
</x-layout.guest>
