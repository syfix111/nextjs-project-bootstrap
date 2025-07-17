<?php
require_once '../includes/auth.php';
require_admin();

// Handle Excel upload
$message = '';
$message_type = '';

// Handle Excel upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['excel_file'])) {
    $file = $_FILES['excel_file'];
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);

    if (in_array(strtolower($file_extension), ['xlsx', 'xls'])) {
        // For now, we'll create a simple CSV import as placeholder
        // In production, you would use PhpSpreadsheet library

        $upload_path = '../uploads/' . time() . '_' . basename($file['name']);
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            // Simple CSV processing placeholder
            $success_count = 0;
            $handle = fopen($upload_path, 'r');
            while (($data = fgetcsv($handle)) !== FALSE) {
                // Process data
                $name = sanitize_input($data[0]);
                $nik = sanitize_input($data[1]);
                $alamat = sanitize_input($data[2]);
                $kategori = sanitize_input($data[3]);
                $status_bantuan = sanitize_input($data[4]);
                $tanggal_bantuan = !empty($data[5]) ? $data[5] : null;

                $query = "INSERT INTO warga (nama, nik, alamat, kategori, status_bantuan, tanggal_bantuan) VALUES (?, ?, ?, ?, ?, ?)";
                execute_query($query, [$name, $nik, $alamat, $kategori, $status_bantuan, $tanggal_bantuan], 'ssssss');
            }
            fclose($handle);
            $message = "Successfully imported data from Excel";
        }
    }
}
