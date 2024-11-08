<?php

namespace App\Controller;
use App\Entity\Image;
use App\Repository\ImageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use OpenApi\Attributes as OA;





#[Route('api/image', name: 'app_api_image')]
class ImageController extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $manager,
        private ImageRepository $imageRepository,
        private SerializerInterface $serializer,
        private UrlGeneratorInterface $urlGenerator,
    ) {

    }


    //METHODE POST
    #[Route(methods: ['POST'])]

    #[OA\Post(
        summary: "Ajouter une nouvelle image",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "image_data", type: "blob", example: "image de l'habitat"),
                    

                ]
            )
        ),
        responses: [  // Utilisation correcte de 'responses' ici
            new OA\Response(
                response: 201,
                description: "Image ajoutée avec succès", // Correction du message
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "image_data", type: "blob", example: "image de l'habitat"),
                        

                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Image non trouvé" // Correction du message
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
        $image = $this->serializer->deserialize($request->getContent(), Image::class, 'json');




        //On met l'objet sur liste d'attente avec persist puis on le push avec flush
        $this->manager->persist($image);
        $this->manager->flush();


        $responseData = $this->serializer->serialize($image, 'json');

        $location = $this->urlGenerator->generate(

            'app_api_imageshow',

            ['id' => $image->getId()],

            UrlGeneratorInterface::ABSOLUTE_URL,
        );



        //à stocker en bdd
        return new JsonResponse($responseData, Response::HTTP_CREATED, ["Location" => $location], true);

    }



    //METHODE GET

    #[Route('/{id}', 'show', methods: ['GET'])]

    #[OA\Get(
        summary: "Afficher l'image",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'image à afficher",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: "Image trouvé avec succès",
                content: new OA\JsonContent(
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 1),
                        new OA\Property(property: "image_data", type: "blob", example: "Image de l'habitat"),
                        

                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: "Image non trouvé"
            )
        ]
    )]







    public function show(int $id): Response
    {
        $image = $this->imageRepository->findOneBy(['id' => $id]);
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if ($image) {
            $responseData = $this->serializer->serialize($image, 'json');
            return new JsonResponse($responseData, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




    //METHODE PUT
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]


    #[OA\Put(
        summary: "Modifier l'image",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'image à modifier",
                schema: new OA\Schema(type: "integer")
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "image_data", type: "blob", example: "Image de l'habitat"),
                    

                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: "Image modifié avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Image non trouvé"
            )
        ]
    )]








    public function edit(int $id, Request $request): JsonResponse
    {
        $image = $this->imageRepository->findOneBy(['id' => $id]);


        if ($image) {
            $image = $this->serializer->deserialize(
                $request->getContent(),
                Image::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $image]
            );

            $this->manager->flush();
            return new JsonResponse(null, Response::HTTP_NO_CONTENT);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);

    }




    //METHODE DELETE

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]



    #[OA\Delete(
        summary: "Supprimer une image",
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID de l'image à supprimer",
                schema: new OA\Schema(type: "integer")
            )
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: "Image supprimé avec succès"
            ),
            new OA\Response(
                response: 404,
                description: "Image non trouvé"
            )
        ]
    )]




    public function delete(int $id): Response
    {

        $image = $this->imageRepository->findOneBy(['id' => $id]);
        // $utilisateur = Chercher utilisateur avec l'id = 1
        if (!$image) {
            throw new \Exception("no picture found for {$id} id");
        }

        $this->manager->remove($image); //S'il ne trouve pas, il supprime l'information
        $this->manager->flush();
        return $this->json(['Message' => 'picture resource deleted'], Response::HTTP_NO_CONTENT);
    }
}