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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $request_amount = trim($_POST['request_amount'] ?? '');
    $request_month = trim($_POST['request_month'] ?? '');
    $request_reason = trim($_POST['request_reason'] ?? '');

    if (!$request_amount || !$request_month || !$request_reason) {
        $error = 'Sila lengkapkan semua medan sebelum menghantar permohonan.';
    } elseif (!is_numeric($request_amount) || $request_amount <= 0) {
        $error = 'Sila masukkan jumlah yang sah untuk permohonan.';
    } else {
        $payroll_request = [
            'amount' => number_format((float)$request_amount, 2),
            'month' => $request_month,
            'reason' => $request_reason,
            'status' => 'Tertunda',
            'submitted_at' => date('Y-m-d H:i'),
        ];

        if (!isset($_SESSION['payroll_requests'])) {
            $_SESSION['payroll_requests'] = [];
        }
        array_unshift($_SESSION['payroll_requests'], $payroll_request);
        $success = 'Permohonan payroll telah dihantar dan sedang menunggu semakan.';
    }
}

$payroll_requests = $_SESSION['payroll_requests'] ?? [
    [
        'amount' => '2560.00',
        'month' => 'Mei 2026',
        'reason' => 'Cukai bulanan',
        'status' => 'Diluluskan',
        'submitted_at' => date('Y-m-d H:i', strtotime('-15 days')),
    ],
    [
        'amount' => '2490.00',
        'month' => 'April 2026',
        'reason' => 'Gaji bulanan',
        'status' => 'Diluluskan',
        'submitted_at' => date('Y-m-d H:i', strtotime('-45 days')),
    ],
];

$pay_history = [
    ['month' => 'Mei 2026', 'gross' => '3000.00', 'deductions' => '440.00', 'net' => '2560.00', 'date' => date('Y-m-d', strtotime('-10 days'))],
    ['month' => 'April 2026', 'gross' => '3000.00', 'deductions' => '510.00', 'net' => '2490.00', 'date' => date('Y-m-d', strtotime('-40 days'))],
    ['month' => 'Mac 2026', 'gross' => '3000.00', 'deductions' => '470.00', 'net' => '2530.00', 'date' => date('Y-m-d', strtotime('-70 days'))],
];

$pending_requests = count(array_filter($payroll_requests, fn($row) => $row['status'] === 'Tertunda'));
$latest_salary = $pay_history[0];
?><!DOCTYPE html>
<html lang="<?php echo current_language(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo htmlspecialchars(t('payroll_title')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Payroll</h1>
                    <p class="mt-2 text-sm text-slate-600">Urus permohonan payroll dan lihat ringkasan gaji anda di sini.</p>
                </div>
                <a href="dashboard.php" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">← Kembali ke papan pemuka</a>
            </div>
        </div>

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

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.9fr]">
            <section class="space-y-6 rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <div class="grid gap-4 sm:grid-cols-3">
                    <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Gaji Bersih</p>
                        <p class="mt-4 text-3xl font-semibold text-slate-900">RM <?php echo htmlspecialchars($latest_salary['net']); ?></p>
                        <p class="mt-2 text-sm text-slate-500">Bulan <?php echo htmlspecialchars($latest_salary['month']); ?></p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Tarikh Pembayaran</p>
                        <p class="mt-4 text-3xl font-semibold text-slate-900"><?php echo htmlspecialchars($latest_salary['date']); ?></p>
                        <p class="mt-2 text-sm text-slate-500">Diluluskan dan dipindahkan</p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Permohonan Tertunda</p>
                        <p class="mt-4 text-3xl font-semibold text-amber-700"><?php echo $pending_requests; ?></p>
                        <p class="mt-2 text-sm text-slate-500">Menunggu pengesahan HR</p>
                    </div>
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Gaji Kasar</p>
                        <p class="mt-4 text-2xl font-semibold text-slate-900">RM <?php echo htmlspecialchars($latest_salary['gross']); ?></p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Potongan</p>
                        <p class="mt-4 text-2xl font-semibold text-slate-900">RM <?php echo htmlspecialchars($latest_salary['deductions']); ?></p>
                    </div>
                </div>

                <div class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200">
                    <h2 class="text-xl font-semibold text-slate-900">Permohonan Payroll</h2>
                    <p class="mt-2 text-sm text-slate-500">Hantar permohonan untuk bayaran semula, penyesuaian atau pertanyaan payroll.</p>
                    <form method="post" class="mt-6 space-y-5">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">Jumlah (RM)</span>
                                <input type="text" name="request_amount" placeholder="2500" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">Bulan</span>
                                <select name="request_month" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                    <option value="">Pilih bulan</option>
                                    <option value="Jun 2026">Jun 2026</option>
                                    <option value="Mei 2026">Mei 2026</option>
                                    <option value="April 2026">April 2026</option>
                                </select>
                            </label>
                            <label class="block">
                                <span class="text-sm font-medium text-slate-700">Jenis</span>
                                <select name="request_reason" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                    <option value="">Pilih jenis</option>
                                    <option value="Cukai bulanan">Cukai bulanan</option>
                                    <option value="Klaim elaun">Klaim elaun</option>
                                    <option value="Pertanyaan gaji">Pertanyaan gaji</option>
                                </select>
                            </label>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center rounded-3xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                            Hantar Permohonan
                        </button>
                    </form>
                </div>
            </section>

            <aside class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Ringkasan Gaji</p>
                        <h2 class="text-xl font-semibold text-slate-900">Sejarah Bayaran</h2>
                    </div>
                </div>

                <div class="space-y-4">
                    <?php foreach ($pay_history as $history): ?>
                        <div class="rounded-3xl border border-slate-200 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900"><?php echo htmlspecialchars($history['month']); ?></p>
                                    <p class="text-xs text-slate-500">Tarikh pembayaran: <?php echo htmlspecialchars($history['date']); ?></p>
                                </div>
                                <p class="text-sm font-semibold text-slate-900">RM <?php echo htmlspecialchars($history['net']); ?></p>
                            </div>
                            <div class="mt-3 text-sm text-slate-600">
                                <p>Gaji kasar: RM <?php echo htmlspecialchars($history['gross']); ?></p>
                                <p>Potongan: RM <?php echo htmlspecialchars($history['deductions']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="mt-8">
                    <h3 class="text-sm font-semibold uppercase tracking-[0.24em] text-slate-500">Permohonan Payroll Terkini</h3>
                    <div class="mt-4 space-y-3">
                        <?php foreach ($payroll_requests as $request): ?>
                            <div class="rounded-3xl border border-slate-200 p-4 bg-slate-50">
                                <div class="flex items-center justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">RM <?php echo htmlspecialchars($request['amount']); ?> — <?php echo htmlspecialchars($request['month']); ?></p>
                                        <p class="text-xs text-slate-500">Dihantar: <?php echo htmlspecialchars($request['submitted_at']); ?></p>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo $request['status'] === 'Tertunda' ? 'bg-amber-100 text-amber-700' : ($request['status'] === 'Diluluskan' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'); ?>">
                                        <?php echo htmlspecialchars($request['status']); ?>
                                    </span>
                                </div>
                                <p class="mt-3 text-sm text-slate-600"><?php echo htmlspecialchars($request['reason']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</body>
</html>
