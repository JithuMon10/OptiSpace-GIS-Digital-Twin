import re

# Read SQL file
with open('c:/xampp/htdocs/optispace/setup_files/optispace_db.sql', 'r', encoding='utf-8') as f:
    content = f.read()

# Extract slot data using regex
pattern = r'\((\d+), \'([^']+)\', \'([^']+)\', \'([^']+)\', ([\d.]+), ([\d.]+), \'([^']+)\'\)'
matches = re.findall(pattern, content)

print(f'// Total {len(matches)} parking slots from database')
print('const DEMO_SLOTS = [')

for match in matches:
    slot_id, slot_name, size_cat, status, lat, lng, zone_type = match
    # Always set status to 'free' for demo
    print(f"    {{ slot_id: {slot_id}, slot_name: '{slot_name}', lat: {lat}, lng: {lng}, zone_type: '{zone_type}', status: 'free' }},")

print('];')
