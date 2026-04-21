<?php

namespace App\Repository;

use App\Entity\Panier;
use App\Entity\Utilisateur;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class PanierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Panier::class);
    }

    public function findEnCoursByUtilisateur(Utilisateur $user): ?Panier
    {
        return $this->createQueryBuilder('p')
            ->where('p.utilisateur = :user')
            ->andWhere('p.statut = :statut')
            ->setParameter('user', $user)
            ->setParameter('statut', 'en_cours')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
