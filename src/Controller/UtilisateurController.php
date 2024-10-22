<?php

namespace App\Controller;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;





#[Route('api/utilisateur', name: 'app_api_utilisateur')]
class UtilisateurController extends AbstractController
{

public function __construct(
    private EntityManagerInterface $manager,
    private UtilisateurRepository $utilisateurRepository,
    private SerializerInterface $serializer,
    private UrlGeneratorInterface $urlGenerator,
) {

}


//METHODE POST
    #[Route( methods: ['POST'])]
  public function new(Request $request): JsonResponse
  {
 //Création d'un objet utilisateur static en dur avecc de fausses données pour tester l'api
// $utilisateur = new Utilisateur();
// $utilisateur->setUsername('testcrud@mail.com');
// $utilisateur->setPassword('Azerty_123');
// $utilisateur->setNom('koko');
// $utilisateur->setPrenom('jean');


//Serialiszer transforme un format en un autre format
$utilisateur = $this->serializer->deserialize($request->getContent(), Utilisateur::class, 'json');

        


//On met l'objet sur liste d'attente avec persist puis on le push avec flush
$this->manager->persist($utilisateur);
$this->manager->flush();


$responseData = $this->serializer->serialize($utilisateur, 'json');

        $location = $this->urlGenerator->generate(

            'app_api_utilisateurshow',

            ['id' => $utilisateur->getId()],

            UrlGeneratorInterface::ABSOLUTE_URL,
        );



//à stocker en bdd
return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);

  }



//METHODE GET

    #[Route('/{id}', 'show', methods: ['GET'])]
    public function show(int $id): Response
    {
$utilisateur = $this->utilisateurRepository->findOneBy(['id' => $id]);
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if($utilisateur) {
            $responseData = $this->serializer->serialize($utilisateur, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




//METHODE PUT
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $utilisateur = $this->utilisateurRepository->findOneBy(['id' => $id]);


        if ($utilisateur) {
           $utilisateur = $this->serializer->deserialize(
            $request->getContent(),
            Utilisateur::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE =>$utilisateur]
           );

        
return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




//METHODE DELETE

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {

        $utilisateur = $this->utilisateurRepository->findOneBy(['id' => $id]);
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if (!$utilisateur) {
            throw new \Exception("no utilisateur found for {$id} id");
        }

        $this->manager->remove($utilisateur); //S'il ne trouve pas, il supprime l'information
        $this->manager->flush();
        return $this->json(['Message' => 'utilisateur resource deleted'], Response::HTTP_NO_CONTENT);
    }
    }




