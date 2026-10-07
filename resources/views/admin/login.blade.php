<!DOCTYPE html>
@php $siteName = \App\Models\Setting::get('site_name', config('app.name')); @endphp
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin login — {{ $siteName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-soft px-5 text-ink antialiased">
    <div class="w-full max-w-sm">
        <a href="{{ route('home') }}" class="display block text-center text-5xl text-brand-500 transition-colors hover:text-brand-700">{{ $siteName }}<span class="text-brand-300">.</span></a>
        <p class="mt-1 text-center text-sm text-muted">Admin panel</p>

        <form method="POST" action="{{ route('admin.login.post') }}" class="mt-8 space-y-4 rounded-3xl border border-line bg-white p-7 shadow-sm">
            @csrf
            <div>
                <label for="email" class="mb-1.5 block text-sm font-semibold">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="field">
                @error('email')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="password" class="mb-1.5 block text-sm font-semibold">Password</label>
                <div class="relative">
                    <input id="password" type="password" name="password" required autocomplete="current-password" class="field pr-12">
                    <button type="button" id="toggle-password" aria-label="Show password" aria-pressed="false" aria-controls="password"
                            class="absolute inset-y-0 right-0 flex w-12 items-center justify-center rounded-r-xl text-muted transition-colors hover:text-brand-600 focus-visible:outline-none focus-visible:ring-4 focus-visible:ring-brand-200">
                        <svg data-eye class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg data-eye-off class="hidden h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 3l18 18"/><path d="M10.6 5.1A10.7 10.7 0 0112 5c6.5 0 10 7 10 7a17 17 0 01-3.2 4.2M6.6 6.6A17 17 0 002 12s3.5 7 10 7a10 10 0 004.3-.9"/><path d="M9.9 9.9a3 3 0 004.2 4.2"/></svg>
                    </button>
                </div>
            </div>
            <label class="flex items-center gap-2 text-sm text-muted"><input type="checkbox" name="remember" class="rounded border-line text-brand-500"> Keep me signed in</label>
            <button type="submit" class="btn-primary w-full">Sign in</button>
        </form>
    </div>
    <script>
        (() => {
            const input = document.getElementById('password');
            const btn = document.getElementById('toggle-password');
            if (!input || !btn) return;
            btn.addEventListener('click', () => {
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                btn.setAttribute('aria-pressed', show ? 'true' : 'false');
                btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
                btn.querySelector('[data-eye]').classList.toggle('hidden', show);
                btn.querySelector('[data-eye-off]').classList.toggle('hidden', !show);
                input.focus();
            });
        })();
    </script>
</body>
</html>
