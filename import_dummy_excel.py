import openpyxl
import xlrd
import mysql.connector
from datetime import datetime

# Koneksi ke database MySQL simor_bms
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password="",
    database="simor_bms"
)
cursor = db.cursor(dictionary=True)

print("Koneksi ke database simor_bms berhasil!")

# 1. Ambil ID rig dan kategori
cursor.execute("SELECT id, kode, nama_rig FROM rigs")
rigs_db = cursor.fetchall()
rig_map = {}
for r in rigs_db:
    # Key variasi: "BMS 01", "BMS#01", "BMS01"
    clean_k = r['kode'].replace('#', ' ').strip().upper()
    clean_k2 = r['kode'].strip().upper()
    clean_n = r['nama_rig'].strip().upper()
    rig_map[clean_k] = r['id']
    rig_map[clean_k2] = r['id']
    rig_map[clean_n] = r['id']

cursor.execute("SELECT id, nama FROM kategori_downtime")
kats_db = cursor.fetchall()
kat_map = {}
for k in kats_db:
    kat_map[k['nama'].strip().lower()] = k['id']

def get_kat_id(name):
    clean = name.strip().lower()
    for kname, kid in kat_map.items():
        if kname in clean or clean in kname:
            return kid
    return None

def get_or_create_lokasi(nama_lokasi):
    if not nama_lokasi or str(nama_lokasi).strip() in ['', '-', 'None']:
        return None
    nama = str(nama_lokasi).strip()
    cursor.execute("SELECT id FROM lokasi WHERE nama_lokasi = %s", (nama,))
    row = cursor.fetchone()
    if row:
        return row['id']
    cursor.execute("INSERT INTO lokasi (nama_lokasi, aktif) VALUES (%s, 1)", (nama,))
    db.commit()
    return cursor.lastrowid

print("--- 1. Import NPT SEPTEMBER 2026 SYS.xlsx ---")
try:
    wb_npt = openpyxl.load_workbook(r'C:\Users\LENOVO\Downloads\System\NPT SEPTEMBER  2026 SYS.xlsx', data_only=True)
    npt_count = 0
    for sname in wb_npt.sheetnames:
        clean_s = sname.replace('#', ' ').strip().upper()
        rig_id = rig_map.get(clean_s)
        if not rig_id:
            continue
        
        ws = wb_npt[sname]
        # Baca header kategori di baris 3 dan 4
        col_kats = {}
        for col in range(4, ws.max_column):
            val3 = ws.cell(row=3, column=col).value
            val4 = ws.cell(row=4, column=col).value
            name = str(val4 if val4 else val3 or '').strip()
            if name and name.upper() not in ['TOTAL (HRS)', 'REMARK', 'REMARK UNPAID', 'NONE']:
                kid = get_kat_id(name)
                if kid:
                    col_kats[col] = kid
        
        # Baca tanggal baris 6 s/d 36 (atau sampai total downtime)
        for r in range(6, min(ws.max_row + 1, 40)):
            tgl_cell = ws.cell(row=r, column=1).value
            if not tgl_cell or str(tgl_cell).strip().upper().startswith('TOTAL'):
                break
            try:
                day_num = int(tgl_cell)
            except:
                continue
            
            tgl_str = f"2026-09-{day_num:02d}"
            remark = ws.cell(row=r, column=ws.max_column).value
            remark_str = str(remark).strip() if remark else None

            for col, kid in col_kats.items():
                val = ws.cell(row=r, column=col).value
                if val and isinstance(val, (int, float)) and val > 0:
                    cursor.execute("""
                        INSERT INTO npt_harian (rig_id, tanggal, kategori_id, jam, remark, created_at)
                        VALUES (%s, %s, %s, %s, %s, NOW())
                        ON DUPLICATE KEY UPDATE jam = VALUES(jam), remark = VALUES(remark)
                    """, (rig_id, tgl_str, kid, float(val), remark_str))
                    npt_count += 1
    db.commit()
    print(f"Berhasil mengimpor {npt_count} baris data NPT!")
except Exception as e:
    print(f"Gagal NPT: {e}")

