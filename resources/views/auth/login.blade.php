<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - ARSIPIN.</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    @vite('resources/css/app.css')
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col md:flex-row antialiased select-none">

    <div class="w-full md:w-5/12 bg-white p-8 md:p-16 flex flex-col justify-between min-h-screen shadow-2xl z-10">
        
        <div class="flex items-center gap-3">
            <svg class="w-10 h-10 drop-shadow-sm" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                <rect width="100" height="100" rx="24" fill="#0c1a3c"/>
                <path d="M50 22L72 72H60L50 48L40 72H28L50 22Z" fill="#38bdf8"/>
                <path d="M43 58H57L50 42L43 58Z" fill="#ffffff"/>
            </svg>
            <div>
                <h1 class="text-2xl font-black tracking-tight text-[#0c1a3c]">
                    ARSIPIN<span class="text-sky-400">.</span>
                </h1>
                <p class="text-[9px] text-gray-400 font-bold uppercase tracking-widest -mt-1">Financial Document Repository</p>
            </div>
        </div>

        <div class="w-full max-w-sm mx-auto my-auto pt-10 pb-10">
            <div class="mb-8">
                <h2 class="text-2xl font-extrabold text-[#0c1a3c] tracking-tight">Selamat Datang Kembali</h2>
                <p class="text-gray-400 text-xs mt-1 font-light">Silakan masukkan kredensial akun Anda untuk mengakses pusat data pembukuan dan audit finansial.</p>
            </div>

            <form action="/login" method="POST" id="loginForm" class="space-y-5">
                @csrf

                @if($errors->has('loginError'))
                    <div class="p-4 bg-red-50 border border-red-100 text-red-600 text-xs font-bold rounded-xl flex items-center gap-2 shadow-sm">
                        <i class="fa-solid fa-circle-exclamation text-sm"></i>
                        <span>{{ $errors->first('loginError') }}</span>
                    </div>
                @endif

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Username </label>
                    <div class="relative flex items-center">
                        <i class="fa-solid fa-user absolute left-4 text-gray-300 text-sm transition-colors"></i>
                        <input type="text" name="username" value="{{ old('username') }}" required autocomplete="off"
                            placeholder="Masukkan username anda"
                            class="w-full pl-11 pr-4 py-3.5 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-700 text-sm focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider ml-1">Kata Sandi</label>
                    <div class="relative flex items-center">
                        <i class="fa-solid fa-lock absolute left-4 text-gray-300 text-sm transition-colors"></i>
                        <input type="password" name="password" id="passwordInput" required placeholder="••••••••"
                            class="w-full pl-11 pr-12 py-3.5 bg-gray-50/50 border border-gray-200 rounded-xl outline-none text-gray-700 text-sm focus:border-[#0c1a3c] focus:bg-white focus:ring-4 focus:ring-gray-100 transition-all font-light shadow-sm">
                        <button type="button" id="togglePassword" class="absolute right-4 text-gray-400 hover:text-gray-600 transition-colors focus:outline-none">
                            <i class="fa-solid fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="submitBtn" class="w-full py-3.5 bg-[#0c1a3c] text-white text-xs font-bold rounded-xl uppercase tracking-wider hover:bg-opacity-95 active:scale-[0.98] transition-all duration-150 shadow-lg shadow-blue-950/10 flex items-center justify-center gap-2">
                        <span id="btnText">Masuk ke Sistem</span>
                        <i class="fa-solid fa-paper-plane transition-all duration-300 text-sky-400" id="btnIcon"></i>
                    </button>
                </div>
            </form>
        </div>

        <div class="text-[10px] text-gray-400 font-light text-center md:text-left">
            &copy; 2026 ARSIPIN. Politeknik Negeri Batam. All Rights Reserved.
        </div>
    </div>

    <div class="w-full md:w-7/12 bg-gradient-to-br from-[#0c1a3c] via-[#112554] to-[#0ea5e9] flex items-center justify-center p-12 relative overflow-hidden min-h-screen md:min-h-0">
        
        <div class="absolute -top-20 -right-20 w-80 h-80 bg-sky-400/10 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-20 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>

        <div class="max-w-md text-center md:text-left relative z-20">
            <h2 class="text-white text-3xl md:text-4xl font-extrabold tracking-tight leading-tight uppercase">
                INTEGRASI TATA KELOLA <br><span class="text-sky-400">DOKUMEN FINANSIAL</span> PUSAT.
            </h2>
            <p class="mt-4 text-gray-300 text-xs md:text-sm font-light leading-relaxed opacity-90">
                Pusat manajemen dan pelacakan arsip digital pembukuan yang tersentralisasi. Dirancang khusus untuk memfasilitasi audit keuangan perusahaan melalui validasi berkas yang akurat, transparan, dan terlacak secara kronologis (*audit trail tracing*).
            </p>
            
            <div class="mt-8 grid grid-cols-2 gap-4 border-t border-white/10 pt-6">
                <div>
                    <h4 class="text-white font-mono text-lg font-bold">100%</h4>
                    <p class="text-gray-400 text-[10px] font-light uppercase tracking-wider">Integritas Auditing</p>
                </div>
                <div>
                    <h4 class="text-sky-400 font-mono text-lg font-bold">&lt; 3 MB</h4>
                    <p class="text-gray-400 text-[10px] font-light uppercase tracking-wider">Restriksi Berkas</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. MESIN SHOW/HIDE INTERAKSI MATA PASSWORD
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('passwordInput');
            const eyeIcon = document.getElementById('eyeIcon');

            togglePassword.addEventListener('click', function() {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                
                if (type === 'text') {
                    eyeIcon.classList.remove('fa-eye');
                    eyeIcon.classList.add('fa-eye-slash');
                    eyeIcon.classList.add('text-sky-500'); 
                } else {
                    eyeIcon.classList.remove('fa-eye-slash');
                    eyeIcon.classList.add('fa-eye');
                    eyeIcon.classList.remove('text-sky-500');
                }
            });

            // 2. MESIN TELEGRAM FLYING PLANE & LOADING LOCK STATE
            const loginForm = document.getElementById('loginForm');
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnIcon = document.getElementById('btnIcon');

            loginForm.addEventListener('submit', function() {
                // Kunci tombol
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
                
                // Efek Pesawat Telegram Terbang wkwkwk
                btnText.innerText = 'Menautkan Sesi Keuangan...';
                btnIcon.classList.remove('fa-paper-plane');
                btnIcon.classList.add('fa-plane-departure', 'animate-bounce'); 
            });
        });
    </script>
</body>
</html>