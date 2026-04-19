<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\PanierProduit;
use App\Entity\PanierProduitIngredient;
use App\Entity\Produit;
use App\Repository\IngredientRepository;
use App\Repository\PanierRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/panier', name: 'app_panier_')]
class PanierController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(
        SessionInterface $session,
        PanierRepository $panierRepo
    ): Response {
        $panierId = $session->get('panier_id');
        $panier = $panierId ? $panierRepo->find($panierId) : null;

        return $this->render('panier/index.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/ajouter/{id}', name: 'ajouter', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function ajouter(
        Produit $produit,
        Request $request,
        SessionInterface $session,
        PanierRepository $panierRepo,
        IngredientRepository $ingredientRepo,
        EntityManagerInterface $em
    ): Response {
        $panierId = $session->get('panier_id');
        $panier = $panierId ? $panierRepo->find($panierId) : null;

        if (!$panier || $panier->getStatut() !== 'en_cours') {
            $panier = new Panier();
            $em->persist($panier);
        }

        $quantite = max(1, (int) $request->request->get('quantite', 1));

        $ligne = new PanierProduit();
        $ligne->setProduit($produit);
        $ligne->setQuantite($quantite);
        $panier->addPanierProduit($ligne);
        $em->persist($ligne);

        // Ingrédients supprimés (cochés dans le formulaire comme "enlever")
        $ingredientsEnlever = $request->request->all('enlever') ?? [];
        foreach ($ingredientsEnlever as $ingredientId) {
            $ingredient = $ingredientRepo->find($ingredientId);
            if ($ingredient) {
                $modif = new PanierProduitIngredient();
                $modif->setIngredient($ingredient);
                $modif->setAction('enlever');
                $ligne->addModification($modif);
                $em->persist($modif);
            }
        }

        // Suppléments ajoutés
        $ingredientsAjouter = $request->request->all('ajouter') ?? [];
        foreach ($ingredientsAjouter as $ingredientId) {
            $ingredient = $ingredientRepo->find($ingredientId);
            if ($ingredient) {
                $modif = new PanierProduitIngredient();
                $modif->setIngredient($ingredient);
                $modif->setAction('ajouter');
                $ligne->addModification($modif);
                $em->persist($modif);
            }
        }

        $panier->calculerMontantTotal();
        $em->flush();

        $session->set('panier_id', $panier->getId());

        $this->addFlash('success', '✅ ' . $produit->getNom() . ' ajouté au panier !');

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/retirer/{id}', name: 'retirer', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function retirer(
        PanierProduit $ligne,
        SessionInterface $session,
        PanierRepository $panierRepo,
        EntityManagerInterface $em
    ): Response {
        $panier = $ligne->getPanier();
        $panier->removePanierProduit($ligne);
        $em->remove($ligne);
        $panier->calculerMontantTotal();
        $em->flush();

        $this->addFlash('success', 'Produit retiré du panier.');

        return $this->redirectToRoute('app_panier_index');
    }

    #[Route('/vider', name: 'vider', methods: ['POST'])]
    public function vider(
        SessionInterface $session,
        PanierRepository $panierRepo,
        EntityManagerInterface $em
    ): Response {
        $panierId = $session->get('panier_id');
        if ($panierId) {
            $panier = $panierRepo->find($panierId);
            if ($panier) {
                foreach ($panier->getPanierProduits() as $ligne) {
                    $em->remove($ligne);
                }
                $panier->setMontantTotal('0.00');
                $em->flush();
            }
        }

        $this->addFlash('info', 'Panier vidé.');

        return $this->redirectToRoute('app_panier_index');
    }
}
