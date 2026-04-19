<?php

namespace App\Entity;

use App\Repository\PanierProduitIngredientRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierProduitIngredientRepository::class)]
class PanierProduitIngredient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: PanierProduit::class, inversedBy: 'modifications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?PanierProduit $panierProduit = null;

    #[ORM\ManyToOne(targetEntity: Ingredient::class)]
    #[ORM\JoinColumn(nullable: false)]
    private ?Ingredient $ingredient = null;

    /** 'ajouter' ou 'enlever' */
    #[ORM\Column(length: 10)]
    private string $action = 'ajouter';

    public function getId(): ?int { return $this->id; }

    public function getPanierProduit(): ?PanierProduit { return $this->panierProduit; }
    public function setPanierProduit(?PanierProduit $panierProduit): static { $this->panierProduit = $panierProduit; return $this; }

    public function getIngredient(): ?Ingredient { return $this->ingredient; }
    public function setIngredient(?Ingredient $ingredient): static { $this->ingredient = $ingredient; return $this; }

    public function getAction(): string { return $this->action; }
    public function setAction(string $action): static { $this->action = $action; return $this; }
}
