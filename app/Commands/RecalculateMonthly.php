<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\RigModel;
use App\Models\MonthlySummaryModel;

class RecalculateMonthly extends BaseCommand
{
    protected $group       = 'SIMOR';
    protected $name        = 'simor:recalc';
    protected $description = 'Hitung ulang seluruh KPI bulanan SIMOR sesuai formula baku Excel';
    protected $usage       = 'simor:recalc [bulan] [tahun]';
    protected $arguments   = [
        'bulan' => 'Bulan yang akan dihitung (1-12)',
        'tahun' => 'Tahun yang akan dihitung (contoh: 2026)',
    ];

    public function run(array $params)
    {
        $bulan = (int)($params[0] ?? 9);
        $tahun = (int)($params[1] ?? 2026);

        CLI::write("=== REKALKULASI KPI SIMOR (Periode {$bulan}/{$tahun}) ===", 'yellow');

        $rigModel = new RigModel();
        $summaryModel = new MonthlySummaryModel();

        $rigs = $rigModel->getRigAktif();

        foreach ($rigs as $r) {
            $rigId = (int)$r['id'];
            $kode  = $r['kode'];

            $res = $summaryModel->hitungDanSimpan($rigId, $bulan, $tahun);

            $line = sprintf(
                "[%-8s] Rel: %6.2f%% | Avail: %6.2f%% | Util: %6.2f%% | Target: Rp %14s | Actual: Rp %14s",
                $kode,
                ($res['reliability'] ?? 0) * 100,
                ($res['availability'] ?? 0) * 100,
                ($res['utilization'] ?? 0) * 100,
                number_format((float)($res['revenue_target'] ?? 0), 0, ',', '.'),
                number_format((float)($res['revenue_actual'] ?? 0), 0, ',', '.')
            );
            CLI::write($line, 'green');
        }

        CLI::write("\nRekalkulasi selesai untuk seluruh " . count($rigs) . " rig!\n", 'cyan');
    }
}
