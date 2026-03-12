<?php

namespace App\Http\Controllers;

use App\Services\SessionService;

class DashboardController extends Controller
{
    public function __construct(private SessionService $session) {}

    public function index()
    {
        if (!$this->session->isLoggedIn()) {
            return redirect()->route('login');
        }

        return view('dashboard', [
            'pasien' => $this->session->getPasien(),
            'menus'  => config('epasien.menus'),
        ]);
    }
}
