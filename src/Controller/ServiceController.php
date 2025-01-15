<?php

namespace App\Controller;

use App\Document\Service;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Attributes as OA;

#[Route('api/service', name: 'app_api_service')]

class ServiceController extends AbstractController
{
    #[Route('/new', methods: ['POST'], name: 'create')]
    #[OA\Post(
        summary: 'Ajouter un nouveau service',
        tags: ['Service'],
        description: 'Ajoute un service avec les détails fournis dans le corps de la requête.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'description', 'price', 'availability'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Visite guidée'),
                    new OA\Property(property: 'description', type: 'string', example: 'Une visite guidée du zoo.'),
                    new OA\Property(property: 'price', type: 'float', example: 29.99),
                    new OA\Property(property: 'availability', type: 'boolean', example: true)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Service ajouté avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Service ajouté avec succès !')
                    ]
                )
            )
        ]
    )]
    public function addService(Request $request, DocumentManager $dm): Response
    {
        $data = json_decode($request->getContent(), true);

        $service = new Service();
        $service->setName($data['name']);
        $service->setDescription($data['description']);
        $service->setPrice($data['price']);
        $service->setAvailability($data['availability']);

        $dm->persist($service);
        $dm->flush();

        return $this->json(['message' => 'Service ajouté avec succès !'], Response::HTTP_CREATED);
    }

    #[Route('/edit/{id}', name: 'edit', methods: ['PUT'])]
    #[OA\Put(
        summary: 'Mettre à jour un service',
        tags: ['Service'],
        description: 'Met à jour les informations d\'un service existant.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID du service',
                schema: new OA\Schema(type: 'string')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Visite guidée mise à jour'),
                    new OA\Property(property: 'description', type: 'string', example: 'Description mise à jour.'),
                    new OA\Property(property: 'price', type: 'float', example: 39.99),
                    new OA\Property(property: 'availability', type: 'boolean', example: false)
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Service mis à jour avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Service mis à jour avec succès !')
                    ]
                )
            )
        ]
    )]
    public function updateService(string $id, Request $request, DocumentManager $dm): Response
    {
        $data = json_decode($request->getContent(), true);
        $service = $dm->getRepository(Service::class)->find($id);

        if (!$service) {
            return $this->json(['error' => 'Service introuvable'], Response::HTTP_NOT_FOUND);
        }

        $service->setName($data['name']);
        $service->setDescription($data['description']);
        $service->setPrice($data['price']);
        $service->setAvailability($data['availability']);

        $dm->flush();

        return $this->json(['message' => 'Service mis à jour avec succès !']);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['DELETE'])]
    #[OA\Delete(
        summary: 'Supprimer un service',
        tags: ['Service'],
        description: 'Supprime un service en fonction de son ID.',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'ID du service',
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Service supprimé avec succès',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Service supprimé avec succès !')
                    ]
                )
            )
        ]
    )]
    public function deleteService(string $id, DocumentManager $dm): Response
    {
        $service = $dm->getRepository(Service::class)->find($id);

        if (!$service) {
            return $this->json(['error' => 'Service introuvable'], Response::HTTP_NOT_FOUND);
        }

        $dm->remove($service);
        $dm->flush();

        return $this->json(['message' => 'Service supprimé avec succès !']);
    }

    #[Route('/show', name: 'show', methods: ['GET'])]
    #[OA\Get(
        summary: 'Lister tous les services',
        tags: ['Service'],
        description: 'Retourne une liste de tous les services disponibles.',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des services',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'string', example: '64b7f7908f1d320001c9a0b3'),
                            new OA\Property(property: 'name', type: 'string', example: 'Visite guidée'),
                            new OA\Property(property: 'description', type: 'string', example: 'Une visite guidée du zoo.'),
                            new OA\Property(property: 'price', type: 'float', example: 29.99),
                            new OA\Property(property: 'availability', type: 'boolean', example: true)
                        ]
                    )
                )
            )
        ]
    )]
    public function listServices(DocumentManager $dm): Response
    {
        $services = $dm->getRepository(Service::class)->findAll();

        $data = array_map(fn(Service $service) => [
            'id' => $service->getId(),
            'name' => $service->getName(),
            'description' => $service->getDescription(),
            'price' => $service->getPrice(),
            'availability' => $service->getAvailability(),
        ], $services);

        return $this->json($data);
    }
}

