
<?php
session_start();

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/../config/database.php";

// =====================================
// RESPONSE
// =====================================

function response($success, $message, $data = null, $code = 200) {
    http_response_code($code);

    echo json_encode([
        "success" => $success,
        "message" => $message,
        "data" => $data
    ]);

    exit;
}

// =====================================
// CEK LOGIN ADMIN
// =====================================

if (!isset($_SESSION["ID_Admin"])) {
    response(false, "Silakan login sebagai admin.", null, 401);
}

// =====================================
// FOLDER UPLOAD
// =====================================

$uploadDir = __DIR__ . "/../uploads/products/";

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// =====================================
// GET PRODUCTS
// =====================================

if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $sql = "SELECT
                ID_Produk,
                Nama_Produk,
                Kategori,
                Harga,
                Stok,
                Foto_Produk,
                Deskripsi
            FROM produk
            ORDER BY ID_Produk DESC";

    $result = $conn->query($sql);

    if (!$result) {
        response(false, "Gagal mengambil produk: " . $conn->error, null, 500);
    }

    $products = [];

    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    response(true, "Data produk berhasil diambil.", $products);
}

// =====================================
// POST REQUEST
// =====================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    response(false, "Metode tidak diizinkan.", null, 405);
}

$action = $_POST["action"] ?? "";

// =====================================
// DELETE PRODUCT
// =====================================

if ($action === "delete") {

    $id = (int)($_POST["id"] ?? 0);

    if ($id <= 0) {
        response(false, "ID produk tidak valid.", null, 400);
    }

    $stmt = $conn->prepare(
        "SELECT Foto_Produk FROM produk WHERE ID_Produk = ?"
    );

    $stmt->bind_param("i", $id);
    $stmt->execute();

    $product = $stmt->get_result()->fetch_assoc();

    if (!$product) {
        response(false, "Produk tidak ditemukan.", null, 404);
    }

    $stmt = $conn->prepare(
        "DELETE FROM produk WHERE ID_Produk = ?"
    );

    $stmt->bind_param("i", $id);

    if (!$stmt->execute()) {
        response(false, "Produk tidak dapat dihapus. Mungkin sudah digunakan dalam pesanan.", null, 409);
    }

    $oldPhoto = $product["Foto_Produk"];

    if ($oldPhoto && strpos($oldPhoto, "uploads/products/") === 0) {
        $oldPath = __DIR__ . "/../" . $oldPhoto;

        if (is_file($oldPath)) {
            unlink($oldPath);
        }
    }

    response(true, "Produk berhasil dihapus.");
}

// =====================================
// PRODUCT DATA
// =====================================

$id = (int)($_POST["id"] ?? 0);

$nama = trim($_POST["nama_produk"] ?? "");
$kategori = trim($_POST["kategori"] ?? "");
$harga = (float)($_POST["harga"] ?? 0);
$stok = (int)($_POST["stok"] ?? 0);
$deskripsi = trim($_POST["deskripsi"] ?? "");

if ($nama === "" || $kategori === "" || $harga < 0 || $stok < 0) {
    response(false, "Data produk belum lengkap atau tidak valid.", null, 400);
}

// =====================================
// UPLOAD FOTO
// =====================================

$fotoPath = $_POST["foto_lama"] ?? "";

if (isset($_FILES["foto"]) && $_FILES["foto"]["error"] !== UPLOAD_ERR_NO_FILE) {

    $file = $_FILES["foto"];

    if ($file["error"] !== UPLOAD_ERR_OK) {
        response(false, "Upload foto gagal.", null, 400);
    }

    if ($file["size"] > 5 * 1024 * 1024) {
        response(false, "Ukuran foto maksimal 5 MB.", null, 400);
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file["tmp_name"]);

    $allowed = [
        "image/jpeg" => "jpg",
        "image/png" => "png",
        "image/webp" => "webp"
    ];

    if (!isset($allowed[$mime])) {
        response(false, "Format foto harus JPG, PNG, atau WebP.", null, 400);
    }

    $filename = bin2hex(random_bytes(16)) . "." . $allowed[$mime];

    if (!move_uploaded_file($file["tmp_name"], $uploadDir . $filename)) {
        response(false, "Gagal menyimpan foto.", null, 500);
    }

    $fotoPath = "uploads/products/" . $filename;
}

// =====================================
// UPDATE PRODUCT
// =====================================

if ($action === "update") {

    if ($id <= 0) {
        response(false, "ID produk tidak valid.", null, 400);
    }

    $stmt = $conn->prepare("
        UPDATE produk
        SET
            Nama_Produk = ?,
            Kategori = ?,
            Harga = ?,
            Stok = ?,
            Foto_Produk = ?,
            Deskripsi = ?
        WHERE ID_Produk = ?
    ");

    $stmt->bind_param(
        "ssdissi",
        $nama,
        $kategori,
        $harga,
        $stok,
        $fotoPath,
        $deskripsi,
        $id
    );

    if (!$stmt->execute()) {
        response(false, "Gagal memperbarui produk: " . $stmt->error, null, 500);
    }

    response(true, "Produk berhasil diperbarui.");
}

// =====================================
// CREATE PRODUCT
// =====================================

if ($action === "create") {

    if ($fotoPath === "") {
        response(false, "Foto produk wajib diunggah.", null, 400);
    }

    $stmt = $conn->prepare("
        INSERT INTO produk
        (
            Nama_Produk,
            Kategori,
            Harga,
            Stok,
            Foto_Produk,
            Deskripsi
        )
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "ssdiss",
        $nama,
        $kategori,
        $harga,
        $stok,
        $fotoPath,
        $deskripsi
    );

    if (!$stmt->execute()) {
        response(false, "Gagal menambahkan produk: " . $stmt->error, null, 500);
    }

    response(true, "Produk berhasil ditambahkan.", [
        "ID_Produk" => $stmt->insert_id
    ]);
}

// =====================================
// INVALID ACTION
// =====================================

response(false, "Aksi tidak dikenali.", null, 400);
