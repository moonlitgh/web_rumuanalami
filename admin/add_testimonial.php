<?php
session_start();
require_once('../config.php');

if(!isset($_SESSION['admin_logged_in'])) {
    die("Unauthorized");
}

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    die("Invalid request method");
}

$judul = $_POST['judul'];
$deskripsi = $_POST['deskripsi'];

// Validasi file video
if(!isset($_FILES['video']) || $_FILES['video']['error'] != 0) {
    die("Error uploading file");
}

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

// Upload file
if(move_uploaded_file($file['tmp_name'], $upload_path . $filename)) {
    // Save to database
    $video_path = 'asset/video/' . $filename;
    $stmt = $conn->prepare("INSERT INTO testimonials (judul, deskripsi, video_path) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $judul, $deskripsi, $video_path);
    
    if($stmt->execute()) {
        echo "Testimoni berhasil ditambahkan";
    } else {
        unlink($upload_path . $filename); // Delete uploaded file if database insert fails
        echo "Gagal menyimpan testimoni: " . $stmt->error;
    }
} else {
    echo "Gagal mengupload file";
}
?> 