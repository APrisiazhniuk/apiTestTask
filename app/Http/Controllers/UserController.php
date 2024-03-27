<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Helpers\ImageHelper;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::paginate(6);
        return view('users', compact('users'));
    }

    public function showMoreUsers()
    {
        $users = User::paginate(6);
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate data from Request
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'photo' => 'required|image|mimes:jpeg,jpg|max:5120',
        ]);


        // Create new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        // Generate and store remember_token
        $rememberToken = Str::random(60); // Generate a яrandom token
        $user->update(['remember_token' => $rememberToken]);

         // Image optimization
         if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $imagePath = ImageHelper::optimizeAndResize($photo);

            if ($imagePath) {
                $user->photo = $imagePath;
                $user->save();
            } else {
                // Handle error
                return back()->withErrors(['image' => 'Failed to optimize and resize the image.']);
            }
        }

        return redirect('/users')->with('message', 'User created successfully!');
    }

}
