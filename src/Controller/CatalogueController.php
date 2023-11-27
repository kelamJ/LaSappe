<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CatalogueController extends AbstractController
{
    #[Route('/', name: 'catalogue')]
    public function index(CategorieRepository $repo): Response
    {
        $categories = $repo->findBy(["parent" => null]);

        return $this->render('catalogue/index.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/{categorie}', name: 'app_categories')]
    public function categories(Categorie $categorie, CategorieRepository $repo): Response
    {

        return $this->render('catalogue/categories.html.twig', [
            'categorie' => $categorie,
            'souscategories' => $repo->findBy([], ['id' => 'asc'])
        ]);
    }

    #[Route('/allcategories/', name: 'all_categories')]
    public function allCategories( CategorieRepository $repo): Response
    {
        $categoriesWithParent = $repo->findCategoriesWithParent();

        return $this->render('catalogue/allcategories.html.twig', [
            'categoriesWithParent' => $categoriesWithParent,
        ]);
    }

    #[Route('/allproduits/', name: 'all_produits')]
    public function allproduits( ProduitRepository $repo): Response
    {
        $produits = $repo->findAll();

        return $this->render('catalogue/allproduits.html.twig', [
            'produit' => $produits,
        ]);
    }

    #[Route('/produits/{categorie}', name: 'app_produits')]
    public function produits(Categorie $categorie): Response
    {

        return $this->render('catalogue/produits.html.twig', [
            'categorie' => $categorie
        ]);
    }

    #[Route('/details/{slug}', name: 'details')]
    public function details(Produit $produit): Response
    {
        return $this->render('catalogue/details.html.twig', compact('produit'));
    }
}
