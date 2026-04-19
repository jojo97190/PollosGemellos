<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'app_catalogue')]
    public function index(
        Request $request,
        ProduitRepository $produitRepo,
        CategorieRepository $categorieRepo
    ): Response {
        $categories  = $categorieRepo->findAll();
        $categorieId = $request->query->getInt('categorie', 0);

        if ($categorieId > 0) {
            $produits = $produitRepo->findByCategorie($categorieId);
        } else {
            $produits = $produitRepo->findAll();
        }

        return $this->render('catalogue/index.html.twig', [
            'produits'        => $produits,
            'categories'      => $categories,
            'categorieActive' => $categorieId,
        ]);
    }
}
