<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur AuthController - Gestion de l'authentification
 * 
 * Fournit les endpoints pour l'inscription et la connexion des utilisateurs.
 * Note: Le JWT sera géré par lexik_jwt_authentication.
 * 
 * Routes disponibles :
 * - POST /api/login : Connexion d'un utilisateur
 * - POST /api/register : Inscription d'un nouvel utilisateur
 * 
 * @author Campus Scheduler Team
 * @version 1.0
 */
#[Route('/api')]
class AuthController extends AbstractController
{
    /**
     * Connecte un utilisateur avec email et mot de passe
     * 
     * @param Request $request Requête avec email et password en JSON
     * @param UserRepository $userRepository Repository des utilisateurs
     * @param UserPasswordHasherInterface $passwordHasher Hasher de mots de passe
     * @return JsonResponse JSON avec user et token (placeholder)
     */
    #[Route('/login', name: 'api_login', methods: ['POST'])]
    public function login(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        // Décodage des données JSON
        $data = json_decode($request->getContent(), true);
        
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        // Validation des champs requis
        if (!$email || !$password) {
            return $this->json(['message' => 'Email and password required'], 400);
        }

        // Recherche de l'utilisateur par email
        $user = $userRepository->findByEmail($email);

        // Vérification du mot de passe
        if (!$user || !$passwordHasher->isPasswordValid($user, $password)) {
            return $this->json(['message' => 'Invalid credentials'], 401);
        }

        // Note: Le token JWT sera généré par lexik_jwt_authentication
        // C'est un placeholder pour l'implémentation JWT réelle
        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'role' => $user->getRole(),
            ],
            'token' => 'jwt_token_placeholder'
        ]);
    }

    /**
     * Inscrit un nouvel utilisateur
     * 
     * @param Request $request Requête avec email, password et role en JSON
     * @param UserRepository $userRepository Repository des utilisateurs
     * @param UserPasswordHasherInterface $passwordHasher Hasher de mots de passe
     * @return JsonResponse JSON avec l'utilisateur créé
     */
    #[Route('/register', name: 'api_register', methods: ['POST'])]
    public function register(
        Request $request,
        UserRepository $userRepository,
        UserPasswordHasherInterface $passwordHasher
    ): JsonResponse {
        // Décodage des données JSON
        $data = json_decode($request->getContent(), true);
        
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        $role = $data['role'] ?? 'ROLE_USER';

        // Validation des champs requis
        if (!$email || !$password) {
            return $this->json(['message' => 'Email and password required'], 400);
        }

        // Vérification si l'utilisateur existe déjà
        if ($userRepository->findByEmail($email)) {
            return $this->json(['message' => 'User already exists'], 400);
        }

        // Création du nouvel utilisateur
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($passwordHasher->hashPassword($user, $password));
        $user->setRole($role);

        // Sauvegarde en base de données
        $userRepository->save($user, true);

        return $this->json([
            'user' => [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
                'role' => $user->getRole(),
            ]
        ], 201);
    }
}
