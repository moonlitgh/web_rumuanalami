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

// Get petugas info first
$stmt = $conn->prepare("SELECT link_page FROM petugas WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$petugas = $result->fetch_assoc();

if($petugas) {
    // Delete the page file
    $page_file = '../' . $petugas['link_page'] . '.php';
    if(file_exists($page_file)) {
        unlink($page_file);
    }
    
    // Delete from database
    $stmt = $conn->prepare("DELETE FROM petugas WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        echo "Petugas berhasil dihapus";
    } else {
        echo "Gagal menghapus petugas: " . $stmt->error;
    }
} else {
    echo "Petugas tidak ditemukan";
}
?> 