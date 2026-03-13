<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_login_requires_no_rm(): void
    {
        $res = $this->postJson('/login', ['password' => 'secret']);
        $res->assertStatus(422);
    }

    public function test_login_locked_after_5_failures(): void
    {
        $ip = '127.0.0.1';
        Cache::put('login_attempts_' . $ip, 5, now()->addMinutes(15));
        $res = $this->postJson('/login', ['no_rm' => '00-01-01', 'password' => 'wrong']);
        $res->assertStatus(423);
    }
}
