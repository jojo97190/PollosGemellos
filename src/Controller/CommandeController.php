<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/commande', name: 'app_commande_')]
class CommandeController extends AbstractController
{
    #[Route('/passer', name: 'passer', methods: ['POST'])]
    public function passer(
        Request $request,
        SessionInterface $session,
        PanierRepository $panierRepo,
        EntityManagerInterface $em
    ): Response {
        $panierId = $session->get('panier_id');
        $panier = $panierId ? $panierRepo->find($panierId) : null;

        if (!$panier || $panier->getPanierProduits()->isEmpty()) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('app_panier_index');
        }

        $adresse = trim($request->request->get('adresse_livraison', ''));
        if (empty($adresse)) {
            $this->addFlash('error', 'Veuillez indiquer une adresse de livraison.');
            return $this->redirectToRoute('app_panier_index');
        }

        // Lier l'utilisateur connecté au panier
        if ($this->getUser()) {
            $panier->setUtilisateur($this->getUser());
        }

        $panier->calculerMontantTotal();
        $panier->setStatut('valide');

        $commande = new Commande();
        $commande->setPanier($panier);
        $commande->setMontantTotal($panier->getMontantTotal() ?? '0.00');
        $commande->setAdresseLivraison($adresse);
        if ($this->getUser()) {
            $commande->setUtilisateur($this->getUser());
        }

        $em->persist($commande);
        $em->flush();

        $session->remove('panier_id');

        return $this->redirectToRoute('app_commande_confirmation', ['id' => $commande->getId()]);
    }

    #[Route('/confirmation/{id}', name: 'confirmation', requirements: ['id' => '\d+'])]
    public function confirmation(Commande $commande): Response
    {
        return $this->render('commande/confirmation.html.twig', [
            'commande' => $commande,
        ]);
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/mes-commandes', name: 'mes_commandes')]
    public function mesCommandes(CommandeRepository $commandeRepo): Response
    {
        return $this->render('commande/mes_commandes.html.twig', [
            'commandes' => $commandeRepo->findByUtilisateur($this->getUser()),
        ]);
    }
}
