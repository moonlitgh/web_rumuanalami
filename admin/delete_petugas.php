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

// Ambil informasi file petugas sebelum dihapus
$stmt = $conn->prepare("SELECT link_page FROM petugas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$petugas = $result->fetch_assoc();

if($petugas) {
    // Hapus file petugas
    $file_path = '../' . $petugas['link_page'];
    if(file_exists($file_path)) {
        if(unlink($file_path)) {
            // File berhasil dihapus, lanjut hapus data dari database
            $stmt = $conn->prepare("DELETE FROM petugas WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if($stmt->execute()) {
                echo "Petugas dan file berhasil dihapus";
            } else {
                echo "Gagal menghapus data petugas: " . $stmt->error;
            }
        } else {
            echo "Gagal menghapus file petugas, tetapi data akan tetap dihapus dari database";
            // Tetap hapus data dari database
            $stmt = $conn->prepare("DELETE FROM petugas WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
        }
    } else {
        // File tidak ditemukan, hanya hapus data dari database
        $stmt = $conn->prepare("DELETE FROM petugas WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if($stmt->execute()) {
            echo "Data petugas berhasil dihapus (file tidak ditemukan)";
        } else {
            echo "Gagal menghapus data petugas: " . $stmt->error;
        }
    }
} else {
    echo "Petugas tidak ditemukan";
}
?> 