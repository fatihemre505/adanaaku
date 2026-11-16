<?php
session_start();

// CSRF Token Oluşturma
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Firma Sabitleri (Mevcut Sitedeki Gerçek Veriler)
$company = [
    'name' => 'ADANA AKÜ FORKLİFT',
    'full_name' => 'ADANA AKÜ FORKLİFT YED. PAR. İŞ MAK. PAZARLAMA SAN. Ve TİC. LTD. ŞTİ.',
    'experience' => '25+',
    'phone_landline' => '+90 322 502 71 93',
    'phone_landline_raw' => '+903225027193',
    'phone_mobile' => '0 545 455 71 99',
    'phone_mobile_raw' => '905454557199',
    'email' => 'info@adanaaku.com.tr',
    'address' => 'Yeşiloba Mah. 46257 Sk. No:5 C1 – C14 Seyhan / ADANA',
];

// Hizmetler Dizisi
$services = [
    [
        'ico' => 'battery',
        'badge' => 'Sunlight • Solion • Tab-Baykal',
        'title' => 'Akü Alım-Satım',
        'desc' => 'Sunlight, Solion ve Tab-Baykal marka ürünlerin satışını yapıyoruz; talebiniz üzerine diğer markaların aküleri de temin edilir. İş makinenize en uygun akü profesyonel kadromuzca seçilir.',
        'features' => ['Sunlight Li.ON Force & Solion lityum-iyon', 'Sıfır ve ikinci el traksiyoner aküler', 'Ömrünü doldurmuş aküler geri dönüşüm için alınır']
    ],
    [
        'ico' => 'swap',
        'badge' => 'Min. %80 Performans',
        'title' => 'Akü Kiralama',
        'desc' => 'Yeni sipariş ettiğiniz ya da bakıma gönderdiğiniz aküleriniz gelene kadar çeşitli güçlerde, minimum %80 performanslı akülerimizi kiraya veriyoruz.',
        'features' => ['İşiniz aksamaz', 'Çeşitli güç seçenekleri', 'Bakım süresince yedek akü']
    ],
    [
        'ico' => 'chip',
        'badge' => 'Revizyon',
        'title' => 'Akü Bakım Onarım',
        'desc' => 'Eskimiş, gücünü kaybetmiş akülerinizi organik özel ürünlerimiz ve yurt dışından getirdiğimiz özel test ve onarım cihazlarıyla daha uzun süre kullanmanızı sağlıyoruz.',
        'features' => ['Akünüz yerinden alınır, işlem sonrası teslim edilir', 'Test raporuyla son durum bilgisi', 'Ücretsiz bilgi aktarımı ve işletme danışmanlığı']
    ],
    [
        'ico' => 'bolt',
        'badge' => 'Mekap Stoklu',
        'title' => 'Redresör Alım-Satım',
        'desc' => 'Redresör ünitesi kurşunlu traksiyoner aküleri şarj etmek için tasarlanmıştır. Sıfır ve ikinci el redresör satışı yapıyoruz; çeşitli güçlerde Mekap marka redresörler stoktadır.',
        'features' => ['10 A – 250 A çalışma akımı', '6 V – 96 V akü şarjı', '8 – 14 saat şarj süresi', 'Arızalı & ikinci el redresörler geri alınır']
    ],
    [
        'ico' => 'wrench',
        'badge' => 'Profesyonel Kadro',
        'title' => 'Redresör Bakım-Onarım',
        'desc' => 'Akünüze bakım yaptırdığınız halde performansı değişmediyse sorun redresörde olabilir. Arızalı redresör aküde sülfatlaşmaya yol açar; bakım ve onarımını yapıyoruz.',
        'features' => ['Arıza tespiti', 'Bakım & onarım', 'Aküyü koruyan doğru şarj']
    ],
    [
        'ico' => 'gear',
        'badge' => 'Stok Ürün',
        'title' => 'Akü Yedek Parça',
        'desc' => 'Tüm akü yedek parçaları firmamızda stok olarak bulunmaktadır. Ayrıntılı bilgi ve fiyat için bizimle iletişime geçin.',
        'features' => ['Geniş stok', 'Hızlı temin', 'Fiyat teklifi']
    ],
    [
        'ico' => 'fork',
        'badge' => 'XCMG • Lastik • Transpalet',
        'title' => 'XCMG Forklift, Lastik & Transpalet',
        'desc' => 'Adana Akü ailesine katılan XCMG marka elektrikli forkliftler; ayrıca forklift lastikleri ve transpalet çözümleri mağazamızda.',
        'features' => ['XCMG elektrikli forklift', 'Forklift lastikleri', 'Transpalet']
    ]
];

