<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\User;

/**
 *Author: JP Lacsinto <jplacsinto@gmail.com>
 * Business logic for user authentication and registration
 */
class UserController extends Controller
{
    /**
     * Authenticate user
     * @param  Request $request Illuminate\Http\Request
     * @return Illuminate\Http\Response
     */
    public function authenticate(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);
        $user = User::where('email', $request->input('email'))->first();
        if ($user && Hash::check($request->input('password'), $user->password)) {
             $apiToken = base64_encode(Str::random(40));
             User::where('email', $request->input('email'))->update(['api_token' => $apiToken]);
             return response()->json(['status' => 'success','api_token' => $apiToken]);
        }
        return response()->json([
            'status' => 'fail',
            'message' => 'Invalid credentials'
        ], 401);
    }

    /**
     * Register user
     * @param  Request $request Illuminate\Http\Request
     * @return Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validate($request, [
            'email' => 'required|unique:users|max:255',
            'password' => 'required|max:255'
        ]);
        User::create([
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password'))
        ]);
        return response()->json(['status' => 'success']);
    }
}
