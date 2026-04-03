<x-layout.guest>
	<div class="min-h-dvh flex items-center justify-center p-16">
		<div class="w-full max-w-sm">

			{{-- Logo --}}
			<div class="mb-10 text-white">
				<x-icons.logo class="w-148" />
			</div>

			{{-- Form panel --}}
			<div class="bg-warm-50 rounded-xl p-32">

				<h1 class="text-lg font-medium text-warm-900 mb-1">Passwort zurücksetzen</h1>
				<p class="text-sm text-warm-400 mb-24">Geben Sie Ihre E-Mail-Adresse ein und wir senden Ihnen einen Link zum Zurücksetzen.</p>

				@if (session('status'))
					<div class="mb-16 p-12 text-sm text-emerald-700 bg-emerald-50 rounded-md border border-emerald-200">
						{{ session('status') }}
					</div>
				@endif

				<form method="POST" action="{{ route('password.email') }}" class="space-y-20">
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

					<div class="flex items-center justify-between">
						<x-form.link :href="route('login')">Zurück zum Login</x-form.link>
						<x-form.button>Link senden</x-form.button>
					</div>
				</form>

			</div>
		</div>
	</div>
</x-layout.guest>
