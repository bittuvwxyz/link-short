<?php
require_once __DIR__ . '/../config/config.php';

function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf() {
    return isset($_POST['csrf_token'], $_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
}

function client_ip() {
    foreach (['HTTP_CF_CONNECTING_IP','HTTP_X_FORWARDED_FOR','REMOTE_ADDR'] as $k) {
        if (!empty($_SERVER[$k])) return trim(explode(',', $_SERVER[$k])[0]);
    }
    return '0.0.0.0';
}

function get_os($ua) {
    $oses = ['Windows'=>'Windows','Mac'=>'Macintosh|Mac OS X','Linux'=>'Linux','Android'=>'Android','iOS'=>'iPhone|iPad'];
    foreach ($oses as $n=>$p) if (preg_match('/'.$p.'/i', $ua)) return $n;
    return 'Other';
}
function detect_browser($ua) {
    $b = ['Edge'=>'Edg','Chrome'=>'Chrome','Firefox'=>'Firefox','Safari'=>'Safari','Opera'=>'OPR'];
    foreach ($b as $n=>$p) if (stripos($ua, $p)!==false) return $n;
    return 'Other';
}
function get_device($ua) { return preg_match('/Mobile|Android|iPhone/i', $ua) ? 'Mobile' : 'Desktop'; }

function geo_lookup($ip) {
    $json = @file_get_contents('http://ip-api.com/json/' . urlencode($ip));
    if (!$json) return ['country'=>'Unknown','city'=>'Unknown'];
    $d = json_decode($json, true);
    return ['country'=>$d['country'] ?? 'Unknown', 'city'=>$d['city'] ?? 'Unknown'];
}

function rate_limited($pdo, $ip, $limitPerHour) {
    $st = $pdo->prepare('SELECT COUNT(*) c FROM urls WHERE creator_ip = ? AND created_at >= (NOW() - INTERVAL 1 HOUR)');
    $st->execute([$ip]);
    return ((int)$st->fetch()['c']) >= (int)$limitPerHour;
}

function send_notification_email($to, $longUrl, $shortUrl, $analyticsUrl) {
    global $SMTP_HOST,$SMTP_PORT,$SMTP_SECURE,$SMTP_USER,$SMTP_PASS,$SMTP_FROM,$SMTP_FROM_NAME;
    require_once __DIR__ . '/../vendor/phpmailer/src/Exception.php';
    require_once __DIR__ . '/../vendor/phpmailer/src/PHPMailer.php';
    require_once __DIR__ . '/../vendor/phpmailer/src/SMTP.php';

    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = $SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = $SMTP_USER;
        $mail->Password = $SMTP_PASS;
        $mail->SMTPSecure = $SMTP_SECURE;
        $mail->Port = $SMTP_PORT;
        $mail->setFrom($SMTP_FROM, $SMTP_FROM_NAME);
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = 'New Short URL Created';
        $mail->Body = '<p>Original URL: '.e($longUrl).'</p><p>Short URL: '.e($shortUrl).'</p><p>Date: '.date('Y-m-d H:i:s').'</p><p>Analytics: '.e($analyticsUrl).'</p>';
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
