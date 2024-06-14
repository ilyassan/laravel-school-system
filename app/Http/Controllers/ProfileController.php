<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function show(Request $request): View
    {
        return view('profile.index');
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();
        
        $user = auth()->user();
        
        $user->update($validatedData);

        return Redirect::route('profile')->with('message', 'Your informations has been updated successfully!');
    }

    /**
     * Reset the user's password.
     */
    public function showResetPassword(Request $request): View
    {
        return view('profile.reset-password');
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $user = auth()->user();

        $user->password = Hash::make($request->new_password);

        $user->save();

        return back()->with('message', 'Your password has been updated successfully!');
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
