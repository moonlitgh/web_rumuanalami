<?php
session_start();
require_once('../config.php');

if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Ambil data petugas berdasarkan ID
if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT id, username, nama_lengkap, no_wa, no_rekening, nama_bank FROM petugas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $petugas = $result->fetch_assoc();

    if(!$petugas) {
        die("Petugas tidak ditemukan");
    }
} else {
    die("ID tidak valid");
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Petugas - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <?php require_once('navbar.php'); ?>

    <div class="dashboard-container">
        <div class="card">
            <div class="card-header">
                <h3>Edit Petugas</h3>
            </div>
            <div class="card-body">
                <form id="editForm">
                    <input type="hidden" name="id" value="<?= $petugas['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($petugas['username']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password Baru (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="password" class="form-control" name="password">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama_lengkap" value="<?= htmlspecialchars($petugas['nama_lengkap']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">No. WhatsApp</label>
                        <input type="text" class="form-control" name="no_wa" value="<?= htmlspecialchars($petugas['no_wa']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nomor Rekening</label>
                        <input type="text" class="form-control" name="no_rekening" value="<?= htmlspecialchars($petugas['no_rekening']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Bank</label>
                        <input type="text" class="form-control" name="nama_bank" value="<?= htmlspecialchars($petugas['nama_bank']) ?>" required>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="updatePetugas()">Simpan Perubahan</button>
                        <a href="manage_petugas.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updatePetugas() {
            const form = document.getElementById('editForm');
            const formData = new FormData(form);

            fetch('update_petugas.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                if(data.includes('berhasil')) {
                    window.location.href = 'manage_petugas.php';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupdate petugas');
            });
        }
    </script>
</body>
</html> 