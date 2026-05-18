<?php
require_once 'auth.php';
header('Content-Type:text/csv'); header('Content-Disposition: attachment; filename="analytics.csv"');
$out=fopen('php://output','w'); fputcsv($out,['Code','Long URL','Clicks','Unique Clicks']);
$rows=$pdo->query('SELECT u.short_code,u.long_url,COUNT(c.id) clicks,COUNT(DISTINCT c.unique_hash) unique_clicks FROM urls u LEFT JOIN url_clicks c ON c.url_id=u.id GROUP BY u.id ORDER BY clicks DESC')->fetchAll();
foreach($rows as $r) fputcsv($out,$r);
fclose($out);
