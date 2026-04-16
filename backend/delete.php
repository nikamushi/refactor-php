<?php

/**
 * Delete Product Script
 * Handles product deletion from inventory
 */

include_once '../config/database.php';
include_once '../classes/Product.php';
include_once '../helpers/FileUpload.php';

// Validate product ID
$product_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$product_id) {
    echo "<script>
        alert('ID produk tidak valid');
        document.location.href = '../index.php';
    </script>";
    exit;
}

try {
    $db_connection = get_database_connection();
    $product_manager = new Product($db_connection);

    // Get product details to delete associated image
    $product = $product_manager->get_product_by_id($product_id);

    if (!$product) {
        echo "<script>
            alert('Produk tidak ditemukan');
            document.location.href = '../index.php';
        </script>";
        exit;
    }

    // Delete product from database
    if ($product_manager->delete_product($product_id)) {
        // Delete associated image file
        if (!empty($product['image'])) {
            $file_uploader = new FileUpload();
            $file_uploader->delete_file($product['image']);
        }

        echo "<script>
            alert('Produk berhasil dihapus');
            document.location.href = '../index.php';
        </script>";
    } else {
        echo "<script>
            alert('Gagal menghapus produk');
            document.location.href = '../index.php';
        </script>";
    }
} catch (Exception $e) {
    error_log("Error deleting product: " . $e->getMessage());
    echo "<script>
        alert('Terjadi kesalahan saat menghapus produk');
        document.location.href = '../index.php';
    </script>";
}
