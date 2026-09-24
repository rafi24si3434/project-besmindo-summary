
import re
with open(r'c:\xampp\htdocs\Project Besmindo Summary\app\Views\npt\grid.php', 'r', encoding='utf-8') as f:
    text = f.read()

text = re.sub(
    r'const mainRemarkUnpaid.*?\n\s*if \(quickRemarkSbwc && mainRemarkSbwc\) \{\n\s*quickRemarkSbwc\.value = mainRemarkSbwc\.value \|\| \'\';\n\s*\}',
    r'''const mainRemark = document.getElementById(emark_);
        const quickRemark = document.getElementById('quick_remark');
        if (quickRemark && mainRemark) {
            quickRemark.value = mainRemark.value || '';
        }''',
    text,
    flags=re.DOTALL
)

text = re.sub(
    r'const quickRemarkUnpaid.*?\n\s*if \(quickRemarkSbwc && mainRemarkSbwc\) \{\n\s*mainRemarkSbwc\.value = quickRemarkSbwc\.value\.trim\(\);\n\s*\}',
    r'''const quickRemark = document.getElementById('quick_remark');
        const mainRemark = document.getElementById(emark_);
        if (quickRemark && mainRemark) {
            mainRemark.value = quickRemark.value.trim();
        }''',
    text,
    flags=re.DOTALL
)

with open(r'c:\xampp\htdocs\Project Besmindo Summary\app\Views\npt\grid.php', 'w', encoding='utf-8') as f:
    f.write(text)
print('Regex replace complete')

