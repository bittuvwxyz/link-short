<?php
require_once __DIR__ . '/../includes/functions.php';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    $u = trim($_POST['username'] ?? ''); $p = $_POST['password'] ?? '';
    $st = $pdo->prepare('SELECT * FROM admin WHERE username=? LIMIT 1'); $st->execute([$u]);
    $a = $st->fetch();
    if ($a && password_verify($p, $a['password_hash'])) { $_SESSION['admin_id'] = $a['id']; header('Location: dashboard.php'); exit; }
    $err = 'Invalid credentials';
}
?><!doctype html><html><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"></head><body><div class="container py-5" style="max-width:420px"><h3>Admin Login</h3><?php if(!empty($err)) echo '<div class="alert alert-danger">'.e($err).'</div>';?><form method="post"><input class="form-control mb-2" name="username" placeholder="Username"><input class="form-control mb-2" type="password" name="password" placeholder="Password"><button class="btn btn-primary">Login</button></form></div></body></html>
