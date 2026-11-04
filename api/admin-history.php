
<?php
session_start();

header("Content-Type: application/json; charset=utf-8");

if (!isset($_SESSION["ID_Admin"])) {
    http_response_code(401);
    echo json_encode([
        "success" => false,
        "message" => "Sesi admin tidak valid."
    ]);
    exit;
}

require_once __DIR__ . "/../config/database.php";

function response($success, $message, $data = null) {
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$action = $_GET["action"] ?? "list";

try {

    // =====================================
    // DAFTAR RIWAYAT PESANAN
    // =====================================

    if ($action === "list") {

        $sql = "
            SELECT
                ps.ID_Pesanan,
                ps.Tanggal_Pesan,
                ps.Tanggal_Dibutuhkan,
                ps.Status_Pesanan,
                ps.Total_Harga,
                pl.Nama_Lengkap,
                pb.Status_Bayar

            FROM pesanan ps

            INNER JOIN pelanggan pl
                ON ps.ID_Pelanggan = pl.ID_Pelanggan

            LEFT JOIN pembayaran pb
                ON pb.ID_Pembayaran = (
                    SELECT MAX(pb2.ID_Pembayaran)
                    FROM pembayaran pb2
                    WHERE pb2.ID_Pesanan = ps.ID_Pesanan
                )

            WHERE ps.Status_Pesanan IN ('Selesai', 'Dibatalkan')

            ORDER BY ps.Tanggal_Pesan DESC
        ";

        $result = $conn->query($sql);

        if (!$result) {
            throw new Exception($conn->error);
        }

        $orders = [];

        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        response(true, "Riwayat pesanan berhasil diambil.", $orders);
    }


    // =====================================
    // DETAIL RIWAYAT PESANAN
    // =====================================

    if ($action === "detail") {

        $id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

        if (!$id) {
            response(false, "ID pesanan tidak valid.");
        }

        $sql = "
            SELECT
                ps.ID_Pesanan,
                ps.Tanggal_Pesan,
                ps.Tanggal_Dibutuhkan,
                ps.Waktu_Dibutuhkan,
                ps.Status_Pesanan,
                ps.Metode_Ambil,
                ps.Catatan,
                ps.Total_Harga,

                pl.Nama_Lengkap,
                pl.No_Kontak,
                pl.Alamat,

                pb.Status_Bayar,
                pb.Metode_Bayar,
                pb.Jumlah_Bayar

            FROM pesanan ps

            INNER JOIN pelanggan pl
                ON ps.ID_Pelanggan = pl.ID_Pelanggan

            LEFT JOIN pembayaran pb
                ON pb.ID_Pembayaran = (
                    SELECT MAX(pb2.ID_Pembayaran)
                    FROM pembayaran pb2
                    WHERE pb2.ID_Pesanan = ps.ID_Pesanan
                )

            WHERE ps.ID_Pesanan = ?
            AND ps.Status_Pesanan IN ('Selesai', 'Dibatalkan')

            LIMIT 1
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        if (!$order) {
            response(false, "Riwayat pesanan tidak ditemukan.");
        }

        // =====================================
        // DETAIL PRODUK / PAKET
        // =====================================

        $itemSql = "
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
                ON d.ID_Produk = pr.ID_Produk

            LEFT JOIN paket pk
                ON d.ID_Paket = pk.ID_Paket

            WHERE d.ID_Pesanan = ?
        ";

        $itemStmt = $conn->prepare($itemSql);
        $itemStmt->bind_param("i", $id);
        $itemStmt->execute();

        $itemResult = $itemStmt->get_result();

        $items = [];

        while ($item = $itemResult->fetch_assoc()) {
            $items[] = $item;
        }

        $order["items"] = $items;

        response(true, "Detail pesanan berhasil diambil.", $order);
    }

    response(false, "Aksi tidak dikenali.");

} catch (Throwable $e) {

    http_response_code(500);

    response(
        false,
        "Terjadi kesalahan saat mengambil data riwayat."
    );
}
