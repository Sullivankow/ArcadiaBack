<?php

namespace App\Controller;
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use DateTimeImmutable;
use OpenApi\Attributes as OA;







//Système d'inscription
#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
    public function __construct(private EntityManagerInterface $manager, private SerializerInterface $serializer, private UserPasswordHasherInterface $passwordHasher, )
    {

    }

    #[Route('/registration', name: 'registration', methods: ['POST'])]

    #[OA\Post(
        path: "/api/registration",
        summary: "Inscription d'un nouvel utilisateur",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de l'utilisateur à inscrire",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "email", type: "string", example: "mail@email.com"),
                    new OA\Property(property: "password", type: "string", example: "Mot de passe"),
                    new OA\Property(property: "Roles", type: "string", example: ["rôle"]),


                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: "Utilisateur inscrit avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "user", type: "string", example: "Nom d'utilisateur"),
                        new OA\Property(property: "apiToken", type: "string", example: "31a023e212f116124a36af14ea0c1c3806eb9378"),
                        new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string", example: "ROLE_USER"))
                    ]
                )
            )
        ]
    )]







    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {

        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));




        $this->manager->persist($user);
        $this->manager->flush();
        return new JsonResponse(['user' => $user->getUserIdentifier(), 'apiToken' => $user->getApiToken(), 'roles' => $user->getRoles()], Response::HTTP_CREATED);

    }



    //system d'authentification

    #[Route('/login', name: 'login', methods: ['POST'])]


    #[OA\Post(
        path: "/api/login",
        summary: "Connecter un utilisateur",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Données de l’utilisateur pour se connecter",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "username", type: "string", example: "adresse@email.com"),
                    new OA\Property(property: "password", type: "string", example: "Mot de passe")
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Connexion réussie",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "user", type: "string", example: "Nom d'utilisateur"),
                        new OA\Property(property: "apiToken", type: "string", example: "31a023e212f116124a36af14ea0c1c3806eb9378"),
                        new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string", example: "ROLE_USER"))
                    ]
                )
            )
        ]
    )]







    public function login(#[CurrentUser] ?User $user): JsonResponse //Va chercher l'utilisateur en base de données
    {
        if (null === $user) {
            return new JsonResponse(['message' => 'Missing credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'user' => $user->getUserIdentifier(),
            'apiToken' => $user->getApiToken(),
            'roles' => $user->getRoles(),
        ]);
    }









    #[Route('/account/me', name: 'me', methods: 'GET')]


    #[OA\Get(
        path: "/api/account/me",
        summary: "Récupérer toutes les informations de l'objet User",
        responses: [
            new OA\Response(
                response: 200,
                description: "Tous les champs utilisateurs retournés",
            )
        ]
    )]

    public function me(): JsonResponse
    {
        $user = $this->getUser();

        $responseData = $this->serializer->serialize($user, 'json');

        return new JsonResponse($responseData, Response::HTTP_OK, [], true);
    }

    #[Route('/account/edit', name: 'edit', methods: 'PUT')]




    //Méthode pour modifier un utilisateur

    #[OA\Put(
        path: "/api/account/edit",
        summary: "Modifier son compte utilisateur avec l'un ou tous les champs",
        requestBody: new OA\RequestBody(
            required: true,
            description: "Nouvelles données éventuelles de l'utilisateur à mettre à jour",
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "username", type: "string", example: "adresse@email.com"),
                    new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string", example: "ROLE_USER"))
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 204,
                description: "Utilisateur modifié avec succès"
            )
        ]
    )]



    public function edit(Request $request): JsonResponse
    {
        $user = $this->serializer->deserialize(
            $request->getContent(),
            User::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $this->getUser()],
        );
        // $user->setUpdatedAt(new DateTimeImmutable());

        if (isset($request->toArray()['password'])) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        }

        $this->manager->flush();

        return new JsonResponse(null, Response::HTTP_NO_CONTENT);
    }









    //  Méthode pour lister les utilisateurs
    #[Route('/users', name: 'list_users', methods: ['GET'])]
    #[OA\Get(
        path: "/api/users",
        summary: "Obtenir la liste des utilisateurs",
        responses: [
            new OA\Response(
                response: 200,
                description: "Liste des utilisateurs",
                content: new OA\JsonContent(
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 1),
                            new OA\Property(property: "email", type: "string", example: "adresse@email.com"),
                            new OA\Property(property: "roles", type: "array", items: new OA\Items(type: "string", example: "ROLE_USER"))
                        ]
                    )
                )
            )
        ]
    )]
    public function listUsers(): JsonResponse
    {
        // Récupération des utilisateurs depuis la base de données
        $users = $this->manager->getRepository(User::class)->findAll();

        // Vérifiez que les utilisateurs sont bien récupérés
        if (empty($users)) {
            return new JsonResponse(['message' => 'Aucun utilisateur trouvé'], Response::HTTP_NOT_FOUND);
        }

        // Sérialisation des utilisateurs avec le groupe 'user:read'
        $data = $this->serializer->serialize($users, 'json', ['groups' => 'user:read']);

        return new JsonResponse($data, Response::HTTP_OK, ['Content-Type' => 'application/json'], true);
    }

}
