<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['Tambah'])) {
        $JudulFoto = $_POST['JudulFoto'];
        $deskripsiFoto = $_POST['deskripsiFoto'];
        $targetDir = "mage/";
        $originalFileName = basename($_FILES["file"]["name"]);
        $imageFileType = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
        
        // Encrypt file name with unique hash
        $encryptedFileName = md5(uniqid(rand(), true)) . '.' . $imageFileType;
        $targetFile = $targetDir . $encryptedFileName;
        $uploadOk = 1;

        // Check if the file is an image
        $check = getimagesize($_FILES["file"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "<script>swal('Error!', 'File bukan gambar.', 'error');</script>";
            $uploadOk = 0;
        }

        // Restrict file type
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            echo "<script>swal('Error!', 'Hanya JPG, JPEG, PNG & GIF yang diperbolehkan.', 'error');</script>";
            $uploadOk = 0;
        }

        // Check if upload is allowed
        if ($uploadOk == 0) {
            echo "<script>swal('Error!', 'Maaf, file gagal di-upload.', 'error');</script>";
        } else {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFile)) {
                // Insert into database
                $stmt = $conn->prepare("INSERT INTO foto (JudulFoto, DeskripsiFoto, LokasiFile) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $JudulFoto, $deskripsiFoto, $targetFile);
                if ($stmt->execute()) {
                    echo "<script>swal('Success!', 'File " . htmlspecialchars($encryptedFileName) . " berhasil diupload.', 'success');</script>";
                } else {
                    echo "<script>swal('Error!', 'Error: " . $stmt->error . "', 'error');</script>";
                }
            } else {
                echo "<script>swal('Error!', 'Maaf, ada kesalahan saat mengunggah file Anda.', 'error');</script>";
            }
        }
        
        } 

        // Prepare DELETE statement
        $stmt = $conn->prepare("DELETE FROM foto WHERE FotoID = ?");
        $stmt->bind_param("i", $fotoID);

        if ($stmt->execute()) {
            echo "<script>swal('Success!', 'Foto berhasil dihapus.', 'success');</script>";
        } else {
            echo "<script>swal('Error!', 'Gagal menghapus foto: " . $stmt->error . "', 'error');</script>";
        }

    } elseif (isset($_POST['edit'])) {
        $fotoID = $_POST['FotoID'];
        $JudulFoto = $_POST['JudulFoto'];
        $deskripsiFoto = $_POST['deskripsiFoto'];

        // Prepare UPDATE statement
        $stmt = $conn->prepare("UPDATE foto SET JudulFoto = ?, DeskripsiFoto = ? WHERE FotoID = ?");
        $stmt->bind_param("ssi", $JudulFoto, $deskripsiFoto, $fotoID);

        if ($stmt->execute()) {
            echo "<script>swal('Success!', 'Foto berhasil diupdate.', 'success');</script>";
        } else {
            echo "<script>swal('Error!', 'Gagal mengupdate foto: " . $stmt->error . "', 'error');</script>";
        }
    }

    if (isset($_POST['edit'])) {
    $fotoID = $_POST['FotoID'];
    $judulFoto = $_POST['JudulFoto'];
    $deskripsiFoto = $_POST['DeskripsiFoto'];

    // Update data di database
    $query = "UPDATE foto SET JudulFoto = ?, DeskripsiFoto = ? WHERE FotoID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssi", $judulFoto, $deskripsiFoto, $fotoID);

    if ($stmt->execute()) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: 'Foto berhasil diupdate!'
                }).then(() => {
                    window.location.href = 'index.php';
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Terjadi kesalahan saat mengupdate data!'
                });
              </script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Upload Foto</title>
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/aos/aos.css" rel="stylesheet">
    <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
    <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">
    <link href="assets/css/main.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-color: #121212;
            color: #a9c3a3; 
        }
        .card {
            background-color: #1c1c1c;
            border: 1px solid #4b5d4a; 
            color: #a9c3a3;
        }
        .card-header {
            background-color: #4b5d4a; 
            color: #121212;
            font-weight: bold;
        }
        .card-body label {
            color: #a9c3a3;
        }
        .card-body input,
        .card-body textarea {
            background-color: #333333;
            border: 1px solid #4b5d4a; 
            color: #a9c3a3;
            width: 100%;
            padding: 8px;
            border-radius: 5px;
        }
        .card-body button {
            background-color: #4b5d4a; 
            color: #121212;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .card-body button:hover {
            background-color: #3d4b39; 
        }
        .table-wrapper {
            display: flex;
            justify-content: center;
            padding-top: 20px;
        }
        .photo-table-container {
            background-color: #1c1c1c;
            border: 1px solid #4b5d4a;
            border-radius: 10px;
            padding: 20px;
            max-width: 800px;
            width: 100%;
            color: #a9c3a3;
        }
        .table {
            color: #a9c3a3;
        }
        .table th, .table td {
            vertical-align: middle;
            text-align: center;
        }
        .table img {
            width: 100px;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body class="index-page">

<!-- Header -->
<header id="header" class="header d-flex align-items-center sticky-top">
    <div class="container-fluid position-relative d-flex align-items-center justify-content-between">
        <a href="gallery.php" class="logo d-flex align-items-center me-auto me-xl-0">
            <i class="bi bi-camera"></i>
            <h1 class="sitename">GalleryPhoto</h1>
        </a>
        <nav id="navmenu" class="navmenu">
            <ul>
                <li><a href="gallery.php">Kembali</a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
    </div>
</header>

<main class="main">
    <div class="container mt-4">
        <div class="card">
            <div class="card-header">Unggah Foto</div>
            <div class="card-body">
                <form action="upload.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="JudulFoto" class="form-label">Judul Foto</label>
                        <input type="text" class="form-control" id="JudulFoto" name="JudulFoto" required>
                    </div>
                    <div class="mb-3">
                        <label for="deskripsiFoto" class="form-label">Deskripsi Foto</label>
                        <textarea class="form-control" id="deskripsiFoto" name="deskripsiFoto" required></textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="LokasiFile" class="form-label">File Foto</label>
                        <input type="file" class="form-control" id="LokasiFile" name="file" required>
                    </div>
                    <button type="submit" name="Tambah" class="btn btn-primary w-100">Unggah Foto</button>
                
                </form>
            </div>
            
        </div>
    </div>

    <!-- Daftar Foto -->
    <div class="table-wrapper">
        <div class="photo-table-container">
            <h2 class="text-center">Daftar Foto</h2>
            <table class="table table-dark table-striped">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Foto</th>
                        <th scope="col">Judul Foto</th>
                        <th scope="col">Deskripsi Foto</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM foto");
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$i}</td>
                                <td><img src='{$row['LokasiFile']}' alt='Foto'></td>
                                <td>{$row['JudulFoto']}</td>
                                <td>{$row['DeskripsiFoto']}</td>
                                
                                <td>
                                    <form action='upload.php' method='POST' style='display:inline;'>
                                        <input type='hidden' name='fotoID' value='{$row['FotoID']}'>
                                        <button type='button' class='btn btn-warning btn-sm edit-button'>Edit</button>
                                    </form>
                                    <form action='upload.php' method='POST' style='display:inline;'>
                                        <input type='hidden' name='fotoID' value='{$row['FotoID']}'>
                                        <button type='submit' name='delete' class='btn btn-danger btn-sm' onclick='return confirm(\"Yakin ingin menghapus foto ini?\");'>Hapus</button>
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

    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Foto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm" action="upload.php" method="POST">
                        <input type="hidden" id="editFotoID" name="FotoID">
                        <div class="mb-3">
                            <label for="editJudulFoto" class="form-label">Judul Foto</label>
                            <input type="text" class="form-control" id="editJudulFoto" name="JudulFoto" required>
                        </div>
                        <div class="mb-3">
                            <label for="editDeskripsiFoto" class="form-label">Deskripsi Foto</label>
                            <textarea class="form-control" id="editDeskripsiFoto" name="DeskripsiFoto" required></textarea>
                        </div>
                        <button type="submit" name="edit" class="btn btn-primary">Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Footer -->
<?php include ("footer.php");?>

<!-- Vendor JS Files -->
<script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/vendor/aos/aos.js"></script>
<script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
<script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/main.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Edit functionality
        document.querySelectorAll('.edit-button').forEach(button => {
            button.addEventListener('click', function (event) {
                const row = this.closest('tr');
                const fotoID = row.querySelector('input[name="fotoID"]').value;
                const judulFoto = row.cells[1].innerText;
                const deskripsiFoto = row.cells[2].innerText;

                document.getElementById('editFotoID').value = fotoID;
                document.getElementById('editJudulFoto').value = judulFoto;
                document.getElementById('editDeskripsiFoto').value = deskripsiFoto;

                const editModal = new bootstrap.Modal(document.getElementById('editModal'));
                editModal.show();
            });
        });
    });
</script>

</body>
</html> 