<?php
declare(strict_types=1);
if (file_exists(__DIR__.'/config.php')) { $config=require __DIR__.'/config.php'; $db=new PDO('sqlite:'.$config['database']); $id=(int)($_GET['ad']??0); $db->prepare("INSERT INTO events(ad_id,event_type,referer) VALUES (?, 'impression', ?)")->execute([$id,$_SERVER['HTTP_REFERER']??'']); if($id) $db->prepare('UPDATE campaigns SET impressions=impressions+1 WHERE id=(SELECT campaign_id FROM ads WHERE id=?)')->execute([$id]); }
header('Content-Type: image/gif'); echo base64_decode('R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==');
