<x-layout.guest>
	<div class="min-h-full flex">

		{{-- Left: Brand panel --}}
		<div class="hidden lg:flex lg:w-1/2 bg-neutral-900 dark:bg-neutral-900 relative overflow-hidden items-end p-16">
			<div class="relative z-10 text-white">
				<x-icons.logo class="w-48" />
			</div>
		</div>

		{{-- Right: Login form --}}
		<div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
			<div class="w-full max-w-sm">

				{{-- Mobile brand --}}
				<div class="lg:hidden mb-16 text-neutral-900 dark:text-white">
					<x-icons.logo class="w-36" />
				</div>

				<h1 class="text-lg font-medium text-neutral-900 dark:text-white mb-1">Anmelden</h1>
				<p class="text-sm text-neutral-500 dark:text-neutral-400 mb-10">Melden Sie sich mit Ihrem Konto an.</p>

				@if (session('status'))
					<div class="mb-6 p-3 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200">
						{{ session('status') }}
					</div>
				@endif

				<form method="POST" action="{{ route('login') }}" class="space-y-6">
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

					<div class="flex items-center justify-between pt-2">
						<x-form.checkbox name="remember">Angemeldet bleiben</x-form.checkbox>

						@if (Route::has('password.request'))
							<x-form.link :href="route('password.request')">Passwort vergessen?</x-form.link>
						@endif
					</div>

					<div class="pt-4">
						<x-form.button class="w-full">Anmelden</x-form.button>
					</div>
				</form>

			</div>
		</div>

	</div>
</x-layout.guest>
