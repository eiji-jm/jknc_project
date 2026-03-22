<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>John Kelly &amp; Company – Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
    </style>
</head>

<body class="h-screen bg-gray-100">

{{-- 40/60 Split to keep your preferred layout --}}
<div class="grid grid-cols-1 md:grid-cols-[40%_60%] min-h-screen">

    <div class="flex items-center justify-center p-8 bg-white">
        <div class="relative w-full max-w-md bg-white rounded-2xl shadow-lg p-10 animate-fadeIn">

            <a href="{{ route('login') }}" 
                class="absolute top-4 right-4 text-gray-400 hover:text-gray-800 text-2xl transition-colors"> ×
            </a>
            <div class="flex justify-center mb-6">
                <img src="{{ asset('images/imaglogo.png') }}" alt="John Kelly & Company"
                     class="h-14 object-contain"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                
                <div class="hidden flex-col items-center justify-center text-center uppercase tracking-tighter">
                    <span class="text-xl font-black text-slate-800 leading-none">John Kelly</span>
                    <span class="text-blue-600 font-black text-lg my-1">&</span>
                    <span class="text-xl font-black text-slate-800 leading-none">Company</span>
                </div>
            </div>

            <h1 class="text-center text-xl font-semibold text-slate-800 mb-1">Join the Team</h1>
            <p class="text-center text-gray-400 text-xs mb-6">
                Have an account? <a href="{{ route('login') }}" class="text-blue-500 font-bold hover:underline">Log in</a>
            </p>

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label for="name" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Full Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           class="w-full px-4 py-2.5 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required
                               class="w-full px-4 py-2.5 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm">
                    </div>
                    {{-- NEW: Role Selection Dropdown --}}
                    <div>
                        <label for="role" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">System Role</label>
                        <select name="role" id="role" required
                                class="w-full px-4 py-2.5 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm font-medium">
                            <option value="Employee">Employee</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-2.5 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full px-4 py-2.5 border rounded-lg border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-50 text-sm">
                </div>

                <button type="submit"
                        class="w-full py-3 mt-4 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-full shadow-md transition duration-200 uppercase text-xs tracking-widest">
                    Create Account
                </button>
            </form>

        </div>
    </div>

    {{-- RIGHT PANEL: Hero Image --}}
    <div class="relative hidden md:block overflow-hidden bg-slate-200">
        <img src="{{ asset('images/imag1logo.jpg') }}" alt="Hero Image"
             class="w-full h-full object-cover"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="absolute inset-0 bg-blue-900/10 mix-blend-multiply pointer-events-none"></div>
    </div>

</div>

</body>
</html>