<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieSupprimerType;
use App\Form\CategorieType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoriesController extends AbstractController
{
    /**
     * @Route("/", name="app_home")
     */
    public function index(ManagerRegistry $doctrine,Request $request): Response
    {

        //creation du formulaire d'ajout
        $categorie=new Categorie();//on crée une categorie vide
        //on crée un formulaire à partir de la class CategorieType et de notre objet vide
        $form=$this->createForm(CategorieType::class,$categorie);

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
            $em->persist($categorie);

            //générer l'insert
            $em->flush();
        }

        //pour aller cherher les categories dans la table categories, je vais utiliser un repository
        //pour me servir de doctrine j'ajoute le parametre $doctrine à la méthode
        $repo = $doctrine->getRepository(Categorie::class);
        $categories = $repo->findAll();


        return $this->render('categories/index.html.twig', [
            'categories' => $categories,
            'formulaire'=> $form->createView()
        ]);
    }

    /**
     * @Route("/categorie/modifier/{id}", name="categorie_modifier")
     */
    public function modifierCategorie($id, ManagerRegistry $doctrine, Request $request){
        //récupérer la categorie dans la BDD
        $categorie=$doctrine->getRepository(Categorie::class)->find($id);

        //si on n'a rien trouvé -> 404
        if(!$categorie){
            throw $this->createNotFoundException("Aucune catégorie avec l'id $id");
        }

        //si on arrive là, c'est qu'on a trouvé une catégorie
        //on crée le formulaire avec (il sera rempli avec ses valeurs)
        $form=$this->createForm(CategorieType::class,$categorie);

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
            $em->persist($categorie);

            //générer l'insert
            $em->flush();

            //retour à l'accueil
            return $this->redirectToRoute("app_home");
        }

        return $this->render("categories/modifier.html.twig",[
            'categorie' => $categorie,
            'formulaire'=>$form->createView()
        ]);
    }

    /**
     * @Route("/categorie/supprimer/{id}", name="categorie_supprimer")
     */
    public function supprimerCategorie($id, ManagerRegistry $doctrine, Request $request){
        //récupérer la categorie dans la BDD
        $categorie=$doctrine->getRepository(Categorie::class)->find($id);

        //si on n'a rien trouvé -> 404
        if(!$categorie){
            throw $this->createNotFoundException("Aucune catégorie avec l'id $id");
        }

        //si on arrive là, c'est qu'on a trouvé une catégorie
        //on crée le formulaire avec (il sera rempli avec ses valeurs)
        $form=$this->createForm(CategorieSupprimerType::class,$categorie);

        //gestion du retour du formulaire
        //on ajoute Request dans les parametres comme dans le projet precedent
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            //le handleRequest a rempli notre objet $categorie
            //qui n'est plus vide
            //pour sauvegarder,on va récupérer un entityManager de doctrine
            //qui comme son nom l'indique gère les entités
            $em=$doctrine->getManager();
            //on lui dit de la supprimer dans la BDD
            $em->remove($categorie);

            //générer l'insert
            $em->flush();

            //retour à l'accueil
            return $this->redirectToRoute("app_home");
        }

        return $this->render("categories/supprimer.html.twig",[
            'categorie' => $categorie,
            'formulaire'=>$form->createView()
        ]);
    }

}
