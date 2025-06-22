<?php

namespace App\Services;

use App\Models\Site;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WordPressPublisher
{
    /**
     * Mempublikasikan sebuah artikel ke situs WordPress.
     * @param Site $site
     * @param array $payload
     * @return array|null Mengembalikan data post jika sukses, null jika gagal.
     */
    public function publish(Site $site, array $payload)
    {
        $response = Http::withoutVerifying()
            ->withBasicAuth($site->user, $site->pass)
            ->timeout(30)
            ->post(rtrim($site->url, '/') . '/wp-json/wp/v2/posts', $payload);

        // Guard Clause: Cek kondisi gagal terlebih dahulu. Jika gagal, langsung keluar.
        if (!$response->successful() || $response->status() != 201) {
            Log::error('Gagal posting ke ' . $site->name, [
                'status' => $response->status(),
                'body' => $response->body(),
                'payload' => $payload,
            ]);
            return null;
        }

        // Jika lolos dari pengecekan di atas, berarti sudah pasti berhasil.
        Log::info('Berhasil posting ke ' . $site->name, ['payload' => $payload]);
        return $response->json();
    }

    /**
     * Mengambil data satu postingan dari WordPress.
     * @param Site $site
     * @param int $wpPostId
     * @return array|null Mengembalikan data post jika sukses, null jika gagal.
     */
    public function getPost(Site $site, int $wpPostId)
    {
        $response = Http::withoutVerifying()
            ->withBasicAuth($site->user, $site->pass)
            ->timeout(30)
            ->get(rtrim($site->url, '/') . "/wp-json/wp/v2/posts/{$wpPostId}");

        // Guard Clause: Jika gagal, langsung return null.
        if (!$response->successful()) {
            Log::error('Gagal mengambil post dari ' . $site->name, ['wp_post_id' => $wpPostId]);
            return null;
        }

        // Jika berhasil, return datanya.
        return $response->json();
    }

    /**
     * Mengupdate postingan yang sudah ada di WordPress.
     * @param Site $site
     * @param int $wpPostId
     * @param array $postData
     * @return array|null Mengembalikan data post jika sukses, null jika gagal.
     */
    public function updatePost(Site $site, int $wpPostId, array $postData)
    {
        $response = Http::withoutVerifying()
            ->withBasicAuth($site->user, $site->pass)
            ->timeout(30)
            ->post(rtrim($site->url, '/') . "/wp-json/wp/v2/posts/{$wpPostId}", [
                'title'   => $postData['title'],
                'content' => $postData['content'],
                'categories' => $postData['categories'] ?? [], // Tambahkan categories jika ada
            ]);
        
        // Guard Clause: Jika gagal, langsung return null.
        if (!$response->successful()) {
            Log::error('Gagal mengupdate post di ' . $site->name, ['wp_post_id' => $wpPostId]);
            return null;
        }
        
        // Jika berhasil, return datanya.
        return $response->json();
    }

    /**
     * Mengupload file media ke WordPress dan mengembalikan ID-nya.
     * @param Site $site
     * @param string $filePath
     * @param string $fileName
     * @return int|null ID media jika berhasil, null jika gagal.
     */
    public function uploadMedia(Site $site, string $filePath, string $fileName)
    {
        $response = Http::withoutVerifying()
            ->withBasicAuth($site->user, $site->pass)
            ->timeout(60) // Timeout lebih lama untuk upload file
            ->attach('file', file_get_contents($filePath), $fileName)
            ->post(rtrim($site->url, '/') . '/wp-json/wp/v2/media');

        if ($response->successful()) {
            Log::info('Berhasil upload media ke ' . $site->name, ['file' => $fileName]);
            return $response->json('id');
        } else {
            Log::error('Gagal upload media ke ' . $site->name, ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        }
    }

    /**
     * Mengambil daftar kategori dari situs WordPress.
     * @param Site $site
     * @return array Mengembalikan array kategori atau array kosong jika gagal.
     */
    public function getCategories(Site $site)
    {
        $response = Http::withoutVerifying()
            ->withBasicAuth($site->user, $site->pass)
            ->timeout(20)
            ->get(rtrim($site->url, '/') . '/wp-json/wp/v2/categories', ['per_page' => 100]);
        
        if (!$response->successful()) {
            return [];
        }
        
        return collect($response->json())->pluck('name', 'id')->all();
    }
}