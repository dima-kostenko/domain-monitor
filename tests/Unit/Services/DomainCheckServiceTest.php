<?php

namespace Tests\Unit\Services;

use App\Models\Domain;
use App\Services\DomainCheckService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DomainCheckServiceTest extends TestCase
{
    use RefreshDatabase;

    private DomainCheckService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DomainCheckService();
    }

    public function test_online_domain_saves_correct_check(): void
    {
        $domain = Domain::factory()->create(['method' => 'HEAD', 'timeout' => 10]);

        Http::fake([
            'https://' . $domain->name => Http::response('', 200),
        ]);

        $check = $this->service->check($domain);

        $this->assertEquals('online', $check->status);
        $this->assertEquals(200, $check->response_code);
        $this->assertNotNull($check->response_time);
        $this->assertNull($check->error_message);
        $this->assertDatabaseHas('domain_checks', ['domain_id' => $domain->id, 'status' => 'online']);
    }

    public function test_5xx_response_saved_as_offline(): void
    {
        $domain = Domain::factory()->create(['method' => 'GET', 'timeout' => 10]);

        Http::fake([
            'https://' . $domain->name => Http::response('Server Error', 503),
        ]);

        $check = $this->service->check($domain);

        $this->assertEquals('offline', $check->status);
        $this->assertEquals(503, $check->response_code);
        $this->assertEquals('HTTP 503', $check->error_message);
        $this->assertNull($check->response_time);
    }

    public function test_connection_exception_saved_as_offline(): void
    {
        $domain = Domain::factory()->create(['timeout' => 5]);

        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
        });

        $check = $this->service->check($domain);

        $this->assertEquals('offline', $check->status);
        $this->assertNull($check->response_code);
        $this->assertStringContainsString('timed out', $check->error_message);
    }

    public function test_uses_correct_http_method(): void
    {
        $domain = Domain::factory()->create(['method' => 'HEAD', 'timeout' => 10]);

        Http::fake([
            'https://' . $domain->name => Http::response('', 200),
        ]);

        $this->service->check($domain);

        Http::assertSent(function (Request $request) {
            return $request->method() === 'HEAD';
        });
    }

    public function test_redirect_followed_and_marked_online(): void
    {
        $domain = Domain::factory()->create(['method' => 'GET', 'timeout' => 10]);

        Http::fake([
            'https://' . $domain->name => Http::response('', 301),
        ]);

        $check = $this->service->check($domain);

        // 3xx is considered online (redirect)
        $this->assertEquals('online', $check->status);
    }
}
