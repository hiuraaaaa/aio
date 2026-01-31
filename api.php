<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Cek apakah ada parameter URL
if (!isset($_GET['url']) || empty($_GET['url'])) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'URL parameter is required'
    ]);
    exit;
}

$tiktokUrl = $_GET['url'];

// Validasi URL TikTok
if (!preg_match('/https?:\/\/(vt\.tiktok\.com|vm\.tiktok\.com|www\.tiktok\.com|tiktok\.com)\/\S+/', $tiktokUrl)) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'Invalid TikTok URL'
    ]);
    exit;
}

// API endpoint
$apiUrl = 'https://labs.shannzx.xyz/api/v1/tiktok?url=' . urlencode($tiktokUrl);

// Initialize cURL
$ch = curl_init();

curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]
]);

// Execute request
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

// Handle errors
if ($error) {
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'cURL Error: ' . $error
    ]);
    exit;
}

if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode([
        'status' => false,
        'message' => 'API returned status code: ' . $httpCode
    ]);
    exit;
}

// Return response
echo $response;
?>
