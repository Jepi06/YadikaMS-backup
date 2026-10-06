<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login PKL</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

    <div class="min-h-screen flex items-center justify-center">

        <div class="bg-white p-8 rounded-xl shadow-md w-full max-w-md">

            <h1 class="text-2xl font-bold mb-6 text-center">
                Login Sistem PKL
            </h1>

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    {{ $errors->first() }}
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="bg-green-100 text-green-700 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="{{ route('pkl.login.process') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block mb-2 font-medium">
                        Email
                    </label>

                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full border rounded-lg px-4 py-2" required>
                </div>

                <div class="mb-6">
                    <label class="block mb-2 font-medium">
                        Password
                    </label>

                    <input type="password" name="password" class="w-full border rounded-lg px-4 py-2" required>
                </div>
                <div class="mb-4 flex items-center gap-2">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember" class="text-sm">Ingat saya</label>
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg">
                    Login
                </button>
            </form>

        </div>

    </div>

</body>

</html>
