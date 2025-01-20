<?php
require_once('config.php');

if(!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = (int)$_GET['id'];
$stmt = $conn->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if(!$order) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Berhasil - D-Gassvit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .success-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }
        .order-details {
            background: white;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .payment-details {
            background: white;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success-icon {
            color: #28a745;
            font-size: 48px;
            margin-bottom: 20px;
        }
        .bank-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 15px;
        }
        .copy-button {
            cursor: pointer;
            padding: 5px 10px;
            background: #e9ecef;
            border: none;
            border-radius: 3px;
            font-size: 14px;
        }
        .copy-button:hover {
            background: #dee2e6;
        }
        .whatsapp-button {
            background: #25D366;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
            margin-top: 20px;
        }
        .whatsapp-button:hover {
            background: #128C7E;
            color: white;
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="text-center mb-4">
            <i class="bi bi-check-circle-fill success-icon"></i>
            <h2>Pesanan Berhasil!</h2>
            <p>Terima kasih telah melakukan pemesanan di D-Gassvit</p>
        </div>

        <div class="order-details">
            <h4>Detail Pesanan</h4>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Order ID:</strong> #<?= $order['id'] ?></p>
                    <p><strong>Nama:</strong> <?= htmlspecialchars($order['nama']) ?></p>
                    <p><strong>Telepon:</strong> <?= htmlspecialchars($order['telepon']) ?></p>
                    <p><strong>Alamat:</strong> <?= htmlspecialchars($order['alamat']) ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Paket:</strong> <?= $order['paket'] ?> Botol</p>
                    <p><strong>Kurir:</strong> <?= strtoupper($order['kurir']) ?> - <?= $order['paket_kurir'] ?></p>
                    <p><strong>Ongkir:</strong> Rp <?= number_format($order['ongkir'], 0, ',', '.') ?></p>
                    <p><strong>Total:</strong> Rp <?= number_format($order['total_pembayaran'], 0, ',', '.') ?></p>
                </div>
            </div>
        </div>

        <div class="payment-details">
            <h4>Informasi Pembayaran</h4>
            <hr>
            <p>Silakan lakukan pembayaran ke rekening berikut:</p>
            
            <div class="bank-details">
                <p><strong>Bank BCA</strong></p>
                <p>No. Rekening: <span id="rekening">1234567890</span> 
                    <button class="copy-button" onclick="copyToClipboard('rekening')">
                        <i class="bi bi-clipboard"></i> Copy
                    </button>
                </p>
                <p>Atas Nama: PT D-Gassvit Indonesia</p>
                <p>Jumlah: Rp <?= number_format($order['total_pembayaran'], 0, ',', '.') ?>
                    <button class="copy-button" onclick="copyToClipboard('nominal')">
                        <i class="bi bi-clipboard"></i> Copy
                    </button>
                </p>
            </div>

            <div class="text-center mt-4">
                <p>Setelah melakukan pembayaran, silakan konfirmasi melalui WhatsApp:</p>
                <a href="https://wa.me/6281234567890?text=Halo%20Admin%2C%20saya%20ingin%20konfirmasi%20pembayaran%20untuk%20Order%20ID%20%23<?= $order['id'] ?>" 
                   class="whatsapp-button">
                    <i class="bi bi-whatsapp"></i> Konfirmasi Pembayaran
                </a>
            </div>
        </div>
    </div>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copyToClipboard(type) {
            let text = '';
            if(type === 'rekening') {
                text = '1234567890';
            } else if(type === 'nominal') {
                text = '<?= $order['total_pembayaran'] ?>';
            }
            
            navigator.clipboard.writeText(text).then(() => {
                alert('Berhasil disalin!');
            }).catch(err => {
                console.error('Gagal menyalin teks: ', err);
            });
        }
    </script>
</body>
</html> 