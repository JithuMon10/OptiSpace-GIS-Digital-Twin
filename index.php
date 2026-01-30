<?php
require 'db_connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiSpace | Command Center - Auto-Pilot v7.9</title>
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

        /* --- CCTV HUD (Fixed Location) --- */
        .cctv-hud {
            bottom: 20px;
            left: 20px;
            width: 360px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid var(--neon-border);
            position: absolute;
        }

        .cctv-title {
            background: var(--neon-border);
            padding: 8px 15px;
            font-size: 0.65rem;
            font-family: 'Orbitron', sans-serif;
            display: flex;
            justify-content: space-between;
            position: relative;
            z-index: 5;
        }

        .cctv-view {
            position: relative;
            height: 200px;
            background: #000;
            overflow: hidden;
        }

        .cctv-hud video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
            filter: grayscale(0.2) contrast(1.1);
        }

        /* --- SURVEILLANCE OVERLAYS --- */
        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: var(--accent);
            box-shadow: 0 0 15px var(--accent);
            z-index: 10;
            animation: scan 3.5s linear infinite;
            display: none;
        }

        .bounding-box {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 140px;
            height: 90px;
            border: 2px solid var(--success);
            box-shadow: 0 0 10px var(--success);
            z-index: 10;
            display: none;
        }

        .ai-log-overlay {
            position: absolute;
            bottom: 10px;
            left: 10px;
            right: 10px;
            background: rgba(0,0,0,0.85);
            border: 1px solid var(--accent);
            padding: 8px;
            font-size: 0.55rem;
            color: var(--accent);
            z-index: 15;
            display: none;
            max-height: 80px;
            overflow: hidden;
            font-family: 'Roboto Mono', monospace;
        }

        @keyframes scan {
            0% { top: 0; }
            50% { top: 100%; }
            100% { top: 0; }
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

        /* Beam Effect Animation */
        .beam-path {
            stroke-dasharray: 8;
            animation: dash 1s linear infinite;
        }

        @keyframes dash {
            to { stroke-dashoffset: -16; }
        }
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

    <!-- SIDEBAR -->
    <div class="hud hud-panel sidebar-hud">
        <div class="zone-section">
            <div class="section-title">Fleet Fitment Status</div>
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
            <div class="section-title">Field Node Activity</div>
            <div id="event-log">
                <div class="log-entry"><span class="log-time">SYS</span> Establishing node connection...</div>
            </div>
        </div>
    </div>

    <!-- CCTV HUD WITH AUTO-PILOT AI -->
    <div class="hud hud-panel cctv-hud">
        <div class="cctv-title">
            <span>LIVE // GATE_SURVEILLANCE_01</span>
            <span style="color: red; font-weight: bold;">● REC</span>
        </div>
        <div class="cctv-view">
            <div class="scan-line" id="laser-scan"></div>
            <div class="bounding-box" id="ai-box"></div>
            <div class="ai-log-overlay" id="ai-text-log"></div>
            <video id="ai-player" src="cctv/VID1.mp4" autoplay muted></video>
        </div>
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

        const ENTRANCE_GATE = [8.488533, 76.921948];
        let slotMarkers = {};
        let lastStates = {}; // Stores latest captured status data
        let currentSlotsArray = []; // List of all slot objects
        let isLoopRunning = false;
        let activeVideo = 1;

        // --- SURVEILLANCE SYNC & LOOP ---
        const player = document.getElementById('ai-player');
        player.playbackRate = 0.75;
        player.onended = () => {
            activeVideo = activeVideo === 1 ? 2 : 1;
            player.src = `cctv/VID${activeVideo}.mp4`;
            player.playbackRate = 0.75;
            player.play();
        };

        // Continuous Loop Trigger
        async function runAutoPilotLoop() {
            if (isLoopRunning) return;
            
            // Wait for 10-15 seconds before starting the next visual cycle
            await new Promise(r => setTimeout(r, 10000 + Math.random() * 5000));
            
            // Find a random free slot
            const freeSlots = currentSlotsArray.filter(s => s.status === 'free');
            if (freeSlots.length > 0) {
                const targetSlot = freeSlots[Math.floor(Math.random() * freeSlots.length)];
                await triggerCCTVSurveillance(targetSlot, true);
            }
            
            runAutoPilotLoop();
        }

        async function triggerCCTVSurveillance(slot, isSimulation = false) {
            isLoopRunning = true;
            const logPanel = document.getElementById('ai-text-log');
            const scanLine = document.getElementById('laser-scan');
            const aiBox = document.getElementById('ai-box');
            
            logPanel.style.display = 'block';
            scanLine.style.display = 'block';
            logPanel.innerHTML = "> INCOMING VEHICLE DETECTED...<br>> ANALYZING GEOMETRY...";
            
            await new Promise(r => setTimeout(r, 3000));
            
            aiBox.style.display = 'block';
            logPanel.innerHTML += `<br>> CLASS: SCANNING...`;

            await new Promise(r => setTimeout(r, 2500));
            
            const vClasses = ['SUV (Fitment: Large)', 'CAR (Fitment: Std)', 'BIKE (Fitment: Compact)', 'TRUCK (Fitment: Heavy)'];
            const vClass = vClasses[Math.floor(Math.random() * vClasses.length)];
            const tagID = "FASTag-" + Math.floor(1000 + Math.random() * 8999).toString(16).toUpperCase();
            logPanel.innerHTML += `<br>> RESULT: ${vClass}<br>> ID: ${tagID}`;

            await new Promise(r => setTimeout(r, 2500));
            
            logPanel.innerHTML += `<br>> ALLOCATING NODE ${slot.slot_id}...`;
            
            // FIRE DATA BEAM
            fireDataBeam(slot, isSimulation);

            await new Promise(r => setTimeout(r, 3000));
            
            scanLine.style.display = 'none';
            aiBox.style.display = 'none';
            logPanel.style.display = 'none';
            isLoopRunning = false;
        }

        function fireDataBeam(slot, isSimulation = false) {
            const start = L.latLng(ENTRANCE_GATE);
            const end = L.latLng(parseFloat(slot.lat), parseFloat(slot.lng));

            const beam = L.polyline([start, end], {
                color: '#00f2ff', weight: 2, opacity: 0.6, dashArray: '5, 10', className: 'beam-path'
            }).addTo(map);

            const projectile = L.circleMarker(start, {
                radius: 5, fillColor: '#fff', fillOpacity: 1, color: '#00f2ff', weight: 2
            }).addTo(map);

            const duration = 800;
            const startTime = performance.now();

            function animate(time) {
                const elapsed = time - startTime;
                const progress = Math.min(elapsed / duration, 1);
                const currentLat = start.lat + (end.lat - start.lat) * progress;
                const currentLng = start.lng + (end.lng - start.lng) * progress;
                projectile.setLatLng([currentLat, currentLng]);
                
                if (progress < 1) requestAnimationFrame(animate);
                else {
                    map.removeLayer(beam);
                    map.removeLayer(projectile);
                    
                    if (isSimulation) {
                        // Just flash the slot to show impact but don't change status
                        if (slotMarkers[slot.slot_id]) {
                            const originalColor = getSlotColor(slot);
                            slotMarkers[slot.slot_id].setStyle({ fillColor: '#fff', color: '#fff' });
                            setTimeout(() => {
                                slotMarkers[slot.slot_id].setStyle({ fillColor: originalColor, color: originalColor });
                            }, 500);
                        }
                    } else {
                        applySlotStatusUpdate(slot);
                    }
                    addLog(`${slot.slot_id} : NODE SIGNAL SYNCED.`);
                }
            }
            requestAnimationFrame(animate);
        }

        function getSlotColor(slot) {
            // STRICT COLOR MATRIX (v7.9)
            if (slot.status === 'occupied') return '#FF0000';
            if (slot.status === 'inefficient') return '#FFA500';
            if (slot.zone_type === 'suv') return '#FFD700'; // Gold
            if (slot.zone_type === 'logistics') return '#D000FF'; // Purple
            if (slot.zone_type === 'bike') return '#00FFFF'; // Cyan
            return '#00FF00'; // Green
        }

        function applySlotStatusUpdate(slot) {
            const color = getSlotColor(slot);
            if (slotMarkers[slot.slot_id]) {
                slotMarkers[slot.slot_id].setStyle({ fillColor: color, color: color });
            }
        }

        function addLog(msg) {
            const log = document.getElementById('event-log');
            const entry = document.createElement('div');
            entry.className = 'log-entry';
            const time = new Date().toLocaleTimeString([], { hour12: false });
            entry.innerHTML = `<span class="log-time">${time}</span> ${msg}`;
            log.prepend(entry);
            if (log.children.length > 20) log.lastChild.remove();
        }

        async function updateDashboard() {
            try {
                const response = await fetch('logic.php?action=fetch_status');
                const data = await response.json();
                if (!data || data.status !== 'success') return;

                currentSlotsArray = data.slots;
                const total = data.slots.length;
                let occ = 0;
                let zones = { general: {o:0, t:0}, suv: {o:0, t:0}, bike: {o:0, t:0}, logistics: {o:0, t:0} };

                data.slots.forEach(slot => {
                    const z = slot.zone_type;
                    if (zones[z]) {
                        zones[z].t++;
                        if (slot.status !== 'free') {
                            zones[z].o++;
                            occ++;
                        }
                    }

                    // REAL SENSOR TRIGGER
                    if (lastStates[slot.slot_id] === 'free' && slot.status !== 'free') {
                        triggerCCTVSurveillance(slot, false);
                    } else if (lastStates[slot.slot_id] && lastStates[slot.slot_id] !== 'free' && slot.status === 'free') {
                        applySlotStatusUpdate(slot);
                        addLog(`${slot.slot_id} : DISCONNECT.`);
                    } else {
                        applySlotStatusUpdate(slot);
                    }
                    
                    lastStates[slot.slot_id] = slot.status;

                    if (!slotMarkers[slot.slot_id]) {
                        const m = L.circle([parseFloat(slot.lat), parseFloat(slot.lng)], {
                            radius: 2.2, fillColor: getSlotColor(slot), fillOpacity: 0.7, color: getSlotColor(slot), weight: 1.5
                        }).addTo(map);
                        m.bindPopup(`<div style="font-family:'Orbitron'; font-size: 0.7rem;"><b>${slot.slot_name}</b><br>Zone: ${z.toUpperCase()}</div>`);
                        slotMarkers[slot.slot_id] = m;
                    }
                });

                document.getElementById('occupancy-val').innerText = `${occ} / ${total} (${Math.round((occ/total)*100)}%)`;
                document.getElementById('revenue-val').innerText = `₹ ${data.stats.revenue}`;
                document.getElementById('co2-val').innerText = `${data.stats.co2_saved} kg`;
                document.getElementById('z-general').innerText = `${zones.general.o} / ${zones.general.t}`;
                document.getElementById('z-suv').innerText = `${zones.suv.o} / ${zones.suv.t}`;
                document.getElementById('z-bike').innerText = `${zones.bike.o} / ${zones.bike.t}`;
                document.getElementById('z-logistics').innerText = `${zones.logistics.o} / ${zones.logistics.t}`;
            } catch (err) { console.error("Data Sync Fail."); }
        }

        setInterval(updateDashboard, 2000);
        updateDashboard().then(() => {
            // Start Auto-Pilot loop after initial data load
            runAutoPilotLoop();
        });
        addLog("AI-SEC SYSTEM READY.");
    </script>
</body>
</html>