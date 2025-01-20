<?php
session_start();
require_once('../config.php');

// Cek apakah user sudah login
if(!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['petugas_logged_in'])) {
    header("Location: login.php");
    exit();
}

// Proses logout
if(isset($_GET['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Load navbar yang sesuai
if(isset($_SESSION['admin_logged_in'])) {
    require_once(__DIR__ . '/navbar.php');
} else {
    require_once(__DIR__ . '/navbar_petugas.php');
}

// Ambil data pesanan
try {
    // Total pesanan
    $stmt = $conn->query("SELECT COUNT(*) as total FROM orders");
    $total_orders = $stmt->fetch_assoc()['total'];

    // Pesanan pending
    $stmt = $conn->query("SELECT COUNT(*) as pending FROM orders WHERE status = 'pending'");
    $pending_orders = $stmt->fetch_assoc()['pending'];

    // Pesanan processing
    $stmt = $conn->query("SELECT COUNT(*) as processing FROM orders WHERE status = 'processing'");
    $processing_orders = $stmt->fetch_assoc()['processing'];

    // Pesanan shipped
    $stmt = $conn->query("SELECT COUNT(*) as shipped FROM orders WHERE status = 'shipped'");
    $shipped_orders = $stmt->fetch_assoc()['shipped'];

    // Pesanan delivered
    $stmt = $conn->query("SELECT COUNT(*) as delivered FROM orders WHERE status = 'delivered'");
    $delivered_orders = $stmt->fetch_assoc()['delivered'];

    // Pesanan cancelled
    $stmt = $conn->query("SELECT COUNT(*) as cancelled FROM orders WHERE status = 'cancelled'");
    $cancelled_orders = $stmt->fetch_assoc()['cancelled'];

    // Ambil data pesanan terbaru
    $result = $conn->query("SELECT * FROM orders ORDER BY tanggal DESC");
    $orders = [];
    while($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }
} catch(Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?= isset($_SESSION['admin_logged_in']) ? 'Admin' : 'Petugas' ?> - D-Gassvit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .dashboard-container {
            padding: 20px;
            width: 98%;
            max-width: 1800px;
            margin: 0 auto;
        }

        .navbar {
            background-color: #28a745;
            padding: 15px 0;
            margin-bottom: 30px;
        }

        .navbar-brand {
            color: white !important;
            font-size: 24px;
            font-weight: bold;
        }

        .nav-link {
            color: white !important;
            font-size: 16px;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #28a745;
        }

        .stats-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stats-label {
            color: #6c757d;
            font-size: 1rem;
        }

        .orders-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin: 0 auto;
        }

        .card-header {
            padding: 20px 40px;
            background-color: #28a745;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            width: 100%;
            box-sizing: border-box;
        }

        .table-responsive {
            padding: 25px 40px;
            width: 100%;
        }

        .table {
            width: 100%;
            margin: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th, .table td {
            padding: 18px 25px;
            vertical-align: middle;
        }

        .table-mobile th:nth-child(1),
        .table-mobile td:nth-child(1) {
            width: 15%;
            min-width: 140px;
        }

        .table-mobile th:nth-child(2),
        .table-mobile td:nth-child(2) {
            width: 25%;
            min-width: 180px;
        }

        .table-mobile th:nth-child(3),
        .table-mobile td:nth-child(3) {
            width: 15%;
            min-width: 120px;
        }

        .table-mobile th:nth-child(4),
        .table-mobile td:nth-child(4) {
            width: 20%;
            min-width: 160px;
        }

        .table-mobile th:nth-child(5),
        .table-mobile td:nth-child(5) {
            width: 12%;
            min-width: 120px;
        }

        .table-mobile th:nth-child(6),
        .table-mobile td:nth-child(6) {
            width: 13%;
            min-width: 120px;
            text-align: center;
        }

        .status-badge {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .status-pending {
            background-color: #ffc107;
            color: #000;
        }

        .status-completed {
            background-color: #28a745;
            color: #fff;
        }

        .btn-action {
            padding: 8px 12px;
            border-radius: 5px;
            margin: 0 3px;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 10px;
                width: 100%;
            }

            .table-responsive {
                padding: 15px;
            }

            .card-header {
                padding: 15px 20px;
            }
        }

        @media (min-width: 769px) {
            .table-responsive {
                padding: 30px 40px;
            }

            .table th, .table td {
                padding: 20px 25px;
            }
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 24px;
            color: white;
        }

        .stats-info {
            flex-grow: 1;
        }

        .stats-number {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stats-label {
            color: #6c757d;
            font-size: 14px;
        }

        /* Warna untuk icon status */
        .stats-icon.total { background-color: #0dcaf0; }
        .stats-icon.pending { background-color: #ffc107; }
        .stats-icon.processing { background-color: #17a2b8; }
        .stats-icon.shipped { background-color: #6610f2; }
        .stats-icon.delivered { background-color: #28a745; }
        .stats-icon.cancelled { background-color: #dc3545; }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 15px;
            }

            .stats-card {
                padding: 15px;
            }

            .stats-number {
                font-size: 20px;
            }

            .stats-icon {
                width: 40px;
                height: 40px;
                font-size: 20px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }

        .btn-group .dropdown-menu {
            min-width: 120px;
            padding: 5px 0;
        }

        .btn-group .dropdown-item {
            padding: 8px 15px;
            cursor: pointer;
        }

        .btn-group .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .status-pending { background-color: #ffc107; color: #000; }
        .status-processing { background-color: #17a2b8; color: #fff; }
        .status-shipped { background-color: #6610f2; color: #fff; }
        .status-delivered { background-color: #28a745; color: #fff; }
        .status-cancelled { background-color: #dc3545; color: #fff; }

        .navbar {
            background-color: #28a745;
            padding: 15px 0;
            margin-bottom: 30px;
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 24px;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            padding: 8px 15px !important;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            color: white !important;
            background-color: rgba(255,255,255,0.1);
        }

        .nav-link.active {
            background-color: rgba(255,255,255,0.2);
            color: white !important;
        }

        .navbar-toggler {
            border-color: rgba(255,255,255,0.5);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 255, 255, 0.9%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        @media (max-width: 991px) {
            .navbar-collapse {
                background-color: #28a745;
                padding: 10px;
                border-radius: 5px;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <!-- Statistik -->
        <div class="stats-grid">
            <div class="stats-card">
                <div class="stats-icon total">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-number"><?= $total_orders ?></div>
                    <div class="stats-label">Total Pesanan</div>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-icon pending">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-number"><?= $pending_orders ?></div>
                    <div class="stats-label">Pending</div>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-icon processing">
                    <i class="bi bi-gear"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-number"><?= $processing_orders ?></div>
                    <div class="stats-label">Processing</div>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-icon shipped">
                    <i class="bi bi-truck"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-number"><?= $shipped_orders ?></div>
                    <div class="stats-label">Shipped</div>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-icon delivered">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-number"><?= $delivered_orders ?></div>
                    <div class="stats-label">Delivered</div>
                </div>
            </div>

            <div class="stats-card">
                <div class="stats-icon cancelled">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stats-info">
                    <div class="stats-number"><?= $cancelled_orders ?></div>
                    <div class="stats-label">Cancelled</div>
                </div>
            </div>
        </div>

        <!-- Tabel Pesanan -->
        <div class="orders-card">
            <div class="card-header">
                <span>Daftar Pesanan Terbaru</span>
                <button class="btn btn-light btn-sm" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Refresh
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover table-mobile">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Paket</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($orders as $order): ?>
                        <tr>
                            <td data-label="Tanggal"><?= date('d/m/Y H:i', strtotime($order['tanggal'])) ?></td>
                            <td data-label="Nama"><?= htmlspecialchars($order['nama']) ?></td>
                            <td data-label="Paket"><?= $order['paket'] ?> Botol</td>
                            <td data-label="Total">Rp <?= number_format($order['total_pembayaran'], 0, ',', '.') ?></td>
                            <td data-label="Status">
                                <span class="status-badge status-<?= $order['status'] ?>">
                                    <?= ucfirst($order['status']) ?>
                                </span>
                            </td>
                            <td data-label="Aksi">
                                <button class="btn btn-info btn-action" onclick="viewOrder(<?= $order['id'] ?>)">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <div class="btn-group">
                                    <button class="btn btn-action dropdown-toggle status-<?= $order['status'] ?>" 
                                            type="button" 
                                            data-bs-toggle="dropdown" 
                                            aria-expanded="false">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $order['id'] ?>, 'pending')">Pending</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $order['id'] ?>, 'processing')">Processing</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $order['id'] ?>, 'shipped')">Shipped</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $order['id'] ?>, 'delivered')">Delivered</a></li>
                                        <li><a class="dropdown-item" href="#" onclick="updateStatus(<?= $order['id'] ?>, 'cancelled')">Cancelled</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Detail Pesanan -->
    <div class="modal fade" id="orderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Pesanan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="orderDetails">
                    <!-- Detail pesanan akan dimuat di sini -->
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewOrder(id) {
            fetch('get_order_details.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'id=' + id
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('orderDetails').innerHTML = data;
                new bootstrap.Modal(document.getElementById('orderModal')).show();
            });
        }

        function updateStatus(id, newStatus) {
            const statusMessages = {
                'pending': 'menunggu pembayaran',
                'processing': 'sedang diproses',
                'shipped': 'sedang dikirim',
                'delivered': 'telah diterima',
                'cancelled': 'dibatalkan'
            };

            if(confirm(`Apakah Anda yakin ingin mengubah status pesanan menjadi ${statusMessages[newStatus]}?`)) {
                fetch('update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `id=${id}&status=${newStatus}`
                })
                .then(response => response.text())
                .then(data => {
                    alert(data);
                    location.reload();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat mengupdate status');
                });
            }
        }

        // Fungsi untuk memperbarui warna badge status
        function updateStatusBadge(element, status) {
            const badge = element.closest('tr').querySelector('.status-badge');
            badge.className = 'status-badge ' + (status === 'completed' ? 'status-completed' : 'status-pending');
            badge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
        }

        // Tambahkan CSS untuk animasi
        const style = document.createElement('style');
        style.textContent = `
            .status-badge {
                transition: all 0.3s ease;
            }
            .status-pending {
                background-color: #ffc107;
                color: #000;
            }
            .status-completed {
                background-color: #28a745;
                color: #fff;
            }
            .dropdown-item {
                cursor: pointer;
            }
            .dropdown-item:hover {
                background-color: #f8f9fa;
            }
            .btn-action {
                transition: all 0.2s ease;
            }
            .btn-action:hover {
                transform: translateY(-2px);
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            .status-badge {
                animation: fadeIn 0.3s ease;
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html> 