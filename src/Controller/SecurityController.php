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






//Système d'inscription
#[Route('/api', name: 'app_api_')]
class SecurityController extends AbstractController
{
public function __construct(private EntityManagerInterface $manager, private serializerInterface $serializer)
{

}

    #[Route('/registration', name: 'registration', methods:['POST'])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {

        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setPassword ($passwordHasher->hashPassword($user, $user->getPassword()));



        $this->manager->persist($user);
        $this->manager->flush();
        return new JsonResponse(['user' => $user->getUserIdentifier(), 'apiToken' => $user->getApiToken(), 'roles' => $user->getRoles()], Response::HTTP_CREATED);
    
}



//system d'authentification

#[Route('/login', name: 'login', methods: 'POST')]
    public function login(#[CurrentUser] ?User $user): JsonResponse //Va chercher l'utilisateur en base de données
    {
        if (null === $user) {
            return new JsonResponse(['message' => 'Missing credentials'], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'user'  => $user->getUserIdentifier(),
            'apiToken' => $user->getApiToken(),
            'roles' => $user->getRoles(),
        ]);
    }
}




