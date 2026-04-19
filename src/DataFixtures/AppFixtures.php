<?php

namespace App\DataFixtures;

use App\Entity\Categorie;
use App\Entity\Ingredient;
use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // ===== CATÉGORIES =====
        $categories = [];
        foreach ([
            ['Burgers',          'Nos burgers faits maison avec des ingrédients frais'],
            ['Pizzas',           'Pâte fine croustillante et garnitures généreuses'],
            ['Poulet',           'Poulet frit croustillant ou grillé, marinés à notre façon'],
            ['Accompagnements',  'Frites, nuggets et autres à-côtés savoureux'],
            ['Salades',          'Fraîches et légères, idéales pour les jours d\'été'],
            ['Desserts',         'Douceurs maison pour finir en beauté'],
            ['Boissons',         'Fraîches et désaltérantes'],
        ] as [$nom, $desc]) {
            $cat = new Categorie();
            $cat->setNom($nom)->setDescription($desc);
            $manager->persist($cat);
            $categories[$nom] = $cat;
        }

        // ===== INGRÉDIENTS =====
        $ingredients = [];
        $defs = [
            // Ingrédients de base
            ['Pain brioché',            null],
            ['Steak haché 150g',        null],
            ['Salade',                  null],
            ['Tomate',                  null],
            ['Cornichon',               null],
            ['Oignon',                  null],
            ['Sauce maison',            null],
            ['Pâte à pizza',            null],
            ['Sauce tomate',            null],
            ['Mozzarella',              null],
            ['Poulet grillé 150g',      null],
            ['Poulet frit 150g',        null],
            ['Croûtons',                null],
            ['Parmesan',                null],
            ['Marinade épicée',         null],
            ['Panure croustillante',    null],
            ['Pommes de terre',         null],
            // Suppléments payants
            ['Fromage extra',           '1.00'],
            ['Bacon croustillant',      '1.50'],
            ['Œuf',                    '0.80'],
            ['Avocat',                  '1.20'],
            ['Champignons',             '0.90'],
            ['Jalapeños',               '0.70'],
            ['Sauce buffalo',           '0.60'],
            ['Double portion frites',   '1.50'],
        ];
        foreach ($defs as [$nom, $prix]) {
            $ing = new Ingredient();
            $ing->setNom($nom)->setPrixSupplement($prix);
            $manager->persist($ing);
            $ingredients[$nom] = $ing;
        }

        // ===== PRODUITS =====
        $produits = [
            // --- BURGERS ---
            [
                'categorie' => 'Burgers',
                'nom'       => 'Burger Classic',
                'prix'      => '8.90',
                'desc'      => 'Pain brioché, steak haché 150g, salade, tomate, cornichon, sauce maison.',
                'ings'      => ['Pain brioché','Steak haché 150g','Salade','Tomate','Cornichon','Sauce maison','Fromage extra','Bacon croustillant','Œuf'],
            ],
            [
                'categorie' => 'Burgers',
                'nom'       => 'Burger Double',
                'prix'      => '11.90',
                'desc'      => 'Deux steaks hachés, double fromage, sauce BBQ et oignons confits.',
                'ings'      => ['Pain brioché','Steak haché 150g','Oignon','Sauce maison','Fromage extra','Bacon croustillant'],
            ],
            [
                'categorie' => 'Burgers',
                'nom'       => 'Burger Poulet Grillé',
                'prix'      => '9.90',
                'desc'      => 'Filet de poulet grillé mariné, salade croquante, tomate et sauce yaourt.',
                'ings'      => ['Pain brioché','Poulet grillé 150g','Salade','Tomate','Sauce maison','Fromage extra','Avocat'],
            ],
            [
                'categorie' => 'Burgers',
                'nom'       => 'Burger Poulet Frit',
                'prix'      => '10.50',
                'desc'      => 'Filet de poulet frit ultra-croustillant, sauce buffalo, oignons, cornichons.',
                'ings'      => ['Pain brioché','Poulet frit 150g','Oignon','Cornichon','Sauce maison','Sauce buffalo','Fromage extra'],
            ],
            [
                'categorie' => 'Burgers',
                'nom'       => 'Burger Veggie',
                'prix'      => '9.50',
                'desc'      => 'Steak de légumes, avocat frais, salade, tomate, sauce yaourt citronnée.',
                'ings'      => ['Pain brioché','Salade','Tomate','Sauce maison','Avocat','Champignons'],
            ],
            // --- PIZZAS ---
            [
                'categorie' => 'Pizzas',
                'nom'       => 'Pizza Margherita',
                'prix'      => '10.50',
                'desc'      => 'Sauce tomate fraîche, mozzarella fior di latte, basilic.',
                'ings'      => ['Pâte à pizza','Sauce tomate','Mozzarella','Fromage extra','Champignons','Jalapeños'],
            ],
            [
                'categorie' => 'Pizzas',
                'nom'       => 'Pizza Poulet BBQ',
                'prix'      => '12.50',
                'desc'      => 'Sauce BBQ, poulet grillé, oignons rouges, mozzarella.',
                'ings'      => ['Pâte à pizza','Sauce tomate','Mozzarella','Poulet grillé 150g','Oignon','Bacon croustillant','Jalapeños'],
            ],
            [
                'categorie' => 'Pizzas',
                'nom'       => 'Pizza Pepperoni',
                'prix'      => '11.90',
                'desc'      => 'Sauce tomate, mozzarella généreuse, pepperoni croustillantes.',
                'ings'      => ['Pâte à pizza','Sauce tomate','Mozzarella','Fromage extra','Bacon croustillant','Jalapeños'],
            ],
            [
                'categorie' => 'Pizzas',
                'nom'       => 'Pizza 4 Fromages',
                'prix'      => '13.00',
                'desc'      => 'Mozzarella, gorgonzola, parmesan et fromage de chèvre fondus ensemble.',
                'ings'      => ['Pâte à pizza','Sauce tomate','Mozzarella','Parmesan','Fromage extra'],
            ],
            // --- POULET ---
            [
                'categorie' => 'Poulet',
                'nom'       => 'Poulet Frit (4 pièces)',
                'prix'      => '9.90',
                'desc'      => 'Morceaux de poulet marinés 24h, panure croustillante, accompagnés de sauce.',
                'ings'      => ['Poulet frit 150g','Panure croustillante','Marinade épicée','Sauce buffalo','Jalapeños'],
            ],
            [
                'categorie' => 'Poulet',
                'nom'       => 'Poulet Frit Épicé (4 pièces)',
                'prix'      => '10.50',
                'desc'      => 'Version pimentée de notre poulet frit signature, pour les amateurs de sensations fortes.',
                'ings'      => ['Poulet frit 150g','Panure croustillante','Marinade épicée','Sauce buffalo','Jalapeños'],
            ],
            [
                'categorie' => 'Poulet',
                'nom'       => 'Poulet Grillé (filet)',
                'prix'      => '10.90',
                'desc'      => 'Filet de poulet grillé à la plancha, mariné aux herbes, servi avec salade et tomates.',
                'ings'      => ['Poulet grillé 150g','Marinade épicée','Salade','Tomate','Sauce maison'],
            ],
            [
                'categorie' => 'Poulet',
                'nom'       => 'Bucket Poulet Frit (8 pièces)',
                'prix'      => '18.90',
                'desc'      => 'Le grand format ! 8 pièces de poulet frit croustillant pour partager en famille.',
                'ings'      => ['Poulet frit 150g','Panure croustillante','Marinade épicée','Sauce buffalo'],
            ],
            // --- ACCOMPAGNEMENTS ---
            [
                'categorie' => 'Accompagnements',
                'nom'       => 'Frites Maison',
                'prix'      => '3.50',
                'desc'      => 'Frites dorées coupées à la main, assaisonnées à notre mélange secret.',
                'ings'      => ['Pommes de terre','Double portion frites'],
            ],
            [
                'categorie' => 'Accompagnements',
                'nom'       => 'Frites Épicées',
                'prix'      => '3.90',
                'desc'      => 'Frites maison relevées d\'un mélange de paprika, piment et ail.',
                'ings'      => ['Pommes de terre','Jalapeños','Double portion frites'],
            ],
            [
                'categorie' => 'Accompagnements',
                'nom'       => 'Potatoes Wedges',
                'prix'      => '4.20',
                'desc'      => 'Quartiers de pommes de terre au four, croustillants, avec sauce sour cream.',
                'ings'      => ['Pommes de terre','Sauce maison','Double portion frites'],
            ],
            [
                'categorie' => 'Accompagnements',
                'nom'       => 'Nuggets de Poulet (6 pièces)',
                'prix'      => '5.50',
                'desc'      => 'Nuggets maison croustillants, panés à la chapelure panko.',
                'ings'      => ['Poulet frit 150g','Panure croustillante','Sauce buffalo'],
            ],
            // --- SALADES ---
            [
                'categorie' => 'Salades',
                'nom'       => 'Salade César Poulet',
                'prix'      => '8.50',
                'desc'      => 'Poulet grillé, croûtons maison, parmesan, sauce césar.',
                'ings'      => ['Poulet grillé 150g','Croûtons','Parmesan','Salade','Avocat','Œuf'],
            ],
            [
                'categorie' => 'Salades',
                'nom'       => 'Salade Caprese',
                'prix'      => '7.50',
                'desc'      => 'Mozzarella fraîche, tomates, basilic, huile d\'olive.',
                'ings'      => ['Mozzarella','Tomate','Salade','Fromage extra'],
            ],
            // --- DESSERTS ---
            [
                'categorie' => 'Desserts',
                'nom'       => 'Fondant au Chocolat',
                'prix'      => '5.50',
                'desc'      => 'Fondant chaud coulant au chocolat noir, boule de glace vanille.',
                'ings'      => [],
            ],
            [
                'categorie' => 'Desserts',
                'nom'       => 'Tiramisu Maison',
                'prix'      => '5.00',
                'desc'      => 'Tiramisu traditionnel, mascarpone, biscuits cuillère, café.',
                'ings'      => [],
            ],
            // --- BOISSONS ---
            [
                'categorie' => 'Boissons',
                'nom'       => 'Limonade Maison',
                'prix'      => '3.50',
                'desc'      => 'Limonade artisanale à la menthe fraîche.',
                'ings'      => [],
            ],
            [
                'categorie' => 'Boissons',
                'nom'       => 'Ice Tea Pêche',
                'prix'      => '3.00',
                'desc'      => 'Thé glacé maison à la pêche, légèrement sucré.',
                'ings'      => [],
            ],
            [
                'categorie' => 'Boissons',
                'nom'       => 'Milkshake Vanille',
                'prix'      => '4.50',
                'desc'      => 'Milkshake onctueux à la vanille de Madagascar.',
                'ings'      => [],
            ],
        ];

        foreach ($produits as $data) {
            $p = new Produit();
            $p->setNom($data['nom'])
              ->setPrix($data['prix'])
              ->setDescription($data['desc'])
              ->setCategorie($categories[$data['categorie']]);
            foreach ($data['ings'] as $ingNom) {
                if (isset($ingredients[$ingNom])) {
                    $p->addIngredient($ingredients[$ingNom]);
                }
            }
            $manager->persist($p);
        }

        $manager->flush();
    }
}
