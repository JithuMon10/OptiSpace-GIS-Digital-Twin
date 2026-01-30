<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiSpace | SOC Command Dashboard v6</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #05080a;
            --panel: rgba(10, 20, 30, 0.9);
            --accent: #00f2ff;
            --danger: #ff0000;
            --warning: #ffa500;
            --success: #00ff00;
            --premium: #ffd700;
            --logistics: #ff00ff;
            --border: rgba(0, 242, 255, 0.3);
        }

        body,
        html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: #fff;
            overflow: hidden;
        }

        .main-container {
            display: flex;
            height: calc(100vh - 30px);
            width: 100%;
        }

        /* Left Panel - CCTV */
        .left-panel {
            flex: 0 0 20%;
            background: #000;
            border-right: 2px solid var(--border);
            display: flex;
            flex-direction: column;
        }

        .feed-header {
            background: var(--panel);
            padding: 10px;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.7rem;
            color: var(--accent);
            border-bottom: 1px solid var(--border);
        }

        .video-box {
            flex: 1;
            position: relative;
            overflow: hidden;
        }

        video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.6;
            filter: grayscale(0.5);
        }

        .scan-line {
            position: absolute;
            top: 0;
            width: 100%;
            height: 2px;
            background: rgba(0, 242, 255, 0.2);
            box-shadow: 0 0 10px var(--accent);
            animation: scan 4s linear infinite;
            pointer-events: none;
        }

        @keyframes scan {
            from {
                top: 0
            }

            to {
                top: 100%
            }
        }

        /* Right Panel - GIS */
        .right-panel {
            flex: 1;
            position: relative;
        }

        #map {
            height: 100%;
            width: 100%;
            background: #000;
        }

        /* Overlays */
        .branding {
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 1000;
            font-family: 'Orbitron', sans-serif;
            font-size: 1.2rem;
            color: var(--accent);
            background: var(--panel);
            padding: 10px 20px;
            border: 1px solid var(--border);
            letter-spacing: 2px;
            box-shadow: 0 0 20px rgba(0, 242, 255, 0.2);
        }

        .telemetry {
            position: absolute;
            top: 15px;
            right: 15px;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .stat-card {
            background: var(--panel);
            border: 1px solid var(--border);
            padding: 12px;
            border-radius: 4px;
            width: 200px;
            backdrop-filter: blur(5px);
        }

        .stat-card h3 {
            margin: 0;
            font-size: 0.6rem;
            color: #888;
            text-transform: uppercase;
        }

        .stat-card .val {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.4rem;
            color: var(--accent);
            margin-top: 5px;
        }

        .ticker {
            height: 30px;
            background: #000;
            border-top: 1px solid var(--border);
            display: flex;
            align-items: center;
            overflow: hidden;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.65rem;
            color: var(--accent);
        }

        .ticker-wrap {
            white-space: nowrap;
            padding-left: 100%;
            animation: ticker 40s linear infinite;
        }

        @keyframes ticker {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .legend {
            position: absolute;
            bottom: 30px;
            right: 20px;
            z-index: 1000;
            background: var(--panel);
            padding: 15px;
            border: 1px solid var(--border);
            font-size: 0.75rem;
            border-radius: 4px;
        }

        .l-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .box {
            width: 12px;
            height: 12px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
    </style>
</head>

<body>
    <div class="branding">OPTISPACE // SOC DASHBOARD</div>

    <div class="main-container">
        <div class="left-panel">
            <div class="feed-header">LIVE FEED | CAM-01 | ENTRANCE</div>
            <div class="video-box">
                <div class="scan-line"></div>
                <!-- Note: Ensure parking_loop.mp4 exists in root -->
                <video autoplay muted loop playsinline src="parking_loop.mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>

        <div class="right-panel">
            <div id="map"></div>

            <div class="telemetry">
                <div class="stat-card">
                    <h3>Operational Revenue</h3>
                    <div class="val" id="revenue">₹0.00</div>
                </div>
                <div class="stat-card">
                    <h3>CO2 Emissions Saved</h3>
                    <div class="val" id="co2">0.00 kg</div>
                </div>
            </div>

            <div class="legend">
                <div
                    style="font-weight: bold; margin-bottom: 10px; color: var(--accent); font-size: 0.6rem; text-transform: uppercase;">
                    Map Legend</div>
                <div class="l-item"><span class="box" style="background:var(--success)"></span> General Free</div>
                <div class="l-item"><span class="box" style="background:var(--premium)"></span> Premium Free (SUV)</div>
                <div class="l-item"><span class="box" style="background:var(--logistics)"></span> Logistics Free</div>
                <div class="l-item"><span class="box" style="background:var(--danger)"></span> Occupied Slot</div>
                <div class="l-item"><span class="box" style="background:var(--warning)"></span> Inefficient (Mismatched)
                </div>
            </div>
        </div>
    </div>

    <div class="ticker">
        <div class="ticker-wrap">
            ARC-SYNC: SYSTEM STATUS NOMINAL... DATA COLLECTION V5.0 PROTOCOLS ACTIVE... MONITORING ${8.488} N, ${76.923}
            E... ALL ZONES ENFORCED...
        </div>
    </div>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // ESRI Layers
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

        async function updateDashboard() {
            try {
                const response = await fetch('logic.php?action=fetch_status');
                const data = await response.json();

                if (!data || data.status !== 'success') {
                    console.warn("Invalid telemetry data received");
                    return;
                }

                // Update Stats
                document.getElementById('revenue').innerText = '₹' + data.stats.revenue;
                document.getElementById('co2').innerText = data.stats.co2_saved + ' kg';

                data.slots.forEach(slot => {
                    let color = '#00FF00'; // Default Green (Free)

                    // Strict Color Matrix (Digital Twin style)
                    if (slot.status === 'occupied') {
                        color = '#FF0000'; // Red
                    } else if (slot.status === 'inefficient') {
                        color = '#FFA500'; // Orange
                    } else {
                        // Free statuses
                        if (slot.zone_type === 'premium') color = '#FFD700'; // Gold
                        else if (slot.zone_type === 'logistics') color = '#D000FF'; // Neon Purple
                    }

                    if (slotMarkers[slot.slot_id]) {
                        slotMarkers[slot.slot_id].setStyle({ fillColor: color, color: color });
                    } else {
                        // Precision 2.2m markers
                        const marker = L.circle([parseFloat(slot.lat), parseFloat(slot.lng)], {
                            radius: 2.2,
                            fillColor: color,
                            fillOpacity: 0.7,
                            color: color,
                            weight: 1.5
                        }).addTo(map);

                        marker.bindPopup(`
                            <div style="font-family:'Orbitron'; font-size:0.8rem; line-height:1.4;">
                                <b>${slot.slot_name}</b><br>
                                Zone: ${slot.zone_type.charAt(0).toUpperCase() + slot.zone_type.slice(1)}<br>
                                Status: ${slot.status.charAt(0).toUpperCase() + slot.status.slice(1)}
                            </div>
                        `);
                        slotMarkers[slot.slot_id] = marker;
                    }
                });

            } catch (err) {
                console.error("Dashboard Sync Error:", err);
            }
        }

        setInterval(updateDashboard, 2000);
        updateDashboard();
    </script>
</body>

</html>