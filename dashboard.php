<?php
session_start();
require_once 'config/lang.php';

// Guard gate: tendang pengguna balik ke index jika tiada sesi Supabase aktif
if (!isset($_SESSION['access_token'])) {
    header("Location: index.php");
    exit;
}

// Simulasi data untuk kad statistik (Boleh dihubungkan ke pangkalan data kemudian)
$total_employees = 42;
$active_programs = 3; // Contoh: SHRP, MHFA, HIRARC
$pending_claims  = 5;
?>
<!DOCTYPE html>
<html lang="<?php echo current_language(); ?>" class="h-full bg-slate-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(t('dashboard_title')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full antialiased text-slate-800">

<div class="flex min-h-full">
    
    <!-- 1. SIDEBAR NAVIGATION (Kiri) -->
    <div class="hidden md:flex md:w-64 md:flex-col md:fixed md:inset-y-0 bg-[#0f172a] text-slate-300">
        <!-- Logo Syarikat -->
        <div class="flex items-center space-x-3 px-6 py-5 border-b border-slate-800">
            <div class="h-9 w-9 bg-white rounded-full flex items-center justify-center p-1">
                <img src="assets/images/MASMA Northern (1).png" alt="Logo" class="h-full w-full object-contain">
            </div>
            <div class="leading-none">
                <span class="block text-base font-bold text-white tracking-wide">MASMA Safety</span>
                <span class="text-[10px] text-teal-400 font-medium tracking-widest uppercase">HR Portal</span>
            </div>
        </div>

        <!-- Menu Pilihan -->
        <div class="flex-1 flex flex-col justify-between pt-5 pb-4 overflow-y-auto">
            <nav class="flex-1 px-3 space-y-1">
                <!-- Utama (Active) -->
                <a href="#" class="bg-slate-800 text-white flex items-center px-4 py-3 text-sm font-medium rounded-lg group transition-all">
                    <svg class="mr-3 h-5 w-5 text-teal-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2v-4zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z" />
                    </svg>
                        <?php echo htmlspecialchars(t('main_summary')); ?>
                </a>

                <!-- Program Keselamatan -->
                <a href="program_keselamatan.php" class="hover:bg-slate-800/50 hover:text-white flex items-center px-4 py-3 text-sm font-medium rounded-lg group transition-all">
                    <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                    </svg>
                    <?php echo htmlspecialchars(t('safety_programs')); ?>
                </a>

                <!-- Tuntutan Kewangan -->
                <a href="claims.php" class="hover:bg-slate-800/50 hover:text-white flex items-center px-4 py-3 text-sm font-medium rounded-lg group transition-all">
                    <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <?php echo htmlspecialchars(t('claims')); ?>
                </a>

                <!-- Log Kerja / Logbook -->
                <a href="logbook.php" class="hover:bg-slate-800/50 hover:text-white flex items-center px-4 py-3 text-sm font-medium rounded-lg group transition-all">
                    <svg class="mr-3 h-5 w-5 text-slate-400 group-hover:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                    <?php echo htmlspecialchars(t('logbook')); ?>
                </a>
            </nav>

            <!-- Profil Ringkas Bawah Sidebar -->
            <div class="px-4 border-t border-slate-800 pt-4">
                <div class="flex items-center justify-between bg-slate-900 p-3 rounded-xl border border-slate-800">
                    <div class="truncate max-w-[140px]">
                        <p class="text-xs font-semibold text-white truncate"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
                        <p class="text-[10px] text-slate-400">Peringkat Akses: Staf</p>
                    </div>
                    <a href="logout.php" class="p-1.5 bg-red-500/10 hover:bg-red-500/20 text-red-400 rounded-md transition-all" title="Log Keluar">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. MAIN CONTENT AREA (Kanan) -->
    <div class="flex flex-col md:pl-64 flex-1">
        
        <!-- Top Header Bar -->
        <header class="sticky top-0 z-10 bg-white border-b border-slate-200 flex items-center justify-between h-16 px-6 sm:px-8">
            <div class="flex items-center gap-4">
                <h2 class="text-lg font-semibold text-slate-800"><?php echo htmlspecialchars(t('app_name')); ?></h2>
            </div>
            <div class="flex items-center space-x-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                    <span class="w-1.5 h-1.5 mr-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                    Sesi Supabase Aktif
                </span>
                <div class="flex items-center gap-2 text-xs text-slate-500">
                    <span><?php echo htmlspecialchars(t('language_switch')); ?>:</span>
                    <a href="<?php echo lang_url('en'); ?>" class="text-blue-600 hover:text-blue-700">EN</a>
                    <span>|</span>
                    <a href="<?php echo lang_url('ms'); ?>" class="text-blue-600 hover:text-blue-700">BM</a>
                </div>
            </div>
        </header>

        <!-- Dashboard Dashboard Viewport Grid -->
        <main class="flex-1 p-6 sm:p-8 space-y-8">
            
            <!-- Banner Selamat Datang -->
            <div class="bg-gradient-to-r from-blue-700 to-indigo-800 rounded-2xl p-6 sm:p-8 text-white shadow-md relative overflow-hidden">
                <div class="relative z-10 max-w-md space-y-2">
                    <h3 class="text-2xl font-bold"><?php echo htmlspecialchars(t('home_welcome')); ?></h3>
                    <p class="text-indigo-100 text-sm font-light leading-relaxed">
                        <?php echo htmlspecialchars(t('home_description')); ?>
                    </p>
                </div>
                <!-- Hiasan Geometri Latar Belakang -->
                <div class="absolute -right-10 -bottom-10 h-40 w-40 bg-white/5 rounded-full blur-xl pointer-events-none"></div>
            </div>

            <!-- 3. CARDS STATISTIK (Macam Niagawan KPI Dashboard) -->
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                
                <!-- Kad 1: Bilangan Kakitangan -->
                <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Jumlah Kakitangan</span>
                        <h4 class="text-3xl font-bold text-slate-800"><?php echo $total_employees; ?></h4>
                        <p class="text-[11px] text-slate-500">Kemas kini aktif bulan Mei</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-xl text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Kad 2: Modul Program Aktif -->
                <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Modul Program Keselamatan</span>
                        <h4 class="text-3xl font-bold text-slate-800"><?php echo $active_programs; ?></h4>
                        <p class="text-[11px] text-emerald-600 font-medium">SHRP, MHFA, HIRARC ready</p>
                    </div>
                    <div class="p-3 bg-emerald-50 rounded-xl text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>

                <!-- Kad 3: Tuntutan Pending -->
                <div class="bg-white p-5 rounded-xl border border-slate-200/80 shadow-sm flex items-center justify-between hover:shadow-md transition-all">
                    <div class="space-y-1">
                        <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Tuntutan Belum Diproses</span>
                        <h4 class="text-3xl font-bold text-amber-600"><?php echo $pending_claims; ?></h4>
                        <p class="text-[11px] text-slate-500">Menunggu semakan pengurus</p>
                    </div>
                    <div class="p-3 bg-amber-50 rounded-xl text-amber-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

            </div>

            <!-- 4. HR FEATURES GRID -->
            <div class="space-y-4">
                <div>
                    <h4 class="font-bold text-slate-800 text-lg mb-4">HR Management Features</h4>
                </div>
                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    
                    <!-- Payroll Card -->
                    <a href="payroll.php" class="group bg-white rounded-xl shadow-md hover:shadow-lg transition-all border border-slate-200 overflow-hidden">
                        <div class="p-6 flex flex-col items-center justify-center text-center h-40">
                            <div class="w-14 h-14 bg-blue-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-blue-200 transition-all">
                                <svg class="w-7 h-7 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Payroll</h3>
                            <p class="text-xs text-slate-500 mt-2">Manage salaries</p>
                        </div>
                    </a>

                    <!-- Annual Leave Card -->
                    <a href="annual_leave.php" class="group bg-white rounded-xl shadow-md hover:shadow-lg transition-all border border-slate-200 overflow-hidden">
                        <div class="p-6 flex flex-col items-center justify-center text-center h-40">
                            <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-green-200 transition-all">
                                <svg class="w-7 h-7 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Annual Leave</h3>
                            <p class="text-xs text-slate-500 mt-2">Request & track</p>
                        </div>
                    </a>

                    <!-- Attendance Card -->
                    <a href="attendance.php" class="group bg-white rounded-xl shadow-md hover:shadow-lg transition-all border border-slate-200 overflow-hidden">
                        <div class="p-6 flex flex-col items-center justify-center text-center h-40">
                            <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-purple-200 transition-all">
                                <svg class="w-7 h-7 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Attendance</h3>
                            <p class="text-xs text-slate-500 mt-2">Check-in tracking</p>
                        </div>
                    </a>

                    <!-- Employee Management Card -->
                    <a href="employees.php" class="group bg-white rounded-xl shadow-md hover:shadow-lg transition-all border border-slate-200 overflow-hidden">
                        <div class="p-6 flex flex-col items-center justify-center text-center h-40">
                            <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-orange-200 transition-all">
                                <svg class="w-7 h-7 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0zM6 20h12a6 6 0 00-6-6 6 6 0 00-6 6z" /></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Employees</h3>
                            <p class="text-xs text-slate-500 mt-2">Staff records</p>
                        </div>
                    </a>

                    <!-- Announcement Card -->
                    <a href="announcement.php" class="group bg-white rounded-xl shadow-md hover:shadow-lg transition-all border border-slate-200 overflow-hidden">
                        <div class="p-6 flex flex-col items-center justify-center text-center h-40">
                            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-red-200 transition-all">
                                <svg class="w-7 h-7 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 001-2.868V7a1 1 0 00-1-1h-1.468c-.596 0-1.144.321-1.433.878M9 19l3-7 3 7" /></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Announcement</h3>
                            <p class="text-xs text-slate-500 mt-2">News & updates</p>
                        </div>
                    </a>

                    <!-- Duty Roster Card -->
                    <a href="duty_roster.php" class="group bg-white rounded-xl shadow-md hover:shadow-lg transition-all border border-slate-200 overflow-hidden">
                        <div class="p-6 flex flex-col items-center justify-center text-center h-40">
                            <div class="w-14 h-14 bg-indigo-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-indigo-200 transition-all">
                                <svg class="w-7 h-7 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" /></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Duty Roster</h3>
                            <p class="text-xs text-slate-500 mt-2">Shift schedule</p>
                        </div>
                    </a>

                    <!-- GajiNow Card -->
                    <a href="gajinow.php" class="group bg-white rounded-xl shadow-md hover:shadow-lg transition-all border border-slate-200 overflow-hidden">
                        <div class="p-6 flex flex-col items-center justify-center text-center h-40">
                            <div class="w-14 h-14 bg-pink-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-pink-200 transition-all">
                                <svg class="w-7 h-7 text-pink-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">GajiNow</h3>
                            <p class="text-xs text-slate-500 mt-2">Advance salary</p>
                        </div>
                    </a>

                    <!-- Team Calendar Card -->
                    <a href="team_calendar.php" class="group bg-white rounded-xl shadow-md hover:shadow-lg transition-all border border-slate-200 overflow-hidden">
                        <div class="p-6 flex flex-col items-center justify-center text-center h-40">
                            <div class="w-14 h-14 bg-cyan-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-cyan-200 transition-all">
                                <svg class="w-7 h-7 text-cyan-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <h3 class="text-base font-bold text-slate-800">Team Calendar</h3>
                            <p class="text-xs text-slate-500 mt-2">Shared events</p>
                        </div>
                    </a>

                </div>
            </div>

            <!-- 5. SESSION INFO FOOTER -->
            <div class="bg-white rounded-xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <div>
                        <h4 class="font-bold text-slate-800">Account Information</h4>
                        <p class="text-xs text-slate-500">Your Supabase authentication session details</p>
                    </div>
                </div>
                <div class="p-6">
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200/60 font-mono text-xs text-slate-600 space-y-2 max-w-3xl">
                        <div>
                            <span class="font-semibold text-slate-400 select-none">USER_EMAIL:</span> 
                            <span class="text-slate-800"><?php echo htmlspecialchars($_SESSION['user_email']); ?></span>
                        </div>
                        <div class="border-t border-slate-200/60 pt-2">
                            <span class="font-semibold text-slate-400 select-none">USER_ID:</span> 
                            <span class="text-slate-800"><?php echo htmlspecialchars($_SESSION['user_id']); ?></span>
                        </div>
                        <div class="border-t border-slate-200/60 pt-2">
                            <span class="font-semibold text-slate-400 select-none">SESSION_TOKEN:</span> 
                            <span class="text-blue-600 break-all"><?php echo substr($_SESSION['access_token'], 0, 50); ?>... (Truncated for security)</span>
                        </div>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

</body>
</html>