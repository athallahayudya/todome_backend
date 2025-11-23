<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /*
      GET PROFILE
     */
    public function getProfile(Request $request)
    {
        $user = Auth::user();

        return response()->json([
            'message' => 'success',
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'bio'   => $user->bio,
                'photo' => $user->photo,
                'photo_url' => $user->photo
                    ? asset('storage/' . $user->photo)
                    : asset('default.png'),
            ]
        ]);
    }

    /*
      UPDATE PROFILE
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'  => 'required|string|max:255',
            'bio'   => 'nullable|string|max:500',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

        // Update data text
        $user->name = $request->name;
        $user->bio  = $request->bio;

        // Upload foto baru
        if ($request->hasFile('photo')) {
            // Hapus foto lama
            if ($user->photo && Storage::disk('public')->exists($user->photo)) {
                Storage::disk('public')->delete($user->photo);
            }

            // Upload foto baru
            $path = $request->file('photo')->store('profile_photos', 'public');
            $user->photo = $path;
        }

        $user->save();

        return response()->json([
            'message' => 'Profil berhasil diperbarui',
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
                'bio'   => $user->bio,
                'photo' => $user->photo,
                'photo_url' => $user->photo
                    ? asset('storage/' . $user->photo)
                    : asset('default.png'),
            ]
        ]);
    }
}
