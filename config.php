<?php
// Thông tin Bot Telegram
$botToken = "7969927874:AAErxyYwZLOQCxudE6JLqGD6pAMKZf9CM0E";

// Thông tin DB PostgreSQL
$host = "dpg-d2e5s28gjchc73e2kb40-a.oregon-postgres.render.com";
$port = "5432";
$dbname = "beetm";
$user = "beetm_user";
$password = "e0H83VDofaXiw1XQadZiE7WOirMPYFke";

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$password");
if (!$conn) {
    die("Kết nối DB thất bại: " . pg_last_error());
}
?>
