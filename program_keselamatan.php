<?php
session_start();
if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit;
}

$user_email = $_SESSION['user_email'] ?? 'Staf Terdaftar';
$success = '';
$error = '';

if (!isset($_SESSION['safety_programs'])) {
    $_SESSION['safety_programs'] = [
        ['name' => 'SHRP', 'status' => 'Aktif', 'due_date' => '2026-12-31'],
        ['name' => 'MHFA', 'status' => 'Dalam Latihan', 'due_date' => '2026-08-15'],
        ['name' => 'HIRARC', 'status' => 'Selesai', 'due_date' => '2026-06-01'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $program_name = trim($_POST['program_name'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $due_date = trim($_POST['due_date'] ?? '');

    if (!$program_name || !$status || !$due_date) {
        $error = 'Sila lengkapkan semua medan untuk menambah program keselamatan.';
    } else {
        array_unshift($_SESSION['safety_programs'], [
            'name' => $program_name,
            'status' => $status,
            'due_date' => $due_date,
        ]);
        $success = 'Program keselamatan baru telah disimpan.';
    }
}

$programs = $_SESSION['safety_programs'];
?><!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Keselamatan - MS Smart HR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-6xl mx-auto space-y-6">
        <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Program Keselamatan</h1>
                    <p class="mt-2 text-sm text-slate-500">Uruskan program keselamatan MASMA dan jejak tarikh akhir latihan.</p>
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

        <div class="grid gap-6 lg:grid-cols-[1.3fr_0.9fr]">
            <section class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <div>
                        <p class="text-sm text-slate-500">Program keselamatan aktif</p>
                        <h2 class="text-3xl font-semibold text-slate-900"><?php echo count($programs); ?></h2>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200 text-right">
                        <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Log masuk sebagai</p>
                        <p class="mt-2 text-sm font-medium text-slate-900"><?php echo htmlspecialchars($user_email); ?></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <?php foreach ($programs as $program): ?>
                        <div class="rounded-3xl border border-slate-200 p-5 bg-slate-50">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($program['name']); ?></p>
                                    <p class="mt-1 text-sm text-slate-600">Tarikh akhir: <?php echo htmlspecialchars(date('d M Y', strtotime($program['due_date']))); ?></p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo $program['status'] === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : ($program['status'] === 'Selesai' ? 'bg-slate-100 text-slate-700' : 'bg-amber-100 text-amber-700'); ?>">
                                    <?php echo htmlspecialchars($program['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <aside class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold text-slate-900">Tambah Program Baru</h2>
                <p class="mt-2 text-sm text-slate-500">Masukkan program keselamatan baru untuk rekod.</p>
                <form method="post" class="mt-6 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Nama Program</span>
                        <input type="text" name="program_name" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: SHRP" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Status</span>
                        <select name="status" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                            <option value="Aktif">Aktif</option>
                            <option value="Dalam Latihan">Dalam Latihan</option>
                            <option value="Selesai">Selesai</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Tarikh Akhir</span>
                        <input type="date" name="due_date" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" />
                    </label>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Simpan Program</button>
                </form>
            </aside>
        </div>
    </div>
</body>
</html>
