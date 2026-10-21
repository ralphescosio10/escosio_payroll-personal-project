<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Payroll Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-800">
    <div class="mx-auto flex min-h-screen max-w-5xl items-center justify-center px-4 py-10">
        <div class="grid w-full overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-xl lg:grid-cols-[1.1fr_0.9fr]">
            <div class="hidden bg-gradient-to-br from-emerald-500 via-teal-600 to-cyan-700 p-10 text-white lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-emerald-100">Setup</p>
                    <h1 class="mt-6 text-4xl font-bold leading-tight">Build a secure payroll workspace</h1>
                </div>
                <div class="space-y-3 text-sm text-emerald-50/90">
                    <div class="rounded-2xl border border-white/15 bg-white/5 p-4">Employee management</div>
                    <div class="rounded-2xl border border-white/15 bg-white/5 p-4">Daily compensation and tax review</div>
                    <div class="rounded-2xl border border-white/15 bg-white/5 p-4">Tax reporting</div>
                </div>
            </div>

            <div class="p-8 sm:p-10">
                <div class="mb-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-emerald-600">New account</p>
                    <h2 class="mt-2 text-3xl font-bold text-slate-900">Create account</h2>
                </div>

                <form method="POST" action="{{ route('register') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-slate-700">Full name</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-3 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/15">
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-700">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-slate-300 px-3 py-3 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/15">
                        @error('email')
                            <span class="mt-2 block text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-700">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required class="w-full rounded-xl border border-slate-300 px-3 py-3 pr-16 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/15">
                            <button type="button" data-password-toggle aria-controls="password" aria-label="Show password" aria-pressed="false" class="absolute inset-y-0 right-3 text-sm font-medium text-emerald-700 hover:text-emerald-600">Show</button>
                        </div>
                        @error('password')
                            <span class="mt-2 block text-xs text-rose-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-700">Confirm password</label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full rounded-xl border border-slate-300 px-3 py-3 pr-16 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/15">
                            <button type="button" data-password-toggle aria-controls="password_confirmation" aria-label="Show password" aria-pressed="false" class="absolute inset-y-0 right-3 text-sm font-medium text-emerald-700 hover:text-emerald-600">Show</button>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-emerald-600 px-4 py-3 font-semibold text-white transition hover:bg-emerald-500">Create account</button>
                </form>

                <p class="mt-6 text-sm text-slate-500">
                    Already have an account?
                    <a href="{{ route('login') }}" class="font-medium text-emerald-600 hover:text-emerald-500">Sign in</a>
                </p>
            </div>
        </div>
    </div>
    @include('auth.password-toggle')
</body>
</html>
