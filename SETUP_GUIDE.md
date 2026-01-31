# Full System Setup Guide

This guide covers the installation and configuration of the complete OptiSpace system with database backend and live video feeds.

> **Note**: For instant evaluation, use `demo.html` instead - no setup required.

---

## Prerequisites

Before starting, ensure you have:

- **PHP**: Version 7.4 or higher
- **MySQL**: Version 5.7 or higher  
- **Web Server**: Apache with `mod_rewrite` enabled (or Nginx)
- **Modern Browser**: Chrome 90+, Firefox 88+, Edge 90+, or Safari 14+

### Recommended: XAMPP

The easiest way to get all prerequisites on Windows:

1. Download XAMPP from [https://www.apachefriends.org](https://www.apachefriends.org)
2. Install to `C:\xampp`
3. Includes Apache, MySQL, and PHP pre-configured

---

## Installation Steps

### 1. Clone/Copy Project

```bash
# Navigate to web root
cd C:\xampp\htdocs

# Project should be in 'optispace' folder
```

Your directory structure should look like:
```
C:\xampp\htdocs\optispace\
├── demo.html
├── index.php
├── logic.php
├── db_connect.php
├── database.sql
├── simulator.php
└── cctv\
    ├── VID1.mp4
    └── VID2.mp4
```

### 2. Database Setup

#### Option A: Using MySQL Command Line

```bash
# Start MySQL from XAMPP Control Panel

# Then run:
mysql -u root -p

# In MySQL prompt:
CREATE DATABASE optispace_db;
USE optispace_db;
SOURCE C:/xampp/htdocs/optispace/database.sql;
```

#### Option B: Using phpMyAdmin

1. Open `http://localhost/phpmyadmin`
2. Click "New" to create database
3. Name it `optispace_db`
4. Select the database
5. Go to "Import" tab
6. Choose `database.sql` file
7. Click "Go"

### 3. Configure Database Connection

Edit `db_connect.php`:

```php
<?php
$host = 'localhost';
$db = 'optispace_db';      // Database name
$user = 'root';            // Change if needed
$pass = '';                // Add password if you set one
$charset = 'utf8mb4';
// ... rest of file
?>
```

**Important**: Update `$user` and `$pass` if your MySQL has custom credentials.

### 4. Verify Video Files

Ensure video files are in the correct location:

```
C:\xampp\htdocs\optispace\cctv\VID1.mp4  (Entry camera)
C:\xampp\htdocs\optispace\cctv\VID2.mp4  (Exit camera)
```

**If videos are missing**:
- Camera panels will show black/gradient
- AR overlays will still function
- Demo will work visually without errors

### 5. Start Server

1. Open **XAMPP Control Panel**
2. Start **Apache** module
3. Start **MySQL** module
4. Verify both show green "Running" status

### 6. Access Dashboard

Open your browser and navigate to:

```
http://localhost/optispace/index.php
```

You should see:
- ✅ Satellite map with colored parking slots
- ✅ Live metrics (Free/Occupied numbers)
- ✅ Camera feeds playing (if videos exist)
- ✅ Event log populating with entries
- ✅ Revenue counter incrementing

---

## Optional: Manual Control Panel

To manually control vehicle entry/exit instead of autopilot:

1. Open `http://localhost/optispace/simulator.php`
2. Use the interface to:
   - Select a slot
   - Trigger entry (marks as occupied)
   - Trigger exit (marks as free, adds revenue)

---

## Troubleshooting

### Map Doesn't Load

**Symptom**: Blank screen or no map tiles  
**Cause**: Leaflet.js not loading or internet connection issue  
**Fix**:
- Check browser console for errors (F12)
- Verify internet connection (satellite tiles require online access)
- Demo version has offline fallback - use that if needed

### No Parking Slots Visible

**Symptom**: Map loads but no colored circles  
**Cause**: Database not connected or empty  
**Fix**:
```bash
# Check database exists
mysql -u root -p -e "SHOW DATABASES;"

# Should see 'optispace_db' in list

# Check slots table
mysql -u root -p optispace_db -e "SELECT COUNT(*) FROM slots;"

# Should return 120 (or your slot count)
```

### Videos Not Playing

**Symptom**: Black boxes where cameras should be  
**Cause**: Video files missing or wrong codec  
**Fix**:
- Verify video files exist in `cctv/` folder
- Check file names match exactly: `VID1.mp4` and `VID2.mp4`
- Ensure videos are H.264 encoded MP4 format
- Browser should support HTML5 video (all modern browsers do)

### PHP Errors Visible on Page

**Symptom**: JSON response shows HTML error messages  
**Cause**: `db_connect.php` has wrong credentials or database doesn't exist  
**Fix**:
- Open `http://localhost/optispace/logic.php?action=fetch_status` directly
- If you see error message, fix database connection in `db_connect.php`
- Restart Apache after making changes

### "System initialized" but No Activity

**Symptom**: Metrics show 0, no autopilot running  
**Cause**: JavaScript error or database returned no slots  
**Fix**:
- Open browser console (F12) and check for JavaScript errors
- Verify database has slot data:
  ```sql
  SELECT * FROM slots LIMIT 5;
  ```
- Make sure `init()` function is being called at end of `index.php`

### Satellite Imagery Not Loading (Shows Gray Tiles)

**Symptom**: Map renders but tiles are gray/blank  
**Cause**: ArcGIS tile service unavailable or blocked  
**Fix**:
- This is normal if offline
- Demo version has fallback to dark base map
- Full version: wait a few seconds, tiles may load slowly
- Alternative: modify tile layer URL in code to use CartoDB dark tiles

---

## Performance Optimization

### For Large Slot Counts (500+)

If you have many parking slots:

1. **Enable marker clustering**:
   - Add Leaflet.markercluster plugin
   - Group nearby slots at lower zoom levels

2. **Reduce autopilot frequency**:
   - Change entry interval from 3000ms to 5000ms
   - Change exit interval from 10000ms to 15000ms

3. **Limit event log length**:
   - Already set to 20 entries (line adjustments in code if needed)

### For Slow Servers

- Increase `max_execution_time` in `php.ini`
- Enable MySQL query caching
- Use CDN for Leaflet.js instead of unpkg

---

## Advanced Configuration

### Changing Coordinates

To use a different location:

1. Get new coordinates (lat/lng) from Google Maps
2. Update `$GATE` constant in `index.php`:
   ```javascript
   const GATE = [YOUR_LAT, YOUR_LNG];
   ```
3. Update map center:
   ```javascript
   const map = L.map('map', {
       center: [YOUR_LAT, YOUR_LNG],
       zoom: 19
   });
   ```
4. Update slot coordinates in database or `database.sql`

### Adding More Slots

1. Edit `database.sql` or insert directly:
   ```sql
   INSERT INTO slots (slot_name, lat, lng, zone_type, status)
   VALUES ('E-01', 8.488700, 76.921900, 'general', 'free');
   ```

2. Refresh dashboard to see new slots

### Customizing Colors

Edit CSS variables in `index.php` (or `demo.html`):

```css
:root {
    --color-free: #22c55e;      /* Change free slot color */
    --color-occupied: #dc2626;  /* Change occupied slot color */
    --color-bike: #eab308;      /* Bike zone color */
    --color-general: #3b82f6;   /* General zone color */
    --color-suv: #a855f7;        /* SUV zone color */
}
```

---

## Security Notes

### For Production Deployment

⚠️ **The current setup is for development/demo only!**

Before deploying to production:

1. **Database Security**:
   - Create dedicated MySQL user (not root)
   - Set strong password
   - Grant only necessary permissions

2. **File Permissions**:
   - Set `db connect.php` to read-only
   - Restrict web server user permissions

3. **Input Validation**:
   - `logic.php` needs input sanitization for production
   - Add CSRF tokens for simulator panel

4. **HTTPS**:
   - Use SSL certificate
   - Force HTTPS in Apache config

5. **Error Handling**:
   - Disable PHP error display
   - Use proper error logging

---

## API Endpoints

The system exposes REST-style API endpoints via `logic.php`:

### Fetch All Slots
```
GET /logic.php?action=fetch_status
Returns: { "slots": [...] }
```

### Mark Slot as Occupied
```
GET /logic.php?action=entry&slot_id=5
Returns: { "status": "success" }
```

### Mark Slot as Free
```
GET /logic.php?action=exit&slot_id=5
Returns: { "status": "success" }
```

---

## System Requirements

| Component | Minimum | Recommended |
|-----------|---------|-------------|
| PHP | 7.4 | 8.0+ |
| MySQL | 5.7 | 8.0+ |
| RAM | 512MB | 1GB+ |
| Disk Space | 50MB | 100MB+ |
| Browser | Chrome 80+ | Chrome 100+ |

---

## Testing Checklist

After setup, verify:

- [ ] Dashboard loads at `http://localhost/optispace/index.php`
- [ ] Map shows satellite imagery
- [ ] All 120 parking slots are visible as colored circles
- [ ] Zone labels (A, B, C, D) appear on map
- [ ] Free/Occupied counters show correct numbers
- [ ] Camera overlays cycle through messages
- [ ] Autopilot runs (vehicles enter/exit automatically)
- [ ] Event log populates with entries
- [ ] Revenue counter increments by ₹150 per exit
- [ ] Simulator panel works (`simulator.php`)
- [ ] No PHP errors in browser console
- [ ] No JavaScript errors in browser console

---

## Getting Help

If you encounter issues not covered here:

1. **Check browser console** (F12) for JavaScript errors
2. **Check Apache error log** (`C:\xampp\apache\logs\error.log`)
3. **Test API directly**: Visit `logic.php?action=fetch_status`
4. **Verify database**: Query `slots` table directly in phpMyAdmin

---

**For instant demo without setup, use `demo.html` instead!**

**For technical details, see PROJECT_DESCRIPTION.md in artifacts**
