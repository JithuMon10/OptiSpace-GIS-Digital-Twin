# OptiSpace | Enterprise-Grade GIS Digital Twin

OptiSpace is a professional-tier Smart Parking Solution and Digital Twin dashboard designed for advanced logistics and urban planning. This project features high-fidelity GIS integration, real-time vehicle tracking, and automated zoning logic.

![OptiSpace UI](https://img.shields.io/badge/Status-Enterprise--Ready-00f2ff?style=for-the-badge&logo=esri)

## 🚀 Key Features

- **Esri GIS Integration**: Military-grade satellite imagery and reference layers centered on Kochi, India.
- **Strict Zoning Logic**: Automated enforcement of parking rules for Heavy Vehicles (Buses/Trucks) in dedicated Logistics Hub zones.
- **Smart Inefficiency Detection**: Real-time flagging of "inefficiently" parked vehicles (e.g., bikes in XL slots).
- **Enterprise Dashboard**: Split-screen interface with a Live Camera Feed and GIS tracking.
- **LiDAR Ready**: Built-in hooks for NeST Cloud LiDAR 3D services.

## 🛠️ Tech Stack

- **Frontend**: HTML5, CSS3 (Cyber-tech aesthetic), JavaScript, Leaflet.js
- **Backend**: PHP 8.x
- **Database**: MySQL/MariaDB
- **Maps**: Esri ArcGIS Online Services

## 📥 Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/JithuMon10/OptiSpace-GIS-Digital-Twin.git
   ```
2. **Setup Database**:
   - Import `database.sql` into your local MySQL server (XAMPP/WAMP recommended).
3. **Configure Connection**:
   - Update `db_connect.php` with your database credentials.
4. **Deploy**:
   - Place the project in your `htdocs` or equivalent web root directory.
   - Access via `localhost/optispace`.

## 📸 Dashboard Preview

The dashboard features a split-screen layout:
- **Left Panel**: 24/7 Live Security Camera loop.
- **Right Panel**: Interactive Esri Map with real-time slot statuses.
- **Bottom Ticker**: Live system status updates and ArcGIS sync status.

---
*Developed for NeST Digital Internship Target.*
