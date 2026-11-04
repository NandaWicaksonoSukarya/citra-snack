
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config/database.php";

function response($success, $message, $data = null, $code = 200)
{
    http_response_code($code);
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    response(false, "Metode tidak diizinkan.", null, 405);
}

$input = json_decode(file_get_contents("php://input"), true);

$orderId = filter_var(
    $input["orderId"] ?? null,
    FILTER_VALIDATE_INT
);

$whatsapp = trim($input["whatsapp"] ?? "");

if (!$orderId || $whatsapp === "") {
    response(false, "Nomor pesanan dan WhatsApp wajib diisi.", null, 400);
}

try {
    // Ambil pesanan dengan verifikasi nomor WhatsApp
    $stmt = $conn->prepare("
        SELECT
            p.ID_Pesanan,
            p.Tanggal_Pesan,
            p.Tanggal_Dibutuhkan,
            p.Waktu_Dibutuhkan,
            p.Status_Pesanan,
            p.Total_Harga,
            p.Catatan,
            pl.Nama_Lengkap,
            pl.No_Kontak,
            pl.Alamat,
            pb.Metode_Bayar,
            pb.Status_Bayar,
            pb.Jumlah_Bayar,
            pb.Bukti_Pembayaran,
            pb.Tanggal_Pembayaran
        FROM pesanan p
        JOIN pelanggan pl
            ON pl.ID_Pelanggan = p.ID_Pelanggan
        LEFT JOIN pembayaran pb
            ON pb.ID_Pembayaran = (
                SELECT MAX(pb2.ID_Pembayaran)
                FROM pembayaran pb2
                WHERE pb2.ID_Pesanan = p.ID_Pesanan
            )
        WHERE p.ID_Pesanan = ?
          AND pl.No_Kontak = ?
        LIMIT 1
    ");

    $stmt->bind_param("is", $orderId, $whatsapp);
    $stmt->execute();

    $order = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$order) {
        response(
            false,
            "Pesanan tidak ditemukan. Periksa nomor pesanan dan WhatsApp.",
            null,
            404
        );
    }

    // Ambil detail produk pesanan
    $stmt = $conn->prepare("
        SELECT
            d.Jenis_Item,
            d.ID_Item,
            d.Jumlah,
            d.Harga_Satuan,
            d.Subtotal,
            COALESCE(
                pr.Nama_Produk,
                pk.Nama_Paket,
                CONCAT('Item #', d.ID_Item)
            ) AS Nama_Item
        FROM detail_pesanan d
        LEFT JOIN produk pr
            ON pr.ID_Produk = d.ID_Produk
        LEFT JOIN paket pk
            ON pk.ID_Paket = d.ID_Paket
        WHERE d.ID_Pesanan = ?
        ORDER BY d.ID_Detail_Pesanan ASC
    ");

    $stmt->bind_param("i", $orderId);
    $stmt->execute();

    $items = [];
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }

    $stmt->close();

    $order["items"] = $items;

    response(
        true,
        "Data pesanan berhasil diambil.",
        $order
    );

} catch (Throwable $e) {
    error_log("Order status error: " . $e->getMessage());

    response(
        false,
        "Terjadi kesalahan saat mengambil data pesanan.",
        null,
        500
    );
}
