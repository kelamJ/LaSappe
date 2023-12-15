<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Produit;
use App\Form\CategorieType;
use App\Form\ProduitType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/crud', name: 'crud_')]
class CrudController extends AbstractController
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/read/{id}', name: 'read')]
    public function readCat(int $id): Response
    {
        $repository = $this->entityManager->getRepository(Categorie::class);
        $categorie = $repository->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('La catégorie avec l\'ID ' . $id . ' n\'existe pas.');
        }

        return $this->render('crud/cat/read.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/addCat', name: 'add')]
    public function createCat(Request $request): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $catImage = $form['cat_image']->getData();

            if ($catImage) {
                $newFilename = uniqid().'.'.$catImage->guessExtension();

                try {
                    $catImage->move(
                        $this->getParameter('categorie_images_directory'),
                        $newFilename
                    );
                }   catch (FileException $e) {}

                $categorie->setCatImage($newFilename);
            }
            $this->entityManager->persist($categorie);
            $this->entityManager->flush();

            return $this->redirectToRoute('crud_read', ['id' => $categorie->getId()]);
        }

        return $this->render('crud/cat/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/update/{id}', name: 'update')]
    public function updateCat(Request $request, int $id): Response
    {
        $categorie = $this->entityManager->getRepository(Categorie::class)->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('La catégorie avec l\'ID ' . $id . ' n\'existe pas.');
        }
        $currentImage = $categorie->getCatImage();

        $form = $this->createForm(CategorieType::class, $categorie);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        // Générez un nouveau nom de fichier unique pour l'image
            $newCatImage = $form['cat_image']->getData();

            if ($newCatImage) {
                $newFilename = uniqid().'.'.$newCatImage->guessExtension();
                try {
                    // Déplacez la nouvelle image vers le répertoire approprié
                    $newCatImage->move(
                        $this->getParameter('categorie_images_directory'),
                        $newFilename
                    );

                    // Mettez à jour le nom de fichier dans l'entité
                    $categorie->setCatImage($newFilename);

                    // Supprimez l'ancienne image si elle existe
                    if ($currentImage) {
                        unlink($this->getParameter('categorie_images_directory').'/'.$currentImage);
                    }
                } catch (FileException $e) {
                    // Gestion des erreurs si le déplacement échoue, possibilité d'ajouter du code pour ajuster l'erreur
                }
            }

            // Mettez à jour la relation parent/enfant
            $parent = $form['parent']->getData();
            $categorie->setParent($parent);

            // Mettez à jour d'autres propriétés
            $categorie->setCatNom($form['cat_nom']->getData());
            $categorie->setCatDescription($form['cat_description']->getData());

            $this->entityManager->flush();

            return $this->redirectToRoute('crud_read', ['id' => $categorie->getId()]);
        }

        return $this->render('crud/cat/update.html.twig', [
            'form' => $form->createView(),
            'categorie' => $categorie,
        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function deleteCat(Request $request, int $id): Response
    {
        $categorie = $this->entityManager->getRepository(Categorie::class)->find($id);

        if (!$categorie) {
            throw $this->createNotFoundException('La catégorie avec l\'ID ' . $id . ' n\'existe pas.');
        }

        if ($request->isMethod('POST')) {
            // Si le formulaire a été soumis, supprimez l'entité de la base de données
            $this->entityManager->remove($categorie);
            $this->entityManager->flush();

            $this->addFlash('success', 'La catégorie a été supprimée avec succès.');

            return $this->redirectToRoute('catalogue'); // Redirigez vers la page d'accueil ou une autre page
            // appropriée.
        }

        return $this->render('crud/cat/delete.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    #[Route('/readPro/{id}', name: 'readPro')]
    public function readPro(int $id): Response
    {
        $repository = $this->entityManager->getRepository(Produit::class);
        $produit = $repository->find($id);

        if (!$produit) {
            throw $this->createNotFoundException('Le produit avec l\'ID ' . $id . ' n\'existe pas.');
        }

        return $this->render('crud/pro/read.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/addPro', name: 'createPro')]
    public function createPro(Request $request): Response
    {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form['image']->getData();

            if ($imageFile) {
                $newFilename = uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('produit_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception si le déplacement du fichier échoue
                }

                $produit->setImage($newFilename);
            }

            $this->entityManager->persist($produit);
            $this->entityManager->flush();

            return $this->redirectToRoute('crud_readPro', ['id' => $produit->getId()]);
        }

        return $this->render('crud/pro/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
