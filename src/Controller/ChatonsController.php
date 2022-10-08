<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Chaton;
use App\Form\CategorieType;
use App\Form\ChatonType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ChatonsController extends AbstractController
{
    /**
     * @Route("/chatons/ajouter", name="app_chatons")
     */
    public function AjouterChatons(ManagerRegistry $doctrine,Request $request): Response
    {

        //creation du formulaire d'ajout
        $chaton=new Chaton();//on crée une categorie vide
        //on crée un formulaire à partir de la class CategorieType et de notre objet vide
        $form=$this->createForm(ChatonType::class,$chaton);

        //gestion du retour du formulaire
        //on ajoute Request dans les parametres comme dans le projet precedent
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            //le handleRequest a rempli notre objet $categorie
            //qui n'est plus vide
            //pour sauvegarder,on va récupérer un entityManager de doctrine
            //qui comme son nom l'indique gère les entités
            $em=$doctrine->getManager();
            //on lui dit de ranger dans la BDD
            $em->persist($chaton);

            //générer l'insert
            $em->flush();
        }

        //pour aller cherher les categories dans la table categories, je vais utiliser un repository
        //pour me servir de doctrine j'ajoute le parametre $doctrine à la méthode
        $repo = $doctrine->getRepository(Chaton::class);
        $categories = $repo->findAll();


        return $this->render('chatons/ajouter.html.twig', [
            'chaton' => $chaton,
            'categories'=> $categories,
            'formulaire'=> $form->createView()
        ]);
    }


    /**
     * @Route("/chatons/{idCategorie}", name="chatons_view")
     */
    public function AfficherChatons($idCategorie, ManagerRegistry $doctrine, Request $request){
        //récupérer la categorie dans la BDD
        $categorie=$doctrine->getRepository(Categorie::class)->find($idCategorie);

        //si on n'a rien trouvé -> 404
        if(!$categorie){
            throw $this->createNotFoundException("Aucune catégorie avec l'id $idCategorie");
        }

        return $this->render("chatons/index.html.twig", [
            "categorie"=>$categorie,
            "chatons"=>$categorie->getChatons()
        ]);
    }



}
