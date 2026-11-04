
<?php
header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config/database.php";

function response($success, $message, $data = [], $code = 200)
{
    http_response_code($code);

    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);

    exit;
}

// Hanya menerima POST
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    response(false, "Metode request tidak diizinkan.", [], 405);
}

// Ambil data JSON
$input = json_decode(file_get_contents("php://input"), true);

if (!is_array($input)) {
    response(false, "Data request tidak valid.", [], 400);
}

$checkout = $input["checkoutData"] ?? [];
$cart = $input["cart"] ?? [];
$paymentMethod = $input["paymentMethod"] ?? "";

// Validasi data pelanggan
$nama = trim($checkout["nama"] ?? "");
$whatsapp = trim($checkout["whatsapp"] ?? "");
$tanggalDibutuhkan = $checkout["tanggalDibutuhkan"] ?? "";
$waktuDibutuhkan = $checkout["waktuDibutuhkan"] ?? "";
$alamat = trim($checkout["alamat"] ?? "");
$catatan = trim($checkout["catatan"] ?? "");

if (
    $nama === "" ||
    $whatsapp === "" ||
    $tanggalDibutuhkan === "" ||
    $waktuDibutuhkan === "" ||
    $alamat === ""
) {
    response(false, "Data pelanggan belum lengkap.", [], 400);
}

if (
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggalDibutuhkan) ||
    !preg_match('/^\d{2}:\d{2}$/', $waktuDibutuhkan)
) {
    response(false, "Format tanggal atau waktu tidak valid.", [], 400);
}

$tanggalParts = explode("-", $tanggalDibutuhkan);

if (!checkdate(
    (int)$tanggalParts[1],
    (int)$tanggalParts[2],
    (int)$tanggalParts[0]
)) {
    response(false, "Tanggal dibutuhkan tidak valid.", [], 400);
}

if (!in_array($paymentMethod, ["QRIS", "COD"], true)) {
    response(false, "Metode pembayaran tidak valid.", [], 400);
}

if (!is_array($cart) || count($cart) === 0) {
    response(false, "Keranjang masih kosong.", [], 400);
}

// Batasi jumlah item agar request tidak berlebihan
if (count($cart) > 100) {
    response(false, "Jumlah item pesanan terlalu banyak.", [], 400);
}

