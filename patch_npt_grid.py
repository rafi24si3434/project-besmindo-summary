
with open(r'c:\xampp\htdocs\Project Besmindo Summary\app\Views\npt\grid.php', 'r', encoding='utf-8') as f:
    text = f.read()

import re
# Remove missing log banner
text = re.sub(r'<\?php if \(!empty\(\\)\):\ ?>.*?<\?php endif; \?>', '', text, flags=re.DOTALL)

with open(r'c:\xampp\htdocs\Project Besmindo Summary\app\Views\npt\grid.php', 'w', encoding='utf-8') as f:
    f.write(text)
print('Patched successfully')

