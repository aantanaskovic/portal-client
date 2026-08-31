<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateApiToken(Request $request): RedirectResponse
    {
        $request->validate([
            'api_token' => ['nullable', 'string', 'max:500'],
        ]);

        $user = $request->user();

        if ($request->api_token === null) {
            $user->api_token = null;
            $user->api_token_abilities = null;
            $user->save();

            return Redirect::route('profile.edit')->with('status', 'api-token-removed');
        }

        if ($request->filled('api_token')) {

            $response = Http::withToken($request->api_token)
                ->acceptJson()
                ->timeout(5)
                ->get('http://portal-server.test/api/user/abilities');

            if ($response->failed()) {
                return back()->withErrors([
                    'api_token' => 'The provided API token is invalid or has expired.'
                ])->withInput();
            }

            $abilities = $response->json('abilities', []);

            $user->api_token = $request->api_token;
            $user->api_token_abilities = $abilities;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'api-token-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
