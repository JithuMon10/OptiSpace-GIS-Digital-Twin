<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiSpace | Enterprise GIS Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #05080a;
            --panel: rgba(10, 20, 30, 0.9);
            --accent: #00f2ff;
            --danger: #ff3e3e;
            --warning: #ffbe00;
            --success: #00ff88;
            --text: #e0e0e0;
            --border: rgba(0, 242, 255, 0.3);
        }

        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            overflow: hidden;
        }

        .main-container {
            display: flex;
            height: calc(100vh - 30px);
            width: 100%;
        }

        /* Left Panel - Camera Feed */
        .left-panel {
            flex: 0 0 20%;
            background: #000;
            border-right: 2px solid var(--border);
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .feed-header {
            background: var(--panel);
            padding: 10px;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.7rem;
            letter-spacing: 1px;
            border-bottom: 1px solid var(--border);
            color: var(--accent);
            display: flex;
            justify-content: space-between;
        }

        .video-container {
            flex: 1;
            position: relative;
            overflow: hidden;
            background: #111;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: grayscale(0.5) contrast(1.2);
        }

        .scan-line {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background: rgba(0, 242, 255, 0.2);
            box-shadow: 0 0 10px var(--accent);
            animation: scan 4s linear infinite;
            z-index: 5;
            pointer-events: none;
        }

        @keyframes scan {
            from {
                top: 0%
            }

            to {
                top: 100%
            }
        }

        /* Right Panel - GIS Map */
        .right-panel {
            flex: 1;
            position: relative;
        }

        #map {
            height: 100%;
            width: 100%;
            background: #05080a;
        }

        /* Overlay UI */
        .dashboard-overlay {
            position: absolute;
            top: 20px;
            right: 20px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 15px;
            pointer-events: none;
        }

        .stat-card {
            background: var(--panel);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border);
            padding: 15px;
            border-radius: 8px;
            width: 220px;
            pointer-events: auto;
        }

        .stat-card h3 {
            margin: 0;
            font-size: 0.65rem;
            color: #aaa;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-card .value {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.5rem;
            color: var(--accent);
            margin-top: 5px;
        }

        .lidar-btn {
            background: var(--accent);
            color: #000;
            border: none;
            padding: 12px;
            border-radius: 5px;
            font-family: 'Orbitron', sans-serif;
            font-weight: bold;
            font-size: 0.7rem;
            cursor: pointer;
            pointer-events: auto;
            transition: all 0.3s;
            box-shadow: 0 0 15px rgba(0, 242, 255, 0.4);
            text-align: center;
        }

        .lidar-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 0 25px var(--accent);
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
            font-family: 'Orbitron', sans-serif;
            color: var(--accent);
            font-size: 1.2rem;
            background: var(--panel);
            padding: 8px 15px;
            border: 1px solid var(--border);
            letter-spacing: 2px;
        }

        /* Ticker */
        .system-ticker {
            height: 30px;
            background: #000;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            overflow: hidden;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.7rem;
            color: var(--accent);
        }

        .ticker-content {
            display: inline-block;
            white-space: nowrap;
            padding-left: 100%;
            animation: ticker 30s linear infinite;
        }

        @keyframes ticker {
            0% {
                transform: translate(0, 0);
            }

            100% {
                transform: translate(-100%, 0);
            }
        }

        .legend {
            position: absolute;
            bottom: 50px;
            right: 20px;
            z-index: 1000;
            background: var(--panel);
            padding: 12px;
            border-radius: 5px;
            border: 1px solid var(--border);
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
            font-size: 0.7rem;
            color: #fff;
        }

        .dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .dot.free {
            background: var(--success);
        }

        .dot.occupied {
            background: var(--danger);
        }

        .dot.inefficient {
            background: var(--warning);
        }
    </style>
</head>

