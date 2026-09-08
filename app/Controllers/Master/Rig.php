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
        $data = [
            'title'         => 'Master Armada Rig',
            'page_title'    => 'Daftar Armada Rig & Tarif Harian (ODR)',
            'page_subtitle' => 'Pengaturan kode rig, nama unit operasi, status keaktifan, dan target rate harian',
            'rigs'          => $this->rigModel->orderBy('id', 'ASC')->findAll(),
        ];
        return view('master/rig/index', $data);
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
        $rules = [
            'kode'     => 'required|min_length[3]|max_length[20]',
            'nama_rig' => 'required|min_length[3]|max_length[50]',
            'odr'      => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi data gagal. Pastikan semua field terisi benar.');
        }

        $this->rigModel->insert([
            'kode'       => $this->request->getPost('kode'),
            'nama_rig'   => $this->request->getPost('nama_rig'),
            'odr'        => (int)$this->request->getPost('odr'),
            'aktif'      => $this->request->getPost('aktif') ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('master/rig'))->with('success', 'Armada rig berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $rig = $this->rigModel->find($id);
        if (!$rig) {
            return redirect()->to(base_url('master/rig'))->with('error', 'Rig tidak ditemukan.');
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
        $rules = [
            'kode'     => 'required|min_length[3]|max_length[20]',
            'nama_rig' => 'required|min_length[3]|max_length[50]',
            'odr'      => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi data gagal.');
        }

        $this->rigModel->update($id, [
            'kode'     => $this->request->getPost('kode'),
            'nama_rig' => $this->request->getPost('nama_rig'),
            'odr'      => (int)$this->request->getPost('odr'),
            'aktif'    => $this->request->getPost('aktif') ? 1 : 0,
        ]);

        return redirect()->to(base_url('master/rig'))->with('success', 'Data armada rig berhasil diperbarui.');
    }

    public function hapus($id)
    {
        $this->rigModel->delete($id);
        return redirect()->to(base_url('master/rig'))->with('success', 'Rig berhasil dihapus.');
    }
}