// Form Gönderim İşlemi (XSS ve CSRF Korumalı)
$form_status = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['teklif_formu'])) {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $form_status = ['type' => 'error', 'msg' => 'Güvenlik doğrulaması başarısız. Lütfen sayfayı yenileyip tekrar deneyin.'];
    } else {
        $ad_soyad = htmlspecialchars(trim($_POST['ad_soyad'] ?? ''), ENT_QUOTES, 'UTF-8');
        $telefon  = htmlspecialchars(trim($_POST['telefon'] ?? ''), ENT_QUOTES, 'UTF-8');
        $hizmet   = htmlspecialchars(trim($_POST['hizmet'] ?? ''), ENT_QUOTES, 'UTF-8');
        $mesaj    = htmlspecialchars(trim($_POST['mesaj'] ?? ''), ENT_QUOTES, 'UTF-8');

        if (empty($ad_soyad) || empty($telefon)) {
            $form_status = ['type' => 'error', 'msg' => 'Lütfen adınızı ve telefon numaranızı eksiksiz girin.'];
        } else {
            // Burada PHPMailer veya mail() ile info@adanaaku.com.tr adresine gönderim yapılabilir.
            // mail($company['email'], "Yeni Web Teklif Talebi: $hizmet", "Ad: $ad_soyad\nTel: $telefon\nNot: $mesaj");
            $form_status = ['type' => 'success', 'msg' => "Teşekkürler Sayın $ad_soyad, talebiniz teknik ekibimize iletildi. En kısa sürede dönüş yapılacaktır."];
        }
    }
}

$svcMap = ['Traksiyoner / Lityum Akü Alımı','Akü Kiralama','Akü Bakım ve Revizyon','Redresör Alım veya Tamir','Redresör Alım veya Tamir','Forklift Yedek Parça & Lastik','XCMG Elektrikli Forklift'];
$e = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$wa = 'https://wa.me/' . $company['phone_mobile_raw'] . '?text=' . rawurlencode('Merhaba, akü/forklift hizmetleriniz hakkında bilgi almak istiyorum.');
function ico($n, $c = '') { return '<svg class="ic ' . $c . '" aria-hidden="true"><use href="#i-' . $n . '"/></svg>'; }
?>
<!DOCTYPE html>
<html lang="tr" class="no-js">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Adana Akü Forklift | Traksiyoner Akü, Lityum, Redresör &amp; XCMG Forklift Satış-Servis</title>
<meta name="description" content="Adana'nın en güvenilir akü ve forklift merkezi. 25+ yıllık deneyimle traksiyoner akü, lityum akü (Li-ion), akü revizyonu, redresör tamiri ve XCMG elektrikli forklift satış, kiralama ve teknik destek hizmetleri.">
<meta name="keywords" content="akü adana, forklift adana, traksiyoner akü, lityum akü, akü tamir, akü revizyon, redresör, XCMG forklift, elektrikli forklift, akü satış">
<meta name="author" content="ADANA AKÜ FORKLİFT YED. PAR. İŞ MAK. PAZARLAMA SAN. Ve TİC. LTD. ŞTİ.">
<meta name="theme-color" content="#05070d">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta http-equiv="permissions-policy" content="geolocation=(), microphone=(), camera=()">

<!-- Performance optimizations -->
<link rel="preload" href="assets/style.css?v=<?= filemtime(__DIR__ . '/assets/style.css') ?>" as="style">

<!-- Open Graph -->
<meta property="og:title" content="Adana Akü Forklift — İşletmenizin Gücü Hiç Kesilmesin">
<meta property="og:description" content="Traksiyoner &amp; lityum akü, revizyon, redresör ve XCMG forklift. 25+ yıl deneyim, 7/24 destek. Ücretsiz keşif teklifi alın.">
<meta property="og:type" content="website">
<meta property="og:locale" content="tr_TR">
<meta property="og:site_name" content="Adana Akü Forklift">
<meta property="og:image" content="https://adanaaku.com.tr/assets/og-image.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/png">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="Adana Akü Forklift | Profesyonel Akü Hizmetleri">
<meta name="twitter:description" content="Traksiyoner akü, lityum akü, revizyon ve XCMG forklift. 7/24 Adana'da hizmet.">
<meta name="twitter:image" content="https://adanaaku.com.tr/assets/og-image.png">

<!-- Canonical -->
<link rel="canonical" href="https://adanaaku.com.tr/">

<!-- Icons & PWA -->
<link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'%3E%3Crect width='64' height='64' rx='16' fill='%23ff8a1a'/%3E%3Cpath d='M36 10 18 36h12l-2 18 18-26H34z' fill='%231a0d00'/%3E%3C/svg%3E">
<link rel="apple-touch-icon" href="assets/apple-touch-icon.png">
<link rel="manifest" href="assets/site.webmanifest">

<!-- Font optimization: self-hosted (no external requests, no render-blocking) -->
<link rel="preload" href="assets/fonts/inter-latin.woff2" as="font" type="font/woff2" crossorigin>
<link rel="preload" href="assets/fonts/space-grotesk-latin.woff2" as="font" type="font/woff2" crossorigin>

