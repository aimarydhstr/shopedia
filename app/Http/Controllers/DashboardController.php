<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Illuminate\Validation\Rule;
use Session;

class DashboardController extends Controller
{
    public function index()
    {
        $title = "Dashboard";
        $auth = Auth::user();

        return view('dashboard.index', compact('auth', 'title'));
    }
}