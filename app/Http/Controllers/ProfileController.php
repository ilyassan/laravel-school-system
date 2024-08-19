<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\Helper;
use Illuminate\View\View;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ResetPasswordRequest;

class ProfileController extends BaseController
{
    /**
     * Display the user's profile information.
     */
    public function index(): View
    {
        return view('profile.index');
    }

    /**
     * Display the user's profile information.
     */
    public function show(User $user)
    {
        return view('profile.show', compact('user'));
    }

    /**
     * Display the user's profile information.
     */
    public function edit(): View
    {
        return view('profile.edit');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $validatedData = $request->validated();

        $user = $this->getAuthUser();

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            if ($image->isValid()) {
                $imagePath = $image->store(Helper::profile_images_path());
                $validatedData['image_path'] = basename($imagePath);

                // Delete old image if exists
                if ($user->image_path) {
                    Storage::delete(Helper::profile_images_path() . $user->image_path);
                }
            } else {
                return back()->withErrors(['image' => 'The uploaded image is not valid.']);
            }
        }
        $validatedData = Arr::except($validatedData, ['image']);

        $user->update($validatedData);

        return redirect()->route('profile.index')->with('success', 'Your informations has been updated successfully!');
    }

    /**
     * Reset user's password page.
     */
    public function resetImage(): RedirectResponse
    {
        $user = $this->getAuthUser();

        // Delete old image if exists
        if ($user->image_path) {
            Storage::delete(Helper::profile_images_path() . $user->image_path);
        }

        $user->update(['image_path' => NULL]);

        return redirect()->route('profile.index')->with('success', 'Your informations has been updated successfully!');
    }


    /**
     * Reset user's password page.
     */
    public function showResetPassword(): View
    {
        return view('profile.reset-password');
    }

    /**
     * Reset the user's password.
     */
    public function resetPassword(ResetPasswordRequest $request): RedirectResponse
    {
        $user = $this->getAuthUser();

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Your password has been updated successfully!');
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

        return redirect()->route('login');
    }
}
