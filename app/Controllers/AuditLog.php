<?php

namespace App\Controllers;

use App\Models\ActivityLogModel;
use App\Models\RigModel;

class AuditLog extends BaseController
{
    protected $activityLogModel;
    protected $rigModel;

    public function __construct()
    {
        $this->activityLogModel = new ActivityLogModel();
        $this->rigModel = new RigModel();
    }

    public function index()
    {
        $modul = $this->request->getGet('modul') ?: 'ALL';
        $rigId = $this->request->getGet('rig_id') ? (int)$this->request->getGet('rig_id') : null;

        $logs = $this->activityLogModel->getLogs(200, $modul, $rigId);
        $allRigs = $this->rigModel->getRigAktif();

        $data = [
            'title'         => 'Riwayat Aktivitas Sistem (Audit Trail)',
            'page_title'    => 'Riwayat Aktivitas & Jejak Audit',
            'page_subtitle' => 'Transparansi pencatatan perubahan data operasional rig, sumur, dan NPT',
            'logs'          => $logs,
            'allRigs'       => $allRigs,
            'selectedModul' => $modul,
            'selectedRigId' => $rigId,
            'totalLogs'     => count($logs),
        ];

        return view('audit_log/index', $data);
    }
}
