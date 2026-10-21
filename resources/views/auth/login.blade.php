<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Portal Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <div class="mx-auto flex min-h-screen max-w-6xl items-center justify-center px-4 py-10">
        <div class="grid w-full overflow-hidden rounded-3xl border border-slate-800 bg-slate-900 shadow-2xl shadow-slate-950/50 lg:grid-cols-2">
            <div class="hidden bg-gradient-to-br from-indigo-600 via-violet-600 to-cyan-500 p-10 lg:flex lg:flex-col lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.35em] text-indigo-100/80">Payroll Suite</p>
                    <h1 class="mt-6 text-4xl font-bold leading-tight">Modern tax and payroll operations</h1>
                </div>
                <div class="space-y-4 text-sm text-indigo-50/90">
                    <div class="rounded-2xl border border-white/15 bg-white/5 p-4 backdrop-blur-sm">
                        Faster filing workflows
                    </div>
                    <div class="rounded-2xl border border-white/15 bg-white/5 p-4 backdrop-blur-sm">
                        Cleaner reconciliation and reporting
                    </div>
                </div>
            </div>

            <div class="p-8 sm:p-10">
                <div class="mb-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-indigo-400">Welcome back</p>
                    <h2 class="mt-2 text-3xl font-bold text-white">Sign in</h2>
                </div>

                @if (session('status'))
                    <div role="status" class="mb-6 rounded-xl border border-emerald-800 bg-emerald-950/60 px-4 py-3 text-sm text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-200">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-3 text-white outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/25">
                        @error('email')
                            <span class="mt-2 block text-xs text-rose-400">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-200">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-3 pr-16 text-white outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/25">
                            <button type="button" data-password-toggle aria-controls="password" aria-label="Show password" aria-pressed="false" class="absolute inset-y-0 right-3 text-sm font-medium text-indigo-400 hover:text-indigo-300">Show</button>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-indigo-500 px-4 py-3 font-semibold text-white transition hover:bg-indigo-400">Login to dashboard</button>
                </form>

                <div class="mt-6 flex items-center justify-between text-sm">
                    <p class="text-slate-400">
                        Need an account?
                        <a href="{{ route('register') }}" class="font-medium text-indigo-400 hover:text-indigo-300">Create one</a>
                    </p>
                    <a href="{{ route('password.request') }}" class="font-medium text-indigo-400 hover:text-indigo-300">Forgot password?</a>
                </div>
            </div>
        </div>
    </div>
    @include('auth.password-toggle')
</body>
</html>
