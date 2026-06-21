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
                            50: '#fff7ed',
                            100: '#ffedd5',
                            200: '#fed7aa',
                            300: '#fdba74',
                            400: '#fb923c',
                            500: '#f97316',
                            600: '#ea580c',
                            700: '#c2410c',
                            800: '#9a3412',
                            900: '#7c2d12',
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
            background-color: #F8FAFC;
            background-image: 
                radial-gradient(at 0% 0%, hsla(24,100%,94%,1) 0, transparent 50%), 
                radial-gradient(at 50% 0%, hsla(199,100%,95%,1) 0, transparent 50%), 
                radial-gradient(at 100% 0%, hsla(24,100%,94%,1) 0, transparent 50%),
                radial-gradient(at 0% 100%, hsla(24,100%,94%,1) 0, transparent 50%),
                radial-gradient(at 100% 100%, hsla(199,100%,95%,1) 0, transparent 50%);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(226, 232, 240, 0.8);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="h-full mesh-gradient flex items-center justify-center p-6 relative overflow-hidden" 
      x-data="{ 
          showPass: false, 
          showForgotModal: false,
          nip: localStorage.getItem('remember_nip') || '{{ old('nip') }}',
          remember: localStorage.getItem('remember_nip') ? true : false,
          saveNip() {
              if (this.remember) {
                  localStorage.setItem('remember_nip', this.nip);
              } else {
                  localStorage.removeItem('remember_nip');
              }
          }
      }">
    <!-- Animated background elements -->
    <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-primary-500/10 rounded-full blur-[120px] animate-pulse"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-primary-600/10 rounded-full blur-[120px] animate-pulse" style="animation-delay: 2s"></div>
 
    <div class="max-w-5xl w-full grid grid-cols-1 lg:grid-cols-2 gap-0 rounded-[40px] overflow-hidden shadow-2xl shadow-slate-200/80 relative z-10 border border-slate-200 glass-card">
        
        <!-- Left Side: Branding -->
        <div class="hidden lg:flex flex-col justify-between p-16 bg-[#FFF7ED]/30 backdrop-blur-md relative border-r border-slate-100">
            <div class="relative z-10">
                <div class="flex items-center space-x-4 mb-12">
                    <div class="w-12 h-12 bg-primary-500 rounded-2xl flex items-center justify-center shadow-lg shadow-primary-500/30">
                        @if(\App\Models\Setting::get('app_logo'))
                            <img src="{{ asset(\App\Models\Setting::get('app_logo')) }}" class="max-h-full max-w-full">
                        @else
                            <i class="fas fa-boxes-stacked text-white text-2xl"></i>
                        @endif
                    </div>
                    <span class="text-2xl font-bold text-slate-800 tracking-wider">{{ \App\Models\Setting::get('app_name', 'SIDARLOG') }}</span>
                </div>
                
                <h1 class="text-5xl font-bold text-slate-800 leading-tight mb-8">
                    Smart Inventory & <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-primary-500 to-amber-600">Digital Logistics.</span>
                </h1>
                
                <p class="text-slate-600 text-lg leading-relaxed max-w-md">
                    Kelola seluruh aset, inventaris, dan logistik penanggulangan bencana daerah dengan sistem terpusat yang aman, transparan, dan akurat.
                </p>
            </div>
 
            <div class="relative z-10">
                <div class="flex items-center space-x-6">
                    <div class="flex -space-x-3">
                        <img src="https://ui-avatars.com/api/?name=User+1&background=f97316&color=fff" class="w-10 h-10 rounded-full border-2 border-white">
                        <img src="https://ui-avatars.com/api/?name=User+2&background=ea580c&color=fff" class="w-10 h-10 rounded-full border-2 border-white">
                        <img src="https://ui-avatars.com/api/?name=User+3&background=c2410c&color=fff" class="w-10 h-10 rounded-full border-2 border-white">
                    </div>
                    <p class="text-sm text-slate-500 font-medium">Dipercaya oleh <span class="text-primary-600 font-bold">1,200+</span> staf operasional daerah BPBD.</p>
                </div>
            </div>
            
            <!-- Abstract background pattern -->
            <div class="absolute bottom-0 right-0 w-64 h-64 opacity-[0.03] pointer-events-none text-primary-600">
                <i class="fas fa-cubes text-[200px]"></i>
            </div>
        </div>
 
        <!-- Right Side: Login Form -->
        <div class="bg-white p-12 lg:p-20 flex flex-col justify-center">
            <div class="lg:hidden flex items-center space-x-3 mb-10">
                <div class="w-10 h-10 bg-primary-500 rounded-xl flex items-center justify-center shadow-lg shadow-primary-500/20">
                    <i class="fas fa-boxes-stacked text-white text-xl"></i>
                </div>
                <span class="text-xl font-bold text-slate-800 tracking-wider">SIDARLOG</span>
            </div>
 
            <div class="mb-12">
                <h2 class="text-3xl font-bold text-slate-800 mb-3">Selamat Datang</h2>
                <p class="text-slate-500">Silakan masuk untuk melanjutkan akses ke portal logistik daerah BPBD.</p>
            </div>
 
            <form action="/login" method="POST" class="space-y-6" @submit="saveNip()">
                @csrf
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-widest ml-1">Username / NIP</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary-500 transition-colors">
                            <i class="fas fa-id-badge text-sm"></i>
                        </div>
                        <input type="text" name="nip" required x-model="nip"
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-2xl py-4 pl-12 pr-4 text-slate-800 placeholder-slate-400 outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all font-medium"
                            placeholder="Contoh: 19880123XXXXXXXX">
                    </div>
                    @error('nip')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
 
                <div class="space-y-2">
                    <div class="flex justify-between items-center px-1">
                        <label class="text-xs font-bold text-slate-500 uppercase tracking-widest">Kata Sandi</label>
                        <button type="button" @click="showForgotModal = true" class="text-[10px] font-bold text-primary-600 hover:text-primary-700 uppercase tracking-wider outline-none">Lupa Password?</button>
                    </div>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary-500 transition-colors">
                            <i class="fas fa-lock text-sm"></i>
                        </div>
                        <input :type="showPass ? 'text' : 'password'" name="password" required
                            class="w-full bg-slate-50/50 border border-slate-200 rounded-2xl py-4 pl-12 pr-12 text-slate-800 placeholder-slate-400 outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 transition-all font-medium"
                            placeholder="••••••••">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-slate-400 hover:text-slate-600 transition-colors">
                            <i :class="showPass ? 'fas fa-eye-slash' : 'fas fa-eye'" class="text-sm"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
 
                <div class="flex items-center pt-2">
                    <label class="flex items-center cursor-pointer group">
                        <input type="checkbox" name="remember" class="hidden peer" x-model="remember" value="1">
                        <div class="w-5 h-5 border-2 border-slate-200 rounded-lg flex items-center justify-center peer-checked:bg-primary-500 peer-checked:border-primary-500 transition-all">
                            <i class="fas fa-check text-white text-[10px] hidden peer-checked:block"></i>
                        </div>
                        <span class="ml-3 text-sm text-slate-500 group-hover:text-slate-700 transition-colors">Ingat akun saya</span>
                    </label>
                </div>
 
                <button type="submit"
                    class="w-full py-4 mt-4 bg-gradient-to-r from-primary-500 to-amber-600 hover:from-primary-600 hover:to-amber-700 text-white font-bold rounded-2xl shadow-xl shadow-primary-500/20 transform hover:-translate-y-0.5 active:scale-[0.99] transition-all flex items-center justify-center group">
                    Masuk Sekarang
                    <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>
 
            <div class="mt-16 text-center">
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">
                    {{ \App\Models\Setting::get('footer_text', '© 2026 Pemerintah Kabupaten - Digital Logistics.') }}
                </p>
            </div>
        </div>
    </div>

    <!-- Modal Lupa Password -->
    <div x-show="showForgotModal" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
         x-cloak>
        <div class="bg-white rounded-[32px] max-w-md w-full p-8 border border-slate-100 shadow-2xl relative overflow-hidden" @click.away="showForgotModal = false">
            <div class="absolute top-[-10%] right-[-10%] w-[30%] h-[30%] bg-primary-500/10 rounded-full blur-2xl"></div>
            
            <div class="flex items-center space-x-3 mb-6">
                <div class="w-10 h-10 bg-primary-50 text-primary-600 rounded-xl flex items-center justify-center shadow-inner">
                    <i class="fas fa-key text-sm"></i>
                </div>
                <h3 class="text-lg font-extrabold text-slate-800">Lupa Kata Sandi?</h3>
            </div>
            
            <div class="space-y-4 text-sm text-slate-600 leading-relaxed">
                <p>Untuk menjaga keamanan data logistik kebencanaan, pemulihan kata sandi dilakukan secara terpusat oleh Administrator.</p>
                
                <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 space-y-2">
                    <div class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-primary-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
                        <span>Hubungi **Super Admin** atau **Admin Logistik** BPBD.</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-primary-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
                        <span>Informasikan **NIP** dan nama lengkap Anda untuk verifikasi identitas.</span>
                    </div>
                    <div class="flex items-start gap-2.5">
                        <span class="w-5 h-5 rounded-full bg-primary-500 text-white font-bold text-xs flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
                        <span>Administrator akan mereset sandi Anda ke kata sandi bawaan sistem.</span>
                    </div>
                </div>

                <div class="p-3.5 bg-amber-50 border border-amber-200/50 rounded-2xl text-amber-800 text-xs flex items-start gap-2">
                    <i class="fas fa-circle-info mt-0.5 text-sm"></i>
                    <span>Setelah berhasil login menggunakan kata sandi default, harap **segera mengubah** kata sandi Anda pada menu **Profil** demi keamanan akun.</span>
                </div>
            </div>
            
            <div class="mt-8 flex justify-end">
                <button type="button" @click="showForgotModal = false" 
                        class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl text-xs transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</body>
</html>

