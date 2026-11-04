<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "citra_snack";

$conn = new mysqli(
    $host,
    $user,
    $password,
    $database
);

if ($conn->connect_error) {
    http_response_code(500);
    exit("Koneksi database gagal.");
}

$conn->set_charset("utf8mb4");
