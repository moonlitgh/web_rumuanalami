<?php
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get petugas data berdasarkan path URL
    $url_path = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_PATH);
    $filename = basename($url_path); // Ambil nama file dari URL
    
    // Query untuk mencari petugas berdasarkan link_page
    $stmt = $conn->prepare("SELECT * FROM petugas WHERE link_page LIKE ?");
    $like_param = "%$filename%";
    $stmt->bind_param("s", $like_param);
    $stmt->execute();
    $result = $stmt->get_result();
    $petugas = $result->fetch_assoc();

    if (!$petugas) {
        die("Petugas tidak ditemukan. Debug info: filename = $filename");
    }

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

    // Format pesan WhatsApp dengan info rekening
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
           . "*Informasi Pembayaran:*\n"
           . "Bank: " . $petugas['nama_bank'] . "\n"
           . "No. Rekening: " . $petugas['no_rekening'] . "\n"
           . "Total: Rp " . number_format($total, 0, ',', '.') . "\n\n"
           . "*Informasi Pengiriman:*\n"
           . "Kurir: " . strtoupper($kurir) . "\n"
           . "Layanan: " . $paket_kurir . "\n"
           . "Ongkir: Rp " . number_format($ongkir, 0, ',', '.') . "\n"
           . "Estimasi: " . $estimasi . " hari\n\n"
           . "Terima kasih telah berbelanja! 🙏";

    // Redirect ke WhatsApp petugas
    $wa_number = $petugas['no_wa'];
    $encoded_pesan = urlencode($pesan);
    // header("Location: https://wa.me/$wa_number?text=$encoded_pesan");
    // exit();

    // Langsung tampilkan halaman detail
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Detail Pesanan - D-Gassvit</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background-color: #f8f9fa;
                font-family: Arial, sans-serif;
            }
            .order-container {
                max-width: 600px;
                margin: 30px auto;
                padding: 20px;
                background: white;
                border-radius: 10px;
                box-shadow: 0 0 15px rgba(0,0,0,0.1);
            }
            .detail-section {
                margin-bottom: 20px;
                padding-bottom: 15px;
                border-bottom: 1px solid #eee;
            }
            .bank-info {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 5px;
                margin: 15px 0;
            }
            .total-amount {
                font-size: 1.2em;
                font-weight: bold;
                color: #28a745;
            }
            .copy-button {
                padding: 2px 8px;
                font-size: 0.8em;
                margin-left: 10px;
            }
            .wa-button {
                background-color: #25D366;
                color: white;
                border: none;
                padding: 10px 20px;
                border-radius: 5px;
                text-decoration: none;
                display: inline-block;
                margin-top: 15px;
            }
            .wa-button:hover {
                background-color: #128C7E;
                color: white;
            }
        </style>
    </head>
    <body>
        <div class="order-container">
            <h3 class="text-center mb-4">Detail Pesanan #<?= $stmt->insert_id ?></h3>
            
            <div class="detail-section">
                <h5>Data Pemesan</h5>
                <p>Nama: <?= htmlspecialchars($nama) ?></p>
                <p>Telepon: <?= htmlspecialchars($telepon) ?></p>
                <p>Alamat: <?= htmlspecialchars($alamat) ?></p>
                <p>Provinsi: <?= htmlspecialchars($provinsi) ?></p>
                <p>Kota: <?= htmlspecialchars($kota) ?></p>
            </div>

            <div class="detail-section">
                <h5>Detail Produk</h5>
                <p>Paket: <?= $paket ?> Botol</p>
                <p>Ekspedisi: <?= strtoupper($kurir) ?> - <?= $paket_kurir ?></p>
                <p>Ongkir: Rp <?= number_format($ongkir, 0, ',', '.') ?></p>
                <p class="total-amount">Total: Rp <?= number_format($total_pembayaran, 0, ',', '.') ?></p>
            </div>

            <div class="detail-section">
                <h5>Informasi Pembayaran</h5>
                <div class="bank-info">
                    <p><strong>Bank <?= htmlspecialchars($petugas['nama_bank']) ?></strong></p>
                    <p>
                        No. Rekening: 
                        <span id="norek"><?= htmlspecialchars($petugas['no_rekening']) ?></span>
                        <button class="btn btn-sm btn-secondary copy-button" onclick="copyText('norek')">
                            Salin
                        </button>
                    </p>
                    <p>A/N: <?= htmlspecialchars($petugas['nama_lengkap']) ?></p>
                    <p>
                        Nominal: 
                        <span id="nominal">Rp <?= number_format($total_pembayaran, 0, ',', '.') ?></span>
                        <button class="btn btn-sm btn-secondary copy-button" onclick="copyText('nominal')">
                            Salin
                        </button>
                    </p>
                </div>
            </div>

            <div class="text-center">
                <p>Silakan lakukan pembayaran dan konfirmasi melalui WhatsApp</p>
                <?php
                $wa_number = preg_replace('/[^0-9]/', '', $petugas['no_wa']);
                if (substr($wa_number, 0, 1) === '0') {
                    $wa_number = '62' . substr($wa_number, 1);
                }
                
                $wa_link = "https://api.whatsapp.com/send?phone=" . $wa_number . "&text=" . urlencode($pesan);
                ?>
                <a href="<?= $wa_link ?>" class="wa-button">
                    Konfirmasi Pembayaran via WhatsApp
                </a>
            </div>
        </div>

        <script>
            function copyText(elementId) {
                const element = document.getElementById(elementId);
                const text = element.innerText;
                navigator.clipboard.writeText(text).then(() => {
                    alert('Teks berhasil disalin!');
                }).catch(err => {
                    console.error('Gagal menyalin teks:', err);
                });
            }
        </script>
    </body>
    </html>
    <?php
    exit();
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