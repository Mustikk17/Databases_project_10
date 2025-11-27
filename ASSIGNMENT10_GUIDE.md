# Assignment 10 - Linked Services: Deployment Guide

## What Was Implemented

### All Requirements Met:

1. **Extract Client IP Address**
   - Checks multiple HTTP headers (X-Forwarded-For, Client-IP, etc.)
   - Handles proxies and load balancers
   - Validates IP addresses

2. **Perform Geolocation Lookup**
   - Uses ipinfo.io API (free tier: 50,000 requests/month)
   - Returns: city, region, country, coordinates, ISP
   - Error handling and fallback

3. **Display on Interactive Map**
   - Leaflet.js library (latest version)
   - OpenStreetMap tiles
   - Centered on user's location

4. **Marker with IP Callout**
   - Blue marker at exact coordinates
   - Popup shows: IP, city, country, coordinates
   - Circle showing approximate area (5km radius)

5. **Integration Options**
   - Embedded in landing page (via link in index.php)
   - Can be accessed as separate page (geolocation.php)

---

## Deployment Steps

### Step 1: Upload Files via WinSCP

Connect to university server and upload:

**New Files:**
```
~/public_html/project6/
├── geolocation.php     [NEW - Main geolocation page]
└── index.php          [UPDATED - Added link to geolocation]
```

**Existing Files (keep as-is):**
```
├── css/style.css
├── config/
├── courses/
└── teams/
```

### Step 2: Set File Permissions

In SSH terminal:
```bash
cd ~/public_html/project6
chmod 644 geolocation.php
chmod 644 index.php
```

### Step 3: Test Locally (Optional)

If testing on XAMPP first:
1. Copy files to `C:\xampp\htdocs\project6\`
2. Access: `http://localhost/project6/geolocation.php`
3. Note: Will show demo mode for localhost IPs

### Step 4: Access on University Server

Open browser:
```
http://10.60.36.1/~mzeynalli/project6/geolocation.php
```

Or via main page:
```
http://10.60.36.1/~mzeynalli/project6/
Click "View My Location"
```

---

## Testing Checklist

### Basic Functionality:
- [ ] Page loads without errors
- [ ] Map displays correctly
- [ ] Marker appears on map
- [ ] Popup shows when clicking marker
- [ ] Location info cards show data
- [ ] "Back to Home" link works

### IP Detection:
- [ ] Shows your actual IP (not 127.0.0.1)
- [ ] Shows university network IP if on campus
- [ ] Shows demo mode notice if testing locally

### Map Features:
- [ ] Can zoom in/out
- [ ] Can pan around map
- [ ] Map tiles load correctly (no broken images)
- [ ] Marker is centered
- [ ] Circle overlay visible

### Data Accuracy:
- [ ] City matches your location (approximate)
- [ ] Country is correct
- [ ] Coordinates make sense
- [ ] ISP/Organization shown

---

## Troubleshooting

### Problem: Map doesn't display / blank area
**Solution:**
- Check browser console (F12) for JavaScript errors
- Verify Leaflet CSS/JS loaded (check Network tab)
- Ensure internet connection (Leaflet loads from CDN)

### Problem: Shows "Unknown" for all location data
**Solution:**
- ipinfo.io API might be blocked/rate limited
- Check if curl is enabled on server: `php -m | grep curl`
- Wait a few minutes if rate limited

### Problem: Shows localhost IP (127.0.0.1 or ::1)
**Solution:**
- This is normal for local testing
- Script automatically switches to demo mode
- Will work correctly on university server

### Problem: Wrong location shown
**Solution:**
- Geolocation is approximate (based on ISP)
- University networks might show ISP location
- VPN/Proxy will show exit node location
- This is expected behavior

### Problem: "Page not found" / 404 error
**Solution:**
- Check file uploaded to correct directory
- Verify path: `~/public_html/project6/geolocation.php`
- Check permissions: `chmod 644 geolocation.php`

---

## How It Works (Technical Overview)

### Architecture:

```
Client Browser
    ↓
PHP Server (geolocation.php)
    ↓ [1. Get IP]
$_SERVER['REMOTE_ADDR']
    ↓ [2. API Call]
ipinfo.io API
    ↓ [3. Return JSON]
{city, region, country, loc: "lat,lon"}
    ↓ [4. Render HTML + JS]
Leaflet.js + OpenStreetMap
    ↓ [5. Display]
Interactive Map with Marker
```

