<?php

namespace App\Http\Controllers;

use App\Models\Dataset;
use App\Models\Institution;
use App\Models\Setting;
use App\Models\Submission;

class LandingController extends Controller
{
    public function index()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }

        return view('landing', [
            'siteName' => Setting::siteName(),
            'tagline' => Setting::tagline(),
            'logoUrl' => Setting::logoUrl(),
            'stats' => [
                'institutions' => Institution::where('is_active', true)->count(),
                'datasets' => Dataset::where('is_active', true)->count(),
                'published' => Submission::where('status', 'published')->count(),
            ],
        ]);
    }
}
