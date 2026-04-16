<?php

/**
 * Main Index Page
 * Displays products and shopping cart
 */

require_once 'config/database.php';
require_once 'classes/Product.php';
require_once 'classes/ShoppingCart.php';

// Initialize
$db_connection = get_database_connection();
$product_manager = new Product($db_connection);
$shopping_cart = new ShoppingCart();
$message = '';

// Handle cart actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $item_id = filter_input(INPUT_POST, 'hidden_id', FILTER_VALIDATE_INT);
    $item_name = filter_input(INPUT_POST, 'hidden_name', FILTER_SANITIZE_STRING);
    $item_price = filter_input(INPUT_POST, 'hidden_price', FILTER_VALIDATE_FLOAT);
    $quantity = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);

    if ($item_id && $item_name && $item_price && $quantity > 0) {
        $shopping_cart->add_item($item_id, $item_name, $item_price, $quantity);
        header("Location: index.php?success=1");
        exit;
    }
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'delete':
            $item_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
            if ($item_id) {
                $shopping_cart->remove_item($item_id);
                header("Location: index.php?remove=1");
                exit;
            }
            break;

        case 'clear':
            $shopping_cart->clear_cart();
            header("Location: index.php?clearall=1");
            exit;
            break;
    }
}

// Set success messages
if (isset($_GET['success'])) {
    $message = '<div class="alert alert-success alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        Barang Berhasil Ditambahkan
    </div>';
}

if (isset($_GET['remove'])) {
    $message = '<div class="alert alert-success alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        Barang Dicancel
    </div>';
}

if (isset($_GET['clearall'])) {
    $message = '<div class="alert alert-success alert-dismissible">
        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        Barang Dibersihkan
    </div>';
}

// Get data
$all_products = $product_manager->get_all_products();
$cart_items = $shopping_cart->get_cart_items();
$cart_total = $shopping_cart->get_cart_total();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Toko Sepatu</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>

<body>
    <br />
    <div class="container">
        <h3 align="center">Toko Sepatu</h3>
        <br />

        <!-- Product Display -->
        <?php foreach ($all_products as $product): ?>
            <div class="col-md-3">
                <form method="post" style="margin-bottom: 25px;">
                    <div style="border:1px solid #333; background-color:#f1f1f1; border-radius:5px; padding:16px" align="center">
                        <img src="images/<?php echo htmlspecialchars($product['image']); ?>"
                            style="width: 175px; height: 125px;"
                            class="img-responsive"
                            alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <br />

                        <h4 class="text-info">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h4>

                        <h4 class="text-danger">
                            $ <?php echo number_format($product['price'], 2); ?>
                        </h4>

                        <input type="number" name="quantity" value="1" min="1" class="form-control" required />
                        <input type="hidden" name="hidden_name" value="<?php echo htmlspecialchars($product['name']); ?>" />
                        <input type="hidden" name="hidden_price" value="<?php echo $product['price']; ?>" />
                        <input type="hidden" name="hidden_id" value="<?php echo $product['id']; ?>" />

                        <input type="submit" name="add_to_cart" style="margin-top:5px;" class="btn btn-success" value="Tambahkan" />
                    </div>
                </form>
            </div>
        <?php endforeach; ?>

        <div style="clear:both"></div>
        <br />

        <!-- Stock Table -->
        <h3>Stok Barang</h3>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="35%">Nama Barang</th>
                        <th width="10%">Jumlah</th>
                        <th width="20%">Harga</th>
                        <th width="20%">Gambar</th>
                        <th width="5%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo $product['quantity']; ?></td>
                            <td>$ <?php echo number_format($product['price'], 2); ?></td>
                            <td>
                                <img src="images/<?php echo htmlspecialchars($product['image']); ?>"
                                    style="width: 175px; height: 125px"
                                    class="img-responsive"
                                    alt="<?php echo htmlspecialchars($product['name']); ?>">
                            </td>
                            <td>
                                <a href="backend/update.php?id=<?php echo $product['id']; ?>">Update</a> |
                                <a href="backend/delete.php?id=<?php echo $product['id']; ?>"
                                    onclick="return confirm('Yakin ingin menghapus?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div style="clear:both"></div>
        <br />

        <!-- Shopping Cart -->
        <h3>Detail Belanja</h3>
        <div class="table-responsive">
            <?php echo $message; ?>
            <div align="right">
                <a href="index.php?action=clear" onclick="return confirm('Clear semua item?')">
                    <b>Clear Cart</b>
                </a>
            </div>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th width="40%">Nama Barang</th>
                        <th width="10%">Jumlah</th>
                        <th width="20%">Harga</th>
                        <th width="15%">Total</th>
                        <th width="5%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($cart_items)): ?>
                        <?php foreach ($cart_items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                <td><?php echo $item['item_quantity']; ?></td>
                                <td>$ <?php echo number_format($item['item_price'], 2); ?></td>
                                <td>$ <?php echo number_format($item['item_quantity'] * $item['item_price'], 2); ?></td>
                                <td>
                                    <a href="index.php?action=delete&id=<?php echo $item['item_id']; ?>"
                                        class="text-danger">Cancel</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3" align="right"><strong>Total</strong></td>
                            <td align="right"><strong>$ <?php echo number_format($cart_total, 2); ?></strong></td>
                            <td></td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" align="center">Tidak Ada Barang yang Ditambahkan</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <a href="backend/add_product.php" class="btn btn-primary">Tambah Produk Baru</a>
        </div>
    </div>
</body>

</html>
