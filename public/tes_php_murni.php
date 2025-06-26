<?php
echo "<h1>Mencoba Posting dengan PHP Murni...</h1>";

$username = 'admin'; // Ganti ini
$password = 'ZSJ1 ycLF 9Amc Ke7x GhZq Nmr0'; // Ganti ini
$url = 'https://7metergame.one/wp-json/wp/v2/posts'; // Ganti jika targetnya beda

$post_data = json_encode([
    'title'   => 'Tes dari Skrip PHP Murni di Server Laravel',
    'content' => 'Jika ini berhasil, berarti masalahnya 100% pada cara Laravel mengirim request.',
    'status'  => 'draft'
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json'
]);
curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
// Baris di bawah ini untuk mengabaikan SSL, sama seperti Http::withoutVerifying()
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "<h2>Hasil:</h2>";
echo "Kode Status HTTP: " . $http_code . "<br>";
echo "Respons dari server:<br>";
echo "<pre>" . htmlspecialchars($result) . "</pre>";
?>