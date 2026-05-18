<?php
require_once __DIR__ . '/../includes/functions.php'; header('Content-Type: application/json');
$longUrl = trim($_POST['long_url'] ?? ''); if(!filter_var($longUrl,FILTER_VALIDATE_URL)){echo json_encode(['status'=>'error','message'=>'Invalid URL']);exit;}
$pdo->prepare('INSERT INTO urls (long_url, short_code, creator_ip, created_at) VALUES (?, NULL, ?, NOW())')->execute([$longUrl, client_ip()]);
$id=(int)$pdo->lastInsertId(); $code=(string)(999999+$id); $pdo->prepare('UPDATE urls SET short_code=? WHERE id=?')->execute([$code,$id]);
echo json_encode(['status'=>'success','short_url'=>$BASE_URL.'/'.$code,'code'=>$code]);
