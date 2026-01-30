<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiSpace | Command Simulator</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #050709;
            --accent: #00f2ff;
            --panel: #0d1117;
        }

        body {
            background: var(--bg);
            color: #fff;
            font-family: 'Inter', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .sim-card {
            background: var(--panel);
            border: 2px solid rgba(0, 242, 255, 0.2);
            padding: 40px;
            border-radius: 10px;
            width: 380px;
            text-align: center;
        }

        h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 1.1rem;
            color: var(--accent);
            margin-bottom: 30px;
            letter-spacing: 1px;
        }

        .group {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        button {
            background: transparent;
            border: 1px solid var(--accent);
            color: var(--accent);
            padding: 14px;
            font-family: 'Orbitron', sans-serif;
            font-size: 0.75rem;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: var(--accent);
            color: #000;
            box-shadow: 0 0 15px var(--accent);
        }

        .reset {
            border-color: #ff3e3e;
            color: #ff3e3e;
            margin-top: 20px;
            font-size: 0.6rem;
        }

        .reset:hover {
            background: #ff3e3e;
            color: #fff;
        }

        #log {
            margin-top: 25px;
            background: #000;
            padding: 12px;
            font-family: monospace;
            font-size: 0.7rem;
            text-align: left;
            height: 80px;
            overflow-y: auto;
            color: #00ff88;
            border: 1px solid #333;
        }
    </style>
</head>

<body>
    <div class="sim-card">
        <h1>OPTISPACE GROUND SIMULATOR</h1>
        <div class="group">
            <button onclick="entry('suv')">SUV Arrival (Premium)</button>
            <button onclick="entry('car')">Car Arrival (General)</button>
            <button onclick="entry('truck')">Truck Arrival (Logistics)</button>
            <button onclick="entry('bike')" style="border-color:#eab308; color:#eab308;">Bike Arrival
                (2-Wheeler)</button>
        </div>
        <button class="reset" onclick="resetSim()">RESET ALL SYSTEMS</button>
        <div id="log">> STANDBY...</div>
    </div>

    <script>
        const logBox = document.getElementById('log');

        function print(msg, color = '#00ff88') {
            const div = document.createElement('div');
            div.style.color = color;
            div.innerText = `> ${new Date().toLocaleTimeString()}: ${msg}`;
            logBox.prepend(div);
        }

        async function entry(type) {
            const form = new FormData();
            form.append('vehicle_type', type);
            try {
                const r = await fetch('logic.php?action=entry', { method: 'POST', body: form });
                const d = await r.json();
                print(d.message, d.success ? '#00ff88' : '#ff3e3e');
            } catch (e) { print("COMM FAIL", "#ff3e3e"); }
        }

        async function resetSim() {
            await fetch('logic.php?action=reset');
            print("SIMULATION RESET", "#ff3e3e");
        }
    </script>
</body>

</html>