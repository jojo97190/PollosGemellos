<?php

namespace App\Repository;

use App\Entity\Commande;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CommandeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Commande::class);
    }

    public function findByUtilisateur(Utilisateur $user): array
    {
        return $this->createQueryBuilder('c')
            ->where('c.utilisateur = :user')
            ->setParameter('user', $user)
            ->orderBy('c.dateCommande', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
