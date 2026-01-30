<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OptiSpace | Security Command Center</title>
    <link
        href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --bg: #0a0e14;
            --panel: rgba(255, 255, 255, 0.05);
            --accent: #00f2ff;
            --text: #e0e0e0;
            --bike: #4caf50;
            --car: #2196f3;
            --suv: #ff9800;
            --bus: #f44336;
        }

        body {
            margin: 0;
            padding: 0;
            background-color: var(--bg);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            overflow: hidden;
            background-image: radial-gradient(circle at 50% 50%, #1a2332 0%, #0a0e14 100%);
        }

        .container {
            width: 90%;
            max-width: 800px;
            padding: 40px;
            background: var(--panel);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: 2.5rem;
            margin-bottom: 30px;
            letter-spacing: 4px;
            color: var(--accent);
            text-shadow: 0 0 15px rgba(0, 242, 255, 0.5);
        }

        .gate-status {
            margin-bottom: 40px;
            font-size: 1.1rem;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        .button-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-bottom: 40px;
        }

        .btn {
            padding: 25px;
            font-size: 1.2rem;
            font-weight: 600;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn i {
            font-size: 2rem;
        }

        .btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
        }

        .btn-bike {
            background: rgba(76, 175, 80, 0.2);
            color: #81c784;
            border: 1px solid #4caf50;
        }

        .btn-bike:hover {
            background: #4caf50;
            color: #fff;
        }

        .btn-car {
            background: rgba(33, 150, 243, 0.2);
            color: #64b5f6;
            border: 1px solid #2196f3;
        }

        .btn-car:hover {
            background: #2196f3;
            color: #fff;
        }

        .btn-suv {
            background: rgba(255, 152, 0, 0.2);
            color: #ffb74d;
            border: 1px solid #ff9800;
        }

        .btn-suv:hover {
            background: #ff9800;
            color: #fff;
        }

        .btn-bus {
            background: rgba(244, 67, 54, 0.2);
            color: #e57373;
            border: 1px solid #f44336;
        }

        .btn-bus:hover {
            background: #f44336;
            color: #fff;
        }

        .btn-reset {
            margin-top: 20px;
            background: rgba(255, 255, 255, 0.05);
            color: #ccc;
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 10px 30px;
            border-radius: 30px;
            font-size: 0.9rem;
        }

        .btn-reset:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }

        #message {
            margin-top: 30px;
            padding: 15px;
            border-radius: 8px;
            font-weight: 500;
            min-height: 20px;
            transition: opacity 0.5s;
        }

        .success {
            background: rgba(76, 175, 80, 0.1);
            color: #81c784;
            border: 1px solid rgba(76, 175, 80, 0.2);
        }

        .error {
            background: rgba(244, 67, 54, 0.1);
            color: #e57373;
            border: 1px solid rgba(244, 67, 54, 0.2);
        }
    </style>
</head>

<body>

    <div class="container">
        <h1>OPTISPACE</h1>
        <div class="gate-status">System Online // Entry Gate 01</div>

        <div class="button-grid">
            <button class="btn btn-bike" onclick="parkVehicle(1)">
                Bike <small>(Small)</small>
            </button>
            <button class="btn btn-car" onclick="parkVehicle(2)">
                Car <small>(Medium)</small>
            </button>
            <button class="btn btn-suv" onclick="parkVehicle(3)">
                SUV <small>(Premium)</small>
            </button>
            <button class="btn btn-bus" onclick="parkVehicle(4)">
                Bus <small>(Heavy)</small>
            </button>
        </div>

        <button class="btn-reset" onclick="resetSimulation()">Reset Simulation</button>

        <div id="message"></div>
    </div>

    <script>
        function parkVehicle(id) {
            const formData = new FormData();
            formData.append('vehicle_id', id);

            fetch('logic.php?action=enter', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    const msgDiv = document.getElementById('message');
                    msgDiv.innerText = data.message;
                    msgDiv.className = data.success ? 'success' : 'error';

                    setTimeout(() => {
                        msgDiv.innerText = '';
                        msgDiv.className = '';
                    }, 3000);
                });
        }

        function resetSimulation() {
            fetch('logic.php?action=reset')
                .then(response => response.json())
                .then(data => {
                    const msgDiv = document.getElementById('message');
                    msgDiv.innerText = data.message;
                    msgDiv.className = 'success';
                    setTimeout(() => {
                        msgDiv.innerText = '';
                        msgDiv.className = '';
                    }, 3000);
                });
        }
    </script>
</body>

</html>