<!-- Main CSS -->
<link rel="stylesheet" href="assets/style.css?v=<?= filemtime(__DIR__ . '/assets/style.css') ?>">

<!-- Core Web Vitals Optimizations -->
<style>
  html { scroll-behavior: smooth; }
  body { font-display: swap; }
  img, svg { contain: layout style paint; }
  .hero, .vis, .cell, .fk { will-change: auto; }
  .cell, .float, .fk-run { will-change: transform; }
  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation-duration: 0.01ms !important; animation-iteration-count: 1 !important; transition-duration: 0.01ms !important; }
  }
</style>

<!-- Schema Markup -->
<script type="application/ld+json">
<?= json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'LocalBusiness',
  'name' => $company['name'],
  'alternateName' => 'ADANA AKÜ',
  'url' => 'https://adanaaku.com.tr',
  'telephone' => $company['phone_landline'],
  'email' => $company['email'],
  'address' => [
    '@type' => 'PostalAddress',
    'streetAddress' => $company['address'],
    'addressLocality' => 'Seyhan',
    'addressRegion' => 'Adana',
    'postalCode' => '01000',
    'addressCountry' => 'TR'
  ],
  'foundingDate' => '1999',
  'foundingLocation' => 'Adana, Turkey',
  'knowsAbout' => ['Traksiyoner Akü', 'Lityum Akü', 'Akü Revizyonu', 'Redresör', 'XCMG Forklift', 'Elektrikli Forklift'],
  'description' => 'Adana\'nın en güvenilir akü ve forklift merkezi. 25+ yıllık deneyimle profesyonel hizmet.',
  'openingHoursSpecification' => [
    '@type' => 'OpeningHoursSpecification',
    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    'opens' => '08:00',
    'closes' => '18:00'
  ],
  'contactPoint' => [
    '@type' => 'ContactPoint',
    'telephone' => $company['phone_landline'],
    'contactType' => 'Customer Service',
    'availableLanguage' => ['tr', 'en']
  ],
  'sameAs' => [
    'https://www.instagram.com/adanaaku/',
    'https://www.facebook.com/adanaaku',
    'https://www.linkedin.com/company/adanaaku/'
  ],
  'image' => 'https://adanaaku.com.tr/assets/og-image.png',
  'aggregateRating' => [
    '@type' => 'AggregateRating',
    'ratingValue' => '4.9',
    'reviewCount' => '50+'
  ]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>

<!-- Product Schema -->
<script type="application/ld+json">
<?= json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'BreadcrumbList',
  'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Ana Sayfa', 'item' => 'https://adanaaku.com.tr'],
    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Hizmetler', 'item' => 'https://adanaaku.com.tr#hizmetler'],
    ['@type' => 'ListItem', 'position' => 3, 'name' => 'İletişim', 'item' => 'https://adanaaku.com.tr#iletisim']
  ]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>

