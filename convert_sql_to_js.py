import random

# SQL data from user
sql_data = """INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'ENTRANCE', 'free', 8.48853378086417, 76.92194851273933, 'general');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-1', 'free', 8.488573628478088, 76.92205272968972, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-2', 'free', 8.488555064036314, 76.92207954248542, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-3', 'free', 8.488539151656957, 76.92210099272201, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-4', 'free', 8.488517935150092, 76.92211708039945, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-5', 'free', 8.488499370705608, 76.92213584935644, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-6', 'free', 8.488470198005368, 76.92214925575429, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-7', 'free', 8.488454285622474, 76.92217070599084, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-8', 'free', 8.488435721174936, 76.92218679366827, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-9', 'free', 8.488411852598198, 76.92220556262531, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-10', 'free', 8.488624017672649, 76.92211439911985, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-11', 'free', 8.488605453233316, 76.92213048679729, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-12', 'free', 8.488597497044763, 76.92215461831343, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-13', 'free', 8.488568324351967, 76.92216266215213, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-14', 'free', 8.488547107846705, 76.92218947494787, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-15', 'free', 8.488531195467004, 76.92221092518442, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-16', 'free', 8.488512631023182, 76.92223237542102, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-17', 'free', 8.48849406657848, 76.92224846309843, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-18', 'free', 8.48847285006911, 76.92226186949631, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-19', 'free', 8.488687667172124, 76.92221092518442, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-20', 'free', 8.488666450673465, 76.92222969414145, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-21', 'free', 8.488642582111103, 76.92225114437804, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-22', 'free', 8.488624017672649, 76.92227527589418, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-23', 'free', 8.488594844981874, 76.9222806384533, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-24', 'free', 8.48858158466715, 76.9223020886899, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-25', 'free', 8.488560368162625, 76.92232622020603, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-26', 'free', 8.48853649959366, 76.92233962660391, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-27', 'free', 8.488751316661054, 76.92227527589418, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-28', 'free', 8.488730100165906, 76.92229672613074, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-29', 'free', 8.48869827542102, 76.92231549508776, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-30', 'free', 8.488677058922947, 76.9223289014856, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-31', 'free', 8.488658494486172, 76.92235303300174, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-32', 'free', 8.48863727798591, 76.92237180195877, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-33', 'free', 8.488616061484477, 76.92239057091575, 'premium');
INSERT INTO parking_slots (slot_id, slot_name, status, lat, lng, zone_type) VALUES (NULL, 'A-34', 'free', 8.488594844981874, 76.9224147024319, 'premium');"""

# Parse SQL and extract data
lines = sql_data.strip().split('\n')
slots = []
slot_id = 1

for line in lines:
    # Extract values from SQL
    if "VALUES" in line:
        # Extract the part between parentheses
        values_part = line.split('VALUES')[1].strip()
        values_part = values_part.replace('(NULL, ', '').replace(');', '').replace("'", '')
        
        parts = [p.strip() for p in values_part.split(',')]
        if len(parts) >= 5:
            slot_name = parts[0]
            lat = parts[2]
            lng = parts[3]
            zone_type = parts[4]
            
            # Rename 'premium' to 'suv' to match the system
            if zone_type == 'premium':
                zone_type = 'suv'
            
            # Randomly mark 30% as occupied
            status = 'occupied' if random.random() < 0.3 else 'free'
            
            slots.append({
                'slot_id': slot_id,
                'slot_name': slot_name,
                'lat': lat,
                'lng': lng,
                'zone_type': zone_type,
                'status': status
            })
            slot_id += 1

# Generate JavaScript array
print(f"// Total {len(slots)} parking slots - converted from SQL with ~30% randomly occupied")
print("const DEMO_SLOTS = [")
for i, slot in enumerate(slots):
    comma = ',' if i < len(slots) - 1 else ''
    print(f"    {{ slot_id: {slot['slot_id']}, slot_name: '{slot['slot_name']}', lat: {slot['lat']}, lng: {slot['lng']}, zone_type: '{slot['zone_type']}', status: '{slot['status']}' }}{comma}")
print("];")
print(f"\n// Generated {len(slots)} slots total")
