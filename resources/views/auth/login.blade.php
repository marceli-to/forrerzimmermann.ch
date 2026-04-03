<x-layout.guest>
	<div class="min-h-dvh flex items-center justify-center p-16">
		<div class="w-full max-w-sm">

			{{-- Logo --}}
			<div class="mb-10 text-white">
				<x-icons.logo class="w-148" />
			</div>

			{{-- Form panel --}}
			<div class="bg-warm-50 rounded-xl p-32">

				<h1 class="text-lg font-medium text-warm-900 mb-1">Anmelden</h1>
				<p class="text-sm text-warm-400 mb-24">Melden Sie sich mit Ihrem Konto an.</p>

				@if (session('status'))
					<div class="mb-16 p-12 text-sm text-emerald-700 bg-emerald-50 rounded-md border border-emerald-200">
						{{ session('status') }}
					</div>
				@endif

				<form method="POST" action="{{ route('login') }}" class="space-y-20">
					@csrf

					<div>
						<x-form.label for="email">E-Mail</x-form.label>
						<x-form.input
							type="email"
							name="email"
							:value="old('email')"
							required
							autofocus
							autocomplete="username"
						/>
						<x-form.error name="email" />
					</div>

					<div>
						<x-form.label for="password">Passwort</x-form.label>
						<x-form.input
							type="password"
							name="password"
							required
							autocomplete="current-password"
						/>
						<x-form.error name="password" />
					</div>

					<div class="flex items-center justify-between">
						<x-form.checkbox name="remember">Angemeldet bleiben</x-form.checkbox>

						@if (Route::has('password.request'))
							<x-form.link :href="route('password.request')">Passwort vergessen?</x-form.link>
						@endif
					</div>

					<div>
						<x-form.button class="w-full">Anmelden</x-form.button>
					</div>
				</form>

			</div>
		</div>
	</div>
</x-layout.guest>
