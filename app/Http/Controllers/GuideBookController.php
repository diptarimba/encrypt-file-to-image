<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuideBookController extends Controller
{
    public function index()
    {
        $path = storage_path('app/public/guidebook.pdf');
        return response()->file($path);
    }
}
