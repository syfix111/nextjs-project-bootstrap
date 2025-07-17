<?php
require_once '../includes/auth.php';
require_operator();

// Get statistics
$stats = [];

// Total warga
$result = $conn->query("SELECT COUNT(*) as total FROM warga");
$stats['total_warga'] = $result->fetch_assoc()['total'];

// Total miskin
$result = $conn->query("SELECT COUNT(*) as total FROM warga WHERE kategori = 'Miskin'");
$stats['total_miskin'] = $result->fetch_assoc()['total'];

// Total fakir miskin
$result = $conn->query("SELECT COUNT(*) as total FROM warga WHERE kategori = 'Fakir Miskin'");
$stats['total_fakir_miskin'] = $result->fetch_assoc()['total'];

// Total sudah dapat bantuan
$result = $conn->query("SELECT COUNT(*) as total FROM warga WHERE status_bantuan = 'Sudah'");
$stats['sudah_bantuan'] = $result->fetch_assoc()['total'];

// Recent warga data
$recent_warga = $conn->query("SELECT nama, nik, kategori, status_bantuan, created_at FROM warga ORDER BY created_at DESC LIMIT 5");
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Operator - Sistem Informasi Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="dashboard.php">
                <strong>SID</strong> - Sistem Informasi Desa
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="warga.php">Data Warga</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../public/index.php">Lihat Situs Publik</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid py-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="mb-1">Selamat Datang, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h4>
                        <p class="text-muted mb-0">Dashboard Operator - Sistem Informasi Desa</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card">
                    <div class="stats-number"><?php echo number_format($stats['total_warga']); ?></div>
                    <div class="stats-label">Total Penduduk</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card warning">
                    <div class="stats-number"><?php echo number_format($stats['total_miskin']); ?></div>
                    <div class="stats-label">Penduduk Miskin</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card danger">
                    <div class="stats-number"><?php echo number_format($stats['total_fakir_miskin']); ?></div>
                    <div class="stats-label">Fakir Miskin</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-3">
                <div class="stats-card success">
                    <div class="stats-number"><?php echo number_format($stats['sudah_bantuan']); ?></div>
                    <div class="stats-label">Sudah Dapat Bantuan</div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Aksi Cepat</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <a href="warga.php" class="btn btn-primary w-100">
                                    Tambah Data Warga Baru
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="warga.php" class="btn btn-outline-primary w-100">
                                    Lihat Semua Data Warga
                                </a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <a href="../public/index.php" class="btn btn-outline-secondary w-100">
                                    Lihat Situs Publik
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Data -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Data Warga Terbaru</h5>
                        <a href="warga.php" class="btn btn-outline-primary btn-sm">Lihat Semua</a>
                    </div>
                    <div class="card-body">
                        <?php if ($recent_warga->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Nama</th>
                                            <th>NIK</th>
                                            <th>Kategori</th>
                                            <th>Status Bantuan</th>
                                            <th>Tanggal Input</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $recent_warga->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                                <td><?php echo htmlspecialchars($row['nik']); ?></td>
                                                <td>
                                                    <span class="badge <?php 
                                                        echo $row['kategori'] === 'Miskin' ? 'badge-warning' : 
                                                            ($row['kategori'] === 'Fakir Miskin' ? 'badge-danger' : 'badge-primary'); 
                                                    ?>">
                                                        <?php echo htmlspecialchars($row['kategori']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $row['status_bantuan'] === 'Sudah' ? 'badge-success' : 'badge-danger'; ?>">
                                                        <?php echo $row['status_bantuan']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo date('d/m/Y', strtotime($row['created_at'])); ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <p class="text-muted">Belum ada data warga.</p>
                                <a href="warga.php" class="btn btn-primary">Tambah Data Warga</a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container-fluid">
            <p class="mb-0">&copy; 2024 Sistem Informasi Desa. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
