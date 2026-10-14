<?php
// SMTP credentials are read only on the server, never from storefront settings.
function smtpSettings(): array {
    return [
        'host' => 'smtp.hostinger.com',
        'port' => 465,
        'user' => getenv('BRYXA_SMTP_USERNAME') ?: 'contato@bryxa.com.br',
        'password' => getenv('BRYXA_SMTP_PASSWORD') ?: '',
        'from' => getenv('BRYXA_SMTP_FROM') ?: 'contato@bryxa.com.br',
        'name' => 'BRYXA',
        'enabled' => getenv('BRYXA_SMTP_ENABLED') === 'true',
    ];
}
function smtpReady(): bool {
    $s = smtpSettings();
    return $s['enabled'] && $s['password'] !== '' && function_exists('curl_init');
}
function smtpMessage(array $mail, array $s): string {
    foreach ([$mail['recipient'], $s['from'], $s['user']] as $address) {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL) || preg_match('/[\r\n]/', $address)) throw new RuntimeException('Invalid email address');
    }
    $boundary = 'bryxa_' . bin2hex(random_bytes(18));
    $encoded = fn($v) => '=?UTF-8?B?' . base64_encode($v) . '?=';
    $headers = [
        'Date: ' . date(DATE_RFC2822),
        'Message-ID: <outbox-' . (int)$mail['id'] . '@bryxa.com.br>',
        'From: ' . $encoded($s['name']) . ' <' . $s['from'] . '>',
        'To: <' . $mail['recipient'] . '>',
        'Reply-To: <' . $s['from'] . '>',
        'Subject: ' . $encoded($mail['subject']),
        'MIME-Version: 1.0',
        'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
    ];
    $message = implode("\r\n", $headers) . "\r\n\r\n";
    foreach (['plain' => 'text/plain', 'html' => 'text/html'] as $field => $type) {
        $message .= '--' . $boundary . "\r\nContent-Type: $type; charset=UTF-8\r\nContent-Transfer-Encoding: base64\r\n\r\n" . chunk_split(base64_encode($mail[$field]), 76, "\r\n");
    }
    return $message . '--' . $boundary . "--\r\n";
}
function smtpSend(array $mail): void {
    if (!smtpReady()) throw new RuntimeException('SMTP not configured');
    $s = smtpSettings();
    $message = smtpMessage($mail, $s);
    $stream = fopen('php://temp', 'r+');
    fwrite($stream, $message);
    rewind($stream);
    $ch = curl_init('smtps://' . $s['host'] . ':' . $s['port']);
    curl_setopt_array($ch, [
        CURLOPT_USERNAME => $s['user'], CURLOPT_PASSWORD => $s['password'],
        CURLOPT_MAIL_FROM => $s['from'], CURLOPT_MAIL_RCPT => [$mail['recipient']],
        CURLOPT_UPLOAD => true, CURLOPT_INFILE => $stream, CURLOPT_INFILESIZE => strlen($message),
        CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 10, CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true, CURLOPT_SSL_VERIFYHOST => 2, CURLOPT_USE_SSL => CURLUSESSL_ALL,
    ]);
    try {
        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        if ($result === false || $code < 200 || $code >= 300) throw new RuntimeException('SMTP delivery failed (' . (int)$code . '/' . curl_errno($ch) . ')');
    } finally {
        curl_close($ch);
        fclose($stream);
    }
}
function smtpProcess(PDO $db, ?callable $send = null): int {
    $db->exec("UPDATE email_outbox SET status='Envio interrompido',last_error='Confira a entrega antes de tentar novamente.' WHERE status='Enviando' AND attempted_at < strftime('%s','now')-600");
    $db->exec("UPDATE email_outbox SET status='Expirado' WHERE status='Pendente' AND expires_at < strftime('%s','now')");
    if (!$send && !smtpReady()) return 0;
    $send ??= 'smtpSend';
    $processed = 0;
    // Claim each message atomically; failed/ambiguous sends are never blindly resent.
    foreach ($db->query("SELECT * FROM email_outbox WHERE status='Pendente' ORDER BY id LIMIT 5")->fetchAll(PDO::FETCH_ASSOC) as $mail) {
        $claim = $db->prepare("UPDATE email_outbox SET status='Enviando',attempted_at=? WHERE id=? AND status='Pendente'");
        $claim->execute([time(), $mail['id']]);
        if (!$claim->rowCount()) continue;
        try {
            $send($mail);
            $db->prepare("UPDATE email_outbox SET status='Enviado',sent_at=?,last_error='' WHERE id=?")->execute([date('c'), $mail['id']]);
        } catch (Throwable $e) {
            $db->prepare("UPDATE email_outbox SET status='Falha no envio',last_error=? WHERE id=?")->execute(['Verifique as credenciais SMTP e a conexão do servidor.', $mail['id']]);
            error_log('BRYXA SMTP message ' . (int)$mail['id'] . ': delivery failed');
        }
        $processed++;
    }
    return $processed;
}
