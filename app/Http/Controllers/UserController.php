<?php

namespace App\Http\Controllers;

use App\Models\Following;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function showProfile($id)
    {
        return view('pages.showProfile', [
            'user' => User::findOrFail($id),
            'activeUser' => auth()->user(),
        ]);
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

    public function follow($id)
    {
        try {
            $activeUser = auth()->user()->load('followings');
            $user = User::findOrFail($id)->load('followers');
            // cek apakah user sudah mengikuti
            if ($activeUser->followings->contains('following_id', $user->id)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Already following this user',
                ]);
            }
            // buat data following baru
            $activeUser->followings()->create([
                'user_id' => $activeUser->id,
                'following_id' => $user->id,
            ]);
            // buat data follower baru
            $user->followers()->create([
                'user_id' => $user->id,
                'follower_id' => $activeUser->id,
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Followed successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not found',
                ],
                404,
            );
        }
    }
    public function unFollow($id)
    {
        try {
            $activeUser = auth()->user()->load('followings');
            $user = User::findOrFail($id)->load('followers');
            // cek apakah user sudah mengikuti
            if ($activeUser->followings->contains('following_id', $user->id)) {
                // hapus data following
                $activeUser->followings()->where('following_id', $user->id)->delete();
                // hapus data follower
                $user->followers()->where('follower_id', $activeUser->id)->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Unfollowed successfully',
                ]);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'You are not following this user',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'status' => 'error',
                    'message' => 'User not found',
                ],
                404,
            );
        }
    }
}
