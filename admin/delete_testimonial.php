<?php
session_start();
require_once('../config.php');

if(!isset($_SESSION['admin_logged_in'])) {
    die("Unauthorized");
}

if(!isset($_POST['id'])) {
    die("Invalid request");
}

$id = (int)$_POST['id'];

// Get video path first
$stmt = $conn->prepare("SELECT video_path FROM testimonials WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$testimonial = $result->fetch_assoc();

if($testimonial) {
    // Delete video file
    $file_path = '../' . $testimonial['video_path'];
    if(file_exists($file_path)) {
        unlink($file_path);
    }
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM testimonials WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        echo "Testimoni berhasil dihapus";
    } else {
        echo "Gagal menghapus testimoni: " . $stmt->error;
    }
} else {
    echo "Testimoni tidak ditemukan";
}
?> 