<?php

use App\Models\RupRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardApiTest extends \Tests\TestCase
{
    use RefreshDatabase;

    public function test_dashboard_page_renders_professional_overview(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('RUP Intelligence');
    }

    public function test_dashboard_api_returns_summary_payload(): void
    {
        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'stats',
                    'recent_records',
                    'chart_series',
                ],
            ]);
    }

    public function test_record_detail_page_renders(): void
    {
        $record = RupRecord::create([
            'nama_pekerjaan' => 'Detail test project',
            'nama_instansi' => 'Instansi demo',
            'tahun_anggaran' => '2026',
            'keterangan' => 'sample detail',
        ]);

        $response = $this->get('/records/'.$record->id);

        $response->assertStatus(200)
            ->assertSee('Detail record');
    }

    public function test_openclaw_preview_page_renders(): void
    {
        $response = $this->get('/openclaw');

        $response->assertStatus(200)
            ->assertSee('OpenClaw');
    }
}
