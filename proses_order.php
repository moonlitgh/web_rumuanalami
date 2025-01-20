<?php
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $paket = isset($_POST['paket-order']) ? (int)$_POST['paket-order'] : 0;
    $nama = isset($_POST['nama']) ? $conn->real_escape_string($_POST['nama']) : '';
    $telepon = isset($_POST['telepon']) ? $conn->real_escape_string($_POST['telepon']) : '';
    $provinsi = isset($_POST['provinsi_nama']) ? $conn->real_escape_string($_POST['provinsi_nama']) : '';
    $kota = isset($_POST['distrik_nama']) ? $conn->real_escape_string($_POST['distrik_nama']) : '';
    $alamat = isset($_POST['alamat']) ? $conn->real_escape_string($_POST['alamat']) : '';
    $kurir = isset($_POST['ekspedisi']) ? $conn->real_escape_string($_POST['ekspedisi']) : '';
    $paket_kurir = isset($_POST['paket_nama']) ? $conn->real_escape_string($_POST['paket_nama']) : '';
    $ongkir = isset($_POST['ongkir']) ? (float)$_POST['ongkir'] : 0;
    $estimasi = isset($_POST['estimasi']) ? $conn->real_escape_string($_POST['estimasi']) : '';
    $total_pembayaran = isset($_POST['total_pembayaran']) ? (float)$_POST['total_pembayaran'] : 0;

    // Validasi data
    if(empty($paket) || empty($nama) || empty($telepon) || empty($alamat)) {
        die("Data tidak lengkap");
    }

    // Validasi data pengiriman
    if(empty($provinsi) || empty($kota) || empty($kurir) || empty($paket_kurir) || empty($ongkir) || empty($estimasi)) {
        die("Data pengiriman tidak lengkap");
    }

    // Debug data
    echo "<!-- Debug Data:
    Provinsi: $provinsi
    Kota: $kota
    Kurir: $kurir
    Paket: $paket_kurir
    Ongkir: $ongkir
    Estimasi: $estimasi
    -->";

    // Ambil harga dari database berdasarkan jumlah botol
    $query = "SELECT harga FROM products WHERE jumlah_botol = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $paket);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if($result->num_rows === 0) {
        die("Paket tidak valid");
    }
    
    $product = $result->fetch_assoc();
    $harga_paket = $product['harga'];

    // Hitung total pembayaran
    $total = $harga_paket + $ongkir;

    // Format pesan WhatsApp
    $pesan = "🛍️ *PESANAN BARU!*\n\n"
           . "Detail Pesanan:\n"
           . "================\n"
           . "*Paket:* " . $paket . " Botol\n"
           . "*Harga Paket:* Rp " . number_format($harga_paket, 0, ',', '.') . "\n\n"
           . "*Data Penerima:*\n"
           . "Nama: " . $nama . "\n"
           . "No. HP: " . $telepon . "\n"
           . "Alamat: " . $alamat . "\n"
           . "Provinsi: " . $provinsi . "\n"
           . "Kota: " . $kota . "\n\n"
           . "*Informasi Pengiriman:*\n"
           . "Kurir: " . strtoupper($kurir) . "\n"
           . "Layanan: " . $paket_kurir . "\n"
           . "Ongkir: Rp " . number_format($ongkir, 0, ',', '.') . "\n"
           . "Estimasi: " . $estimasi . " hari\n\n"
           . "*Total Pembayaran: Rp " . number_format($total, 0, ',', '.') . "*\n\n"
           . "Terima kasih telah berbelanja! 🙏";

    // Simpan order ke database
    $stmt = $conn->prepare("INSERT INTO orders (nama, telepon, provinsi, kota, alamat, paket, kurir, paket_kurir, ongkir, estimasi, total_pembayaran, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->bind_param("ssssssssdsd", $nama, $telepon, $provinsi, $kota, $alamat, $paket, $kurir, $paket_kurir, $ongkir, $estimasi, $total_pembayaran);
    
    if($stmt->execute()) {
        $order_id = $stmt->insert_id;
        // Redirect ke halaman sukses dengan parameter order_id
        header("Location: order_success.php?id=" . $order_id);
        exit();
    } else {
        die("Error: " . $stmt->error);
    }

} else {
    // Jika bukan method POST, redirect ke halaman utama
    header("Location: index.php");
    exit();
}
?>

<!-- Halaman loading -->
<!DOCTYPE html>
<html>
<head>
    <title>Memproses Pesanan...</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .loading {
            text-align: center;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #28a745;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="loading">
        <div class="spinner"></div>
        <h2>Memproses Pesanan Anda...</h2>
        <p>Mohon tunggu sebentar, Anda akan diarahkan ke WhatsApp...</p>
    </div>
</body>
</html> 