<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => auth()->user()->load('roles', 'institution')]);
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        auth()->user()->update(['password' => Hash::make($data['password'])]);
        AuditLog::record('profile.password-changed');

        return back()->with('success', 'Password updated.');
    }

    /** Create a Sanctum API token (API integration accounts). */
    public function createToken(Request $request)
    {
        $this->authorize('api.access');
        $data = $request->validate(['token_name' => ['required', 'string', 'max:60']]);

        $token = auth()->user()->createToken($data['token_name'], ['submit']);
        AuditLog::record('api.token-created', null, null, ['name' => $data['token_name']]);

        return back()->with('api_token', $token->plainTextToken);
    }

    public function revokeToken(int $tokenId)
    {
        $this->authorize('api.access');
        auth()->user()->tokens()->where('id', $tokenId)->delete();
        AuditLog::record('api.token-revoked', null, null, ['token_id' => $tokenId]);

        return back()->with('success', 'API token revoked.');
    }
}
