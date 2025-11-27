<?php
/*
    Assignment 9 - Linked Services: IP Geolocation
    Displays visitor's location on an interactive map
*/

// Get client IP address
function getClientIP() {
    // Check for various headers that might contain the real IP
    $ip_keys = array(
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR',
        'HTTP_X_FORWARDED',
        'HTTP_X_CLUSTER_CLIENT_IP',
        'HTTP_FORWARDED_FOR',
        'HTTP_FORWARDED',
        'REMOTE_ADDR'
    );
    
    foreach ($ip_keys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                
                // Validate IP address
                if (filter_var($ip, FILTER_VALIDATE_IP, 
                    FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    
    // Fallback to REMOTE_ADDR
    return $_SERVER['REMOTE_ADDR'];
}

// Get geolocation data from ipinfo.io
function getGeolocation($ip) {
    // ipinfo.io API endpoint (free tier: 50,000 requests/month)
    $api_url = "https://ipinfo.io/{$ip}/json";
    
    // Use cURL for better error handling
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For university server compatibility
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return null;
    }
    
    $data = json_decode($response, true);
    
    // Parse location (format: "lat,lon")
    if (isset($data['loc'])) {
        list($lat, $lon) = explode(',', $data['loc']);
        $data['latitude'] = floatval($lat);
        $data['longitude'] = floatval($lon);
    }
    
    return $data;
}

// Get client IP
$client_ip = getClientIP();

// Handle localhost/private IPs for testing
if ($client_ip === '127.0.0.1' || $client_ip === '::1' || 
    strpos($client_ip, '192.168.') === 0 || strpos($client_ip, '10.') === 0) {
    // Use a demo IP for testing (Google's public DNS)
    $demo_mode = true;
    $test_ip = '8.8.8.8';
    $location = getGeolocation($test_ip);
} else {
    $demo_mode = false;
    $location = getGeolocation($client_ip);
}

// Default fallback location (your university or a default location)
if (!$location || !isset($location['latitude'])) {
    $location = array(
        'ip' => $client_ip,
        'city' => 'Unknown',
        'region' => 'Unknown',
        'country' => 'Unknown',
        'latitude' => 51.1657, // Default: Göttingen, Germany (example)
        'longitude' => 9.8535,
        'org' => 'Unknown'
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IP Geolocation - Assignment 9</title>
    <link rel="stylesheet" href="css/style.css">
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
          crossorigin=""/>
    
    <style>
        /* Map container */
        #map {
            width: 100%;
            height: 500px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            margin-bottom: 30px;
        }
        
        .info-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        
        .info-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .info-item label {
            display: block;
            font-weight: 600;
            color: #666;
            font-size: 0.9em;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        
        .info-item .value {
            font-size: 1.1em;
            color: #333;
            word-break: break-all;
        }
        
        .demo-notice {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>IP Geolocation Service</h1>
            <p class="subtitle">Assignment 9 - Linked Services</p>
        </header>

        <main>
            <div class="nav-links">
                <a href="index.php">← Back to Home</a>
            </div>

            <?php if ($demo_mode): ?>
            <div class="demo-notice">
                <strong>⚠️ Demo Mode:</strong> You're accessing from a local IP (<?php echo htmlspecialchars($client_ip); ?>). 
                Showing demo location for IP: <?php echo htmlspecialchars($test_ip); ?> (Google Public DNS).
            </div>
            <?php endif; ?>

            <div class="info-card">
                <h2>📍 Your Location Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>IP Address</label>
                        <div class="value"><?php echo htmlspecialchars($demo_mode ? $test_ip : $client_ip); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <label>City</label>
                        <div class="value"><?php echo htmlspecialchars($location['city'] ?? 'Unknown'); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <label>Region</label>
                        <div class="value"><?php echo htmlspecialchars($location['region'] ?? 'Unknown'); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <label>Country</label>
                        <div class="value"><?php echo htmlspecialchars($location['country'] ?? 'Unknown'); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <label>Coordinates</label>
                        <div class="value">
                            <?php echo number_format($location['latitude'], 4); ?>,
                            <?php echo number_format($location['longitude'], 4); ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <label>ISP / Organization</label>
                        <div class="value"><?php echo htmlspecialchars($location['org'] ?? 'Unknown'); ?></div>
                    </div>
                </div>
            </div>

            <div class="info-card">
                <h2>🗺️ Interactive Map</h2>
                <div id="map"></div>
                <p style="color: #666; font-size: 0.9em; margin-top: 10px;">
                    <strong>Map powered by:</strong> Leaflet.js + OpenStreetMap | 
                    <strong>Geolocation by:</strong> ipinfo.io
                </p>
            </div>

            <div class="info-card">
                <h3>🔧 Technical Details</h3>
                <ul style="line-height: 1.8; color: #555;">
                    <li><strong>Client IP Detection:</strong> Checks multiple HTTP headers (X-Forwarded-For, Client-IP, etc.)</li>
                    <li><strong>Geolocation API:</strong> ipinfo.io RESTful API</li>
                    <li><strong>Map Library:</strong> Leaflet.js v1.9.4</li>
                    <li><strong>Map Tiles:</strong> OpenStreetMap contributors</li>
                    <li><strong>Marker:</strong> Custom popup showing IP address and location details</li>
                </ul>
            </div>
        </main>

        <footer>
            <p>&copy; 2025 Academic Management System | Assignment 9: Linked Services</p>
        </footer>
    </div>

    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
    
    <script>
        // Initialize map centered on user's location
        const lat = <?php echo $location['latitude']; ?>;
        const lon = <?php echo $location['longitude']; ?>;
        const ip = "<?php echo htmlspecialchars($demo_mode ? $test_ip : $client_ip); ?>";
        const city = "<?php echo htmlspecialchars($location['city'] ?? 'Unknown'); ?>";
        const country = "<?php echo htmlspecialchars($location['country'] ?? 'Unknown'); ?>";
        
        // Create map
        const map = L.map('map').setView([lat, lon], 13);
        
        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        
        // Create custom icon (optional - using default blue marker)
        const marker = L.marker([lat, lon]).addTo(map);
        
        // Add popup with location details
        marker.bindPopup(`
            <div style="text-align: center;">
                <strong style="font-size: 1.2em; color: #667eea;">📍 Your Location</strong><br><br>
                <strong>IP Address:</strong> ${ip}<br>
                <strong>City:</strong> ${city}<br>
                <strong>Country:</strong> ${country}<br>
                <strong>Coordinates:</strong> ${lat.toFixed(4)}, ${lon.toFixed(4)}
            </div>
        `).openPopup();
        
        // Optional: Add a circle to show approximate area
        L.circle([lat, lon], {
            color: '#667eea',
            fillColor: '#764ba2',
            fillOpacity: 0.2,
            radius: 5000 // 5km radius
        }).addTo(map);
    </script>
</body>
</html>