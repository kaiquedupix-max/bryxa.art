<?php
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/public/api/smtp.php';
$config = is_file(__DIR__ . '/config.local.php') ? require __DIR__ . '/config.local.php' : [];
$storage = $config['storage_path'] ?? __DIR__ . '/storage';
$loop = in_array('--loop', $argv, true);
do {
    try {
        if (is_file($storage . '/bryxa.sqlite')) {
            $db = new PDO('sqlite:' . $storage . '/bryxa.sqlite', null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
            $db->exec('PRAGMA busy_timeout=5000');
            $columns = array_column($db->query('PRAGMA table_info(email_outbox)')->fetchAll(PDO::FETCH_ASSOC), 'name');
            if (in_array('expires_at', $columns, true)) smtpProcess($db);
        }
    } catch (Throwable $e) { error_log('BRYXA email worker: database temporarily unavailable'); }
    if ($loop) sleep(30);
} while ($loop);
