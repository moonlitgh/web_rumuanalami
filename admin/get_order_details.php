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
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if(!$order) {
    die("Order not found");
}
?>

<div class="p-3">
    <h6 class="mb-3">Informasi Pemesan</h6>
    <p><strong>Nama:</strong> <?= htmlspecialchars($order['nama']) ?></p>
    <p><strong>Telepon:</strong> <?= htmlspecialchars($order['telepon']) ?></p>
    <p><strong>Alamat:</strong> <?= htmlspecialchars($order['alamat']) ?></p>
    
    <h6 class="mb-3 mt-4">Detail Pesanan</h6>
    <p><strong>Paket:</strong> <?= $order['paket'] ?> Botol</p>
    <p><strong>Total Pembayaran:</strong> Rp <?= number_format($order['total_pembayaran'], 0, ',', '.') ?></p>
    <p><strong>Tanggal Order:</strong> <?= date('d/m/Y H:i', strtotime($order['tanggal'])) ?></p>
    <p><strong>Status:</strong> 
        <span class="badge <?= $order['status'] == 'completed' ? 'bg-success' : 'bg-warning' ?>">
            <?= ucfirst($order['status']) ?>
        </span>
    </p>

    <h6 class="mb-3 mt-4">Informasi Pengiriman</h6>
    <p><strong>Kurir:</strong> <?= strtoupper($order['kurir']) ?></p>
    <p><strong>Layanan:</strong> <?= $order['paket_kurir'] ?></p>
    <p><strong>Ongkir:</strong> Rp <?= number_format($order['ongkir'], 0, ',', '.') ?></p>
    <p><strong>Estimasi:</strong> <?= $order['estimasi'] ?> hari</p>
</div> 