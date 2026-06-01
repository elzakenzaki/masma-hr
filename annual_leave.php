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
    $leave_type = trim($_POST['leave_type'] ?? '');
    $start_date = trim($_POST['start_date'] ?? '');
    $end_date = trim($_POST['end_date'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    if (!$leave_type || !$start_date || !$end_date || !$reason) {
        $error = 'Sila lengkapkan semua medan sebelum menghantar permohonan cuti.';
    } elseif ($end_date < $start_date) {
        $error = 'Tarikh akhir mesti sama atau selepas tarikh mula.';
    } else {
        $request = [
            'leave_type' => $leave_type,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'reason' => $reason,
            'status' => 'Tertunda',
            'submitted_at' => date('Y-m-d H:i'),
        ];

        if (!isset($_SESSION['leave_requests'])) {
            $_SESSION['leave_requests'] = [];
        }
        array_unshift($_SESSION['leave_requests'], $request);
        $success = 'Permohonan cuti telah dihantar. Pengurus anda akan menyemak dalam masa terdekat.';
    }
}

$leave_requests = $_SESSION['leave_requests'] ?? [
    [
        'leave_type' => 'Annual Leave',
        'start_date' => date('Y-m-d', strtotime('+5 days')),
        'end_date' => date('Y-m-d', strtotime('+9 days')),
        'reason' => 'Cuti keluarga',
        'status' => 'Diluluskan',
        'submitted_at' => date('Y-m-d H:i', strtotime('-3 days')),
    ],
    [
        'leave_type' => 'Medical Leave',
        'start_date' => date('Y-m-d', strtotime('-10 days')),
        'end_date' => date('Y-m-d', strtotime('-8 days')),
        'reason' => 'Janji temu klinik',
        'status' => 'Ditolak',
        'submitted_at' => date('Y-m-d H:i', strtotime('-15 days')),
    ],
];
?><!DOCTYPE html>
<html lang="<?php echo current_language(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo htmlspecialchars(t('annual_leave_title')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-8">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900"><?php echo htmlspecialchars(t('page_heading_annual_leave')); ?></h1>
                    <p class="mt-2 text-sm text-slate-600"><?php echo htmlspecialchars(t('page_subtitle_annual_leave')); ?></p>
                </div>
                <a href="dashboard.php" class="inline-flex items-center rounded-full bg-slate-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-slate-800">
                    <?php echo htmlspecialchars(t('back_to_dashboard')); ?>
                </a>
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

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.8fr]">
            <section class="space-y-6 rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <div class="space-y-3">
                    <p class="text-sm text-slate-500">Log masuk sebagai</p>
                    <h2 class="text-xl font-semibold text-slate-900"><?php echo htmlspecialchars($user_email); ?></h2>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Baki Cuti</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-900">12 hari</p>
                        <p class="mt-2 text-sm text-slate-500">Cuti tahunan tersedia untuk permohonan.</p>
                    </div>
                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Permohonan Tertunda</p>
                        <p class="mt-3 text-3xl font-semibold text-amber-700"><?php echo count(array_filter($leave_requests, fn($row) => $row['status'] === 'Tertunda')); ?></p>
                        <p class="mt-2 text-sm text-slate-500">Permohonan yang belum semak.</p>
                    </div>
                </div>

                <form method="post" class="space-y-6">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Jenis Cuti</span>
                            <select name="leave_type" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                                <option value="Annual Leave">Annual Leave</option>
                                <option value="Medical Leave">Medical Leave</option>
                                <option value="Compassionate Leave">Compassionate Leave</option>
                            </select>
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Tarikh Mula</span>
                            <input type="date" name="start_date" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                        </label>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Tarikh Tamat</span>
                            <input type="date" name="end_date" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                        </label>

                        <label class="block">
                            <span class="text-sm font-medium text-slate-700">Alasan</span>
                            <input type="text" name="reason" placeholder="Contoh: Cuti keluarga" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                        </label>
                    </div>

                    <button type="submit" class="inline-flex items-center justify-center rounded-3xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">
                        Hantar Permohonan
                    </button>
                </form>
            </section>

            <aside class="rounded-3xl bg-white p-6 shadow-sm border border-slate-200">
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Permohonan Terkini</p>
                        <h3 class="text-xl font-semibold text-slate-900">Sejarah Cuti</h3>
                    </div>
                </div>

                <div class="space-y-4">
                    <?php foreach ($leave_requests as $request): ?>
                        <div class="rounded-3xl border border-slate-200 p-4">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900"><?php echo htmlspecialchars($request['leave_type']); ?></p>
                                    <p class="text-xs text-slate-500">Dihantar: <?php echo htmlspecialchars($request['submitted_at']); ?></p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo $request['status'] === 'Tertunda' ? 'bg-amber-100 text-amber-700' : ($request['status'] === 'Diluluskan' ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700'); ?>">
                                    <?php echo htmlspecialchars($request['status']); ?>
                                </span>
                            </div>
                            <div class="mt-4 text-sm text-slate-600">
                                <p><strong>Tempoh:</strong> <?php echo htmlspecialchars($request['start_date']); ?> hingga <?php echo htmlspecialchars($request['end_date']); ?></p>
                                <p class="mt-2"><strong>Alasan:</strong> <?php echo htmlspecialchars($request['reason']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </aside>
        </div>
    </div>
</body>
</html>
