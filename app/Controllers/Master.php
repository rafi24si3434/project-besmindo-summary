<?php

namespace App\Controllers;

use App\Models\RigModel;
use App\Models\KategoriDowntimeModel;
use App\Models\ThirdPartyModel;
use App\Models\LokasiModel;

class Master extends BaseController
{
    public function index()
    {
        $rigModel = new RigModel();
        $kategoriModel = new KategoriDowntimeModel();
        $tpModel = new ThirdPartyModel();
        $lokasiModel = new LokasiModel();

        // Ambil tab aktif dari URL, default ke "rig"
        $activeTab = $this->request->getGet("tab") ?? "rig";
        
        $allowedTabs = ["rig", "kategori", "third_party", "lokasi"];
        if (!in_array($activeTab, $allowedTabs)) {
            $activeTab = "rig";
        }

        $data = [
            "title"         => "Pusat Master Data Terpadu",
            "page_title"    => "Pusat Master Data Terpadu",
            "page_subtitle" => "Kelola seluruh data referensi pilar operasional: Armada Rig, Kategori NPT, Vendor Pihak ke-3, dan Lapangan Sumur.",
            "activeTab"     => $activeTab,
            "rigs"          => $rigModel->orderBy("id", "ASC")->findAll(),
            "kategori"      => $kategoriModel->getAllOrdered(),
            "thirdParties"  => $tpModel->orderBy("nama", "ASC")->findAll(),
            "lokasi"        => $lokasiModel->orderBy("nama_lokasi", "ASC")->findAll(),
        ];

        return view("master/index", $data);
    }
}

