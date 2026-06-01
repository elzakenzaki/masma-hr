<?php
session_start();
require_once 'config/lang.php';
require_once 'config/supabase.php';

if (isset($_SESSION['access_token'])) {
    header("Location: dashboard.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirmPassword = trim($_POST['confirm_password'] ?? '');

    if (empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'Please complete all fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {
        $payload = [
            'email' => $email,
            'password' => $password,
        ];

        $response = supabase_api_request('/auth/v1/signup', $payload);

        if (in_array($response['status'], [200, 201], true)) {
            if (!empty($response['data']['access_token'])) {
                $_SESSION['access_token'] = $response['data']['access_token'];
                $_SESSION['user_email'] = $response['data']['user']['email'] ?? $email;
                $_SESSION['user_id'] = $response['data']['user']['id'] ?? null;
                header('Location: dashboard.php');
                exit;
            }

            $success = 'Registration successful. Please check your email to confirm your account.';
        } else {
            $error = $response['data']['error_description'] ?? $response['data']['msg'] ?? 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo current_language(); ?>" class="h-full bg-[#ebf8ff]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars(t('register_title')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; background-color: #ebf8ff; }
    </style>
</head>
<body class="h-full antialiased">
<div class="flex min-h-full">
    <div class="flex flex-1 flex-col justify-center px-4 py-12 sm:px-6 lg:flex-none lg:px-24 xl:px-32 bg-[#fcfdfe]">
        <div class="mx-auto w-full max-w-sm lg:w-96 bg-white p-8 sm:p-10 rounded-2xl shadow-xl shadow-slate-100 border border-slate-100/60">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-800"><?php echo htmlspecialchars(t('registration_header')); ?></h2>
                <p class="mt-2 text-sm text-slate-500"><?php echo htmlspecialchars(t('registration_subtitle')); ?></p>
            </div>

            <div class="mt-8">
                <?php if (!empty($error)): ?>
                <div class="text-xs font-medium text-slate-500">
                    <span><?php echo htmlspecialchars(t('language_switch')); ?>:</span>
                    <a href="<?php echo lang_url('en'); ?>" class="ml-2 text-blue-600 hover:text-blue-700">EN</a>
                    <span class="text-slate-300">|</span>
                    <a href="<?php echo lang_url('ms'); ?>" class="text-blue-600 hover:text-blue-700">BM</a>
                </div>
                    <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-xs font-medium text-red-600">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="mb-4 p-3 rounded-lg bg-green-50 border border-green-200 text-xs font-medium text-green-600">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST" class="space-y-5">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider"><?php echo htmlspecialchars(t('email_address')); ?></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
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
                                class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all">
                        </div>
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-xs font-semibold text-slate-600 uppercase tracking-wider"><?php echo htmlspecialchars(t('confirm_password')); ?></label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" /></svg>
                            </div>
                            <input id="confirm_password" name="confirm_password" type="password" required placeholder="<?php echo htmlspecialchars(t('confirm_password_placeholder')); ?>"
                                class="block w-full pl-10 pr-3 py-3 border border-slate-200 rounded-lg text-slate-800 placeholder-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-all">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-lg text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-teal-500 hover:opacity-95 shadow-md shadow-blue-500/10 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all">
                            <?php echo htmlspecialchars(t('create_account')); ?>
                        </button>
                    </div>
                </form>

                <div class="mt-6 text-center text-sm text-slate-500">
                    <?php echo htmlspecialchars(t('already_member')); ?> <a href="index.php" class="font-semibold text-blue-600 hover:text-blue-700"><?php echo htmlspecialchars(t('sign_in_link')); ?></a>
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
</body>
</html>
