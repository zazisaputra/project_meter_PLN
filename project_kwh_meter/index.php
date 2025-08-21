<?php
// Kode PHP di bagian atas ini tidak berubah sama sekali
session_start();

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: dashboard.php");
    exit;
}

function validateUser($username, $password) {
    $loginFile = 'tabel_login.csv';
    if (!file_exists($loginFile) || !is_readable($loginFile)) { return false; }
    $isValid = false;
    if (($handle = fopen($loginFile, "r")) !== FALSE) {
        fgetcsv($handle, 1000, ";");
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            $csv_username = isset($data[4]) ? trim($data[4]) : '';
            $csv_password = isset($data[5]) ? trim($data[5]) : '';
            if ($csv_username === $username && $csv_password === $password) {
                $isValid = true;
                break;
            }
        }
        fclose($handle);
    }
    return $isValid;
}

$login_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_username = $_POST['username'];
    $input_password = $_POST['password'];
    if (validateUser($input_username, $input_password)) {
        session_start();
        $_SESSION["loggedin"] = true;
        $_SESSION["username"] = $input_username;
        header("location: dashboard.php");
        exit;
    } else {
        $login_err = "Username atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Login - Ikon Dinamis</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <h2>Login Pengguna</h2>
            <p>Silakan masuk untuk mengakses data.</p>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="input-group password-wrapper">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
                <img src="hidden.png" id="togglePassword" alt="Tampilkan Password">
            </div>
            <?php 
            if(!empty($login_err)){
                echo '<p class="error">' . $login_err . '</p>';
            }
            ?>
            <button type="submit">Login</button>
        </form>
        <footer>
            <p>&copy; <?php echo date("Y"); ?> &mdash; Dibuat oleh Team IT Citra Sanxing Indonesia</p>
        </footer>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');

            const eyeOpenIcon = 'view.png';     // Nama file ikon mata terbuka
            const eyeSlashedIcon = 'hidden.png';  // Nama file ikon mata tercoret

            togglePassword.addEventListener('click', function () {
                // Cek tipe input saat ini
                const isPassword = passwordInput.getAttribute('type') === 'password';

                if (isPassword) {
                    // Jika password, ubah ke text dan ganti ikon ke mata terbuka
                    passwordInput.setAttribute('type', 'text');
                    this.src = eyeOpenIcon;
                    this.alt = 'Sembunyikan Password';
                } else {
                    // Jika text, ubah ke password dan ganti ikon ke mata tercoret
                    passwordInput.setAttribute('type', 'password');
                    this.src = eyeSlashedIcon;
                    this.alt = 'Tampilkan Password';
                }
            });
        });
    </script>
</body>
</html>