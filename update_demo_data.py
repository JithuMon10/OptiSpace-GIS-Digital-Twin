import re
import random

# Absolute paths
SQL_FILE = r'c:\xampp\htdocs\optispace\setup_files\optispace_db.sql'
HTML_FILE = r'c:\xampp\htdocs\optispace\demo.html'

def update():
    # 1. Extract from SQL
    with open(SQL_FILE, 'r', encoding='utf-8') as f:
        sql_content = f.read()
    
    # Pattern: (slot_id, 'name', 'size', 'status', lat, lng, 'type')
    pattern = r"\((\d+),\s*'([^']+)',\s*'[^']+',\s*'[^']+',\s*([\d.]+),\s*([\d.]+),\s*'([^']+)'\)"
    matches = re.findall(pattern, sql_content)
    
    js_slots = []
    for m in matches:
        slot_id, name, lat, lng, ztype = m
        status = 'occupied' if random.random() < 0.3 else 'free'
        js_slots.append(f"            {{ slot_id: {slot_id}, slot_name: '{name}', lat: {lat}, lng: {lng}, zone_type: '{ztype}', status: '{status}' }}")
    
    slots_array_str = "        const DEMO_SLOTS = [\n" + ",\n".join(js_slots) + "\n        ];"

    # 2. Read HTML
    with open(HTML_FILE, 'r', encoding='utf-8') as f:
        html_content = f.read()

    # 3. Replace DEMO_SLOTS section
    # Use a regex that captures everything from "const DEMO_SLOTS = [" to "];"
    new_html = re.sub(
        r'const DEMO_SLOTS = \[.*?\];',
        slots_array_str,
        html_content,
        flags=re.DOTALL
    )

    # 4. Write back
    with open(HTML_FILE, 'w', encoding='utf-8') as f:
        f.write(new_html)
    
    print(f"Successfully updated {HTML_FILE} with {len(matches)} slots.")

if __name__ == "__main__":
    update()
