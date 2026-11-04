
<?php
session_start();
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config/database.php";

function response($success, $message, $data = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);
    exit;
}

if (!isset($_SESSION["ID_Admin"])) {
    response(false, "Silakan login sebagai admin.", null, 401);
}

$method = $_SERVER["REQUEST_METHOD"];

// =====================================
// GET: DAFTAR / DETAIL PESANAN
// =====================================

if ($method === "GET") {
    $id = (int)($_GET["id"] ?? 0);
    $status = trim($_GET["status"] ?? "");

    if ($id > 0) {
        $stmt = $conn->prepare("
            SELECT
                p.ID_Pesanan,
                p.Tanggal_Pesan,
                p.Tanggal_Dibutuhkan,
                p.Waktu_Dibutuhkan,
                p.Status_Pesanan,
                p.Metode_Ambil,
                p.Catatan,
                p.Total_Harga,
                pl.Nama_Lengkap,
                pl.No_Kontak,
                pl.Alamat,
                pb.Metode_Bayar,
                pb.Status_Bayar,
                pb.Jumlah_Bayar
            FROM pesanan p
            JOIN pelanggan pl ON pl.ID_Pelanggan = p.ID_Pelanggan
            LEFT JOIN pembayaran pb ON pb.ID_Pembayaran = (
                SELECT MAX(pb2.ID_Pembayaran)
                FROM pembayaran pb2
                WHERE pb2.ID_Pesanan = p.ID_Pesanan
            )
            WHERE p.ID_Pesanan = ?
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();
        $order = $stmt->get_result()->fetch_assoc();

        if (!$order) {
            response(false, "Pesanan tidak ditemukan.", null, 404);
        }

        $stmt = $conn->prepare("
            SELECT
                d.ID_Detail_Pesanan,
                d.Jenis_Item,
                d.ID_Item,
                d.Jumlah,
                d.Harga_Satuan,
                d.Subtotal,
                d.ID_Produk,
                d.ID_Paket,
                COALESCE(pr.Nama_Produk, pk.Nama_Paket, CONCAT('Item #', d.ID_Item)) AS Nama_Item
            FROM detail_pesanan d
            LEFT JOIN produk pr ON pr.ID_Produk = d.ID_Produk
            LEFT JOIN paket pk ON pk.ID_Paket = d.ID_Paket
            WHERE d.ID_Pesanan = ?
            ORDER BY d.ID_Detail_Pesanan ASC
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $items = [];
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $items[] = $row;
        }

        $order["items"] = $items;

        response(true, "Detail pesanan berhasil diambil.", $order);
    }

    $sql = "
        SELECT
            p.ID_Pesanan,
            p.Tanggal_Pesan,
            p.Status_Pesanan,
            p.Total_Harga,
            pl.Nama_Lengkap,
            pb.Metode_Bayar,
            pb.Status_Bayar
        FROM pesanan p
        JOIN pelanggan pl ON pl.ID_Pelanggan = p.ID_Pelanggan
        LEFT JOIN pembayaran pb ON pb.ID_Pembayaran = (
            SELECT MAX(pb2.ID_Pembayaran)
            FROM pembayaran pb2
            WHERE pb2.ID_Pesanan = p.ID_Pesanan
        )
    ";

    if ($status !== "" && $status !== "semua") {
        $stmt = $conn->prepare(
            $sql . " WHERE p.Status_Pesanan = ? ORDER BY p.ID_Pesanan DESC"
        );
        $stmt->bind_param("s", $status);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql . " ORDER BY p.ID_Pesanan DESC");
    }

    if (!$result) {
        response(false, "Gagal mengambil daftar pesanan: " . $conn->error, null, 500);
    }

    $orders = [];

    while ($row = $result->fetch_assoc()) {
        $orders[] = $row;
    }

    response(true, "Daftar pesanan berhasil diambil.", $orders);
}

// =====================================
// POST: UPDATE STATUS PESANAN
// =====================================

if ($method === "POST") {
    $id = (int)($_POST["id"] ?? 0);
    $newStatus = trim($_POST["status"] ?? "");
    $adminId = (int)$_SESSION["ID_Admin"];

    $allowedStatuses = [
        "Pesanan Diterima",
        "Sedang Diproses",
        "Dalam Pengantaran",
        "Selesai",
        "Dibatalkan",
        "Menunggu",
        "Diproses"
    ];

    if ($id <= 0 || !in_array($newStatus, $allowedStatuses, true)) {
        response(false, "Data status atau ID pesanan tidak valid.", null, 400);
    }

    $conn->begin_transaction();

    try {
        $stmt = $conn->prepare("
            SELECT Status_Pesanan
            FROM pesanan
            WHERE ID_Pesanan = ?
            FOR UPDATE
        ");

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $order = $stmt->get_result()->fetch_assoc();

        if (!$order) {
            throw new Exception("Pesanan tidak ditemukan.");
        }

        $oldStatus = $order["Status_Pesanan"];

        $stmt = $conn->prepare("
            UPDATE pesanan
            SET Status_Pesanan = ?
            WHERE ID_Pesanan = ?
        ");

        $stmt->bind_param("si", $newStatus, $id);

        if (!$stmt->execute()) {
            throw new Exception("Gagal memperbarui status pesanan.");
        }

        if ($oldStatus !== $newStatus) {
            $stmt = $conn->prepare("
                INSERT INTO riwayat_status
                (Status_Sebelumnya, Status_Baru, ID_Pesanan, ID_Admin)
                VALUES (?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "ssii",
                $oldStatus,
                $newStatus,
                $id,
                $adminId
            );

            if (!$stmt->execute()) {
                throw new Exception("Gagal menyimpan riwayat status.");
            }
        }

        $conn->commit();

        response(true, "Status pesanan berhasil diperbarui.");

    } catch (Throwable $e) {
        $conn->rollback();
        response(false, $e->getMessage(), null, 500);
    }
}

response(false, "Metode tidak diizinkan.", null, 405);
