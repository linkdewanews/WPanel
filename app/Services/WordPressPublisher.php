<?php

namespace App\Services;

use App\Models\Site;
use Illuminate\Support\Facades\Log;

class WordPressPublisher
{
    /**
     * Fungsi helper privat untuk menjalankan cURL request.
     */
    private function executeCurl(string $url, string $username, string $password, ?string $postData = null, bool $isPost = true)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERPWD, $username . ':' . $password);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        if ($isPost) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if ($postData) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            }
        }
        
        $responseBody = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorMsg = curl_error($ch);
        curl_close($ch);

        if ($errorMsg) {
            Log::error('Gagal posting (Error cURL)', ['site_url' => $url, 'error' => $errorMsg]);
            return null;
        }

        return ['status' => $httpCode, 'body' => $responseBody];
    }

    /**
     * Mempublikasikan sebuah artikel ke situs WordPress.
     * @return array|null
     */
    public function publish(Site $site, array $payload)
    {
        // ===================================================================
        // === LANGKAH DEBUGGING FINAL: CATAT KREDENSIAL YANG DIGUNAKAN ===
        // ===================================================================
        Log::info('MEMULAI PROSES POSTING DARI PUBLISHER', [
            'site_id' => $site->id,
            'site_name' => $site->name,
            'target_url' => rtrim($site->url, '/') . '/wp-json/wp/v2/posts',
            'username_used' => $site->user,
            // Hanya log beberapa karakter password demi keamanan
            'password_used_preview' => substr($site->pass, 0, 5) . '...(dan seterusnya)' 
        ]);
        // ===================================================================

        $url = rtrim($site->url, '/') . '/wp-json/wp/v2/posts';
        $jsonData = json_encode($payload);
        
        $result = $this->executeCurl($url, $site->user, $site->pass, $jsonData);

        if ($result && $result['status'] == 201) {
            Log::info('Berhasil posting ke ' . $site->name, ['payload' => $payload]);
            return json_decode($result['body'], true);
        }
        
        Log::error('Gagal posting (Respons Error dari WP)', ['site' => $site->name, 'status' => $result['status'] ?? 'N/A', 'body' => $result['body'] ?? '']);
        return null;
    }

    public function getPost(Site $site, int $wpPostId)
    {
        $url = rtrim($site->url, '/') . "/wp-json/wp/v2/posts/{$wpPostId}";
        $result = $this->executeCurl($url, $site->user, $site->pass, null, false); // isPost = false

        if ($result && $result['status'] == 200) {
            return json_decode($result['body'], true);
        }
        return null;
    }

    public function updatePost(Site $site, int $wpPostId, array $postData)
    {
        $url = rtrim($site->url, '/') . "/wp-json/wp/v2/posts/{$wpPostId}";
        $jsonData = json_encode([
            'title'   => $postData['title'],
            'content' => $postData['content'],
            'categories' => $postData['categories'] ?? [],
        ]);

        $result = $this->executeCurl($url, $site->user, $site->pass, $jsonData);

        if ($result && $result['status'] == 200) {
            return json_decode($result['body'], true);
        }
        return null;
    }

    // Untuk upload media, kita perlu cURL yang sedikit berbeda
    public function uploadMedia(Site $site, string $filePath, string $fileName)
    {
        $url = rtrim($site->url, '/') . '/wp-json/wp/v2/media';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents($filePath));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Disposition: attachment; filename="' . $fileName . '"']);
        curl_setopt($ch, CURLOPT_USERPWD, $site->user . ':' . $site->pass);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode == 201) {
            return json_decode($result, true)['id'] ?? null;
        }
        return null;
    }
    
    public function getCategories(Site $site)
    {
        $url = rtrim($site->url, '/') . '/wp-json/wp/v2/categories?per_page=100';
        $result = $this->executeCurl($url, $site->user, $site->pass, null, false); // isPost = false

        if ($result && $result['status'] == 200) {
            return collect(json_decode($result['body'], true))->pluck('name', 'id')->all();
        }
        return [];
    }
}