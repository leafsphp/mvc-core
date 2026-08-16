<?php

namespace App\Controllers\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        return response()->inertia('dashboard');
    }
}
