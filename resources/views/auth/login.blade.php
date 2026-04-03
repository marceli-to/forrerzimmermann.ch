<x-layout.guest>
	<div class="min-h-dvh flex">
		<div class="hidden lg:flex lg:w-1/2 bg-navy items-end p-48">
			<div class="text-white">
				<x-icons.logo class="w-148" />
			</div>
		</div>
		<div class="w-full lg:w-1/2 bg-white flex items-center justify-center px-32 py-48">
			<div class="w-full max-w-xs">
				<div class="lg:hidden mb-32 text-navy">
					<x-icons.logo class="w-120" />
				</div>
				<h1 class="text-lg font-medium text-gray-900 mb-4">Anmelden</h1>
				<p class="text-sm text-gray-400 mb-24">Melden Sie sich mit Ihrem Konto an.</p>
				@if (session('status'))
					<div class="mb-16 p-12 text-sm text-emerald-700 bg-emerald-50 rounded-md border border-emerald-200">{{ session('status') }}</div>
				@endif
				<form method="POST" action="{{ route('login') }}" class="space-y-16">
					@csrf
					<div>
						<x-form.label for="email">E-Mail</x-form.label>
						<x-form.input type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
						<x-form.error name="email" />
					</div>
					<div>
						<x-form.label for="password">Passwort</x-form.label>
						<x-form.input type="password" name="password" required autocomplete="current-password" />
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
