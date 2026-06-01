<?php
session_start();
require_once 'config/lang.php';
require_once 'config/supabase.php';

// Route Guard: Redirect straight to dashboard if token session is already active
if (isset($_SESSION['access_token'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($email) && !empty($password)) {
        $payload = [
            'email' => $email,
            'password' => $password
        ];

        // Request authentication token from Supabase Auth service
        $response = supabase_api_request('/auth/v1/token?grant_type=password', $payload);

        if ($response['status'] === 200 && isset($response['data']['access_token'])) {
            // Save authenticated session parameters locally
            $_SESSION['access_token'] = $response['data']['access_token'];
            $_SESSION['user_email']   = $response['data']['user']['email'];
            $_SESSION['user_id']      = $response['data']['user']['id'];
            
            header("Location: dashboard.php");
            exit;
        } else {
            // Fetch descriptive error message from API response
            $error = $response['data']['error_description'] ?? ($response['data']['msg'] ?? 'Authentication failed.');
        }
    } else {
        $error = 'Please fill in both email and password fields.';
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo current_language(); ?>" class="h-full bg-white">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(t('sign_in_title')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="h-full antialiased">
<div class="flex min-h-full">
    
    <div class="relative hidden w-0 flex-1 lg:flex flex-col justify-between bg-gradient-to-br from-[#1239a1] via-[#0262b5] to-[#00bda7] p-16 text-white">
        <div class="flex items-center space-x-4">
            <div class="h-16 w-16 bg-white rounded-full flex items-center justify-center shadow-md p-1">
                <img src="assets/images/MASMA Northern (1).png" alt="MASMA Safety Logo" class="h-full w-full object-contain">
            </div>
            <div class="leading-none">
                <span class="block text-2xl font-black tracking-wider text-white">MASMA</span>
                <span class="text-xs tracking-widest text-teal-200 uppercase font-semibold">Safety</span>
            </div>
        </div>

        <div class="max-w-xl space-y-6 my-auto">
            <h1 class="text-5xl font-extrabold tracking-tight leading-tight">MS Smart Management HR</h1>
            <p class="text-xl text-teal-50/90 font-light">Complete Safety & HR Management Solution</p>
            
            <ul class="space-y-4 pt-4 text-base font-medium text-white/95">
                <li class="flex items-center space-x-3">
                    <svg class="h-6 w-6 text-teal-300 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    <span>Comprehensive HR & Safety Management</span>
                </li>
                <li class="flex items-center space-x-3">
                    <svg class="h-6 w-6 text-teal-300 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    <span>HRDF/HRDC Compliance & Tracking</span>
                </li>
                <li class="flex items-center space-x-3">
                    <svg class="h-6 w-6 text-teal-300 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    <span>Real-time Attendance & Payroll</span>
                </li>
                <li class="flex items-center space-x-3">
                    <svg class="h-6 w-6 text-teal-300 stroke-[2.5]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                    <span>Cloud-based & Secure</span>
                </li>
            </ul>
        </div>

        <div class="border-t border-white/10 pt-6">
            <p class="text-sm text-teal-100/70">Trusted by 500+ companies across Malaysia</p>
        </div>
    </div>

    <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-24 xl:px-32 bg-[#fcfdfe]">
        <div class="mx-auto w-full max-w-sm lg:w-96 bg-white p-8 sm:p-10 rounded-2xl shadow-xl shadow-slate-100 border border-slate-100/60">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-3xl font-bold tracking-tight text-slate-800"><?php echo htmlspecialchars(t('welcome_back')); ?></h2>
                    <p class="mt-2 text-sm text-slate-500"><?php echo htmlspecialchars(t('signin_subtitle')); ?></p>
                </div>
                <div class="text-xs font-medium text-slate-500">
                    <span><?php echo htmlspecialchars(t('language_switch')); ?>:</span>
                    <a href="<?php echo lang_url('en'); ?>" class="ml-2 text-blue-600 hover:text-blue-700">EN</a>
                    <span class="text-slate-300">|</span>
                    <a href="<?php echo lang_url('ms'); ?>" class="text-blue-600 hover:text-blue-700">BM</a>
                </div>
            </div>

            <div class="mt-8">
                <?php if (!empty($error)): ?>
                    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-xs font-medium text-red-600">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form action="index.php" method="POST" class="space-y-5">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider"><?php echo htmlspecialchars(t('email_address')); ?></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 002-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <input id="email" name="email" type="email" required placeholder="your.email@company.com" 
                                class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all"
                                value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider"><?php echo htmlspecialchars(t('password')); ?></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </div>
                            <input id="password" name="password" type="password" required placeholder="<?php echo htmlspecialchars(t('password_placeholder')); ?>" 
                                class="block w-full pl-10 pr-10 py-3 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                <button type="button" id="togglePassword" class="text-slate-400 hover:text-slate-600 focus:outline-none">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" id="eyeIcon">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 text-blue-600 focus:ring-blue-500/20 border-slate-300 rounded transition-all">
                            <label for="remember" class="ml-2 block text-xs font-medium text-slate-500"><?php echo htmlspecialchars(t('remember_me')); ?></label>
                        </div>
                        <div>
                            <a href="#" class="text-xs font-semibold text-blue-600 hover:text-blue-700 transition-all"><?php echo htmlspecialchars(t('forgot_password')); ?></a>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-teal-500 hover:opacity-95 shadow-md shadow-blue-500/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <?php echo htmlspecialchars(t('sign_in_button')); ?>
                        </button>
                    </div>
                </form>

                <div class="mt-6 relative">
                    <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-100"></div></div>
                    <div class="relative flex justify-center text-xs font-medium uppercase">
                        <span class="bg-white px-3 text-slate-400"><?php echo htmlspecialchars(t('new_to_masma')); ?></span>
                    </div>
                </div>

                <div class="mt-6">
                    <a href="register.php" class="w-full flex justify-center py-3 px-4 border border-blue-600 rounded-lg text-sm font-semibold text-blue-600 bg-white hover:bg-blue-50/30 transition-all text-center">
                        <?php echo htmlspecialchars(t('create_account')); ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="mt-8 flex flex-col items-center justify-center space-y-1 text-center text-xs text-slate-400">
            <span class="flex items-center space-x-1 font-medium">
                <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                <span>Secured by 256-bit SSL encryption</span>
            </span>
            <span class="pt-4 text-[10px] tracking-wider">&copy; 2026 MASMA Safety HR System. All rights reserved.</span>
        </div>
    </div>
</div>

<script>
    // Reveal/Hide raw password logic inside DOM
    document.getElementById('togglePassword').addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />`;
        } else {
            passwordInput.type = 'password';
            eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
        }
    });
</script>
</body>
</html>