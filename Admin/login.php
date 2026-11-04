<?php
session_start();

require_once __DIR__ . "/../config/database.php";

// Jika sudah login, langsung ke dashboard
if (isset($_SESSION["ID_Admin"])) {
    header("Location: dashboard.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Username dan password wajib diisi.";
    } else {
        $stmt = $conn->prepare(
            "SELECT ID_Admin, Nama_Admin, Username, Password
             FROM admin_pengguna
             WHERE Username = ?
             LIMIT 1"
        );

        $stmt->bind_param("s", $username);
        $stmt->execute();

        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && password_verify($password, $admin["Password"])) {
            session_regenerate_id(true);

            $_SESSION["ID_Admin"] = $admin["ID_Admin"];
            $_SESSION["Nama_Admin"] = $admin["Nama_Admin"];
            $_SESSION["Username"] = $admin["Username"];

            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Username atau password salah.";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login Admin - Citra Snack</title>

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <link rel="stylesheet" href="assets/css/admin-login.css">
</head>

<body>

    <main class="login-page">

        <div class="login-card">

            <div class="login-logo">
                Citra<span>Snack</span>
            </div>

            <div class="login-header">
                <h1>Login Admin</h1>
                <p>Masuk untuk mengelola Citra Snack.</p>
            </div>

            <form id="loginForm" method="POST" action="">

                <div class="form-group">
                    <label for="username">Username</label>

                    <div class="input-wrapper">
                        <i class="bi bi-person"></i>

                        <input
                            type="text"
                            id="username"
                            name="username"
                            placeholder="Masukkan username"
                            value="<?= htmlspecialchars($_POST["username"] ?? "") ?>"
                            required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <div class="input-wrapper">
                        <i class="bi bi-lock"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            required>

                        <button type="button" id="togglePassword">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="login-error" style="display: flex;">
                        <i class="bi bi-exclamation-circle"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>

                <button
                    type="submit"
                    class="btn-login"
                    id="loginButton">

                    <span>Masuk ke Dashboard</span>
                </button>

            </form>

            <a href="../index.html" class="back-home">
                <i class="bi bi-arrow-left"></i>
                Kembali ke website
            </a>

        </div>

    </main>

    <script>
        const togglePassword = document.getElementById("togglePassword");
        const passwordInput = document.getElementById("password");

        togglePassword.addEventListener("click", function () {
            const icon = this.querySelector("i");

            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.className = "bi bi-eye-slash";
            } else {
                passwordInput.type = "password";
                icon.className = "bi bi-eye";
            }
        });
    </script>

</body>
</html>