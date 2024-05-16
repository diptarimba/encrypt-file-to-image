<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Steganography;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $countEncrypted = Steganography::count();
        return view('page.admin-dashboard.home.index', compact('countEncrypted'));
    }
}
