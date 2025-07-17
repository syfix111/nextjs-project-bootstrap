<?php
require_once '../includes/auth.php';
require_operator();

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $nama = sanitize_input($_POST['nama']);
                $nik = sanitize_input($_POST['nik']);
                $alamat = sanitize_input($_POST['alamat']);
                $kategori = sanitize_input($_POST['kategori']);
                $status_bantuan = sanitize_input($_POST['status_bantuan']);
                $tanggal_bantuan = !empty($_POST['tanggal_bantuan']) ? $_POST['tanggal_bantuan'] : null;
                
                $query = "INSERT INTO warga (nama, nik, alamat, kategori, status_bantuan, tanggal_bantuan) VALUES (?, ?, ?, ?, ?, ?)";
                $result = execute_query($query, [$nama, $nik, $alamat, $kategori, $status_bantuan, $tanggal_bantuan], 'ssssss');
                
                if ($result !== false) {
                    $message = 'Data warga berhasil ditambahkan!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menambahkan data warga. NIK mungkin sudah ada.';
                    $message_type = 'danger';
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['id'];
                $nama = sanitize_input($_POST['nama']);
                $nik = sanitize_input($_POST['nik']);
                $alamat = sanitize_input($_POST['alamat']);
                $kategori = sanitize_input($_POST['kategori']);
                $status_bantuan = sanitize_input($_POST['status_bantuan']);
                $tanggal_bantuan = !empty($_POST['tanggal_bantuan']) ? $_POST['tanggal_bantuan'] : null;
                
                $query = "UPDATE warga SET nama=?, nik=?, alamat=?, kategori=?, status_bantuan=?, tanggal_bantuan=? WHERE id=?";
                $result = execute_query($query, [$nama, $nik, $alamat, $kategori, $status_bantuan, $tanggal_bantuan, $id], 'ssssssi');
                
                if ($result !== false) {
                    $message = 'Data warga berhasil diupdate!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal mengupdate data warga.';
                    $message_type = 'danger';
                }
                break;
        }
    }
}

// Get search parameters
$search = isset($_GET['search']) ? sanitize_input($_GET['search']) : '';
$kategori_filter = isset($_GET['kategori']) ? sanitize_input($_GET['kategori']) : '';

// Build query
$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(nama LIKE ? OR nik LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= 'ss';
}

if (!empty($kategori_filter)) {
    $where_conditions[] = "kategori = ?";
    $params[] = $kategori_filter;
    $types .= 's';
}

$where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
$query = "SELECT * FROM warga $where_clause ORDER BY created_at DESC";

$warga_data = execute_query($query, $params, $types);

// Get edit data if editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_result = execute_query("SELECT * FROM warga WHERE id = ?", [$edit_id], 'i');
    if ($edit_result && $edit_result->num_rows > 0) {
        $edit_data = $edit_result->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Warga - Sistem Informasi Desa</title>
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
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="warga.php">Data Warga</a>
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
        <?php if (!empty($message)): ?>
            <div class="alert alert-<?php echo $message_type; ?> alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($message); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Add/Edit Form -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo $edit_data ? 'Edit Data Warga' : 'Tambah Data Warga'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                            <?php if ($edit_data): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="nama" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama" name="nama" 
                                           value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama']) : ''; ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="nik" class="form-label">NIK</label>
                                    <input type="text" class="form-control" id="nik" name="nik" 
                                           value="<?php echo $edit_data ? htmlspecialchars($edit_data['nik']) : ''; ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat</label>
                                <textarea class="form-control" id="alamat" name="alamat" rows="3"><?php echo $edit_data ? htmlspecialchars($edit_data['alamat']) : ''; ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="kategori" class="form-label">Kategori</label>
                                    <select class="form-control" id="kategori" name="kategori" required>
                                        <option value="">Pilih Kategori</option>
                                        <option value="Mampu" <?php echo ($edit_data && $edit_data['kategori'] === 'Mampu') ? 'selected' : ''; ?>>Mampu</option>
                                        <option value="Miskin" <?php echo ($edit_data && $edit_data['kategori'] === 'Miskin') ? 'selected' : ''; ?>>Miskin</option>
                                        <option value="Fakir Miskin" <?php echo ($edit_data && $edit_data['kategori'] === 'Fakir Miskin') ? 'selected' : ''; ?>>Fakir Miskin</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="status_bantuan" class="form-label">Status Bantuan</label>
                                    <select class="form-control" id="status_bantuan" name="status_bantuan" required>
                                        <option value="Belum" <?php echo ($edit_data && $edit_data['status_bantuan'] === 'Belum') ? 'selected' : ''; ?>>Belum</option>
                                        <option value="Sudah" <?php echo ($edit_data && $edit_data['status_bantuan'] === 'Sudah') ? 'selected' : ''; ?>>Sudah</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="tanggal_bantuan" class="form-label">Tanggal Bantuan</label>
                                    <input type="date" class="form-control" id="tanggal_bantuan" name="tanggal_bantuan" 
                                           value="<?php echo $edit_data ? $edit_data['tanggal_bantuan'] : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo $edit_data ? 'Update Data' : 'Tambah Data'; ?>
                                </button>
                                <?php if ($edit_data): ?>
                                    <a href="warga.php" class="btn btn-secondary">Batal</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="search" class="form-label">Cari Nama/NIK</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                           value="<?php echo htmlspecialchars($search); ?>" placeholder="Masukkan nama atau NIK">
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="kategori" class="form-label">Filter Kategori</label>
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
        </div>

        <!-- Data Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Data Warga</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($warga_data && $warga_data->num_rows > 0): ?>
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
                                            <th>Tanggal Bantuan</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        while ($row = $warga_data->fetch_assoc()): 
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
                                                <td><?php echo $row['tanggal_bantuan'] ? date('d/m/Y', strtotime($row['tanggal_bantuan'])) : '-'; ?></td>
                                                <td>
                                                    <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-outline-primary btn-sm">Edit</a>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <p class="text-muted">Tidak ada data warga ditemukan.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
