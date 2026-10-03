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
                <input id="password" type="password" name="password" required autocomplete="current-password" class="field">
            </div>
            <label class="flex items-center gap-2 text-sm text-muted"><input type="checkbox" name="remember" class="rounded border-line text-brand-500"> Keep me signed in</label>
            <button type="submit" class="btn-primary w-full">Sign in</button>
        </form>
    </div>
</body>
</html>
