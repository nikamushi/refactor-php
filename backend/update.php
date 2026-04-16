<?php
$connect = new PDO("mysql:host=localhost;dbname=test", "root", "");

$id = $_GET['id'];

$query = "SELECT * FROM  tbl_product WHERE id = $id";
$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();

if (isset($_POST['btn'])) {

    $name = $_POST['name'];
    $price = $_POST['price'];
    $quantity = $_POST['quantity'];

    $fileName = "";

    echo "<script>
    alert('data berhasil ditambah');
    document.location.href = '../index.php';
    </script";

    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];

        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileError = $file['error'];

        $folder = "C:\\xampp\\htdocs\\test\\test\\images\\";

        if ($fileError === UPLOAD_ERR_OK) {
            move_uploaded_file($fileTmp, $folder . $fileName);
        }
    }

    $stmt = $connect->prepare("UPDATE tbl_product SET name = :name, quantity = :quantity, price = :price, image = :image WHERE id = :id");

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':quantity', $quantity);
    $stmt->bindParam(':image', $fileName);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
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
    <form action="" method="post" enctype="multipart/form-data">
        <div class="container">
            <h1>Edit Stok Barang</h1>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input class="form-control" type="text" name="name" value="<?= $result[0]["name"]; ?>">

                        <label style="margin-top: 20px;">Gambar Sepatu</label>
                        <input type="file" name="image">

                        <label style="margin-top: 20px;">Jumlah</label>
                        <input class="form-control" type="text" name="quantity" value="<?= $result[0]["quantity"]; ?>">

                        <label style="margin-top: 20px;">Harga</label>
                        <input class="form-control" type="text" name="price" value="<?= $result[0]["price"]; ?>">

                        <button style="margin-top: 20px;" type="submit" name="btn">Ubah</button>
                        <a href="../index.php">Kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</body>

</html>