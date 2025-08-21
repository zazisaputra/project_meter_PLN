<?php
// Memulai sesi
session_start();

// Hapus semua variabel sesi
$_SESSION = [];

// Hancurkan sesi
session_destroy();

// Arahkan ke halaman login
header("location: index.php");
exit;
?>