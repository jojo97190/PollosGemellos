<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateCreation = null;

    #[ORM\Column(length: 20)]
    private string $statut = 'en_cours';

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    private ?string $montantTotal = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: 'paniers')]
    private ?Utilisateur $utilisateur = null;

    #[ORM\OneToMany(targetEntity: PanierProduit::class, mappedBy: 'panier', cascade: ['persist', 'remove'])]
    private Collection $panierProduits;

    #[ORM\OneToOne(targetEntity: Commande::class, mappedBy: 'panier')]
    private ?Commande $commande = null;

    public function __construct()
    {
        $this->panierProduits = new ArrayCollection();
        $this->dateCreation = new \DateTime();
        $this->statut = 'en_cours';
        $this->montantTotal = '0.00';
    }

    public function getId(): ?int { return $this->id; }

    public function getDateCreation(): ?\DateTimeInterface { return $this->dateCreation; }
    public function setDateCreation(\DateTimeInterface $dateCreation): static { $this->dateCreation = $dateCreation; return $this; }

    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getMontantTotal(): ?string { return $this->montantTotal; }
    public function setMontantTotal(?string $montantTotal): static { $this->montantTotal = $montantTotal; return $this; }

    public function getUtilisateur(): ?Utilisateur { return $this->utilisateur; }
    public function setUtilisateur(?Utilisateur $utilisateur): static { $this->utilisateur = $utilisateur; return $this; }

    public function getPanierProduits(): Collection { return $this->panierProduits; }

    public function addPanierProduit(PanierProduit $panierProduit): static
    {
        if (!$this->panierProduits->contains($panierProduit)) {
            $this->panierProduits->add($panierProduit);
            $panierProduit->setPanier($this);
        }
        return $this;
    }

    public function removePanierProduit(PanierProduit $panierProduit): static
    {
        if ($this->panierProduits->removeElement($panierProduit)) {
            if ($panierProduit->getPanier() === $this) {
                $panierProduit->setPanier(null);
            }
        }
        return $this;
    }

    public function getCommande(): ?Commande { return $this->commande; }

    public function calculerMontantTotal(): void
    {
        $total = 0;
        foreach ($this->panierProduits as $ligne) {
            $total += $ligne->calculerSousTotal();
        }
        $this->montantTotal = (string) $total;
    }
}
