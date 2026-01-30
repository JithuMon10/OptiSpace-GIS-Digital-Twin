<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OptiSpace | Map Maker v2</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        :root { --sidebar-w: 350px; --accent: #00f2ff; --bg: #0f172a; }
        body, html { margin: 0; padding: 0; height: 100%; font-family: 'Inter', sans-serif; background: #000; color: #fff; overflow: hidden; }
        
        .container { display: flex; height: 100vh; }
        #map { flex: 1; height: 100%; cursor: crosshair; }
        
        .sidebar { width: var(--sidebar-w); background: var(--bg); border-left: 2px solid #334155; display: flex; flex-direction: column; }
        .sidebar-header { padding: 20px; border-bottom: 1px solid #334155; background: #1e293b; }
        .sidebar-header h2 { margin: 0; font-size: 1.2rem; color: var(--accent); }
        
        .controls { padding: 20px; display: flex; flex-direction: column; gap: 10px; }
        .input-group { display: flex; gap: 10px; }
        input, select { background: #020617; border: 1px solid #475569; color: #fff; padding: 8px; border-radius: 4px; flex: 1; }
        
        .slot-list { flex: 1; overflow-y: auto; padding: 10px; }
        .slot-item { background: #1e293b; margin-bottom: 8px; padding: 10px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center; border: 1px solid transparent; }
        .slot-item:hover { border-color: var(--accent); }
        .slot-info { font-size: 0.85rem; }
        .slot-info b { color: var(--accent); }
        .delete-btn { background: #ef4444; color: white; border: none; padding: 4px 8px; cursor: pointer; border-radius: 4px; font-size: 12px; }
        .delete-btn:hover { background: #b91c1c; }
        
        .footer { padding: 20px; border-top: 1px solid #334155; }
        .download-btn { display: block; width: 100%; background: var(--accent); color: #000; text-decoration: none; text-align: center; padding: 10px; border-radius: 4px; font-weight: bold; }

        /* Context Menu Styles */
        #ctx-menu {
            display: none;
            position: absolute;
            z-index: 1001;
            background: #1e293b;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--accent);
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
            min-width: 180px;
        }
    </style>
</head>
<body>

<div class="container">
    <div id="map"></div>
    
    <div class="sidebar">
        <div class="sidebar-header">
            <h2>MAP MAKER V2</h2>
        </div>
        
        <div class="controls">
            <div class="input-group">
                <input type="text" id="prefix" placeholder="Prefix" value="A">
                <input type="number" id="counter" placeholder="Start" value="1">
            </div>
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
            <a href="slots.sql" download class="download-btn">DOWNLOAD SQL</a>
        </div>
    </div>
</div>

<div id="ctx-menu">
    <div style="font-size: 0.9rem; margin-bottom: 10px;" id="ctx-info">Add Slot?</div>
    <button onclick="saveSlot()" style="width:100%; background:var(--accent); border:none; padding:8px; border-radius:4px; font-weight:bold; cursor:pointer;">SAVE SLOT</button>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const imagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 });
    const map = L.map('map', { center: [8.488000, 76.923000], zoom: 19, layers: [imagery] });

    const menu = document.getElementById('ctx-menu');
    const ctxInfo = document.getElementById('ctx-info');
    let activeLatLng = null;
    let markers = {};

    // Initial Load
    async function loadSlots() {
        try {
            const r = await fetch('api.php?action=load');
            const d = await r.json();
            if (d.success) {
                renderList(d.slots);
                drawMarkers(d.slots);
            }
        } catch (e) { console.error("Load fail", e); }
    }

    function drawMarkers(slots) {
        // Clear existing markers
        Object.values(markers).forEach(m => map.removeLayer(m));
        markers = {};

        slots.forEach(s => {
            const m = L.circle([s.lat, s.lng], {
                radius: 0.8,
                color: 'cyan',
                weight: 2,
                fillOpacity: 0.4
            }).addTo(map);
            m.bindPopup(`<b>${s.name}</b><br>${s.zone.toUpperCase()}`);
            markers[s.name] = m;
        });
    }

    function renderList(slots) {
        const list = document.getElementById('slot-list');
        list.innerHTML = '';
        slots.reverse().forEach(s => {
            const div = document.createElement('div');
            div.className = 'slot-item';
            div.innerHTML = `
                <div class="slot-info">
                    <b>${s.name}</b><br>
                    <span style="color:#94a3b8; font-size:0.7rem;">${s.zone}</span>
                </div>
                <button class="delete-btn" onclick="deleteSlot('${s.name}')">X</button>
            `;
            list.appendChild(div);
        });
    }

    map.on('contextmenu', (e) => {
        activeLatLng = e.latlng;
        const prefix = document.getElementById('prefix').value;
        const count = document.getElementById('counter').value;
        ctxInfo.innerText = `Add Slot ${prefix}-${count}?`;
        
        menu.style.display = 'block';
        menu.style.left = e.containerPoint.x + 'px';
        menu.style.top = e.containerPoint.y + 'px';
    });

    map.on('click', () => { menu.style.display = 'none'; });

    async function saveSlot() {
        const prefix = document.getElementById('prefix').value;
        const count = document.getElementById('counter').value;
        const type = document.getElementById('type-select').value;
        const name = `${prefix}-${count}`;
        
        // Zoning Logic
        let zone = 'general';
        if (type === 'truck') zone = 'logistics';
        else if (type === 'suv') zone = 'premium';

        const fd = new FormData();
        fd.append('lat', activeLatLng.lat);
        fd.append('lng', activeLatLng.lng);
        fd.append('name', name);
        fd.append('type', type);
        fd.append('zone', zone);

        try {
            const r = await fetch('api.php?action=save', { method: 'POST', body: fd });
            const d = await r.json();
            if (d.success) {
                // Increment counter
                document.getElementById('counter').value = parseInt(count) + 1;
                menu.style.display = 'none';
                loadSlots(); // Refresh
            }
        } catch (e) { alert("Save failed"); }
    }

    async function deleteSlot(name) {
        if (!confirm(`Delete ${name}?`)) return;
        const fd = new FormData();
        fd.append('name', name);
        try {
            const r = await fetch('api.php?action=delete', { method: 'POST', body: fd });
            const d = await r.json();
            if (d.success) loadSlots();
        } catch (e) { alert("Delete failed"); }
    }

    loadSlots();
</script>
</body>
</html>