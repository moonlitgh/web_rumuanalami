<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">D-Gassvit Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'manage_petugas.php' ? 'active' : '' ?>" href="manage_petugas.php">
                        <i class="bi bi-people"></i> Kelola Petugas
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= in_array(basename($_SERVER['PHP_SELF']), ['manage_testimonials.php', 'edit_testimonial.php']) ? 'active' : '' ?>" href="manage_testimonials.php">
                        <i class="bi bi-camera-video"></i> Kelola Testimoni
                    </a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php" onclick="return confirm('Apakah Anda yakin ingin logout?')">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    .navbar {
        background-color: #28a745;
        padding: 15px 0;
        margin-bottom: 30px;
    }

    .navbar-brand {
        color: white !important;
        font-weight: bold;
        font-size: 24px;
        padding-left: 15px;
    }

    .nav-link {
        color: rgba(255,255,255,0.9) !important;
        padding: 8px 15px !important;
        border-radius: 5px;
        transition: all 0.3s ease;
        margin: 0 5px;
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
        margin-right: 15px;
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

        .nav-link {
            margin: 5px 0;
        }
    }

    /* Tambahan untuk halaman edit */
    .nav-link[href="manage_testimonials.php"].active {
        background-color: rgba(255,255,255,0.2);
    }
</style> 