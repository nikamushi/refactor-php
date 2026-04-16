<?php
$connect = new PDO("mysql:host=localhost;dbname=test", "root", "");

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


    $stmt = $connect->prepare("INSERT INTO tbl_product (name, price, image, quantity) VALUES (:name,:price,:image,:quantity)");

    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':image', $fileName);
    $stmt->bindParam(':quantity', $quantity);
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
            <h1>Stok</h1>
            <div class="row">
                <div class="col-md-8">
                    <div class="form-group">
                        <label>Nama Barang</label>
                        <input class="form-control" type="text" name="name">

                        <label style="margin-top: 20px;">Gambar Sepatu</label>
                        <input type="file" name="image">

                        <label style="margin-top: 20px;">Jumlah</label>
                        <input class="form-control" type="text" name="quantity">

                        <label style="margin-top: 20px;">Harga</label>
                        <input class="form-control" type="text" name="price">

                        <button style="margin-top: 20px;" type="submit" name="btn">tambah</button>
                        <a href="../index.php">kembali</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</body>

</html>