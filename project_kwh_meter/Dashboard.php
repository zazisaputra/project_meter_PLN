<?php
// Memulai sesi
session_start();

// Cek apakah pengguna sudah login, jika belum, arahkan ke halaman login
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

/**
 * Fungsi untuk mencari data berdasarkan SN (Serial Number) dari file CSV.
 * @param string $sn - Nomor SN yang ingin dicari.
 * @return array|null - Mengembalikan array data jika ditemukan, atau null jika tidak.
 */
function findDataBySN($sn) {
    $csvFile = 'data5.csv'; // Nama file CSV Anda

    // Cek apakah file ada dan bisa dibaca
    if (!file_exists($csvFile) || !is_readable($csvFile)) {
        return null; 
    }

    $dataFound = null;
    // Buka file CSV untuk dibaca
    if (($handle = fopen($csvFile, "r")) !== FALSE) {
        // Lewati baris header pertama
        fgetcsv($handle, 1000, ";");

        // Baca file baris per baris
        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
            // Kolom pertama (indeks 0) adalah 'sn'
            if (isset($data[0]) && $data[0] == $sn) {
                $dataFound = [
                    'sn' => $data[0],
                    'system_title' => $data[1],
                    'ak' => $data[2],
                    'ek' => $data[3]
                ];
                break; // Hentikan pencarian jika data sudah ditemukan
            }
        }
        fclose($handle); // Tutup file
    }
    return $dataFound;
}

// Inisialisasi variabel hasil pencarian
$search_result = "";
$nik_input = "";

// Cek jika ada request GET untuk pencarian NIK/SN
if ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($_GET['nik'])) {
    $nik_input = htmlspecialchars($_GET['nik']);
    $data = findDataBySN($nik_input);
    
    if ($data) {
        // Jika data ditemukan, format untuk ditampilkan
        $search_result = "
            <p><strong>No Meter:</strong> {$data['sn']}</p>
            <p><strong>Password:</strong> {$data['system_title']}</p>
            <p><strong>User Verification:</strong> {$data['ak']}</p>
            <p><strong>Password Verification:</strong> {$data['ek']}</p>
            <p><strong>Physical Address:</strong> 1 </p>
            <p><strong>Logical Address:</strong> 1 </p>
            <p><strong>Is Ver:</strong> 1</p>
        ";
    } else {
        $search_result = "<p>Data untuk SN <strong>{$nik_input}</strong> tidak ditemukan dalam file.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Data dari CSV</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Selamat Datang UP3, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
            <a href="logout.php"><button id="logoutButton">Logout</button></a>
        </div>
        
        <h3>Cek Data Berdasarkan No Meter</h3>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
            <div class="input-group">
                <label for="nikInput">Masukkan Nomor Meter</label>
                <input type="text" id="nikInput" name="nik" placeholder="Contoh: 45625000010" value="<?php echo $nik_input; ?>" required>
            </div>
            <button type="submit">Cari Data</button>
        </form>
        
        <div id="data-result">
            <?php echo $search_result; // Tampilkan hasil pencarian di sini ?>
        </div>
        <footer>
            <p>&copy; <?php echo date("Y"); ?> &mdash; Dibuat oleh Team IT Citra Sanxing Indonesia</p>
        </footer>
    </div>
</body>
</html>