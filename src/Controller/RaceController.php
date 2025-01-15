<?php

namespace App\Controller;
use App\Entity\Race;
use App\Repository\RaceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;





#[Route('api/race', name: 'app_api_race')]
class RaceController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $manager,
        private RaceRepository $raceRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {

    }


    //METHODE POST
    #[Route('/new', methods: ['POST'], name: 'create')]

    #[OA\Post(
        summary: "Ajouter une nouvelle race",
        tags: ["Race"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "label", type: "string", example: "Race de l'animal"),


                ]
            )
        ),
        responses: [  // Utilisation correcte de 'responses' ici
            new OA\Response(
                response: 201,
                description: "Race ajoutée avec succès", // Correction du message
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "label", type: "string", example: "Race de l'animal"),


                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Race non trouvé" // Correction du message
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
        $race = $this->serializer->deserialize($request->getContent(), Race::class, 'json');




        //On met l'objet sur liste d'attente avec persist puis on le push avec flush
        $this->manager->persist($race);
        $this->manager->flush();


        $responseData = $this->serializer->serialize($race, 'json');

        $location = $this->urlGenerator->generate(

            'app_api_raceshow',

            ['id' => $race->getId()],

            UrlGeneratorInterface::ABSOLUTE_URL,
        );



        //à stocker en bdd
        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);

    }



    //METHODE GET

    #[Route('/show/{id}', 'show', methods: ['GET'])]

    #[OA\Get(
        summary: "Afficher la race",
        tags: ["Race"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de la race à afficher",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Race trouvé avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "label", type: "string", example: "Race de l'animal"),


                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Race non trouvé"
            )
        ]
    )]







    public function show(int $id): Response
    {
        $race = $this->raceRepository->findOneBy(['id' => $id]);
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if ($race) {
            $responseData = $this->serializer->serialize($race, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




    //METHODE PUT
    #[Route('/edit/{id}', name: 'edit', methods: ['PUT'])]


    #[OA\Put(
        summary: "Modifier la race de l'animal",
        tags: ["Race"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de la race à modifier",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "label", type: "string", example: "Race de l'animal"),


                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Race modifié avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Race non trouvé"
            )
        ]
    )]








    public function edit(int $id, Request $request): JsonResponse
    {
        $race = $this->raceRepository->findOneBy(['id' => $id]);


        if ($race) {
            $race = $this->serializer->deserialize(
                $request->getContent(),
                Race::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $race]
            );

            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




    //METHODE DELETE

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]



    #[OA\Delete(
        summary: "Supprimer une race",
        tags: ["Race"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de la race à supprimer",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Race supprimé avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Race non trouvé"
            )
        ]
    )]




    public function delete(int $id): Response
    {

        $race = $this->raceRepository->findOneBy(['id' => $id]);
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if (!$race) {
            throw new \Exception("no race found for {$id} id");
        }

        $this->manager->remove($race); //S'il ne trouve pas, il supprime l'information
        $this->manager->flush();
        return $this->json(['Message' => 'race resource deleted'], Response::HTTP_NO_CONTENT);
    }
}