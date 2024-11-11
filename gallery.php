<?php
session_start();
include 'koneksi.php';
 
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$result = $conn->query("SELECT * FROM foto"); 
if (!$result) {
    die("Query failed: " . $conn->error);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Pastikan pengguna login
  if (!isset($_SESSION['UserID'])) {
      echo json_encode(['success' => false, 'message' => 'Anda harus login untuk menyukai foto ini.']);
      exit;
  }
  $foto_id = $_POST['FotoID'];
  $user_id = $_SESSION['UserID'];
  
  // Check if user has already liked this photo
  $checkLike = $conn->prepare("SELECT * FROM likefoto WHERE FotoID = ? AND UserID = ?");
  $checkLike->bind_param("ii", $foto_id, $user_id);
  $checkLike->execute();
  $result = $checkLike->get_result();

  if ($result->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Anda sudah menyukai foto ini.']);
} else {
    // Tambahkan like baru
    $insertLike = $conn->prepare("INSERT INTO likefoto (FotoID, UserID) VALUES (?, ?)");
    $insertLike->bind_param("ii", $foto_id, $user_id);
    $insertLike->execute();

}
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>Menu Utama</title>
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
                <li><a href="upload.php">Unggah</a></li>
                <li><a href="gallery.php">galeri</a></li>
                <li><a href="index.php" class="bi bi-box-arrow-right me-1">Keluar<br></a></li>
            </ul>
            <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
        </nav>
    </div>
</header>

<main class="main">
    <div class="page-title">
        <div class="heading">
            <div class="container">
                <div class="row d-flex justify-content-center text-center">
                    <div class="col-lg-8">
                        <h1>Halaman Utama</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>

   
    <section id="gallery" class="gallery section">
    <div class="container-fluid">
        <div class="row gy-4 justify-content-center">
        <?php
// Ambil semua foto dari database
$result = $conn->query("SELECT * FROM foto"); 
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $JudulFoto = htmlspecialchars($row['JudulFoto']);
        $file_path = htmlspecialchars($row['LokasiFile']); 
        $DeskripsiFoto = htmlspecialchars($row['DeskripsiFoto']);
        $foto_id = (int)$row['FotoID']; 

        // Hitung jumlah like
        $likeCountQuery = $conn->prepare("SELECT COUNT(*) AS likeCount FROM likefoto WHERE FotoID = ?");
        $likeCountQuery->bind_param("i", $foto_id);
        $likeCountQuery->execute();
        $likeCountResult = $likeCountQuery->get_result();
        $likeData = $likeCountResult->fetch_assoc();
        $likeCount = $likeData['likeCount'] ?? 0;

        // Hitung jumlah komentar
        $commentCountQuery = $conn->prepare("SELECT COUNT(*) AS commentCount FROM komentarfoto WHERE FotoID = ?");
        $commentCountQuery->bind_param("i", $foto_id);
        $commentCountQuery->execute();
        $commentCountResult = $commentCountQuery->get_result();
        $commentData = $commentCountResult->fetch_assoc();
        $commentCount = $commentData['commentCount'] ?? 0;
?>    

        <!-- Card Foto -->
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card shadow-sm">
                <div class="card-img-top overflow-hidden" style="height: 250px;">
                    <img src="<?php echo $file_path; ?>" class="img-fluid w-100 h-100 object-fit-cover" alt="<?php echo $JudulFoto; ?>">
                </div>
                <div class="card-body text-center">
                    <h5 class="card-title"><?php echo $JudulFoto; ?></h5>
                    <p class="card-text"><?php echo $DeskripsiFoto; ?></p>
                    <div class="gallery-interactions d-flex justify-content-between">
                        <span class="like" onclick="likePhoto(<?php echo $foto_id; ?>)" data-foto-id="<?php echo $foto_id; ?>">
                            <i class="bi bi-heart-fill"></i>
                            <span class="like-count" data-foto-id="<?php echo $foto_id; ?>"><?php echo $likeCount; ?></span>
                        </span>
                        <span class="comment" onclick="commentPhoto(<?php echo $foto_id; ?>)">
                            <i class="bi bi-chat-fill"></i>
                            <span class="comment-count" data-foto-id="<?php echo $foto_id; ?>"><?php echo $commentCount; ?></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

<?php
    }
} else {
    echo '<p>Tidak ada foto untuk ditampilkan.</p>';
}
?>
</section>

<script>
function likePhoto(fotoID) {
    // Pastikan UserID tersedia dari session PHP
    const userID = <?php echo json_encode($_SESSION['UserID']); ?>;
    if (!userID) {
        alert("Anda harus login untuk menyukai foto ini.");
        return;
    }

    // Kirim request menggunakan fetch
    fetch("like.php", {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: `FotoID=${fotoID}&UserID=${userID}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Perbarui jumlah like di halaman tanpa reload
            const likeCountElement = document.querySelector(`.like-count[data-foto-id="${fotoID}"]`);
            likeCountElement.textContent = data.likeCount;

            // Ubah ikon heart berdasarkan status like/unlike
            const likeIcon = document.querySelector(`.like[data-foto-id="${fotoID}"] i`);
            if (data.action === 'liked') {
                likeIcon.classList.add('bi-heart-fill');
                likeIcon.classList.remove('bi-heart');
            } else {
                likeIcon.classList.add('bi-heart');
                likeIcon.classList.remove('bi-heart-fill');
            }
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat menyukai foto.");
    });
}


function commentPhoto(fotoID) {
    
    const userID = <?php echo json_encode($_SESSION['UserID']); ?>;
    const commentText = prompt("Masukkan komentar Anda:");
    if (!userID || !commentText) {
        alert("Anda harus login dan mengisi komentar.");
        return;
    }

    fetch("comment.php", {
        method: "POST",
        body: new URLSearchParams({ FotoID: fotoID, UserID: userID, IsiKomentar: commentText })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`.comment-count[data-foto-id="${fotoID}"]`).textContent = data.commentCount;
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error("Error:", error);
        alert("Terjadi kesalahan saat menambahkan komentar.");
    });
}

</script>

  

</body>
</html>  
