<?php
declare(strict_types=1);
session_start();

if (!file_exists(__DIR__ . '/config.php')) {
    header('Location: install.php');
    exit;
}
$config = require __DIR__ . '/config.php';
$db = new PDO('sqlite:' . $config['database']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function faNumber(int|float $number): string { return strtr(number_format($number), ['0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹',','=>'٬']); }
function appStat(PDO $db, string $sql): int { return (int) $db->query($sql)->fetchColumn(); }

$pages = [
    'dashboard' => ['داشبورد', 'نمای کلی عملکرد شبکه تبلیغاتی شما'],
    'campaigns' => ['کمپین‌ها', 'مدیریت بودجه، زمان‌بندی و وضعیت کمپین‌ها'],
    'ads' => ['تبلیغات', 'تبلیغات متنی و گرافیکی فعال در شبکه'],
    'publishers' => ['ناشران و سایت‌ها', 'سایت‌های همکار و جایگاه‌های تبلیغاتی'],
    'reports' => ['گزارش‌ها', 'گزارش دقیق نمایش، کلیک و تبدیل'],
    'settings' => ['تنظیمات', 'تنظیمات عمومی سامانه و کدهای رهگیری'],
];
$page = $_GET['page'] ?? 'dashboard';
if (!isset($pages[$page])) $page = 'dashboard';
[$title, $subtitle] = $pages[$page];
$impressions = appStat($db, 'SELECT COUNT(*) FROM events WHERE event_type = "impression"');
$clicks = appStat($db, 'SELECT COUNT(*) FROM events WHERE event_type = "click"');
$conversions = appStat($db, 'SELECT COUNT(*) FROM events WHERE event_type = "conversion"');
$campaigns = $db->query('SELECT * FROM campaigns ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
$sites = $db->query('SELECT * FROM sites ORDER BY id DESC')->fetchAll(PDO::FETCH_ASSOC);
$ads = $db->query('SELECT ads.*, campaigns.name campaign_name FROM ads JOIN campaigns ON campaigns.id=ads.campaign_id ORDER BY ads.id DESC')->fetchAll(PDO::FETCH_ASSOC);
$ctr = $impressions ? round($clicks / $impressions * 100, 2) : 0;
?>
<!doctype html><html lang="fa" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?= htmlspecialchars($title) ?> | نِگارسامانه</title><link rel="stylesheet" href="assets/style.css"></head>
<body><aside class="sidebar"><a class="brand" href="index.php"><span class="brand-mark">ن</span><span>نِگارسامانه<small>مدیریت هوشمند تبلیغات</small></span></a><nav><?php foreach ($pages as $key => [$label]): ?><a class="<?= $page === $key ? 'active' : '' ?>" href="?page=<?= $key ?>"><i><?= ['dashboard'=>'▦','campaigns'=>'◎','ads'=>'▣','publishers'=>'◈','reports'=>'▤','settings'=>'⚙'][$key] ?></i><?= $label ?></a><?php endforeach ?></nav><div class="help"><b>نیاز به راهنمایی دارید؟</b><span>راهنمای راه‌اندازی و مستندات</span><a href="#">مشاهده راهنما ←</a></div></aside>
<main><header><button class="menu">☰</button><div class="breadcrumbs">نِگارسامانه <span>/</span> <?= $title ?></div><div class="header-actions"><button class="bell">♧<em></em></button><div class="user"><span class="avatar">م</span><span>مدیر سیستم<small>مدیر کل</small></span><b>⌄</b></div></div></header>
<section class="content"><div class="page-title"><div><h1><?= $title ?></h1><p><?= $subtitle ?></p></div><?php if($page === 'dashboard'): ?><button class="primary">＋ ایجاد کمپین جدید</button><?php endif ?></div>
<?php if ($page === 'dashboard'): ?>
<div class="date-filter">امروز، ۲۰ شهریور ۱۴۰۵ <span>⌄</span></div><div class="metrics"><article><div class="metric-icon blue">◉</div><div><span>نمایش تبلیغات</span><strong><?= faNumber($impressions) ?></strong><small class="up">↑ ۱۲٪ نسبت به دیروز</small></div></article><article><div class="metric-icon purple">↗</div><div><span>کلیک‌ها</span><strong><?= faNumber($clicks) ?></strong><small class="up">↑ ۸٫۴٪ نسبت به دیروز</small></div></article><article><div class="metric-icon orange">◌</div><div><span>نرخ کلیک (CTR)</span><strong><?= strtr((string)$ctr,['0'=>'۰','1'=>'۱','2'=>'۲','3'=>'۳','4'=>'۴','5'=>'۵','6'=>'۶','7'=>'۷','8'=>'۸','9'=>'۹','.'=>'٫']) ?>٪</strong><small class="down">↓ ۱٫۲٪ نسبت به دیروز</small></div></article><article><div class="metric-icon green">✓</div><div><span>ارجاعات موفق</span><strong><?= faNumber($conversions) ?></strong><small class="up">↑ ۲۴٪ نسبت به دیروز</small></div></article></div>
<div class="grid"><section class="card chart-card"><div class="card-head"><div><h2>عملکرد تبلیغات</h2><p>نمایش و کلیک در ۷ روز گذشته</p></div><div class="legend"><span><i class="line blue-dot"></i> نمایش</span><span><i class="line purple-dot"></i> کلیک</span></div></div><div class="chart"><div class="ylabels"><span>۲۰هزار</span><span>۱۵هزار</span><span>۱۰هزار</span><span>۵هزار</span><span>۰</span></div><div class="plot"><div class="gridlines"></div><svg viewBox="0 0 700 210" preserveAspectRatio="none"><path class="area" d="M0 140 C50 105 60 123 110 94 S180 111 220 80 S300 114 335 68 S410 86 450 50 S530 102 560 68 S620 78 700 22 L700 210 L0 210Z"/><path class="blue-line" d="M0 140 C50 105 60 123 110 94 S180 111 220 80 S300 114 335 68 S410 86 450 50 S530 102 560 68 S620 78 700 22"/><path class="purple-line" d="M0 182 C60 170 80 180 130 158 S190 175 225 148 S285 166 340 128 S415 154 450 116 S520 139 560 112 S640 128 700 72"/></svg><div class="xlabels"><span>۱۴ شهریور</span><span>۱۵ شهریور</span><span>۱۶ شهریور</span><span>۱۷ شهریور</span><span>۱۸ شهریور</span><span>۱۹ شهریور</span><span>امروز</span></div></div></div></section><section class="card quick"><div class="card-head"><div><h2>دسترسی سریع</h2><p>مدیریت سریع بخش‌های اصلی</p></div></div><a href="?page=campaigns"><i class="qicon violet">◎</i><span><b>ایجاد کمپین</b><small>تعریف کمپین تبلیغاتی جدید</small></span><b>←</b></a><a href="?page=ads"><i class="qicon orange">▣</i><span><b>افزودن تبلیغ</b><small>متنی یا گرافیکی</small></span><b>←</b></a><a href="?page=reports"><i class="qicon green">▤</i><span><b>مشاهده گزارش‌ها</b><small>تحلیل عملکرد تبلیغات</small></span><b>←</b></a></section></div>
<section class="card table-card"><div class="card-head"><div><h2>کمپین‌های فعال</h2><p>عملکرد کمپین‌ها در بازه انتخاب شده</p></div><a href="?page=campaigns">مشاهده همه ←</a></div><table><thead><tr><th>نام کمپین</th><th>تبلیغ‌دهنده</th><th>نمایش</th><th>کلیک</th><th>CTR</th><th>بودجه مصرف‌شده</th><th>وضعیت</th><th></th></tr></thead><tbody><?php foreach(array_slice($campaigns,0,4) as $c): ?><tr><td><b><?= htmlspecialchars($c['name']) ?></b></td><td><?= htmlspecialchars($c['advertiser']) ?></td><td><?= faNumber((int)$c['impressions']) ?></td><td><?= faNumber((int)$c['clicks']) ?></td><td><?= $c['impressions'] ? round($c['clicks']/$c['impressions']*100,1) : 0 ?>٪</td><td><?= faNumber((int)$c['spent']) ?> تومان</td><td><span class="status">● فعال</span></td><td>•••</td></tr><?php endforeach ?></tbody></table></section>
<?php else: ?><section class="card empty-page"><div class="empty-icon"><?= $page === 'reports' ? '▤' : ($page === 'settings' ? '⚙' : '◈') ?></div><h2><?= $title ?></h2><p><?= $subtitle ?>. این بخش برای اتصال به عملیات مدیریتی آماده است.</p><?php if($page === 'campaigns'): ?><div class="simple-table"><h3>کمپین‌های ثبت‌شده</h3><?php foreach($campaigns as $c): ?><div><b><?= htmlspecialchars($c['name']) ?></b><span><?= htmlspecialchars($c['advertiser']) ?> · <?= faNumber((int)$c['impressions']) ?> نمایش</span><em>فعال</em></div><?php endforeach ?></div><?php elseif($page === 'ads'): ?><div class="simple-table"><h3>تبلیغات شبکه</h3><?php foreach($ads as $ad): ?><div><b><?= htmlspecialchars($ad['title']) ?></b><span><?= htmlspecialchars($ad['campaign_name']) ?> · <?= $ad['type'] === 'banner' ? 'گرافیکی' : 'متنی' ?></span><em>فعال</em></div><?php endforeach ?></div><?php elseif($page === 'publishers'): ?><div class="simple-table"><h3>سایت‌های همکار</h3><?php foreach($sites as $site): ?><div><b><?= htmlspecialchars($site['name']) ?></b><span><?= htmlspecialchars($site['domain']) ?> · <?= faNumber((int)$site['impressions']) ?> نمایش</span><em>تأییدشده</em></div><?php endforeach ?></div><?php endif ?></section><?php endif ?></section></main></body></html>
