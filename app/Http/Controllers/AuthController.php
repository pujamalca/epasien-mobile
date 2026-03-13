<?php

namespace App\Http\Controllers;

use App\Services\EpasienApiService;
use App\Services\SessionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    public function __construct(
        private EpasienApiService $api,
        private SessionService $session,
    ) {}

    public function showLogin()
    {
        if ($this->session->isLoggedIn()) {
            return redirect()->route('dashboard');
        }
        return view('login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'no_rm'    => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'max:100'],
        ]);

        $ip  = $request->ip();
        $key = 'login_attempts_' . $ip;

        if (Cache::get($key, 0) >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Akun terkunci 15 menit karena terlalu banyak percobaan.',
            ], 423);
        }

        $result = $this->api->login(
            $request->input('no_rm'),
            $request->input('password')
        );

        if (!$result['success']) {
            Cache::put($key, Cache::get($key, 0) + 1, now()->addMinutes(15));
            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'No. RM atau password salah.',
            ]);
        }

        Cache::forget($key);
        $this->session->store($result['phpsessid'], $result['pasien']);

        return response()->json(['success' => true]);
    }

    public function logout()
    {
        $this->session->clear();
        return redirect()->route('login');
    }
}
