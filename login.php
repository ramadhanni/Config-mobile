<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'koneksi.php';

// Mendapatkan koneksi ke database
$koneksi = koneksi_database();

$response = array();

// $password = $_POST['password'];
// $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Cek apakah data username dan password dikirimkan melalui metode POST
if (isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    $query = "SELECT password FROM profile_siswa WHERE nama_siswa = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashedPasswordFromDB = $row['password'];
    
        // Gunakan password_verify untuk memverifikasi
        if (password_verify($password, $hashedPasswordFromDB)) {
            $response['message'] = 'success';
        } else {
            $response['message'] = 'error';
        }
    } else {
        $response['message'] = 'error';
    }
} else {
    $response['message'] = 'error'; // Jika username atau password tidak dikirim
}

// Menutup koneksi database
mysqli_close($koneksi);

// Mengembalikan respon dalam format JSON
echo json_encode($response);
