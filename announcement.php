<?php
session_start();
if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit;
}

$user_email = $_SESSION['user_email'] ?? 'Staf Terdaftar';
$success = '';
$error = '';

if (!isset($_SESSION['announcements'])) {
    $_SESSION['announcements'] = [
        ['title' => 'Cuti Sekolah Penggal', 'summary' => 'Pejabat ditutup pada 8-10 Jun 2026 untuk cuti penggal.', 'date' => date('Y-m-d', strtotime('-2 days'))],
        ['title' => 'Audit Keselamatan Dalaman', 'summary' => 'Audit dijadualkan pada 15 Jun. Sila siapkan dokumentasi.', 'date' => date('Y-m-d', strtotime('-7 days'))],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $summary = trim($_POST['summary'] ?? '');

    if (!$title || !$summary) {
        $error = 'Sila masukkan tajuk dan ringkasan pengumuman.';
    } else {
        array_unshift($_SESSION['announcements'], [
            'title' => $title,
            'summary' => $summary,
            'date' => date('Y-m-d'),
        ]);
        $success = 'Pengumuman telah diterbitkan.';
    }
}

$announcements = $_SESSION['announcements'];
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Announcement - MS Smart HR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        <section class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Announcements</h1>
                    <p class="mt-2 text-sm text-slate-500">Terbitkan dan semak kemas kini penting syarikat.</p>
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
                <div class="mb-6 flex items-center justify-between">
                    <div>
                        <p class="text-sm text-slate-500">Pengumuman terkini</p>
                        <h2 class="text-3xl font-semibold text-slate-900"><?php echo count($announcements); ?></h2>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-4 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Log masuk sebagai</p>
                        <p class="mt-2 text-sm font-medium text-slate-900"><?php echo htmlspecialchars($user_email); ?></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <?php foreach ($announcements as $announcement): ?>
                        <article class="rounded-3xl border border-slate-200 p-5 bg-slate-50">
                            <div class="flex items-center justify-between gap-3">
                                <h3 class="text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($announcement['title']); ?></h3>
                                <span class="text-xs uppercase tracking-[0.18em] text-slate-500"><?php echo htmlspecialchars(date('d M Y', strtotime($announcement['date']))); ?></span>
                            </div>
                            <p class="mt-3 text-sm text-slate-600"><?php echo htmlspecialchars($announcement['summary']); ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold text-slate-900">Terbitkan Pengumuman</h2>
                <p class="mt-2 text-sm text-slate-500">Kongsikan berita atau peringatan dengan kakitangan.</p>
                <form method="post" class="mt-6 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Tajuk</span>
                        <input type="text" name="title" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: Notis Cuti Umum" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Ringkasan</span>
                        <textarea name="summary" rows="4" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: Pejabat akan ditutup pada..." /></textarea>
                    </label>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Terbitkan</button>
                </form>
            </aside>
        </div>
    </div>
</body>
</html>
