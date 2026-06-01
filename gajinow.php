<?php
session_start();
require_once 'config/lang.php';
if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit;
}

$user_email = $_SESSION['user_email'] ?? 'Staf Terdaftar';
$success = '';
$error = '';

if (!isset($_SESSION['gaji_advance_requests'])) {
    $_SESSION['gaji_advance_requests'] = [
        ['amount' => '800.00', 'reason' => 'Kecemasan rumah', 'status' => 'Diluluskan', 'submitted_at' => date('Y-m-d H:i', strtotime('-8 days'))],
        ['amount' => '600.00', 'reason' => 'Kos perubatan', 'status' => 'Tertunda', 'submitted_at' => date('Y-m-d H:i', strtotime('-2 days'))],
    ];
}

$available_limit = 1500.00;
$used_limit = array_sum(array_map(fn($item) => (float)$item['amount'], $_SESSION['gaji_advance_requests']));
$remaining_limit = max(0, $available_limit - $used_limit);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    if (!$amount || !$reason) {
        $error = 'Sila masukkan jumlah dan alasan.';
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $error = 'Jumlah mesti nombor positif.';
    } elseif ((float)$amount > $remaining_limit) {
        $error = 'Jumlah melebihi had permintaan anda.';
    } else {
        array_unshift($_SESSION['gaji_advance_requests'], [
            'amount' => number_format((float)$amount, 2),
            'reason' => $reason,
            'status' => 'Tertunda',
            'submitted_at' => date('Y-m-d H:i'),
        ]);
        $success = 'Permohonan GajiNow anda telah dihantar.';
        $remaining_limit -= (float)$amount;
    }
}

$requests = $_SESSION['gaji_advance_requests'];
?><!DOCTYPE html>
<html lang="<?php echo current_language(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo htmlspecialchars(t('gajinow_title')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-6xl mx-auto space-y-6">
        <section class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">GajiNow</h1>
                    <p class="mt-2 text-sm text-slate-500">Mohon pendahuluan gaji segera dan jejak status permohonan anda.</p>
                </div>
                <a href="dashboard.php" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">← Kembali ke papan pemuka</a>
            </div>
        </section>

        <?php if ($success): ?>
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800">
                <strong>Berjaya:</strong> <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-4 text-amber-900">
                <strong>Ralat:</strong> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Had Bulanan</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-900">RM <?php echo number_format($available_limit, 2); ?></p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Digunakan</p>
                        <p class="mt-3 text-3xl font-semibold text-amber-700">RM <?php echo number_format($used_limit, 2); ?></p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Baki</p>
                        <p class="mt-3 text-3xl font-semibold text-emerald-700">RM <?php echo number_format($remaining_limit, 2); ?></p>
                    </div>
                </div>

                <div class="mt-8">
                    <h2 class="text-xl font-semibold text-slate-900">Permohonan Terbaru</h2>
                    <div class="mt-4 space-y-4">
                        <?php foreach ($requests as $request): ?>
                            <div class="rounded-3xl border border-slate-200 p-4 bg-slate-50">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">RM <?php echo htmlspecialchars($request['amount']); ?></p>
                                        <p class="text-xs text-slate-500">Dihantar: <?php echo htmlspecialchars($request['submitted_at']); ?></p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo $request['status'] === 'Tertunda' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'; ?>">
                                        <?php echo htmlspecialchars($request['status']); ?>
                                    </span>
                                </div>
                                <p class="mt-3 text-sm text-slate-600"><?php echo htmlspecialchars($request['reason']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <aside class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold text-slate-900">Hantar Permohonan</h2>
                <p class="mt-2 text-sm text-slate-500">Isi butiran untuk meminta pendahuluan gaji.</p>
                <form method="post" class="mt-6 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Jumlah (RM)</span>
                        <input type="text" name="amount" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: 500" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Sebab</span>
                        <input type="text" name="reason" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: Kos kecemasan" />
                    </label>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Hantar Permohonan</button>
                </form>
            </aside>
        </div>
    </div>
</body>
</html>
