<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_EMPLOYE')]
#[Route('/employe', name: 'app_employe_')]
class EmployeController extends AbstractController
{
    #[Route('/commandes', name: 'commandes')]
    public function commandes(CommandeRepository $commandeRepo): Response
    {
        return $this->render('employe/commandes.html.twig', [
            'commandes' => $commandeRepo->findBy([], ['dateCommande' => 'DESC']),
        ]);
    }

    #[Route('/commandes/{id}/statut', name: 'commande_statut', methods: ['POST'], requirements: ['id' => '\d+'])]
    public function commandeStatut(
        Commande $commande,
        Request $request,
        EntityManagerInterface $em
    ): Response {
        if ($this->isCsrfTokenValid('statut_employe_' . $commande->getId(), $request->request->get('_token'))) {
            $statut = $request->request->get('statut', 'en_preparation');
            if (in_array($statut, ['en_preparation', 'en_livraison', 'livree', 'annulee'], true)) {
                $commande->setStatut($statut);
                $em->flush();
                $this->addFlash('success', 'Statut de la commande #CMD-' . str_pad((string) $commande->getId(), 5, '0', STR_PAD_LEFT) . ' mis à jour.');
            }
        }

        return $this->redirectToRoute('app_employe_commandes');
    }
}
