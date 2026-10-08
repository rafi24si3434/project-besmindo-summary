<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<style>
    /*
     * Design Read: Zero-Scroll Single-Screen Well Registration (/daily-report/tambah)
     * Strict Viewport Lock (design-taste-frontend 4.7): Entire form, continuity presets,
     * and primary CTAs fit comfortably inside a single laptop viewport without scrolling.
     */
    :root {
        --wf-card-bg: #0d111a;
        --wf-card-border: rgba(255, 255, 255, 0.08);
        --wf-card-shadow: 0 12px 28px -8px rgba(0, 0, 0, 0.5);
        --wf-sub-bg: #080b11;
        --wf-sub-border: rgba(255, 255, 255, 0.07);
        --wf-input-bg: #06080d;
        --wf-input-border: #25334a;
        --wf-input-text: #f8fafc;
        --wf-text-title: #f8fafc;
        --wf-text-sec: #cbd5e1;
        --wf-text-muted: #64748b;
        --wf-divider: rgba(255, 255, 255, 0.07);
        --wf-chip-bg: #131b29;
        --wf-chip-border: #25334a;
        --wf-chip-text: #e2e8f0;
        --wf-dropdown-bg: #0f1522;
    }

    :root[data-theme="light"] {
        --wf-card-bg: #ffffff;
        --wf-card-border: #e2e8f0;
        --wf-card-shadow: 0 4px 18px -4px rgba(15, 23, 42, 0.06);
        --wf-sub-bg: #f8fafc;
        --wf-sub-border: #e2e8f0;
        --wf-input-bg: #ffffff;
        --wf-input-border: #cbd5e1;
        --wf-input-text: #0f172a;
        --wf-text-title: #0f172a;
        --wf-text-sec: #334155;
        --wf-text-muted: #64748b;
        --wf-divider: #e2e8f0;
        --wf-chip-bg: #f1f5f9;
        --wf-chip-border: #cbd5e1;
        --wf-chip-text: #1e293b;
        --wf-dropdown-bg: #ffffff;
    }

    .wf-frame {
        background: var(--wf-card-bg);
        border: 1px solid var(--wf-card-border);
        border-radius: 16px;
        box-shadow: var(--wf-card-shadow);
        color: var(--wf-text-sec);
    }

    .wf-sub-box {
        background: var(--wf-sub-bg);
        border: 1px solid var(--wf-sub-border);
        border-radius: 11px;
        color: var(--wf-text-sec);
    }

    .wf-title { color: var(--wf-text-title); }
    .wf-sec   { color: var(--wf-text-sec); }
    .wf-muted { color: var(--wf-text-muted); }
    .wf-div-b { border-bottom: 1px solid var(--wf-divider); }
    .wf-div-t { border-top: 1px solid var(--wf-divider); }
    .wf-div-r { border-right: 1px solid var(--wf-divider); }

    .wf-input {
        background: var(--wf-input-bg);
        border: 1.5px solid var(--wf-input-border);
        color: var(--wf-input-text);
        border-radius: 10px;
        height: 42px;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .wf-input:focus, .wf-input:focus-within {
        border-color: #0284c7;
        box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.16);
        outline: none;
    }

    .wf-chip-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        height: 36px;
        padding: 0 12px;
        border-radius: 9px;
        font-size: 12px;
        font-weight: 700;
        background: var(--wf-chip-bg);
        border: 1px solid var(--wf-chip-border);
        color: var(--wf-chip-text);
        cursor: pointer;
        transition: all 0.14s ease;
        text-decoration: none;
        white-space: nowrap;
    }
    .wf-chip-btn:hover {
        border-color: #0284c7;
        color: #0284c7;
    }
    .wf-chip-btn.is-active {
        background: rgba(2, 132, 199, 0.15);
        border-color: #0284c7;
        color: #38bdf8;
    }
    :root[data-theme="light"] .wf-chip-btn.is-active {
        background: #e0f2fe;
        border-color: #0284c7;
        color: #0369a1;
    }

    .wf-btn-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        height: 44px;
        padding: 0 22px;
        border-radius: 11px;
        background: #0284c7;
        color: #ffffff;
        font-size: 13px;
        font-weight: 800;
        border: 1px solid rgba(255, 255, 255, 0.16);
        box-shadow: 0 4px 14px rgba(2, 132, 199, 0.3), inset 0 1px 0 rgba(255, 255, 255, 0.2);
        cursor: pointer;
        transition: background-color 0.15s ease, transform 0.1s ease, box-shadow 0.15s ease;
        white-space: nowrap;
    }
    .wf-btn-primary:hover {
        background: #0369a1;
        box-shadow: 0 6px 18px rgba(2, 132, 199, 0.4);
    }
    .wf-btn-primary:active {
        transform: translateY(1px);
    }

    .wf-dropdown {
        background: var(--wf-dropdown-bg);
        border: 1.5px solid var(--wf-input-border);
        border-radius: 12px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.45);
    }
