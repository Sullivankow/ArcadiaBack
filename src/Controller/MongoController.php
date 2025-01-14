<?php

namespace App\Controller;

use App\Document\YourDocument;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface; // Importation du SerializerInterface

#[Route('/mongo')]
class MongoController extends AbstractController
{
    private DocumentManager $documentManager;
    private SerializerInterface $serializer; // Injection du SerializerInterface

    public function __construct(DocumentManager $documentManager, SerializerInterface $serializer)
    {
        $this->documentManager = $documentManager;
        $this->serializer = $serializer;
    }

    // Méthode pour tester la connexion avec MongoDB
    #[Route('/mongo-test', name: 'mongo_test', methods: ['GET'])]
    public function testMongoConnection(): JsonResponse
    {
        // Créer un nouveau document
        $document = new YourDocument();
        $document->setName('Test Name');
        $document->setDescription('Test Description');

        // Persister et sauvegarder dans MongoDB
        $this->documentManager->persist($document);
        $this->documentManager->flush();

        // Retourner une réponse JSON pour vérifier
        $responseData = $this->serializer->serialize($document, 'json'); // Utilisation du serializer
        return new JsonResponse([
            'message' => 'Document ajouté avec succès dans MongoDB',
            'document' => $responseData, // Sérialisation du document
        ]);
    }
}








