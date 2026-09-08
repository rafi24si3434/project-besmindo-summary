<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\LokasiModel;

class Lokasi extends BaseController
{
    protected $lokasiModel;

    public function __construct()
    {
        $this->lokasiModel = new LokasiModel();
    }

    public function index()
    {
        $data = [
            'title'         => 'Master Lokasi / Sumur',
            'page_title'    => 'Daftar Lokasi Sumur Minyak',
            'page_subtitle' => 'Pencatatan titik lokasi pekerjaan sumur pengeboran & workover',
            'lokasi'        => $this->lokasiModel->orderBy('nama_lokasi', 'ASC')->findAll(),
        ];
        return view('master/lokasi/index', $data);
    }

    public function tambah()
    {
        $data = [
            'title'         => 'Tambah Lokasi Sumur',
            'page_title'    => 'Registrasi Titik Lokasi Sumur Baru',
            'page_subtitle' => 'Masukkan nama tag atau identitas lokasi sumur',
        ];
        return view('master/lokasi/form', $data);
    }

    public function simpan()
    {
        $rules = ['nama_lokasi' => 'required|min_length[2]|max_length[100]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama lokasi sumur harus diisi.');
        }

        $this->lokasiModel->insert([
            'nama_lokasi' => $this->request->getPost('nama_lokasi'),
            'aktif'       => $this->request->getPost('aktif') ? 1 : 0,
        ]);

        return redirect()->to(base_url('master/lokasi'))->with('success', 'Lokasi sumur berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $lokasi = $this->lokasiModel->find($id);
        if (!$lokasi) {
            return redirect()->to(base_url('master/lokasi'))->with('error', 'Lokasi tidak ditemukan.');
        }

        $data = [
            'title'         => 'Edit Lokasi',
            'page_title'    => 'Edit Data Lokasi Sumur',
            'page_subtitle' => 'Perbarui penamaan lokasi sumur',
            'lokasi'        => $lokasi,
        ];
        return view('master/lokasi/form', $data);
    }

    public function update($id)
    {
        $rules = ['nama_lokasi' => 'required|min_length[2]|max_length[100]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal.');
        }

        $this->lokasiModel->update($id, [
            'nama_lokasi' => $this->request->getPost('nama_lokasi'),
            'aktif'       => $this->request->getPost('aktif') ? 1 : 0,
        ]);

        return redirect()->to(base_url('master/lokasi'))->with('success', 'Lokasi sumur berhasil diperbarui.');
    }

    public function hapus($id)
    {
        $this->lokasiModel->delete($id);
        return redirect()->to(base_url('master/lokasi'))->with('success', 'Lokasi berhasil dihapus.');
    }
}
