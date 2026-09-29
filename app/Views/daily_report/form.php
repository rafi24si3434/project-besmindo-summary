<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen ui-screen--form max-w-4xl mx-auto space-y-6">
    <div class="p-6 rounded-2xl bg-slate-800 border border-slate-700 shadow-xl space-y-6">
        <div class="border-b border-slate-700 pb-4 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-white"><?= isset($report) ? "Edit Pekerjaan Sumur #{$report['no_well']}" : "Form Input Pekerjaan Sumur #{$nextNoWell}" ?></h3>
                <p class="text-xs text-slate-400">Armada Rig: <strong class="text-blue-400"><?= esc($rig['kode']) ?></strong> (Periode Bulan <?= $report['bulan'] ?? $bulan ?>/<?= $report['tahun'] ?? $tahun ?>)</p>
            </div>
            <a href="<?= base_url('daily-report/' . ($report['rig_id'] ?? $rigId) . '/' . ($report['bulan'] ?? $bulan) . '/' . ($report['tahun'] ?? $tahun)) ?>" class="text-xs text-slate-400 hover:text-white flex items-center gap-1.5 transition">
                <i class="fa-solid fa-arrow-left"></i> Kembali ke Matriks
            </a>
        </div>

        <form action="<?= isset($report) ? base_url('daily-report/update/' . $report['id']) : base_url('daily-report/simpan') ?>" method="POST" class="space-y-6">
            <?= csrf_field() ?>
            <input type="hidden" name="rig_id" value="<?= $report['rig_id'] ?? $rigId ?>">
            <input type="hidden" name="bulan" value="<?= $report['bulan'] ?? $bulan ?>">
            <input type="hidden" name="tahun" value="<?= $report['tahun'] ?? $tahun ?>">

            <!-- Bagian 1: Identitas Sumur -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 p-4 rounded-xl bg-slate-900/60 border border-slate-750">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">No Well</label>
                    <input type="number" name="no_well" value="<?= old('no_well', $report['no_well'] ?? $nextNoWell) ?>" required min="1"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white font-num font-bold text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
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
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5 flex items-center justify-between">
                        <span>Location (Nama Sumur) <span class="text-rose-400">*</span></span>
                        <span id="lokasiStatusHint" class="text-[10px] text-amber-400 font-normal">Ketik pilih atau buat lokasi baru otomatis</span>
                    </label>

                    <!-- Hidden Input for Form Submission (Optional if user types a new name) -->
                    <input type="hidden" name="lokasi_id" id="lokasi_id" value="<?= esc($selectedLokId) ?>">

                    <!-- Searchable & Auto-Create Combobox Container -->
                    <div class="relative" id="lokasiComboboxWrapper">
                        <div class="flex items-center bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 focus-within:border-amber-400 focus-within:ring-2 focus-within:ring-amber-400/20 transition shadow-inner">
                            <i class="fa-solid fa-location-dot text-amber-400 text-xs mr-2 flex-shrink-0"></i>
                            <input type="text" name="nama_lokasi_input" id="lokasiSearchInput" required
                                   placeholder="Ketik nama sumur (cth: KB 387, PH 155, 3J...)" 
                                   value="<?= esc(old('nama_lokasi_input', $selectedLokNama)) ?>"
                                   data-selected-name="<?= esc($selectedLokNama) ?>"
                                   autocomplete="off"
                                   class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none w-full placeholder-slate-500 uppercase">
                            
                            <!-- Clear Selection Button -->
                            <button type="button" id="btnClearLokasi" onclick="clearLokasiSelection()" 
                                    class="text-slate-400 hover:text-white px-1.5 py-0.5 rounded text-xs transition <?= empty($selectedLokId) && empty($selectedLokNama) ? 'hidden' : '' ?>" title="Hapus / Ganti Lokasi">
                                <i class="fa-solid fa-xmark"></i>
                            </button>

                            <!-- Dropdown Toggle Arrow -->
                            <button type="button" id="btnToggleLokasiDropdown" onclick="toggleLokasiDropdown()"
                                    class="text-slate-400 hover:text-amber-300 px-1.5 py-0.5 rounded text-xs transition ml-0.5" title="Buka / Tutup Daftar Lokasi">
                                <i class="fa-solid fa-chevron-down transition-transform duration-200" id="lokasiChevronIcon"></i>
                            </button>
                        </div>

                        <!-- Dropdown List of Locations with Live Filter & Instant Auto-Create -->
                        <div id="lokasiDropdownMenu" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-slate-900 border-2 border-slate-700 rounded-xl shadow-2xl z-50 overflow-hidden max-h-64 flex flex-col">
                            <!-- Instant Auto-Create Banner when typed name is new -->
                            <div id="lokasiCreateNewItem" onclick="useNewLokasiName()" class="hidden px-3.5 py-2.5 bg-emerald-950/80 hover:bg-emerald-900 border-b border-emerald-500/40 cursor-pointer transition flex items-center justify-between text-xs text-emerald-300 font-bold">
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid fa-plus-circle text-emerald-400"></i>
                                    <span>Buat &amp; Gunakan: "<strong id="newLokasiPreviewText" class="text-white uppercase"></strong>"</span>
                                </span>
                                <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-[10px] font-mono text-emerald-300 border border-emerald-500/40">Otomatis Simpan Master</span>
                            </div>

                            <div class="px-3.5 py-2 bg-slate-850 border-b border-slate-750 flex items-center justify-between text-[11px] text-slate-400 font-bold">
                                <span>DAFTAR LOKASI TERDAFTAR:</span>
                                <span id="lokasiMatchCount" class="text-amber-400 font-mono"><?= count($lokasiList) ?> lokasi</span>
                            </div>
                            <div class="overflow-y-auto custom-scrollbar divide-y divide-slate-800/80" id="lokasiOptionsList">
                                <?php foreach ($lokasiList as $lok): 
                                    $isSel = ($selectedLokId == $lok['id']);
                                ?>
                                <div class="lokasi-option-item px-3.5 py-2 hover:bg-amber-500/20 hover:text-white cursor-pointer transition flex items-center justify-between text-xs text-slate-200 <?= $isSel ? 'bg-amber-500/15 text-amber-300 font-black' : '' ?>"
                                     data-id="<?= $lok['id'] ?>" 
                                     data-nama="<?= esc($lok['nama_lokasi']) ?>"
                                     onclick="selectLokasi(<?= $lok['id'] ?>, '<?= esc($lok['nama_lokasi'], 'js') ?>')">
                                    <span class="flex items-center gap-2">
                                        <i class="fa-solid fa-location-dot text-[10px] text-amber-400"></i>
                                        <span class="lokasi-text font-bold"><?= esc($lok['nama_lokasi']) ?></span>
                                    </span>
                                    <?php if ($isSel): ?>
                                        <span class="text-[10px] text-amber-400 font-bold flex items-center gap-1">
                                            <i class="fa-solid fa-check"></i> Terpilih
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <?php endforeach; ?>
                                <div id="lokasiEmptyState" class="hidden px-4 py-4 text-center text-xs text-emerald-300 bg-emerald-950/20">
                                    <i class="fa-solid fa-wand-magic-sparkles text-sm text-emerald-400 mb-1 block"></i>
                                    Lokasi belum ada di Master Data — klik tombol hijau di atas atau langsung klik <strong>Simpan</strong>, sistem akan otomatis menyimpannya!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Distance (Rp)</label>
                    <input type="text" inputmode="decimal" name="jarak" value="<?= str_replace('.', ',', (string)old('jarak', $report['jarak'] ?? '0')) ?>" placeholder="Contoh: 2,5"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white font-num text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <?php
                    $bBulan = (int)($report['bulan'] ?? $bulan);
                    $bTahun = (int)($report['tahun'] ?? $tahun);
                    $maxDaysPeriod = cal_days_in_month(CAL_GREGORIAN, $bBulan, $bTahun);
                    $minDatePeriod = sprintf('%04d-%02d-01', $bTahun, $bBulan);
                    $maxDatePeriod = sprintf('%04d-%02d-%02d', $bTahun, $bBulan, $maxDaysPeriod);
                    $valMulai   = old('tanggal_mulai', $report['tanggal_mulai'] ?? $minDatePeriod);
                    $valSelesai = old('tanggal_selesai', $report['tanggal_selesai'] ?? $valMulai);
                ?>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">
                        Tanggal Mulai <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" name="tanggal_mulai" id="inputTglMulai" required
                        value="<?= esc($valMulai) ?>"
                        oninput="syncWellDateRange()"
                        class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg text-white font-bold text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">
                        Tanggal Selesai <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" name="tanggal_selesai" id="inputTglSelesai" required
                        value="<?= esc($valSelesai) ?>"
                        min="<?= esc($valMulai) ?>"
                        oninput="syncWellDateRange()"
                        class="w-full px-3 py-2.5 bg-slate-950 border border-slate-700 rounded-lg text-white font-bold text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- Live Info Rentang Tanggal Sumur -->
                <div class="sm:col-span-4 pt-1">
                    <div class="px-3.5 py-2.5 rounded-xl bg-blue-950/40 border border-blue-500/30 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-2 text-xs text-blue-200">
                            <i class="fa-solid fa-calendar-check text-blue-400"></i>
                            <span>Jadwal Input Harian Sumur Ini: <strong id="labelRentangTanggal" class="text-amber-300 font-mono">-</strong></span>
                        </div>
                        <span id="badgeDurasiHari" class="px-2.5 py-0.5 rounded-md bg-blue-500/20 border border-blue-400/30 text-[11px] font-bold text-blue-300 font-mono">
                            1 Hari
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1.5">
                        <i class="fa-solid fa-circle-info text-sky-400 mr-1"></i>
                        Saat mengisi <strong>Log Harian (MIRU, OPS, SBWC &amp; UNPAID)</strong>, operator hanya dapat menginput pada rentang tanggal yang tertera di atas.
                    </p>
                </div>

                <?php $curStatus = strtoupper(trim((string)($report['status_job'] ?? 'JOB PROGRESS'))); ?>
                <div class="sm:col-span-4 pt-2 border-t border-slate-800 flex flex-wrap items-center justify-between gap-3">
                    <span class="text-xs font-bold text-slate-300 uppercase">Status Pekerjaan Sumur:</span>
                    <div class="flex items-center gap-2">
                        <select name="status_job" class="px-3 py-1.5 bg-slate-950 border border-slate-700 rounded-lg text-white font-bold text-xs focus:ring-2 focus:ring-blue-500">
                            <option value="JOB PROGRESS" <?= ($curStatus !== 'JOB COMPLETED' && $curStatus !== 'JOB SUSPEND') ? 'selected' : '' ?>>ON PROGRESS (Sedang Dikerjakan)</option>
                            <option value="JOB COMPLETED" <?= ($curStatus === 'JOB COMPLETED') ? 'selected' : '' ?>>JOB COMPLETED (Selesai)</option>
                            <option value="JOB SUSPEND" <?= ($curStatus === 'JOB SUSPEND') ? 'selected' : '' ?>>JOB SUSPEND (Ditunda / Suspend)</option>
                        </select>
                    </div>
                </div>
            </div>

            <input type="hidden" name="redirect_action" id="redirect_action" value="log_harian">

            <div class="pt-4 flex flex-wrap items-center justify-between gap-3 border-t border-slate-700">
                <a href="<?= base_url('daily-report/' . ($report['rig_id'] ?? $rigId) . '/' . ($report['bulan'] ?? $bulan) . '/' . ($report['tahun'] ?? $tahun)) ?>" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-300 hover:text-white font-semibold text-xs rounded-xl transition">
                    Batal
                </a>
                <div class="flex flex-wrap items-center gap-2.5">
                    <button type="submit" onclick="document.getElementById('redirect_action').value='grid'" class="px-4 py-2.5 bg-slate-700 hover:bg-slate-600 text-white font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                        <i class="fa-solid fa-table-list"></i>
                        <span><?= isset($report) ? 'Simpan Perubahan' : 'Simpan ke Daftar Sumur' ?></span>
                    </button>
                    <?php if (!isset($report)): ?>
                    <button type="submit" onclick="document.getElementById('redirect_action').value='log_harian'" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-500 text-white font-extrabold text-xs rounded-xl shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                        <i class="fa-solid fa-bolt"></i>
                        <span>SIMPAN &amp; ISI LOG HARIAN</span>
                    </button>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function syncWellDateRange() {
        const startEl = document.getElementById('inputTglMulai');
        const endEl   = document.getElementById('inputTglSelesai');
        const labelEl = document.getElementById('labelRentangTanggal');
        const badgeEl = document.getElementById('badgeDurasiHari');

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
            const fmt = (d) => d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
            labelEl.textContent = `${fmt(d1)} s/d ${fmt(d2)}`;
            badgeEl.textContent = `${diffDays} Hari Operasi`;
        }
    }
    syncWellDateRange();

    // ═══ Searchable Location Combobox Logic ═══
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

        let matches = 0;
        let exactMatchId = null;
        let exactMatchName = null;

        items.forEach(item => {
            const name = (item.getAttribute('data-nama') || '');
            const nameLower = name.toLowerCase().trim();
            if (q !== '' && nameLower === q) {
                exactMatchId = item.getAttribute('data-id');
                exactMatchName = name;
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
                hintEl.textContent = '✓ Lokasi Terdaftar';
                hintEl.className = 'text-[10px] text-emerald-400 font-bold';
            }
        } else if (q !== '') {
            document.getElementById('lokasi_id').value = '';
            if (previewEl) previewEl.textContent = rawVal.toUpperCase();
            if (createNewEl) createNewEl.classList.remove('hidden');
            if (hintEl) {
                hintEl.textContent = `✨ Lokasi Baru "${rawVal.toUpperCase()}" (Otomatis Simpan)`;
                hintEl.className = 'text-[10px] text-emerald-400 font-bold';
            }
        } else {
            document.getElementById('lokasi_id').value = '';
            if (createNewEl) createNewEl.classList.add('hidden');
            if (hintEl) {
                hintEl.textContent = 'Ketik pilih atau buat lokasi baru otomatis';
                hintEl.className = 'text-[10px] text-amber-400 font-normal';
            }
        }

        if (countEl) countEl.innerText = matches + ' lokasi';
        if (emptyState) {
            emptyState.style.display = matches === 0 ? 'block' : 'none';
        }
    }

    function useNewLokasiName() {
        const input = document.getElementById('lokasiSearchInput');
        const rawVal = input.value.trim().toUpperCase();
        if (!rawVal) return;
        input.value = rawVal;
        document.getElementById('lokasi_id').value = '';
        document.getElementById('btnClearLokasi').classList.remove('hidden');
        const hintEl = document.getElementById('lokasiStatusHint');
        if (hintEl) {
            hintEl.textContent = `✓ Lokasi Baru "${rawVal}" Siap Disimpan Otomatis`;
            hintEl.className = 'text-[10px] text-emerald-400 font-bold';
        }
        closeLokasiDropdown();
    }

    function selectLokasi(id, nama) {
        document.getElementById('lokasi_id').value = id;
        const input = document.getElementById('lokasiSearchInput');
        input.value = nama;
        input.setAttribute('data-selected-name', nama);
        document.getElementById('btnClearLokasi').classList.remove('hidden');
        const hintEl = document.getElementById('lokasiStatusHint');
        if (hintEl) {
            hintEl.textContent = '✓ Lokasi Terpilih';
            hintEl.className = 'text-[10px] text-emerald-400 font-bold';
        }
        closeLokasiDropdown();

        // Highlight selected item
        document.querySelectorAll('#lokasiOptionsList .lokasi-option-item').forEach(item => {
            if (item.getAttribute('data-id') == id) {
                item.classList.add('bg-amber-500/15', 'text-amber-300', 'font-black');
            } else {
                item.classList.remove('bg-amber-500/15', 'text-amber-300', 'font-black');
            }
        });
    }

    function clearLokasiSelection() {
        document.getElementById('lokasi_id').value = '';
        const input = document.getElementById('lokasiSearchInput');
        input.value = '';
        input.setAttribute('data-selected-name', '');
        input.focus();
        document.getElementById('btnClearLokasi').classList.add('hidden');
        openLokasiDropdown();
    }

    // Event Listeners for Combobox
    const lokasiInput = document.getElementById('lokasiSearchInput');
    if (lokasiInput) {
        lokasiInput.addEventListener('focus', () => {
            openLokasiDropdown();
        });
        lokasiInput.addEventListener('input', () => {
            openLokasiDropdown();
            document.getElementById('btnClearLokasi').classList.toggle('hidden', lokasiInput.value.trim() === '');
            filterLokasiList();
        });
        lokasiInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const firstVisible = document.querySelector('#lokasiOptionsList .lokasi-option-item:not([style*="display: none"])');
                if (firstVisible && firstVisible.getAttribute('data-nama').toLowerCase() === lokasiInput.value.trim().toLowerCase()) {
                    selectLokasi(firstVisible.getAttribute('data-id'), firstVisible.getAttribute('data-nama'));
                } else {
                    useNewLokasiName();
                }
            } else if (e.key === 'Escape') {
                closeLokasiDropdown();
            }
        });
    }

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        const wrapper = document.getElementById('lokasiComboboxWrapper');
        if (wrapper && !wrapper.contains(e.target)) {
            closeLokasiDropdown();
        }
    });
</script>
<?= $this->endSection() ?>
