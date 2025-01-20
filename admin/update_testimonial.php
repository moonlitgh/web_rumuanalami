<?php
session_start();
require_once('../config.php');

if(!isset($_SESSION['admin_logged_in'])) {
    die("Unauthorized");
}

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    die("Invalid request method");
}

$id = (int)$_POST['id'];
$judul = $_POST['judul'];
$deskripsi = $_POST['deskripsi'];

// Cek apakah testimoni ada
$stmt = $conn->prepare("SELECT video_path FROM testimonials WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$testimonial = $result->fetch_assoc();

if(!$testimonial) {
    die("Testimoni tidak ditemukan");
}

// Jika ada file video baru
if(isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
    $file = $_FILES['video'];
    $allowed_types = ['video/mp4', 'video/webm', 'video/ogg'];
    
    if(!in_array($file['type'], $allowed_types)) {
        die("File type not allowed. Please upload MP4, WebM, or OGG video.");
    }

    // Generate unique filename
    $filename = uniqid() . '_' . $file['name'];
    $upload_path = '../asset/video/';

    // Create directory if not exists
    if (!file_exists($upload_path)) {
        mkdir($upload_path, 0755, true);
    }

    // Upload file baru
    if(move_uploaded_file($file['tmp_name'], $upload_path . $filename)) {
        // Hapus file lama
        $old_file = '../' . $testimonial['video_path'];
        if(file_exists($old_file)) {
            unlink($old_file);
        }
        
        // Update database dengan file baru
        $video_path = 'asset/video/' . $filename;
        $stmt = $conn->prepare("UPDATE testimonials SET judul = ?, deskripsi = ?, video_path = ? WHERE id = ?");
        $stmt->bind_param("sssi", $judul, $deskripsi, $video_path, $id);
    } else {
        die("Gagal mengupload file baru");
    }
} else {
    // Update tanpa mengubah video
    $stmt = $conn->prepare("UPDATE testimonials SET judul = ?, deskripsi = ? WHERE id = ?");
    $stmt->bind_param("ssi", $judul, $deskripsi, $id);
}

if($stmt->execute()) {
    echo "Testimoni berhasil diupdate";
} else {
    echo "Gagal mengupdate testimoni: " . $stmt->error;
}
?> 