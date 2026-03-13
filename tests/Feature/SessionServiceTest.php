<?php

namespace Tests\Feature;

use App\Services\SessionService;
use Tests\TestCase;

class SessionServiceTest extends TestCase
{
    public function test_store_saves_phpsessid_in_laravel_session(): void
    {
        $svc = app(SessionService::class);
        $svc->store('abc123', ['nama' => 'Budi', 'no_rm' => '001']);
        $this->assertEquals('abc123', session('epasien_phpsessid'));
    }

    public function test_get_session_id_returns_value(): void
    {
        session(['epasien_phpsessid' => 'xyz789']);
        $svc = app(SessionService::class);
        $this->assertEquals('xyz789', $svc->getSessionId());
    }

    public function test_is_logged_in_false_when_empty(): void
    {
        $svc = app(SessionService::class);
        $this->assertFalse($svc->isLoggedIn());
    }

    public function test_clear_removes_session(): void
    {
        session(['epasien_phpsessid' => 'abc123']);
        $svc = app(SessionService::class);
        $svc->clear();
        $this->assertNull($svc->getSessionId());
    }
}
