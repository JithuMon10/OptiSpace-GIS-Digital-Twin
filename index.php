<?php
require 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiSpace | Smart City Command Center</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #050709;
            --accent: #00f2ff;
            --glass: rgba(13, 17, 23, 0.85);
            --neon-border: rgba(0, 242, 255, 0.3);
            --danger: #ff0000;
            --success: #00ff00;
            --suv: #ffd700;
            --logistics: #d000ff;
            --bike: #00ffff;
            --warning: #ffa500;
        }

        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
            background: var(--bg);
            font-family: 'Roboto Mono', monospace;
            overflow: hidden;
            color: #fff;
        }

        #map {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1;
            background: #000;
        }

        /* --- UI OVERLAY ELEMENTS --- */
        .hud {
            position: absolute;
            z-index: 1000;
            pointer-events: none;
        }

        .hud-panel {
            pointer-events: auto;
            background: var(--glass);
            backdrop-filter: blur(12px);
            border: 1px solid var(--neon-border);
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }

        /* --- HEADER HUD --- */
        .header-hud {
            top: 0;
            left: 0;
            width: 100%;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            box-sizing: border-box;
            border-bottom: 2px solid var(--neon-border);
        }

        .brand {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.2rem;
            letter-spacing: 2px;
            color: var(--accent);
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .brand .tag {
            font-size: 0.6rem;
            background: var(--accent);
            color: #000;
            padding: 2px 6px;
            border-radius: 3px;
            font-weight: bold;
        }

        .vitals {
            display: flex;
            gap: 40px;
            align-items: center;
        }

        .vital-item {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .vital-label {
            font-size: 0.6rem;
            color: #888;
            text-transform: uppercase;
        }

        .vital-value {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.95rem;
            color: #fff;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: var(--success);
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            box-shadow: 0 0 10px var(--success);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.4; }
            100% { opacity: 1; }
        }

        /* --- RIGHT HUD PANEL --- */
        .sidebar-hud {
            right: 25px;
            top: 95px;
            width: 320px;
            bottom: 25px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px;
            border-radius: 10px;
        }

        .section-title {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.75rem;
            color: var(--accent);
            border-bottom: 1px solid var(--neon-border);
            padding-bottom: 8px;
            margin-bottom: 15px;
            text-transform: uppercase;
        }

        .zone-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .zone-card {
            background: rgba(255,255,255,0.05);
            padding: 12px;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 4px;
        }

        .zone-card .name { font-size: 0.65rem; color: #888; margin-bottom: 5px; }
        .zone-card .count { font-family: 'Orbitron', sans-serif; font-size: 0.9rem; }

        .log-container {
            flex: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        #event-log {
            font-size: 0.65rem;
            color: #ccc;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .log-entry {
            border-left: 2px solid var(--accent);
            padding-left: 8px;
            background: rgba(0, 242, 255, 0.05);
            padding: 5px 8px;
        }

        .log-time { color: var(--accent); font-weight: bold; margin-right: 5px; }

        /* --- CCTV HUD --- */
        .cctv-hud {
            bottom: 40px;
            left: 25px;
            width: 340px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid var(--neon-border);
        }

        .cctv-title {
            background: var(--neon-border);
            padding: 8px 15px;
            font-size: 0.65rem;
            font-family: 'Orbitron', sans-serif;
            display: flex;
            justify-content: space-between;
        }

        .cctv-hud video {
            width: 100%;
            display: block;
            filter: grayscale(0.5) contrast(1.2);
        }

        /* --- MAP OVERRIDES --- */
        .leaflet-popup-content-wrapper {
            background: var(--glass) !important;
            backdrop-filter: blur(8px);
            border: 1px solid var(--accent);
            color: #fff !important;
            border-radius: 0 !important;
        }
        .leaflet-popup-tip { background: var(--accent) !important; }

    </style>
</head>
<body>

    <div id="map"></div>

    <!-- HUD OVERLAYS -->
    <div class="hud hud-panel header-hud">
        <div class="brand">
            <span class="tag">AI-SEC</span>
            OPTISPACE // TRV T2
        </div>
        <div class="vitals">
            <div class="vital-item">
                <span class="vital-label">Occupancy</span>
                <span class="vital-value" id="occupancy-val">0 / 0 (0%)</span>
            </div>
            <div class="vital-item">
                <span class="vital-label">Revenue Flow</span>
                <span class="vital-value" id="revenue-val">₹ 0.00</span>
            </div>
            <div class="vital-item">
                <span class="vital-label">CO2 Mitigated</span>
                <span class="vital-value" id="co2-val">0.0 kg</span>
            </div>
            <div class="vital-item">
                <span class="vital-label">System State</span>
                <span class="vital-value"><span class="status-dot"></span> ONLINE</span>
            </div>
        </div>
    </div>

    <div class="hud hud-panel sidebar-hud">
        <div class="zone-section">
            <div class="section-title">Zone Fitment Status</div>
            <div class="zone-grid">
                <div class="zone-card">
                    <div class="name">🚗 General</div>
                    <div class="count" id="z-general">0 / 0</div>
                </div>
                <div class="zone-card">
                    <div class="name" style="color:var(--suv)">🚙 SUV (Large)</div>
                    <div class="count" id="z-suv">0 / 0</div>
                </div>
                <div class="zone-card">
                    <div class="name" style="color:var(--bike)">🏍️ Bike</div>
                    <div class="count" id="z-bike">0 / 0</div>
                </div>
                <div class="zone-card">
                    <div class="name" style="color:var(--logistics)">🚚 Logistics</div>
                    <div class="count" id="z-logistics">0 / 0</div>
                </div>
            </div>
        </div>

        <div class="log-container">
            <div class="section-title">Live Node Telemetry</div>
            <div id="event-log">
                <div class="log-entry"><span class="log-time">SYS</span> Establishing node connection...</div>
            </div>
        </div>
    </div>

    <div class="hud hud-panel cctv-hud">
        <div class="cctv-title">
            <span>LIVE // GATE_CAM_01</span>
            <span style="color: red;">● REC</span>
        </div>
        <video src="parking_loop.mp4" autoplay muted loop></video>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Init Map
        const worldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });
        const worldLabels = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });

        const map = L.map('map', {
            center: [8.488000, 76.923000],
            zoom: 19,
            zoomControl: false,
            attributionControl: false,
            layers: [worldImagery, worldLabels]
        });

        let slotMarkers = {};
        let lastStates = {}; // To track changes for log

        function addLog(msg) {
            const log = document.getElementById('event-log');
            const entry = document.createElement('div');
            entry.className = 'log-entry';
            const time = new Date().toLocaleTimeString([], { hour12: false });
            entry.innerHTML = `<span class="log-time">${time}</span> ${msg}`;
            log.prepend(entry);
            if (log.children.length > 30) log.lastChild.remove();
        }

        async function updateDashboard() {
            try {
                const response = await fetch('logic.php?action=fetch_status');
                const data = await response.json();

                if (!data || data.status !== 'success') return;

                // 1. Stats Calculation
                const total = data.slots.length;
                let occupied = 0;
                let zones = { general: {o:0, t:0}, suv: {o:0, t:0}, bike: {o:0, t:0}, logistics: {o:0, t:0} };

                data.slots.forEach(slot => {
                    const z = slot.zone_type;
                    if (zones[z]) {
                        zones[z].t++;
                        if (slot.status !== 'free') {
                            zones[z].o++;
                            occupied++;
                        }
                    }

                    // Log detection
                    if (lastStates[slot.slot_id] && lastStates[slot.slot_id] !== slot.status) {
                        const action = slot.status === 'free' ? 'freed' : 'occupied';
                        addLog(`${slot.slot_id} is now ${action.toUpperCase()}`);
                    }
                    lastStates[slot.slot_id] = slot.status;

                    // Map Rendering
                    let color = '#00FF00'; // Success Green
                    if (slot.status === 'occupied') color = '#FF0000';
                    else if (slot.status === 'inefficient') color = '#FFA500';
                    else {
                        if (z === 'suv') color = '#FFD700';
                        else if (z === 'logistics') color = '#D000FF';
                        else if (z === 'bike') color = '#00FFFF';
                    }

                    if (slotMarkers[slot.slot_id]) {
                        slotMarkers[slot.slot_id].setStyle({ fillColor: color, color: color });
                    } else {
                        const m = L.circle([parseFloat(slot.lat), parseFloat(slot.lng)], {
                            radius: 2.2,
                            fillColor: color,
                            fillOpacity: 0.7,
                            color: color,
                            weight: 1.5
                        }).addTo(map);

                        m.bindPopup(`
                            <div style="font-family:'Orbitron'; font-size:0.8rem; line-height:1.4;">
                                <b>${slot.slot_name}</b><br>
                                Zone: ${z.toUpperCase()}<br>
                                Status: ${slot.status.toUpperCase()}
                            </div>
                        `);
                        slotMarkers[slot.slot_id] = m;
                    }
                });

                // Update HUD
                const pct = total > 0 ? Math.round((occupied / total) * 100) : 0;
                document.getElementById('occupancy-val').innerText = `${occupied} / ${total} (${pct}%)`;
                document.getElementById('revenue-val').innerText = `₹ ${data.stats.revenue}`;
                document.getElementById('co2-val').innerText = `${data.stats.co2_saved} kg`;

                // Update Sector breakdown
                document.getElementById('z-general').innerText = `${zones.general.o} / ${zones.general.t}`;
                document.getElementById('z-suv').innerText = `${zones.suv.o} / ${zones.suv.t}`;
                document.getElementById('z-bike').innerText = `${zones.bike.o} / ${zones.bike.t}`;
                document.getElementById('z-logistics').innerText = `${zones.logistics.o} / ${zones.logistics.t}`;

            } catch (err) { console.error("HUD Sync Error:", err); }
        }

        setInterval(updateDashboard, 2000);
        updateDashboard();
        addLog("AI-SEC Protocols Engaged.");
    </script>
</body>
</html>