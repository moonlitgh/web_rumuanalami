<?php
session_start();
require_once('../config.php');

if(!isset($_SESSION['admin_logged_in'])) {
    die("Unauthorized");
}

if(!isset($_POST['id']) || !isset($_POST['status'])) {
    die("Invalid request");
}

$id = (int)$_POST['id'];
$status = $_POST['status'];

// Validasi status yang diperbolehkan
$allowed_statuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
if (!in_array($status, $allowed_statuses)) {
    die("Status tidak valid");
}

// Gunakan prepared statement
$stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}

$stmt->bind_param("si", $status, $id);

if($stmt->execute()) {
    echo "Status pesanan berhasil diupdate menjadi " . ucfirst($status);
} else {
    echo "Gagal mengupdate status pesanan: " . $stmt->error;
}

$stmt->close();
?> 