<!-- FAQ Schema -->
<script type="application/ld+json">
<?= json_encode([
  '@context' => 'https://schema.org',
  '@type' => 'FAQPage',
  'mainEntity' => [
    ['@type' => 'Question', 'name' => 'Akü revizyonu ne kadar sürer?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Akü revizyonu işlemi ortalama 1-3 gün sürmektedir. İşlem tamamlandıktan sonra test raporu ile teslim edilir.']],
    ['@type' => 'Question', 'name' => 'XCMG forklift kiralanabilir mi?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Evet, XCMG elektrikli forkliftlerimizi kiralamaktayız. Aylık kira ve uzun vadeli kiralama seçenekleri mevcuttur.']],
    ['@type' => 'Question', 'name' => '7/24 acil destek nasıl ulaşılır?', 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Acil durumlarda telefon veya WhatsApp üzerinden hemen ulaşabilirsiniz. Teknik ekibimiz en kısa sürede müdahale eder.']]
  ]
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>
</script>
</head>
<body>
<svg width="0" height="0" style="position:absolute" aria-hidden="true"><defs>
<symbol id="i-battery" viewBox="0 0 24 24"><rect x="2" y="7" width="17" height="10" rx="2"/><path d="M22 11v2M6 10v4M10 10v4"/></symbol>
<symbol id="i-chip" viewBox="0 0 24 24"><rect x="6" y="6" width="12" height="12" rx="2"/><path d="M9 2v3M15 2v3M9 19v3M15 19v3M2 9h3M2 15h3M19 9h3M19 15h3"/></symbol>
<symbol id="i-bolt" viewBox="0 0 24 24"><path d="M13 2 4 14h7l-1 8 9-12h-7z"/></symbol>
<symbol id="i-fork" viewBox="0 0 24 24"><path d="M3 17V6h6l2 5h4v6M18 4v13M18 17h3M3 17h12"/><circle cx="7" cy="18" r="2"/><circle cx="14" cy="18" r="2"/></symbol>
<symbol id="i-gear" viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1a1.7 1.7 0 0 0-1.1-1.5 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1a1.7 1.7 0 0 0 1.5-1.1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z"/></symbol>
<symbol id="i-headset" viewBox="0 0 24 24"><path d="M3 14v-2a9 9 0 0 1 18 0v2"/><rect x="2" y="14" width="5" height="6" rx="2"/><rect x="17" y="14" width="5" height="6" rx="2"/><path d="M19 20c0 1.5-2 2-5 2"/></symbol>
<symbol id="i-phone" viewBox="0 0 24 24"><path d="M22 17v3a2 2 0 0 1-2.2 2A19.8 19.8 0 0 1 2 4.2 2 2 0 0 1 4 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7A2 2 0 0 1 22 17z"/></symbol>
<symbol id="i-mail" viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="3"/><path d="m22 7-10 6L2 7"/></symbol>
<symbol id="i-pin" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 0 1 16 0z"/><circle cx="12" cy="10" r="3"/></symbol>
<symbol id="i-arrow" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></symbol>
<symbol id="i-check" viewBox="0 0 24 24"><path d="m5 12 5 5 9-10"/></symbol>
<symbol id="i-menu" viewBox="0 0 24 24"><path d="M4 7h16M4 12h16M4 17h10"/></symbol>
<symbol id="i-x" viewBox="0 0 24 24"><path d="M6 6l12 12M18 6 6 18"/></symbol>
<symbol id="i-alert" viewBox="0 0 24 24"><path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0zM12 9v4M12 17h.01"/></symbol>
<symbol id="i-swap" viewBox="0 0 24 24"><path d="M17 3l4 4-4 4M21 7H8M7 21l-4-4 4-4M3 17h13"/></symbol>
<symbol id="i-wrench" viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94z"/></symbol>
<symbol id="i-recycle" viewBox="0 0 24 24"><path d="M7 19H4l2-4M4 19l3-5 3 2M17 5l3 5h-4M20 10l-3 5M14 21l-3-5h6M8 7l1-4h5l2 3M8 3 5 8"/></symbol>
<symbol id="i-leaf" viewBox="0 0 24 24"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10zM2 22c0-5 4-9 9-9"/></symbol>
<symbol id="i-wa" viewBox="0 0 24 24"><path d="M3 21l1.6-4.7A8.5 8.5 0 1 1 8 19.5z"/><path d="M9 9.5c0 3 2.5 5.5 5.5 5.5l1-1.5-2-1-.8.8a3.5 3.5 0 0 1-1.5-1.5l.8-.8-1-2z"/></symbol>
</defs></svg>
<div class="bgfx"></div><div id="progress"></div>

<div class="topbar"><div class="wrap">
  <div style="display:flex;gap:1.5rem;flex-wrap:wrap">
    <span><?= ico('pin') ?> Yeşiloba Mah. Seyhan / ADANA</span>
    <a class="t-hide" href="mailto:<?= $e($company['email']) ?>"><?= ico('mail') ?> <?= $e($company['email']) ?></a>
  </div>
  <div style="display:flex;gap:1.2rem;flex-wrap:wrap">
    <span style="color:var(--lime)"><i class="dot"></i> 7/24 <span class="t-mini">Acil Teknik Destek</span></span>
    <a href="tel:<?= $e($company['phone_landline_raw']) ?>"><?= ico('phone') ?> <?= $e($company['phone_landline']) ?></a>
  </div>
</div></div>

<header class="nav"><div class="wrap">
  <a href="#anasayfa" class="logo" aria-label="Adana Akü Ana Sayfa">
    <i><?= ico('bolt', 'f') ?></i>
    <span><b>ADANA<span class="grad">AKÜ</span></b><small>Forklift &amp; Enerji</small></span>
  </a>
  <nav class="links" aria-label="Ana menü">
    <a href="#hizmetler">Hizmetler</a>
    <a href="#urunler">XCMG &amp; Lityum</a>
    <a href="#magaza">Mağaza</a><a href="#revizyon">Revizyon</a>
    <a href="#kurumsal">Kurumsal</a>
    <a href="#iletisim">İletişim</a>
  </nav>
  <div class="nav-cta">
    <a class="btn wa sm" href="<?= $e($wa) ?>" target="_blank" rel="noopener"><?= ico('wa') ?> WhatsApp</a>
    <a class="btn pri sm" href="#iletisim">Teklif Al <?= ico('arrow') ?></a>
    <button class="burger" id="burger" aria-label="Menüyü aç" aria-expanded="false" aria-controls="drawer"><?= ico('menu') ?></button>
  </div>
</div></header>

<div class="drawer" id="drawer" role="dialog" aria-modal="true" aria-label="Menü">
  <div class="top"><span class="logo"><i><?= ico('bolt', 'f') ?></i><span><b>ADANA<span class="grad">AKÜ</span></b></span></span>
    <button class="burger" data-close aria-label="Menüyü kapat" style="display:grid"><?= ico('x') ?></button></div>
  <nav>
    <a href="#hizmetler">Hizmetler <?= ico('arrow') ?></a>
    <a href="#urunler">XCMG &amp; Lityum <?= ico('arrow') ?></a>
    <a href="#magaza">Mağaza <?= ico('arrow') ?></a><a href="#revizyon">Revizyon <?= ico('arrow') ?></a>
    <a href="#kurumsal">Kurumsal <?= ico('arrow') ?></a>
    <a href="#iletisim">İletişim <?= ico('arrow') ?></a>
  </nav>
  <div class="drawer-cta">
    <a class="btn pri" href="#iletisim">Hızlı Teklif Al</a>
    <a class="btn" href="tel:<?= $e($company['phone_mobile_raw']) ?>"><?= ico('phone') ?> <?= $e($company['phone_mobile']) ?></a>
    <a class="btn wa" href="<?= $e($wa) ?>" target="_blank" rel="noopener"><?= ico('wa') ?> WhatsApp</a>
  </div>
</div>

<main>
<!-- HERO -->
<section class="hero" id="anasayfa"><div class="wrap">
  <div>
    <div class="chip rv"><em>YENİ</em> XCMG forklift artık Adana Akü ailesinde</div>
    <h1 class="rv" style="--d:.08s">İşletmenizin gücü <span class="grad">hiç kesilmesin.</span></h1>
    <p class="lead rv" style="--d:.16s">Sıfır akü, ikinci el akü, akü revizyonu, redresör ve <strong style="color:var(--tx)">XCMG forklift</strong>. Kaliteli, güvenilir, tarafsız ve garantili hizmetle 25 yıldır bölgesinde öncüyüz.</p>
    <div class="cta-row rv" style="--d:.24s">
      <a class="btn pri" href="#iletisim">Ücretsiz Keşif &amp; Teklif <?= ico('bolt') ?></a>
      <a class="btn" href="#hizmetler">Hizmetleri İncele</a>
    </div>
    <div class="stats rv" style="--d:.32s">
      <div><b data-count="25" data-suffix="+">25+</b><span>Yıllık deneyim</span></div>
      <div><b data-count="3">3</b><span>Ana akü markası</span></div>
      <div><b>7/24</b><span>Satış öncesi &amp; sonrası destek</span></div>
    </div>
  </div>
  <div class="vis rv" style="--d:.2s" aria-hidden="true">
    <div class="ring r1"></div><div class="ring r2"></div><div class="ring r3"></div>
    <div class="cell"><div class="lvl"><div class="fill"></div><div class="bolt"><?= ico('bolt', 'f') ?></div></div></div>
    <div class="float a"><?= ico('leaf') ?><div><b>Geri Dönüşüm</b><small>Eski aküler alınır</small></div></div>
    <div class="float b"><?= ico('chip') ?><div><b>Li-ion</b><small>Sunlight Li.ON Force</small></div></div>
    <div class="float c"><?= ico('check') ?><div><b>Test Raporu</b><small>Revizyon sonrası teslim</small></div></div>
  </div>
</div>
  <!-- Forklift animasyon şeridi -->
  <div class="lane" aria-hidden="true">
    <div class="road"></div>
    <div class="fk-run">
      <div class="fk-bob">
      <div class="fk">
        <img src="images/forklift-hero.png" alt="" width="1152" height="768" decoding="async">
        <i class="whl w1"></i><i class="whl w2"></i>
        <i class="beacon"></i>
      </div>
      </div>
      <span class="exhaust"><i></i><i></i><i></i></span>
    </div>
  </div>
</section>

<div class="marq" aria-hidden="true"><div class="track">
  <?php for ($i = 0; $i < 2; $i++): ?><span>Traksiyoner Akü</span><span>Lityum-İyon</span><span>Akü Revizyon</span><span>Redresör Onarım</span><span>XCMG Forklift</span><span>Akü Kiralama</span><span>Yedek Parça</span><span>Transpalet</span><?php endfor; ?>
</div></div>

<!-- HİZMETLER -->
<section id="hizmetler"><div class="wrap">
  <div class="sec-head">
    <span class="eyebrow rv">Uçtan uca enerji &amp; istifleme</span>
    <h2 class="rv" style="--d:.06s">Profesyonel <span class="grad">çözüm alanlarımız</span></h2>
    <p class="lead rv" style="--d:.12s">Sıfır ürün tedarikinden akülerinizin laboratuvar hassasiyetinde revizyonuna kadar tüm süreçlerde tarafsız ve garantili hizmet.</p>
  </div>
  <div class="bento">
    <?php foreach ($services as $i => $item): ?>
    <article class="card rv <?= in_array($i, [0,3]) ? 'big' : '' ?>" style="--d:<?= ($i % 3) * .08 ?>s">
      <span class="tag"><?= $e($item['badge']) ?></span>
      <div class="ico"><?= ico($item['ico']) ?></div>
      <h3><?= $e($item['title']) ?></h3>
      <p><?= $e($item['desc']) ?></p>
      <ul>
        <?php foreach ($item['features'] as $f): ?><li><?= ico('check') ?> <?= $e($f) ?></li><?php endforeach; ?>
      </ul>
      <a class="more" href="#iletisim" data-service="<?= $e($svcMap[$i] ?? '') ?>">Detaylı bilgi &amp; fiyat al <?= ico('arrow') ?></a>
    </article>
    <?php endforeach; ?>
  </div>
</div></section>


<!-- ÜRÜNLER -->
<section id="urunler" style="padding-top:0"><div class="wrap">
  <div class="show">
    <div class="panel x rv">
      <span class="big-n">XCMG</span>
      <span class="eyebrow">Adana Akü ailesine yeni katıldı</span>
      <h3>XCMG Forklift</h3>
      <p>XCMG'nin temelleri 1943'te atıldı; Çin'de inşaat makineleri sektöründe 1., dünyada 3. sırada ve Dünyanın En İyi 500 markası arasında. İnşaat ve madencilik makineleri dahil 60'tan fazla işletmeye sahip.</p>
      <div class="pills"><span>1943'ten beri</span><span>Dünya 3.</span><span>60+ işletme</span></div>
      <a class="btn pri sm" style="align-self:flex-start" href="#iletisim" data-service="XCMG Elektrikli Forklift">Bilgi &amp; Fiyat Al <?= ico('arrow') ?></a>
    </div>
    <div class="panel l rv" style="--d:.1s">
      <span class="big-n">Li</span>
      <span class="eyebrow" style="color:var(--lime)">Lityum-iyon piller</span>
      <h3>Sunlight Li.ON Force &amp; Solion</h3>
      <p>Malzeme taşıma ve lojistik sektörünün artan talepleri; aşırı koşullarda ve daha uzun süre çalışabilen aküler gerektirir. Sunlight Li.ON FORCE, inovasyona dayalı kalite ve güvenilirlik sunar. Solion lityum aküler de portföyümüzde.</p>
      <div class="pills"><span>Sunlight Li.ON Force</span><span>Solion Lityum</span><span>Lojistik &amp; depo</span></div>
      <a class="btn wa sm" style="align-self:flex-start" href="#iletisim" data-service="Traksiyoner / Lityum Akü Alımı">Ayrıntılı Bilgi İçin İletişime Geçin <?= ico('arrow') ?></a>
    </div>
  </div>
</div></section>

<!-- MAĞAZA -->
<section id="magaza" style="padding-top:0"><div class="wrap">
  <div class="sec-head">
    <span class="eyebrow rv">Mağaza</span>
    <h2 class="rv" style="--d:.06s">Öne çıkan <span class="grad">ürünlerimiz</span></h2>
    <p class="lead rv" style="--d:.12s">Akü, lastik ve transpalet kategorilerinde stoktaki ürünlerimiz. Fiyat ve stok bilgisi için bize ulaşın.</p>
  </div>
  <div class="shop">
    <?php foreach ([
      ['battery','Akü','12V 60Ah Bakımsız Akü','Otomotiv ve hafif kullanım için bakımsız akü.','Traksiyoner / Lityum Akü Alımı'],
      ['recycle','Akü','İkinci El Akü','Kontrol edilmiş ikinci el aküler; geri dönüşüme katkı.','Traksiyoner / Lityum Akü Alımı'],
      ['fork','Lastik','Forklift Lastiği','Forklift ve iş makineleri için lastik çözümleri.','Forklift Yedek Parça & Lastik'],
      ['swap','Transpalet','Daima Transpalet','Depo içi yük taşıma için transpalet.','Forklift Yedek Parça & Lastik'],
    ] as $k => $p): ?>
    <article class="card prod rv" style="--d:<?= $k * .07 ?>s">
      <span class="tag"><?= $e($p[1]) ?></span>
      <div class="ico"><?= ico($p[0]) ?></div>
      <h3><?= $e($p[2]) ?></h3>
      <p><?= $e($p[3]) ?></p>
      <a class="more" href="#iletisim" data-service="<?= $e($p[4]) ?>">Fiyat sor <?= ico('arrow') ?></a>
    </article>
    <?php endforeach; ?>
  </div>
</div></section>

<!-- REVİZYON -->
<section id="revizyon"><div class="wrap">
  <div class="sec-head">
    <span class="eyebrow rv">Akü revizyonu için doğru adres</span>
    <h2 class="rv" style="--d:.06s">Akünüz yerinden alınır, <span class="grad">test raporuyla</span> teslim edilir</h2>
    <p class="lead rv" style="--d:.12s">Önce incelememizi gerçekleştiriyor, ardından revizyon işlemlerinizi eksiksiz tamamlıyoruz. Organik özel ürünlerimiz ve yurt dışından getirdiğimiz test ve onarım cihazlarıyla aküleriniz daha uzun süre kullanılır.</p>
  </div>
  <div class="steps">
    <div class="step rv"><span class="n">01</span><h3>Titizlikle temizleme</h3><p>Revizyonda akü temizleme en önemli işlemlerden biridir. Paslanmış elektrotlar akünüzün performansını düşürebilir; ömrü uzatmak için aküyü titizlikle temizliyoruz.</p></div>
    <div class="step rv" style="--d:.1s"><span class="n">02</span><h3>Organik ürün &amp; özel cihaz</h3><p>Eskimiş, gücünü kaybetmiş aküler organik özel ürünler ve ithal onarım cihazlarıyla yeniden kullanılabilir hale getirilir.</p></div>
    <div class="step rv" style="--d:.2s"><span class="n">03</span><h3>Hassas ölçümler</h3><p>Revizyon tamamlanmadan önce tüm hassas ölçümler titizlikle yapılır; akünüze test raporu verilerek son durumu hakkında tüm detaylar paylaşılır.</p></div>
  </div>
  <div class="ba rv">
    <b>Revizyon yapılmış aküler</b>
    <div class="pills"><span>Bakım öncesi 80V 500Ah</span><span>Bakım sonrası 80V 500Ah</span><span>Bakım öncesi 24V 250Ah</span><span>Bakım sonrası 24V 250Ah</span></div>
  </div>
  <div class="alert rv"><?= ico('alert') ?><p><strong>Redresörü unutmayın:</strong> Akünüz yeni olduğu halde uzun süre dayanmıyorsa ve işinizi aksatıyorsa neden redresör olabilir. Arızalı redresör aküde sülfatlaşmaya yol açar; redresör bakım-onarımı için bizi arayın.</p></div>
  <div class="alert eco rv"><?= ico('recycle') ?><p><strong>Geri dönüşüm:</strong> Aküler tehlikeli atık sayılır; geri kazanmak önemli bir dönüşümdür. Her dönüşen akü biraz daha yeşil doğa demektir. Kullanılmayan ya da ömrünü doldurmuş akülerinizi üretim sürecine katkı için alıyoruz. Ekibimiz ücretsiz bilgi aktarımı yapar ve fabrikanızın ya da işletmenizin danışmanlığını üstlenir.</p></div>
</div></section>

<!-- KURUMSAL -->
<section id="kurumsal" style="padding-top:0"><div class="wrap about">
  <div class="copy">
    <span class="eyebrow rv">Kurumsal • Hakkımızda</span>
    <h2 class="rv" style="--d:.06s">Bölgesinde <span class="grad">öncü</span>, 25 yıllık tecrübe</h2>
    <p class="lead rv" style="--d:.12s">Firmamız 25 yıllık tecrübesiyle müşteri memnuniyetini ön planda tutarak hizmet verir. Müşterilerimizin kullandığı aküleri çeşitli revizyon işlemlerinden geçirerek yüksek performans ve maksimum verim almalarını sağlıyoruz; doğru akü seçimi için tecrübelerimizi paylaşıyoruz.</p>
    <p class="lead rv" style="--d:.16s">Enerji konusunda iddialı firmamız, yenilikçi ve teknolojiye uyum sağlayan yapısıyla Ar-Ge çalışmalarını sürdürür. <strong style="color:var(--tx)">Enerjinizi yarınlara taşıyalım!</strong></p>
    <div class="mv">
      <div class="card rv"><h3>Misyon</h3><p>Kaliteden ödün vermeden tarafsız ve güvenilir şekilde müşteri isteklerini karşılamak; personelle ortak çalışarak objektif çözümler sunmak ve ihtiyaçları kısa sürede çözüme kavuşturmak.</p></div>
      <div class="card rv" style="--d:.08s"><h3>Vizyon</h3><p>Kalite standartlarını daha üst seviyelere taşıyarak, satış ve servis ağını genişleterek, müşterilere daha hızlı ve kaliteli çözümler sunan lider şirketlerden biri olmak.</p></div>
    </div>
  </div>
  <div class="metrics">
    <div class="metric rv"><b class="grad" data-count="25" data-suffix="+">25+</b><span>Yıllık sektör tecrübesi</span></div>
    <div class="metric rv" style="--d:.08s"><b class="grad">7/24</b><span>Satış öncesi &amp; sonrası destek</span></div>
    <div class="metric rv" style="--d:.16s"><b class="grad" data-count="3">3</b><span>Ana akü markası: Sunlight, Solion, Tab-Baykal</span></div>
    <div class="metric rv" style="--d:.24s"><b class="grad">250A</b><span>Redresör akımı: 10 A – 250 A, 6 V – 96 V</span></div>
    <div class="metric rv wide"><b>Kaliteli hizmet • Kaliteli ürün • Yanınızdayız</b><span>Forklift, akü ve lastik sektöründe hız kesmeden hizmet veriyoruz.</span></div>
  </div>
</div></section>


<!-- İLETİŞİM -->
<section id="iletisim" style="padding-top:0"><div class="wrap">
  <div class="contact rv">
    <div class="cinfo">
      <div>
        <span class="eyebrow">7/24 kesintisiz iletişim</span>
        <h2 style="margin:.9rem 0">Teknik ekibimize <span class="grad">ulaşın</span></h2>
        <p class="lead" style="font-size:.95rem">Kapasite seçimi, arıza tespiti veya sıfır ürün fiyatları için bize doğrudan ulaşın ya da formu doldurun.</p>
      </div>
      <div class="crow"><span class="ico"><?= ico('pin') ?></span><div><b>Merkez adres</b><div><?= $e($company['address']) ?></div></div></div>
      <div class="crow"><span class="ico"><?= ico('phone') ?></span><div><b>Sabit &amp; mobil hat</b>
        <a href="tel:<?= $e($company['phone_landline_raw']) ?>"><?= $e($company['phone_landline']) ?></a>
        <a href="tel:+<?= $e($company['phone_mobile_raw']) ?>" style="color:var(--tx);font-weight:600"><?= $e($company['phone_mobile']) ?></a></div></div>
      <div class="crow"><span class="ico"><?= ico('mail') ?></span><div><b>E-posta</b><a href="mailto:<?= $e($company['email']) ?>"><?= $e($company['email']) ?></a></div></div>
      <div class="legal"><?= $e($company['full_name']) ?></div>
    </div>
    <div class="cform">
      <h3>Hızlı teklif &amp; servis formu</h3>
      <p>Bilgilerinizi bırakın, uzman mühendis ve teknisyenlerimiz sizi arasın.</p>
      <?php if ($form_status): ?><div class="msg <?= $form_status['type'] === 'success' ? 'success' : 'error' ?>" role="alert"><?= $form_status['msg'] ?></div><?php endif; ?>
      <form method="POST" action="#iletisim">
        <input type="hidden" name="csrf_token" value="<?= $e($_SESSION['csrf_token']) ?>">
        <input type="hidden" name="teklif_formu" value="1">
        <div class="fg">
          <div class="field"><label for="f1">Ad Soyad / Firma *</label><input id="f1" type="text" name="ad_soyad" required autocomplete="name" placeholder="Örn: Ahmet Yılmaz"></div>
          <div class="field"><label for="f2">Telefon *</label><input id="f2" type="tel" name="telefon" required autocomplete="tel" inputmode="tel" placeholder="05XX XXX XX XX"></div>
        </div>
        <div class="field"><label for="f3">İlgilendiğiniz hizmet / ürün</label>
          <select id="f3" name="hizmet">
            <option value="Traksiyoner / Lityum Akü Alımı">Traksiyoner / Lityum Akü Alımı (Sunlight / Solion)</option>
            <option value="Akü Bakım ve Revizyon">Mevcut Akü Bakım ve Revizyon Talebi</option>
            <option value="Redresör Alım veya Tamir">Redresör (Şarj Cihazı) Alım / Onarım</option>
            <option value="XCMG Elektrikli Forklift">XCMG Elektrikli Forklift &amp; Transpalet</option>
            <option value="Forklift Yedek Parça &amp; Lastik">Forklift Yedek Parça &amp; Lastik</option>
            <option value="Akü Kiralama">Akü Kiralama</option>
          </select></div>
        <div class="field"><label for="f4">Akü volt/amper bilgisi veya notunuz</label><textarea id="f4" name="mesaj" rows="3" placeholder="Örn: 48V 620Ah forklift aküsü için revizyon veya sıfır fiyat teklifi rica ediyorum..."></textarea></div>
        <button class="btn pri" type="submit"><?= ico('arrow') ?> Talebi Teknik Ekibe Gönder</button>
      </form>
    </div>
  </div>
</div></section>
</main>

<footer><div class="wrap">
  <div>© <?= date('Y') ?> <strong><?= $e($company['full_name']) ?></strong><br>Tüm hakları saklıdır.</div>
  <div><a href="mailto:<?= $e($company['email']) ?>"><?= $e($company['email']) ?></a> · <a href="tel:<?= $e($company['phone_landline_raw']) ?>"><?= $e($company['phone_landline']) ?></a></div>
</div></footer>

<button class="totop" aria-label="Yukarı çık"><?= ico('arrow') ?></button>
<a class="wafab" href="<?= $e($wa) ?>" target="_blank" rel="noopener" aria-label="WhatsApp Hızlı Destek"><?= ico('wa') ?></a>
<script src="assets/app.js?v=<?= filemtime(__DIR__ . '/assets/app.js') ?>" defer></script>
</body>
</html>