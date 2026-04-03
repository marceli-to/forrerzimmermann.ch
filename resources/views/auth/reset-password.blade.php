<x-layout.guest>
	<div class="min-h-dvh flex items-center justify-center p-16">
		<div class="w-full max-w-sm">

			{{-- Logo --}}
			<div class="mb-10 text-white">
				<x-icons.logo class="w-148" />
			</div>

			{{-- Form panel --}}
			<div class="bg-warm-50 rounded-xl p-32">

				<h1 class="text-lg font-medium text-warm-900 mb-1">Neues Passwort</h1>
				<p class="text-sm text-warm-400 mb-24">Legen Sie ein neues Passwort für Ihr Konto fest.</p>

				<form method="POST" action="{{ route('password.store') }}" class="space-y-20">
					@csrf
					<input type="hidden" name="token" value="{{ $request->route('token') }}">

					<div>
						<x-form.label for="email">E-Mail</x-form.label>
						<x-form.input
							type="email"
							name="email"
							:value="old('email', $request->email)"
							required
							autofocus
							autocomplete="username"
						/>
						<x-form.error name="email" />
					</div>

					<div>
						<x-form.label for="password">Neues Passwort</x-form.label>
						<x-form.input
							type="password"
							name="password"
							required
							autocomplete="new-password"
						/>
						<x-form.error name="password" />
					</div>

					<div>
						<x-form.label for="password_confirmation">Passwort bestätigen</x-form.label>
						<x-form.input
							type="password"
							name="password_confirmation"
							required
							autocomplete="new-password"
						/>
						<x-form.error name="password_confirmation" />
					</div>

					<div>
						<x-form.button class="w-full">Passwort zurücksetzen</x-form.button>
					</div>
				</form>

			</div>
		</div>
	</div>
</x-layout.guest>
