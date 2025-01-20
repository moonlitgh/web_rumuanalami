<?php
session_start();
require_once('../config.php');

if(!isset($_SESSION['admin_logged_in'])) {
    die("Unauthorized");
}

// Ambil data testimoni berdasarkan ID
if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM testimonials WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $testimonial = $result->fetch_assoc();

    if(!$testimonial) {
        die("Testimoni tidak ditemukan");
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
    <title>Edit Testimoni - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .video-preview {
            max-width: 400px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <?php require_once(__DIR__ . '/navbar.php'); ?>

    <div class="dashboard-container">
        <div class="card">
            <div class="card-header">
                <h3>Edit Video Testimoni</h3>
            </div>
            <div class="card-body">
                <form id="editForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?= $testimonial['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Judul</label>
                        <input type="text" class="form-control" name="judul" value="<?= htmlspecialchars($testimonial['judul']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" name="deskripsi" rows="3"><?= htmlspecialchars($testimonial['deskripsi']) ?></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Video Saat Ini</label>
                        <video class="video-preview" controls>
                            <source src="../<?= $testimonial['video_path'] ?>" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ganti Video (Kosongkan jika tidak ingin mengubah)</label>
                        <input type="file" class="form-control" name="video" accept="video/*">
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary" onclick="updateTestimonial()">Simpan Perubahan</button>
                        <a href="manage_testimonials.php" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateTestimonial() {
            const form = document.getElementById('editForm');
            const formData = new FormData(form);

            fetch('update_testimonial.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                alert(data);
                if(data.includes('berhasil')) {
                    window.location.href = 'manage_testimonials.php';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengupdate testimoni');
            });
        }
    </script>
</body>
</html> 