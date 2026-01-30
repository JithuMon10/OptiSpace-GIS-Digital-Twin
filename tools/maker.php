<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>OptiSpace | Map Maker v5</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root {
            --sidebar-w: 380px;
            --accent: #00f2ff;
            --bg: #0f172a;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: #000;
            color: #fff;
            overflow: hidden;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        #map {
            flex: 1;
            height: 100%;
            cursor: crosshair;
        }

        .sidebar {
            width: var(--sidebar-w);
            background: var(--bg);
            border-left: 2px solid #334155;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid #334155;
            background: #1e293b;
        }

        .sidebar-header h2 {
            margin: 0;
            font-size: 1rem;
            color: var(--accent);
            letter-spacing: 1px;
        }

        .controls {
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            background: #111827;
        }

        /* Zone Buttons */
        .zone-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 6px;
        }

        .zone-btn {
            background: #1e293b;
            border: 1px solid #475569;
            color: #94a3b8;
            padding: 10px 5px;
            cursor: pointer;
            border-radius: 4px;
            font-weight: bold;
            transition: all 0.2s;
        }

        .zone-btn:hover {
            border-color: var(--accent);
            color: #fff;
            background: #334155;
        }

        .zone-btn.active {
            background: var(--accent);
            color: #000;
            border-color: #fff;
            box-shadow: 0 0 10px var(--accent);
        }

        .input-group {
            display: flex;
            gap: 10px;
        }

        input,
        select {
            background: #020617;
            border: 1px solid #475569;
            color: #fff;
            padding: 10px;
            border-radius: 4px;
            flex: 1;
            font-family: inherit;
            font-size: 0.9rem;
        }

        .slot-list {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
            gap: 10px;
            display: flex;
            flex-direction: column;
        }

        .slot-item {
            background: #1e293b;
            padding: 12px;
            border-radius: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #334155;
        }

        .slot-item:hover {
            border-color: var(--accent);
            background: #26334a;
        }

        .slot-info {
            font-size: 0.85rem;
        }

        .slot-info b {
            color: var(--accent);
            font-size: 0.9rem;
        }

        .delete-btn {
            background: #ef4444;
            color: white;
            border: none;
            padding: 6px 10px;
            cursor: pointer;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }

        .delete-btn:hover {
            background: #dc2626;
        }

        .footer {
            padding: 20px;
            border-top: 1px solid #334155;
            background: #1e293b;
        }

        .download-btn {
            display: block;
            width: 100%;
            background: var(--success, #00ff88);
            color: #000;
            text-decoration: none;
            text-align: center;
            padding: 12px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 0.9rem;
        }

        /* Click Menu */
        #ctx-menu {
            display: none;
            position: absolute;
            z-index: 1001;
            background: #1e293b;
            padding: 18px;
            border-radius: 8px;
            border: 1px solid var(--accent);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
            min-width: 220px;
        }

        #ctx-menu h4 {
            margin: 0 0 12px 0;
            color: var(--accent);
            font-size: 1rem;
            border-bottom: 1px solid #334155;
            padding-bottom: 8px;
        }

        .btn-save {
            width: 100%;
            background: var(--accent);
            border: none;
            padding: 12px;
            border-radius: 4px;
            font-weight: bold;
            cursor: pointer;
            color: #000;
            font-size: 0.9rem;
            margin-top: 10px;
        }

        .btn-save:hover {
            background: #fff;
            box-shadow: 0 0 15px var(--accent);
        }

        /* Vehicle Marker Styles */
        .slot-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 7px;
            font-weight: bold;
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 0 3px rgba(0, 0, 0, 0.5);
            text-shadow: 1px 1px 1px #000;
        }

        .dot-car {
            background: #22c55e;
        }

        /* Green */
        .dot-suv {
            background: #a855f7;
        }

        /* Purple */
        .dot-truck {
            background: #ef4444;
        }

        /* Red */
        .dot-bike {
            background: #eab308;
        }

        /* Yellow */
        .dot-default {
            background: #0ea5e9;
        }

        /* Blue */
    </style>
</head>

