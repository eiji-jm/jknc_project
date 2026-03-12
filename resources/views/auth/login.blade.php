<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>John Kelly &amp; Company – Log in</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    </style>
</head>

<body class="h-screen bg-gray-100">

<div class="grid grid-cols-1 md:grid-cols-2 min-h-screen">

    <div class="flex items-center justify-center p-8 bg-white">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-lg p-12 animate-fadeIn">

            <button onclick="history.back()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 text-2xl transition-colors">×</button>

            <div class="flex justify-center mb-8">
                <img src="{{ asset('images/imaglogo.png') }}" alt="John Kelly & Company"
                     class="h-16 object-contain"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                
                <div class="hidden flex-col items-center justify-center text-center uppercase tracking-tighter">
                    <span class="text-xl font-black text-slate-800 leading-none">John Kelly</span>
                    <span class="text-blue-600 font-black text-lg my-1">&</span>
                    <span class="text-xl font-black text-slate-800 leading-none">Company</span>
                </div>
            </div>

            <h1 class="text-center text-2xl font-semibold text-slate-800 mb-1">Log in</h1>
            <p class="text-center text-gray-400 text-sm mb-6">
                Don't have an account? <a href="{{ route('register') }}" class="text-blue-500 hover:underline">Sign up</a>
            </p>

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 text-center text-sm p-3 rounded mb-5 shadow-sm">
                    {{ $errors->first() }}
                </div>
            @endif
            
            @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 text-center text-sm p-3 rounded mb-5 shadow-sm">
                {{ session('success') }}
            </div>
            @endif
            
            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-gray-700 font-medium mb-1">Email</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}"
                           required
                           class="w-full px-4 py-3 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                </div>

                <div>
                    <label for="password" class="block text-gray-700 font-medium mb-1">Password</label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                               class="w-full px-4 py-3 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                        <button type="button" onclick="togglePassword(this)"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 text-sm hover:text-gray-700">
                            Show
                        </button>
                    </div>
                    <a href="#" class="text-blue-500 text-sm float-right mt-1 hover:underline">Forget your password?</a>
                </div>

                <button type="submit"
                        class="w-full py-3 bg-blue-500 hover:bg-blue-600 text-white font-semibold rounded-full shadow-md transition duration-200">
                    Log in
                </button>
            </form>

        </div>
    </div>

    <div class="relative hidden md:block overflow-hidden bg-slate-200">
        <img src="{{ asset('images/imag1logo.jpg') }}" alt="Hero Image"
             class="w-full h-full object-cover"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        
        <div class="hidden flex-col items-center justify-center w-full h-full bg-gradient-to-br from-blue-900 to-indigo-900 text-white text-center p-12">
            <h2 class="text-3xl font-bold mb-4">Precision Management</h2>
            <p class="text-blue-100 italic">John Kelly & Company Enterprise System</p>
        </div>

        <div class="absolute inset-0 bg-blue-900/10 mix-blend-multiply pointer-events-none"></div>
    </div>

</div>

<script>
    function togglePassword(btn) {
        const input = btn.previousElementSibling;
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';
        btn.textContent = isHidden ? 'Hide' : 'Show';
    }
</script>

</body>
</html>