<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        CategorieRepository $categorieRepo,
        ProduitRepository $produitRepo
    ): Response {
        $categories = $categorieRepo->findAll();
        // Quelques produits mis en avant (max 6)
        $produitsVedette = array_slice($produitRepo->findAll(), 0, 6);

        return $this->render('home/index.html.twig', [
            'categories'      => $categories,
            'produitsVedette' => $produitsVedette,
        ]);
    }
}
