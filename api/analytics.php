<?php
require_once __DIR__ . '/../includes/functions.php'; header('Content-Type: application/json');
$code=$_GET['code'] ?? ''; $st=$pdo->prepare('SELECT id,long_url,short_code FROM urls WHERE short_code=?'); $st->execute([$code]); $u=$st->fetch();
if(!$u){echo json_encode(['status'=>'error','message'=>'Not found']);exit;}
$q=$pdo->prepare('SELECT COUNT(*) clicks, COUNT(DISTINCT unique_hash) unique_clicks FROM url_clicks WHERE url_id=?'); $q->execute([$u['id']]);
echo json_encode(['status'=>'success','url'=>$u,'stats'=>$q->fetch()]);
