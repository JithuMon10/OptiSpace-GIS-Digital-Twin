<?php require 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiSpace | COMMAND CENTER v10.0</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto+Mono&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --accent: #00f2ff;
            --bg: #050709;
            --panel: rgba(13, 17, 23, 0.9);
            --border: rgba(0, 242, 255, 0.4);
        }

        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Roboto Mono', monospace;
            background: var(--bg);
            color: #fff;
            overflow: hidden;
        }

        #map {
            position: absolute;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            z-index: 1;
            pointer-events: none;
        }

        .hud {
            position: absolute;
            z-index: 1000;
            pointer-events: none;
        }

        .glass {
            pointer-events: auto;
            background: var(--panel);
            backdrop-filter: blur(15px);
            border: 1px solid var(--border);
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.6);
            border-radius: 4px;
        }

        /* Top Bar HUD */
        #top-bar {
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            border-radius: 0;
            border: none;
            border-bottom: 2px solid var(--border);
            box-sizing: border-box;
        }

        .brand {
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            letter-spacing: 3px;
            color: var(--accent);
            font-size: 1.1rem;
        }

        .top-stats {
            display: flex;
            gap: 40px;
        }

        .stat-item {
            text-align: right;
        }

        .stat-val {
            font-family: 'Orbitron';
            color: #fff;
            display: block;
            font-size: 1rem;
        }

        .stat-label {
            font-size: 0.6rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Right Panel HUD */
        #right-panel {
            right: 25px;
            top: 85px;
            bottom: 25px;
            width: 300px;
            padding: 25px;
            display: flex;
            flex-direction: column;
        }

        .panel-heading {
            font-family: 'Orbitron', sans-serif;
            font-size: 0.75rem;
            color: var(--accent);
            border-bottom: 1px solid var(--border);
            padding-bottom: 12px;
            margin-bottom: 20px;
            letter-spacing: 1px;
        }

        .zone-stats {
            margin-bottom: 30px;
        }

        .zone-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.75rem;
        }

        #event-log {
            flex: 1;
            overflow-y: auto;
            font-size: 0.65rem;
            color: #aaa;
            border-top: 1px solid var(--border);
            padding-top: 15px;
        }

        .log-entry {
            margin-bottom: 8px;
            border-left: 2px solid var(--accent);
            padding-left: 10px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* CCTV HUD (Bottom Left) - PRESERVED FROM v9.2 */
        #cctv-panel {
            bottom: 25px;
            left: 25px;
            width: 320px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 2000;
        }

        .cam-box {
            position: relative;
            height: 160px;
            background: #000;
            border: 2px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
        }

        .cam-box video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(0.2) contrast(1.1);
        }

        .cam-rotate {
            transform: rotate(180deg);
        }

        .cam-tag {
            position: absolute;
            top: 10px;
            left: 10px;
            font-family: 'Orbitron';
            font-size: 0.55rem;
            color: #fff;
            background: rgba(0, 0, 0, 0.6);
            padding: 2px 8px;
            border-radius: 4px;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .rec-dot {
            width: 6px;
            height: 6px;
            background: #ff0000;
            border-radius: 50%;
            animation: blink 1s infinite;
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.2;
            }
        }

        .terminal-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            padding: 8px;
            border-top: 1px solid var(--border);
            z-index: 5;
        }

        .terminal-text {
            font-family: 'Courier New', Courier, monospace;
            color: #00ff00;
            font-size: 0.7rem;
            font-weight: bold;
            text-shadow: 0 0 5px rgba(0, 255, 0, 0.5);
        }

        /* Map Animations */
        .laser-beam {
            stroke-dasharray: 8;
            animation: dash 0.8s linear infinite;
        }

        @keyframes dash {
            to {
                stroke-dashoffset: -16;
            }
        }
    </style>
</head>

