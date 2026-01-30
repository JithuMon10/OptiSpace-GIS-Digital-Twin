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
        // We select EVERYTHING. Since your DB has 'slot_id', this grabs it automatically.
        $stmt = $pdo->query("SELECT * FROM parking_slots");
        $slots = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Stats
        $stmt = $pdo->query("SELECT COUNT(*) as total FROM parking_slots WHERE status='occupied'");
        $occupied = $stmt->fetch()['total'];

        $revenue = $occupied * 50;
        $co2 = $occupied * 0.45;

        echo json_encode([
            'status' => 'success',
            'slots' => $slots,
            'stats' => [
                'total_entries' => (int) $occupied,
                'revenue' => number_format($revenue, 2),
                'co2_saved' => number_format($co2, 2)
            ]
        ]);
        exit;
    }

    // ---------------------------------------------------------
    // ACTION: ENTRY (Simulator) - THIS WAS THE BROKEN PART
    // ---------------------------------------------------------
    if ($action === 'entry') {
        $type = $_POST['type'] ?? 'car'; // car, suv, truck

        $target_zone = 'general';
        if ($type === 'suv')
            $target_zone = 'premium';
        if ($type === 'truck')
            $target_zone = 'logistics';

        // FIX 1: Look for 'slot_id', NOT 'id'
        $sql = "SELECT slot_id FROM parking_slots WHERE status='free' AND zone_type='$target_zone' LIMIT 1";
        $stmt = $pdo->query($sql);
        $slot = $stmt->fetch();

        // Fallback logic
        if (!$slot && $type !== 'truck') {
            $sql = "SELECT slot_id FROM parking_slots WHERE status='free' AND zone_type='general' LIMIT 1";
            $stmt = $pdo->query($sql);
            $slot = $stmt->fetch();
        }

        if ($slot) {
            // FIX 2: Update using 'slot_id'
            $update = $pdo->prepare("UPDATE parking_slots SET status='occupied' WHERE slot_id = ?");
            $update->execute([$slot['slot_id']]);
            echo json_encode(['status' => 'success', 'message' => "Parked in Slot " . $slot['slot_id']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'NO PARKING AVAILABLE']);
        }
        exit;
    }

    // ---------------------------------------------------------
    // ACTION: EXIT (Simulator)
    // ---------------------------------------------------------
    if ($action === 'exit') {
        // FIX 3: Find an occupied slot using 'slot_id'
        $sql = "SELECT slot_id FROM parking_slots WHERE status='occupied' LIMIT 1";
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