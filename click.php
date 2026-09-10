<?php
declare(strict_types=1);
$config = require __DIR__ . '/config.php'; $db = new PDO('sqlite:' . $config['database']);
$id = (int)($_GET['ad'] ?? 0); $ad = $db->prepare('SELECT * FROM ads WHERE id=?'); $ad->execute([$id]); $row=$ad->fetch(PDO::FETCH_ASSOC);
if (!$row) { http_response_code(404); exit('تبلیغ یافت نشد.'); }
$db->prepare("INSERT INTO events(ad_id,event_type,referer) VALUES (?, 'click', ?)")->execute([$id, $_SERVER['HTTP_REFERER'] ?? '']);
$db->prepare('UPDATE campaigns SET clicks=clicks+1 WHERE id=?')->execute([$row['campaign_id']]); header('Location: '.$row['destination_url']);
