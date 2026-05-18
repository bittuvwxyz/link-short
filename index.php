<?php
require_once __DIR__ . '/includes/functions.php';

$message = '';
$shortUrl = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf()) { $message = 'Invalid CSRF token.'; }
    else {
        $longUrl = trim($_POST['long_url'] ?? '');
        $custom = preg_replace('/[^a-zA-Z0-9_-]/', '', trim($_POST['custom_alias'] ?? ''));
        $expiresAt = !empty($_POST['expires_at']) ? $_POST['expires_at'].':00' : null;
        $password = trim($_POST['url_password'] ?? '');
        $ip = client_ip();

        if (!filter_var($longUrl, FILTER_VALIDATE_URL)) $message = 'Invalid URL.';
        elseif (rate_limited($pdo, $ip, $RATE_LIMIT_PER_HOUR)) $message = 'Rate limit exceeded. Try again later.';
        else {
            $hash = $password !== '' ? password_hash($password, PASSWORD_DEFAULT) : null;
            if ($custom !== '') {
                $st = $pdo->prepare('SELECT id FROM urls WHERE short_code = ?');
                $st->execute([$custom]);
                if ($st->fetch()) $message = 'Custom alias already exists.';
            }
            if ($message === '') {
                $pdo->prepare('INSERT INTO urls (long_url, short_code, creator_ip, expires_at, password_hash, created_at) VALUES (?, NULL, ?, ?, ?, NOW())')
                    ->execute([$longUrl, $ip, $expiresAt, $hash]);
                $id = (int)$pdo->lastInsertId();
                $code = $custom !== '' ? $custom : (string)(999999 + $id);
                $pdo->prepare('UPDATE urls SET short_code = ? WHERE id = ?')->execute([$code, $id]);
                $shortUrl = $BASE_URL . '/' . $code;
                $analytics = $BASE_URL . '/admin/analytics.php?code=' . urlencode($code);
                send_notification_email($NOTIFY_TO, $longUrl, $shortUrl, $analytics);
                $message = 'Short URL generated successfully!';
            }
        }
    }
}
?>
<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?=e($SITE_NAME)?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet"></head><body class="bg-light">
<div class="container py-5"><div class="d-flex justify-content-between"><h1>URL Shortener</h1><button id="darkMode" class="btn btn-outline-secondary btn-sm">Dark</button></div>
<?php if($message):?><div class="alert alert-info"><?=e($message)?></div><?php endif;?>
<form method="post" class="card p-3 shadow-sm">
<input type="hidden" name="csrf_token" value="<?=e(csrf_token())?>">
<div class="mb-3"><label class="form-label">Long URL</label><input required name="long_url" type="url" class="form-control"></div>
<div class="mb-3"><label class="form-label">Custom alias (optional)</label><input name="custom_alias" class="form-control"></div>
<div class="mb-3"><label class="form-label">Expiration (optional)</label><input name="expires_at" type="datetime-local" class="form-control"></div>
<div class="mb-3"><label class="form-label">Password (optional)</label><input name="url_password" type="password" class="form-control"></div>
<button class="btn btn-primary">Generate</button></form>
<?php if($shortUrl):?><div class="mt-4 card p-3"><p><b>Short URL:</b> <a href="<?=e($shortUrl)?>"><?=e($shortUrl)?></a></p><button class="btn btn-success" onclick="copyText('<?=e($shortUrl)?>')">Copy</button>
<img class="mt-2" src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?=urlencode($shortUrl)?>" alt="QR"></div><?php endif;?>
</div><script src="assets/js/app.js"></script></body></html>
