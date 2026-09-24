<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="ui-screen ui-screen--report space-y-5">
    <!-- Filter Navigation Bar -->
    <div class="ui-toolbar flex-wrap justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-md flex items-center justify-center flex-shrink-0"
                style="background:var(--danger-bg);color:var(--danger-fg);border:1px solid var(--danger-border)">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold" style="color:var(--foreground)">Rekapitulasi NPT (Non-Productive Time)</h3>
                <p class="text-xs" style="color:var(--muted-foreground)">
                    Tahun <strong class="font-mono" style="color:var(--primary)"><?= $tahun ?></strong>
                    &mdash; Periode:
                    <strong style="color:var(--success-fg)"><?= [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'Mei',6=>'Jun',7=>'Jul',8=>'Agu',9=>'Sep',10=>'Okt',11=>'Nov',12=>'Des'][$activeMonth] ?? '' ?> <?= $tahun ?></strong>
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <?php 
            $monthNames = [
                1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
            ]; ?>

            <!-- Selector Bulan -->
            <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-md" style="background:var(--input);border:1px solid var(--border)">
                <i class="fa-solid fa-calendar text-xs" style="color:var(--muted-foreground)"></i>
                <select id="selectBulanNpt" onchange="navigateNptMonth()"
                    class="bg-transparent border-0 text-xs font-semibold focus:outline-none cursor-pointer"
                    style="color:var(--foreground)">
                    <?php foreach ($monthNames as $num => $nama): ?>
                        <option value="<?= $num ?>" style="background:var(--popover)" <?= $activeMonth == $num ? 'selected' : '' ?>><?= $nama ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Selector Tahun -->
            <div class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-md" style="background:var(--input);border:1px solid var(--border)">
                <i class="fa-solid fa-calendar-days text-xs" style="color:var(--muted-foreground)"></i>
                <select id="selectTahunNpt" onchange="navigateNptYear()"
                    class="bg-transparent border-0 text-xs font-semibold focus:outline-none cursor-pointer"
                    style="color:var(--foreground)">
                    <?php for ($y = 2024; $y <= 2028; $y++): ?>
                        <option value="<?= $y ?>" style="background:var(--popover)" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <a href="<?= base_url('export/rekap-npt-tahunan/' . $tahun) ?>"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-md text-xs font-semibold transition"
                style="background:var(--success-bg);color:var(--success-fg);border:1px solid var(--success-border)"
                onmouseover="this.style.background='var(--success)';this.style.color='#fff'"
                onmouseout="this.style.background='var(--success-bg)';this.style.color='var(--success-fg)'">
                <i class="fa-solid fa-file-excel text-xs"></i> Export Excel
            </a>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="ui-tabs overflow-x-auto custom-scrollbar">
        <button onclick="switchNptTab('summary')" id="tabBtn_summary"
            class="ui-tab active whitespace-nowrap flex items-center gap-1.5">
            <i class="fa-solid fa-table text-xs"></i>
            <span>Ringkasan NPT All Rig (<?= $monthNames[$activeMonth] ?? '' ?>)</span>
        </button>
        <button onclick="switchNptTab('chrono')" id="tabBtn_chrono"
            class="ui-tab whitespace-nowrap flex items-center gap-1.5">
            <i class="fa-solid fa-list-ol text-xs"></i>
            <span>Log Kronologis Per Rig</span>
        </button>
        <button onclick="switchNptTab('matrixDetail')" id="tabBtn_matrixDetail"
            class="ui-tab whitespace-nowrap flex items-center gap-1.5">
            <i class="fa-solid fa-table-cells text-xs"></i>
            <span>Matriks Lengkap SYS</span>
        </button>
        <button onclick="switchNptTab('grandTotal')" id="tabBtn_grandTotal"
            class="ui-tab whitespace-nowrap flex items-center gap-1.5">
            <i class="fa-solid fa-trophy text-xs"></i>
            <span>Grand Total Tahunan <?= $tahun ?></span>
        </button>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 1: RINGKASAN NPT ALL RIG (IDENTIK MONTHLY REPORT - NO SCROLLING)     -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="tabContent_summary" class="tab-content space-y-4">
        <div class="rounded-xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
            
            <!-- Banner Judul Kuning Identik Monthly Report -->
            <div class="bg-yellow-400 text-slate-950 py-2.5 px-4 text-center font-black text-sm uppercase tracking-wider border-b-2 border-yellow-500 flex items-center justify-between">
                <span class="w-16"></span>
                <span>DOWN TIME RIG BMS PERIODE <?= strtoupper($monthNames[$activeMonth] ?? '') ?> <?= $tahun ?></span>
                <span class="text-xs bg-slate-950 text-yellow-300 font-mono px-2.5 py-0.5 rounded-full font-bold">18 Unit Rig</span>
            </div>

            <!-- Clean Table Layout: Fits perfectly on screen without forced horizontal scrolling -->
            <div class="w-full">
                <table class="w-full text-left border-collapse border border-slate-700">
                    <thead class="bg-[#0B1E4A] text-white font-extrabold uppercase text-[10.5px] border-b-2 border-slate-700 tracking-wider text-center select-none">
                        <tr>
                            <th rowspan="2" class="py-2.5 px-2 w-10 border border-slate-700 bg-[#0B1E4A]">NO</th>
                            <th rowspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] min-w-[95px] text-left">NAME RIG</th>
                            <th colspan="3" class="py-1.5 px-2 border border-slate-700 bg-[#0E2A66] text-rose-300">UNPAID (HRS)</th>
                            <th colspan="5" class="py-1.5 px-2 border border-slate-700 bg-[#0E2A66] text-amber-300">STAND BY WITH CREW — SBWC (HRS)</th>
                            <th rowspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0E2A66] min-w-[90px] text-right font-black text-yellow-300">TOTAL (HRS)</th>
                            <th rowspan="2" class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A] min-w-[150px] text-left">REMARK UNPAID</th>
                        </tr>
                        <tr class="text-[9.5px] bg-[#0A1A3F] border-t border-slate-700 text-slate-300">
                            <th class="py-1.5 px-2 border border-slate-700 text-rose-300">Repair Rig</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-rose-300">Personnel</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-rose-400 font-bold bg-rose-950/30">Total Unpaid</th>
                            
                            <th class="py-1.5 px-2 border border-slate-700 text-yellow-300">SWA Rain</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-yellow-300">Dry Road/Pad</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-yellow-300">WO Daylight</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-yellow-300">3rd Party</th>
                            <th class="py-1.5 px-2 border border-slate-700 text-yellow-400 font-bold bg-amber-950/30">Total SBWC</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num">
                        <?php if ($monthData && !empty($monthData['rows'])): 
                            $grandRepair = 0; $grandPers = 0; $grandUnpaid = 0;
                            $grandRain = 0; $grandRoad = 0; $grandDay = 0; $grandTp = 0; $grandSbwc = 0;
                            $grandTotalHrs = 0;
                            
                            foreach ($monthData['rows'] as $r):
                                $vals = $r['vals'] ?? [];
                                $repairVal = (float)($vals[2] ?? 0);
                                $persVal   = (float)($vals[3] ?? 0);
                                $totUnpaid = $repairVal + $persVal;
                                
                                $rainVal   = (float)($vals[4] ?? 0);
                                $roadVal   = (float)($vals[5] ?? 0) + (float)($vals[6] ?? 0); // Road + Pad
                                $dayVal    = (float)($vals[7] ?? 0);
                                
                                // 3rd party sum (cols 12..21)
                                $tpSum = 0;
                                for ($c = 12; $c <= 21; $c++) {
                                    $tpSum += (float)($vals[$c] ?? 0);
                                }
                                
                                // Total SBWC = sum of all SBWC cols (4..31)
                                $totSbwc = 0;
                                for ($c = 4; $c <= 31; $c++) {
                                    $totSbwc += (float)($vals[$c] ?? 0);
                                }
                                
                                $totHrs = (float)($vals[32] ?? ($totUnpaid + $totSbwc));
                                $rem = $vals[33] ?? '';
                                
                                $grandRepair += $repairVal;
                                $grandPers   += $persVal;
                                $grandUnpaid += $totUnpaid;
                                $grandRain   += $rainVal;
                                $grandRoad   += $roadVal;
                                $grandDay    += $dayVal;
                                $grandTp     += $tpSum;
                                $grandSbwc   += $totSbwc;
                                $grandTotalHrs += $totHrs;
                        ?>
                        <tr class="hover:bg-slate-800/80 transition">
                            <td class="py-2 px-2 text-center text-xs text-slate-400 border border-slate-800 bg-slate-900/50"><?= $r['no'] ?></td>
                            <td class="py-2 px-3 font-bold text-white text-xs border border-slate-800 font-sans bg-slate-900/50"><?= esc($r['rig']) ?></td>
                            
                            <!-- UNPAID: Repair Rig -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $repairVal > 0 ? 'text-rose-400 font-bold' : 'text-slate-600' ?>">
                                <?= $repairVal > 0 ? number_format($repairVal, 2) : '-' ?>
                            </td>
                            <!-- UNPAID: Personnel -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $persVal > 0 ? 'text-rose-400 font-bold' : 'text-slate-600' ?>">
                                <?= $persVal > 0 ? number_format($persVal, 2) : '-' ?>
                            </td>
                            <!-- TOTAL UNPAID -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs font-black bg-rose-950/20 text-rose-300">
                                <?= $totUnpaid > 0 ? number_format($totUnpaid, 2) : '-' ?>
                            </td>

                            <!-- SBWC: Rain -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $rainVal > 0 ? 'text-amber-300 font-bold' : 'text-slate-600' ?>">
                                <?= $rainVal > 0 ? number_format($rainVal, 2) : '-' ?>
                            </td>
                            <!-- SBWC: Road & Pad -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $roadVal > 0 ? 'text-amber-300 font-bold' : 'text-slate-600' ?>">
                                <?= $roadVal > 0 ? number_format($roadVal, 2) : '-' ?>
                            </td>
                            <!-- SBWC: Daylight -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $dayVal > 0 ? 'text-amber-300 font-bold' : 'text-slate-600' ?>">
                                <?= $dayVal > 0 ? number_format($dayVal, 2) : '-' ?>
                            </td>
                            <!-- SBWC: 3rd Party -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs <?= $tpSum > 0 ? 'text-amber-300 font-bold' : 'text-slate-600' ?>">
                                <?= $tpSum > 0 ? number_format($tpSum, 2) : '-' ?>
                            </td>
                            <!-- TOTAL SBWC -->
                            <td class="py-2 px-2 text-center border border-slate-800 text-xs font-black bg-amber-950/20 text-yellow-300">
                                <?= $totSbwc > 0 ? number_format($totSbwc, 2) : '-' ?>
                            </td>

                            <!-- TOTAL DOWNTIME -->
                            <td class="py-2 px-3 text-right font-black text-sm text-yellow-300 bg-[#0E2A66]/40 border border-slate-800">
                                <?= $totHrs > 0 ? number_format($totHrs, 2) : '-' ?>
                            </td>

                            <!-- REMARK UNPAID -->
                            <td class="py-2 px-3 text-left text-xs font-sans text-slate-300 border border-slate-800 truncate max-w-[220px]" title="<?= esc($rem) ?>">
                                <?= !empty($rem) ? esc($rem) : '<span class="text-slate-600">-</span>' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <!-- TOTAL FOOTER ROW -->
                    <tfoot class="bg-[#0B1E4A] font-extrabold text-white border-t-2 border-slate-600 text-xs">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 text-center border border-slate-700 bg-[#0B1E4A] font-sans text-amber-300 uppercase">
                                TOTAL DOWNTIME
                            </td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-rose-400"><?= number_format($grandRepair, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-rose-400"><?= number_format($grandPers, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-rose-300 font-black bg-rose-950/40"><?= number_format($grandUnpaid, 2) ?></td>
                            
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-yellow-300"><?= number_format($grandRain, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-yellow-300"><?= number_format($grandRoad, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-yellow-300"><?= number_format($grandDay, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-yellow-300"><?= number_format($grandTp, 2) ?></td>
                            <td class="py-2.5 px-2 text-center border border-slate-700 text-yellow-300 font-black bg-amber-950/40"><?= number_format($grandSbwc, 2) ?></td>

                            <td class="py-2.5 px-3 text-right font-black text-sm text-yellow-300 bg-[#0E2A66] border border-slate-700">
                                <?= number_format($grandTotalHrs, 2) ?>
                            </td>
                            <td class="py-2.5 px-3 border border-slate-700 bg-[#0B1E4A]"></td>
                        </tr>
                    </tfoot>
                    <?php else: ?>
                    <tbody>
                        <tr>
                            <td colspan="12" class="py-8 text-center text-slate-500">
                                Belum ada data untuk periode <?= $monthNames[$activeMonth] ?? '' ?> <?= $tahun ?>.
                            </td>
                        </tr>
                    </tbody>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 2: LOG KRONOLOGIS DETAIL PER RIG                                   -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="tabContent_chrono" class="tab-content hidden space-y-4">
        <!-- Rig Selector Pills -->
        <div class="p-3 bg-slate-800/90 rounded-xl border border-slate-700 flex flex-wrap items-center gap-1.5">
            <span class="text-xs font-bold text-slate-400 mr-2 flex items-center gap-1">
                <i class="fa-solid fa-oil-well text-amber-400"></i>
                <span>Pilih Rig:</span>
            </span>
            <?php foreach ($rigList as $rName): 
                $isRigActive = ($selectedRig === $rName);
            ?>
                <a href="<?= base_url("rekap-tahunan/npt/{$tahun}?mode=kronologis&rig=" . urlencode($rName) . "&bulan={$activeMonth}") ?>"
                   class="px-3 py-1 rounded-lg text-xs font-bold transition <?= $isRigActive ? 'bg-amber-400 text-slate-950 shadow-md font-black' : 'bg-slate-900 text-slate-300 hover:bg-slate-700 hover:text-white border border-slate-700' ?>">
                    <?= esc($rName) ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php
            $totalChronoHrs = 0;
            foreach ($chronoEvents as $ev) {
                $totalChronoHrs += (float)($ev['hrs'] ?? 0);
            }
        ?>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Armada Rig</span>
                    <h4 class="text-xl font-black text-amber-400 mt-0.5"><?= esc($selectedRig) ?></h4>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-400 text-lg">
                    <i class="fa-solid fa-oil-well"></i>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Kejadian</span>
                    <h4 class="text-xl font-black text-white mt-0.5"><?= count($chronoEvents) ?> <span class="text-xs text-slate-400 font-normal">Event</span></h4>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-500/15 border border-blue-500/30 flex items-center justify-center text-blue-400 text-lg">
                    <i class="fa-solid fa-list-check"></i>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-800/90 border border-slate-700 flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Akumulasi Downtime</span>
                    <h4 class="text-xl font-black text-yellow-300 mt-0.5 font-num"><?= number_format($totalChronoHrs, 2) ?> <span class="text-xs text-slate-400 font-normal">Jam</span></h4>
                </div>
                <div class="w-10 h-10 rounded-xl bg-yellow-500/15 border border-yellow-500/30 flex items-center justify-center text-yellow-400 text-lg">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
        </div>

        <!-- Search Box -->
        <div class="p-3 bg-slate-800/90 rounded-xl border border-slate-700 flex items-center gap-3">
            <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm pl-2"></i>
            <input type="text" id="chronoSearchInput" onkeyup="filterChronoTable()" placeholder="Cari berdasarkan tanggal, lokasi sumur, atau uraian kejadian / remark..." class="bg-transparent border-0 text-xs font-semibold text-white focus:outline-none w-full placeholder-slate-500">
            <span id="chronoMatchCount" class="text-[11px] font-bold text-slate-400 whitespace-nowrap pr-2"><?= count($chronoEvents) ?> baris</span>
        </div>

        <!-- Chronological Table (Identik Monthly Report theme) -->
        <div class="rounded-xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
            <div class="p-3 bg-[#0B1E4A] border-b border-slate-700 flex items-center justify-between">
                <h4 class="text-xs font-extrabold uppercase tracking-wider text-white flex items-center gap-2">
                    <i class="fa-solid fa-table-list text-yellow-400"></i>
                    <span>Daftar Kronologis Kejadian Downtime <?= esc($selectedRig) ?> (Tahun <?= $tahun ?>)</span>
                </h4>
                <span class="text-[11px] text-slate-300 font-medium">100% Sinkron dengan Database &amp; Daily Report</span>
            </div>

            <div class="overflow-x-auto max-h-[70vh] custom-scrollbar">
                <table class="w-full text-left text-xs border-collapse border border-slate-700" id="chronoTable">
                    <thead class="bg-[#0A1A3F] text-white font-extrabold uppercase text-[10px] border-b border-slate-700 sticky top-0 z-10 tracking-wider text-center">
                        <tr>
                            <th class="py-2.5 px-3 w-12 border border-slate-700 bg-[#0A1A3F]">No</th>
                            <th class="py-2.5 px-3 w-28 border border-slate-700 bg-[#0A1A3F]">Tanggal</th>
                            <th class="py-2.5 px-3 w-28 border border-slate-700 bg-[#0A1A3F]">Rig</th>
                            <th class="py-2.5 px-4 w-44 border border-slate-700 bg-[#0A1A3F] text-left">Lokasi / Well</th>
                            <th class="py-2.5 px-4 min-w-[320px] border border-slate-700 bg-[#0A1A3F] text-left">Remark / Uraian Detail Kejadian NPT</th>
                            <th class="py-2.5 px-3 w-28 text-right font-num border border-slate-700 bg-[#0E2A66] text-yellow-300">Downtime (Jam)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200" id="chronoTableBody">
                        <?php if (!empty($chronoEvents)): ?>
                            <?php foreach ($chronoEvents as $idx => $ev): 
                                $rem = $ev['remark'] ?? '';
                                $remUnpaid = $ev['remark_unpaid'] ?? '';
                                $isSwa = stripos($rem, 'SWA') !== false;
                                $isShutdown = stripos($rem, 'shutdown') !== false || stripos($rem, 'standby for step') !== false;
                                $isRepair = stripos($rem, 'repair') !== false;
                            ?>
                            <tr class="hover:bg-slate-800/80 transition-colors chrono-row">
                                <td class="py-2.5 px-3 text-center font-bold text-slate-400 border border-slate-800 font-mono bg-slate-900/50"><?= $ev['no'] ?></td>
                                <td class="py-2.5 px-3 text-center font-semibold text-slate-300 border border-slate-800 font-num whitespace-nowrap"><?= esc($ev['date']) ?></td>
                                <td class="py-2.5 px-3 font-bold text-yellow-400 border border-slate-800 whitespace-nowrap"><?= esc($ev['rig']) ?></td>
                                <td class="py-2.5 px-4 font-bold text-white border border-slate-800"><?= esc($ev['location']) ?></td>
                                <td class="py-2.5 px-4 border border-slate-800 leading-relaxed font-sans">
                                    <span class="<?= $isShutdown ? 'text-rose-300' : ($isSwa ? 'text-amber-300' : ($isRepair ? 'text-cyan-300' : 'text-slate-200')) ?>">
                                        <?= esc($rem) ?>
                                    </span>
                                    <?php if (!empty($remUnpaid)): ?>
                                        <span class="block mt-0.5 text-[10px] text-rose-400 font-semibold italic">
                                            [Unpaid: <?= esc($remUnpaid) ?>]
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-2.5 px-3 text-right font-black font-num text-sm text-yellow-300 bg-[#0E2A66]/30 border border-slate-800">
                                    <?= is_numeric($ev['hrs']) ? number_format((float)$ev['hrs'], 2) : esc($ev['hrs']) ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-500">
                                    Tidak ada data kejadian downtime untuk armada <?= esc($selectedRig) ?>.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="bg-[#0B1E4A] font-extrabold text-white border-t-2 border-slate-600 sticky bottom-0">
                        <tr>
                            <td colspan="5" class="py-3 px-4 text-right uppercase tracking-wider text-xs border border-slate-700">Total Downtime <?= esc($selectedRig) ?>:</td>
                            <td class="py-3 px-3 text-right font-black font-num text-base text-yellow-300 bg-[#0E2A66] border border-slate-700">
                                <?= number_format($totalChronoHrs, 2) ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 3: MATRIKS LENGKAP SYS (30+ KOLOM DENGAN SEMUA 3RD PARTY)           -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="tabContent_matrixDetail" class="tab-content hidden space-y-4">
        <div class="rounded-xl bg-slate-900 border border-slate-700 shadow-xl overflow-hidden">
            <div class="bg-[#0B1E4A] text-white p-3 border-b border-slate-700 flex items-center justify-between">
                <span class="text-xs font-bold text-yellow-300 uppercase tracking-wider">Matriks Rinci Semua Pos Downtime &amp; 10 Vendor 3rd Party</span>
                <span class="text-xs text-slate-300">Format Lembar Kerja Excel Asli</span>
            </div>

            <div class="overflow-x-auto custom-scrollbar max-h-[72vh]">
                <table class="w-full text-center border-collapse text-xs border border-slate-700 select-text">
                    <thead class="sticky top-0 z-20 font-bold text-white shadow-sm bg-[#002060]">
                        <tr class="border-b border-blue-900">
                            <th rowspan="3" class="py-2.5 px-2 border border-blue-900 bg-[#002060] w-10 text-center text-white sticky left-0 z-30">NO</th>
                            <th rowspan="3" class="py-2.5 px-3 border border-blue-900 bg-[#002060] min-w-[110px] text-left text-white sticky left-10 z-30">NAME RIG</th>
                            <th colspan="2" class="py-1.5 px-2 border border-blue-900 bg-[#002060] text-center text-rose-300">UNPAID</th>
                            <th colspan="28" class="py-1.5 px-2 border border-blue-900 bg-[#002060] text-center text-yellow-300">STAND BY WITH CREW ( SBWC )</th>
                            <th rowspan="3" class="py-2.5 px-3 border border-blue-900 bg-[#002060] min-w-[80px] text-right text-yellow-300 font-bold">TOTAL (HRS)</th>
                            <th rowspan="3" class="py-2.5 px-4 border border-blue-900 bg-[#002060] min-w-[200px] text-left text-white">REMARK UNPAID</th>
                        </tr>
                        <tr class="bg-[#002060] border-b border-blue-900 text-[10px]">
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 bg-rose-950/30 text-rose-300 font-bold">Repaire Rig</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 bg-rose-950/30 text-rose-300 font-bold">PERSONEL</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">SWA Rain</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">Dry Road</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">Dry Pad</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">Daylight</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][8]['h3'] ?? 'PT. CHAST') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][9]['h3'] ?? 'WO PDC') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">PHR Well</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">ESP</th>
                            <th colspan="10" class="py-1.5 px-1.5 border-x-2 border-y border-indigo-400 text-center bg-indigo-950 text-amber-300 font-black tracking-wider shadow-inner">
                                <div class="flex items-center justify-center gap-1.5">
                                    <i class="fa-solid fa-handshake text-xs"></i>
                                    <span>3RD PARTY (10 VENDOR)</span>
                                </div>
                            </th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">UNISAT</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">TRANS</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300">Foam Unit</th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][25]['h3'] ?? 'WO Decision') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][26]['h3'] ?? 'PEMILU') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][27]['h3'] ?? 'WO OMS') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][28]['h3'] ?? 'PT. PCM') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][29]['h3'] ?? 'WO COSL') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][30]['h3'] ?? 'SAFARI') ?></th>
                            <th rowspan="2" class="py-2 px-1.5 border border-blue-900 text-yellow-300"><?= esc($monthData['cols'][31]['h3'] ?? 'IDUL FITRI') ?></th>
                        </tr>
                        <tr class="bg-indigo-950/90 border-b border-indigo-700/60 text-[9px] text-amber-200 font-bold">
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">BHI</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">HLS</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">WI</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">HALCO</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">EJP</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">SCHL</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">MGA</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">SGN</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">BUKAKA</th>
                            <th class="py-1 px-1 border border-indigo-700/60 hover:bg-indigo-900 transition">PESI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800 text-slate-200 font-num">
                        <?php if ($monthData && !empty($monthData['rows'])): 
                            foreach ($monthData['rows'] as $r):
                                $vals = $r['vals'] ?? [];
                        ?>
                        <tr class="hover:bg-slate-800/80 transition group">
                            <td class="py-1.5 px-1.5 border border-slate-800 bg-slate-900/90 text-slate-400 font-mono text-center sticky left-0 z-10 group-hover:bg-slate-800"><?= $r['no'] ?></td>
                            <td class="py-1.5 px-3 border border-slate-800 bg-slate-900/90 font-bold text-white text-left whitespace-nowrap font-sans sticky left-10 z-10 group-hover:bg-slate-800"><?= esc($r['rig']) ?></td>
                            <?php for ($c = 2; $c <= 31; $c++): 
                                $val = $vals[$c] ?? 0;
                                $hasVal = ($val > 0);
                                $isUnpaid = ($c == 2 || $c == 3);
                                $isTp = ($c >= 12 && $c <= 21);
                            ?>
                                <td class="py-1.5 px-1 border border-slate-800 text-center font-mono text-xs <?= $hasVal ? ($isUnpaid ? 'text-rose-400 font-bold bg-rose-950/25' : ($isTp ? 'text-amber-300 font-bold bg-indigo-950/30' : 'text-yellow-300 font-bold bg-amber-950/20')) : ($isTp ? 'text-slate-600 bg-indigo-950/10' : 'text-slate-600') ?>">
                                    <?= $hasVal ? (fmod($val, 1) !== 0.0 ? number_format($val, 2) : (int)$val) : '-' ?>
                                </td>
                            <?php endfor; ?>
                            <td class="py-1.5 px-2 border border-slate-800 font-bold font-mono text-right bg-[#0E2A66]/40 text-yellow-300">
                                <?= number_format((float)($vals[32] ?? 0), 2) ?>
                            </td>
                            <td class="py-1.5 px-3 border border-slate-800 text-left text-xs text-slate-300 truncate max-w-[200px]" title="<?= esc($vals[33] ?? '') ?>">
                                <?= esc($vals[33] ?? '-') ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="34" class="py-8 text-center text-slate-500">
                                Belum ada data matriks rinci SYS untuk periode <?= $bulanList[$bulan] ?? '' ?> <?= $tahun ?>.
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                    <?php if ($monthData && !empty($monthData['totals'])): 
                        $totals = $monthData['totals'];
                    ?>
                    <tfoot class="bg-[#0B1E4A] font-extrabold text-white border-t-2 border-slate-600 sticky bottom-0 z-20 shadow-lg">
                        <tr>
                            <td colspan="2" class="py-2.5 px-3 text-center border border-slate-700 uppercase tracking-wider text-xs sticky left-0 bg-[#0B1E4A] z-30 font-black text-yellow-300">
                                TOTAL
                            </td>
                            <?php for ($c = 2; $c <= 31; $c++): 
                                $totVal = $totals[$c] ?? 0;
                                $hasTot = ($totVal > 0);
                                $isUnp = ($c == 2 || $c == 3);
                                $isTp = ($c >= 12 && $c <= 21);
                            ?>
                                <td class="py-2 px-1 border border-slate-700 text-center font-mono text-xs <?= $isUnp ? 'text-rose-300' : ($isTp ? 'text-amber-300 font-black bg-indigo-950/40' : 'text-yellow-300') ?> font-bold">
                                    <?= $hasTot ? (fmod($totVal, 1) !== 0.0 ? number_format($totVal, 2) : (int)$totVal) : '-' ?>
                                </td>
                            <?php endfor; ?>
                            <td class="py-2 px-2 border border-slate-700 font-black font-mono text-right text-yellow-300 bg-[#0E2A66]">
                                <?= number_format((float)($totals[32] ?? 0), 2) ?>
                            </td>
                            <td class="py-2 px-3 border border-slate-700 text-left text-xs text-slate-400">
                                -
                            </td>
                        </tr>
                    </tfoot>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>

    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <!-- TAB 4: GRAND TOTAL TAHUNAN                                             -->
    <!-- ════════════════════════════════════════════════════════════════════════ -->
    <div id="tabContent_grandTotal" class="tab-content hidden space-y-4">
        <div class="p-5 rounded-2xl bg-slate-900 border border-slate-700 shadow-xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">
                        <i class="fa-solid fa-trophy text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-base font-bold text-white uppercase">GRAND TOTAL DOWNTIME TAHUN <?= $tahun ?> (SEMUA ARMADA)</h4>
                        <p class="text-sm text-slate-400">Akumulasi 8 Periode Pelaporan (Januari s/d Agustus <?= $tahun ?>)</p>
                    </div>
                </div>
                <span class="px-3 py-1.5 rounded-full text-xs font-mono font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/20">
                    Akumulasi Lengkap
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-slate-950 border border-rose-500/30">
                    <span class="text-xs font-bold uppercase text-rose-400 block mb-1">Grand Total Unpaid (Repair Rig)</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black font-mono text-rose-400"><?= number_format((float)($nptData['grand_total'][2] ?? 0), 2) ?></span>
                        <span class="text-sm text-slate-400">Jam</span>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-950 border border-amber-500/30">
                    <span class="text-xs font-bold uppercase text-amber-400 block mb-1">Grand Total Unpaid (Personel)</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black font-mono text-amber-400"><?= number_format((float)($nptData['grand_total'][3] ?? 0), 2) ?></span>
                        <span class="text-sm text-slate-400">Jam</span>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-950 border border-blue-500/30">
                    <span class="text-xs font-bold uppercase text-blue-400 block mb-1">Grand Total SBWC (Standby)</span>
                    <?php 
                        $sbwcTot = 0;
                        for ($c = 4; $c <= 31; $c++) {
                            $sbwcTot += (float)($nptData['grand_total'][$c] ?? 0);
                        }
                    ?>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black font-mono text-blue-400"><?= number_format($sbwcTot, 2) ?></span>
                        <span class="text-sm text-slate-400">Jam</span>
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-950 border border-yellow-500/30">
                    <span class="text-xs font-bold uppercase text-yellow-300 block mb-1">Grand Total NPT All Rig</span>
                    <div class="flex items-baseline gap-1">
                        <span class="text-2xl font-black font-mono text-yellow-300"><?= number_format((float)($nptData['grand_total'][32] ?? 0), 2) ?></span>
                        <span class="text-sm text-slate-400">Jam</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function navigateNptMonth() {
        const m = document.getElementById('selectBulanNpt').value;
        const y = document.getElementById('selectTahunNpt').value;
        window.location.href = `<?= base_url('rekap-tahunan/npt') ?>/${y}?bulan=${m}`;
    }

    function navigateNptYear() {
        const m = document.getElementById('selectBulanNpt').value;
        const y = document.getElementById('selectTahunNpt').value;
        window.location.href = `<?= base_url('rekap-tahunan/npt') ?>/${y}?bulan=${m}`;
    }

    function switchNptTab(tabId) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.ui-tab, .tab-btn').forEach(btn => {
            btn.classList.remove('active', 'border-yellow-400', 'text-yellow-300', 'bg-slate-800');
        });

        const target = document.getElementById(`tabContent_${tabId}`);
        const targetBtn = document.getElementById(`tabBtn_${tabId}`);
        if (target) target.classList.remove('hidden');
        if (targetBtn) targetBtn.classList.add('active');
    }

    function filterChronoTable() {
        const input = document.getElementById('chronoSearchInput');
        const filter = input.value.toLowerCase().trim();
        const rows = document.querySelectorAll('#chronoTableBody .chrono-row');
        let matchCount = 0;

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            if (filter === '' || text.includes(filter)) {
                row.style.display = '';
                matchCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const badge = document.getElementById('chronoMatchCount');
        if (badge) {
            badge.textContent = `${matchCount} baris`;
        }
    }

    // Handle query param ?mode=kronologis
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const mode = urlParams.get('mode');
        if (mode === 'kronologis') {
            switchNptTab('chrono');
        } else if (mode === 'matrixDetail') {
            switchNptTab('matrixDetail');
        } else if (mode === 'grandTotal') {
            switchNptTab('grandTotal');
        }
    });
</script>
<?= $this->endSection() ?>
