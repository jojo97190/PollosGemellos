<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Entity\Ingredient;
use App\Entity\Produit;
use App\Entity\Utilisateur;
use App\Repository\CategorieRepository;
use App\Repository\CommandeRepository;
use App\Repository\IngredientRepository;
use App\Repository\ProduitRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin')]
class AdminController extends AbstractController
{
    // =====================================================================
    //  DASHBOARD
    // =====================================================================
    #[Route('', name: 'app_admin_dashboard')]
    public function dashboard(
        ProduitRepository $produitRepo,
        CategorieRepository $catRepo,
        UtilisateurRepository $userRepo,
        CommandeRepository $commandeRepo
    ): Response {
        return $this->render('admin/dashboard.html.twig', [
            'nbProduits'    => count($produitRepo->findAll()),
            'nbCategories'  => count($catRepo->findAll()),
            'nbUtilisateurs'=> count($userRepo->findAll()),
            'nbCommandes'   => count($commandeRepo->findAll()),
            'dernieresCmds' => $commandeRepo->findBy([], ['dateCommande' => 'DESC'], 5),
        ]);
    }

    // =====================================================================
    //  PRODUITS
    // =====================================================================
    #[Route('/produits', name: 'app_admin_produits')]
    public function produits(ProduitRepository $repo): Response
    {
        return $this->render('admin/produits/index.html.twig', [
            'produits' => $repo->findAll(),
        ]);
    }

    #[Route('/produits/nouveau', name: 'app_admin_produit_new', methods: ['GET','POST'])]
    public function produitNew(
        Request $request,
        CategorieRepository $catRepo,
        IngredientRepository $ingRepo,
        EntityManagerInterface $em
    ): Response {
        $categories  = $catRepo->findAll();
        $ingredients = $ingRepo->findAll();
        $errors = [];

        if ($request->isMethod('POST')) {
            [$produit, $errors] = $this->handleProduitForm($request, new Produit(), $catRepo, $ingRepo);
            if (!$errors) {
                $em->persist($produit);
                $em->flush();
                $this->addFlash('success', 'Produit créé avec succès.');
                return $this->redirectToRoute('app_admin_produits');
            }
        }

        return $this->render('admin/produits/form.html.twig', [
            'produit'     => null,
            'categories'  => $categories,
            'ingredients' => $ingredients,
            'errors'      => $errors,
            'titre'       => 'Nouveau produit',
        ]);
    }

    #[Route('/produits/{id}/modifier', name: 'app_admin_produit_edit', methods: ['GET','POST'])]
    public function produitEdit(
        Produit $produit,
        Request $request,
        CategorieRepository $catRepo,
        IngredientRepository $ingRepo,
        EntityManagerInterface $em
    ): Response {
        $categories  = $catRepo->findAll();
        $ingredients = $ingRepo->findAll();
        $errors = [];

        if ($request->isMethod('POST')) {
            [$produit, $errors] = $this->handleProduitForm($request, $produit, $catRepo, $ingRepo);
            if (!$errors) {
                $em->flush();
                $this->addFlash('success', 'Produit modifié avec succès.');
                return $this->redirectToRoute('app_admin_produits');
            }
        }

        return $this->render('admin/produits/form.html.twig', [
            'produit'     => $produit,
            'categories'  => $categories,
            'ingredients' => $ingredients,
            'errors'      => $errors,
            'titre'       => 'Modifier : ' . $produit->getNom(),
        ]);
    }