print("--- 2. Import Daily Report SEPTEMBER 2026 SYS.xls ---")
try:
    wb_dr = xlrd.open_workbook(r'C:\Users\LENOVO\Downloads\System\Daily Report SEPTEMBER 2026 SYS.xls')
    dr_count = 0
    for sname in wb_dr.sheet_names():
        clean_s = sname.replace('#', ' ').split('ODR')[0].split('BARU')[0].strip().upper()
        rig_id = rig_map.get(clean_s)
        if not rig_id:
            continue
        
        ws = wb_dr.sheet_by_name(sname)
        # Cari baris data sumur
        for r in range(4, ws.nrows):
            row_vals = ws.row_values(r)
            col_b = row_vals[1] if len(row_vals) > 1 else None # No Well
            col_c = row_vals[2] if len(row_vals) > 2 else None # Lokasi
            col_d = row_vals[3] if len(row_vals) > 3 else None # Date
            col_f = row_vals[5] if len(row_vals) > 5 else 0    # MIRU
            col_g = row_vals[6] if len(row_vals) > 6 else 0    # OPS
            col_u = row_vals[21] if len(row_vals) > 21 else 0  # Total DT
            col_v = row_vals[22] if len(row_vals) > 22 else 0  # Total Jam
            col_w = row_vals[23] if len(row_vals) > 23 else '' # Remark
            col_x = row_vals[24] if len(row_vals) > 24 else '' # Status

            try:
                no_well = int(float(col_b)) if col_b != '' and col_b is not None else None
            except:
                no_well = None

            if no_well and col_c:
                lokasi_id = get_or_create_lokasi(col_c)
                miru = float(col_f) if isinstance(col_f, (int, float)) else 0.0
                ops = float(col_g) if isinstance(col_g, (int, float)) else 0.0
                dt = float(col_u) if isinstance(col_u, (int, float)) else 0.0
                tot = float(col_v) if isinstance(col_v, (int, float)) else (miru + ops + dt)
                status_str = str(col_x).strip() or 'JOB COMPLETED'
                remark_str = str(col_w).strip() or None

                cursor.execute("""
                    INSERT INTO daily_report (rig_id, lokasi_id, no_well, miru_jam, ops_jam, total_dt, total_jam, status_job, remark, bulan, tahun, created_at)
                    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, 9, 2026, NOW())
                """, (rig_id, lokasi_id, no_well, miru, ops, dt, tot, status_str, remark_str))
                dr_count += 1
    db.commit()
    print(f"Berhasil mengimpor {dr_count} baris data Daily Report sumur!")
except Exception as e:
    print(f"Gagal Daily Report: {e}")

# 3. Hitung otomatis ke monthly_summary
print("--- 3. Sinkronisasi Hitungan Monthly Summary ---")
days = 30 # September
total_jam_bln = days * 24.0

cursor.execute("SELECT id, odr FROM rigs WHERE aktif = 1")
all_rigs = cursor.fetchall()

for r in all_rigs:
    rid = r['id']
    odr = float(r['odr'])

    # Total UNPAID
    cursor.execute("""
        SELECT SUM(nh.jam) as tot 
        FROM npt_harian nh 
        JOIN kategori_downtime kd ON kd.id = nh.kategori_id 
        WHERE nh.rig_id = %s AND MONTH(nh.tanggal) = 9 AND YEAR(nh.tanggal) = 2026 AND kd.tipe = 'UNPAID'
    """, (rid,))
    unpaid = float(cursor.fetchone()['tot'] or 0)

    # Total SBWC
    cursor.execute("""
        SELECT SUM(nh.jam) as tot 
        FROM npt_harian nh 
        JOIN kategori_downtime kd ON kd.id = nh.kategori_id 
        WHERE nh.rig_id = %s AND MONTH(nh.tanggal) = 9 AND YEAR(nh.tanggal) = 2026 AND kd.tipe = 'SBWC'
    """, (rid,))
    sbwc = float(cursor.fetchone()['tot'] or 0)

    # Total Daily Report
    cursor.execute("""
        SELECT SUM(miru_jam) as tot_miru, SUM(ops_jam) as tot_ops, COUNT(*) as tot_well
        FROM daily_report
        WHERE rig_id = %s AND bulan = 9 AND tahun = 2026
    """, (rid,))
    dr_stat = cursor.fetchone()
    tot_miru = float(dr_stat['tot_miru'] or 0)
    tot_ops = float(dr_stat['tot_ops'] or 0)
    tot_well = int(dr_stat['tot_well'] or 0)

    rel = max(0, 1.0 - (unpaid / total_jam_bln)) if total_jam_bln > 0 else 0
    ava = rel
    uti = (tot_ops / total_jam_bln) if total_jam_bln > 0 else 0
    avg_miru = (tot_miru / tot_well) if tot_well > 0 else 0
    avg_cycle = (tot_ops / tot_well) if tot_well > 0 else 0
    rev_target = int(odr * days)
    prod_jam = max(0, total_jam_bln - sbwc - unpaid)
    rev_actual = int(odr * (prod_jam / 24.0))

    cursor.execute("""
        INSERT INTO monthly_summary (
            rig_id, bulan, tahun, reliability, availability, utilization,
            total_miru, total_ops, avg_miru, avg_cycle_time, total_well_job,
            sbwc_jam, unpaid_jam, revenue_target, revenue_actual, total_jam
        ) VALUES (%s, 9, 2026, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        ON DUPLICATE KEY UPDATE
            reliability = VALUES(reliability),
            availability = VALUES(availability),
            utilization = VALUES(utilization),
            total_miru = VALUES(total_miru),
            total_ops = VALUES(total_ops),
            avg_miru = VALUES(avg_miru),
            avg_cycle_time = VALUES(avg_cycle_time),
            total_well_job = VALUES(total_well_job),
            sbwc_jam = VALUES(sbwc_jam),
            unpaid_jam = VALUES(unpaid_jam),
            revenue_target = VALUES(revenue_target),
            revenue_actual = VALUES(revenue_actual),
            total_jam = VALUES(total_jam)
    """, (
        rid, round(rel, 6), round(ava, 6), round(uti, 6),
        tot_miru, tot_ops, round(avg_miru, 2), round(avg_cycle, 2), tot_well,
        sbwc, unpaid, rev_target, rev_actual, total_jam_bln
    ))

db.commit()
print("Kalkulasi dan sinkronisasi data selesai 100%!")
cursor.close()
db.close()
