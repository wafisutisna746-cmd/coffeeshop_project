<?php
session_start();
require 'db.php';

$is_logged_in = isset($_SESSION['user_id']);
$discount_rate = 0.10;

$stmt = $pdo->query("SELECT * FROM products");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coffee Shop Menu</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar Header -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">☕ Coffee Shop</a>
            <div class="d-flex align-items-center">
                <?php if ($is_logged_in): ?>
                    <span class="navbar-text me-3">
                        Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>! (Diskon 10% Aktif)
                    </span>
                    <a href="logout.php" class="btn btn-outline-danger btn-sm">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline-light me-2 btn-sm">Login</a>
                    <a href="register.php" class="btn btn-primary btn-sm">Daftar (Diskon 10%)</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container">
        <!-- Hero / Banner -->
        <div class="p-4 mb-4 bg-white rounded-3 shadow-sm border text-center">
            <h1 class="display-6 fw-bold">Nikmati Kopi Pilihan Terbaik</h1>
            <?php if (!$is_logged_in): ?>
                <p class="lead text-muted">Dapatkan potongan diskon <strong>10%</strong> khusus untuk pengguna terdaftar!</p>
            <?php else: ?>
                <p class="lead text-success">Kamu sedang menikmati diskon member 10% untuk semua menu.</p>
            <?php endif; ?>
        </div>

        <!-- Product Grid -->
        <h3 class="mb-3">Daftar Menu</h3>
        <div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
            <?php if (empty($products)): ?>
                <div class="col-12">
                    <div class="alert alert-info">Belum ada produk yang ditambahkan.</div>
                </div>
            <?php endif; ?>

            <?php foreach ($products as $product): ?>
                <?php 
                    $original_price = $product['price'];
                    $final_price = $is_logged_in ? $original_price * (1 - $discount_rate) : $original_price;
                ?>
                <div class="col">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?= htmlspecialchars($product['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['name']) ?>" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <div class="mt-auto">
                                <div class="mb-2">
                                    <?php if ($is_logged_in): ?>
                                        <small class="text-muted text-decoration-line-through">Rp <?= number_format($original_price, 0, ',', '.') ?></small><br>
                                        <span class="fs-5 fw-bold text-success">Rp <?= number_format($final_price, 0, ',', '.') ?></span>
                                        <span class="badge bg-danger ms-1">Diskon 10%</span>
                                    <?php else: ?>
                                        <span class="fs-5 fw-bold text-dark">Rp <?= number_format($original_price, 0, ',', '.') ?></span>
                                    <?php endif; ?>
                                </div>
                                <button class="btn btn-primary w-100">Pesan Sekarang</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Script Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
