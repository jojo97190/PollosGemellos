<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Panier::class, inversedBy: 'commande')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panier $panier = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCommande = null;

    #[ORM\Column(length: 30)]
    private string $statut = 'en_preparation';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $montantTotal = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $adresseLivraison = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Utilisateur $utilisateur = null;

    public function __construct()
    {
        $this->dateCommande = new \DateTime();
        $this->statut = 'en_preparation';
    }

    public function getId(): ?int { return $this->id; }

    public function getPanier(): ?Panier { return $this->panier; }
    public function setPanier(?Panier $panier): static { $this->panier = $panier; return $this; }

    public function getDateCommande(): ?\DateTimeInterface { return $this->dateCommande; }
    public function setDateCommande(\DateTimeInterface $dateCommande): static { $this->dateCommande = $dateCommande; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getMontantTotal(): ?string { return $this->montantTotal; }
    public function setMontantTotal(string $montantTotal): static { $this->montantTotal = $montantTotal; return $this; }

    public function getAdresseLivraison(): ?string { return $this->adresseLivraison; }
    public function setAdresseLivraison(?string $adresseLivraison): static { $this->adresseLivraison = $adresseLivraison; return $this; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getStatutLabel(): string
    {
        return match($this->statut) {
            'en_preparation' => '🟡 En préparation',
            'en_livraison'   => '🚚 En livraison',
            'livree'         => '✅ Livrée',
            'annulee'        => '❌ Annulée',
            default          => $this->statut,
        };
    }
}
