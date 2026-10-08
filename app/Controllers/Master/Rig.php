<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\RigModel;

class Rig extends BaseController
{
    protected $rigModel;

    public function __construct()
    {
        $this->rigModel = new RigModel();
    }

    public function index()
    {
        return redirect()->to(base_url('master?tab=rig'));
    }

    public function tambah()
    {
        $data = [
            'title'         => 'Tambah Rig Baru',
            'page_title'    => 'Registrasi Armada Rig Baru',
            'page_subtitle' => 'Masukkan kode rig, spesifikasi nama, dan Operator Daily Rate (ODR)',
        ];
        return view('master/rig/form', $data);
    }

    public function simpan()
    {
        $rawOdr = (string)$this->request->getPost('odr');
        $cleanOdr = (int)preg_replace('/[^\d]/', '', $rawOdr);

        $rules = [
            'kode'     => 'required|min_length[3]|max_length[20]',
            'nama_rig' => 'required|min_length[3]|max_length[50]',
        ];

        if (!$this->validate($rules) || $cleanOdr < 0) {
            return redirect()->back()->withInput()->with('error', 'Validasi data gagal. Pastikan semua field terisi benar.');
        }

        $this->rigModel->insert([
            'kode'       => $this->request->getPost('kode'),
            'nama_rig'   => $this->request->getPost('nama_rig'),
            'odr'        => $cleanOdr,
            'aktif'      => $this->request->getPost('aktif') ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('master?tab=rig'))->with('success', 'Armada rig berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $rig = $this->rigModel->find($id);
        if (!$rig) {
            return redirect()->to(base_url('master?tab=rig'))->with('error', 'Rig tidak ditemukan.');
        }

        $data = [
            'title'         => 'Edit Rig ' . $rig['kode'],
            'page_title'    => 'Edit Data Armada Rig',
            'page_subtitle' => 'Perbarui informasi dan nilai kontrak ODR armada rig',
            'rig'           => $rig,
        ];
        return view('master/rig/form', $data);
    }

    public function update($id)
    {
        $rawOdr = (string)$this->request->getPost('odr');
        $cleanOdr = (int)preg_replace('/[^\d]/', '', $rawOdr);

        $rules = [
            'kode'     => 'required|min_length[3]|max_length[20]',
            'nama_rig' => 'required|min_length[3]|max_length[50]',
        ];

        if (!$this->validate($rules) || $cleanOdr < 0) {
            return redirect()->back()->withInput()->with('error', 'Validasi data gagal.');
        }

        $this->rigModel->update($id, [
            'kode'     => $this->request->getPost('kode'),
            'nama_rig' => $this->request->getPost('nama_rig'),
            'odr'      => $cleanOdr,
            'aktif'    => $this->request->getPost('aktif') ? 1 : 0,
        ]);

        // Rekalkulasi Monthly Summary seketika agar perubahan ODR langsung berdampak ke target & realisasi
        $summaryModel = new \App\Models\MonthlySummaryModel();
        $summaryModel->hitungDanSimpan((int)$id, 9, 2026);
        $summaryModel->hitungDanSimpan((int)$id, (int)date('n'), (int)date('Y'));

        return redirect()->to(base_url('master?tab=rig'))->with('success', 'Data armada rig dan tarif ODR berhasil diperbarui.');
    }

    public function hapus($id)
    {
        $this->rigModel->delete($id);
        return redirect()->to(base_url('master?tab=rig'))->with('success', 'Rig berhasil dihapus.');
    }
}
