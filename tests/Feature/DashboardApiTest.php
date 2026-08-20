<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardApiTest extends \Tests\TestCase
{
    use RefreshDatabase;

    public function test_root_health_endpoint_is_available(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'API Server is running');
    }

    public function test_dashboard_api_requires_authentication(): void
    {
        $response = $this->getJson('/api/dashboard');

        $response->assertStatus(401);
    }

    public function test_dashboard_api_returns_summary_payload_for_authenticated_client(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/dashboard');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'stats',
                    'summary',
                    'records',
                    'pagination',
                    'chart_series',
                    'weekly_series',
                    'status_breakdown',
                    'unread_notifications',
                ],
            ]);
    }

    public function test_n8n_webhook_accepts_payload_and_persists_log(): void
    {
        $payload = [
            'source' => 'n8n',
            'event' => 'customer_message',
            'message' => 'Halo dari n8n',
            'customer' => 'Budi',
        ];

        $response = $this->postJson('/api/n8n/webhook', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.status', 'accepted');

        $this->assertDatabaseHas('n8n_webhook_logs', [
            'event' => 'customer_message',
            'source' => 'n8n',
        ]);
    }
}
