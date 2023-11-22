<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(CategorieRepository $repo): Response
    {
        $categories = $repo->findBy(["parent" => null]);

        return $this->render('accueil/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/{categorie}', name: 'app_categories')]
    public function categories(Categorie $categorie, CategorieRepository $repo): Response
    {

        return $this->render('accueil/categories.html.twig', [
            'categorie' => $categorie,
            'souscategories' => $repo->findBy([], ['id' => 'asc'])
        ]);
    }

    #[Route('/produits/{categorie}', name: 'app_produits')]
    public function produits(Categorie $categorie, CategorieRepository $repo): Response
    {

        return $this->render('produit/index.html.twig', [
            'categorie' => $categorie,
            'souscategorie' => $repo->findBy([], ['id' => 'asc'])

        ]);
    }
}
