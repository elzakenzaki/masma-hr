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

if (!isset($_SESSION['employee_directory'])) {
    $_SESSION['employee_directory'] = [
        ['name' => 'Amiruddin Ahmad', 'role' => 'HR Executive', 'department' => 'Human Resources', 'status' => 'Aktif'],
        ['name' => 'Nur Afiqah', 'role' => 'Payroll Officer', 'department' => 'Kewangan', 'status' => 'Aktif'],
        ['name' => 'Siti Mariam', 'role' => 'Safety Coordinator', 'department' => 'Keselamatan', 'status' => 'Aktif'],
        ['name' => 'Faizal Ismail', 'role' => 'Admin', 'department' => 'Pentadbiran', 'status' => 'Cuti'],
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $role = trim($_POST['role'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $status = trim($_POST['status'] ?? '');

    if (!$name || !$role || !$department || !$status) {
        $error = 'Sila lengkapkan semua medan sebelum menambah pekerja baru.';
    } else {
        array_unshift($_SESSION['employee_directory'], [
            'name' => $name,
            'role' => $role,
            'department' => $department,
            'status' => $status,
        ]);
        $success = 'Pekerja baru telah ditambah ke direktori.';
    }
}

$employees = $_SESSION['employee_directory'];
?><!DOCTYPE html>
<html lang="<?php echo current_language(); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?php echo htmlspecialchars(t('employees_title')); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 py-8 px-4">
    <div class="max-w-7xl mx-auto space-y-6">
        <section class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-semibold text-slate-900">Employee Management</h1>
                    <p class="mt-2 text-sm text-slate-500">Lihat dan urus rekod staf anda di sini.</p>
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
                        <p class="text-sm text-slate-500">Jumlah pekerja</p>
                        <h2 class="text-3xl font-semibold text-slate-900"><?php echo count($employees); ?></h2>
                    </div>
                    <div class="rounded-3xl bg-slate-50 p-4 text-slate-700 border border-slate-200">
                        <p class="text-xs uppercase tracking-[0.18em] text-slate-500">Log masuk sebagai</p>
                        <p class="mt-2 text-sm font-medium text-slate-900"><?php echo htmlspecialchars($user_email); ?></p>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($employees as $employee): ?>
                        <div class="rounded-3xl border border-slate-200 p-5 bg-slate-50">
                            <p class="text-base font-semibold text-slate-900"><?php echo htmlspecialchars($employee['name']); ?></p>
                            <p class="text-sm text-slate-500"><?php echo htmlspecialchars($employee['role']); ?></p>
                            <p class="mt-3 text-sm text-slate-600">Bahagian: <?php echo htmlspecialchars($employee['department']); ?></p>
                            <span class="mt-4 inline-flex rounded-full px-3 py-1 text-xs font-semibold <?php echo $employee['status'] === 'Aktif' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-700'; ?>">
                                <?php echo htmlspecialchars($employee['status']); ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <aside class="rounded-3xl bg-white p-8 shadow-sm border border-slate-200">
                <h2 class="text-xl font-semibold text-slate-900">Tambah Pekerja Baru</h2>
                <p class="mt-2 text-sm text-slate-500">Isi maklumat pekerja untuk menambah rekod staf.</p>
                <form method="post" class="mt-6 space-y-4">
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Nama Penuh</span>
                        <input type="text" name="name" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: Ahmad Faiz" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Jawatan</span>
                        <input type="text" name="role" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: HR Officer" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Bahagian</span>
                        <input type="text" name="department" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none" placeholder="Contoh: Human Resources" />
                    </label>
                    <label class="block">
                        <span class="text-sm font-medium text-slate-700">Status</span>
                        <select name="status" class="mt-2 w-full rounded-3xl border border-slate-200 bg-white px-4 py-3 text-slate-900 shadow-sm focus:border-slate-400 focus:outline-none">
                            <option value="Aktif">Aktif</option>
                            <option value="Cuti">Cuti</option>
                            <option value="Tamat Perkhidmatan">Tamat Perkhidmatan</option>
                        </select>
                    </label>
                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-3xl bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800">Tambah Pekerja</button>
                </form>
            </aside>
        </div>
    </div>
</body>
</html>
