<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function edit()
    {
        $this->authorize('settings.manage');

        return view('settings.edit', [
            'siteName' => Setting::siteName(),
            'tagline' => Setting::tagline(),
            'logoUrl' => Setting::logoUrl(),
        ]);
    }

    public function update(Request $request)
    {
        $this->authorize('settings.manage');

        $data = $request->validate([
            'site_name' => ['required', 'string', 'max:120'],
            'site_tagline' => ['nullable', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'remove_logo' => ['boolean'],
        ]);

        $old = ['site_name' => Setting::siteName(), 'site_tagline' => Setting::tagline()];

        Setting::set('site_name', $data['site_name']);
        Setting::set('site_tagline', $data['site_tagline'] ?? null);

        if ($request->boolean('remove_logo')) {
            $this->deleteLogo();
            Setting::set('site_logo', null);
        }

        if ($request->hasFile('logo')) {
            $this->deleteLogo();
            $file = $request->file('logo');
            $name = 'logo_'.now()->format('YmdHis').'.'.$file->getClientOriginalExtension();
            $file->move(public_path('uploads'), $name);
            Setting::set('site_logo', $name);
        }

        AuditLog::record('settings.updated', null, $old, [
            'site_name' => $data['site_name'],
            'site_tagline' => $data['site_tagline'] ?? null,
            'logo' => Setting::get('site_logo'),
        ]);

        return back()->with('success', 'Portal settings updated. The new name and logo are live on the landing page and header.');
    }

    private function deleteLogo(): void
    {
        $logo = Setting::get('site_logo');
        if ($logo && file_exists(public_path('uploads/'.$logo))) {
            unlink(public_path('uploads/'.$logo));
        }
    }
}
