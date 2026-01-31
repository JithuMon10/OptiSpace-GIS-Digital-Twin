# OptiSpace - Smart Parking Management System

**Real-time GIS-based Smart Parking Monitoring for TRV Airport Parking Lot**

---

## 🚀 Quick Start (For Evaluation)

### Option 1: Demo Version (Instant, No Setup Required)

1. **Open `demo.html` in your browser**
2. That's it! The dashboard loads immediately with simulated parking data.

> **Note about Videos**: The demo uses gradient placeholders for camera feeds since video files are not embedded. The full system includes actual CCTV video playback.

### Option 2: Full System (Requires Server Setup)

1. See [SETUP_GUIDE.md](SETUP_GUIDE.md) for detailed installation instructions
2. Requires Apache/MySQL (XAMPP) and database configuration
3. Includes live video feeds from `cctv/` directory

---

## 📁 Project Structure

```
optispace/
├── demo.html              ← START HERE (Instant demo, no setup)
├── index.php             ← Full system with PHP backend
├── logic.php             ← API endpoints (fetch/entry/exit)
├── db_connect.php        ← Database connection config
├── database.sql          ← MySQL schema + seed data
├── simulator.php         ← Manual vehicle entry/exit control
└── cctv/                 ← Video feeds (VID1.mp4, VID2.mp4)
```

---

## 🎯 What This System Does

OptiSpace is a professional command center-style dashboard that provides:

- **Real-time GIS Visualization**: Parking slots displayed on ArcGIS satellite imagery
- **Live Camera Feeds**: Entry/exit gates with AR overlays and vehicle detection simulation
- **Automated Operations**: Intelligent vehicle entry/exit with data packet animations
- **Revenue Tracking**: Real-time payment processing and revenue counters
- **Event Logging**: Timestamped activity feed with smooth animations
- **Zone-based Management**: Bike, General, and SUV/Premium slot categorization

---

## 🏗️ Architecture

**Frontend**: HTML5, CSS3, JavaScript (Leaflet.js for mapping)  
**Backend**: PHP with PDO  
**Database**: MySQL  
**Mapping**: ArcGIS World Imagery (satellite tiles)  
**Design**: Glassmorphism with muted color palette

---

## 📸 Key Features

### Professional Command Center UI
- **Top Status Bar**: App title, location, live revenue, utilization %, system status
- **Left Operations Panel**: Free/Occupied metrics, dual camera feeds with AR overlays
- **Right Panel**: Revenue tracker and live event log (scrollable)
- **Bottom Footer**: Availability by type, color legend, system health status
- **Full-screen Map**: 3D CSS transform with satellite imagery and colored slot markers

### Real-time Animations
- **Data Packet Animation**: White particle travels from gate to allocated slot (entry)
- **Flash Animation**: White flash + payment overlay on exit
- **Counter Animations**: Smooth step-by-step number transitions
- **Micro-interactions**: Breathing status dots, pulsing LIVE badges, hover glows

### Visual Design
- **Muted Color Palette**: No flashy or neon colors (professional aesthetics)
- **Typography Hierarchy**: Inter (UI text) + JetBrains Mono (metrics/data)
- **8px Grid System**: Pixel-perfect alignment throughout
- **Glassmorphism Panels**: Semi-transparent with backdrop blur effects
- **Zone Labels**: One letter (A, B, C, D) per zone group to reduce clutter

---

## 🔍 Technical Highlights

| Feature | Implementation |
|---------|----------------|
| **Map Transform** | `perspective(1200px) rotateX(25deg)` for depth |
| **Slot Status** | 300ms smooth color transitions (green ↔ red) |
| **Zone Labeling** | JavaScript Set tracks labeled zones (one  label per group) |
| **Autopilot System** | Entry loop (3s), Exit loop (10s) with overlays |
| **Payment Processing** | ₹150 per exit with flash animation |
| **Video Playback** | 0.5x slow-motion for better AR sync |

---

## 🌐 Browser Compatibility

**Recommended**:  
✅ Chrome 90+  
✅ Edge 90+  
✅ Firefox 88+  
✅ Safari 14+

**Demo Mode**: Works offline with satellite tile fallback to dark base map.

---

## 📊 System Metrics

- **Total Parking Slots**: 120 (configurable via database)
- **Zone Distribution**: 
  - Zone A: 20 Bike slots
  - Zone B: 40 General slots
  - Zone C: 40 General slots
  - Zone D: 20 SUV/Premium slots
- **Update Frequency**: Real-time (demo: 3s entry, 10s exit cycles)
- **Revenue Model**: ₹150 flat rate per vehicle

---

## 🎨 Design Philosophy

This dashboard follows **situational awareness** principles used in airport operations rooms:

1. **Glanceable Metrics**: Large, high-contrast numbers for instant recognition
2. **Color Coding**: Muted but distinct colors for status differentiation
3. **Zone Spatial Awareness**: Letter labels provide quick orientation without clutter
4. **Live Indicators**: Breathing animations show system activity without distraction
5. **Professional Aesthetics**: Dark theme, glassmorphism, grid-based layout

---

## 📦 File Responsibilities

| File | Purpose | Audience |
|------|---------|----------|
| `demo.html` | Instant evaluation demo | Judges/Reviewers |
| `index.php` | Full production system | Developers/Operators |
| `logic.php` | API backend | System integration |
| `README.md` | Project overview | All audiences |
| `SETUP_GUIDE.md` | Installation steps | Developers |

---

## 🔧 Development Notes

### Demo vs Full System

**Demo Version** (`demo.html`):
- ✅ Embedded parking data (no database)
- ✅ Satellite tile fallback (works offline)
- ✅ All animations and UI intact
- ❌ No video files (gradient placeholders)
- ❌ No database writes (simulated only)

**Full Version** (`index.php`):
- ✅ MySQL database integration
- ✅ Real video playback from `cctv/`
- ✅ API endpoints for external integration
- ✅ Manual control via `simulator.php`
- ⚠️ Requires Apache + MySQL setup

---

## 📞 System Status

- ✅ **Gate Sensors**: Online (simulated)
- ✅ **CCTV Array**: 2/2 Active
- ✅ **AI Detection**: Running (demo mode)
- ✅ **Map Rendering**: ArcGIS satellite imagery
- ✅ **Revenue Tracking**: Functional

---

## 🎯 Evaluation Checklist

For judges reviewing this project:

- [x] Open `demo.html` → Should load instantly
- [x] Observe autopilot → Vehicles enter/exit automatically
- [x] Check map → Satellite imagery with color-coded slots
- [x] Watch animations → Smooth data packets, flash effects
- [x] Inspect UI → Professional command center aesthetic
- [x] Test offline → Should fall back to dark base map
- [x] View mobile → Responsive demo badge placement

---

## 📄 License & Credits

**Project**: OptiSpace - Smart Parking System  
**Location**: TRV Airport Parking Lot  
**Mapping**: ArcGIS World Imagery  
**Fonts**: Inter (Google Fonts), JetBrains Mono  
**Icons**: Unicode symbols (no external dependencies)

---

**For full installation instructions, see [SETUP_GUIDE.md](SETUP_GUIDE.md)**

**For technical deep-dive, see PROJECT_DESCRIPTION.md in artifacts directory**
