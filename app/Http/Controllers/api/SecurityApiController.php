<?php


namespace App\Http\Controllers\api;


use App\Http\Controllers\Controller;
use App\Http\Helpers\Helpers;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\Console\Helper\Helper;


class SecurityApiController extends Controller
{
    /**
     * 🔹 Authentification d'un utilisateur (login)
     */
    public function login(Request $request)
    {
        logger($request->all());
        $request->validate([
            'phone' => 'required',
            'password' => 'required|string|min:4',
        ]);

        // Vérifie les identifiants
        if (!Auth::attempt($request->only('phone', 'password'))) {
            return response()->json([
                'status' => 'error',
                'message' => 'Identifiants invalides',
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();

        // Crée un token API
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Connexion réussie',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->user_type,
                'token' => $token,
            ],
        ]);
    }
public function register(Request $request)
{
    logger($request->all());

    // 🔹 Validation
    $request->validate([
        'phone' => 'required|string',
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'password' => 'required|string|min:4',
    ]);

    // 🔹 Vérifier si utilisateur existe
    $existingUser = User::where('phone', $request->phone)
                        ->orWhere('email', $request->email)
                        ->first();

    if ($existingUser) {
        return response()->json([
            'status' => 'error',
            'message' => 'Un utilisateur avec ce téléphone ou email existe déjà.',
        ], 409);
    }

    // 🔹 Création de l'utilisateur
    $user = User::create([
        'phone' => $request->phone,
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'user_type' => 'customer',
    ]);
 $customer = Customer::create([
    'user_id'=>$user->id,

 ]);
    // 🔹 Génération du token JWT
     $token = $user->createToken('auth_token')->plainTextToken;

    // 🔹 Retour JSON avec token
    return response()->json([
        'status' => 'success',
        'message' => 'Inscription réussie',
        'data' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->user_role,
            'token' => $token, // 🔹 Token utilisable côté NextAuth
        ],
    ]);
}


    /**
     * 🔹 Déconnexion
     */
    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Déconnexion réussie',
        ]);
    }

    /**
     * 🔹 Retourne les infos du user connecté
     */
    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user(),
        ]);
    }
    /**
     * 🔄 Mise à jour du profil
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => ['sometimes', 'string', 'max:255'],
            'phone' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('users', 'phone')->ignore($user->id),
            ],
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
        ]);

        if (empty($validated)) {
            return response()->json([
                'message' => 'Aucune donnée à mettre à jour.',
            ], 422);
        }

        $user->update($validated);

        return Helpers::success([
            'message' => 'Profil mis à jour avec succès.',
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'phone' => $user->phone,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * 🔐 Changement du mot de passe
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Mot de passe actuel incorrect.',
            ], 403);
        }

        if (Hash::check($validated['new_password'], $user->password)) {
            return response()->json([
                'message' => 'Le nouveau mot de passe doit être différent de l’ancien.',
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return Helpers::success([
            'message' => 'Mot de passe modifié avec succès.',
        ]);
    }
}
