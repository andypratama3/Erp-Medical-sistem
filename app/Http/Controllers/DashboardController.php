<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FertilizerTransaction;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard.index');
    }
}
