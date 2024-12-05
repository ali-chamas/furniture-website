<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;

class UserController extends Controller
{


    public function getUsers(Request $request)
    {
        $users = User::all();

        return response()->json([
            'users' => $users
        ], 200);
    }

    public function getUser(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }

            return response()->json([
                'user' => $user
            ], 200);

        } catch (JWTException $e) {
            return response()->json(['error' => 'Token is invalid or expired'], 401);
        }
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'full_name' => 'required',
            'address' => 'required',
            'phone_number' => 'required',
        ]);

        $user->update([
            'full_name' => $request->full_name,
            'address' => $request->address,
            'phone_number' => $request->phone_number,
        ]);

        return response()->json([
            'message' => 'User details updated successfully.',
            'user' => $user
        ]);
    }

    public function delete($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    public function ban(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->is_banned = !$user->is_banned;
        $user->save();

        return response()->json([
            'message' => $user->is_banned ? 'User has been banned.' : 'User has been unbanned.',
            'user' => $user
        ]);
    }
}
