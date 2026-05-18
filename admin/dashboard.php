<?php
require_once 'auth.php';
if (isset($_GET['delete'])) { $pdo->prepare('DELETE FROM urls WHERE id=?')->execute([(int)$_GET['delete']]); header('Location: dashboard.php'); exit; }
$q = trim($_GET['q'] ?? ''); $page = max(1, (int)($_GET['page'] ?? 1)); $per = 10; $off = ($page-1)*$per;
$where = $q!=='' ? ' WHERE long_url LIKE :q OR short_code LIKE :q ' : '';
$st = $pdo->prepare('SELECT * FROM urls '.$where.' ORDER BY id DESC LIMIT '.$per.' OFFSET '.$off);
if ($q!=='') $st->bindValue(':q', '%'.$q.'%'); $st->execute(); $rows=$st->fetchAll();
$totalLinks=(int)$pdo->query('SELECT COUNT(*) c FROM urls')->fetch()['c'];
$totalClicks=(int)$pdo->query('SELECT COUNT(*) c FROM url_clicks')->fetch()['c'];
$top=$pdo->query('SELECT u.short_code, COUNT(c.id) clicks FROM urls u LEFT JOIN url_clicks c ON c.url_id=u.id GROUP BY u.id ORDER BY clicks DESC LIMIT 5')->fetchAll();
$recent=$pdo->query('SELECT * FROM url_clicks ORDER BY id DESC LIMIT 10')->fetchAll();
?><!doctype html><html><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><script src="https://cdn.jsdelivr.net/npm/chart.js"></script></head><body><div class="container py-4"><h2>Dashboard</h2><div class="row"><div class="col">Links: <?=$totalLinks?></div><div class="col">Clicks: <?=$totalClicks?></div></div>
<form class="my-3"><input name="q" value="<?=e($q)?>" class="form-control" placeholder="Search"></form>
<table class="table table-bordered"><tr><th>ID</th><th>Code</th><th>Long URL</th><th>Action</th></tr><?php foreach($rows as $r):?><tr><td><?=$r['id']?></td><td><?=e($r['short_code'])?></td><td><?=e($r['long_url'])?></td><td><a class="btn btn-sm btn-danger" href="?delete=<?=$r['id']?>">Delete</a></td></tr><?php endforeach;?></table>
<a class="btn btn-secondary" href="export_csv.php">Export CSV</a> <a class="btn btn-info" href="analytics.php">Analytics</a>
<h4 class="mt-4">Top clicked</h4><ul><?php foreach($top as $t) echo '<li>'.e($t['short_code']).' - '.$t['clicks'].'</li>';?></ul>
<h4>Recent visitors</h4><ul><?php foreach($recent as $v) echo '<li>'.e($v['ip_address']).' '.$v['country'].' '.$v['clicked_at'].'</li>';?></ul></div></body></html>
