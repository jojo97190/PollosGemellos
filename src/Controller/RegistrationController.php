<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $hasher,
        EntityManagerInterface $em
    ): Response {
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $errors = [];

        if ($request->isMethod('POST')) {
            $prenom    = trim($request->request->get('prenom', ''));
            $nom       = trim($request->request->get('nom', ''));
            $email     = trim($request->request->get('email', ''));
            $tel       = trim($request->request->get('telephone', ''));
            $mdp       = $request->request->get('password', '');
            $mdpConf   = $request->request->get('password_confirm', '');

            // — Validation basique —
            if (!$prenom) $errors[] = 'Le prénom est obligatoire.';
            if (!$nom)    $errors[] = 'Le nom est obligatoire.';
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Adresse e-mail invalide.';
            if (strlen($mdp) < 6) $errors[] = 'Le mot de passe doit contenir au moins 6 caractères.';
            if ($mdp !== $mdpConf) $errors[] = 'Les mots de passe ne correspondent pas.';

            // — Vérifier doublon email —
            if (!$errors) {
                $existant = $em->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
                if ($existant) {
                    $errors[] = 'Cette adresse e-mail est déjà utilisée.';
                }
            }

            if (!$errors) {
                $user = new Utilisateur();
                $user->setPrenom($prenom)
                     ->setNom($nom)
                     ->setEmail($email)
                     ->setTelephone($tel ?: null)
                     ->setRoleMetier('client')
                     ->setPassword($hasher->hashPassword($user, $mdp));

                $em->persist($user);
                $em->flush();

                $this->addFlash('success', 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.');
                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('security/register.html.twig', [
            'errors' => $errors,
        ]);
    }
}
