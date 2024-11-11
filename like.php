<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $foto_id = intval($_POST['FotoID']);
    $user_id = intval($_POST['UserID']);

    // Pastikan user sudah login
    if (!$user_id) {
        echo json_encode(['success' => false, 'message' => 'Anda harus login untuk menyukai foto ini.']);
        exit();
    }

    // Cek apakah user sudah menyukai foto ini
    $checkLike = $conn->prepare("SELECT * FROM likefoto WHERE FotoID = ? AND UserID = ?");
    $checkLike->bind_param("ii", $foto_id, $user_id);
    $checkLike->execute();
    $result = $checkLike->get_result();

    if ($result->num_rows > 0) {
        // Jika sudah di-like, lakukan unlike
        $deleteLike = $conn->prepare("DELETE FROM likefoto WHERE FotoID = ? AND UserID = ?");
        $deleteLike->bind_param("ii", $foto_id, $user_id);
        $deleteLike->execute();
        $action = 'unliked';
    } else {
        // Jika belum di-like, tambahkan like
        $insertLike = $conn->prepare("INSERT INTO likefoto (FotoID, UserID) VALUES (?, ?)");
        $insertLike->bind_param("ii", $foto_id, $user_id);
        $insertLike->execute();
        $action = 'liked';
    }

    // Hitung jumlah like terbaru
    $likeCountQuery = $conn->prepare("SELECT COUNT(*) AS likeCount FROM likefoto WHERE FotoID = ?");
    $likeCountQuery->bind_param("i", $foto_id);
    $likeCountQuery->execute();
    $likeCountResult = $likeCountQuery->get_result();
    $likeCountData = $likeCountResult->fetch_assoc();

    // Kirim respons ke JavaScript
    echo json_encode([
        'success' => true,
        'likeCount' => $likeCountData['likeCount'],
        'action' => $action
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>
