<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Gapuro System - Register</title>

    {{-- Vite (Tailwind CSS + optional JS) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="h-full w-full bg-no-repeat bg-center bg-cover flex items-center justify-center"
    style="background-image: url('/images/bg.jpg')">

    <!-- overlay gelap -->
    <div class="absolute inset-0 bg-black/30"></div>

    <div class="relative z-10 w-full max-w-md px-8 py-10 bg-[#0A1A2D]/90 backdrop-blur-md rounded-2xl shadow-xl">
        <!-- Logo + Title -->
        <div class="text-center mb-6">
            <img src="/images/logo.png" alt="Gapuro" class="w-20 mx-auto mb-2" />
            <h1 class="text-yellow-400 font-bold text-xl tracking-widest">GAPURO SYSTEM</h1>
            <p class="text-sm text-gray-300 mt-1">Register an account to access the system</p>
        </div>

        <!-- Session / Status -->
        @if (session('status'))
        <div class="mb-4 text-sm text-green-400">
            {{ session('status') }}
        </div>
        @endif

        <!-- Registration form -->
        <form method="POST" action="{{ route('register') }}" class="space-y-4" novalidate>
            @csrf

            <div>
                <label for="name" class="sr-only">Full name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                    class="w-full px-4 py-2 rounded-md bg-white/90 border border-gray-300 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    placeholder="Full name" />
                @error('name') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="username" class="sr-only">Username</label>
                <input id="username" name="username" type="text" value="{{ old('username') }}" required
                    class="w-full px-4 py-2 rounded-md bg-white/90 border border-gray-300 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    placeholder="Create a username" />
                @error('username') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password" class="sr-only">Password</label>
                <input id="password" name="password" type="password" required
                    class="w-full px-4 py-2 rounded-md bg-white/90 border border-gray-300 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    placeholder="Password" />
                @error('password') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
            </div>

            <div>
                <label for="password_confirmation" class="sr-only">Confirm password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="w-full px-4 py-2 rounded-md bg-white/90 border border-gray-300 placeholder-gray-600 focus:outline-none focus:ring-2 focus:ring-yellow-400"
                    placeholder="Confirm password" />
            </div>

            <!-- Optional role selection (commented out) 
            
    <div>
        <label for="role" class="sr-only">Role</label>
        <select id="role" name="role"
                class="w-full px-4 py-2 rounded-md bg-white/90 border border-gray-300 focus:outline-none focus:ring-2 focus:ring-yellow-400">
        <option value="viewer" {{ old('role')=='viewer' ? 'selected':'' }}>Viewer</option>
            <option value="staff" {{ old('role')=='staff' ? 'selected':'' }}>Staff</option>
            </select>
            @error('role') <p class="mt-1 text-sm text-red-400">{{ $message }}</p> @enderror
    </div>
    -->

            <div>
                <button type="submit"
                    class="w-full bg-yellow-500 hover:bg-yellow-600 text-white font-bold py-2 rounded-md transition">
                    Register
                </button>
            </div>

            <div class="flex items-center justify-between text-sm text-gray-300">
                <div>Already have an account?</div>
                <a href="{{ route('login') }}" class="text-blue-300 hover:underline">Sign in</a>
            </div>
        </form>

        <p class="text-center text-xs text-gray-300 mt-6">PEB ISD 2014 (Gapuro Team Site)</p>
    </div>

</body>

</html>