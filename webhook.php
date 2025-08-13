<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
$config = require __DIR__ . '/config.php';

// ===== Kết nối Postgres =====
function db() {
    static $pdo;
    if ($pdo) return $pdo;
    $cfg = require __DIR__.'/config.php';
    $dsn = "pgsql:host={$cfg['DB']['host']};port={$cfg['DB']['port']};dbname={$cfg['DB']['name']}";
    $pdo = new PDO($dsn, $cfg['DB']['user'], $cfg['DB']['pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

// ===== Hàm gửi tin nhắn =====
function tg_reply($chat_id, $text) {
    $cfg = require __DIR__ . '/config.php';
    $url = "https://api.telegram.org/bot{$cfg['BOT_TOKEN']}/sendMessage";
    $payload = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];
    $opts = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($payload),
            'ignore_errors' => true
        ]
    ];
    @file_get_contents($url, false, stream_context_create($opts));
}

// ===== Hàm lấy phí nhóm =====
function get_fee($chat_id) {
    $pdo = db();
    $row = $pdo->prepare("SELECT fee_percent FROM fees WHERE chat_id=?");
    $row->execute([$chat_id]);
    $data = $row->fetch();
    if (!$data) {
        $pdo->prepare("INSERT INTO fees(chat_id, fee_percent) VALUES(?,0)")->execute([$chat_id]);
        return 0;
    }
    return (float)$data['fee_percent'];
}

function set_fee($chat_id, $fee) {
    $pdo = db();
    $pdo->prepare("
        INSERT INTO fees(chat_id, fee_percent) VALUES(?,?)
        ON CONFLICT (chat_id) DO UPDATE SET fee_percent = EXCLUDED.fee_percent
    ")->execute([$chat_id, $fee]);
}

// ===== Xử lý webhook =====
$raw = file_get_contents('php://input');
if (!$raw) { echo 'ok'; exit; }
$update = json_decode($raw, true);
$msg = $update['message'] ?? null;
if (!$msg || !isset($msg['chat']['id'])) { echo 'ok'; exit; }

$chat_id = $msg['chat']['id'];
$text = trim($msg['text'] ?? '');
if ($text === '' || $text[0] !== '/') { echo 'ok'; exit; }

list($cmdRaw, $rest) = array_pad(explode(' ', $text, 2), 2, '');
$cmd = strtolower(explode('@', $cmdRaw)[0]);
$args = $rest ? preg_split('/\s+/', $rest) : [];

switch ($cmd) {
    case '/start':
        tg_reply($chat_id, "Xin chào! Bot đã sẵn sàng.");
        break;
    case '/setfee':
        $fee = floatval($args[0] ?? 0);
        set_fee($chat_id, $fee);
        tg_reply($chat_id, "Đã đặt phí: {$fee}%");
        break;
    case '/getfee':
        $fee = get_fee($chat_id);
        tg_reply($chat_id, "Phí hiện tại: {$fee}%");
        break;
    default:
        tg_reply($chat_id, "Lệnh không hợp lệ.");
}

echo 'ok';
