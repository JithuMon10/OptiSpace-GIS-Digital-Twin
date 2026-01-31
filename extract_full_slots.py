import re
import random

def extract():
    with open(r'c:\xampp\htdocs\optispace\setup_files\optispace_db.sql', 'r') as f:
        content = f.read()

    # Look for the INSERT INTO parking_slots section
    # Format: (id, 'name', 'size', 'status', lat, lng, 'type')
    pattern = r"\((\d+),\s*'([^']+)',\s*'[^']+',\s*'[^']+',\s*([\d.]+),\s*([\d.]+),\s*'([^']+)'\)"
    matches = re.findall(pattern, content)
    
    print("const DEMO_SLOTS = [")
    for i, m in enumerate(matches):
        slot_id, name, lat, lng, ztype = m
        status = 'occupied' if random.random() < 0.3 else 'free'
        comma = "," if i < len(matches) - 1 else ""
        print(f"    {{ slot_id: {slot_id}, slot_name: '{name}', lat: {lat}, lng: {lng}, zone_type: '{ztype}', status: '{status}' }}{comma}")
    print("];")

if __name__ == "__main__":
    extract()
