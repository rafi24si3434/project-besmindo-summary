<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\KategoriDowntimeModel;

class KategoriDowntime extends BaseController
{
    protected $kategoriModel;

    public function __construct()
    {
        $this->kategoriModel = new KategoriDowntimeModel();
    }

    public function index()
    {
        $data = [
            'title'         => 'Master Kategori Downtime',
            'page_title'    => 'Kategori Downtime (UNPAID & SBWC)',
            'page_subtitle' => 'Daftar klasifikasi downtime operasi rig sesuai standar kontrak',
            'kategori'      => $this->kategoriModel->getAllOrdered(),
        ];
        return view('master/kategori/index', $data);
    }

    public function tambah()
    {
        $data = [
            'title'         => 'Tambah Kategori Downtime',
            'page_title'    => 'Tambah Kategori Downtime Baru',
            'page_subtitle' => 'Tentukan nama kategori, tipe klasifikasi, dan urutan kolom laporan',
        ];
        return view('master/kategori/form', $data);
    }

    public function simpan()
    {
        $rules = [
            'nama'   => 'required|min_length[3]|max_length[100]',
            'tipe'   => 'required|in_list[UNPAID,SBWC]',
            'urutan' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal. Mohon lengkapi formulir.');
        }

        $this->kategoriModel->insert([
            'nama'   => $this->request->getPost('nama'),
            'tipe'   => $this->request->getPost('tipe'),
            'urutan' => (int)$this->request->getPost('urutan'),
            'aktif'  => $this->request->getPost('aktif') ? 1 : 0,
        ]);

        return redirect()->to(base_url('master/kategori'))->with('success', 'Kategori downtime berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $kategori = $this->kategoriModel->find($id);
        if (!$kategori) {
            return redirect()->to(base_url('master/kategori'))->with('error', 'Kategori tidak ditemukan.');
        }

        $data = [
            'title'         => 'Edit Kategori Downtime',
            'page_title'    => 'Edit Kategori Downtime',
            'page_subtitle' => 'Perbarui parameter kategori',
            'kategori'      => $kategori,
        ];
        return view('master/kategori/form', $data);
    }

    public function update($id)
    {
        $rules = [
            'nama'   => 'required|min_length[3]|max_length[100]',
            'tipe'   => 'required|in_list[UNPAID,SBWC]',
            'urutan' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal.');
        }

        $this->kategoriModel->update($id, [
            'nama'   => $this->request->getPost('nama'),
            'tipe'   => $this->request->getPost('tipe'),
            'urutan' => (int)$this->request->getPost('urutan'),
            'aktif'  => $this->request->getPost('aktif') ? 1 : 0,
        ]);

        return redirect()->to(base_url('master/kategori'))->with('success', 'Kategori downtime berhasil diupdate.');
    }

    public function hapus($id)
    {
        $this->kategoriModel->delete($id);
        return redirect()->to(base_url('master/kategori'))->with('success', 'Kategori berhasil dihapus.');
    }
}
