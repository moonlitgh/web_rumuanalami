<?php
session_start();
require_once('../config.php');

if(!isset($_SESSION['admin_logged_in'])) {
    die("Unauthorized");
}

if($_SERVER['REQUEST_METHOD'] != 'POST') {
    die("Invalid request method");
}

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$nama_lengkap = $_POST['nama_lengkap'];
$no_wa = $_POST['no_wa'];
$no_rekening = $_POST['no_rekening'];
$nama_bank = $_POST['nama_bank'];

// Format nomor WhatsApp
$formatted_wa = preg_replace('/[^0-9]/', '', $no_wa); // Hapus semua karakter non-angka
if(substr($formatted_wa, 0, 1) == '0') {
    $formatted_wa = '62' . substr($formatted_wa, 1); // Ganti 0 dengan 62
} elseif(substr($formatted_wa, 0, 2) != '62') {
    $formatted_wa = '62' . $formatted_wa; // Tambah 62 jika belum ada
}

// Generate nama file
$filename = strtolower(str_replace(' ', '-', $username)) . '-' . uniqid() . '.php';
$link_page = 'p/' . $filename; // Path relatif untuk database

// Check if username already exists
$stmt = $conn->prepare("SELECT id FROM petugas WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
    die("Username sudah digunakan");
}

// Insert new petugas
$stmt = $conn->prepare("INSERT INTO petugas (username, password, nama_lengkap, no_wa, no_rekening, nama_bank, link_page) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $username, $password, $nama_lengkap, $formatted_wa, $no_rekening, $nama_bank, $link_page);

if($stmt->execute()) {
    // Create directory if not exists
    if (!file_exists('../p')) {
        mkdir('../p', 0755, true);
    }

    // Create petugas page
    $template = file_get_contents('../index.php');
    
    // Tambahkan path relatif ke config.php
    $config_path = "require_once('../config.php');"; // Path relatif dari folder p/
    $template = str_replace("require_once('config.php');", $config_path, $template);
    
    // Update semua path asset
    $template = str_replace('src="asset/', 'src="../asset/', $template);
    $template = str_replace('src="js/', 'src="../js/', $template);
    $template = str_replace('href="css/', 'href="../css/', $template);
    
    // Update path video testimonial
    $template = str_replace('src="<?= htmlspecialchars($testi[\'video_path\']', 'src="../<?= htmlspecialchars($testi[\'video_path\']', $template);
    
    // Update path untuk file AJAX Raja Ongkir
    $template = str_replace('"dataprovinsi.php"', '"../dataprovinsi.php"', $template);
    $template = str_replace('"datadistrik.php"', '"../datadistrik.php"', $template);
    $template = str_replace('"datakurir.php"', '"../datakurir.php"', $template);
    $template = str_replace('"datapaket.php"', '"../datapaket.php"', $template);
    
    // Modify WhatsApp link
    $template = preg_replace(
        '/https:\/\/wa\.me\/[0-9]+/',
        'https://wa.me/' . $formatted_wa,
        $template
    );
    
    // Update form action untuk proses_order
    $template = str_replace('action="proses_order.php"', 'action="../proses_order.php"', $template);
    
    // Simpan file di folder p/
    $full_path = '../' . $link_page;
    if(file_put_contents($full_path, $template)) {
        echo "Petugas berhasil ditambahkan. Link page: " . $link_page;
    } else {
        echo "Petugas berhasil ditambahkan tetapi gagal membuat file page. Pastikan folder 'p' memiliki permission yang benar.";
    }
} else {
    echo "Gagal menambahkan petugas: " . $stmt->error;
}
?> 