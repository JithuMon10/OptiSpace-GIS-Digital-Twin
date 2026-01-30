<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiSpace | Digital Twin Dashboard</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #0a0e14;
            --panel: rgba(10, 14, 20, 0.85);
            --accent: #00f2ff;
            --text: #e0e0e0;
        }

        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            overflow: hidden;
        }

        #map {
            height: 100vh;
            width: 100%;
            z-index: 1;
        }

        .dashboard-header {
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1000;
            display: flex;
            gap: 20px;
            pointer-events: none;
        }

        .stat-card {
            background: var(--panel);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 15px 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            min-width: 200px;
        }

        .stat-card h3 {
            margin: 0;
            font-size: 0.8rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .stat-card .value {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.8rem;
            color: var(--accent);
            margin-top: 5px;
        }

        .logo {
            position: absolute;
            top: 20px;
            left: 20px;
            z-index: 1000;
            font-family: 'Orbitron', sans-serif;
            color: var(--accent);
            font-size: 1.5rem;
            letter-spacing: 2px;
            background: var(--panel);
            padding: 10px 20px;
            border-radius: 10px;
            border: 1px solid rgba(0, 242, 255, 0.2);
        }

        .legend {
            position: absolute;
            bottom: 30px;
            right: 20px;
            z-index: 1000;
            background: var(--panel);
            padding: 15px;
            border-radius: 10px;
            color: #fff;
            font-size: 0.8rem;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 5px;
        }

        .dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
        }

        .dot.free {
            background: #4caf50;
        }

        .dot.occupied {
            background: #f44336;
        }

        .dot.inefficient {
            background: #ff9800;
        }
    </style>
</head>

<body>

    <div class="logo">OPTISPACE // TWIN</div>

    <div class="dashboard-header">
        <div class="stat-card">
            <h3>Total Revenue</h3>
            <div class="value" id="revenue">₹0.00</div>
        </div>
        <div class="stat-card">
            <h3>CO2 Saved</h3>
            <div class="value" id="co2">0.00 kg</div>
        </div>
    </div>

    <div class="legend">
        <div class="legend-item"><span class="dot free"></span> Free</div>
        <div class="legend-item"><span class="dot occupied"></span> Occupied</div>
        <div class="legend-item"><span class="dot inefficient"></span> Inefficient</div>
    </div>

    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // Initialize Map
        const map = L.map('map', {
            zoomControl: false,
            attributionControl: false
        }).setView([10.0005, 76.0002], 19);

        // Dark Theme Layer
        L.tileLayer('https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png', {
            maxZoom: 21
        }).addTo(map);

        let polygons = {};

        function getColor(status) {
            switch (status) {
                case 'free': return '#4caf50';
                case 'occupied': return '#f44336';
                case 'inefficient': return '#ff9800';
                default: return '#888';
            }
        }

        function updateDashboard() {
            fetch('logic.php?action=get_status')
                .then(response => response.json())
                .then(data => {
                    // Update Stats
                    document.getElementById('revenue').innerText = '₹' + parseFloat(data.stats.total_revenue).toFixed(2);
                    document.getElementById('co2').innerText = parseFloat(data.stats.total_co2).toFixed(2) + ' kg';

                    // Update Slots
                    data.slots.forEach(slot => {
                        const color = getColor(slot.status);

                        if (polygons[slot.id]) {
                            polygons[slot.id].setStyle({
                                fillColor: color,
                                color: color
                            });
                            polygons[slot.id].getPopup().setContent(`<b>${slot.slot_name}</b><br>Status: ${slot.status.toUpperCase()}`);
                        } else {
                            const poly = L.polygon(slot.coordinates, {
                                fillColor: color,
                                fillOpacity: 0.6,
                                color: color,
                                weight: 2
                            }).addTo(map);

                            poly.bindPopup(`<b>${slot.slot_name}</b><br>Status: ${slot.status.toUpperCase()}`);
                            polygons[slot.id] = poly;
                        }
                    });
                })
                .catch(err => console.error('Polling error:', err));
        }

        // Initial setup and interval
        updateDashboard();
        setInterval(updateDashboard, 1000);
    </script>
</body>

</html>