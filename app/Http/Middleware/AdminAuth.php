<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (!$this->validateToken($token)) {
            \Log::info('Token validation failed', ['token' => $token]);
            return response()->json([
                'message' => 'Non autorisé. Token manquant ou invalide.'
            ], 401);
        }

        // Ajouter l'utilisateur à la requête pour un accès facile
        $user = $this->getUserFromToken($token);
        $request->attributes->set('admin_user', $user);

        return $next($request);
    }

    private function validateToken(?string $token): bool
    {
        if (!$token) return false;

        $decoded = base64_decode($token);
        if (!$decoded) return false;

        $parts = explode('|', $decoded);
        if (count($parts) !== 3) return false;

        [$userId, $timestamp, $hash] = $parts;

        // Vérifier que le token n'est pas expiré (24h)
        if (time() - $timestamp > 86400) return false;

        $user = User::find($userId);
        if (!$user) return false;

        $expectedHash = hash('sha256', $user->email . env('APP_KEY'));
        return hash_equals($expectedHash, $hash);
    }

    private function getUserFromToken(string $token)
    {
        $decoded = base64_decode($token);
        $parts = explode('|', $decoded);
        return User::find($parts[0]);
    }
}
