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
$username = $_POST['username'];
$nama_lengkap = $_POST['nama_lengkap'];
$no_wa = $_POST['no_wa'];

// Format nomor WhatsApp
$formatted_wa = preg_replace('/[^0-9]/', '', $no_wa);
if(substr($formatted_wa, 0, 1) == '0') {
    $formatted_wa = '62' . substr($formatted_wa, 1);
} elseif(substr($formatted_wa, 0, 2) != '62') {
    $formatted_wa = '62' . $formatted_wa;
}

// Cek apakah username sudah digunakan oleh petugas lain
$stmt = $conn->prepare("SELECT id FROM petugas WHERE username = ? AND id != ?");
$stmt->bind_param("si", $username, $id);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
    die("Username sudah digunakan oleh petugas lain");
}

// Update data petugas
if(!empty($_POST['password'])) {
    // Jika password diisi, update dengan password baru
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE petugas SET username = ?, password = ?, nama_lengkap = ?, no_wa = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $username, $password, $nama_lengkap, $formatted_wa, $id);
} else {
    // Jika password kosong, update tanpa mengubah password
    $stmt = $conn->prepare("UPDATE petugas SET username = ?, nama_lengkap = ?, no_wa = ? WHERE id = ?");
    $stmt->bind_param("sssi", $username, $nama_lengkap, $formatted_wa, $id);
}

if($stmt->execute()) {
    echo "Data petugas berhasil diupdate";
} else {
    echo "Gagal mengupdate data petugas: " . $stmt->error;
}
?> 