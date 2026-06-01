<?php
session_start();
if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit;
}

$user_email = $_SESSION['user_email'] ?? 'Staf Terdaftar';
$success = '';
$error = '';

if (!isset($_SESSION['duty_roster'])) {
    $_SESSION['duty_roster'] = [
        ['day' => 'Isnin', 'shift' => '08:00 - 16:00', 'employee' => 'Amiruddin Ahmad'],
        ['day' => 'Selasa', 'shift' => '10:00 - 18:00', 'employee' => 'Siti Mariam'],
        ['day' => 'Rabu', 'shift' => '08:00 - 16:00', 'employee' => 'Nur Afiqah'],
        ['day' => 'Khamis', 'shift' => '12:00 - 20:00', 'employee' => 'Faizal Ismail'],
        ['day' => 'Jumaat', 'shift' => '08:00 - 16:00', 'employee' => 'Amiruddin Ahmad'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $day = trim($_POST['day'] ?? '');
    $shift = trim($_POST['shift'] ?? '');
    $employee = trim($_POST['employee'] ?? '');

    if (!$day || !$shift || !$employee) {
        $error = 'Sila lengkapkan semua medan untuk menambah jadual roaster.';
    } else {
        array_unshift($_SESSION['duty_roster'], [
            'day' => $day,
            'shift' => $shift,
            'employee' => $employee,
        ]);
        $success = 'Tugasan roster baru telah disimpan.';
    }
}

$roster = $_SESSION['duty_roster'];
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Duty Roster - MS Smart HR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        <section class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Duty Roster</h1>
                    <p class="mt-2 text-sm text-slate-500">Semak jadual syif mingguan dan kemas kini tugasan roster.</p>
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

        <div class="grid gap-6 xl:grid-cols-[1.3fr_0.9fr]">
            <div class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold text-slate-900">Jadual Syif Mingguan</h2>
                <div class="mt-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-sm">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="px-4 py-3 font-semibold uppercase tracking-[0.16em]">Hari</th>
                                <th class="px-4 py-3 font-semibold uppercase tracking-[0.16em]">Syif</th>
                                <th class="px-4 py-3 font-semibold uppercase tracking-[0.16em]">Nama Pekerja</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            <?php foreach ($roster as $row): ?>
                                <tr>
                                    <td class="px-4 py-4 text-slate-900"><?php echo htmlspecialchars($row['day']); ?></td>
                                    <td class="px-4 py-4 text-slate-700"><?php echo htmlspecialchars($row['shift']); ?></td>
                                    <td class="px-4 py-4 text-slate-700"><?php echo htmlspecialchars($row['employee']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold text-slate-900">Tambah Tugasan Baru</h2>
                <p class="mt-2 text-sm text-slate-500">Serahkan jadual syif untuk hari atau minggu hadapan.</p>
                <form method="post" class="mt-6 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Hari</span>
                        <select name="day" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                            <option value="">Pilih hari</option>
                            <option value="Isnin">Isnin</option>
                            <option value="Selasa">Selasa</option>
                            <option value="Rabu">Rabu</option>
                            <option value="Khamis">Khamis</option>
                            <option value="Jumaat">Jumaat</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Syif</span>
                        <input type="text" name="shift" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: 09:00 - 17:00" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Nama Pekerja</span>
                        <input type="text" name="employee" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: Siti Mariam" />
                    </label>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Simpan Tugasan</button>
                </form>
            </aside>
        </div>
    </div>
</body>
</html>
