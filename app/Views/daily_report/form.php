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
                        <span class="text-[10px] text-amber-400 font-normal">Ketik untuk mencari cepat</span>
                    </label>

                    <!-- Hidden Input for Form Submission -->
                    <input type="hidden" name="lokasi_id" id="lokasi_id" value="<?= esc($selectedLokId) ?>" required>

                    <!-- Searchable Combobox Container -->
                    <div class="relative" id="lokasiComboboxWrapper">
                        <div class="flex items-center bg-slate-950 border border-slate-700 rounded-lg px-3 py-2 focus-within:border-amber-400 focus-within:ring-2 focus-within:ring-amber-400/20 transition shadow-inner">
                            <i class="fa-solid fa-location-dot text-amber-400 text-xs mr-2 flex-shrink-0"></i>
                            <input type="text" id="lokasiSearchInput" 
                                   placeholder="Ketik untuk mencari lokasi (cth: 3J, Duri, Minas...)" 
                                   value="<?= esc($selectedLokNama) ?>"
                                   data-selected-name="<?= esc($selectedLokNama) ?>"
                                   autocomplete="off"
                                   class="bg-transparent border-0 text-xs font-bold text-white focus:outline-none w-full placeholder-slate-500">
                            
                            <!-- Clear Selection Button -->
                            <button type="button" id="btnClearLokasi" onclick="clearLokasiSelection()" 
                                    class="text-slate-400 hover:text-white px-1.5 py-0.5 rounded text-xs transition <?= empty($selectedLokId) ? 'hidden' : '' ?>" title="Hapus / Ganti Lokasi">
                                <i class="fa-solid fa-xmark"></i>
                            </button>

                            <!-- Dropdown Toggle Arrow -->
                            <button type="button" id="btnToggleLokasiDropdown" onclick="toggleLokasiDropdown()"
                                    class="text-slate-400 hover:text-amber-300 px-1.5 py-0.5 rounded text-xs transition ml-0.5" title="Buka / Tutup Daftar Lokasi">
                                <i class="fa-solid fa-chevron-down transition-transform duration-200" id="lokasiChevronIcon"></i>
                            </button>
                        </div>

                        <!-- Dropdown List of Locations with Live Filter -->
                        <div id="lokasiDropdownMenu" class="hidden absolute left-0 right-0 top-full mt-1.5 bg-slate-900 border-2 border-slate-700 rounded-xl shadow-2xl z-50 overflow-hidden max-h-60 flex flex-col">
                            <div class="px-3.5 py-2 bg-slate-850 border-b border-slate-750 flex items-center justify-between text-[11px] text-slate-400 font-bold">
                                <span>PILIH NAMA LOKASI:</span>
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
                                <div id="lokasiEmptyState" class="hidden px-4 py-5 text-center text-xs text-slate-400">
                                    <i class="fa-solid fa-circle-question text-base text-slate-500 mb-1 block"></i>
                                    Tidak ada lokasi yang cocok dengan kata kunci tersebut.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Distance (Rp)</label>
                    <input type="number" name="jarak" value="<?= old('jarak', $report['jarak'] ?? '0') ?>" min="0"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white font-num text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="<?= old('tanggal_mulai', $report['tanggal_mulai'] ?? '') ?>"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="tanggal_selesai" value="<?= old('tanggal_selesai', $report['tanggal_selesai'] ?? '') ?>"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white text-xs focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- Bagian 2: Jam Operasi Utama (MIRU & OPS) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="p-3.5 rounded-xl bg-lime-950/20 border border-lime-500/30">
                    <label class="block text-xs font-bold text-lime-400 uppercase mb-1.5">MIRU (Jam)</label>
                    <input type="number" step="0.25" min="0" name="miru_jam" id="miru_jam" value="<?= old('miru_jam', $report['miru_jam'] ?? '0') ?>" oninput="calcTotalJam()" required
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-lime-400 font-num font-bold text-sm focus:ring-2 focus:ring-lime-500">
                    <p class="text-[10.5px] text-slate-400 mt-1">Durasi Move In / Rig Up</p>
                </div>

                <div class="p-3.5 rounded-xl bg-lime-950/20 border border-lime-500/30">
                    <label class="block text-xs font-bold text-lime-400 uppercase mb-1.5">OPS (Jam)</label>
                    <input type="number" step="0.25" min="0" name="ops_jam" id="ops_jam" value="<?= old('ops_jam', $report['ops_jam'] ?? '0') ?>" oninput="calcTotalJam()" required
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-lime-400 font-num font-bold text-sm focus:ring-2 focus:ring-lime-500">
                    <p class="text-[10.5px] text-slate-400 mt-1">Durasi Operasi Pengeboran / Workover</p>
                </div>
            </div>

            <!-- Bagian 3: Pos Rincian Downtime (SBWC & UNPAID persis seperti Excel) -->
            <div class="p-4 rounded-xl bg-slate-900/80 border border-slate-750 space-y-4">
                <div class="border-b border-slate-750 pb-2 flex items-center justify-between">
                    <span class="text-xs font-bold text-yellow-400 uppercase flex items-center gap-1.5">
                        <i class="fa-solid fa-clock"></i>
                        <span>Rincian Jam Downtime Per Pos (SBWC & UNPAID)</span>
                    </span>
                    <span class="text-[11px] text-slate-400">Total DT: <strong id="previewTotalDt" class="text-orange-300 font-num">0.00</strong> Jam</span>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <?php foreach ($kategoriList as $k): 
                        $kId = $k['id'];
                        $val = $dtMap[$kId] ?? 0;
                    ?>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-300 truncate mb-1" title="<?= esc($k['nama']) ?>">
                            <?= esc($k['nama']) ?>
                            <span class="text-[9px] <?= $k['tipe'] == 'UNPAID' ? 'text-rose-400' : 'text-yellow-400' ?> font-mono">(<?= $k['tipe'] ?>)</span>
                        </label>
                        <input type="number" step="0.25" min="0" name="dt[<?= $kId ?>]" value="<?= $val > 0 ? $val : '' ?>" placeholder="0"
                            oninput="calcTotalJam()"
                            class="dt-input w-full px-2.5 py-1.5 bg-slate-950 border border-slate-700 rounded-lg text-white font-num text-xs focus:ring-2 focus:ring-blue-500">
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Ringkasan Total Jam Sumur -->
            <div class="p-4 rounded-xl bg-slate-950 border border-slate-700 flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-300 uppercase">Formula:</span>
                    <span class="text-xs text-slate-400">Total Jam = MIRU + OPS + Total DT</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="text-right">
                        <span class="text-[11px] text-slate-400 block">TOTAL HOURS:</span>
                        <span id="previewTotalJam" class="font-num font-black text-xl text-emerald-400">0.00 Jam</span>
                    </div>
                </div>
            </div>

            <!-- Status Job & Remark -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Status Job</label>
                    <select name="status_job" class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white font-semibold text-xs focus:ring-2 focus:ring-blue-500">
                        <option value="JOB COMPLETED" <?= (old('status_job', $report['status_job'] ?? '') == 'JOB COMPLETED') ? 'selected' : '' ?>>JOB COMPLETED</option>
                        <option value="JOB PROGRESS" <?= (old('status_job', $report['status_job'] ?? '') == 'JOB PROGRESS') ? 'selected' : '' ?>>JOB PROGRESS</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1.5">Remark Umum / SBWC</label>
                    <input type="text" name="remark" value="<?= old('remark', $report['remark'] ?? '') ?>" placeholder="Catatan kendala operasi umum/SBWC"
                        class="w-full px-3 py-2 bg-slate-950 border border-slate-700 rounded-lg text-white text-xs focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-xs font-bold text-rose-400 uppercase mb-1.5 flex items-center gap-1.5">
                        <i class="fa-solid fa-triangle-exclamation text-rose-400"></i>
                        <span>Remark UNPAID (Sinkron ke NPT)</span>
                    </label>
                    <input type="text" name="remark_unpaid" value="<?= old('remark_unpaid', $report['remark_unpaid'] ?? '') ?>" placeholder="Cth: Unpaid 1 HR Pump Rusak, Part patah"
                        class="w-full px-3 py-2 bg-slate-950 border border-rose-500/50 rounded-lg text-rose-200 text-xs focus:ring-2 focus:ring-rose-500 placeholder:text-slate-600">
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-700">
                <a href="<?= base_url('daily-report/' . ($report['rig_id'] ?? $rigId) . '/' . ($report['bulan'] ?? $bulan) . '/' . ($report['tahun'] ?? $tahun)) ?>" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white font-semibold text-xs rounded-lg transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs rounded-lg shadow-lg shadow-blue-600/30 transition flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>SIMPAN PEKERJAAN SUMUR</span>
                </button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function calcTotalJam() {
        const miru = parseFloat(document.getElementById('miru_jam').value) || 0;
        const ops = parseFloat(document.getElementById('ops_jam').value) || 0;
        
        let sumDt = 0;
        document.querySelectorAll('.dt-input').forEach(inp => {
            const val = parseFloat(inp.value);
            if (!isNaN(val)) sumDt += val;
        });

        document.getElementById('previewTotalDt').innerText = sumDt.toFixed(2);
        
        const total = miru + ops + sumDt;
        document.getElementById('previewTotalJam').innerText = total.toFixed(2) + ' Jam';
    }
    calcTotalJam();

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
        const q = input.value.toLowerCase().trim();
        const items = document.querySelectorAll('#lokasiOptionsList .lokasi-option-item');
        const emptyState = document.getElementById('lokasiEmptyState');
        const countEl = document.getElementById('lokasiMatchCount');

        let matches = 0;
        items.forEach(item => {
            const name = (item.getAttribute('data-nama') || '').toLowerCase();
            if (q === '' || name.includes(q)) {
                item.style.display = '';
                matches++;
            } else {
                item.style.display = 'none';
            }
        });

        if (countEl) countEl.innerText = matches + ' lokasi';
        if (emptyState) {
            emptyState.style.display = matches === 0 ? 'block' : 'none';
        }
    }

    function selectLokasi(id, nama) {
        document.getElementById('lokasi_id').value = id;
        const input = document.getElementById('lokasiSearchInput');
        input.value = nama;
        input.setAttribute('data-selected-name', nama);
        document.getElementById('btnClearLokasi').classList.remove('hidden');
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
            // Invalidate hidden id if text is modified
            const selectedName = lokasiInput.getAttribute('data-selected-name');
            if (lokasiInput.value !== selectedName) {
                document.getElementById('lokasi_id').value = '';
                document.getElementById('btnClearLokasi').classList.add('hidden');
            }
            filterLokasiList();
        });
        lokasiInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                // Select first visible match
                const firstVisible = document.querySelector('#lokasiOptionsList .lokasi-option-item:not([style*="display: none"])');
                if (firstVisible) {
                    const id = firstVisible.getAttribute('data-id');
                    const nama = firstVisible.getAttribute('data-nama');
                    selectLokasi(id, nama);
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
