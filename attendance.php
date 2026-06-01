<?php
session_start();
if (!isset($_SESSION['access_token'])) { header('Location: index.php'); exit; }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Attendance - MS Smart HR</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-50 antialiased">
  <div class="max-w-6xl mx-auto px-6 py-10">
    <div class="bg-white rounded-2xl shadow p-6">
      <div class="flex items-start justify-between">
        <div>
          <h1 class="text-2xl font-extrabold text-slate-800">Attendance</h1>
          <p class="text-sm text-slate-500 mt-1">Check-in/out, view today's summary and recent attendance records.</p>
        </div>
        <div class="text-right">
          <p class="text-sm text-slate-600">Signed in as</p>
          <p class="font-medium text-slate-800"><?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
        </div>
      </div>

      <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Controls -->
        <div class="md:col-span-1 bg-slate-50 p-4 rounded-lg border border-slate-100">
          <div class="space-y-4">
            <div>
              <p class="text-xs text-slate-500 uppercase tracking-wider">Today</p>
              <p id="todayDate" class="text-lg font-semibold text-slate-800 mt-1"></p>
            </div>

            <div class="space-y-2">
              <button id="checkInBtn" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold">Check In</button>
              <button id="checkOutBtn" class="w-full py-3 px-4 bg-rose-500 hover:bg-rose-600 text-white rounded-lg font-semibold">Check Out</button>
            </div>

            <div class="pt-2 text-sm text-slate-500">
              <p>Last action: <span id="lastAction">—</span></p>
              <p class="mt-1">Status: <span id="statusBadge" class="inline-block px-2 py-0.5 rounded-full bg-yellow-100 text-yellow-800">Not checked in</span></p>
            </div>
          </div>
        </div>

        <!-- Summary -->
        <div class="md:col-span-2 bg-white p-4 rounded-lg border border-slate-100">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-semibold text-slate-700">Today's Summary</h3>
            <a href="dashboard.php" class="text-xs text-blue-600">← Back to dashboard</a>
          </div>

          <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-3 bg-slate-50 rounded-md text-center">
              <div class="text-xs text-slate-500">Checked In</div>
              <div id="countIn" class="text-2xl font-bold text-emerald-600">0</div>
            </div>
            <div class="p-3 bg-slate-50 rounded-md text-center">
              <div class="text-xs text-slate-500">Checked Out</div>
              <div id="countOut" class="text-2xl font-bold text-rose-600">0</div>
            </div>
            <div class="p-3 bg-slate-50 rounded-md text-center">
              <div class="text-xs text-slate-500">Total Entries</div>
              <div id="countTotal" class="text-2xl font-bold text-slate-800">0</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Records -->
      <div class="mt-6 bg-white rounded-lg border border-slate-100 p-4">
        <h4 class="text-sm font-semibold text-slate-700">Recent Attendance Records</h4>
        <div class="mt-3 overflow-x-auto">
          <table class="w-full text-left text-sm">
            <thead>
              <tr class="text-xs text-slate-500 uppercase tracking-wider">
                <th class="py-2 px-3">Time</th>
                <th class="py-2 px-3">Action</th>
                <th class="py-2 px-3">Notes</th>
              </tr>
            </thead>
            <tbody id="recordsTable" class="divide-y">
              <!-- JS injects rows here -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Simple client-side placeholder for attendance entries (localStorage)
    const today = new Date();
    document.getElementById('todayDate').textContent = today.toLocaleDateString();

    function getRecords() {
      try { return JSON.parse(localStorage.getItem('attendance_records') || '[]'); } catch(e) { return []; }
    }

    function saveRecords(records) { localStorage.setItem('attendance_records', JSON.stringify(records)); }

    function renderRecords() {
      const rows = getRecords();
      const tbody = document.getElementById('recordsTable');
      tbody.innerHTML = rows.slice().reverse().map(r => `\n        <tr>\n          <td class="py-2 px-3">${new Date(r.time).toLocaleString()}</td>\n          <td class="py-2 px-3 font-medium">${r.action}</td>\n          <td class="py-2 px-3 text-slate-500">${r.note||''}</td>\n        </tr>\n      `).join('');

      document.getElementById('countIn').textContent = rows.filter(r=>r.action==='Check In').length;
      document.getElementById('countOut').textContent = rows.filter(r=>r.action==='Check Out').length;
      document.getElementById('countTotal').textContent = rows.length;

      const last = rows[rows.length-1];
      if (last) {
        document.getElementById('lastAction').textContent = last.action + ' at ' + new Date(last.time).toLocaleTimeString();
        document.getElementById('statusBadge').textContent = last.action === 'Check In' ? 'Checked in' : 'Checked out';
        document.getElementById('statusBadge').className = last.action === 'Check In' ? 'inline-block px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700' : 'inline-block px-2 py-0.5 rounded-full bg-rose-100 text-rose-700';
      }
    }

    document.getElementById('checkInBtn').addEventListener('click', () => {
      const records = getRecords();
      records.push({ time: new Date().toISOString(), action: 'Check In', note: '' });
      saveRecords(records); renderRecords();
    });

    document.getElementById('checkOutBtn').addEventListener('click', () => {
      const records = getRecords();
      records.push({ time: new Date().toISOString(), action: 'Check Out', note: '' });
      saveRecords(records); renderRecords();
    });

    // Initial render
    renderRecords();
  </script>
</body>
</html>
