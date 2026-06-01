<?php
session_start();
if (!isset($_SESSION['access_token'])) {
    header('Location: index.php');
    exit;
}

$user_email = $_SESSION['user_email'] ?? 'Staf Terdaftar';
$success = '';
$error = '';

if (!isset($_SESSION['claims_requests'])) {
    $_SESSION['claims_requests'] = [
        ['amount' => '250.00', 'category' => 'Elaun Jalan', 'status' => 'Diluluskan', 'submitted_at' => date('Y-m-d H:i', strtotime('-10 days')), 'notes' => 'Bayaran balik elaun perjalanan telah diproses.'],
        ['amount' => '120.00', 'category' => 'Elaun Makan', 'status' => 'Tertunda', 'submitted_at' => date('Y-m-d H:i', strtotime('-2 days')), 'notes' => 'Sila hantar resit makan untuk semakan.'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = trim($_POST['amount'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (!$amount || !$category || !$notes) {
        $error = 'Sila lengkapkan semua medan permohonan tuntutan.';
    } elseif (!is_numeric($amount) || $amount <= 0) {
        $error = 'Sila masukkan jumlah yang sah.';
    } else {
        array_unshift($_SESSION['claims_requests'], [
            'amount' => number_format((float)$amount, 2),
            'category' => $category,
            'status' => 'Tertunda',
            'submitted_at' => date('Y-m-d H:i'),
            'notes' => $notes,
        ]);
        $success = 'Permohonan tuntutan telah dihantar.';
    }
}

$claims = $_SESSION['claims_requests'];
$pending_count = count(array_filter($claims, fn($item) => $item['status'] === 'Tertunda'));
$total_amount = array_sum(array_map(fn($item) => (float)$item['amount'], $claims));
?><!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tuntutan Kewangan - MS Smart HR</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        <section class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Tuntutan Kewangan</h1>
                    <p class="mt-2 text-sm text-slate-500">Kendalikan tuntutan kewangan dan semak status pengesahan.</p>
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

        <div class="grid gap-6 xl:grid-cols-[1.2fr_0.9fr]">
            <section class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <div class="grid gap-4 sm:grid-cols-3 mb-6">
                    <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Tuntutan Tertunda</p>
                        <p class="mt-3 text-3xl font-semibold text-amber-700"><?php echo $pending_count; ?></p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Jumlah Tuntutan</p>
                        <p class="mt-3 text-3xl font-semibold text-slate-900">RM <?php echo number_format($total_amount, 2); ?></p>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-5 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Log masuk sebagai</p>
                        <p class="mt-3 text-sm font-medium text-slate-900"><?php echo htmlspecialchars($user_email); ?></p>
                    </div>
                </div>

                <div class="space-y-4">
                    <?php foreach ($claims as $claim): ?>
                        <div class="rounded-3xl border border-slate-200 p-5 bg-slate-50">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-lg font-semibold text-slate-900"><?php echo htmlspecialchars($claim['category']); ?> — RM <?php echo htmlspecialchars($claim['amount']); ?></p>
                                    <p class="mt-1 text-sm text-slate-500">Dihantar: <?php echo htmlspecialchars($claim['submitted_at']); ?></p>
                                </div>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold <?php echo $claim['status'] === 'Tertunda' ? 'bg-amber-100 text-amber-700' : 'bg-emerald-100 text-emerald-700'; ?>">
                                    <?php echo htmlspecialchars($claim['status']); ?>
                                </span>
                            </div>
                            <p class="mt-3 text-sm text-slate-600"><?php echo htmlspecialchars($claim['notes'] ?? 'Tiada nota ditambah.'); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>

            <aside class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold text-slate-900">Hantar Tuntutan Baru</h2>
                <p class="mt-2 text-sm text-slate-500">Isi maklumat tuntutan untuk semakan HR.</p>
                <form method="post" class="mt-6 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Jumlah (RM)</span>
                        <input type="text" name="amount" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: 120" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Kategori</span>
                        <select name="category" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                            <option value="">Pilih kategori</option>
                            <option value="Elaun Jalan">Elaun Jalan</option>
                            <option value="Elaun Makan">Elaun Makan</option>
                            <option value="Elaun Perjalanan">Elaun Perjalanan</option>
                        </select>
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Nota</span>
                        <textarea name="notes" rows="4" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Terangkan tujuan tuntutan..."></textarea>
                    </label>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Hantar Tuntutan</button>
                </form>
            </aside>
        </div>
    </div>
</body>
</html>