<body>
    <div id="map"></div>

    <!-- TOP BAR -->
    <div class="hud glass" id="top-bar">
        <div class="brand">OPTISPACE // COMMAND CENTER</div>
        <div class="top-stats">
            <div class="stat-item"><span class="stat-label">DAILY REVENUE</span><span class="stat-val"
                    id="hud-revenue">₹ 0.00</span></div>
            <div class="stat-item"><span class="stat-label">SITE OCCUPANCY</span><span class="stat-val"
                    id="hud-occupancy">0 / 0</span></div>
            <div class="stat-item"><span class="stat-label">SYSTEM STATUS</span><span class="stat-val"
                    style="color: #00ff00; text-shadow: 0 0 5px #00ff00;">● ONLINE</span></div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="hud glass" id="right-panel">
        <div class="panel-heading">ZONE TELEMETRY</div>
        <div class="zone-stats">
            <div class="zone-row"><span>🚗 GENERAL</span><span id="z-general">0/0</span></div>
            <div class="zone-row" style="color: #FFD700"><span>🚙 SUV (LARGE)</span><span id="z-suv">0/0</span></div>
            <div class="zone-row" style="color: #00FFFF"><span>🏍️ BIKES (COMPACT)</span><span id="z-bike">0/0</span>
            </div>
            <div class="zone-row" style="color: #D000FF"><span>🚚 LOGISTICS</span><span id="z-logistics">0/0</span>
            </div>
        </div>
        <div class="panel-heading" style="margin-bottom: 10px;">LIVE EVENT LOG</div>
        <div id="event-log"></div>
    </div>

    <!-- CCTV HUD (PRESERVED) -->
    <div class="hud" id="cctv-panel">
        <div class="cam-box">
            <div class="cam-tag">
                <div class="rec-dot"></div> CAM-02 [EXIT_LANE]
            </div>
            <div class="terminal-overlay">
                <div class="terminal-text" id="exit-text">> ANALYZING_EXIT...</div>
            </div>
            <video class="cam-rotate" id="vid-exit" src="cctv/VID2.mp4" autoplay muted loop></video>
        </div>
        <div class="cam-box">
            <div class="cam-tag">
                <div class="rec-dot"></div> CAM-01 [ENTRY_GATE]
            </div>
            <div class="terminal-overlay">
                <div class="terminal-text" id="entry-text">> SCANNING_TARGET...</div>
            </div>
            <video id="vid-entry" src="cctv/VID1.mp4" autoplay muted loop></video>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Init Map - Locked and Focused
        const map = L.map('map', {
            center: [8.488533, 76.921948],
            zoom: 20,
            zoomControl: false,
            attributionControl: false,
            scrollWheelZoom: false,
            doubleClickZoom: false,
            touchZoom: false,
            dragging: false
        });
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}').addTo(map);

        let slotMarkers = {};
        let currentSlots = [];
        const ENTRANCE = [8.488533, 76.921948];

        async function initDashboard() {
            const res = await fetch('logic.php?action=fetch_status');
            const data = await res.json();
            currentSlots = data.slots;

            data.slots.forEach(s => {
                let color = (s.status === 'free') ?
                    (s.zone_type === 'suv' ? '#FFD700' : s.zone_type === 'logistics' ? '#D000FF' : s.zone_type === 'bike' ? '#00FFFF' : '#00ff00') : '#FF0000';

                const m = L.circle([s.lat, s.lng], {
                    radius: 2.3,
                    fillColor: color,
                    fillOpacity: 0.9,
                    color: color,
                    weight: 1
                }).addTo(map);

                slotMarkers[s.slot_id] = { marker: m, zone: s.zone_type };
            });

            updateHUD(data);
            startPresentationLoops();
        }

        function updateHUD(data) {
            document.getElementById('hud-revenue').innerText = '₹ ' + data.stats.revenue;
            document.getElementById('hud-occupancy').innerText = data.stats.total_entries + ' / ' + data.slots.length;

            let zones = { general: { o: 0, t: 0 }, suv: { o: 0, t: 0 }, bike: { o: 0, t: 0 }, logistics: { o: 0, t: 0 } };
            data.slots.forEach(s => {
                zones[s.zone_type].t++;
                if (s.status !== 'free') zones[s.zone_type].o++;
            });
            for (let z in zones) {
                const el = document.getElementById('z-' + z);
                if (el) el.innerText = zones[z].o + ' / ' + zones[z].t;
            }
        }

        function pushLog(msg) {
            const logBox = document.getElementById('event-log');
            const entry = document.createElement('div');
            entry.className = 'log-entry';
            const time = new Date().toLocaleTimeString([], { hour12: false });
            entry.innerHTML = `<span style="color:var(--accent)">[${time}]</span> ${msg}`;
            logBox.prepend(entry);
            if (logBox.children.length > 20) logBox.lastChild.remove();
        }

        // --- AUTONOMOUS PRESENTATION LOOPS ---
        function startPresentationLoops() {
            // Force 0.5x Speed on CCTV
            const vEntry = document.getElementById('vid-entry');
            const vExit = document.getElementById('vid-exit');
            if (vEntry) vEntry.playbackRate = 0.5;
            if (vExit) vExit.playbackRate = 0.5;

            const entryMsgs = ["> SCANNING...", "> FITMENT: OK", "> ALLOCATING..."];
            const exitMsgs = ["> VEHICLE LEAVING...", "> FEE: CALCULATING", "> PAYMENT: DONE"];

            // Entry Loop (Every 3s)
            setInterval(async () => {
                const term = document.getElementById('entry-text');
                term.innerText = entryMsgs[0];
                await new Promise(r => setTimeout(r, 1000));
                term.innerText = entryMsgs[1];
                await new Promise(r => setTimeout(r, 1000));
                term.innerText = entryMsgs[2];

                const freeSpots = currentSlots.filter(s => s.status === 'free');
                if (freeSpots.length > 0) {
                    const target = freeSpots[Math.floor(Math.random() * freeSpots.length)];
                    animateAllocation(target);
                }
            }, 3000);

            // Exit Loop (Every 5s)
            setInterval(async () => {
                const term = document.getElementById('exit-text');
                const occSpots = currentSlots.filter(s => s.status !== 'free');
                if (occSpots.length > 0) {
                    term.innerText = exitMsgs[0];
                    await new Promise(r => setTimeout(r, 1500));
                    term.innerText = exitMsgs[1];
                    await new Promise(r => setTimeout(r, 1500));
                    term.innerText = exitMsgs[2];

                    const target = occSpots[Math.floor(Math.random() * occSpots.length)];
                    animateVacation(target);
                } else {
                    term.innerText = "> STANDBY_MODE";
                }
            }, 5000);
        }

        function animateAllocation(slot) {
            const start = L.latLng(ENTRANCE);
            const end = L.latLng(slot.lat, slot.lng);
            const beam = L.polyline([start, end], { color: '#00f2ff', weight: 2, className: 'laser-beam' }).addTo(map);
            const bolt = L.circleMarker(start, { radius: 4, fillColor: '#fff', color: '#00f2ff', fillOpacity: 1 }).addTo(map);

            let p = 0;
            const anim = () => {
                p += 0.05;
                if (p <= 1) {
                    bolt.setLatLng([start.lat + (end.lat - start.lat) * p, start.lng + (end.lng - start.lng) * p]);
                    requestAnimationFrame(anim);
                } else {
                    map.removeLayer(beam); map.removeLayer(bolt);
                    slotMarkers[slot.slot_id].marker.setStyle({ fillColor: '#FF0000', color: '#FF0000' });
                    slot.status = 'occupied';
                    pushLog(`NODE ${slot.slot_id} : ALLOCATED`);
                    syncStats();
                }
            };
            anim();
        }

        function animateVacation(slot) {
            const m = slotMarkers[slot.slot_id].marker;
            m.setStyle({ fillColor: '#fff', color: '#fff' });

            setTimeout(() => {
                let color = (slot.zone_type === 'suv' ? '#FFD700' : slot.zone_type === 'logistics' ? '#D000FF' : slot.zone_type === 'bike' ? '#00FFFF' : '#00ff00');
                m.setStyle({ fillColor: color, color: color });
                slot.status = 'free';
                pushLog(`NODE ${slot.slot_id} : VACATED (+₹150)`);
                syncStats();
            }, 1000);
        }

        async function syncStats() {
            const res = await fetch('logic.php?action=fetch_status');
            const data = await res.json();
            updateHUD(data);
        }

        window.onload = initDashboard;
    </script>
</body>

</html>