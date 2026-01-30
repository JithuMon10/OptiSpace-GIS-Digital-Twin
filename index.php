<?php require 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiSpace | COMMAND CENTER v10.1</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Roboto+Mono&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --accent: #00f2ff;
            --bg: #050709;
            --panel: rgba(13, 17, 23, 0.9);
            --border: rgba(0, 242, 255, 0.3);
            --suv: #FFD700;
            --log: #D000FF;
            --bike: #00FFFF;
            --occ: #FF0000;
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
            backdrop-filter: blur(12px);
            border: 1px solid var(--border);
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.6);
        }

        /* --- TOP HUD BAR --- */
        #header {
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            border-bottom: 2px solid var(--border);
            box-sizing: border-box;
        }

        .brand {
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            letter-spacing: 2px;
            color: var(--accent);
        }

        .status-row {
            display: flex;
            gap: 40px;
        }

        .stat-item {
            text-align: right;
        }

        .stat-val {
            font-family: 'Orbitron';
            font-size: 1rem;
            color: #fff;
            display: block;
        }

        .stat-label {
            font-size: 0.6rem;
            color: #888;
            text-transform: uppercase;
        }

        /* --- RIGHT HUD PANEL --- */
        #sidebar {
            right: 20px;
            top: 80px;
            bottom: 20px;
            width: 300px;
            padding: 25px;
            border-radius: 8px;
            display: flex;
            flex-direction: column;
        }

        .panel-title {
            font-family: 'Orbitron';
            font-size: 0.8rem;
            color: var(--accent);
            border-bottom: 1px solid var(--border);
            padding-bottom: 10px;
            margin-bottom: 20px;
        }

        .zone-table {
            width: 100%;
            font-size: 0.75rem;
            margin-bottom: 30px;
        }

        .zone-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
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
            margin-bottom: 10px;
            border-left: 2px solid var(--accent);
            padding-left: 10px;
            animation: slide 0.3s ease-out;
        }

        @keyframes slide {
            from {
                opacity: 0;
                transform: translateX(10px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* --- CCTV HUD (BOTTOM LEFT) --- */
        #cctv-panel {
            bottom: 20px;
            left: 20px;
            width: 320px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            pointer-events: auto;
        }

        .cam-box {
            position: relative;
            height: 160px;
            background: #000;
            border: 1px solid var(--border);
            border-radius: 6px;
            overflow: hidden;
        }

        .cam-box video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.8) contrast(1.2) grayscale(0.2);
        }

        .cam-rotate {
            transform: rotate(180deg);
        }

        /* Tactical Exit View */

        .cam-tag {
            position: absolute;
            top: 10px;
            left: 10px;
            font-family: 'Orbitron';
            font-size: 0.55rem;
            color: #fff;
            background: rgba(0, 0, 0, 0.6);
            padding: 2px 6px;
            border: 1px solid var(--border);
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 5px;
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

        .terminal-box {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.7);
            border-top: 1px solid var(--border);
            padding: 8px;
            color: #00ff00;
            font-family: 'Courier New', monospace;
            font-size: 0.7rem;
            font-weight: bold;
            z-index: 5;
        }

        .pay-notif {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #00ff00;
            color: #000;
            padding: 10px;
            font-family: 'Orbitron';
            font-size: 0.7rem;
            font-weight: bold;
            display: none;
            z-index: 20;
            box-shadow: 0 0 20px #00ff00;
            white-space: nowrap;
        }

        /* --- ANIMATIONS --- */
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

    <!-- HUD OVERLAYS -->
    <div class="hud glass" id="header">
        <div class="brand">OPTISPACE // COMMAND CENTER</div>
        <div class="status-row">
            <div class="stat-item"><span class="stat-label">DAILY REVENUE</span><span class="stat-val">₹ <span
                        id="rev-total">0</span></span></div>
            <div class="stat-item"><span class="stat-label">SYSTEM STATUS</span><span class="stat-val"
                    style="color:#00ff00; text-shadow: 0 0 5px #00ff00;">● ONLINE</span></div>
        </div>
    </div>

    <div class="hud glass" id="sidebar">
        <div class="panel-title">ZONE ANALYSIS</div>
        <div class="zone-table">
            <div class="zone-row"><span>GENERAL (CAR)</span><span id="c-gen">0 / 0</span></div>
            <div class="zone-row" style="color:var(--suv)"><span>SUV (LARGE)</span><span id="c-suv">0 / 0</span></div>
            <div class="zone-row" style="color:var(--log)"><span>LOGISTICS (HGV)</span><span id="c-log">0 / 0</span>
            </div>
            <div class="zone-row" style="color:var(--bike)"><span>BIKES (CYCLE)</span><span id="c-bike">0 / 0</span>
            </div>
        </div>
        <div class="panel-title" style="margin-bottom:10px">LIVE TELEMETRY</div>
        <div id="event-log"></div>
    </div>

    <!-- CCTV HUD -->
    <div class="hud" id="cctv-panel">
        <!-- TOP: EXIT CAM -->
        <div class="cam-box">
            <div class="cam-tag">
                <div class="rec-dot"></div> CAM-02 [EXIT_LANE]
            </div>
            <div class="pay-notif" id="pay-flash">✔ PAYMENT RECEIVED: ₹150</div>
            <video class="cam-rotate" id="vid-exit" src="cctv/VID2.mp4" autoplay muted loop></video>
            <div class="terminal-box" id="exit-term">> ANALYZING EXIT...</div>
        </div>
        <!-- BOTTOM: ENTRY CAM -->
        <div class="cam-box">
            <div class="cam-tag">
                <div class="rec-dot"></div> CAM-01 [ENTRY_GATE]
            </div>
            <video id="vid-entry" src="cctv/VID1.mp4" autoplay muted loop></video>
            <div class="terminal-box" id="entry-term">> SCANNING TARGET...</div>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Init Map
        const map = L.map('map', {
            center: [8.488533, 76.921948],
            zoom: 19,
            zoomControl: false,
            attributionControl: false,
            scrollWheelZoom: true,
            doubleClickZoom: true,
            touchZoom: true,
            dragging: true
        });
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}').addTo(map);

        const GATE = [8.488533, 76.921948];
        let slotMarkers = {};
        let slotsArray = [];
        let totalRevenue = 0;

        // Force 0.5x Playback
        const vids = [document.getElementById('vid-entry'), document.getElementById('vid-exit')];
        vids.forEach(v => v.playbackRate = 0.5);

        async function init() {
            const res = await fetch('logic.php?action=fetch_status');
            const data = await res.json();
            slotsArray = data.slots;

            data.slots.forEach(s => {
                let color = '#00FF00'; // General
                if (s.zone_type === 'suv') color = '#FFD700';
                if (s.zone_type === 'logistics') color = '#D000FF';
                if (s.zone_type === 'bike') color = '#00FFFF';
                if (s.status !== 'free') color = '#FF0000';

                const m = L.circle([parseFloat(s.lat), parseFloat(s.lng)], {
                    radius: 2.3, fillColor: color, fillOpacity: 0.9, color: color, weight: 1
                }).addTo(map);

                slotMarkers[s.slot_id] = { m, type: s.zone_type };
            });

            updateHUD();
            startAutopilot();
        }

        function updateHUD() {
            document.getElementById('rev-total').innerText = totalRevenue.toLocaleString();
            let zones = { general: { o: 0, t: 0 }, suv: { o: 0, t: 0 }, logistics: { o: 0, t: 0 }, bike: { o: 0, t: 0 } };
            slotsArray.forEach(s => {
                zones[s.zone_type].t++;
                if (s.status !== 'free') zones[s.zone_type].o++;
            });
            document.getElementById('c-gen').innerText = `${zones.general.o} / ${zones.general.t}`;
            document.getElementById('c-suv').innerText = `${zones.suv.o} / ${zones.suv.t}`;
            document.getElementById('c-log').innerText = `${zones.logistics.o} / ${zones.logistics.t}`;
            document.getElementById('c-bike').innerText = `${zones.bike.o} / ${zones.bike.t}`;
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

        function startAutopilot() {
            const eTerm = document.getElementById('entry-term');
            const xTerm = document.getElementById('exit-term');

            // Entry Loop (3s)
            setInterval(async () => {
                eTerm.innerText = "> SCANNING...";
                await new Promise(r => setTimeout(r, 1000));
                eTerm.innerText = "> FITMENT OK...";
                await new Promise(r => setTimeout(r, 1000));
                eTerm.innerText = "> ALLOCATING...";

                const free = slotsArray.filter(s => s.status === 'free');
                if (free.length > 0) {
                    const target = free[Math.floor(Math.random() * free.length)];
                    fireEntryAnimation(target);
                }
            }, 3000);

            // Exit Loop (10s)
            setInterval(async () => {
                xTerm.innerText = "> VEHICLE LEAVING...";
                await new Promise(r => setTimeout(r, 1500));
                xTerm.innerText = "> FEE: CALCULATING...";

                const occupied = slotsArray.filter(s => s.status !== 'free');
                if (occupied.length > 0) {
                    const target = occupied[Math.floor(Math.random() * occupied.length)];
                    fireExitAnimation(target);
                }
            }, 10000);
        }

        function fireEntryAnimation(slot) {
            const start = L.latLng(GATE);
            const end = L.latLng(slot.lat, slot.lng);
            const beam = L.polyline([start, end], { color: '#00f2ff', weight: 2, className: 'laser-beam' }).addTo(map);
            const dot = L.circleMarker(start, { radius: 4, fillColor: '#fff', color: '#00f2ff', fillOpacity: 1 }).addTo(map);

            let p = 0;
            const anim = () => {
                p += 0.05;
                if (p <= 1) {
                    dot.setLatLng([start.lat + (end.lat - start.lat) * p, start.lng + (end.lng - start.lng) * p]);
                    requestAnimationFrame(anim);
                } else {
                    map.removeLayer(beam); map.removeLayer(dot);
                    slotMarkers[slot.slot_id].m.setStyle({ fillColor: '#FF0000', color: '#FF0000' });
                    slot.status = 'occupied';
                    pushLog(`${slot.slot_name} : ALLOCATED`);
                    updateHUD();
                }
            };
            anim();
        }

        function fireExitAnimation(slot) {
            const flash = document.getElementById('pay-flash');
            const xTerm = document.getElementById('exit-term');

            // Map Flash
            const m = slotMarkers[slot.slot_id].m;
            m.setStyle({ fillColor: '#fff', color: '#fff' });

            setTimeout(() => {
                let color = '#00FF00';
                if (slot.zone_type === 'suv') color = '#FFD700';
                if (slot.zone_type === 'logistics') color = '#D000FF';
                if (slot.zone_type === 'bike') color = '#00FFFF';
                m.setStyle({ fillColor: color, color: color });

                slot.status = 'free';
                totalRevenue += 150;

                // Camera Flash
                xTerm.innerText = "> PAYMENT: DONE";
                flash.style.display = 'block';
                setTimeout(() => flash.style.display = 'none', 1500);

                pushLog(`${slot.slot_name} : VACATED (+₹150)`);
                updateHUD();
            }, 1000);
        }

        init();
    </script>
</body>

</html>