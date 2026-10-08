<?php

namespace App\Controllers;

class ErrorDemo extends BaseController
{
    /**
     * Halaman Showcase / Playground Interaktif Notifikasi & Error Pages
     */
    public function index()
    {
        $data = [
            'title'         => 'Pusat Desain Notifikasi & Error — SIMOR BMS',
            'page_title'    => 'Pusat Desain Telemetri: Notifikasi & Error Screens',
            'page_subtitle' => 'Eksplorasi dan pengujian interaktif untuk toast alerts, modal konfirmasi, dan tampilan status HTTP',
        ];

        return view('errors/demo', $data);
    }

    /**
     * Preview 404
     */
    public function show404()
    {
        return view('errors/html/error_404', [
            'message' => 'Halaman atau modul yang Anda minta tidak terdaftar pada radar SIMOR.'
        ]);
    }

    /**
     * Preview 500
     */
    public function show500()
    {
        return view('errors/html/production');
    }

    /**
     * Preview 403
     */
    public function show403()
    {
        return view('errors/html/error_403');
    }

    /**
     * Preview 400
     */
    public function show400()
    {
        return view('errors/html/error_400', [
            'message' => 'Parameter permintaan telemetri yang dikirimkan tidak valid atau korup.'
        ]);
    }
}
