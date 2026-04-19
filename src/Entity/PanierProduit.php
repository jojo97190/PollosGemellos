<?php

namespace App\Entity;

use App\Repository\PanierProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierProduitRepository::class)]
class PanierProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Panier::class, inversedBy: 'panierProduits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Panier $panier = null;

    #[ORM\ManyToOne(targetEntity: Produit::class, inversedBy: 'panierProduits')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Produit $produit = null;

    #[ORM\Column]
    private int $quantite = 1;

    #[ORM\OneToMany(targetEntity: PanierProduitIngredient::class, mappedBy: 'panierProduit', cascade: ['persist', 'remove'])]
    private Collection $modifications;

    public function __construct()
    {
        $this->modifications = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getPanier(): ?Panier { return $this->panier; }
    public function setPanier(?Panier $panier): static { $this->panier = $panier; return $this; }

    public function getProduit(): ?Produit { return $this->produit; }
    public function setProduit(?Produit $produit): static { $this->produit = $produit; return $this; }

    public function getQuantite(): int { return $this->quantite; }
    public function setQuantite(int $quantite): static { $this->quantite = $quantite; return $this; }

    public function getModifications(): Collection { return $this->modifications; }

    public function addModification(PanierProduitIngredient $modification): static
    {
        if (!$this->modifications->contains($modification)) {
            $this->modifications->add($modification);
            $modification->setPanierProduit($this);
        }
        return $this;
    }

    public function removeModification(PanierProduitIngredient $modification): static
    {
        if ($this->modifications->removeElement($modification)) {
            if ($modification->getPanierProduit() === $this) {
                $modification->setPanierProduit(null);
            }
        }
        return $this;
    }

    public function calculerSousTotal(): float
    {
        $prix = (float) $this->produit->getPrix();
        foreach ($this->modifications as $modif) {
            if ($modif->getAction() === 'ajouter' && $modif->getIngredient()->getPrixSupplement()) {
                $prix += (float) $modif->getIngredient()->getPrixSupplement();
            }
        }
        return $prix * $this->quantite;
    }
}
