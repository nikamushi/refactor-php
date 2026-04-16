<?php
$connect = new PDO("mysql:host=localhost;dbname=test", "root", "");

$message = '';

if (isset($_POST["add_to_cart"])) {
    if (isset($_COOKIE["shopping_cart"])) {
        $cookie_data = stripslashes($_COOKIE["shopping_cart"]);

        $cart_data = json_decode($cookie_data, true);
    } else {
        $cart_data = array();
    }

    $item_id_list = array_column($cart_data, 'item_id');

    if (in_array($_POST["hidden_id"], $item_id_list)) {
        foreach ($cart_data as $keys => $values) {
            if ($cart_data[$keys]["item_id"] == $_POST["hidden_id"]) {
                $cart_data[$keys]["item_quantity"] = $cart_data[$keys]["item_quantity"] + $_POST["quantity"];
            }
        }
    } else {
        $item_array = array(
            'item_id' => $_POST["hidden_id"],
            'item_name' => $_POST["hidden_name"],
            'item_price' => $_POST["hidden_price"],
            'item_quantity' => $_POST["quantity"],
        );
        $cart_data[] = $item_array;
    }

    $item_data = json_encode($cart_data);
    setcookie('shopping_cart', $item_data, time() + (86400 * 30));
    header("Location:index.php?success=1");
}

if (isset($_GET["action"])) {
    if ($_GET["action"] == "delete") {
        $cookie_data = stripslashes($_COOKIE["shopping_cart"]);
        $cart_data = json_decode($cookie_data, true);

        foreach ($cart_data as $keys => $values) {
            if ($cart_data[$keys]['item_id'] ==  $_GET['id']) {
                unset($cart_data[$keys]);
                $item_data = json_encode($cart_data);
                setcookie('shopping_cart', $item_data, time() + (86400 * 30));
                header("location:index.php?remove=1");
            }
        }
    }
    if ($_GET["action"] == "clear") {
        setcookie("shopping_cart", "", time() - 3600);
        header("Location:index.php?clearall=1");
    }
}

if (isset($_GET["success"])) {
    $message = '<div class="alert alert-success alert-dismissible">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    Barang Berhasil Ditambahkan
    </div>';
}

if (isset($_GET["remove"])) {
    $message = '<div class="alert alert-success alert-dismissible">
       <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
       Barang Dicancel
       </div>';
}
if (isset($_GET["clearall"])) {
    $message = '<div class="alert alert-success alert-dismissible">
           <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
           Barang Dibersihkan
           </div>';
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Toko Sepatu</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
</head>

<body>

    <br />
    <div class="container">
        <h3 align="center">Toko Sepatu
        </h3> <br />
        <br />

        <?php
        $query = "SELECT * FROM  tbl_product ORDER BY id ASC";
        $statement = $connect->prepare($query);
        $statement->execute();
        $result = $statement->fetchAll();
        foreach ($result as $row) {
        ?>

            <div class="col-md-3">
                <form method="post" style="margin-bottom: 25px;">
                    <div style="border:1px solid #333; background-color:#f1f1f1; border-radius:5px; padding:16px" align="center">
                        <img src="images/<?php echo $row["image"]; ?>" style="width: 175px; height: 125px;" class="img-responsive /"><br />

                        <h4 class="text-info">
                            <?php
                            echo $row["name"];
                            ?>
                        </h4>

                        <h4 class="text-danger">
                            $ <?php
                                echo $row["price"];
                                ?>
                        </h4>

                        <input type="text" name="quantity" value="1" class="form-control" />
                        <input type="hidden" name="hidden_name" value="<?php echo $row["name"]; ?>" />
                        <input type="hidden" name="hidden_price" value="<?php echo $row["price"]; ?>" />
                        <input type="hidden" name="hidden_id" value="<?php echo $row["id"]; ?>" />

                        <input type="submit" name="add_to_cart" style="margin-top:5px;" class="btn btn-success" value="Tambahkan" />
                    </div>
                </form>
            </div>

        <?php
        }
        ?>


        <div style="clear:both"></div>
        <br />
        <h3>Stok Barang</h3>
        <div class="table-responsive">

            <table class="table table-bordered">
                <tr>
                    <th width="35%">Nama Barang</th>
                    <th width="10%">Jumlah</th>
                    <th width="20%">Harga</th>
                    <th width="20%">Gambar</th>
                    <th width="5%">Action</th>
                </tr>

                <?php
                $query = "SELECT * FROM  tbl_product";
                $statement = $connect->query($query);
                $statement->execute();

                $result = $statement->fetchAll();
                foreach ($result as $row) {
                ?>
                    <tr>
                        <td><?php echo $row["name"]; ?></td>
                        <td><?php echo $row["quantity"]; ?></td>
                        <td><?php echo $row["price"]; ?></td>
                        <td><img src="images/<?php echo $row["image"]; ?>" style="width: 175px; height: 125px" class="img-responsive"></td>
                        <td>
                            <a href="backend/update.php?id=<?= $row['id']; ?>">Update</a>
                            <a href="backend/delete.php?id=<?= $row['id']; ?>">Delete</a>
                        </td>
                    </tr>
                <?php
                }
                ?>
            </table>


            <div style="clear:both"></div>
            <br />
            <h3>Detail Belanja</h3>
            <div class="table-responsive">
                <?php echo $message; ?>
                <div align="right">
                    <a href="index.php?action=clear"><b>Clear</b></a>
                </div>
                <table class="table table-bordered">
                    <tr>
                        <th width="40%">Nama Barang</th>
                        <th width="10%">Jumlah</th>
                        <th width="20%">Harga</th>
                        <th width="15%">Total</th>
                        <th width="5%">Action</th>
                    </tr>

                    <?php
                    if (isset($_COOKIE["shopping_cart"])) {
                        $total = 0;
                        $cookie_data = stripslashes($_COOKIE['shopping_cart']);
                        $cart_data = json_decode($cookie_data, true);
                        foreach ($cart_data as $keys => $values) {
                    ?>
                            <tr>
                                <td><?php echo $values["item_name"]; ?></td>
                                <td><?php echo $values["item_quantity"]; ?></td>
                                <td>$ <?php echo $values["item_price"]; ?></td>
                                <td>$ <?php echo number_format($values["item_quantity"] * $values["item_price"], 2); ?></td>
                                <td><a href="index.php?action=delete&id=<?php echo $values["item_id"]; ?>"><span class="text-danger">Cancel</span></a></td>
                            </tr>

                        <?php
                            $total = $total + ($values["item_quantity"] * $values["item_price"]);
                        }
                        ?>

                        <tr>
                            <td colspan="3" align="right">Total</td>
                            <td align="right">$ <?php echo number_format($total, 2); ?></td>
                            <td></td>
                        </tr>

                    <?php
                    } else {
                        echo
                        '<tr>
                    <td colspan="5" align="center">Tidak Ada Barang yang Ditambahkan</td>
                </tr>';
                    }
                    ?>
                </table>
                <a href="backend/datasepatu.php">Stok Sepatu</a>
            </div>
        </div>
        <br />
</body>

</html>