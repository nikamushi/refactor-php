<?php
$connect = new PDO("mysql:host=localhost;dbname=test", "root", "");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $connect->prepare("DELETE FROM tbl_product WHERE id = :id");
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    echo "<script>
            alert('data berhasil dihapus');
            document.location.href = '../index.php';
            </script>";

} else {
    echo "<script>
          alert('data gagal dihapus');
          document.location.href = '../index.php';
          </script";
    
}