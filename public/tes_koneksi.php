<?php
echo "<h1>Tes Koneksi Keluar dengan cURL</h1>";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://api.github.com/zen");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_USERAGENT, 'cPanel-PHP-Test');

echo "<p>Mencoba menghubungi https://api.github.com/zen...</p>";

$output = curl_exec($ch);
$error_no = curl_errno($ch);

if ($error_no !== 0) {
    echo "<h2 style='color:red;'>GAGAL!</h2>";
    echo "<p>Koneksi keluar dari server ini kemungkinan diblokir oleh firewall.</p>";
    echo "<p><b>Pesan Error cURL:</b> " . curl_error($ch) . "</p>";
} else {
    echo "<h2 style='color:green;'>BERHASIL!</h2>";
    echo "<p>Server Anda bisa melakukan koneksi keluar.</p>";
    echo "<p><b>Respons dari server GitHub:</b> \"" . htmlspecialchars($output) . "\"</p>";
}

curl_close($ch);
?>