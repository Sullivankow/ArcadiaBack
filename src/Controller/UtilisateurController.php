<?php

namespace App\Controller;
use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;




#[Route('api/utilisateur', name: 'app_api_utilisateur')]
class UtilisateurController extends AbstractController
{

public function __construct(
    private EntityManagerInterface $manager,
    private UtilisateurRepository $utilisateurRepository
) {

}


//METHODE POST
    #[Route(name: 'new', methods: ['POST'])]
  public function new(): Response
  {




    //Création d'un objet utilisateur en dur avecc de fausses données pour tester l'api
$utilisateur = new Utilisateur();
$utilisateur->setUsername('testcrud@mail.com');
$utilisateur->setPassword('Azerty_123');
$utilisateur->setNom('koko');
$utilisateur->setPrenom('jean');



//On met l'objet sur liste d'attente avec persist puis on le push avec flush
$this->manager->persist($utilisateur);
$this->manager->flush();

//à stocker en bdd
return $this->json(
    ['message' => "utilisateur resource created with {$utilisateur->getId()} id"],
Response::HTTP_CREATED
);

  }



//METHODE GET

    #[Route('/{id}', 'show', methods: ['GET'])]
    public function show(int $id): Response
    {

        // $utilisateur = Chercher utilisateur avec l'id = 1
        if(!$utilisateur) {
            throw new \Exception ("no utilisateur found for {$id} id" );
        }
return $this->json(
    ['Message' => "utilisateur was found {$utilisateur->getNom()} for {$utilisateur->getId()} id"]
);
    }




//METHODE PUT
    #[Route('/{id}', name: 'edit', methods: ['PUT'])]
    public function edit(int $id): Response
    {

        if (!$utilisateur) {
            throw new \Exception("no utilisateur found for {$id} id");
        }
$utilisateur->setNom('utilisateur name updated');
return $this->redirectToRoute('app_api_utilisateur_show', ['id'=> $utilisateur->getId()]);
    }




//METHODE DELETE

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id): Response
    {


        // $utilisateur = Chercher utilisateur avec l'id = 1
        if (!$utilisateur) {
            throw new \Exception("no utilisateur found for {$id} id");
        }
        return $this->json(['Message' => 'utilisateur resource deleted'], Response::HTTP_NO_CONTENT);
    }
    }




