<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include 'koneksi.php';

// Mendapatkan koneksi ke database
$koneksi = koneksi_database();

// Mendapatkan metode HTTP yang dikirimkan
$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Logika untuk mengambil data pengajuan tabungan berdasarkan id_siswa
        $idSiswa = $_GET['id_siswa']; // Misalnya dari parameter query
        $query = "SELECT * FROM pengajuan_pengambilan_tabungan WHERE id_siswa = ?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $idSiswa);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $pengajuan_tabungan = array();
            while ($row = $result->fetch_assoc()) {
                $pengajuan_tabungan[] = $row;
            }
            http_response_code(200);
            echo json_encode($pengajuan_tabungan);
        } else {
            http_response_code(404);
            echo json_encode(array("message" => "Data pengajuan tabungan tidak ditemukan untuk id siswa tersebut"));
        }
        break;

    case 'POST':
        // Endpoint untuk membuat pengajuan tabungan baru
        $data = json_decode(file_get_contents("php://input"));

        $tgl = $data->tgl;
        $nominal = $data->nominal;
        $id_siswa = $data->id_siswa;

        $query = "INSERT INTO pengajuan_pengambilan_tabungan (tgl, nominal, id_siswa) VALUES (?, ?, ?)";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("sii", $tgl, $nominal, $id_siswa);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(array("message" => "Pengajuan tabungan berhasil ditambahkan"));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Gagal menambahkan pengajuan tabungan"));
        }
        break;

    case 'PUT':
        // Endpoint untuk memperbarui pengajuan tabungan
        $data = json_decode(file_get_contents("php://input"));

        $id_pengambilan = $data->id_pengambilan;
        $tgl = $data->tgl;
        $nominal = $data->nominal;
        $id_siswa = $data->id_siswa;

        $query = "UPDATE pengajuan_pengambilan_tabungan SET tgl=?, nominal=?, id_siswa=? WHERE id_pengambilan=?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("siii", $tgl, $nominal, $id_siswa, $id_pengambilan);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(array("message" => "Pengajuan tabungan berhasil diperbarui"));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Gagal memperbarui pengajuan tabungan"));
        }
        break;

    case 'DELETE':
        // Endpoint untuk menghapus pengajuan tabungan
        $data = json_decode(file_get_contents("php://input"));

        $id_pengambilan = $data->id_pengambilan;

        $query = "DELETE FROM pengajuan_pengambilan_tabungan WHERE id_pengambilan=?";
        $stmt = $koneksi->prepare($query);
        $stmt->bind_param("i", $id_pengambilan);

        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode(array("message" => "Pengajuan tabungan berhasil dihapus"));
        } else {
            http_response_code(503);
            echo json_encode(array("message" => "Gagal menghapus pengajuan tabungan"));
        }
        break;

    default:
        // Metode HTTP tidak didukung
        http_response_code(405);
        echo json_encode(array("message" => "Metode HTTP tidak didukung"));
        break;
}

// Menutup koneksi database
mysqli_close($koneksi);
?>
