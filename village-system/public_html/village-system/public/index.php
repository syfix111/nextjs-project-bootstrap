<?php
require_once '../includes/db.php';

// Get statistics for public display
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

// Total belum dapat bantuan
$result = $conn->query("SELECT COUNT(*) as total FROM warga WHERE status_bantuan = 'Belum'");
$stats['belum_bantuan'] = $result->fetch_assoc()['total'];

// Handle search
$search_results = null;
$search_query = '';
$kategori_filter = '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search_query = sanitize_input($_GET['search']);
    $kategori_filter = isset($_GET['kategori']) ? sanitize_input($_GET['kategori']) : '';
    
    $where_conditions = ["nama LIKE ?"];
    $params = ["%$search_query%"];
    $types = 's';
    
    if (!empty($kategori_filter)) {
        $where_conditions[] = "kategori = ?";
        $params[] = $kategori_filter;
        $types .= 's';
    }
    
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
    $query = "SELECT nama, nik, alamat, kategori, status_bantuan FROM warga $where_clause ORDER BY nama ASC";
    
    $search_results = execute_query($query, $params, $types);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Informasi Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <strong>SID</strong> - Sistem Informasi Desa
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#statistik">Statistik</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#pencarian">Pencarian</a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="../admin/login.php">Login Admin</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../operator/login.php">Login Operator</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="hero-title">Sistem Informasi Desa</h1>
                    <p class="hero-subtitle">
                        Portal informasi data penduduk dan bantuan sosial desa. 
                        Akses mudah untuk melihat statistik dan mencari data warga.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section id="statistik" class="py-5">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="mb-3">Statistik Penduduk</h2>
                    <p class="text-muted">Data terkini penduduk dan bantuan sosial</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stats-card text-center">
                        <div class="stats-number"><?php echo number_format($stats['total_warga']); ?></div>
                        <div class="stats-label">Total Penduduk</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stats-card warning text-center">
                        <div class="stats-number"><?php echo number_format($stats['total_miskin']); ?></div>
                        <div class="stats-label">Penduduk Miskin</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stats-card danger text-center">
                        <div class="stats-number"><?php echo number_format($stats['total_fakir_miskin']); ?></div>
                        <div class="stats-label">Fakir Miskin</div>
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <div class="stats-card success text-center">
                        <div class="stats-number"><?php echo number_format($stats['sudah_bantuan']); ?></div>
                        <div class="stats-label">Sudah Dapat Bantuan</div>
                    </div>
                </div>
            </div>
            
            <!-- Additional Stats -->
            <div class="row mt-4">
                <div class="col-lg-6 mx-auto">
                    <div class="card">
                        <div class="card-header text-center">
                            <h5 class="mb-0">Status Bantuan</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="stats-card success mb-0">
                                        <div class="stats-number"><?php echo $stats['sudah_bantuan']; ?></div>
                                        <div class="stats-label">Sudah Terima</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stats-card danger mb-0">
                                        <div class="stats-number"><?php echo $stats['belum_bantuan']; ?></div>
                                        <div class="stats-label">Belum Terima</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Search Section -->
    <section id="pencarian" class="py-5 bg-light">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="mb-3">Pencarian Data Warga</h2>
                    <p class="text-muted">Cari data warga berdasarkan nama dan kategori</p>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="search-box">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="search" class="form-label">Nama Warga</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="<?php echo htmlspecialchars($search_query); ?>" 
                                           placeholder="Masukkan nama warga">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="kategori" class="form-label">Kategori</label>
                                    <select class="form-control" id="kategori" name="kategori">
                                        <option value="">Semua Kategori</option>
                                        <option value="Mampu" <?php echo $kategori_filter === 'Mampu' ? 'selected' : ''; ?>>Mampu</option>
                                        <option value="Miskin" <?php echo $kategori_filter === 'Miskin' ? 'selected' : ''; ?>>Miskin</option>
                                        <option value="Fakir Miskin" <?php echo $kategori_filter === 'Fakir Miskin' ? 'selected' : ''; ?>>Fakir Miskin</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100">Cari</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Search Results -->
            <?php if ($search_results !== null): ?>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    Hasil Pencarian 
                                    <?php if (!empty($search_query)): ?>
                                        untuk "<?php echo htmlspecialchars($search_query); ?>"
                                    <?php endif; ?>
                                    (<?php echo $search_results->num_rows; ?> data ditemukan)
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($search_results->num_rows > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama</th>
                                                    <th>NIK</th>
                                                    <th>Alamat</th>
                                                    <th>Kategori</th>
                                                    <th>Status Bantuan</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $no = 1;
                                                while ($row = $search_results->fetch_assoc()): 
                                                ?>
                                                    <tr>
                                                        <td><?php echo $no++; ?></td>
                                                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['nik']); ?></td>
                                                        <td><?php echo htmlspecialchars($row['alamat']); ?></td>
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
                                                    </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <div class="text-center py-4">
                                        <p class="text-muted">Tidak ada data yang ditemukan dengan kriteria pencarian tersebut.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Information Section -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Akses Admin</h5>
                            <p class="card-text">Login sebagai admin untuk mengelola data warga, operator, dan mengimpor data Excel.</p>
                            <a href="../admin/login.php" class="btn btn-primary">Login Admin</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Akses Operator</h5>
                            <p class="card-text">Login sebagai operator untuk menambah dan mengedit data warga sesuai kewenangan.</p>
                            <a href="../operator/login.php" class="btn btn-outline-primary">Login Operator</a>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <h5 class="card-title">Informasi Publik</h5>
                            <p class="card-text">Akses statistik dan pencarian data warga tersedia untuk umum tanpa perlu login.</p>
                            <a href="#statistik" class="btn btn-outline-primary">Lihat Statistik</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 Sistem Informasi Desa. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0">Dibuat dengan PHP Native & Bootstrap</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>