try {
    $conn->begin_transaction();

    // ==========================================
    // 1. VALIDASI PRODUK DAN HITUNG TOTAL
    // ==========================================

    $validatedItems = [];
    $totalHarga = 0;

    $stmtProduct = $conn->prepare("
        SELECT ID_Produk, Nama_Produk, Harga, Stok
        FROM produk
        WHERE ID_Produk = ?
        FOR UPDATE
    ");

    foreach ($cart as $item) {
        $productId = filter_var(
            $item["id"] ?? null,
            FILTER_VALIDATE_INT
        );

        $quantity = filter_var(
            $item["quantity"] ?? null,
            FILTER_VALIDATE_INT
        );

        if (!$productId || !$quantity || $quantity < 1) {
            throw new Exception("Data produk atau jumlah pesanan tidak valid.");
        }

        $stmtProduct->bind_param("i", $productId);
        $stmtProduct->execute();

        $result = $stmtProduct->get_result();
        $product = $result->fetch_assoc();

        if (!$product) {
            throw new Exception("Salah satu produk tidak ditemukan.");
        }

        $harga = (float)$product["Harga"];
        $stok = (int)$product["Stok"];

        if ($quantity > $stok) {
            throw new Exception(
                "Stok produk " . $product["Nama_Produk"] .
                " tidak mencukupi. Stok tersedia: " . $stok
            );
        }

        $subtotal = $harga * $quantity;
        $totalHarga += $subtotal;

        $validatedItems[] = [
            "id" => (int)$product["ID_Produk"],
            "name" => $product["Nama_Produk"],
            "price" => $harga,
            "quantity" => $quantity,
            "subtotal" => $subtotal
        ];
    }

    $stmtProduct->close();

    // ==========================================
    // 2. SIMPAN DATA PELANGGAN
    // ==========================================

    $stmtCustomer = $conn->prepare("
        INSERT INTO pelanggan
        (Nama_Lengkap, No_Kontak, Alamat)
        VALUES (?, ?, ?)
    ");

    $stmtCustomer->bind_param(
        "sss",
        $nama,
        $whatsapp,
        $alamat
    );

    $stmtCustomer->execute();

    $customerId = $conn->insert_id;

    $stmtCustomer->close();

    // ==========================================
    // 3. SIMPAN DATA PESANAN
    // ==========================================

    // Metode_Ambil diasumsikan menggunakan nilai "Diantar".
    // Sesuaikan jika ENUM di database menggunakan nilai berbeda.

    $metodeAmbil = "Diantar";

    $stmtOrder = $conn->prepare("
        INSERT INTO pesanan
        (
            Tanggal_Dibutuhkan,
            Metode_Ambil,
            Waktu_Dibutuhkan,
            Catatan,
            Total_Harga,
            ID_Pelanggan
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmtOrder->bind_param(
        "ssssdi",
        $tanggalDibutuhkan,
        $metodeAmbil,
        $waktuDibutuhkan,
        $catatan,
        $totalHarga,
        $customerId
    );

    $stmtOrder->execute();

    $orderId = $conn->insert_id;

    $stmtOrder->close();

    // ==========================================
    // 4. SIMPAN DETAIL PESANAN
    // ==========================================

    $stmtDetail = $conn->prepare("
        INSERT INTO detail_pesanan
        (
            Jenis_Item,
            ID_Item,
            Jumlah,
            Harga_Satuan,
            Subtotal,
            ID_Pesanan,
            ID_Produk,
            ID_Paket
        )
        VALUES ('Produk', ?, ?, ?, ?, ?, ?, NULL)
    ");

    foreach ($validatedItems as $item) {
        $productId = $item["id"];
        $quantity = $item["quantity"];
        $harga = $item["price"];
        $subtotal = $item["subtotal"];
        $detailOrderId = $orderId;

        $stmtDetail->bind_param(
            "iiddii",
            $productId,
            $quantity,
            $harga,
            $subtotal,
            $detailOrderId,
            $productId
        );

        $stmtDetail->execute();
    }

    $stmtDetail->close();

    // ==========================================
// 4A. KURANGI STOK PRODUK
// ==========================================

$stmtStock = $conn->prepare("
    UPDATE produk
    SET Stok = Stok - ?
    WHERE ID_Produk = ?
    AND Stok >= ?
");

foreach ($validatedItems as $item) {
    $productId = $item["id"];
    $quantity = $item["quantity"];

    $stmtStock->bind_param(
        "iii",
        $quantity,
        $productId,
        $quantity
    );

    $stmtStock->execute();

    if ($stmtStock->affected_rows !== 1) {
        throw new Exception(
            "Gagal memperbarui stok produk " . $item["name"]
        );
    }
}

$stmtStock->close(); 

    // ==========================================
    // 5. SIMPAN DATA PEMBAYARAN
    // ==========================================

    // QRIS dan COD sama-sama menunggu konfirmasi pembayaran.
    $statusBayar = "Menunggu";

    $stmtPayment = $conn->prepare("
        INSERT INTO pembayaran
        (
            Metode_Bayar,
            Jumlah_Bayar,
            Status_Bayar,
            ID_Pesanan
        )
        VALUES (?, ?, ?, ?)
    ");

    $stmtPayment->bind_param(
        "sdsi",
        $paymentMethod,
        $totalHarga,
        $statusBayar,
        $orderId
    );

    $stmtPayment->execute();
    $stmtPayment->close();

    // ==========================================
    // 6. SELESAIKAN TRANSAKSI
    // ==========================================

    $conn->commit();

    response(true, "Pesanan berhasil disimpan.", [
        "orderId" => $orderId,
        "totalHarga" => $totalHarga,
        "paymentMethod" => $paymentMethod,
        "items" => $validatedItems
    ]);

} catch (Throwable $e) {

    $conn->rollback();

    error_log("Create order error: " . $e->getMessage());

    response(
        false,
        $e->getMessage(),
        [],
        400
    );
}
