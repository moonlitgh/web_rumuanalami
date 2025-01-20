<?php
session_start();
require_once('../config.php');

// Hanya admin yang bisa akses
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

// Ambil daftar testimoni
$query = "SELECT * FROM testimonials ORDER BY created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Testimoni - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .video-preview {
            max-width: 200px;
            margin: 10px 0;
        }
        .action-buttons .btn {
            margin: 0 5px;
        }
    </style>
</head>
<body>
    <?php require_once(__DIR__ . '/navbar.php'); ?>

    <div class="dashboard-container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Kelola Video Testimoni</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTestimonialModal">
                <i class="bi bi-plus-circle"></i> Tambah Testimoni
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Judul</th>
                        <th>Deskripsi</th>
                        <th>Video</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['judul']) ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                        <td>
                            <video class="video-preview" controls>
                                <source src="../<?= $row['video_path'] ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                        <td class="action-buttons">
                            <button class="btn btn-sm btn-warning" onclick="editTestimonial(<?= $row['id'] ?>)">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteTestimonial(<?= $row['id'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Tambah Testimoni -->
    <div class="modal fade" id="addTestimonialModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Video Testimoni</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="testimonialForm" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label class="form-label">Judul</label>
                            <input type="text" class="form-control" name="judul" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea class="form-control" name="deskripsi" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Video</label>
                            <input type="file" class="form-control" name="video" accept="video/*" required>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" onclick="saveTestimonial()">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function saveTestimonial() {
            const form = document.getElementById('testimonialForm');
            const formData = new FormData(form);

            fetch('add_testimonial.php', {
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
                alert('Terjadi kesalahan saat menyimpan testimoni');
            });
        }

        function deleteTestimonial(id) {
            if(confirm('Apakah Anda yakin ingin menghapus testimoni ini?')) {
                fetch('delete_testimonial.php', {
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

        function editTestimonial(id) {
            window.location.href = 'edit_testimonial.php?id=' + id;
        }
    </script>
</body>
</html> 