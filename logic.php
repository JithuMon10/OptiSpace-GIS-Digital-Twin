<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

if ($action === 'get_status') {
    $slots = $pdo->query("SELECT * FROM parking_slots")->fetchAll();
    $stats = $pdo->query("SELECT * FROM simulation_stats WHERE id = 1")->fetch();
    
    // Convert coordinates string to array for Leaflet
    foreach ($slots as &$slot) {
        $slot['coordinates'] = json_decode($slot['coordinates']);
    }
    
    echo json_encode([
        'slots' => $slots,
        'stats' => $stats
    ]);
    exit;
}

if ($action === 'enter') {
    $vehicle_id = $_POST['vehicle_id'] ?? null;
    if (!$vehicle_id) {
        echo json_encode(['success' => false, 'message' => 'Vehicle ID required']);
        exit;
    }

    // Get vehicle info
    $stmt = $pdo->prepare("SELECT * FROM vehicle_types WHERE id = ?");
    $stmt->execute([$vehicle_id]);
    $vehicle = $stmt->fetch();

    if (!$vehicle) {
        echo json_encode(['success' => false, 'message' => 'Invalid vehicle type']);
        exit;
    }

    $v_size = $vehicle['size_rank'];
    $v_name = strtolower($vehicle['name']);

    // Find best available slot based on strict zoning rules
    if ($v_name === 'bus' || $v_name === 'truck' || $v_size == 4) {
        // Rule: Heavy vehicles MUST ONLY check slots with size_category = 'XL' (4)
        $stmt = $pdo->prepare("SELECT * FROM parking_slots WHERE status = 'free' AND size_rank = 4 LIMIT 1");
    } else {
        // Standard rule: Find best available slot (matching size first)
        $stmt = $pdo->prepare("SELECT * FROM parking_slots WHERE status = 'free' AND size_rank >= ? ORDER BY size_rank ASC LIMIT 1");
    }
    
    $stmt->execute($v_name === 'bus' || $v_name === 'truck' || $v_size == 4 ? [] : [$v_size]);
    $slot = $stmt->fetch();

    if (!$slot) {
        echo json_encode(['success' => false, 'message' => 'No available slots for this vehicle']);
        exit;
    }

    // Rule: If a 'bike' parks in an 'XL' spot, flag it as status = 'inefficient'
    $new_status = 'occupied';
    if ($v_name === 'bike' && $slot['size_rank'] == 4) {
        $new_status = 'inefficient';
    } elseif ($slot['size_rank'] > $v_size) {
        $new_status = 'inefficient';
    }

    // Update slot
    $updateStmt = $pdo->prepare("UPDATE parking_slots SET status = ?, current_vehicle_type = ? WHERE id = ?");
    $updateStmt->execute([$new_status, $vehicle_id, $slot['id']]);

    // Update global stats
    $pdo->prepare("UPDATE simulation_stats SET total_revenue = total_revenue + ?, total_co2 = total_co2 + ? WHERE id = 1")
        ->execute([$vehicle['hourly_rate'], $vehicle['co2_savings']]);

    echo json_encode([
        'success' => true, 
        'message' => "Vehicle parked in {$slot['slot_name']} ({$slot['zone_name']})",
        'slot_id' => $slot['id'],
        'status' => $new_status,
        'zone_name' => $slot['zone_name']
    ]);
    exit;
}

if ($action === 'reset') {
    $pdo->query("UPDATE parking_slots SET status = 'free', current_vehicle_type = NULL");
    $pdo->query("UPDATE simulation_stats SET total_revenue = 0, total_co2 = 0 WHERE id = 1");
    echo json_encode(['success' => true, 'message' => 'Simulation reset successfully']);
    exit;
}

echo json_encode(['error' => 'Invalid action']);
?>
