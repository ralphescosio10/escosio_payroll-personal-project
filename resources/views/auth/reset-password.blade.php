<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-950 text-white">
    <div class="mx-auto flex min-h-screen max-w-4xl items-center justify-center px-4 py-10">
        <div class="w-full max-w-md overflow-hidden rounded-3xl border border-slate-800 bg-slate-900 shadow-2xl shadow-slate-950/50">
            <div class="p-8 sm:p-10">
                <div class="mb-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-indigo-400">Security</p>
                    <h2 class="mt-2 text-3xl font-bold text-white">Reset password</h2>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-slate-200">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-3 text-white outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/25">
                        @error('email')
                            <span class="mt-2 block text-xs text-rose-400">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-slate-200">New password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-3 pr-16 text-white outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/25">
                            <button type="button" data-password-toggle aria-controls="password" aria-label="Show password" aria-pressed="false" class="absolute inset-y-0 right-3 text-sm font-medium text-indigo-400 hover:text-indigo-300">Show</button>
                        </div>
                        @error('password')
                            <span class="mt-2 block text-xs text-rose-400">{{ $message }}</span>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-slate-200">Confirm new password</label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-3 pr-16 text-white outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/25">
                            <button type="button" data-password-toggle aria-controls="password_confirmation" aria-label="Show password" aria-pressed="false" class="absolute inset-y-0 right-3 text-sm font-medium text-indigo-400 hover:text-indigo-300">Show</button>
                        </div>
                    </div>

                    <button type="submit" class="w-full rounded-xl bg-indigo-500 px-4 py-3 font-semibold text-white transition hover:bg-indigo-400">Set new password</button>
                </form>
            </div>
        </div>
    </div>
    @include('auth.password-toggle')
</body>
</html>
