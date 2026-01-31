<?php require 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Parking System | OptiSpace</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            /* Muted Color Palette */
            --color-free: #22c55e;
            --color-occupied: #dc2626;
            --color-bike: #eab308;
            --color-general: #3b82f6;
            --color-suv: #a855f7;
            --color-logistics: #a855f7;
            --color-active: #06b6d4;
            --color-warning: #f97316;

            /* Theme */
            --bg-primary: #0a0e1a;
            --bg-secondary: #151b2e;
            --glass: rgba(15, 20, 35, 0.85);
            --border: rgba(100, 116, 139, 0.2);
            --text-primary: #e2e8f0;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;

            /* Spacing Grid */
            --gap-xs: 4px;
            --gap-sm: 8px;
            --gap-md: 16px;
            --gap-lg: 24px;
            --gap-xl: 32px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html,
        body {
            height: 100%;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 14px;
        }

        /* MAP BASE */
        #map {
            position: absolute;
            inset: 0;
            z-index: 1;
            /* 3D Transform */
            transform: perspective(1200px) rotateX(25deg) scale(1.1);
            /* Depth Shadows */
            box-shadow: inset 0 0 150px rgba(0, 0, 0, 0.9);
        }

        /* GLASS PANELS */
        .glass-panel {
            background: var(--glass);
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        /* TOP STATUS BAR */
        #status-bar {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 var(--gap-lg);
            background: var(--glass);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border);
            z-index: 1000;
        }

        .status-bar-section {
            display: flex;
            align-items: center;
            gap: var(--gap-lg);
        }

        .app-title {
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: -0.02em;
        }

        .location-pill {
            padding: 6px 14px;
            background: rgba(6, 182, 212, 0.12);
            border: 1px solid rgba(6, 182, 212, 0.3);
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            color: var(--color-active);
        }

        .status-metric {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 2px;
        }

        .status-metric-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 16px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .status-metric-label {
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
        }

        .system-status {
            display: flex;
            align-items: center;
            gap: var(--gap-sm);
            font-size: 12px;
            font-weight: 500;
            color: var(--color-free);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: var(--color-free);
            border-radius: 50%;
            box-shadow: 0 0 8px var(--color-free);
            animation: breathe 2s ease-in-out infinite;
        }

        @keyframes breathe {

            0%,
            100% {
                opacity: 1;
                transform: scale(1);
            }

            50% {
                opacity: 0.6;
                transform: scale(0.9);
            }
        }

        /* LEFT OPERATIONS PANEL */
        #operations-panel {
            position: fixed;
            top: 72px;
            left: var(--gap-md);
            width: 340px;
            max-height: calc(100vh - 180px);
            padding: var(--gap-lg);
            z-index: 900;
            overflow-y: auto;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        #operations-panel::-webkit-scrollbar {
            width: 4px;
        }

        #operations-panel::-webkit-scrollbar-track {
            background: transparent;
        }

        #operations-panel::-webkit-scrollbar-thumb {
            background: var(--border);
            border-radius: 2px;
        }

        .panel-section {
            margin-bottom: var(--gap-lg);
        }

        .section-title {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: var(--gap-md);
        }

        /* METRIC CARDS */
        .metrics-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--gap-md);
        }

        .metric-card {
            padding: var(--gap-md);
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid var(--border);
            border-radius: 6px;
            text-align: center;
            transition: all 0.3s ease;
        }

        .metric-card:hover {
            border-color: var(--color-active);
            box-shadow: 0 0 12px rgba(6, 182, 212, 0.15);
        }

        .metric-label {
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            margin-bottom: var(--gap-sm);
        }

        .metric-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 32px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .metric-value.green {
            color: var(--color-free);
        }

        .metric-value.red {
            color: var(--color-occupied);
        }

        /* CAMERA FEEDS */
        .camera-feed {
            position: relative;
            height: 140px;
            background: #000;
            border-radius: 6px;
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: var(--gap-md);
            transition: all 0.3s ease;
        }

        .camera-feed:hover {
            border-color: var(--color-active);
            box-shadow: 0 0 16px rgba(6, 182, 212, 0.2);
        }

        .camera-feed video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.9) contrast(1.05);
        }

        .cam-rotate {
            transform: rotate(180deg);
        }

        .camera-header {
            position: absolute;
            top: 8px;
            left: 8px;
            right: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 5;
        }

        .camera-label {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 4px 10px;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 4px;
            font-family: 'JetBrains Mono', monospace;
            font-size: 10px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .rec-indicator {
            width: 6px;
            height: 6px;
            background: #ef4444;
            border-radius: 50%;
            animation: pulse-rec 1.5s ease-in-out infinite;
        }

        @keyframes pulse-rec {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.3;
            }
        }

        .live-badge {
            padding: 4px 8px;
            background: rgba(6, 182, 212, 0.2);
            border: 1px solid var(--color-active);
            border-radius: 4px;
            font-size: 10px;
            font-weight: 600;
            color: var(--color-active);
            animation: pulse-live 1.5s ease-in-out infinite;
        }

        @keyframes pulse-live {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .camera-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px 12px;
            background: rgba(0, 0, 0, 0.8);
            font-family: 'JetBrains Mono', monospace;
            font-size: 11px;
            font-weight: 500;
            color: var(--color-free);
            text-shadow: 0 0 8px rgba(34, 197, 94, 0.5);
            z-index: 5;
        }

        .payment-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 8px 16px;
            background: var(--color-free);
            color: #000;
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            font-weight: 700;
            border-radius: 4px;
            box-shadow: 0 0 24px var(--color-free);
            display: none;
            z-index: 10;
        }

        /* AVAILABILITY LIST */
        .availability-list {
            display: flex;
            flex-direction: column;
            gap: var(--gap-sm);
        }

        .availability-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--gap-sm) 0;
            font-size: 13px;
        }

        .availability-label {
            display: flex;
            align-items: center;
            gap: var(--gap-sm);
            color: var(--text-secondary);
        }

        .color-bullet {
            width: 10px;
            height: 10px;
            border-radius: 2px;
        }

        .availability-count {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* SYSTEM HEALTH */
        .health-list {
            display: flex;
            flex-direction: column;
            gap: var(--gap-sm);
        }

        .health-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--gap-sm) 0;
            font-size: 12px;
        }

        .health-label {
            color: var(--text-secondary);
        }

        .health-status {
            display: flex;
            align-items: center;
            gap: 6px;
            font-weight: 500;
            color: var(--color-free);
        }

        .health-status::before {
            content: '';
            width: 6px;
            height: 6px;
            background: var(--color-free);
            border-radius: 50%;
            box-shadow: 0 0 6px var(--color-free);
        }

        /* RIGHT PANEL */
        #right-panel {
            position: fixed;
            top: 72px;
            right: var(--gap-md);
            width: 280px;
            max-height: calc(100vh - 180px);
            padding: var(--gap-lg);
            z-index: 900;
            display: flex;
            flex-direction: column;
        }

        .revenue-display {
            text-align: center;
            padding: var(--gap-lg);
            background: rgba(6, 182, 212, 0.08);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 8px;
            margin-bottom: var(--gap-lg);
        }

        .revenue-label {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: var(--gap-sm);
        }

        .revenue-value {
            font-family: 'JetBrains Mono', monospace;
            font-size: 36px;
            font-weight: 700;
            color: var(--color-active);
        }

        .event-log {
            flex: 1;
            overflow-y: auto;
            margin-top: var(--gap-md);
        }

        .log-entry {
            padding: var(--gap-sm) 0;
            padding-left: var(--gap-sm);
            border-left: 2px solid var(--color-active);
            margin-bottom: var(--gap-sm);
            font-size: 11px;
            color: var(--text-secondary);
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(8px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .log-time {
            color: var(--color-active);
            font-family: 'JetBrains Mono', monospace;
        }

        /* BOTTOM FOOTER */
        #legend-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: auto;
            padding: var(--gap-sm) var(--gap-xl);
            display: grid;
            grid-template-columns: 280px 1fr 280px;
            gap: var(--gap-xl);
            align-items: center;
            background: var(--glass);
            backdrop-filter: blur(12px);
            border-top: 1px solid var(--border);
            z-index: 1000;
        }

        .footer-section {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .footer-title {
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--text-muted);
            margin-bottom: 4px;
        }

        .legend-grid {
            display: flex;
            gap: var(--gap-lg);
            justify-content: center;
            align-items: center;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 10px;
            color: var(--text-secondary);
        }

        .footer-list {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }

        .footer-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 10px;
        }

        .footer-item-label {
            display: flex;
            align-items: center;
            gap: 5px;
            color: var(--text-secondary);
        }

        .footer-item-value {
            font-family: 'JetBrains Mono', monospace;
            font-weight: 600;
            color: var(--text-primary);
        }

        .footer-status {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 10px;
            color: var(--color-free);
        }

        .footer-status::before {
            content: '';
            width: 5px;
            height: 5px;
            background: var(--color-free);
            border-radius: 50%;
            box-shadow: 0 0 4px var(--color-free);
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 2px;
        }

        /* MAP SLOT ANIMATIONS */
        .leaflet-interactive {
            transition: fill 0.3s ease, stroke 0.3s ease !important;
        }

        /* DATA PACKET */
        .data-trail {
            opacity: 0.3;
        }

        .data-packet {
            filter: drop-shadow(0 0 6px #fff) drop-shadow(0 0 10px var(--color-active));
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <!-- TOP STATUS BAR -->
    <div id="status-bar">
        <div class="status-bar-section">
            <div class="app-title">Smart Parking System</div>
        </div>
        <div class="status-bar-section">
            <div class="location-pill">TRV Airport Parking Lot</div>
        </div>
        <div class="status-bar-section">
            <div class="status-metric">
                <div class="status-metric-value">₹ <span id="header-revenue">0</span></div>
                <div class="status-metric-label">Revenue Today</div>
            </div>
            <div class="status-metric">
                <div class="status-metric-value"><span id="header-util">0</span>%</div>
                <div class="status-metric-label">Utilization</div>
            </div>
            <div class="system-status">
                <div class="status-dot"></div>
                SYSTEM ONLINE
            </div>
        </div>
    </div>

    <!-- LEFT OPERATIONS PANEL -->
    <div id="operations-panel" class="glass-panel">
        <!-- SECTION A: METRICS -->
        <div class="panel-section">
            <div class="section-title">Live Metrics</div>
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-label">Free Slots</div>
                    <div class="metric-value green" id="free-count">0</div>
                </div>
                <div class="metric-card">
                    <div class="metric-label">Occupied</div>
                    <div class="metric-value red" id="occ-count">0</div>
                </div>
            </div>
        </div>

        <!-- SECTION B: CAMERAS -->
        <div class="panel-section">
            <div class="section-title">Camera Feeds</div>

            <!-- CAM-01 ENTRY -->
            <div class="camera-feed">
                <div class="camera-header">
                    <div class="camera-label">
                        <div class="rec-indicator"></div>
                        CAM-01 FRONT
                    </div>
                    <div class="live-badge">LIVE</div>
                </div>
                <video id="vid-entry" src="cctv/VID1.mp4" autoplay muted loop></video>
                <div class="camera-overlay" id="entry-overlay">> SCANNING...</div>
            </div>

            <!-- CAM-02 EXIT -->
            <div class="camera-feed">
                <div class="camera-header">
                    <div class="camera-label">
                        <div class="rec-indicator"></div>
                        CAM-02 REAR
                    </div>
                    <div class="live-badge">LIVE</div>
                </div>
                <div class="payment-overlay" id="payment-flash">✔ PAYMENT RECEIVED</div>
                <video class="cam-rotate" id="vid-exit" src="cctv/VID2.mp4" autoplay muted loop></video>
                <div class="camera-overlay" id="exit-overlay">> MONITORING...</div>
            </div>
        </div>
    </div>
    </div>

    <!-- RIGHT PANEL -->
    <div id="right-panel" class="glass-panel">
        <div class="section-title">Revenue Tracker</div>
        <div class="revenue-display">
            <div class="revenue-label">Today's Revenue</div>
            <div class="revenue-value">₹ <span id="revenue">0</span></div>
        </div>

        <div class="section-title">Live Event Log</div>
        <div class="event-log" id="event-log"></div>
    </div>

    <!-- BOTTOM FOOTER -->
    <div id="legend-bar">
        <!-- LEFT: AVAILABILITY -->
        <div class="footer-section">
            <div class="footer-title">Availability by Type</div>
            <div class="footer-list">
                <div class="footer-item">
                    <div class="footer-item-label">
                        <div class="color-bullet" style="background: var(--color-bike)"></div>
                        Bike Zone
                    </div>
                    <div class="footer-item-value" id="avail-bike">0</div>
                </div>
                <div class="footer-item">
                    <div class="footer-item-label">
                        <div class="color-bullet" style="background: var(--color-general)"></div>
                        General
                    </div>
                    <div class="footer-item-value" id="avail-gen">0</div>
                </div>
                <div class="footer-item">
                    <div class="footer-item-label">
                        <div class="color-bullet" style="background: var(--color-suv)"></div>
                        SUV / Premium
                    </div>
                    <div class="footer-item-value" id="avail-suv">0</div>
                </div>
            </div>
        </div>

        <!-- CENTER: LEGEND -->
        <div class="footer-section">
            <div class="legend-grid">
                <div class="legend-item">
                    <div class="legend-color" style="background: var(--color-free)"></div>
                    Free
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: var(--color-occupied)"></div>
                    Occupied
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: var(--color-bike)"></div>
                    Bike
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: var(--color-general)"></div>
                    General
                </div>
                <div class="legend-item">
                    <div class="legend-color" style="background: var(--color-suv)"></div>
                    SUV
                </div>
            </div>
        </div>

        <!-- RIGHT: SYSTEM HEALTH -->
        <div class="footer-section" style="align-items: flex-end;">
            <div class="footer-title" style="text-align: right;">System Health</div>
            <div class="footer-list">
                <div class="footer-item">
                    <div class="footer-item-label">Gate Sensors</div>
                    <div class="footer-status">Online</div>
                </div>
                <div class="footer-item">
                    <div class="footer-item-label">CCTV Array</div>
                    <div class="footer-status">2 / 2 Active</div>
                </div>
                <div class="footer-item">
                    <div class="footer-item-label">AI Detection</div>
                    <div class="footer-status">Running</div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const map = L.map('map', {
            center: [8.488533, 76.921948],
            zoom: 19,
            zoomControl: false,
            attributionControl: false,
            scrollWheelZoom: true,
            doubleClickZoom: false
        });

        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}').addTo(map);

        const GATE = [8.488533, 76.921948];
        let slotMarkers = {};
        let slotsData = [];
        let totalRevenue = 0;

        // Force 0.5x playback
        document.getElementById('vid-entry').playbackRate = 0.5;
        document.getElementById('vid-exit').playbackRate = 0.5;

        async function init() {
            const res = await fetch('logic.php?action=fetch_status');
            const data = await res.json();
            slotsData = data.slots;

            data.slots.forEach(s => {
                let color = s.status === 'free' ?
                    (s.zone_type === 'suv' ? '#a855f7' : s.zone_type === 'bike' ? '#eab308' : s.zone_type === 'logistics' ? '#a855f7' : '#22c55e')
                    : '#dc2626';

                const m = L.circle([parseFloat(s.lat), parseFloat(s.lng)], {
                    radius: 2.5,
                    fillColor: color,
                    fillOpacity: 0.85,
                    color: color,
                    weight: 1
                }).addTo(map);

                slotMarkers[s.slot_id] = { m, type: s.zone_type };
            });

            updateHUD();
            startAutopilot();
            pushLog('System initialized successfully');
        }

        function updateHUD() {
            const free = slotsData.filter(s => s.status === 'free');
            const occ = slotsData.filter(s => s.status !== 'free');
            const util = slotsData.length > 0 ? Math.round((occ.length / slotsData.length) * 100) : 0;

            animateCounter('free-count', free.length);
            animateCounter('occ-count', occ.length);

            document.getElementById('header-revenue').innerText = totalRevenue.toLocaleString();
            document.getElementById('header-util').innerText = util;
            document.getElementById('revenue').innerText = totalRevenue.toLocaleString();

            document.getElementById('avail-bike').innerText = free.filter(s => s.zone_type === 'bike').length;
            document.getElementById('avail-suv').innerText = free.filter(s => s.zone_type === 'suv').length;
            document.getElementById('avail-gen').innerText = free.filter(s => s.zone_type === 'general').length;
        }

        function animateCounter(id, target) {
            const el = document.getElementById(id);
            const current = parseInt(el.innerText) || 0;
            if (current === target) return;

            const step = target > current ? 1 : -1;
            let value = current;
            const interval = setInterval(() => {
                value += step;
                el.innerText = value;
                if (value === target) clearInterval(interval);
            }, 30);
        }

        function pushLog(msg) {
            const log = document.getElementById('event-log');
            const entry = document.createElement('div');
            entry.className = 'log-entry';
            const time = new Date().toLocaleTimeString([], { hour12: false });
            entry.innerHTML = `<span class="log-time">[${time}]</span> ${msg}`;
            log.prepend(entry);
            if (log.children.length > 20) log.lastChild.remove();
        }

        function startAutopilot() {
            const entryOverlay = document.getElementById('entry-overlay');
            const exitOverlay = document.getElementById('exit-overlay');

            // Entry Loop
            setInterval(async () => {
                entryOverlay.innerText = "> SCANNING...";
                await sleep(700);
                const types = ['SUV', 'SEDAN', 'BIKE', 'HATCHBACK'];
                const vehicleType = types[Math.floor(Math.random() * types.length)];
                entryOverlay.innerText = `> CLASS: ${vehicleType}`;
                await sleep(700);
                entryOverlay.innerText = "> ALLOCATING SLOT...";

                const free = slotsData.filter(s => s.status === 'free');
                if (free.length > 0) {
                    const target = free[Math.floor(Math.random() * free.length)];
                    fireDataPacket(target);
                }
            }, 3000);

            // Exit Loop
            setInterval(async () => {
                exitOverlay.innerText = "> VEHICLE DETECTED...";
                await sleep(1000);
                exitOverlay.innerText = "> CALCULATING FEE...";
                await sleep(1000);

                const occ = slotsData.filter(s => s.status !== 'free');
                if (occ.length > 0) {
                    const target = occ[Math.floor(Math.random() * occ.length)];
                    handleExit(target);
                }
            }, 10000);
        }

        function sleep(ms) {
            return new Promise(r => setTimeout(r, ms));
        }

        function fireDataPacket(slot) {
            const start = L.latLng(GATE);
            const end = L.latLng(slot.lat, slot.lng);
            const trail = L.polyline([start, start], {
                color: '#06b6d4',
                weight: 1,
                className: 'data-trail'
            }).addTo(map);

            const packet = L.circleMarker(start, {
                radius: 3,
                fillColor: '#fff',
                color: '#fff',
                fillOpacity: 1,
                className: 'data-packet'
            }).addTo(map);

            const startTime = performance.now();
            const duration = 1200;

            const animate = (time) => {
                const p = Math.min((time - startTime) / duration, 1);
                const pos = [
                    start.lat + (end.lat - start.lat) * p,
                    start.lng + (end.lng - start.lng) * p
                ];
                packet.setLatLng(pos);
                trail.setLatLngs([start, pos]);

                if (p < 1) {
                    requestAnimationFrame(animate);
                } else {
                    map.removeLayer(trail);
                    map.removeLayer(packet);

                    // Smooth color transition
                    slotMarkers[slot.slot_id].m.setStyle({
                        fillColor: '#dc2626',
                        color: '#dc2626'
                    });

                    slot.status = 'occupied';
                    pushLog(`${slot.slot_name} allocated successfully`);
                    updateHUD();
                }
            };
            requestAnimationFrame(animate);
        }

        function handleExit(slot) {
            const flash = document.getElementById('payment-flash');
            const exitOverlay = document.getElementById('exit-overlay');
            const m = slotMarkers[slot.slot_id].m;

            // White flash on map
            m.setStyle({ fillColor: '#fff', color: '#fff' });

            setTimeout(() => {
                let color = slot.zone_type === 'suv' ? '#a855f7' :
                    slot.zone_type === 'bike' ? '#eab308' :
                        slot.zone_type === 'logistics' ? '#a855f7' : '#22c55e';

                m.setStyle({ fillColor: color, color: color });
                slot.status = 'free';
                totalRevenue += 150;

                exitOverlay.innerText = "> PAYMENT: ₹150 OK";
                flash.style.display = 'block';
                setTimeout(() => flash.style.display = 'none', 1500);

                pushLog(`${slot.slot_name} vacated • Payment ₹150 received`);
                updateHUD();
            }, 1000);
        }

        init();
    </script>
</body>

</html>