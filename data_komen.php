<?php
session_start();
include 'koneksi.php';

if (isset($_GET['delete']) && isset($_GET['type'])) {
    $id = $_GET['delete'];
    $type = $_GET['type'];

    if ($type == 'komentarfoto') {
        // Hapus foto
        $deletequery = "DELETE FROM komentarfoto WHERE KomentarID = ?";
        $stmt = $conn->prepare($deletequery);
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo "<script>alert('Berhasil Menghapus Komentar');</script>";
        } else {
            echo "<script>alert('Gagal Menghapus Komentar');</script>";
        }
    }
    $stmt->close();
    header("location: file.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Data Komentar</title>
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
</head>

<body class="gallery-page">
<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid position-relative d-flex align-items-center justify-content-between">
        <a href="gallery.php" class="logo d-flex align-items-center me-auto me-xl-0">
            <i class="bi bi-camera"></i>
            <h1 class="sitename">GalleryPhoto</h1>
        </a>
        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="admin.php">Kembali</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
    </div>
</header>


<main class="main">
   
      <!-- Tabel Daftar Komentar -->
      <div class="table-wrapper">
        <div class="container">
            <h2 class="text-center">Daftar Komentar</h2>
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>FotoID</th>
                        <th>UserID</th>
                        <th>Isi Komentar</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM komentarfoto");
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <th scope='row'>{$i}</th>
                                <td>{$row['FotoID']}</td>
                                <td>{$row['UserID']}</td>
                                <td>{$row['IsiKomentar']}</td>
                                <td>
                                    <form action='file.php' method='GET' style='display:inline;'>
                                        <input type='hidden' name='delete' value='{$row['UserID']}'>
                                        <input type='hidden' name='type' value='user'>
                                        <button type='submit' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus pengguna ini?\");'>Hapus</button>
                                    </form>
                                </td>
                              </tr>";
                        $i++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<!--footer-->
<footer id="footer" class="footer">

<div class="container p-3">
  <div class="copyright text-center ">
    <p>© <span>Copyright</span> <strong class="px-1 sitename">GalleryPhoto</strong> <span>All Rights Reserved</span></p>
  </div>
  
  <div class="credits">
    Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
  </div>
</div>

</footer>

<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
