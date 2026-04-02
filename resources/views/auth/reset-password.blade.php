<x-layout.guest>
	<div class="min-h-full flex">

		{{-- Left: Brand panel --}}
		<div class="hidden lg:flex lg:w-1/2 bg-neutral-900 relative overflow-hidden items-end p-16">
			<div class="relative z-10">
				<div class="text-neutral-500 text-xs tracking-[0.2em] uppercase mb-3">Content Management</div>
				<div class="text-white text-4xl font-light tracking-tight leading-tight">
					{{ config('app.name', 'CMS') }}
				</div>
			</div>
			<div class="absolute inset-0 opacity-[0.03]"
				style="background-image: linear-gradient(rgba(255,255,255,.5) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.5) 1px, transparent 1px); background-size: 60px 60px;">
			</div>
		</div>

		{{-- Right: Form --}}
		<div class="w-full lg:w-1/2 flex items-center justify-center px-6 py-12">
			<div class="w-full max-w-sm">

				<div class="lg:hidden mb-16">
					<div class="text-neutral-400 text-xs tracking-[0.2em] uppercase mb-2">Content Management</div>
					<div class="text-neutral-900 text-2xl font-light tracking-tight">{{ config('app.name', 'CMS') }}</div>
				</div>

				<h1 class="text-lg font-medium text-neutral-900 mb-1">Neues Passwort</h1>
				<p class="text-sm text-neutral-500 mb-10">Legen Sie ein neues Passwort für Ihr Konto fest.</p>

				<form method="POST" action="{{ route('password.store') }}" class="space-y-6">
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

					<div class="pt-4">
						<x-form.button class="w-full">Passwort zurücksetzen</x-form.button>
					</div>
				</form>

			</div>
		</div>

	</div>
</x-layout.guest>
