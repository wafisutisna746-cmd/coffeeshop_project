<?php
session_start();
require 'db.php';

// Proteksi Halaman: Hanya admin yang boleh mengakses
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak. Halaman ini hanya untuk Admin.");
}

$message = "";
$message_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $price = $_POST['price'];
    
    // Penanganan Unggah Gambar
    $target_dir = "uploads/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $file_name = time() . '_' . basename($_FILES["image"]["name"]);
    $target_file = $target_dir . $file_name;
    
    // Validasi file gambar
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'webp'];
    
    if (in_array($imageFileType, $allowed_types)) {
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            $stmt = $pdo->prepare("INSERT INTO products (name, price, image_path) VALUES (?, ?, ?)");
            $stmt->execute([$name, $price, $target_file]);
            $message = "Produk berhasil ditambahkan!";
            $message_type = "success";
        } else {
            $message = "Gagal mengunggah gambar.";
            $message_type = "danger";
        }
    } else {
        $message = "Format gambar harus JPG, JPEG, PNG, atau WEBP.";
        $message_type = "warning";
    }
}

// Ambil semua daftar produk untuk ditampilkan di tabel
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Kelola Menu Coffee Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar Admin -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">⚙️ Admin Panel - Coffee Shop</a>
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-outline-light btn-sm me-2">Lihat Halaman Menu</a>
                <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="row g-4">
            <!-- Form Tambah Menu -->
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-3">Tambah Menu Baru</h4>
                        
                        <?php if ($message): ?>
                            <div class="alert alert-<?= $message_type ?> py-2"><?= $message ?></div>
                        <?php endif; ?>

                        <form action="admin.php" method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label class="form-label">Nama Kopi / Makanan</label>
                                <input type="text" name="name" class="form-control" placeholder="Contoh: Caramel Macchiato" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Harga (Rp)</label>
                                <input type="number" step="500" name="price" class="form-control" placeholder="25000" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Foto Produk</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Tambah ke Menu</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar Menu Saat Ini -->
            <div class="col-md-7">
                <div class="card shadow-sm border-0">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-3">Daftar Menu Saat Ini</h4>
                        
                        <?php if (empty($products)): ?>
                            <div class="alert alert-info mb-0">Belum ada menu yang tersimpan. Silakan tambah menu baru di samping.</div>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Gambar</th>
                                            <th>Nama Produk</th>
                                            <th>Harga Normal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td>
                                                    <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="foto" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                                </td>
                                                <td><strong><?= htmlspecialchars($product['name']) ?></strong></td>
                                                <td>Rp <?= number_format($product['price'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
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
