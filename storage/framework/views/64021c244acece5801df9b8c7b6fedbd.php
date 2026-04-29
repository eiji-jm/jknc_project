<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>John Kelly &amp; Company - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.5s ease-out forwards; }
        :root {
            --navy: #102d79;
            --blue: #1d54e2;
            --black: #000000;
            --fog: #f1f2f2;
            --white: #ffffff;
        }
        body {
            font-family: "Space Grotesk", sans-serif;
            background-color: var(--fog);
            background-image:
                radial-gradient(1200px 300px at 10% -20%, rgba(29, 84, 226, 0.12), transparent 60%),
                radial-gradient(900px 260px at 90% 0%, rgba(16, 45, 121, 0.14), transparent 55%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.7), rgba(241, 242, 242, 0.9));
        }
        .brand-title {
            font-family: "Fraunces", serif;
            letter-spacing: 0.02em;
        }
    </style>
</head>

<body class="h-screen text-[#000000]">


<div class="grid grid-cols-1 md:grid-cols-[40%_60%] min-h-screen">

    <div class="flex items-center justify-center p-8 bg-[#ffffff]">
        <div class="relative w-full max-w-md bg-[#ffffff] rounded-2xl shadow-[0_20px_50px_rgba(16,45,121,0.12)] p-10 animate-fadeIn border border-[#102d79]/10">

            <a href="<?php echo e(route('login')); ?>" 
                class="absolute top-4 right-4 text-[#102d79]/60 hover:text-[#102d79] text-2xl transition-colors">&times;
            </a>
            <div class="flex justify-center mb-6">
                <img src="<?php echo e(asset('images/imaglogo.png')); ?>" alt="John Kelly & Company"
                     class="h-14 object-contain"
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                
                <div class="hidden flex-col items-center justify-center text-center uppercase tracking-tighter">
                    <span class="text-xl font-black text-[#102d79] leading-none brand-title">John Kelly</span>
                    <span class="text-[#1d54e2] font-black text-lg my-1 brand-title">&</span>
                    <span class="text-xl font-black text-[#102d79] leading-none brand-title">Company</span>
                </div>
            </div>

            <h1 class="text-center text-xl font-semibold text-[#102d79] mb-1 brand-title">Join the Team</h1>
            <p class="text-center text-[#102d79]/60 text-xs mb-6">
                Have an account? <a href="<?php echo e(route('login')); ?>" class="text-[#1d54e2] font-bold hover:underline">Log in</a>
            </p>

            <form method="POST" action="<?php echo e(route('register.post')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>

                <div>
                    <label for="name" class="block text-xs font-bold text-[#102d79]/60 uppercase tracking-widest mb-1">Full Name</label>
                    <input type="text" name="name" id="name" value="<?php echo e(old('name')); ?>" required
                           class="w-full px-4 py-2.5 border rounded-lg border-[#102d79]/20 focus:outline-none focus:ring-2 focus:ring-[#1d54e2] bg-[#f1f2f2] text-sm">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="email" class="block text-xs font-bold text-[#102d79]/60 uppercase tracking-widest mb-1">Email</label>
                        <input type="email" name="email" id="email" value="<?php echo e(old('email')); ?>" required
                               class="w-full px-4 py-2.5 border rounded-lg border-[#102d79]/20 focus:outline-none focus:ring-2 focus:ring-[#1d54e2] bg-[#f1f2f2] text-sm">
                    </div>
                    
                    <div>
                        <label for="role" class="block text-xs font-bold text-[#102d79]/60 uppercase tracking-widest mb-1">System Role</label>
                        <select name="role" id="role" required
                                class="w-full px-4 py-2.5 border rounded-lg border-[#102d79]/20 focus:outline-none focus:ring-2 focus:ring-[#1d54e2] bg-[#f1f2f2] text-sm font-medium">
                            <option value="Employee">Employee</option>
                            <option value="Admin">Admin</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-[#102d79]/60 uppercase tracking-widest mb-1">Password</label>
                    <input type="password" name="password" id="password" required
                           class="w-full px-4 py-2.5 border rounded-lg border-[#102d79]/20 focus:outline-none focus:ring-2 focus:ring-[#1d54e2] bg-[#f1f2f2] text-sm">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-[#102d79]/60 uppercase tracking-widest mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                           class="w-full px-4 py-2.5 border rounded-lg border-[#102d79]/20 focus:outline-none focus:ring-2 focus:ring-[#1d54e2] bg-[#f1f2f2] text-sm">
                </div>

                <button type="submit"
                        class="w-full py-3 mt-4 bg-[#1d54e2] hover:bg-[#102d79] text-white font-bold rounded-full shadow-md transition duration-200 uppercase text-xs tracking-widest">
                    Create Account
                </button>
            </form>

        </div>
    </div>

    
    <div class="relative hidden md:block overflow-hidden bg-[#102d79]">
        <img src="<?php echo e(asset('images/imag1logo.jpg')); ?>" alt="Hero Image"
             class="w-full h-full object-cover"
             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="hidden flex-col items-center justify-center w-full h-full bg-gradient-to-br from-[#102d79] via-[#1d54e2] to-[#102d79] text-white text-center p-12">
            <h2 class="text-3xl font-bold mb-4 brand-title">Welcome Aboard</h2>
            <p class="text-white/80 italic">Start managing everything in one secure place.</p>
        </div>
        <div class="absolute inset-0 bg-[#102d79]/20 mix-blend-multiply pointer-events-none"></div>
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(255,255,255,0.18),_transparent_55%)]"></div>
    </div>

</div>

</body>
</html>
<?php /**PATH D:\School\OJT\JK&CDealsContacts\jknc_project\resources\views/auth/register.blade.php ENDPATH**/ ?>