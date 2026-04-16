<?php

/**
 * Update Product Page
 */

include_once '../config/database.php';
include_once '../classes/Product.php';
include_once '../helpers/FileUpload.php';

$error_message = '';
$success_message = '';

// Validate ID
$product_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$product_id) {
    header("Location: ../index.php");
    exit;
}

// Initialize
$db_connection = get_database_connection();
$product_manager = new Product($db_connection);

// Get existing product
$product = $product_manager->get_product_by_id($product_id);
if (!$product) {
    header("Location: ../index.php");
    exit;
}

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_update'])) {

    $product_name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $product_price = filter_input(INPUT_POST, 'price', FILTER_VALIDATE_FLOAT);
    $product_quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    $current_image = $product['image'];
    $new_image = $current_image;

    // Validation
    if (empty($product_name)) {
        $error_message = "Nama produk harus diisi";
    } elseif ($product_price === false || $product_price <= 0) {
        $error_message = "Harga harus berupa angka positif";
    } elseif ($product_quantity === false || $product_quantity < 0) {
        $error_message = "Jumlah harus berupa angka non-negatif";
    } else {

        // Handle image upload (optional)
        if (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
            $file_uploader = new FileUpload();
            $uploaded_filename = $file_uploader->upload($_FILES['image']);

            if ($uploaded_filename) {
                $new_image = $uploaded_filename;

                // Delete old image
                if (!empty($current_image)) {
                    $file_uploader->delete_file($current_image);
                }
            } else {
                $errors = $file_uploader->get_errors();
                $error_message = "Upload gagal: " . implode(", ", $errors);
            }
        }

        // If no upload error
        if (empty($error_message)) {
            if ($product_manager->update_product(
                $product_id,
                $product_name,
                $product_price,
                $new_image,
                $product_quantity
            )) {
                header("Location: ../index.php?updated=1");
                exit;
            } else {
                $error_message = "Gagal update produk";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Update Produk</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
</head>

<body>
    <div class="container">
        <h2>Update Produk</h2>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Nama Produk</label>
                <input type="text" name="name" class="form-control"
                    value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Harga</label>
                <input type="number" name="price" step="0.01" class="form-control"
                    value="<?php echo $product['price']; ?>" required>
            </div>

            <div class="form-group">
                <label>Jumlah</label>
                <input type="number" name="quantity" class="form-control"
                    value="<?php echo $product['quantity']; ?>" required>
            </div>

            <div class="form-group">
                <label>Gambar Saat Ini</label><br>
                <img src="../images/<?php echo htmlspecialchars($product['image']); ?>"
                    width="150">
            </div>

            <div class="form-group">
                <label>Ganti Gambar (Opsional)</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <button type="submit" name="btn_update" class="btn btn-success">
                Update
            </button>

            <a href="../index.php" class="btn btn-default">Kembali</a>
        </form>
    </div>
</body>

</html>
