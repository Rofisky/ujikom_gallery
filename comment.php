<?php
session_start();
include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Pastikan pengguna login
    if (!isset($_SESSION['UserID'])) {
        echo json_encode(['success' => false, 'message' => 'Anda harus login untuk mengomentari foto ini.']);
        exit;
    }

    $foto_id = isset($_POST['FotoID']) ? intval($_POST['FotoID']) : 0;
    $user_id = $_SESSION['UserID'];
    $isi_komentar = isset($_POST['IsiKomentar']) ? trim($_POST['IsiKomentar']) : '';

    // Validasi input
    if (empty($isi_komentar)) {
        echo json_encode(['success' => false, 'message' => 'Komentar tidak boleh kosong.']);
        exit;
    }

    if ($foto_id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID foto tidak valid.']);
        exit;
    }

    // Masukkan komentar ke database
    $insertComment = $conn->prepare("INSERT INTO komentarfoto (FotoID, UserID, IsiKomentar) VALUES (?, ?, ?)");
    if (!$insertComment) {
        echo json_encode(['success' => false, 'message' => 'Gagal mempersiapkan statement.']);
        exit;
    }

    $insertComment->bind_param("iis", $foto_id, $user_id, $isi_komentar);
    if ($insertComment->execute()) {
        // Ambil jumlah komentar terbaru
        $commentCountQuery = $conn->prepare("SELECT COUNT(*) AS commentCount FROM komentarfoto WHERE FotoID = ?");
        $commentCountQuery->bind_param("i", $foto_id);
        $commentCountQuery->execute();
        $commentCountResult = $commentCountQuery->get_result();
        $commentCountData = $commentCountResult->fetch_assoc();

        echo json_encode([
            'success' => true,
            'message' => 'Komentar berhasil ditambahkan.',
            'commentCount' => $commentCountData['commentCount']
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal menambahkan komentar.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Metode request tidak valid.']);
}
?>
