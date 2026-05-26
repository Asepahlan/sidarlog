<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ \App\Models\Setting::get('app_name', 'SIDARLOG') }}</title>
    @if(\App\Models\Setting::get('app_favicon'))
        <link rel="icon" type="image/png" href="{{ asset(\App\Models\Setting::get('app_favicon')) }}">
    @endif
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        navy: {
                            800: '#1e293b',
                            900: '#0f172a',
                            950: '#020617',
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .mesh-gradient {
            background-color: #020617;
            background-image: 
                radial-gradient(at 0% 0%, hsla(202,100%,15%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(199,100%,10%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(202,100%,15%,1) 0, transparent 50%),
                radial-gradient(at 0% 100%, hsla(202,100%,15%,1) 0, transparent 50%),
                radial-gradient(at 100% 100%, hsla(199,100%,10%,1) 0, transparent 50%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="h-full mesh-gradient flex items-center justify-center p-6 relative overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary-600/10 rounded-full blur-[120px] animate-pulse"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary-900/20 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s"></div>

    <div class="max-w-5xl w-full grid grid-cols-1 lg:grid-cols-2 gap-0 rounded-[40px] overflow-hidden shadow-2xl shadow-black/50 relative z-10 border border-white/5">
        
        <!-- Left Side: Branding -->
        <div class="hidden lg:flex flex-col justify-between p-16 bg-navy-900/40 backdrop-blur-md relative">
            <div class="relative z-10">
                <div class="flex items-center space-x-4 mb-12">
                    <div class="w-12 h-12 bg-primary-600 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                        @if(\App\Models\Setting::get('app_logo'))
                            <img src="{{ asset(\App\Models\Setting::get('app_logo')) }}" class="max-h-full max-w-full">
                        @else
                            <i class="fas fa-boxes-stacked text-white text-2xl"></i>
                        @endif
                    </div>
                    <span class="text-2xl font-bold text-white tracking-wider">{{ \App\Models\Setting::get('app_name', 'SIDARLOG') }}</span>
                </div>
                
                <h1 class="text-5xl font-bold text-white leading-tight mb-8">
                    Smart Inventory & <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-400 to-blue-600">Digital Logistics.</span>
                </h1>
                
                <p class="text-gray-400 text-lg leading-relaxed max-w-md">
                    Kelola seluruh aset, inventaris, dan logistik daerah dengan sistem terpusat yang aman, transparan, dan akurat.
                </p>
            </div>

            <div class="relative z-10">
                <div class="flex items-center space-x-6">
                    <div class="flex -space-x-3">
                        <img src="https://ui-avatars.com/api/?name=User+1&background=0284c7&color=fff" class="w-10 h-10 rounded-full border-2 border-navy-900">
                        <img src="https://ui-avatars.com/api/?name=User+2&background=0369a1&color=fff" class="w-10 h-10 rounded-full border-2 border-navy-900">
                        <img src="https://ui-avatars.com/api/?name=User+3&background=075985&color=fff" class="w-10 h-10 rounded-full border-2 border-navy-900">
                    </div>
                    <p class="text-sm text-gray-400 font-medium">Dipercaya oleh <span class="text-white font-bold">1,200+</span> staf operasional daerah.</p>
                </div>
            </div>
            
            <!-- Abstract background pattern -->
            <div class="absolute bottom-0 right-0 w-64 h-64 opacity-5 pointer-events-none">
                <i class="fas fa-cubes text-[200px] text-white"></i>
            </div>
        </div>

        <!-- Right Side: Login Form -->
        <div class="bg-white/5 backdrop-blur-xl p-12 lg:p-20 flex flex-col justify-center border-l border-white/5">
            <div class="lg:hidden flex items-center space-x-3 mb-10">
                <div class="w-10 h-10 bg-primary-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-boxes-stacked text-white text-xl"></i>
                </div>
                <span class="text-xl font-bold text-white tracking-wider">SIDARLOG</span>
            </div>

            <div class="mb-12">
                <h2 class="text-3xl font-bold text-white mb-3">Selamat Datang</h2>
                <p class="text-gray-400">Silakan masuk untuk melanjutkan akses ke portal logistik daerah.</p>
            </div>

            <form action="/login" method="POST" class="space-y-6" x-data="{ showPass: false }">
                @csrf
                <div class="space-y-2">
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-widest ml-1">Username / NIP</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 group-focus-within:text-primary-500 transition-colors">
                            <i class="fas fa-id-badge text-sm"></i>
                        </div>
                        <input type="text" name="nip" required value="{{ old('nip') }}"
                            class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white placeholder-gray-600 outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all font-medium"
                            placeholder="Contoh: 19880123XXXXXXXX">
                    </div>
                    @error('nip')
                        <p class="text-red-400 text-[10px] font-bold uppercase tracking-tighter mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-widest">Kata Sandi</label>
                        <a href="#" class="text-[10px] font-bold text-primary-400 hover:text-primary-300 uppercase tracking-wider">Lupa Password?</a>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-500 group-focus-within:text-primary-500 transition-colors">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <input :type="showPass ? 'text' : 'password'" name="password" required
                            class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-12 text-white placeholder-gray-600 outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all font-medium"
                            placeholder="••••••••">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-500 hover:text-white transition-colors">
                            <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center pt-2">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="hidden peer">
                        <div class="w-5 h-5 border-2 border-white/10 rounded-lg flex items-center justify-center peer-checked:bg-primary-600 peer-checked:border-primary-600 transition-all">
                            <i class="fas fa-check text-white text-[10px] hidden peer-checked:block"></i>
                        </div>
                        <span class="ml-3 text-sm text-gray-400 group-hover:text-gray-300 transition-colors">Ingat akun saya</span>
                    </label>
                </div>

                <button type="submit"
                    class="w-full py-4 mt-4 bg-gradient-to-r from-primary-600 to-blue-700 hover:from-primary-500 hover:to-blue-600 text-white font-bold rounded-2xl shadow-xl shadow-primary-500/20 transform hover:-translate-y-1 active:scale-[0.98] transition-all flex items-center justify-center group">
                    Masuk Sekarang
                    <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <div class="mt-16 text-center">
                <p class="text-[10px] text-gray-600 font-bold uppercase tracking-widest">
                    {{ \App\Models\Setting::get('footer_text', '© 2026 Pemerintah Kabupaten - Digital Logistics.') }}
                </p>
            </div>
        </div>
    </div>
</body>
</html>

