<?php
require_once 'auth.php';
$daily = $pdo->query('SELECT DATE(clicked_at) d, COUNT(*) c FROM url_clicks GROUP BY DATE(clicked_at) ORDER BY d DESC LIMIT 14')->fetchAll();
$brows = $pdo->query('SELECT browser b, COUNT(*) c FROM url_clicks GROUP BY browser ORDER BY c DESC LIMIT 10')->fetchAll();
$dev = $pdo->query('SELECT device d, COUNT(*) c FROM url_clicks GROUP BY device')->fetchAll();
$cnt = $pdo->query('SELECT country ctry, COUNT(*) c FROM url_clicks GROUP BY country ORDER BY c DESC LIMIT 10')->fetchAll();
?><!doctype html><html><head><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><script src="https://cdn.jsdelivr.net/npm/chart.js"></script></head><body><div class="container py-4"><h3>Analytics Charts</h3><canvas id="daily"></canvas><canvas id="browser"></canvas><canvas id="device"></canvas><canvas id="country"></canvas></div>
<script>
function c(id,labels,data,label){new Chart(document.getElementById(id),{type:'bar',data:{labels:labels,datasets:[{label:label,data:data}]}})}
c('daily', <?=json_encode(array_column($daily,'d'))?>, <?=json_encode(array_column($daily,'c'))?>, 'Daily Clicks');
c('browser', <?=json_encode(array_column($brows,'b'))?>, <?=json_encode(array_column($brows,'c'))?>, 'Browser');
c('device', <?=json_encode(array_column($dev,'d'))?>, <?=json_encode(array_column($dev,'c'))?>, 'Device');
c('country', <?=json_encode(array_column($cnt,'ctry'))?>, <?=json_encode(array_column($cnt,'c'))?>, 'Country');
</script></body></html>
