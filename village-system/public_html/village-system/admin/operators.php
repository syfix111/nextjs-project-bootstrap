<?php
require_once '../includes/auth.php';
require_admin();

$message = '';
$message_type = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'add':
                $nama = sanitize_input($_POST['nama']);
                $email = sanitize_input($_POST['email']);
                $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                
                $query = "INSERT INTO admin (nama, email, password, role) VALUES (?, ?, ?, 'operator')";
                $result = execute_query($query, [$nama, $email, $password], 'sss');
                
                if ($result !== false) {
                    $message = 'Operator berhasil ditambahkan!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menambahkan operator. Email mungkin sudah ada.';
                    $message_type = 'danger';
                }
                break;
                
            case 'edit':
                $id = (int)$_POST['id'];
                $nama = sanitize_input($_POST['nama']);
                $email = sanitize_input($_POST['email']);
                
                if (!empty($_POST['password'])) {
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $query = "UPDATE admin SET nama=?, email=?, password=? WHERE id=? AND role='operator'";
                    $result = execute_query($query, [$nama, $email, $password, $id], 'sssi');
                } else {
                    $query = "UPDATE admin SET nama=?, email=? WHERE id=? AND role='operator'";
                    $result = execute_query($query, [$nama, $email, $id], 'ssi');
                }
                
                if ($result !== false) {
                    $message = 'Data operator berhasil diupdate!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal mengupdate data operator.';
                    $message_type = 'danger';
                }
                break;
                
            case 'delete':
                $id = (int)$_POST['id'];
                $query = "DELETE FROM admin WHERE id=? AND role='operator'";
                $result = execute_query($query, [$id], 'i');
                
                if ($result !== false) {
                    $message = 'Operator berhasil dihapus!';
                    $message_type = 'success';
                } else {
                    $message = 'Gagal menghapus operator.';
                    $message_type = 'danger';
                }
                break;
        }
    }
}

// Get operators data
$operators = execute_query("SELECT * FROM admin WHERE role = 'operator' ORDER BY created_at DESC", [], '');

// Get edit data if editing
$edit_data = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    $edit_result = execute_query("SELECT * FROM admin WHERE id = ? AND role = 'operator'", [$edit_id], 'i');
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
    <title>Kelola Operator - Sistem Informasi Desa</title>
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
                        <a class="nav-link" href="warga.php">Data Warga</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="operators.php">Kelola Operator</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="import.php">Import Excel</a>
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
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><?php echo $edit_data ? 'Edit Operator' : 'Tambah Operator Baru'; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="">
                            <input type="hidden" name="action" value="<?php echo $edit_data ? 'edit' : 'add'; ?>">
                            <?php if ($edit_data): ?>
                                <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
                            <?php endif; ?>
                            
                            <div class="mb-3">
                                <label for="nama" class="form-label">Nama Lengkap</label>
                                <input type="text" class="form-control" id="nama" name="nama" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['nama']) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo $edit_data ? htmlspecialchars($edit_data['email']) : ''; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">
                                    Password <?php echo $edit_data ? '(Kosongkan jika tidak ingin mengubah)' : ''; ?>
                                </label>
                                <input type="password" class="form-control" id="password" name="password" 
                                       <?php echo !$edit_data ? 'required' : ''; ?>>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <?php echo $edit_data ? 'Update Operator' : 'Tambah Operator'; ?>
                                </button>
                                <?php if ($edit_data): ?>
                                    <a href="operators.php" class="btn btn-secondary">Batal</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h6>Hak Akses Operator:</h6>
                            <ul class="mb-0">
                                <li>Dapat menambah dan mengedit data warga</li>
                                <li>Tidak dapat menghapus data warga</li>
                                <li>Tidak dapat mengelola operator lain</li>
                                <li>Tidak dapat mengimpor data Excel</li>
                            </ul>
                        </div>
                        
                        <div class="alert alert-warning">
                            <strong>Catatan:</strong> Pastikan email yang digunakan valid karena akan digunakan untuk login operator.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operators List -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Daftar Operator</h5>
                    </div>
                    <div class="card-body">
                        <?php if ($operators && $operators->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Nama</th>
                                            <th>Email</th>
                                            <th>Tanggal Dibuat</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        while ($row = $operators->fetch_assoc()): 
                                        ?>
                                            <tr>
                                                <td><?php echo $no++; ?></td>
                                                <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="?edit=<?php echo $row['id']; ?>" class="btn btn-outline-primary">Edit</a>
                                                        <button type="button" class="btn btn-outline-danger" 
                                                                onclick="confirmDelete(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['nama']); ?>')">
                                                            Hapus
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <p class="text-muted">Belum ada operator yang terdaftar.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menghapus operator <strong id="deleteName"></strong>?</p>
                    <div class="alert alert-warning">
                        <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" style="display: inline;">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="id" id="deleteId">
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function confirmDelete(id, name) {
            document.getElementById('deleteId').value = id;
            document.getElementById('deleteName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>
