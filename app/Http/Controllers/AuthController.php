<?php

namespace App\Http\Controllers;

use App\Services\EpasienApiService;
use App\Services\SessionService;
use Illuminate\Http\Request;

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

        $result = $this->api->login(
            $request->input('no_rm'),
            $request->input('password')
        );

        if (!$result['success']) {
            return view('login', [
                'error' => $result['message'] ?? 'Login gagal.',
                'no_rm' => $request->input('no_rm'),
            ]);
        }

        $this->session->store($result['phpsessid'], $result['pasien']);

        return redirect()->route('dashboard');
    }

    public function logout()
    {
        $this->session->clear();
        return redirect()->route('login');
    }
}