<body>
    <div class="logo">OPTISPACE <span style="font-weight: 300; opacity: 0.7;">PRO</span></div>

    <div class="main-container">
        <div class="left-panel">
            <div class="feed-header">
                <span>LIVE FEED | CAM-04</span>
                <span style="color: var(--danger);">● REC</span>
            </div>
            <div class="video-container">
                <div class="scan-line"></div>
                <video autoplay muted loop playsinline>
                    <source
                        src="https://assets.mixkit.co/videos/preview/mixkit-top-view-of-a-busy-parking-lot-40439-large.mp4"
                        type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>

        <div class="right-panel">
            <div id="map"></div>

            <div class="dashboard-overlay">
                <div class="stat-card">
                    <h3>Total Revenue</h3>
                    <div class="value" id="revenue">₹0.00</div>
                </div>
                <div class="stat-card">
                    <h3>CO2 Emissions Saved</h3>
                    <div class="value" id="co2">0.00 kg</div>
                </div>
                <button class="lidar-btn" onclick="alert('LiDAR Integration: Authentication Required')">
                    LOAD LiDAR 3D VIEW (NeST CLOUD)
                </button>
            </div>

            <div class="legend">
                <div class="legend-item"><span class="dot free"></span> Free (Available)</div>
                <div class="legend-item"><span class="dot occupied"></span> Occupied (Full)</div>
                <div class="legend-item"><span class="dot inefficient"></span> Inefficient (Zoning Alert)</div>
            </div>
        </div>
    </div>

    <div class="system-ticker">
        <div class="ticker-content">
            CONNECTED TO ESRI ARCGIS SERVICES... SYNCING WITH NeST DATA LAKE... REAL-TIME GIS STATUS: OPTIMAL...
            ENFORCING LOGISTICS ZONE PROTOCOLS... LIDAR SYSTEM STANDBY...
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // ESRI Tile Layers
        const worldImagery = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        });

        const worldLabels = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19
        });

        const map = L.map('map', {
            zoomControl: false,
            attributionControl: false,
            layers: [worldImagery, worldLabels]
        }).setView([10.055, 76.355], 18);

        let polygons = {};

        function getColor(status) {
            switch (status) {
                case 'free': return '#00ff88';
                case 'occupied': return '#ff3e3e';
                case 'inefficient': return '#ffbe00';
                default: return '#888';
            }
        }

        function updateDashboard() {
            fetch('logic.php?action=get_status')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('revenue').innerText = '₹' + parseFloat(data.stats.total_revenue).toLocaleString(undefined, { minimumFractionDigits: 2 });
                    document.getElementById('co2').innerText = parseFloat(data.stats.total_co2).toFixed(2) + ' kg';

                    data.slots.forEach(slot => {
                        const color = getColor(slot.status);
                        const coords = JSON.parse(slot.coordinates);

                        if (polygons[slot.id]) {
                            polygons[slot.id].setStyle({
                                fillColor: color,
                                color: color
                            });
                            polygons[slot.id].getPopup().setContent(`
                                <div style="font-family: 'Orbitron', sans-serif; font-size: 0.8rem;">
                                    <b style="color: var(--accent);">${slot.slot_name}</b><br>
                                    <span style="color: #aaa;">Zone:</span> ${slot.zone_name}<br>
                                    <span style="color: #aaa;">Status:</span> ${slot.status.toUpperCase()}
                                </div>
                            `);
                        } else {
                            const poly = L.polygon(coords, {
                                fillColor: color,
                                fillOpacity: 0.4,
                                color: color,
                                weight: 2,
                                dashArray: '5, 5'
                            }).addTo(map);

                            poly.bindPopup(`
                                <div style="font-family: 'Orbitron', sans-serif; font-size: 0.8rem;">
                                    <b style="color: var(--accent);">${slot.slot_name}</b><br>
                                    <span style="color: #aaa;">Zone:</span> ${slot.zone_name}<br>
                                    <span style="color: #aaa;">Status:</span> ${slot.status.toUpperCase()}
                                </div>
                            `);
                            polygons[slot.id] = poly;
                        }
                    });
                })
                .catch(err => console.error('Dashboard Sync Error:', err));
        }

        setInterval(updateDashboard, 1500);
        updateDashboard();
    </script>
</body>

</html>