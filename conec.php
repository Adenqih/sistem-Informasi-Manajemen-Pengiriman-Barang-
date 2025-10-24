<?php
// conec.php - database connection
$host = '127.0.0.1';
$db   = 'shiptrack';
$user = 'root';
$pass = '';
$port = 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);
if ($conn->connect_errno) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset('utf8mb4');
?>
