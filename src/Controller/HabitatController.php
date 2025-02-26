<?php

namespace App\Controller;

use App\Entity\Habitat;
use App\Entity\Image;
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
        private UrlGeneratorInterface $urlGenerator
    ) {}

    #[Route('/new', methods: ['POST'], name: 'new')]
    #[OA\Post(
        summary: "Créer un nouvel habitat",
        tags: ["Habitat"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "nom", type: "string"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "commentaire_habitat", type: "string"),
                    new OA\Property(property: "image_id", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Habitat créé avec succès"),
            new OA\Response(response: 400, description: "Données invalides")
        ]
    )]
    public function new(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (!isset($data['nom'], $data['description'])) {
            return new JsonResponse(['message' => 'Données incomplètes'], Response::HTTP_BAD_REQUEST);
        }

        $habitat = $this->serializer->deserialize($request->getContent(), Habitat::class, 'json');

        if (isset($data['image_id'])) {
            $image = $this->manager->getRepository(Image::class)->find($data['image_id']);
            if ($image) {
                $habitat->addImage($image);
            }
        }

        $this->manager->persist($habitat);
        $this->manager->flush();

        return new JsonResponse(
            $this->serializer->serialize($habitat, 'json', [AbstractNormalizer::GROUPS => ['habitat:read']]),
            Response::HTTP_CREATED,
            [],
            true
        );
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        summary: "Modifier un habitat existant",
        tags: ["Habitat"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                type: "object",
                properties: [
                    new OA\Property(property: "nom", type: "string"),
                    new OA\Property(property: "description", type: "string"),
                    new OA\Property(property: "commentaire_habitat", type: "string"),
                    new OA\Property(property: "image_id", type: "integer")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Habitat modifié avec succès"),
            new OA\Response(response: 404, description: "Habitat non trouvé"),
            new OA\Response(response: 400, description: "Données invalides")
        ]
    )]
    public function edit(int $id, Request $request): JsonResponse
    {
        $habitat = $this->habitatRepository->find($id);
        if (!$habitat) {
            return new JsonResponse(['message' => 'Habitat non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->serializer->deserialize(
            $request->getContent(),
            Habitat::class,
            'json',
            [AbstractNormalizer::OBJECT_TO_POPULATE => $habitat]
        );

        $data = json_decode($request->getContent(), true);
        if (isset($data['image_id'])) {
            $image = $this->manager->getRepository(Image::class)->find($data['image_id']);
            if ($image) {
                $habitat->addImage($image);
            }
        }

        $this->manager->flush();

        return new JsonResponse(
            $this->serializer->serialize($habitat, 'json', [AbstractNormalizer::GROUPS => ['habitat:read']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/show', name: 'show', methods: ['GET'])]
    #[OA\Get(
        summary: "Lister tous les habitats",
        tags: ["Habitat"],
        responses: [
            new OA\Response(response: 200, description: "Liste des habitats"),
        ]
    )]
    public function list(): JsonResponse
    {
        $habitats = $this->habitatRepository->findAll();
        return new JsonResponse(
            $this->serializer->serialize($habitats, 'json', [AbstractNormalizer::GROUPS => ['habitat:read']]),
            Response::HTTP_OK,
            [],
            true
        );
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: "Supprimer un habitat",
        tags: ["Habitat"],
        responses: [
            new OA\Response(response: 200, description: "Habitat supprimé avec succès"),
            new OA\Response(response: 404, description: "Habitat non trouvé")
        ]
    )]
    public function delete(int $id): JsonResponse
    {
        $habitat = $this->habitatRepository->find($id);
        if (!$habitat) {
            return new JsonResponse(['message' => 'Habitat non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $this->manager->remove($habitat);
        $this->manager->flush();

        return new JsonResponse(['message' => 'Habitat supprimé avec succès'], Response::HTTP_OK);
    }
}

