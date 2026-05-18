<?php
require_once __DIR__ . '/../config/config.php'; header('Content-Type: application/json');
$code=$_GET['code']??''; $st=$pdo->prepare('SELECT long_url FROM urls WHERE short_code=?'); $st->execute([$code]); $u=$st->fetch();
echo json_encode($u?['status'=>'success','long_url'=>$u['long_url']]:['status'=>'error','message'=>'Not found']);
