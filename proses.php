<?php
 
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Method tidak diizinkan'
    ]);
    exit;
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

if (empty($data)) {
    echo json_encode([
        'success' => false,
        'message' => 'Data tidak valid'
    ]);
    exit;
}

$nama = isset($data['nama']) ? cleanInput($data['nama']) : '';
$email = isset($data['email']) ? cleanInput($data['email']) : '';
$telepon = isset($data['telepon']) ? cleanInput($data['telepon']) : '';
$minat = isset($data['minat']) ? cleanInput($data['minat']) : '';
$pesan = isset($data['pesan']) ? cleanInput($data['pesan']) : '';

$errors = [];

if (empty($nama)) {
    $errors[] = 'Nama harus diisi';
} elseif (strlen($nama) < 3) {
    $errors[] = 'Nama minimal 3 karakter';
}

if (empty($email)) {
    $errors[] = 'Email harus diisi';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Format email tidak valid';
}

if (empty($telepon)) {
    $errors[] = 'Nomor telepon harus diisi';
} else {
    $cleanPhone = preg_replace('/[^0-9]/', '', $telepon);
    if (strlen($cleanPhone) < 10 || strlen($cleanPhone) > 13) {
        $errors[] = 'Nomor telepon harus 10-13 digit';
    }
}

if (empty($minat)) {
    $errors[] = 'Minat program harus dipilih';
}


if (!empty($errors)) {
    echo json_encode([
        'success' => false,
        'message' => implode(', ', $errors)
    ]);
    exit;
}

$saved = saveToFile($nama, $email, $telepon, $minat, $pesan);

if ($saved) {
    
    echo json_encode([
        'success' => true,
        'message' => 'Pesan berhasil dikirim! Kami akan menghubungi Anda segera.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menyimpan data. Silakan coba lagi.'
    ]);
}

function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function saveToFile($nama, $email, $telepon, $minat, $pesan) {
    try {
    
        $filename = 'data_kontak.csv';
        
        
        $fileExists = file_exists($filename);
        
        
        $file = fopen($filename, 'a');
        
        if ($file === false) {
            return false;
        }
        
        
        if (!$fileExists) {
            fputcsv($file, ['Tanggal', 'Nama', 'Email', 'Telepon', 'Minat Program', 'Pesan']);
        }
        
        
        $tanggal = date('Y-m-d H:i:s');
        fputcsv($file, [$tanggal, $nama, $email, $telepon, $minat, $pesan]);
        
        
        fclose($file);
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}


function sendEmailNotification($nama, $email, $telepon, $minat, $pesan) {
    
    $to = "admin@teknik-komputer.ac.id";
    $subject = "Pesan Baru dari Website - " . $nama;
    

    $message = "
    <html>
    <head>
        <title>Pesan Kontak Baru</title>
        <style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; }
            td { padding: 10px; border: 1px solid #ddd; }
            .label { font-weight: bold; background: #f0f0f0; width: 150px; }
        </style>
    </head>
    <body>
        <h2>Pesan Kontak Baru dari Website</h2>
        <table>
            <tr>
                <td class='label'>Nama</td>
                <td>$nama</td>
            </tr>
            <tr>
                <td class='label'>Email</td>
                <td>$email</td>
            </tr>
            <tr>
                <td class='label'>Telepon</td>
                <td>$telepon</td>
            </tr>
            <tr>
                <td class='label'>Minat Program</td>
                <td>" . ucfirst($minat) . "</td>
            </tr>
            <tr>
                <td class='label'>Pesan</td>
                <td>$pesan</td>
            </tr>
            <tr>
                <td class='label'>Waktu</td>
                <td>" . date('d-m-Y H:i:s') . "</td>
            </tr>
        </table>
    </body>
    </html>
    ";
    
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@teknik-komputer.ac.id" . "\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    
    
    return @mail($to, $subject, $message, $headers);
}
?>