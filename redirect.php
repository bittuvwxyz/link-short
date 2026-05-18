<?php
require_once __DIR__ . '/includes/functions.php';
$code = $_GET['code'] ?? '';
if ($code === '') { http_response_code(404); include '404.php'; exit; }
$st = $pdo->prepare('SELECT * FROM urls WHERE short_code = ? LIMIT 1');
$st->execute([$code]);
$url = $st->fetch();
if (!$url || ($url['expires_at'] && strtotime($url['expires_at']) < time())) { http_response_code(404); include '404.php'; exit; }
if (!empty($url['password_hash'])) {
    if ($_SERVER['REQUEST_METHOD']==='POST' && password_verify($_POST['url_password'] ?? '', $url['password_hash'])) {
        $_SESSION['unlock_'.$code] = 1;
    }
    if (empty($_SESSION['unlock_'.$code])) {
        echo '<form method="post" style="max-width:400px;margin:80px auto"><h3>Password Protected URL</h3><input type="password" name="url_password" class="form-control" placeholder="Password"><button class="btn btn-primary mt-2">Unlock</button></form>';
        exit;
    }
}
$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
$ip = client_ip();
$geo = geo_lookup($ip);
$ref = $_SERVER['HTTP_REFERER'] ?? '';
$uniqueHash = hash('sha256', $ip . '|' . date('Y-m-d') . '|' . $code);
$pdo->prepare('INSERT INTO url_clicks (url_id, ip_address, country, city, browser, device, os, referrer, user_agent, clicked_at, unique_hash) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), ?)')
    ->execute([$url['id'],$ip,$geo['country'],$geo['city'],detect_browser($ua),get_device($ua),get_os($ua),$ref,$ua,$uniqueHash]);
header('Location: ' . $url['long_url'], true, 302); exit;
