<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIMASET SBH</title>
    @include('partials.assets')
</head>
<body class="auth-shell text-slate-900">
    <main class="flex min-h-screen items-center justify-center px-5 py-10">
        <section class="auth-card w-full max-w-md border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="mb-8 flex items-start gap-4">
                <img src="{{ asset('images/logo-simaset-rounded.png') }}" alt="Logo SIMASET SBH" class="auth-logo-mark 
                h-14 w-14 rounded-lg object-contain">
                <div class="min-w-0">
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-600">SIMASET SBH</p>
                    <h1 class="mt-1 text-2xl font-bold tracking-tight">Masuk ke Dashboard</h1>
                    <p class="mt-2 text-sm leading-6 text-slate-600">Gunakan username/email dan password akun Anda.</p>
                </div>
            </div>

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf
                @if (old('redirect', $redirect ?? null))
                    <input type="hidden" name="redirect" value="{{ old('redirect', $redirect ?? null) }}">
                @endif

                <div>
                    <label for="login" class="block text-sm font-medium text-slate-700">Username atau Email</label>
                    <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus class="mt-2 
                    block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm outline-none transition 
                    focus:border-blue-500 focus:ring-2 focus:ring-blue-200">
                    @error('login')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required class="mt-2 block w-full rounded-lg 
                    border border-slate-300 px-3 py-2 text-sm shadow-sm outline-none transition focus:border-blue-500 
                    focus:ring-2 focus:ring-blue-200">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="auth-primary-button w-full rounded-lg bg-blue-600 px-4 py-3 text-sm 
                font-semibold text-white shadow-sm transition hover:bg-blue-700">
                    Login
                </button>

            </form>
        </section>
    </main>
</body>
</html>
