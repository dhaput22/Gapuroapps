<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Gapuro System - Login</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-cover bg-center flex items-center justify-center"
    style="background-image: url('/images/bg.jpg');">

    <div class="w-full max-w-md bg-[#0A1A2F]/90 backdrop-blur-md p-8 rounded-2xl shadow-xl">

        <div class="text-center mb-6">
            <img src="/images/logo.png" alt="Gapuro" class="w-20 mx-auto mb-2">
            <h1 class="text-yellow-400 font-bold text-xl tracking-widest">
                GAPURO SYSTEM
            </h1>
        </div>

        <!-- ERROR LOGIN (username/password salah) -->
        @if (session('error'))
        <div class="mb-4 px-4 py-3 rounded-md bg-red-500/80 text-white text-sm">
            {{ session('error') }}
        </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <input type="text" name="username"
                    placeholder="Enter your username"
                    class="w-full px-4 py-2 rounded-md bg-white/90 border border-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                @error('username') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <input type="password" name="password"
                    placeholder="Password"
                    class="w-full px-4 py-2 rounded-md bg-white/90 border border-gray-300 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                @error('password') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <button type="submit"
                class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded-md transition">
                Log in
            </button>

            <div class="flex justify-end">

                <a href="{{ route('register') }}" class="text-sm text-blue-400 hover:underline">
                    Reset Password
                </a>
            </div>
        </form>

        <p class="text-center text-xs text-gray-300 mt-6">
            PEB ISD 2014 (Gapuro Team Site)
        </p>
    </div>

</body>

</html>