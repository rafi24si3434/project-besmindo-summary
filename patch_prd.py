import re

with open(r'C:\xampp\htdocs\Project Besmindo Summary\PRD_SIMOR_BMS.md', 'r', encoding='utf-8') as f:
    text = f.read()

# Replace Roadmap Phase 1, Phase 2, Phase 3
roadmap = '''### Phase 1 — MVP & Architectural Upgrades (Selesai September 2026)

- [x] Autentikasi (login/logout)
- [x] Pusat Master Data Terpadu (Rig, Kategori, 3rd Party, Lokasi)
- [x] Daily Report Step 1 (matriks sumur) + redesign UI
- [x] Daily Report Quick Well Creation (AJAX modal tanpa reload)
- [x] UNIFIED DAILY LOG (The 24-Hour Balance Rule) — Sentralisasi form NPT ke dalam Daily Report
- [x] 3rd Party Breakdown interaktif di dalam Daily Report
- [x] NPT Harian Matrix Upgrade — Read-Only Dashboard dengan Live Financial Impact & Smart Contextual Remark
- [x] Monthly Report (KPI otomatis)
- [x] Rekap Tahunan KPI & NPT (4 mode view)
- [x] Dashboard dengan Chart.js
- [x] Export Excel komprehensif (Quick Export Bundle, format SYS)
- [x] Import data historis 2026 (4,849 sumur, 5,460 logs, 3,392 NPT)
- [x] Sinkronisasi otomatis 2-arah Log Harian ? NPT Harian

### Phase 2 — Enhancement (Q4 2026)'''

text = re.sub(r'### Phase 1.*?### Phase 2 — Enhancement \(Q4 2026\)', roadmap, text, flags=re.DOTALL)

with open(r'C:\xampp\htdocs\Project Besmindo Summary\PRD_SIMOR_BMS.md', 'w', encoding='utf-8') as f:
    f.write(text)

print("Patched Roadmap")
