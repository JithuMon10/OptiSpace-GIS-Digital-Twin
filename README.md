# 🏙️ OptiSpace: GIS-Powered Digital Twin & Management System


> **Transforming Urban Mobility through Real-Time Surveillance & Predictive Space Intelligence.**

[![Demo Mode](https://img.shields.io/badge/Demo-Instant_Run-brightgreen?style=for-the-badge&logo=googlechrome)](run_this.html)
[![License](https://img.shields.io/badge/Status-Judge--Ready-blue?style=for-the-badge)](https://github.com/JithuMon10/OptiSpace-GIS-Digital-Twin)

---

## ⚡ Quick Start: Choose Your Experience

| **Option A: Instant Demo (Recommended)** | **Option B: Full System (Developer Mode)** |
| :--- | :--- |
| 🚀 **Run `run_this.html` in any browser.** | 🛠️ **Setup PHP/MySQL Environment.** |
| • Zero setup required.<br>• Simulated real-time sensor data.<br>• Offline-reliable mapping fallback.<br>• **Perfect for evaluation!** | • Fully functional backend logic.<br>• Live database synchronization.<br>• Multi-node simulator integration.<br>• [Setup Directory](./setup_files) |

---

## 🌌 Overview
OptiSpace is a next-generation **Command Center** for airport and smart city parking. It bridges the gap between hardware sensors and GIS-mapped decision support systems. 

Designed for high-stakes environments like **TRV Airport Terminal 2**, the system provides a cinematic "NASA-style" HUD for monitoring hundreds of parking bays with zero latency.

### 🎯 Key Performance Indicators (KPIs)
*   **📡 Digital Twin Precision**: All 277+ slots mapped using sub-meter accuracy coordinates.
*   **🏎️ Predictive Allocation**: AI-driven slot suggestions via real-time data packets.
*   **💳 Automated Revenue HUD**: Live billing ticker and occupancy metrics.
*   **🎥 Integrated Surveillance**: Live CCTV feeds sync with map-layer events.

---

## 🏗️ System Architecture

### 💎 Frontend (The "Glass" Dashboard)
- **Engine**: Leaflet.js with ArcGIS Satellite Layers.
- **Visuals**: Glassmorphism UI, JetBrains Mono typography, pulsing AR overlays.
- **Reliability**: Dual-tier map fallback (ArcGIS → CartoDB Dark).

### ⚙️ Backend (The "Logic" Engine)
- **Core**: PHP 8.x with PDO Security.
- **Database**: MySQL Optimized with indexing for sub-second status queries.
- **Simulator**: Independent node simulating entry/exit events via AJAX.

---

## 📜 Repository Structure

```bash
├── 📄 run_this.html          # STANDALONE DEMO (CLICK ME FIRST)
├── 📄 index.php              # Main Production Dashboard
├── 📄 simulator.php          # Entry/Exit Node Simulator
├── 📄 logic.php              # Smart Space Backend Controller
├── 📁 setup_files            # Complete SQL & Database Dumps
├── 📁 tools                  # API Testing & Mapping Utilities
└── 📁 cctv                   # Real Surveillance Video Assets
```

---

## 📂 Documentation

- 📘 **[Setup Guide](./SETUP_GUIDE.md)**: How to deploy the full system on XAMPP/Apache.
- 📋 **[Evaluation Checklist](./SUBMISSION_CHECKLIST.md)**: Proof of system robustness.
- 👥 **[Team Details](./TEAM_DETAILS.md)**: Contributor information.

---

<div align="center">
  <br>
  <b>Built with ❤️ for Innovation</b><br>
  <i>OptiSpace - Space as a Service. Optimized.</i>
</div>