</style>

<?php
$bulanNames = [
    1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',
    5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',
    9=>'September',10=>'Oktober',11=>'November',12=>'Desember'
];
$bBulan = (int)($report['bulan'] ?? $bulan);
$bTahun = (int)($report['tahun'] ?? $tahun);
$bRigId = (int)($report['rig_id'] ?? $rigId);
$maxDaysPeriod = cal_days_in_month(CAL_GREGORIAN, $bBulan, $bTahun);
$minDatePeriod = sprintf('%04d-%02d-01', $bTahun, $bBulan);
$maxDatePeriod = sprintf('%04d-%02d-%02d', $bTahun, $bBulan, $maxDaysPeriod);

$defaultStart = $smartStartDate ?? $minDatePeriod;
$defaultEnd   = $smartEndDate ?? $defaultStart;

$valMulai     = old('tanggal_mulai', $report['tanggal_mulai'] ?? $defaultStart);
$valSelesai   = old('tanggal_selesai', $report['tanggal_selesai'] ?? $defaultEnd);
$targetWellNo = (int)old('no_well', $report['no_well'] ?? $nextNoWell);

$logHarianUrl = base_url("daily-report/log-harian/{$bRigId}/{$bBulan}/{$bTahun}");
$rekapGridUrl = base_url("daily-report/{$bRigId}/{$bBulan}/{$bTahun}");
?>

