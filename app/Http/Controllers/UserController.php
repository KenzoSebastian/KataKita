<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function showProfile($id)
    {
        return "data profile $id";
    }

    public function updatePhotoProfile(Request $request, $id)
    {
        $validated = $request->validate(
            [
                'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'image.image' => 'Image must be an image',
                'image.mimes' => 'Image must be a jpeg, png, jpg, gif, or svg',
                'image.max' => 'Image must be less than 2MB',
            ],
        );
        try {
            $user = User::findOrFail($id);

            // Hapus file lama jika ada
            if ($user->profile_picture) {
                // Ambil path relatif dari storage/public
                $oldPath = str_replace('storage/', '', $user->profile_picture);
                \Storage::delete($oldPath);
            }

            if ($request->hasFile('image')) {
                $storeFile = $request->file('image')->store('profile-pictures');
                $validated['image'] = 'storage/' . $storeFile;
            }

            User::where('id', $id)->update(['profile_picture' => $validated['image']]);
            return redirect()->route('beranda')->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with(['error' => 'Failed to update profile. Please try again.']);
        }
    }
}