    #[Route('/produits/{id}/supprimer', name: 'app_admin_produit_delete', methods: ['POST'])]
    public function produitDelete(Produit $produit, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_produit_' . $produit->getId(), $request->request->get('_token'))) {
            $em->remove($produit);
            $em->flush();
            $this->addFlash('success', 'Produit supprimé.');
        }
        return $this->redirectToRoute('app_admin_produits');
    }

    private function handleProduitForm(Request $req, Produit $p, CategorieRepository $catRepo, IngredientRepository $ingRepo): array
    {
        $errors = [];
        $nom    = trim($req->request->get('nom', ''));
        $prix   = $req->request->get('prix', '');
        $desc   = trim($req->request->get('description', ''));
        $catId  = (int) $req->request->get('categorie_id', 0);
        $ingIds = $req->request->all('ingredients');

        if (!$nom)  $errors[] = 'Le nom est obligatoire.';
        if (!is_numeric($prix) || (float)$prix < 0) $errors[] = 'Le prix doit être un nombre positif.';

        $categorie = $catId ? $catRepo->find($catId) : null;
        if (!$categorie) $errors[] = 'Veuillez choisir une catégorie.';

        if (!$errors) {
            $p->setNom($nom)->setPrix($prix)->setDescription($desc)->setCategorie($categorie);
            // Réinitialiser les ingrédients
            foreach ($p->getIngredients() as $ing) {
                $p->removeIngredient($ing);
            }
            foreach ($ingIds as $ingId) {
                $ing = $ingRepo->find((int)$ingId);
                if ($ing) $p->addIngredient($ing);
            }
        }

        return [$p, $errors];
    }

    // =====================================================================
    //  CATÉGORIES
    // =====================================================================
    #[Route('/categories', name: 'app_admin_categories')]
    public function categories(CategorieRepository $repo): Response
    {
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $repo->findAll(),
        ]);
    }

    #[Route('/categories/nouvelle', name: 'app_admin_categorie_new', methods: ['GET','POST'])]
    public function categorieNew(Request $request, EntityManagerInterface $em): Response
    {
        $errors = [];
        if ($request->isMethod('POST')) {
            $nom  = trim($request->request->get('nom', ''));
            $desc = trim($request->request->get('description', ''));
            if (!$nom) { $errors[] = 'Le nom est obligatoire.'; }
            if (!$errors) {
                $cat = (new Categorie())->setNom($nom)->setDescription($desc);
                $em->persist($cat); $em->flush();
                $this->addFlash('success', 'Catégorie créée.');
                return $this->redirectToRoute('app_admin_categories');
            }
        }
        return $this->render('admin/categories/form.html.twig', ['categorie' => null, 'errors' => $errors, 'titre' => 'Nouvelle catégorie']);
    }

    #[Route('/categories/{id}/modifier', name: 'app_admin_categorie_edit', methods: ['GET','POST'])]
    public function categorieEdit(Categorie $categorie, Request $request, EntityManagerInterface $em): Response
    {
        $errors = [];
        if ($request->isMethod('POST')) {
            $nom  = trim($request->request->get('nom', ''));
            $desc = trim($request->request->get('description', ''));
            if (!$nom) { $errors[] = 'Le nom est obligatoire.'; }
            if (!$errors) {
                $categorie->setNom($nom)->setDescription($desc);
                $em->flush();
                $this->addFlash('success', 'Catégorie modifiée.');
                return $this->redirectToRoute('app_admin_categories');
            }
        }
        return $this->render('admin/categories/form.html.twig', ['categorie' => $categorie, 'errors' => $errors, 'titre' => 'Modifier : ' . $categorie->getNom()]);
    }

    #[Route('/categories/{id}/supprimer', name: 'app_admin_categorie_delete', methods: ['POST'])]
    public function categorieDelete(Categorie $categorie, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete_cat_' . $categorie->getId(), $request->request->get('_token'))) {
            $em->remove($categorie); $em->flush();
            $this->addFlash('success', 'Catégorie supprimée.');
        }
        return $this->redirectToRoute('app_admin_categories');
    }

    // =====================================================================
    //  UTILISATEURS
    // =====================================================================
    #[Route('/utilisateurs', name: 'app_admin_utilisateurs')]
    public function utilisateurs(UtilisateurRepository $repo): Response
    {
        return $this->render('admin/utilisateurs/index.html.twig', [
            'utilisateurs' => $repo->findAll(),
        ]);
    }

    #[Route('/utilisateurs/{id}/role', name: 'app_admin_utilisateur_role', methods: ['POST'])]
    public function utilisateurRole(Utilisateur $user, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('role_user_' . $user->getId(), $request->request->get('_token'))) {
            $role = $request->request->get('role_metier', 'client');
            if (in_array($role, ['client', 'employe', 'admin'])) {
                $user->setRoleMetier($role);
                $em->flush();
                $this->addFlash('success', 'Rôle mis à jour.');
            }
        }
        return $this->redirectToRoute('app_admin_utilisateurs');
    }

    // =====================================================================
    //  COMMANDES
    // =====================================================================
    #[Route('/commandes', name: 'app_admin_commandes')]
    public function commandes(CommandeRepository $repo): Response
    {
        return $this->render('admin/commandes/index.html.twig', [
            'commandes' => $repo->findBy([], ['dateCommande' => 'DESC']),
        ]);
    }

    #[Route('/commandes/{id}/statut', name: 'app_admin_commande_statut', methods: ['POST'])]
    public function commandeStatut(\App\Entity\Commande $commande, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('statut_cmd_' . $commande->getId(), $request->request->get('_token'))) {
            $statut = $request->request->get('statut', 'en_preparation');
            if (in_array($statut, ['en_preparation','en_livraison','livree','annulee'])) {
                $commande->setStatut($statut);
                $em->flush();
                $this->addFlash('success', 'Statut mis à jour.');
            }
        }
        return $this->redirectToRoute('app_admin_commandes');
    }
}
