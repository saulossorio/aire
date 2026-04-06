<?php
/**
 * aire — API Proxy
 * Forwards requests to Anthropic's Claude API.
 * The API key is stored here on the server and never exposed to the client.
 * 
 * SETUP:
 * 1. Replace the API key below with your real key from console.anthropic.com
 * 2. Upload this file to: public_html/artifacts/api/refine.php
 * 3. Test by opening artifacts.saulossorio.com/a1.html
 */

// ── YOUR ANTHROPIC API KEY (only exists on this server) ──
$API_KEY = 'sk-ant-PASTE-YOUR-REAL-KEY-HERE';

// ── CORS: only allow your own domain ──
$allowed_origins = [
    'https://artifacts.saulossorio.com',
    'https://saulossorio.com',
    'https://www.saulossorio.com'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $allowed_origins)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Methods: POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
}

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

header('Content-Type: application/json');

// Read the request body
$raw_input = file_get_contents('php://input');
$input = json_decode($raw_input, true);

if (!$input || !isset($input['messages'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid request: missing messages']);
    exit;
}

// Forward to Anthropic API
$ch = curl_init('https://api.anthropic.com/v1/messages');
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT        => 120,
    CURLOPT_HTTPHEADER     => [
        'Content-Type: application/json',
        'x-api-key: ' . $API_KEY,
        'anthropic-version: 2023-06-01'
    ],
    CURLOPT_POSTFIELDS => $raw_input
]);

$response = curl_exec($ch);
$err = curl_error($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($err) {
    http_response_code(502);
    echo json_encode(['error' => 'Failed to reach Anthropic API: ' . $err]);
    exit;
}

http_response_code($httpCode);
echo $response;
