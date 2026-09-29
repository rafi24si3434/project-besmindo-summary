<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\ThirdPartyModel;

class ThirdParty extends BaseController
{
    protected $thirdPartyModel;

    public function __construct()
    {
        $this->thirdPartyModel = new ThirdPartyModel();
    }

    public function index()
    {
        return redirect()->to(base_url('master?tab=third_party'));
    }

    public function tambah()
    {
        $data = [
            'title'         => 'Tambah Vendor 3rd Party',
            'page_title'    => 'Registrasi Vendor Pihak Ketiga',
            'page_subtitle' => 'Masukkan nama vendor mitra',
        ];
        return view('master/third_party/form', $data);
    }

    public function simpan()
    {
        $rules = ['nama' => 'required|min_length[2]|max_length[100]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Nama vendor harus diisi.');
        }

        $this->thirdPartyModel->insert([
            'nama'  => $this->request->getPost('nama'),
            'aktif' => $this->request->getPost('aktif') ? 1 : 0,
        ]);

        return redirect()->to(base_url('master?tab=third_party'))->with('success', 'Vendor berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $tp = $this->thirdPartyModel->find($id);
        if (!$tp) {
            return redirect()->to(base_url('master?tab=third_party'))->with('error', 'Vendor tidak ditemukan.');
        }

        $data = [
            'title'         => 'Edit Vendor',
            'page_title'    => 'Edit Data Vendor',
            'page_subtitle' => 'Perbarui nama atau status keaktifan vendor',
            'vendor'        => $tp,
        ];
        return view('master/third_party/form', $data);
    }

    public function update($id)
    {
        $rules = ['nama' => 'required|min_length[2]|max_length[100]'];
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Validasi gagal.');
        }

        $this->thirdPartyModel->update($id, [
            'nama'  => $this->request->getPost('nama'),
            'aktif' => $this->request->getPost('aktif') ? 1 : 0,
        ]);

        return redirect()->to(base_url('master?tab=third_party'))->with('success', 'Data vendor berhasil diupdate.');
    }

    public function hapus($id)
    {
        $this->thirdPartyModel->delete($id);
        return redirect()->to(base_url('master?tab=third_party'))->with('success', 'Vendor berhasil dihapus.');
    }
}
