<?php
session_start();
if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit;
}

$user_email = $_SESSION['user_email'] ?? 'Staf Terdaftar';
$success = '';
$error = '';

if (!isset($_SESSION['logbook_entries'])) {
    $_SESSION['logbook_entries'] = [
        ['date' => date('Y-m-d', strtotime('-1 day')), 'task' => 'Semak senarai PPE', 'status' => 'Selesai'],
        ['date' => date('Y-m-d'), 'task' => 'Audit tapak kerja', 'status' => 'Dalam Proses'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = trim($_POST['date'] ?? '');
    $task = trim($_POST['task'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if (!$date || !$task || !$status) {
        $error = 'Sila lengkapkan semua medan entri logbook.';
    } else {
        array_unshift($_SESSION['logbook_entries'], [
            'date' => $date,
            'task' => $task,
            'status' => $status,
        ]);
        $success = 'Entri logbook baru telah disimpan.';
    }
}

$entries = $_SESSION['logbook_entries'];
?><!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logbook Harian - MS Smart HR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        <section class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Logbook Harian</h1>
                    <p class="mt-2 text-sm text-slate-500">Rekodkan aktiviti harian dan kes progres tugasan keselamatan.</p>
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

        <div class="grid gap-6 lg:grid-cols-[1.3fr_0.9fr]">
            <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <div class="mb-6 flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-slate-500">Entri logbook</p>
                        <h2 class="text-3xl font-semibold text-slate-900"><?php echo count($entries); ?></h2>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Log masuk sebagai</p>
                        <p class="mt-2 text-sm font-medium text-slate-900"><?php echo htmlspecialchars($user_email); ?></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <?php foreach ($entries as $entry): ?>
                        <div class="rounded-3xl border border-slate-200 p-5 bg-slate-50">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($entry['task']); ?></p>
                                    <p class="mt-1 text-sm text-slate-500">Tarikh: <?php echo htmlspecialchars(date('d M Y', strtotime($entry['date']))); ?></p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo $entry['status'] === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700'; ?>">
                                    <?php echo htmlspecialchars($entry['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold text-slate-900">Tambah Entri Baru</h2>
                <p class="mt-2 text-sm text-slate-500">Simpan tugas atau pemeriksaan harian dengan cepat.</p>
                <form method="post" class="mt-6 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Tarikh</span>
                        <input type="date" name="date" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Tugasan / Catatan</span>
                        <input type="text" name="task" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: Semak lampu keselamatan" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Status</span>
                        <select name="status" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                            <option value="Dalam Proses">Dalam Proses</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </label>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Simpan Entri</button>
                </form>
            </aside>
        </div>
    </div>
</body>
</html>
