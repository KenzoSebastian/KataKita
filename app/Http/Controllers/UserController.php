<?php

namespace App\Http\Controllers;

use App\Models\Following;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function showProfile($id)
    {
        $user = User::findOrFail($id)->loadCount(['followers', 'followings', 'posts']);
        if (auth()->check()) {
            $activeUser = auth()
                ->user()
                ->load(['followers', 'followings']);
            return view('pages.showProfile', compact(['activeUser', 'user']));
        }
        return view('pages.showProfile', compact(['user']));
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
            return redirect()->back()->with('success', 'Profile updated successfully');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with(['error' => 'Failed to update profile. Please try again.']);
        }
    }

    public function updateBannerProfile(Request $request, $id)
    {
        $validated = $request->validate(
            [
                'banner' => 'mimes:jpeg,png,jpg,gif,svg|max:4096',
            ],
            [
                'banner.mimes' => 'Banner must be a jpeg, png, jpg, gif, or svg',
                'banner.max' => 'Banner must be less than 4MB',
            ],
        );
        try {
            $user = User::findOrFail($id);
            // Hapus banner lama jika ada
            if ($user->banner_picture) {
                $oldPath = str_replace('storage/', '', $user->banner_picture);
                \Storage::delete($oldPath);
            }
            // Simpan banner baru
            if ($request->hasFile('banner')) {
                $storeFile = $request->file('banner')->store('banner-images');
                $validated['banner'] = 'storage/' . $storeFile;
            }

            User::where('id', $id)->update($validated);
            return redirect()->back()->with('success', 'Banner updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update banner. Please try again.');
        }
    }

    public function updateBio(Request $request, $id)
    {
        $validated = $request->validate(
            [
                'bio' => 'nullable|string|max:100',
            ],
            [
                'bio.string' => 'Bio must be a string',
                'bio.max' => 'Bio must be less than 100 characters',
            ],
        );
        try {
            $user = User::findOrFail($id);
            $user->bio = $validated['bio'];
            $user->save();

            return response()->json([
                'status' => 'success',
                'bio' => $user->bio,
                'message' => 'Bio updated successfully',
            ]);
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Failed to update bio. Please try again.',
                    ],
                    500,
                );
            }
            return redirect()->back()->with('error', 'Failed to update bio. Please try again.');
        }
    }

    public function follow($id)
    {
        try {
            $activeUser = auth()->user()->load('followings');
            $user = User::findOrFail($id)->load('followers')->loadCount('followers');
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
                'followers_count' => $user->followers_count + 1,
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
            $user = User::findOrFail($id)->load('followers')->loadCount('followers');
            // cek apakah user sudah mengikuti
            if ($activeUser->followings->contains('following_id', $user->id)) {
                // hapus data following
                $activeUser->followings()->where('following_id', $user->id)->delete();
                // hapus data follower
                $user->followers()->where('follower_id', $activeUser->id)->delete();
                return response()->json([
                    'status' => 'success',
                    'followers_count' => $user->followers_count - 1,
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

    public function followers($id)
    {
        $user = User::findOrFail($id);

        return view('pages.follow', [
            'title' => 'Followers',
            'user' => $user,
        ]);
    }
    public function followings($id)
    {
        $user = User::findOrFail($id);
        return view('pages.follow', [
            'title' => 'Following',
            'user' => $user,
        ]);
    }
    public function followersData($id)
    {
        try {
            $user = User::findOrFail($id)->load('followersData')->loadCount('followers');
            $followers = $user->followersData;

            if (request()->ajax()) {
                return view('components.follow-list', [
                    'list' => $followers,
                    'type' => 'followers',
                    'length' => $user->followers_count,
                ]);
            }
            return redirect()->route('profile.followers', ['id' => $user->id]);
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Failed to load followers data.',
                    ],
                    500,
                );
            }
            return response('<div class="py-8 text-center text-gray-400">Failed to load followers data.</div>', 500);
        }
    }

    public function followingsData($id)
    {
        try {
            $user = User::findOrFail($id)->load('followingsData')->loadCount('followings');
            $followings = $user->followingsData;
            if (request()->ajax()) {
                return view('components.follow-list', [
                    'list' => $followings,
                    'type' => 'followings',
                    'length' => $user->followings_count,
                ]);
            }
            return redirect()->route('profile.followings', ['id' => $user->id]);

        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json(
                    [
                        'status' => 'error',
                        'message' => 'Failed to load followings data.',
                    ],
                    500,
                );
            }
            return response('<div class="py-8 text-center text-gray-400">Failed to load followings data.</div>', 500);
        }
    }
}
