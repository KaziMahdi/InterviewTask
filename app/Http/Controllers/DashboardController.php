<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class DashboardController extends Controller
{
    public function dashBoard(){
        View::share('title','Dashboard');
        return view('panel.dashboard.dashboard');
    }
}
