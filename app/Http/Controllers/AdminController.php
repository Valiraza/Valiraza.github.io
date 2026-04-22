<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    private function formatUser(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'photo_url' => $user->profile_photo_path
                ? Storage::url($user->profile_photo_path)
                : null,
        ];
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->username)
            ->orWhere('name', $request->username)
            ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects',
            ], 401);
        }

        Auth::login($user);

        $token = base64_encode(
            $user->id . '|' . time() . '|' . hash('sha256', $user->email . env('APP_KEY'))
        );

        return response()->json([
            'success' => true,
            'message' => 'Connexion reussie',
            'access_token' => $token,
            'user' => $this->formatUser($user),
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        return response()->json([
            'success' => true,
            'message' => 'Deconnexion reussie',
        ]);
    }

    public function check(Request $request)
    {
        $token = $request->bearerToken();

        if ($this->validateToken($token)) {
            $user = $this->getUserFromToken($token);

            return response()->json([
                'authenticated' => true,
                'user' => $this->formatUser($user),
            ]);
        }

        return response()->json([
            'authenticated' => false,
        ], 200);
    }

    private function validateToken(?string $token): bool
    {
        if (! $token) {
            return false;
        }

        $decoded = base64_decode($token);
        if (! $decoded) {
            return false;
        }

        $parts = explode('|', $decoded);
        if (count($parts) !== 3) {
            return false;
        }

        [$userId, $timestamp, $hash] = $parts;

        if (time() - $timestamp > 86400) {
            return false;
        }

        $user = User::find($userId);
        if (! $user) {
            return false;
        }

        $expectedHash = hash('sha256', $user->email . env('APP_KEY'));

        return hash_equals($expectedHash, $hash);
    }

    private function getUserFromToken(string $token): ?User
    {
        $decoded = base64_decode($token);
        $parts = explode('|', $decoded);

        return User::find($parts[0]);
    }

    public function updateProfile(Request $request)
    {
        $user = $this->getUserFromToken($request->bearerToken());

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur introuvable.',
            ], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'remove_profile_photo' => 'nullable|boolean',
        ]);

        if ($request->filled('password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Le mot de passe actuel est incorrect.',
                ], 400);
            }

            $user->password = Hash::make($request->password);
        }

        if ($request->boolean('remove_profile_photo') && $user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
        }

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $extension = $request->file('profile_photo')->getClientOriginalExtension();
            $filename = 'admin-' . $user->id . '-' . Str::uuid() . '.' . $extension;

            $user->profile_photo_path = $request->file('profile_photo')
                ->storeAs('admin-profiles', $filename, 'public');
        }

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Profil mis a jour avec succes.',
            'user' => $this->formatUser($user),
        ]);
    }
}
