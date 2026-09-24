
with open(r'c:\xampp\htdocs\Project Besmindo Summary\app\Controllers\DailyReport.php', 'r', encoding='utf-8') as f:
    text = f.read()

import re

# Remove the broken method
text = re.sub(r'public function simpanSumurCepat\(\).*?\}\s*$', '', text, flags=re.DOTALL)

new_method = '''
    public function simpanSumurCepat()
    {
         = (int)->request->getPost('rig_id');
         = (int)->request->getPost('bulan');
         = (int)->request->getPost('tahun');
         = (int)->request->getPost('no_well');
         = (int)->request->getPost('lokasi_id');

        if (! || ! || ! || ! || !) {
            return ->response->setJSON(['status' => 'error', 'message' => 'Data tidak lengkap']);
        }

         = new \\App\\Models\\LokasiModel();
         = ->find();

         = \\Config\\Database::connect();
        
         = [
            'rig_id' => ,
            'lokasi_id' => ,
            'no_well' => ,
            'bulan' => ,
            'tahun' => ,
            'status_job' => 'Moving',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        try {
            ->table('daily_report')->insert();
             = ->insertID();
            
            return ->response->setJSON([
                'status' => 'success',
                'id' => ,
                'no_well' => ,
                'nama_lokasi' => ['nama_lokasi'] ?? 'N/A'
            ]);
        } catch (\\Exception ) {
            return ->response->setJSON(['status' => 'error', 'message' => ->getMessage()]);
        }
    }
}
'''

text = text.rstrip() + new_method

with open(r'c:\xampp\htdocs\Project Besmindo Summary\app\Controllers\DailyReport.php', 'w', encoding='utf-8') as f:
    f.write(text)
print('Fixed DailyReport')