<!-- SINGLE-VIEWPORT ZERO-SCROLL ARCHITECTURAL FRAME -->
<div class="max-w-[1280px] mx-auto">
    <div class="wf-frame overflow-visible">

        <!-- ═══ BARIS ATAS: INTEGRATED HEADER BAR (COMPACT 54px) ═══ -->
        <div class="px-5 py-3 wf-div-b flex flex-wrap items-center justify-between gap-3" style="background: var(--wf-sub-bg); border-radius: 16px 16px 0 0;">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-sky-500/15 border border-sky-500/30 text-sky-500 flex items-center justify-center shrink-0 font-mono font-black text-xs">
                    #<?= $targetWellNo ?>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <h2 class="text-sm sm:text-base font-extrabold wf-title tracking-tight">
                        <?= isset($report) ? "Edit Pekerjaan Sumur #{$report['no_well']}" : "Pendaftaran Pekerjaan Sumur Baru (Well #{$targetWellNo})" ?>
                    </h2>
                    <span class="px-2 py-0.5 rounded bg-sky-500/15 border border-sky-500/30 text-sky-500 text-xs font-mono font-bold">
                        <?= esc($rig['kode']) ?>
                    </span>
                    <span class="px-2 py-0.5 rounded wf-sub-box text-xs font-semibold">
                        <?= $bulanNames[$bBulan] ?> <?= $bTahun ?>
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <a href="<?= $logHarianUrl ?>" class="wf-chip-btn">
                    <i class="fa-solid fa-arrow-left text-sky-500"></i>
                    <span>Kembali ke Log Harian (24 Jam)</span>
                </a>
                <a href="<?= $rekapGridUrl ?>" class="wf-chip-btn">
                    <i class="fa-solid fa-table-list text-slate-400"></i>
                    <span>Tabel Sumur</span>
                </a>
            </div>
        </div>

        <!-- ═══ ISI UTAMA SPLIT 2 KOLOM (8 COL FORM + 4 COL URUTAN SUMUR) — TANPA SCROLL ═══ -->
        <div class="grid grid-cols-1 lg:grid-cols-12 items-stretch">

            <!-- KOLOM KIRI (8 COL): FORMULIR PADAT 2 BARIS + TOMBOL AKSI BESAR -->
            <div class="lg:col-span-8 p-5 sm:p-6 lg:wf-div-r flex flex-col justify-between">
                <form action="<?= isset($report) ? base_url('daily-report/update/' . $report['id']) : base_url('daily-report/simpan') ?>" method="POST" class="space-y-4">
                    <?= csrf_field() ?>
                    <input type="hidden" name="rig_id" value="<?= $bRigId ?>">
                    <input type="hidden" name="bulan" value="<?= $bBulan ?>">
                    <input type="hidden" name="tahun" value="<?= $bTahun ?>">
                    <input type="hidden" name="redirect_action" id="redirect_action" value="log_harian">

                    <?php if (!isset($report) && !empty($lastWell)): ?>
                    <!-- ─── BARIS PINTAR KONTINUITAS (1 BARIS RINGKAS) ─── -->
                    <div class="wf-sub-box px-3.5 py-2.5 border-l-4 border-l-sky-500 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2 text-xs wf-sec">
                            <i class="fa-solid fa-wand-magic-sparkles text-sky-500"></i>
                            <?php if (!empty($sameDayOption)): ?>
                                <span>
                                    Lanjutan <strong>Well #<?= esc($lastWell['no_well']) ?> (<?= esc($lastWell['nama_lokasi']) ?>)</strong>
                                    · Tgl <strong><?= date('d M', strtotime($sameDayOption['date'])) ?></strong> masih ada
                                    <strong class="text-emerald-500">Sisa <?= number_format((float)$sameDayOption['sisa_hrs'], 2) ?> Jam</strong>:
                                </span>
                            <?php else: ?>
                                <span>
                                    Melanjutkan otomatis setelah <strong>Well #<?= esc($lastWell['no_well']) ?> (<?= esc($lastWell['nama_lokasi']) ?>)</strong>
                                    (selesai <?= date('d M Y', strtotime($lastWell['tanggal_selesai'] ?? $lastWell['tanggal_mulai'])) ?>)
                                </span>
                            <?php endif; ?>
                        </div>

                        <?php if (!empty($sameDayOption) && !empty($nextDayOption)): ?>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" id="btnOptSameDay" onclick="setWellStartDateOption('<?= $sameDayOption['date'] ?>', 'same')"
                                class="wf-chip-btn !h-8 !px-2.5 !text-[11px] is-active">
                                <i class="fa-solid fa-bolt text-amber-500"></i>
                                <span>Mulai <?= date('d M', strtotime($sameDayOption['date'])) ?> (Sisa <?= number_format((float)$sameDayOption['sisa_hrs'], 1) ?>j)</span>
                            </button>
                            <button type="button" id="btnOptNextDay" onclick="setWellStartDateOption('<?= $nextDayOption ?>', 'next')"
                                class="wf-chip-btn !h-8 !px-2.5 !text-[11px]">
                                <i class="fa-regular fa-calendar text-sky-500"></i>
                                <span>Besoknya (<?= date('d M', strtotime($nextDayOption)) ?>)</span>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>

                    <!-- ─── BARIS 1: NO. WELL (2 COL) + NAMA SUMUR/LOKASI (5 COL) + DISTANCE (2 COL) + STATUS (3 COL) ─── -->
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-end">
                        <!-- Nomor Well -->
                        <div class="sm:col-span-2">
                            <label for="inputNoWell" class="block text-xs font-bold wf-title mb-1">
                                No. Well <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs font-mono font-bold wf-muted">#</span>
                                <input type="number" id="inputNoWell" name="no_well"
                                    value="<?= $targetWellNo ?>" required min="1"
                                    class="wf-input w-full pl-7 pr-2 font-mono font-black text-base">
                            </div>
                        </div>

                        <!-- Nama Sumur / Lokasi Combobox -->
                        <div class="sm:col-span-5">
                            <?php
                                $selectedLokId = old('lokasi_id', $report['lokasi_id'] ?? '');
                                $selectedLokNama = '';
                                foreach ($lokasiList as $lok) {
                                    if ($lok['id'] == $selectedLokId) {
                                        $selectedLokNama = $lok['nama_lokasi'];
                                        break;
                                    }
                                }
                            ?>
                            <div class="flex items-center justify-between mb-1">
                                <label for="lokasiSearchInput" class="block text-xs font-bold wf-title">
                                    Nama Sumur / Lokasi <span class="text-rose-500">*</span>
                                </label>
                                <span id="lokasiStatusHint" class="text-[10px] font-bold text-sky-500">
                                    Ketik / pilih
                                </span>
                            </div>

                            <input type="hidden" name="lokasi_id" id="lokasi_id" value="<?= esc($selectedLokId) ?>">

                            <div class="relative" id="lokasiComboboxWrapper">
                                <div class="wf-input flex items-center px-3">
                                    <i class="fa-solid fa-location-dot text-sky-500 text-xs mr-2 shrink-0"></i>
                                    <input type="text" name="nama_lokasi_input" id="lokasiSearchInput" required
                                        placeholder="Ketik nama sumur (misal: KB 387)..."
                                        value="<?= esc(old('nama_lokasi_input', $selectedLokNama)) ?>"
                                        data-selected-name="<?= esc($selectedLokNama) ?>"
                                        autocomplete="off"
                                        autofocus
                                        class="bg-transparent border-0 text-sm font-extrabold wf-title focus:outline-none w-full uppercase">

                                    <button type="button" id="btnClearLokasi" onclick="clearLokasiSelection()"
                                        class="wf-muted hover:text-rose-500 px-1.5 py-0.5 rounded text-xs transition <?= empty($selectedLokId) && empty($selectedLokNama) ? 'hidden' : '' ?>"
                                        title="Bersihkan Pilihan">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>

                                    <button type="button" id="btnToggleLokasiDropdown" onclick="toggleLokasiDropdown()"
                                        class="wf-muted hover:text-sky-500 px-1.5 py-0.5 rounded text-xs transition"
                                        title="Lihat Daftar Lokasi">
                                        <i class="fa-solid fa-chevron-down transition-transform duration-200" id="lokasiChevronIcon"></i>
                                    </button>
                                </div>

                                <!-- Dropdown Daftar Lokasi -->
                                <div id="lokasiDropdownMenu" class="hidden wf-dropdown absolute left-0 right-0 top-full mt-1 z-50 overflow-hidden max-h-56 flex flex-col">
                                    <div id="lokasiCreateNewItem" onclick="useNewLokasiName()"
                                         class="hidden px-3.5 py-2.5 bg-emerald-500/15 hover:bg-emerald-500/25 border-b border-emerald-500/30 cursor-pointer transition flex items-center justify-between text-xs font-bold text-emerald-500">
                                        <span class="flex items-center gap-2">
                                            <i class="fa-solid fa-plus-circle"></i>
                                            <span>Simpan Lokasi Baru: "<strong id="newLokasiPreviewText" class="uppercase"></strong>"</span>
                                        </span>
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-500/20 text-[10px] font-mono">Baru</span>
                                    </div>

                                    <div class="px-3.5 py-1.5 wf-sub-box border-b wf-div-b flex items-center justify-between text-[10px] font-bold wf-muted">
                                        <span>DAFTAR LOKASI</span>
                                        <span id="lokasiMatchCount" class="font-mono text-sky-500"><?= count($lokasiList) ?> lokasi</span>
                                    </div>

                                    <div class="overflow-y-auto divide-y" style="border-color: var(--wf-divider);" id="lokasiOptionsList">
                                        <?php foreach ($lokasiList as $lok):
                                            $isSel = ($selectedLokId == $lok['id']);
                                        ?>
                                        <div class="lokasi-option-item px-3.5 py-2 hover:bg-sky-500/10 cursor-pointer transition flex items-center justify-between text-xs wf-title <?= $isSel ? 'bg-sky-500/15 font-extrabold text-sky-500' : '' ?>"
                                             data-id="<?= $lok['id'] ?>"
                                             data-nama="<?= esc($lok['nama_lokasi']) ?>"
                                             onclick="selectLokasi(<?= $lok['id'] ?>, '<?= esc($lok['nama_lokasi'], 'js') ?>')">
                                            <span class="flex items-center gap-2">
                                                <i class="fa-solid fa-location-dot text-[10px] text-sky-500"></i>
                                                <span class="font-bold"><?= esc($lok['nama_lokasi']) ?></span>
                                            </span>
                                            <?php if ($isSel): ?>
                                            <span class="text-[10px] text-sky-500 font-bold"><i class="fa-solid fa-check"></i></span>
                                            <?php endif; ?>
                                        </div>
                                        <?php endforeach; ?>
                                        <div id="lokasiEmptyState" class="hidden px-3.5 py-3 text-center text-xs wf-muted">
                                            Lokasi baru — langsung klik <strong>Simpan</strong>, otomatis terdaftar di Master!
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Distance / Jarak -->
                        <div class="sm:col-span-2">
                            <label for="inputJarakWell" class="block text-xs font-bold wf-title mb-1">
                                Distance (KM)
                            </label>
                            <input type="text" id="inputJarakWell" inputmode="decimal" name="jarak"
                                value="<?= str_replace('.', ',', (string)old('jarak', $report['jarak'] ?? '0')) ?>"
                                placeholder="0"
                                class="wf-input w-full px-3 font-mono font-bold text-sm text-right">
                        </div>

                        <!-- Status Sumur -->
                        <?php $curStatus = strtoupper(trim((string)($report['status_job'] ?? 'JOB PROGRESS'))); ?>
                        <div class="sm:col-span-3">
                            <label for="selectStatusWell" class="block text-xs font-bold wf-title mb-1">
                                Status Pekerjaan
                            </label>
                            <select id="selectStatusWell" name="status_job" class="wf-input w-full px-2.5 font-bold text-xs cursor-pointer">
                                <option value="JOB PROGRESS" <?= ($curStatus !== 'JOB COMPLETED' && $curStatus !== 'JOB SUSPEND') ? 'selected' : '' ?>>ON PROGRESS</option>
                                <option value="JOB COMPLETED" <?= ($curStatus === 'JOB COMPLETED') ? 'selected' : '' ?>>JOB COMPLETED</option>
                                <option value="JOB SUSPEND" <?= ($curStatus === 'JOB SUSPEND') ? 'selected' : '' ?>>JOB SUSPEND</option>
                            </select>
                        </div>
                    </div>

                    <!-- ─── BARIS 2: TANGGAL MULAI (3 COL) + TANGGAL SELESAI (3 COL) + PILIH CEPAT DURASI (6 COL) ─── -->
                    <div class="wf-sub-box p-3.5 grid grid-cols-1 sm:grid-cols-12 gap-3.5 items-end">
                        <div class="sm:col-span-3">
                            <label for="inputTglMulai" class="block text-xs font-bold wf-title mb-1">
                                Tanggal Mulai <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_mulai" id="inputTglMulai" required
                                value="<?= esc($valMulai) ?>"
                                min="<?= esc($minDatePeriod) ?>"
                                max="<?= esc($maxDatePeriod) ?>"
                                oninput="syncWellDateRange()"
                                class="wf-input w-full px-2.5 font-mono font-bold text-xs sm:text-sm">
                        </div>

                        <div class="sm:col-span-3">
                            <label for="inputTglSelesai" class="block text-xs font-bold wf-title mb-1">
                                Tanggal Selesai (Fleksibel Berlanjut) <span class="text-rose-500">*</span>
                            </label>
                            <input type="date" name="tanggal_selesai" id="inputTglSelesai" required
                                value="<?= esc($valSelesai) ?>"
                                min="<?= esc($valMulai) ?>"
                                max="<?= esc($maxDatePeriod) ?>"
                                oninput="syncWellDateRange()"
                                class="wf-input w-full px-2.5 font-mono font-bold text-xs sm:text-sm">
                            <span class="text-[10px] wf-muted block mt-0.5">Otomatis berlanjut saat input log harian.</span>
                        </div>

                        <div class="sm:col-span-6">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[11px] font-bold wf-sec">Pilih Cepat Durasi Jadwal:</span>
                                <span id="badgeDurasiHari" class="text-[11px] font-mono font-extrabold text-sky-500">
                                    1 Hari Operasi
                                </span>
                            </div>
                            <div class="grid grid-cols-6 gap-1.5">
                                <button type="button" onclick="setDurationDays(1)" class="wf-chip-btn !px-0 !h-[42px]">1H</button>
                                <button type="button" onclick="setDurationDays(3)" class="wf-chip-btn !px-0 !h-[42px]">3H</button>
                                <button type="button" onclick="setDurationDays(4)" class="wf-chip-btn !px-0 !h-[42px]">4H</button>
                                <button type="button" onclick="setDurationDays(5)" class="wf-chip-btn !px-0 !h-[42px]">5H</button>
                                <button type="button" onclick="setDurationDays(7)" class="wf-chip-btn !px-0 !h-[42px]">7H</button>
                                <button type="button" onclick="setDurationDays(99)" class="wf-chip-btn !px-0 !h-[42px]" title="Sampai Akhir Bulan">Akhir</button>
                            </div>
                        </div>
                    </div>

                    <!-- ─── BARIS 3: ACTION BAR (LANGSUNG TERLIHAT TANPA SCROLL!) ─── -->
                    <div class="pt-3 wf-div-t flex flex-wrap items-center justify-between gap-3">
                        <a href="<?= $logHarianUrl ?>" class="wf-chip-btn !h-[44px] !px-4">
                            <i class="fa-solid fa-arrow-left"></i>
                            <span>Batal</span>
                        </a>

                        <div class="flex flex-wrap items-center gap-2.5">
                            <button type="submit" onclick="document.getElementById('redirect_action').value='grid'"
                                class="wf-chip-btn !h-[44px] !px-4">
                                <i class="fa-solid fa-table-list"></i>
                                <span><?= isset($report) ? 'Simpan Perubahan' : 'Simpan ke Tabel Sumur' ?></span>
                            </button>

                            <?php if (!isset($report)): ?>
                            <button type="submit" onclick="document.getElementById('redirect_action').value='log_harian'"
                                class="wf-btn-primary">
                                <i class="fa-solid fa-check-circle"></i>
                                <span>SIMPAN &amp; LANGSUNG ISI LOG HARIAN (WELL #<?= $targetWellNo ?>)</span>
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </form>
            </div>

            <!-- KOLOM KANAN (4 COL): RINGKASAN URUTAN SUMUR BULAN INI (INTERNAL COMPACT LIST) -->
            <div class="lg:col-span-4 p-5 flex flex-col justify-between" style="background: var(--wf-sub-bg); border-radius: 0 0 16px 0;">
                <div>
                    <div class="flex items-center justify-between gap-2 pb-2.5 wf-div-b mb-3">
                        <span class="text-xs font-extrabold uppercase tracking-wider wf-title flex items-center gap-1.5">
                            <i class="fa-solid fa-timeline text-sky-500"></i>
                            <span>Urutan Sumur Bulan Ini</span>
                        </span>
                        <span class="px-2 py-0.5 rounded bg-sky-500/15 text-sky-500 text-[11px] font-mono font-bold">
                            <?= count($existingWells ?? []) ?> Well
                        </span>
                    </div>

                    <div class="space-y-2 max-h-[215px] overflow-y-auto pr-1">
                        <?php if (!empty($existingWells)): ?>
                            <?php foreach ($existingWells as $ew): ?>
                            <?php
                                $ewStart = $ew['tanggal_mulai'] ?? '';
                                $ewEnd   = $ew['tanggal_selesai'] ?? $ewStart;
                            ?>
                            <div class="px-3 py-2 rounded-lg border flex items-center justify-between gap-2"
                                 style="background: var(--wf-card-bg); border-color: var(--wf-card-border);">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.5 rounded bg-sky-500/15 text-sky-500 font-mono text-[10px] font-black">
                                            #<?= esc($ew['no_well']) ?>
                                        </span>
                                        <span class="text-xs font-extrabold wf-title truncate">
                                            <?= esc($ew['nama_lokasi']) ?>
                                        </span>
                                    </div>
                                    <div class="text-[10px] font-mono wf-muted mt-0.5">
                                        <?= $ewStart ? date('d/m', strtotime($ewStart)) : '-' ?> - <?= $ewEnd ? date('d/m/Y', strtotime($ewEnd)) : '-' ?>
                                        · <strong class="wf-sec"><?= number_format((float)($ew['total_jam'] ?? 0), 2) ?>j</strong>
                                    </div>
                                </div>
                                <span class="px-1.5 py-0.5 rounded text-[9px] font-bold uppercase bg-emerald-500/15 text-emerald-500 shrink-0">
                                    Terdaftar
                                </span>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>

                        <?php if (!isset($report)): ?>
                        <div class="px-3 py-2.5 rounded-lg border-2 border-dashed border-sky-500/50 bg-sky-500/5 flex items-center justify-between gap-2">
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5">
                                    <span class="px-1.5 py-0.5 rounded bg-sky-600 text-white font-mono text-[10px] font-black">
                                        #<?= $targetWellNo ?>
                                    </span>
                                    <span id="previewWellNameCard" class="text-xs font-extrabold wf-title truncate">
                                        SUMUR BARU (SEDANG DIISI)
                                    </span>
                                </div>
                                <div id="previewWellDateCard" class="text-[10px] font-mono text-sky-500 font-bold mt-0.5">
                                    -
                                </div>
                            </div>
                            <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold uppercase bg-sky-500/20 text-sky-400 shrink-0">
                                Baru
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="pt-3 mt-3 wf-div-t text-[11px] wf-muted leading-snug">
                    <i class="fa-solid fa-bolt text-sky-500 mr-1"></i>
                    Klik <strong>Simpan &amp; Langsung Isi Log Harian</strong> untuk otomatis beralih ke pengisian jam MIRU &amp; Operasi Well #<?= $targetWellNo ?>.
                </div>
            </div>

        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const periodMinDate = '<?= esc($minDatePeriod) ?>';
    const periodMaxDate = '<?= esc($maxDatePeriod) ?>';

    function addDaysIso(dateStr, days) {
        if (!dateStr) return periodMinDate;
        const d = new Date(dateStr + 'T00:00:00');
        d.setDate(d.getDate() + days);
        const yyyy = d.getFullYear();
        const mm = String(d.getMonth() + 1).padStart(2, '0');
        const dd = String(d.getDate()).padStart(2, '0');
        const res = `${yyyy}-${mm}-${dd}`;
        if (res < periodMinDate) return periodMinDate;
        if (res > periodMaxDate) return periodMaxDate;
        return res;
    }

    function setWellStartDateOption(dateStr, mode) {
        const startEl = document.getElementById('inputTglMulai');
        const endEl   = document.getElementById('inputTglSelesai');
        const btnSame = document.getElementById('btnOptSameDay');
        const btnNext = document.getElementById('btnOptNextDay');
        if (!startEl || !endEl) return;

        startEl.value = dateStr;
        if (endEl.value < dateStr) {
            endEl.value = addDaysIso(dateStr, 3);
        }
        if (btnSame && btnNext) {
            btnSame.classList.toggle('is-active', mode === 'same');
            btnNext.classList.toggle('is-active', mode === 'next');
        }
        syncWellDateRange();
    }

    function setDurationDays(daysCount) {
        const startEl = document.getElementById('inputTglMulai');
        const endEl   = document.getElementById('inputTglSelesai');
        if (!startEl || !endEl) return;
        const base = startEl.value || periodMinDate;
        if (daysCount >= 90) {
            endEl.value = periodMaxDate;
        } else {
            endEl.value = addDaysIso(base, Math.max(0, daysCount - 1));
        }
        syncWellDateRange();
    }

    function syncWellDateRange() {
        const startEl   = document.getElementById('inputTglMulai');
        const endEl     = document.getElementById('inputTglSelesai');
        const badgeEl   = document.getElementById('badgeDurasiHari');
        const prevDate  = document.getElementById('previewWellDateCard');

        if (!startEl || !endEl) return;

        if (startEl.value) {
            endEl.min = startEl.value;
            if (!endEl.value || endEl.value < startEl.value) {
                endEl.value = startEl.value;
            }
        }

        if (startEl.value && endEl.value) {
            const d1 = new Date(startEl.value + 'T00:00:00');
            const d2 = new Date(endEl.value + 'T00:00:00');
            const diffDays = Math.max(1, Math.round((d2 - d1) / (1000 * 60 * 60 * 24)) + 1);
            const fmt = (d) => d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
            if (badgeEl) badgeEl.textContent = `${diffDays} Hari (${fmt(d1)} - ${fmt(d2)})`;
            if (prevDate) prevDate.textContent = `${fmt(d1)} s/d ${fmt(d2)} (${diffDays} Hari)`;
        }
    }

    function toggleLokasiDropdown() {
        const menu = document.getElementById('lokasiDropdownMenu');
        if (menu.classList.contains('hidden')) {
            openLokasiDropdown();
        } else {
            closeLokasiDropdown();
        }
    }

    function openLokasiDropdown() {
        const menu = document.getElementById('lokasiDropdownMenu');
        const icon = document.getElementById('lokasiChevronIcon');
        menu.classList.remove('hidden');
        if (icon) icon.classList.add('rotate-180');
        filterLokasiList();
    }

    function closeLokasiDropdown() {
        const menu = document.getElementById('lokasiDropdownMenu');
        const icon = document.getElementById('lokasiChevronIcon');
        menu.classList.add('hidden');
        if (icon) icon.classList.remove('rotate-180');
    }

    function filterLokasiList() {
        const input = document.getElementById('lokasiSearchInput');
        const rawVal = input.value.trim();
        const q = rawVal.toLowerCase();
        const items = document.querySelectorAll('#lokasiOptionsList .lokasi-option-item');
        const emptyState = document.getElementById('lokasiEmptyState');
        const countEl = document.getElementById('lokasiMatchCount');
        const createNewEl = document.getElementById('lokasiCreateNewItem');
        const previewEl = document.getElementById('newLokasiPreviewText');
        const hintEl = document.getElementById('lokasiStatusHint');
        const cardNameEl = document.getElementById('previewWellNameCard');

        if (cardNameEl) {
            cardNameEl.textContent = rawVal ? rawVal.toUpperCase() : 'SUMUR BARU (SEDANG DIISI)';
        }

        let matches = 0;
        let exactMatchId = null;

        items.forEach(item => {
            const name = (item.getAttribute('data-nama') || '');
            const nameLower = name.toLowerCase().trim();
            if (q !== '' && nameLower === q) {
                exactMatchId = item.getAttribute('data-id');
            }
            if (q === '' || nameLower.includes(q)) {
                item.style.display = '';
                matches++;
            } else {
                item.style.display = 'none';
            }
        });

        if (exactMatchId) {
            document.getElementById('lokasi_id').value = exactMatchId;
            if (createNewEl) createNewEl.classList.add('hidden');
            if (hintEl) {
                hintEl.textContent = '✓ Terdaftar';
                hintEl.className = 'text-[10px] text-emerald-500 font-bold';
            }
        } else if (q !== '') {
            document.getElementById('lokasi_id').value = '';
            if (previewEl) previewEl.textContent = rawVal.toUpperCase();
            if (createNewEl) createNewEl.classList.remove('hidden');
            if (hintEl) {
                hintEl.textContent = '+ Lokasi Baru';
                hintEl.className = 'text-[10px] text-sky-500 font-bold';
            }
        } else {
            document.getElementById('lokasi_id').value = '';
            if (createNewEl) createNewEl.classList.add('hidden');
            if (hintEl) {
                hintEl.textContent = 'Ketik / pilih';
                hintEl.className = 'text-[10px] text-sky-500 font-bold';
            }
        }

        if (countEl) countEl.textContent = `${matches} lokasi`;
        if (emptyState) emptyState.classList.toggle('hidden', matches > 0);
    }

    function selectLokasi(id, nama) {
        const input = document.getElementById('lokasiSearchInput');
        const hiddenId = document.getElementById('lokasi_id');
        const clearBtn = document.getElementById('btnClearLokasi');
        const hintEl = document.getElementById('lokasiStatusHint');
        const cardNameEl = document.getElementById('previewWellNameCard');

        hiddenId.value = id;
        input.value = nama;
        input.setAttribute('data-selected-name', nama);
        if (clearBtn) clearBtn.classList.remove('hidden');
        if (cardNameEl) cardNameEl.textContent = nama.toUpperCase();
        if (hintEl) {
            hintEl.textContent = '✓ Terdaftar';
            hintEl.className = 'text-[10px] text-emerald-500 font-bold';
        }
        closeLokasiDropdown();
    }

    function useNewLokasiName() {
        const input = document.getElementById('lokasiSearchInput');
        const hiddenId = document.getElementById('lokasi_id');
        const clearBtn = document.getElementById('btnClearLokasi');
        hiddenId.value = '';
        input.value = input.value.trim().toUpperCase();
        if (clearBtn && input.value !== '') clearBtn.classList.remove('hidden');
        closeLokasiDropdown();
    }

    function clearLokasiSelection() {
        const input = document.getElementById('lokasiSearchInput');
        const hiddenId = document.getElementById('lokasi_id');
        const clearBtn = document.getElementById('btnClearLokasi');
        hiddenId.value = '';
        input.value = '';
        input.setAttribute('data-selected-name', '');
        if (clearBtn) clearBtn.classList.add('hidden');
        openLokasiDropdown();
        input.focus();
    }

    document.addEventListener('DOMContentLoaded', () => {
        syncWellDateRange();
        const input = document.getElementById('lokasiSearchInput');
        const wrapper = document.getElementById('lokasiComboboxWrapper');
        const clearBtn = document.getElementById('btnClearLokasi');

        if (input) {
            input.addEventListener('focus', () => openLokasiDropdown());
            input.addEventListener('input', () => {
                if (clearBtn) clearBtn.classList.toggle('hidden', input.value.trim() === '');
                openLokasiDropdown();
            });
        }

        document.addEventListener('click', (e) => {
            if (wrapper && !wrapper.contains(e.target)) {
                closeLokasiDropdown();
            }
        });
    });
</script>
<?= $this->endSection() ?>
