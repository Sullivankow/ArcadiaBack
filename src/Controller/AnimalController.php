<?php

namespace App\Controller;
use App\Entity\Animal;
use App\Repository\AnimalRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;





#[Route('api/animal', name: 'app_api_animal')]
class AnimalController extends AbstractController
{

    public  function __construct  (
        private EntityManagerInterface $manager,
        private AnimalRepository $animalRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {

    }


    //METHODE POST
    #[Route(methods: ['POST'])]

    #[OA\Post(
        summary: "Créer un nouvel animal",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "prenom", type: "string", example: "Prénom de l'animal"),
                    new OA\Property(property: "etat", type: "string", example: "état de l'animal"),
                   
                ]
            )
        ),
        responses: [  // Utilisation correcte de 'responses' ici
            new OA\Response(
                response: 201,
                description: "Animal créé avec succès", // Correction du message
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "prenom", type: "string", example: "Prénom de l'animal"),
                        new OA\Property(property: "etat", type: "string", example: "état de l'animal"),
                        
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Animal non trouvé" // Correction du message
            )
        ]
    )]








    public function new(Request $request): JsonResponse
    {
        //Création d'un objet utilisateur static en dur avec de fausses données pour tester l'api
// $utilisateur = new Utilisateur();
// $utilisateur->setUsername('testcrud@mail.com');
// $utilisateur->setPassword('Azerty_123');
// $utilisateur->setNom('koko');
// $utilisateur->setPrenom('jean');


        //Serialiszer transforme un format en un autre format
        $animal = $this->serializer->deserialize($request->getContent(), Animal::class, 'json');




        //On met l'objet sur liste d'attente avec persist puis on le push avec flush
        $this->manager->persist($animal);
        $this->manager->flush();


        $responseData = $this->serializer->serialize($animal, 'json');

        $location = $this->urlGenerator->generate(

            'app_api_animalshow',

            ['id' => $animal->getId()],

            UrlGeneratorInterface::ABSOLUTE_URL,
        );



        //à stocker en bdd
        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);

    }



    //METHODE GET

    #[Route('/{id}', 'show', methods: ['GET'])]

    #[OA\Get(
        summary: "Afficher Animal",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'habitat à afficher",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Animal trouvé avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "prenom", type: "string", example: "Nom de l'animal"),
                        new OA\Property(property: "etat", type: "string", example: "état de l'animal"),

                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Animal non trouvé"
            )
        ]
    )]







    public function show(int $id): Response
    {
        $animal = $this->animalRepository->findOneBy(['id' => $id]);
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if ($animal) {
            $responseData = $this->serializer->serialize($animal, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




    //METHODE PUT
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]


    #[OA\Put(
        summary: "Modifier animal",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'animal à modifier",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "prenom", type: "string", example: "Prénom de l'animal"),
                    new OA\Property(property: "etat", type: "string", example: "état de l'animal"),
                    
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Animal modifié avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Animal non trouvé"
            )
        ]
    )]








    public function edit(int $id, Request $request): JsonResponse
    {
        $animal = $this->animalRepository->findOneBy(['id' => $id]);


        if ($animal) {
            $animal = $this->serializer->deserialize(
                $request->getContent(),
                Animal::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $animal]
            );

            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




    //METHODE DELETE

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]



    #[OA\Delete(
        summary: "Supprimer un animal",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'animal à supprimer",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Animal supprimé avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Animal non trouvé"
            )
        ]
    )]




    public function delete(int $id): Response
    {

        $animal = $this->animalRepository->findOneBy(['id' => $id]);
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if (!$animal) {
            throw new \Exception("no animal found for {$id} id");
        }

        $this->manager->remove($animal); //S'il ne trouve pas, il supprime l'information
        $this->manager->flush();
        return $this->json(['Message' => 'animal resource deleted'], Response::HTTP_NO_CONTENT);
    }
}