
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config/database.php";

try {

    // Ambil semua produk dari database
    $query = "
        SELECT
            ID_Produk,
            Nama_Produk,
            Kategori,
            Harga,
            Stok,
            Foto_Produk,
            Deskripsi
        FROM produk
        ORDER BY ID_Produk DESC
    ";

    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Gagal mengambil data produk.");
    }

    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    echo json_encode([
        "success" => true,
        "data" => $products
    ]);

} catch (Throwable $e) {

    http_response_code(500);

    echo json_encode([
        "success" => false,
        "message" => "Terjadi kesalahan saat mengambil produk."
    ]);

}