<body>

    <div class="container">
        <div id="map"></div>

        <div class="sidebar">
            <div class="sidebar-header">
                <h2>TRV MAPPING PROTOCOL v5.0</h2>
            </div>

            <div class="controls">
                <div
                    style="font-size: 0.75rem; color: #94a3b8; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">
                    Active Zone Selector:</div>
                <div class="zone-grid" id="zone-grid">
                    <!-- A-N Buttons -->
                </div>

                <div class="input-group">
                    <input type="text" id="prefix" placeholder="Prefix" value="A" readonly>
                    <input type="number" id="counter" placeholder="Start" value="1">
                </div>

                <div style="font-size: 0.75rem; color: #94a3b8; font-weight: bold; text-transform: uppercase;">Default
                    Vehicle Type:</div>
                <select id="type-select">
                    <option value="suv">SUV (Premium)</option>
                    <option value="car" selected>Car (General)</option>
                    <option value="truck">Truck (Logistics)</option>
                    <option value="bike">Bike</option>
                </select>
            </div>

            <div class="slot-list" id="slot-list">
                <!-- Items injected here -->
            </div>

            <div class="footer">
                <a href="slots.sql" download class="download-btn">DOWNLOAD slots.sql</a>
            </div>
        </div>
    </div>

    <div id="ctx-menu">
        <h4 id="ctx-title">Add Slot?</h4>
        <label style="font-size: 0.7rem; color: #94a3b8; margin-bottom: 5px; display: block;">Select Vehicle:</label>
        <select id="ctx-type-select" style="margin-bottom: 10px; width: 100%;">
            <option value="suv">SUV</option>
            <option value="car">Car</option>
            <option value="truck">Truck</option>
            <option value="bike">Bike</option>
        </select>
        <button onclick="saveSlot()" class="btn-save">CONFIRM SAVE</button>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const imagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });
        const labels = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });

        const map = L.map('map', {
            center: [8.488000, 76.923000],
            zoom: 19,
            layers: [imagery, labels],
            zoomControl: false
        });

        const menu = document.getElementById('ctx-menu');
        const ctxTitle = document.getElementById('ctx-title');
        const ctxTypeSelect = document.getElementById('ctx-type-select');
        const mainTypeSelect = document.getElementById('type-select');
        const prefixInput = document.getElementById('prefix');
        const counterInput = document.getElementById('counter');

        let activeLatLng = null;
        let markers = {};
        let allSlots = [];

        // Initialize Zone Grid
        const zones = "ABCDEFGHIJKLMN".split("");
        const grid = document.getElementById('zone-grid');
        zones.forEach(z => {
            const btn = document.createElement('button');
            btn.className = 'zone-btn' + (z === 'A' ? ' active' : '');
            btn.id = 'zone-btn-' + z;
            btn.innerText = z;
            btn.onclick = (e) => {
                e.stopPropagation();
                setZone(z);
            };
            grid.appendChild(btn);
        });

        function setZone(z) {
            document.querySelectorAll('.zone-btn').forEach(b => b.classList.toggle('active', b.innerText === z));
            prefixInput.value = z;
            updateCounterForPrefix(z);
        }

        function updateCounterForPrefix(p) {
            let maxCount = 0;
            allSlots.forEach(s => {
                const parts = s.name.split('-');
                if (parts[0] === p) {
                    const c = parseInt(parts[1]);
                    if (!isNaN(c)) maxCount = Math.max(maxCount, c);
                }
            });
            counterInput.value = maxCount + 1;
        }

        async function loadSlots() {
            try {
                const r = await fetch('api.php?action=load');
                const d = await r.json();
                if (d.success) {
                    allSlots = d.slots;
                    renderList(d.slots);
                    drawMarkers(d.slots);
                    updateCounterForPrefix(prefixInput.value);
                }
            } catch (e) { console.error("Load fail", e); }
        }

        function drawMarkers(slots) {
            Object.values(markers).forEach(m => map.removeLayer(m));
            markers = {};
            slots.forEach(s => {
                // Determine vehicle type for coloring
                let typeClass = 'dot-default';
                if (s.zone === 'premium') typeClass = 'dot-suv';
                else if (s.zone === 'general') typeClass = 'dot-car';
                else if (s.zone === 'logistics') typeClass = 'dot-truck';

                // Extract number from name
                const number = s.name.split('-')[1] || '?';

                const icon = L.divIcon({
                    className: 'custom-div-icon',
                    html: `<div class="slot-dot ${typeClass}">${number}</div>`,
                    iconSize: [14, 14],
                    iconAnchor: [7, 7]
                });

                const m = L.marker([s.lat, s.lng], { icon: icon }).addTo(map);
                m.bindPopup(`<b style="color:var(--accent)">${s.name}</b><br>TYPE: ${s.zone.toUpperCase()}`);
                markers[s.name] = m;
            });
        }

        function renderList(slots) {
            const list = document.getElementById('slot-list');
            list.innerHTML = '';
            [...slots].reverse().forEach(s => {
                const div = document.createElement('div');
                div.className = 'slot-item';
                div.innerHTML = `
                <div class="slot-info"><b>${s.name}</b><br><span style="color:#94a3b8; font-size:0.7rem;">TYPE: ${s.zone.toUpperCase()}</span></div>
                <button class="delete-btn" onclick="deleteSlot('${s.name}')">DELETE</button>
            `;
                list.appendChild(div);
            });
        }

        map.on('click', (e) => {
            activeLatLng = e.latlng;
            const prefix = prefixInput.value;
            const count = counterInput.value;
            ctxTitle.innerText = `NEW SLOT: ${prefix}-${count}`;
            ctxTypeSelect.value = mainTypeSelect.value;

            menu.style.display = 'block';
            menu.style.left = e.containerPoint.x + 'px';
            menu.style.top = e.containerPoint.y + 'px';
        });

        map.on('movestart', () => { menu.style.display = 'none'; });

        async function saveSlot() {
            const prefix = prefixInput.value;
            const count = counterInput.value;
            const type = ctxTypeSelect.value;
            const name = `${prefix}-${count}`;

            mainTypeSelect.value = type;

            let zone = 'general';
            if (type === 'truck') zone = 'logistics';
            else if (type === 'suv') zone = 'premium';
            else if (type === 'bike') zone = 'general';

            const fd = new FormData();
            fd.append('lat', activeLatLng.lat); fd.append('lng', activeLatLng.lng);
            fd.append('name', name); fd.append('type', type); fd.append('zone', zone);

            try {
                const r = await fetch('api.php?action=save', { method: 'POST', body: fd });
                const d = await r.json();
                if (d.success) {
                    menu.style.display = 'none';
                    await loadSlots();
                }
            } catch (e) { alert("Save failed"); }
        }

        async function deleteSlot(name) {
            if (!confirm(`Delete ${name}?`)) return;
            const fd = new FormData(); fd.append('name', name);
            try {
                const r = await fetch('api.php?action=delete', { method: 'POST', body: fd });
                const d = await r.json();
                if (d.success) await loadSlots();
            } catch (e) { alert("Delete failed"); }
        }

        loadSlots();
    </script>
</body>

</html>