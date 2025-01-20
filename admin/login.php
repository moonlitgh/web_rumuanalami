<?php
session_start();
require_once('../config.php');

if(isset($_SESSION['admin_logged_in']) || isset($_SESSION['petugas_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Cek di tabel admin
    $query = "SELECT * FROM admin WHERE username = '$username'";
    $result = $conn->query($query);
    
    if($result->num_rows == 1) {
        $admin = $result->fetch_assoc();
        if(password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_name'] = $admin['nama_lengkap'];
            header("Location: dashboard.php");
            exit();
        }
    }

    // Jika tidak ditemukan di admin, cek di tabel petugas
    $query = "SELECT * FROM petugas WHERE username = '$username'";
    $result = $conn->query($query);
    
    if($result->num_rows == 1) {
        $petugas = $result->fetch_assoc();
        if(password_verify($password, $petugas['password'])) {
            $_SESSION['petugas_logged_in'] = true;
            $_SESSION['petugas_name'] = $petugas['nama_lengkap'];
            $_SESSION['petugas_id'] = $petugas['id'];
            header("Location: dashboard.php");
            exit();
        }
    }
    
    $error = "Username atau password salah!";
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - D-Gassvit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h1 {
            color: #28a745;
            font-size: 24px;
        }
        .login-type {
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="login-form">
        <div class="login-header">
            <h1>D-Gassvit Admin</h1>
        </div>
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Login</button>
        </form>
        <div class="login-type">
            <small class="text-muted">Login sebagai Admin atau Petugas</small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 