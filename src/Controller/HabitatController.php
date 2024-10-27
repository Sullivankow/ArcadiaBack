<?php

namespace App\Controller;
use App\Entity\Habitat;
use App\Repository\HabitatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;





#[Route('api/habitat', name: 'app_api_habitat')]
class HabitatController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $manager,
        private HabitatRepository $habitatRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {

    }


    //METHODE POST
    #[Route(methods: ['POST'])]

    #[OA\Post(
        summary: "Créer un nouvel habitat",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "nom", type: "string", example: "Nom de l'habitat"),
                    new OA\Property(property: "description", type: "string", example: "Description de l'habitat"),
                    new OA\Property(property: "commentaire", type: "string", example: "Commentaire sur l'habitat")
                ]
            )
        ),
        responses: [  // Utilisation correcte de 'responses' ici
            new OA\Response(
                response: 201,
                description: "Habitat créé avec succès", // Correction du message
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "name", type: "string", example: "Nom de l'habitat"),
                        new OA\Property(property: "description", type: "string", example: "Description de l'habitat"),
                        new OA\Property(property: "createdAt", type: "string", format: "date-time")
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Habitat non trouvé" // Correction du message
            )
        ]
    )]








      public function new(Request $request): JsonResponse
    {
        //Création d'un objet utilisateur static en dur avecc de fausses données pour tester l'api
// $utilisateur = new Utilisateur();
// $utilisateur->setUsername('testcrud@mail.com');
// $utilisateur->setPassword('Azerty_123');
// $utilisateur->setNom('koko');
// $utilisateur->setPrenom('jean');


        //Serialiszer transforme un format en un autre format
        $habitat = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json');




        //On met l'objet sur liste d'attente avec persist puis on le push avec flush
        $this->manager->persist($habitat);
        $this->manager->flush();


        $responseData = $this->serializer->serialize($habitat, 'json');

        $location = $this->urlGenerator->generate(

            'app_api_habitatshow',

            ['id' => $habitat->getId()],

            UrlGeneratorInterface::ABSOLUTE_URL,
        );



        //à stocker en bdd
        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);

    }



    //METHODE GET

    #[Route('/{id}', 'show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $habitat = $this->habitatRepository->findOneBy(['id' => $id]);
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if ($habitat) {
            $responseData = $this->serializer->serialize($habitat, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




    //METHODE PUT
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id, Request $request): JsonResponse
    {
        $habitat = $this->habitatRepository->findOneBy(['id' => $id]);


        if ($habitat) {
            $habitat = $this->serializer->deserialize(
                $request->getContent(),
                Habitat::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $habitat]
            );


            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




    //METHODE DELETE

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {

        $habitat = $this->habitatRepository->findOneBy(['id' => $id]);
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if (!$habitat) {
            throw new \Exception("no habitat found for {$id} id");
        }

        $this->manager->remove($habitat); //S'il ne trouve pas, il supprime l'information
        $this->manager->flush();
        return $this->json(['Message' => 'habitat resource deleted'], Response::HTTP_NO_CONTENT);
    }
}


