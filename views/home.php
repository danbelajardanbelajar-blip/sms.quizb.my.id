<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS Gateway - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f3f4f6;
            --surface: rgba(255, 255, 255, 0.85);
            --text: #1f2937;
            --border: #e5e7eb;
            --danger: #ef4444;
            --danger-hover: #dc2626;
            --warning: #f59e0b;
        }

        * { box-sizing: border-box; }
        
        body { 
            font-family: 'Inter', sans-serif; 
            background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
            margin: 0; 
            padding: 40px 20px; 
            color: var(--text);
            min-height: 100vh;
        }

        .container { 
            max-width: 1100px; 
            margin: 0 auto; 
        }

        /* Glassmorphism Panel */
        .panel {
            background: var(--surface);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 30px; 
            border-radius: 16px; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.05); 
            margin-bottom: 30px;
        }

        h1, h2 { color: #111827; margin-top: 0; }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        /* Forms */
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px; }
        input, select, textarea { 
            width: 100%; 
            padding: 10px 12px; 
            border: 1px solid var(--border); 
            border-radius: 8px; 
            font-family: inherit;
            transition: all 0.2s;
            background: rgba(255,255,255,0.9);
        }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        /* Buttons */
        .btn {
            display: inline-block;
            padding: 10px 18px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.2s;
            font-family: inherit;
        }
        .btn:hover { background: var(--primary-hover); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(79,70,229,0.2); }
        .btn-danger { background: var(--danger); }
        .btn-danger:hover { background: var(--danger-hover); box-shadow: 0 4px 12px rgba(239,68,68,0.2); }
        .btn-warning { background: var(--warning); color: white; }
        .btn-sm { padding: 6px 12px; font-size: 13px; }

        /* Tables */
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 10px; }
        th, td { 
            padding: 15px; 
            text-align: left; 
            border-bottom: 1px solid var(--border); 
        }
        th { 
            background: rgba(243, 244, 246, 0.5); 
            font-weight: 600; 
            color: #374151;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.05em;
        }
        th:first-child { border-top-left-radius: 8px; }
        th:last-child { border-top-right-radius: 8px; }
        tr:last-child td { border-bottom: none; }
        tbody tr { transition: background 0.2s; }
        tbody tr:hover { background: rgba(249, 250, 251, 0.5); }

        .actions { display: flex; gap: 8px; }

        /* Modal */
        .modal {
            display: none; 
            position: fixed; 
            z-index: 100; 
            left: 0; top: 0; 
            width: 100%; height: 100%; 
            background-color: rgba(0,0,0,0.4); 
            backdrop-filter: blur(4px);
        }
        .modal-content {
            background: var(--surface);
            margin: 5% auto; 
            padding: 30px; 
            border-radius: 16px;
            width: 90%; 
            max-width: 500px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease-out;
        }
        @keyframes slideIn {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .close { float: right; font-size: 24px; font-weight: bold; cursor: pointer; color: #9ca3af; }
        .close:hover { color: var(--text); }
        
        .empty-state { text-align: center; padding: 40px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>SMS Gateway Server</h1>
                <p style="color: #6b7280; margin-top: 5px;">Kelola jadwal pesan yang akan disinkronisasi ke Aplikasi Android.</p>
            </div>
            <button class="btn" onclick="openModal('addModal')">+ Tambah Jadwal</button>
        </div>
        
        <div class="panel">
            <h2>Daftar Jadwal</h2>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Nomor HP</th>
                            <th>Pesan</th>
                            <th>Waktu (Lokal)</th>
                            <th>Repeat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($schedules as $sched): ?>
                        <tr>
                            <td><strong><?= htmlspecialchars($sched['name'] ?? '-') ?></strong></td>
                            <td><?= htmlspecialchars($sched['phoneNumber']) ?></td>
                            <td title="<?= htmlspecialchars($sched['message']) ?>"><?= htmlspecialchars(mb_strimwidth($sched['message'], 0, 50, "...")) ?></td>
                            <td>
                                <?php 
                                    // Convert epoch ms to readable format (approximate to local server time)
                                    $epoch = (int)($sched['timeInMillis'] / 1000);
                                    echo date("d M Y H:i", $epoch);
                                ?>
                            </td>
                            <td>
                                <span style="background: #e5e7eb; padding: 3px 8px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                    <?= htmlspecialchars($sched['repeatMode']) ?>
                                </span>
                            </td>
                            <td class="actions">
                                <button class="btn btn-warning btn-sm" onclick="openEditModal(<?= htmlspecialchars(json_encode($sched), ENT_QUOTES, 'UTF-8') ?>)">Edit</button>
                                <a href="index.php?url=delete&id=<?= $sched['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus jadwal ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="6" class="empty-state">
                                <svg style="width:48px;height:48px;margin-bottom:10px;color:#cbd5e1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg><br>
                                Belum ada jadwal terdaftar. Klik "Tambah Jadwal" untuk memulai.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addModal')">&times;</span>
            <h2>Tambah Jadwal Baru</h2>
            <form action="index.php?url=add" method="POST">
                <div class="form-group">
                    <label>Nama Penerima</label>
                    <input type="text" name="name" required placeholder="Contoh: Budi">
                </div>
                <div class="form-group">
                    <label>Nomor HP</label>
                    <input type="text" name="phoneNumber" required placeholder="+628...">
                </div>
                <div class="form-group">
                    <label>Pesan</label>
                    <textarea name="message" rows="6" required placeholder="Isi pesan SMS..."></textarea>
                </div>
                <div class="form-group">
                    <label>Waktu Pengiriman (Waktu Lokal)</label>
                    <input type="datetime-local" name="scheduled_time" required>
                </div>
                <div class="form-group">
                    <label>Perulangan (Repeat)</label>
                    <select name="repeatMode">
                        <option value="NONE">Tidak Ada (Sekali Saja)</option>
                        <option value="MINUTELY">Tiap Menit</option>
                        <option value="HOURLY">Tiap Jam</option>
                        <option value="DAILY">Tiap Hari</option>
                        <option value="WEEKLY">Tiap Minggu</option>
                        <option value="MONTHLY">Tiap Bulan</option>
                    </select>
                </div>
                <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">Simpan Jadwal</button>
            </form>
        </div>
    </div>

    <!-- Modal Edit -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('editModal')">&times;</span>
            <h2>Edit Jadwal</h2>
            <form action="index.php?url=edit" method="POST">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label>Nama Penerima</label>
                    <input type="text" name="name" id="edit_name" required>
                </div>
                <div class="form-group">
                    <label>Nomor HP</label>
                    <input type="text" name="phoneNumber" id="edit_phone" required>
                </div>
                <div class="form-group">
                    <label>Pesan</label>
                    <textarea name="message" id="edit_message" rows="6" required></textarea>
                </div>
                <div class="form-group">
                    <label>Waktu Pengiriman (Waktu Lokal)</label>
                    <input type="datetime-local" name="scheduled_time" id="edit_time" required>
                </div>
                <div class="form-group">
                    <label>Perulangan (Repeat)</label>
                    <select name="repeatMode" id="edit_repeat">
                        <option value="NONE">Tidak Ada (Sekali Saja)</option>
                        <option value="MINUTELY">Tiap Menit</option>
                        <option value="HOURLY">Tiap Jam</option>
                        <option value="DAILY">Tiap Hari</option>
                        <option value="WEEKLY">Tiap Minggu</option>
                        <option value="MONTHLY">Tiap Bulan</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-warning" style="width: 100%; margin-top: 10px; color:white;">Update Jadwal</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(id) {
            document.getElementById(id).style.display = "block";
        }
        function closeModal(id) {
            document.getElementById(id).style.display = "none";
        }
        
        function openEditModal(sched) {
            document.getElementById('edit_id').value = sched.id;
            document.getElementById('edit_name').value = sched.name || '';
            document.getElementById('edit_phone').value = sched.phoneNumber || '';
            document.getElementById('edit_message').value = sched.message || '';
            document.getElementById('edit_repeat').value = sched.repeatMode || 'NONE';
            
            // Format epoch to YYYY-MM-DDThh:mm for datetime-local
            if(sched.timeInMillis) {
                const date = new Date(parseInt(sched.timeInMillis));
                const tzoffset = (new Date()).getTimezoneOffset() * 60000; // offset in milliseconds
                const localISOTime = (new Date(date - tzoffset)).toISOString().slice(0, -1);
                // get up to minutes
                document.getElementById('edit_time').value = localISOTime.substring(0, 16);
            }
            
            openModal('editModal');
        }

        // Tutup modal jika klik di luar area modal
        window.onclick = function(event) {
            if (event.target.className === "modal") {
                event.target.style.display = "none";
            }
        }
    </script>
</body>
</html>
