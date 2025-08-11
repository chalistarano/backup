<?php
// Aktifkan error reporting untuk debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define base URL for the site
$base_url = 'https://opac.handayani.ac.id/'; // Ganti dengan URL situs utama Anda

// Fungsi untuk membuat file index.php berdasarkan template
function buat_index_file($nama_folder, $template_konten, $base_url) {
    // Ganti {{ BRAND }} dengan nama folder dalam huruf besar (capslock)
    $konten_index = str_replace('{{ BRAND }}', strtoupper(htmlspecialchars($nama_folder)), $template_konten);

    // Ganti {{ URL }} dengan URL lengkap (base URL + nama folder)
    $url_folder = $base_url . $nama_folder . '/';
    $konten_index = str_replace('{{ URL }}', htmlspecialchars($url_folder), $konten_index);

    // Menulis konten ke file index.php
    file_put_contents($nama_folder . '/index.php', $konten_index);
}

// Baca daftar folder dari file brand.txt
$daftar_folder = file('brand.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

// Periksa jika file brand.txt berhasil dibaca
if ($daftar_folder === false) {
    die('Gagal membaca file brand.txt');
}

// Baca konten dari file template.html
$template_file = 'template.html';
if (!file_exists($template_file)) {
    die('File template.html tidak ditemukan.');
}

$template_konten = file_get_contents($template_file);
if ($template_konten === false) {
    die('Gagal membaca konten dari file template.html.');
}

// Membuat folder dan file index.php untuk setiap nama folder dalam daftar
foreach ($daftar_folder as $folder) {
    // Ubah nama folder menjadi huruf kecil
    $folder = strtolower(trim($folder));

    // Buat folder jika belum ada
    if (!file_exists($folder)) {
        mkdir($folder, 0755, true);
    }

    // Buat file index.php di dalam folder tersebut dengan nama folder dalam huruf besar dan URL lengkap
    buat_index_file($folder, $template_konten, $base_url);
}

// Membuat sitemap.xml
$sitemap_content = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
$sitemap_content .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

// Tambahkan setiap URL yang telah dibuat
foreach ($daftar_folder as $folder) {
    // Ubah nama folder menjadi huruf kecil untuk keperluan URL
    $folder = strtolower(trim($folder));
    
    $sitemap_content .= '    <url>' . "\n";
    $sitemap_content .= '        <loc>' . htmlspecialchars($base_url . $folder . '/') . '</loc>' . "\n";
    $sitemap_content .= '    </url>' . "\n";
}

$sitemap_content .= '</urlset>';

// Menyimpan konten ke file sitemap.xml
file_put_contents('sitemap.xml', $sitemap_content);

// Membuat robots.txt
$robots_content = "User-agent: *\n";
$robots_content .= "Allow: /\n";  // Mengizinkan semua halaman di-crawl
$robots_content .= "Sitemap: " . $base_url . "sitemap.xml\n";  // Menyertakan URL sitemap

// Menyimpan konten ke file robots.txt
file_put_contents('robots.txt', $robots_content);

echo "Folder, file index.php, sitemap.xml, dan robots.txt berhasil dibuat.";

?>

