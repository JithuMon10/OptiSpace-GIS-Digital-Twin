<?php
header('Content-Type: application/json');

$file = 'slots.sql';

// Ensure file exists
if (!file_exists($file)) {
    file_put_contents($file, "-- OptiSpace Generated Slots SQL --" . PHP_EOL);
}

$action = $_GET['action'] ?? '';

try {
    if ($action === 'load') {
        $content = file_get_contents($file);
        $slots = [];

        // Match: INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'Slot-Name', 'free', lat, lng, 'zone');
        $pattern = "/VALUES\s*\(NULL,\s*'([^']+)',\s*'[^']+',\s*([\d\.-]+),\s*([\d\.-]+),\s*'([^']+)'\)/i";

        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $slots[] = [
                'name' => $match[1],
                'lat' => (float) $match[2],
                'lng' => (float) $match[3],
                'zone' => $match[4]
            ];
        }

        echo json_encode(['success' => true, 'slots' => $slots]);
        exit;
    }

    if ($action === 'save') {
        $lat = $_POST['lat'] ?? 0;
        $lng = $_POST['lng'] ?? 0;
        $type = $_POST['type'] ?? 'car';
        $name = $_POST['name'] ?? 'Unnamed';
        $zone = $_POST['zone'] ?? 'general';

        $sql = "INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, '$name', 'free', $lat, $lng, '$zone');" . PHP_EOL;

        file_put_contents($file, $sql, FILE_APPEND);
        echo json_encode(['success' => true]);
        exit;
    }

    if ($action === 'delete') {
        $targetName = $_POST['name'] ?? '';
        if (!$targetName)
            throw new Exception("Name required");

        $lines = file($file);
        $newLines = [];
        foreach ($lines as $line) {
            // Check if the line contains the slot name in the VALUES section
            if (strpos($line, "'$targetName'") === false) {
                $newLines[] = $line;
            }
        }

        file_put_contents($file, implode("", $newLines));
        echo json_encode(['success' => true]);
        exit;
    }

    echo json_encode(['success' => false, 'message' => 'Invalid action']);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>