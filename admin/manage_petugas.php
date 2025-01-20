<?php
session_start();
require_once('../config.php');

// Hanya admin yang bisa akses
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

// Deteksi domain secara otomatis
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

// Jika website berada dalam subfolder di hosting
// $base_url .= "/nama-folder"; // Uncomment dan sesuaikan jika perlu

// Ambil daftar petugas
$query = "SELECT id, username, nama_lengkap, no_wa, link_page FROM petugas";
$result = $conn->query($query);
$petugas_list = [];
while($row = $result->fetch_assoc()) {
    $petugas_list[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Petugas - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .petugas-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .copy-link {
            cursor: pointer;
            color: #0d6efd;
        }
        .copy-link:hover {
            text-decoration: underline;
        }
        .table th {
            background-color: #28a745;
            color: white;
        }
        .btn-action {
            margin: 0 2px;
        }
    </style>
</head>
<body>
    <?php include('navbar.php'); ?>

    <div class="dashboard-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Manajemen Petugas</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPetugasModal">
                <i class="bi bi-plus-circle"></i> Tambah Petugas
            </button>
        </div>

        <div class="petugas-card">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Username</th>
                            <th>Nama Lengkap</th>
                            <th>No. WhatsApp</th>
                            <th>Link Page</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($petugas_list as $petugas): ?>
                        <tr>
                            <td><?= htmlspecialchars($petugas['username']) ?></td>
                            <td><?= htmlspecialchars($petugas['nama_lengkap']) ?></td>
                            <td><?= htmlspecialchars($petugas['no_wa']) ?></td>
                            <td>
                                <span class="copy-link" onclick="copyToClipboard('<?= $base_url . '/' . $petugas['link_page'] ?>')">
                                    <?= $base_url . '/' . $petugas['link_page'] ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning btn-action" onclick="editPetugas(<?= $petugas['id'] ?>)">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-action" onclick="deletePetugas(<?= $petugas['id'] ?>)">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Petugas -->
    <div class="modal fade" id="addPetugasModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Petugas Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addPetugasForm">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" name="nama_lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. WhatsApp</label>
                            <input type="text" class="form-control" name="no_wa" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="savePetugas()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function savePetugas() {
            const form = document.getElementById('addPetugasForm');
            const formData = new FormData(form);

            fetch('add_petugas.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                location.reload();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat menyimpan data');
            });
        }

        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(() => {
                alert('Link berhasil disalin!');
            }).catch(err => {
                console.error('Gagal menyalin teks: ', err);
            });
        }

        function deletePetugas(id) {
            if(confirm('Apakah Anda yakin ingin menghapus petugas ini?')) {
                fetch('delete_petugas.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'id=' + id
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                });
            }
        }

        function editPetugas(id) {
            window.location.href = 'edit_petugas.php?id=' + id;
        }
    </script>
</body>
</html> 