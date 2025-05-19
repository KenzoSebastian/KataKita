<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index()
    {
        // get all user for search
        $allUser = User::all();
        if (auth()->check()) {
            $activeUser = auth()
                ->user()
                ->load(['followers', 'followings']);
            return view('pages.main', compact(['activeUser', 'allUser']));
        }
        return view('pages.main', compact(['allUser']));
    }
}
