<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <div class="mx-auto flex min-h-screen max-w-4xl items-center justify-center px-4 py-10">
        <div class="w-full max-w-md overflow-hidden rounded-3xl border border-slate-800 bg-slate-900 shadow-2xl shadow-slate-950/50">
            <div class="p-8 sm:p-10">
                <div class="mb-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-indigo-400">Recovery</p>
                    <h2 class="mt-2 text-3xl font-bold text-white">Forgot password</h2>
                </div>

                @if (session('status'))
                    <div class="mb-5 rounded-xl border border-emerald-500/30 bg-emerald-500/10 px-3 py-2 text-sm text-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-200">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-3 text-white outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/25">
                        @error('email')
                            <span class="mt-2 block text-xs text-rose-400">{{ $message }}</span>
                        @enderror
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-indigo-500 px-4 py-3 font-semibold text-white transition hover:bg-indigo-400">Send reset link</button>
                </form>

                <p class="mt-6 text-sm text-slate-400">
                    <a href="{{ route('login') }}" class="font-medium text-indigo-400 hover:text-indigo-300">Back to login</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
