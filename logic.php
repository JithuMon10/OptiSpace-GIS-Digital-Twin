<?php
// 1. SILENCE ERRORS (Crucial for Simulator)
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');

require 'db_connect.php';

$action = $_GET['action'] ?? '';

try {
    // ---------------------------------------------------------
    // ACTION: FETCH STATUS (For Dashboard)
    // ---------------------------------------------------------
    if ($action === 'fetch_status') {
        $stmt = $pdo->query("SELECT * FROM parking_slots");
        $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Stats Calculation
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM parking_slots WHERE status IN ('occupied', 'inefficient')");
        $occupiedCount = $stmt->fetch()['total'];

        $revenue = $occupiedCount * 50;
        $co2 = $occupiedCount * 0.45;

        echo json_encode([
            'status' => 'success',
            'slots' => $slots,
            'stats' => [
                'total_entries' => (int) $occupiedCount,
                'revenue' => number_format($revenue, 2),
                'co2_saved' => number_format($co2, 2)
            ]
        ]);
        exit;
    }

    // ---------------------------------------------------------
    // ACTION: ENTRY (Simulator)
    // ---------------------------------------------------------
    if ($action === 'entry') {
        $type = $_POST['vehicle_type'] ?? 'car'; // car, suv, truck, bike
        $status_to_set = 'occupied';
        $target_zone = 'general';

        if ($type === 'suv')
            $target_zone = 'premium';
        if ($type === 'truck')
            $target_zone = 'logistics';
        if ($type === 'bike')
            $target_zone = 'bike';

        // Strict Truck Logic
        if ($type === 'truck') {
            $sql = "SELECT slot_id FROM parking_slots WHERE status='free' AND zone_type='logistics' LIMIT 1";
            $stmt = $pdo->query($sql);
            $slot = $stmt->fetch();

            if (!$slot) {
                echo json_encode(['status' => 'error', 'message' => 'No Logistics Bay Available']);
                exit;
            }
        }
        // Bike Logic (Priority: Bike Zone -> General -> Premium)
        else if ($type === 'bike') {
            // 1. Try Dedicated Bike Zone
            $sql = "SELECT slot_id, zone_type FROM parking_slots WHERE status='free' AND zone_type='bike' LIMIT 1";
            $slot = $pdo->query($sql)->fetch();

            if (!$slot) {
                // 2. Try General Zone
                $sql = "SELECT slot_id, zone_type FROM parking_slots WHERE status='free' AND zone_type='general' LIMIT 1";
                $slot = $pdo->query($sql)->fetch();
            }

            if (!$slot) {
                // 3. Try Premium Zone (Result: Inefficient)
                $sql = "SELECT slot_id, zone_type FROM parking_slots WHERE status='free' AND zone_type='premium' LIMIT 1";
                $slot = $pdo->query($sql)->fetch();
                if ($slot)
                    $status_to_set = 'inefficient';
            }
        }
        // Standards (Car/SUV)
        else {
            $sql = "SELECT slot_id FROM parking_slots WHERE status='free' AND zone_type='$target_zone' LIMIT 1";
            $slot = $pdo->query($sql)->fetch();

            // Fallback for cars
            if (!$slot && $type === 'car') {
                $sql = "SELECT slot_id FROM parking_slots WHERE status='free' AND zone_type='premium' LIMIT 1";
                $slot = $pdo->query($sql)->fetch();
            }
        }

        if ($slot) {
            $update = $pdo->prepare("UPDATE parking_slots SET status=? WHERE slot_id = ?");
            $update->execute([$status_to_set, $slot['slot_id']]);
            echo json_encode(['status' => 'success', 'message' => "Parked in Slot " . $slot['slot_id']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'NO PARKING AVAILABLE']);
        }
        exit;
    }

    // ---------------------------------------------------------
    // ACTION: EXIT
    // ---------------------------------------------------------
    if ($action === 'exit') {
        $sql = "SELECT slot_id FROM parking_slots WHERE status IN ('occupied', 'inefficient') LIMIT 1";
        $stmt = $pdo->query($sql);
        $slot = $stmt->fetch();

        if ($slot) {
            $update = $pdo->prepare("UPDATE parking_slots SET status='free' WHERE slot_id = ?");
            $update->execute([$slot['slot_id']]);
            echo json_encode(['status' => 'success', 'message' => "Slot " . $slot['slot_id'] . " Freed"]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'LOT IS ALREADY EMPTY']);
        }
        exit;
    }

    // ---------------------------------------------------------
    // ACTION: RESET
    // ---------------------------------------------------------
    if ($action === 'reset') {
        $pdo->query("UPDATE parking_slots SET status='free'");
        echo json_encode(['status' => 'success', 'message' => 'SIMULATION RESET']);
        exit;
    }

} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Server error: ' . $e->getMessage()]);
}
?>