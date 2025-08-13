<?php
// Thông tin Bot Telegram
$botToken = "123456789:ABCdefGHIjklMNOpqrSTUvwxYZ";

// Thông tin DB MySQL/MariaDB
$host = "sql313.ezyro.com";  // host mới
$port = "3306";               // cổng MySQL
$dbname = "ezyro_39696007_beetm";          // tên DB
$user = "ezyro_39696007";             // user DB
$password = "980c532";     // mật khẩu DB

// Kết nối MySQL
$conn = new mysqli($host, $user, $password, $dbname, $port);

// Kiểm tra lỗi kết nối
if ($conn->connect_error) {
    die("Kết nối DB thất bại: " . $conn->connect_error);
}
?>
