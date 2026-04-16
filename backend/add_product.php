<?php

/**
 * Add Product Page
 * Handles adding new products to inventory
 */

include_once '../config/database.php';
include_once '../classes/Product.php';
include_once '../helpers/FileUpload.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_add'])) {
    // Validate and sanitize inputs
    $product_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $product_price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $product_quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    // Validation
    if (empty($product_name)) {
        $error_message = "Nama produk harus diisi";
    } elseif ($product_price === false || $product_price <= 0) {
        $error_message = "Harga harus berupa angka positif";
    } elseif ($product_quantity === false || $product_quantity < 0) {
        $error_message = "Jumlah harus berupa angka non-negatif";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] === UPLOAD_ERR_NO_FILE) {
        $error_message = "Gambar produk harus diupload";
    } else {
        // Handle file upload
        $file_uploader = new FileUpload();
        $uploaded_filename = $file_uploader->upload($_FILES['image']);

        if ($uploaded_filename) {
            // Save to database
            $db_connection = get_database_connection();
            $product_manager = new Product($db_connection);

            if ($product_manager->add_product($product_name, $product_price, $uploaded_filename, $product_quantity)) {
                $success_message = "Produk berhasil ditambahkan!";
                // Redirect after success
                header("Location: ../index.php?product_added=1");
                exit;
            } else {
                $error_message = "Gagal menyimpan produk ke database";
                $file_uploader->delete_file($uploaded_filename);
            }
        } else {
            $errors = $file_uploader->get_errors();
            $error_message = "Upload gagal: " . implode(", ", $errors);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Toko Sepatu</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>

<body>
    <div class="container">
        <h1>Tambah Produk Baru</h1>

        <?php if ($error_message): ?>
            <div class="alert alert-danger alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success alert-dismissible">
                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label for="name">Nama Barang <span class="text-danger">*</span></label>
                        <input class="form-control"
                            type="text"
                            id="name"
                            name="name"
                            required
                            placeholder="Masukkan nama produk">
                    </div>

                    <div class="form-group">
                        <label for="image">Gambar Sepatu <span class="text-danger">*</span></label>
                        <input type="file"
                            id="image"
                            name="image"
                            accept="image/*"
                            required>
                        <p class="help-block">Format: JPG, PNG, GIF, WEBP (Max 5MB)</p>
                    </div>

                    <div class="form-group">
                        <label for="quantity">Jumlah <span class="text-danger">*</span></label>
                        <input class="form-control"
                            type="number"
                            id="quantity"
                            name="quantity"
                            min="0"
                            required
                            placeholder="0">
                    </div>

                    <div class="form-group">
                        <label for="price">Harga ($) <span class="text-danger">*</span></label>
                        <input class="form-control"
                            type="number"
                            id="price"
                            name="price"
                            step="0.01"
                            min="0"
                            required
                            placeholder="0.00">
                    </div>

                    <button type="submit" name="btn_add" class="btn btn-primary">
                        <span class="glyphicon glyphicon-plus"></span> Tambah Produk
                    </button>
                    <a href="../index.php" class="btn btn-default">
                        <span class="glyphicon glyphicon-arrow-left"></span> Kembali
                    </a>
                </div>
            </div>
        </form>
    </div>
</body>

</html>
