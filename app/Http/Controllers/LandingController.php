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

        $institutions = Institution::where('is_active', true)
            ->withCount(['datasets' => fn ($q) => $q->where('is_active', true)])
            ->with(['datasets' => fn ($q) => $q->where('is_active', true)->select('datasets.id', 'datasets.institution_id', 'datasets.code', 'datasets.name', 'datasets.frequency')])
            ->orderBy('code')
            ->get();

        return view('landing', [
            'siteName' => Setting::siteName(),
            'tagline' => Setting::tagline(),
            'logoUrl' => Setting::logoUrl(),
            'manualInstitutions' => $institutions->where('integration_mode', 'manual')->values(),
            'apiInstitutions' => $institutions->where('integration_mode', 'api')->values(),
            'stats' => [
                'institutions' => $institutions->count(),
                'datasets' => Dataset::where('is_active', true)->count(),
                'published' => Submission::where('status', 'published')->count(),
            ],
        ]);
    }
}
