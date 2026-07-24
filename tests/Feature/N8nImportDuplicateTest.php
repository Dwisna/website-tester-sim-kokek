<?php

use App\Models\RupRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class N8nImportDuplicateTest extends \Tests\TestCase
{
    use RefreshDatabase;

    public function test_import_skips_exact_duplicates_and_updates_changed()
    {
        // initial record with id_rup
        $existing = RupRecord::create([
            'id_rup' => 'RUP-001',
            'nama_pekerjaan' => 'Project Alpha',
            'nama_instansi' => 'Instansi A',
            'tahun_anggaran' => '2026',
            'keterangan' => 'original',
        ]);

        $payload = [
            'records' => [
                // same id_rup should update (no change)
                [
                    'id_rup' => 'RUP-001',
                    'nama_pekerjaan' => 'Project Alpha',
                    'nama_instansi' => 'Instansi A',
                    'tahun_anggaran' => '2026',
                    'keterangan' => 'original',
                ],
                // duplicate by name+instansi+tahun should be skipped
                [
                    'nama_pekerjaan' => 'Project Alpha',
                    'nama_instansi' => 'Instansi A',
                    'tahun_anggaran' => '2026',
                    'keterangan' => 'duplicate via name',
                ],
                // new unique record should be created
                [
                    'id_rup' => 'RUP-002',
                    'nama_pekerjaan' => 'Project Beta',
                    'nama_instansi' => 'Instansi B',
                    'tahun_anggaran' => '2026',
                    'keterangan' => 'new',
                ],
            ],
        ];

        $response = $this->postJson('/api/n8n/import', $payload);

        $response->assertStatus(200);
        $data = $response->json('data');

        $this->assertEquals(1, $data['created']);
        // the first record matches existing and has no changes => updated should be 0
        $this->assertEquals(0, $data['updated']);
        // one skipped because duplicate by name
        $this->assertEquals(1, $data['skipped']);

        $this->assertDatabaseHas('import_rencana_umum_pengadaan', [
            'id_rup' => 'RUP-002',
            'nama_pekerjaan' => 'Project Beta'
        ]);
    }
}
