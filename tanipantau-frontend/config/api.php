<?php
/**
 * api.php — Helper CURL untuk semua request ke Laravel API
 * TaniPantau Smart Farming
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://127.0.0.1:8000/api/');

/**
 * Fungsi utama untuk request ke API Laravel
 *
 * @param string      $endpoint  Endpoint tanpa base URL, e.g. "petani", "petani/1"
 * @param string      $method    HTTP method: GET | POST | PUT | DELETE
 * @param array|null  $data      Data payload (untuk POST/PUT)
 * @param bool        $isUpload  Jika true, kirim sebagai multipart (untuk upload file)
 * @return array                 ['http_code', 'curl_error', 'raw_response', 'data']
 */
function apiRequest(string $endpoint, string $method = 'GET', ?array $data = null, bool $isUpload = false): array
{
    $url = BASE_URL . ltrim($endpoint, '/');
    $ch  = curl_init($url);

    // Headers default
    $headers = [
        'Accept: application/json',
    ];

    // Tambahkan token otorisasi jika sudah login
    if (!empty($_SESSION['token'])) {
        $headers[] = 'Authorization: Bearer ' . $_SESSION['token'];
    }

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 15);

    switch (strtoupper($method)) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data !== null) {
                // Jika ada _method (method spoofing) dan bukan upload,
                // pindahkan _method ke query string agar Laravel bisa baca
                if (!$isUpload && isset($data['_method'])) {
                    $spoofMethod = $data['_method'];
                    unset($data['_method']);
                    $url .= (strpos($url, '?') === false ? '?' : '&') . '_method=' . $spoofMethod;
                    curl_setopt($ch, CURLOPT_URL, $url);
                }
                if ($isUpload) {
                    // Multipart form-data untuk upload file
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                } else {
                    // JSON payload
                    $headers[] = 'Content-Type: application/json';
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
            }
            break;

        case 'PUT':
            // Laravel membaca PUT dari JSON body atau multipart
            if ($isUpload) {
                // Multipart form-data untuk PUT dengan file upload
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
                }
            } else {
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                if ($data !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
            }
            break;

        case 'DELETE':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;

        default: // GET
            // Tidak ada body tambahan
            break;
    }

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $rawResponse = curl_exec($ch);
    $httpCode    = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError   = curl_error($ch);
    $curlErrno   = curl_errno($ch);

    curl_close($ch);

    // Decode JSON response
    $decoded = json_decode($rawResponse, true);

    // ---- DEBUG LOGGING (sementara — hapus setelah confirmed fix) ----
    // Log setiap request yang gagal atau response tidak valid ke error_log PHP
    if ($curlErrno || $httpCode === 0 || $httpCode >= 400 || $decoded === null) {
        $logMsg = sprintf(
            "[TaniPantau API ERROR] %s %s%s | HTTP %d | cURL(%d): %s | Response: %s",
            $method,
            BASE_URL,
            $endpoint,
            $httpCode,
            $curlErrno,
            $curlError ?: '-',
            $rawResponse ?: '(empty)'
        );
        error_log($logMsg);
    }
    // ---- /DEBUG LOGGING ----

    return [
        'http_code'    => $httpCode,
        'curl_error'   => $curlError,
        'raw_response' => $rawResponse,
        'data'         => $decoded,
    ];
}

/**
 * Redirect ke login jika API mengembalikan 401 atau 403
 * Panggil setelah apiRequest() pada halaman yang membutuhkan auth
 */
function checkAuthResponse(array $response): void
{
    if (in_array($response['http_code'], [401, 403])) {
        $_SESSION = [];
        session_destroy();
        header('Location: login-petugas.php?expired=1');
        exit;
    }
}