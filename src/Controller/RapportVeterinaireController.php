<?php

namespace App\Controller;
use App\Entity\RapportVeterinaire;
use App\Repository\RapportVeterinaireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;





#[Route('api/rapport', name: 'app_api_rapport')]
class RapportVeterinaireController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $manager,
        private RapportVeterinaireRepository $rapportVeternaireRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {

    }


    //METHODE POST
    #[Route('/new', methods: ['POST'], name: 'create')]

    #[OA\Post(
        summary: "Créer un nouveau rapport vétérinaire",
        tags: ["Rapport Vétérinaire"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "date", type: "date", example: "Date du rapport"),
                    new OA\Property(property: "detail", type: "string", example: "Détail du rapport"),

                ]
            )
        ),
        responses: [  // Utilisation correcte de 'responses' ici
            new OA\Response(
                response: 201,
                description: "Rappor vétérinaire créé avec succès", // Correction du message
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "date", type: "date", example: "Date du rapport"),
                        new OA\Property(property: "detail", type: "string", example: "Détail du rapport"),

                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Rapport vétérinaire non trouvé" // Correction du message
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
        $rapportVeterinaire = $this->serializer->deserialize($request->getContent(), RapportVeterinaire::class, 'json');




        //On met l'objet sur liste d'attente avec persist puis on le push avec flush
        $this->manager->persist($rapportVeterinaire);
        $this->manager->flush();


        $responseData = $this->serializer->serialize($rapportVeterinaire, 'json');

        $location = $this->urlGenerator->generate(

            'app_api_rapportshow',

            ['id' => $rapportVeterinaire->getId()],

            UrlGeneratorInterface::ABSOLUTE_URL,
        );



        //à stocker en bdd
        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);

    }



    //METHODE GET

    #[Route('/show/{id}', 'show', methods: ['GET'])]

    #[OA\Get(
        summary: "Afficher le rapport vétérinaire",
        tags: ["Rapport Vétérinaire"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID ddu rapport vétérinaire à afficher",
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
                        new OA\Property(property: "date", type: "date", example: "Date du rapport"),
                        new OA\Property(property: "detail", type: "string", example: "Détail du rapport"),

                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Rapport non trouvé"
            )
        ]
    )]







    public function show(int $id): Response
    {
        $rapportVeterinaire = $this->rapportVeternaireRepository->findOneBy(['id' => $id]);
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if ($rapportVeterinaire) {
            $responseData = $this->serializer->serialize($rapportVeterinaire, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




    //METHODE PUT
    #[Route('/edit/{id}', name: 'edit', methods: ['PUT'])]


    #[OA\Put(
        summary: "Modifier le rapport vétérinaire",
        tags: ["Rapport Vétérinaire"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du rapport vétérinaire à modifier",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "date", type: "date", example: "Date du rapport vétérinaire"),
                    new OA\Property(property: "detail", type: "string", example: "Détail du rapport vétérinaire"),

                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Rapport vétérinaire modifié avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Rapport vétérnaire non trouvé"
            )
        ]
    )]








    public function edit(int $id, Request $request): JsonResponse
    {
        $rapportVeterinaire = $this->rapportVeternaireRepository->findOneBy(['id' => $id]);


        if ($rapportVeterinaire) {
            $rapportVeterinaire = $this->serializer->deserialize(
                $request->getContent(),
                RapportVeterinaire::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $rapportVeterinaire]
            );

            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




    //METHODE DELETE

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]



    #[OA\Delete(
        summary: "Supprimer un rapport vétérinaire",
        tags: ["Rapport Vétérinaire"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID du rapport vétérinaire à supprimer",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Rapport vétérinaire supprimé avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Raport vétérinaire non trouvé"
            )
        ]
    )]




    public function delete(int $id): Response
    {

        $rapportVeterinaire = $this->rapportVeterinaireRepository->findOneBy(['id' => $id]);
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if (!$rapportVeterinaire) {
            throw new \Exception("no report found for {$id} id");
        }

        $this->manager->remove($rapportVeterinaire); //S'il ne trouve pas, il supprime l'information
        $this->manager->flush();
        return $this->json(['Message' => 'report resource deleted'], Response::HTTP_NO_CONTENT);
    }
}