### API Response Example:

```json
{
  "ip": "134.76.10.50",
  "city": "Göttingen",
  "region": "Lower Saxony",
  "country": "DE",
  "loc": "51.5333,9.9333",
  "org": "Georg-August-Universitaet Goettingen",
  "timezone": "Europe/Berlin"
}
```

### JavaScript Map Initialization:

```javascript
// Create map centered on coordinates
const map = L.map('map').setView([lat, lon], 13);

// Add OpenStreetMap tiles
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

// Add marker with popup
L.marker([lat, lon]).addTo(map).bindPopup("IP: ...").openPopup();
```

---

## External Services Used

### 1. ipinfo.io
- **Purpose**: IP to geolocation translation
- **Endpoint**: `https://ipinfo.io/{ip}/json`
- **Free Tier**: 50,000 requests/month
- **No API key required** for basic usage
- **Rate Limit**: ~1,000 requests/day

### 2. Leaflet.js
- **Purpose**: Interactive map library
- **Version**: 1.9.4
- **CDN**: unpkg.com
- **License**: BSD 2-Clause (open source)

### 3. OpenStreetMap
- **Purpose**: Map tile provider
- **Tiles**: `https://tile.openstreetmap.org/`
- **Free**: For non-commercial use
- **Attribution**: Required (included in code)

---

## Assignment Submission

### What to Submit:

1. **Website URL**:
   ```
   http://10.60.36.1/~mzeynalli/project6/geolocation.php
   ```

2. **Git Repository**:
   - Push `geolocation.php` to your GitHub repo
   - Update `index.php` in repo
   - Update README.md

3. **Documentation** (optional but recommended):
   - Screenshot of working map
   - Brief explanation of implementation

### GitHub Commit:

```bash
cd ~/project6
git add geolocation.php index.php
git commit -m "Assignment 9: Implement IP geolocation with Leaflet and ipinfo.io"
git push
```

---

## Expected Result

When you access the page, you should see:

1. **Header**: "IP Geolocation Service"
2. **Info Cards** showing:
   - Your IP address
   - City, Region, Country
   - Coordinates
   - ISP/Organization
3. **Interactive Map** with:
   - Your location centered
   - Blue marker
   - Popup with details
   - Circle showing area
   - Zoom/pan controls
4. **Footer** with attribution

---

## Security Notes

### IP Detection:
- Script checks multiple headers to get real IP
- Validates IP format
- Handles proxies/load balancers correctly

### API Security:
- No sensitive data exposed
- API calls server-side (not from browser)
- SSL verification disabled for compatibility (university server)

### Privacy:
- No IP addresses stored in database
- No logging of visitor data
- All processing happens in real-time

---

## Customization Options

### Change Map Style:

Instead of OpenStreetMap, you can use other tile providers:

```javascript
// Dark mode
L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png').addTo(map);

// Satellite
L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}').addTo(map);
```

### Change Marker Icon:

```javascript
const customIcon = L.icon({
    iconUrl: 'marker-icon.png',
    iconSize: [25, 41],
    iconAnchor: [12, 41]
});
L.marker([lat, lon], {icon: customIcon}).addTo(map);
```

### Change Zoom Level:

```javascript
const map = L.map('map').setView([lat, lon], 10); // 10 = wider view
// 13 = default (neighborhood)
// 15 = street level
```

---

## Final Checklist

Before submitting:

- [ ] geolocation.php uploaded to server
- [ ] index.php updated with link
- [ ] Tested page loads correctly
- [ ] Map displays and is interactive
- [ ] Marker shows correct location
- [ ] Popup displays IP and location info
- [ ] Works on university network
- [ ] Files pushed to Git repository
- [ ] Submitted website URL

---

## Tips

1. **Test from different devices** to see different IPs
2. **Use VPN** to test different locations
3. **Check browser console** (F12) for any errors
4. **Take screenshots** for documentation
5. **Test on mobile** to verify responsive design

---

## Grading Criteria (Typical)

Expected grading points:

- **IP Extraction** (20%): Correctly gets client IP
- **Geolocation Lookup** (25%): Successfully calls ipinfo.io API
- **Map Display** (25%): Leaflet + OpenStreetMap working
- **Marker & Popup** (20%): Shows position with IP info
- **Code Quality** (10%): Clean, documented, error handling

---

**You're ready to submit Assignment 9!** 🎉

Estimated time: 30 minutes (upload + test)