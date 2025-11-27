<?php
/*
    Quick Test Script for Geolocation Setup
    Upload this alongside geolocation.php to test components
*/

echo "<h1>Geolocation Service Test</h1>";
echo "<style>body{font-family:sans-serif;padding:20px;} .pass{color:green;} .fail{color:red;} .info{color:blue;}</style>";

// Test 1: PHP cURL Extension
echo "<h2>Test 1: PHP cURL Extension</h2>";
if (function_exists('curl_init')) {
    echo "<p class='pass'>✓ cURL is available</p>";
} else {
    echo "<p class='fail'>✗ cURL is NOT available (required for API calls)</p>";
}

// Test 2: IP Detection
echo "<h2>Test 2: IP Detection</h2>";
$ip = $_SERVER['REMOTE_ADDR'];
echo "<p class='info'>Detected IP: <strong>$ip</strong></p>";
if ($ip === '127.0.0.1' || $ip === '::1') {
    echo "<p class='info'>Note: Localhost detected. Will use demo mode on geolocation page.</p>";
} else {
    echo "<p class='pass'>✓ Real IP detected</p>";
}

// Test 3: API Connection
echo "<h2>Test 3: ipinfo.io API Connection</h2>";
$test_ip = '8.8.8.8';
$api_url = "https://ipinfo.io/{$test_ip}/json";

if (function_exists('curl_init')) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        echo "<p class='fail'>✗ cURL Error: $error</p>";
    } elseif ($http_code === 200) {
        echo "<p class='pass'>✓ API connection successful</p>";
        $data = json_decode($response, true);
        echo "<p class='info'>Test response for IP $test_ip:</p>";
        echo "<pre>" . print_r($data, true) . "</pre>";
    } else {
        echo "<p class='fail'>✗ API returned HTTP code: $http_code</p>";
    }
} else {
    echo "<p class='fail'>✗ Cannot test API (cURL not available)</p>";
}

// Test 4: Internet Connectivity
echo "<h2>Test 4: External Resource Access</h2>";
$leaflet_css = "https://unpkg.com/leaflet@1.9.4/dist/leaflet.css";
$headers = @get_headers($leaflet_css);
if ($headers && strpos($headers[0], '200') !== false) {
    echo "<p class='pass'>✓ Can access Leaflet CDN</p>";
} else {
    echo "<p class='fail'>✗ Cannot access Leaflet CDN (may affect map display)</p>";
}

// Test 5: File Permissions
echo "<h2>Test 5: File Check</h2>";
if (file_exists('geolocation.php')) {
    echo "<p class='pass'>✓ geolocation.php found</p>";
} else {
    echo "<p class='fail'>✗ geolocation.php not found in current directory</p>";
}

if (file_exists('css/style.css')) {
    echo "<p class='pass'>✓ css/style.css found</p>";
} else {
    echo "<p class='fail'>✗ css/style.css not found</p>";
}

// Summary
echo "<h2>Summary</h2>";
echo "<p><a href='geolocation.php' style='color:blue;font-weight:bold;'>→ Go to Geolocation Page</a></p>";
echo "<p><a href='index.php' style='color:blue;font-weight:bold;'>→ Go to Main Page</a></p>";
?>