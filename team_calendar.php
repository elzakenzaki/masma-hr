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

if (!isset($_SESSION['team_calendar_events'])) {
    $_SESSION['team_calendar_events'] = [
        ['title' => 'Mesyuarat Mingguan', 'date' => date('Y-m-d', strtotime('+1 day')), 'time' => '10:00', 'location' => 'Bilik Mesyuarat A'],
        ['title' => 'Latihan Keselamatan', 'date' => date('Y-m-d', strtotime('+3 days')), 'time' => '14:00', 'location' => 'Auditorium'],
        ['title' => 'Penyerahan Laporan', 'date' => date('Y-m-d', strtotime('+5 days')), 'time' => '09:00', 'location' => 'Ofis Utama'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $date = trim($_POST['date'] ?? '');
    $time = trim($_POST['time'] ?? '');
    $location = trim($_POST['location'] ?? '');

    if (!$title || !$date || !$time || !$location) {
        $error = 'Sila lengkapkan semua medan untuk menambah acara.';
    } else {
        array_unshift($_SESSION['team_calendar_events'], [
            'title' => $title,
            'date' => $date,
            'time' => $time,
            'location' => $location,
        ]);
        $success = 'Acara pasukan telah ditambah ke kalendar.';
    }
}

$events = $_SESSION['team_calendar_events'];
?><!DOCTYPE html>
<html lang="<?php echo current_language(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo htmlspecialchars(t('calendar_title')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        <section class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Team Calendar</h1>
                    <p class="mt-2 text-sm text-slate-500">Rancang acara dan kesesuaian pasukan dengan kalendar berkongsi.</p>
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
                <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Acara akan datang</p>
                        <h2 class="text-3xl font-semibold text-slate-900"><?php echo count($events); ?></h2>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Log masuk sebagai</p>
                        <p class="mt-2 text-sm font-medium text-slate-900"><?php echo htmlspecialchars($user_email); ?></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <?php foreach ($events as $event): ?>
                        <div class="rounded-3xl border border-slate-200 p-5 bg-slate-50">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($event['title']); ?></p>
                                    <p class="text-sm text-slate-500"><?php echo htmlspecialchars($event['location']); ?></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-semibold text-slate-900"><?php echo htmlspecialchars(date('d M Y', strtotime($event['date']))); ?></p>
                                    <p class="text-xs text-slate-500"><?php echo htmlspecialchars($event['time']); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold text-slate-900">Tambah Acara</h2>
                <p class="mt-2 text-sm text-slate-500">Sertakan tarikh, masa dan lokasi acara pasukan baru.</p>
                <form method="post" class="mt-6 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Tajuk Acara</span>
                        <input type="text" name="title" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: Sesi Latihan" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Tarikh</span>
                        <input type="date" name="date" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Masa</span>
                        <input type="time" name="time" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Lokasi</span>
                        <input type="text" name="location" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: Bilik Mesyuarat" />
                    </label>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Tambah Acara</button>
                </form>
            </aside>
        </div>
    </div>
</body>
</html>
