<?php

namespace App\Http\Controllers;

use App\Services\SessionService;

class SessionExpiredController extends Controller
{
    public function __construct(private SessionService $session) {}

    public function index()
    {
        $this->session->clear();
        return redirect()->route('login')
            ->with('info', 'Sesi Anda telah berakhir. Silakan login kembali.');
    }
}
