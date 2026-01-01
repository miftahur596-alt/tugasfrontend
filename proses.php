<?php

header('Content-Type: application/json');


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method tidak diizinkan']);
    exit;
}


$input = json_decode(file_get_contents('php://input'), true);


if (!isset($input['nama']) || !isset($input['email']) || !isset($input['telepon']) || !isset($input['pesan'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
    exit;
}


$nama = htmlspecialchars(trim($input['nama']));
$email = htmlspecialchars(trim($input['email']));
$telepon = htmlspecialchars(trim($input['telepon']));
$pesan = htmlspecialchars(trim($input['pesan']));


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Format email tidak valid']);
    exit;
}


if (!preg_match('/^(\+62|0)[0-9]{8,12}$/', $telepon)) {
    echo json_encode(['success' => false, 'message' => 'Nomor telepon tidak valid']);
    exit;
}


if (strlen($pesan) < 10) {
    echo json_encode(['success' => false, 'message' => 'Pesan minimal 10 karakter']);
    exit;
}


$logData = date('Y-m-d H:i:s') . " | Nama: " . $nama . " | Email: " . $email . " | Telepon: " . $telepon . " | Pesan: " . $pesan . "\n";
$logFile = 'pesan_log.txt';

if (file_put_contents($logFile, $logData, FILE_APPEND)) {
    
    $to = 'admin@example.com';
    $subject = 'Pesan Baru dari ' . $nama;
    $message = "Nama: " . $nama . "\n";
    $message .= "Email: " . $email . "\n";
    $message .= "Telepon: " . $telepon . "\n";
    $message .= "Pesan: " . $pesan;
    
    

    echo json_encode(['success' => true, 'message' => 'Pesan berhasil dikirim']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Gagal menyimpan pesan']);
}
?>
