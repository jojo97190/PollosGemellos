# PollosGemelos

Application web de restauration en ligne développée avec **Symfony 8**, permettant aux clients de parcourir un catalogue, de personnaliser leurs produits et de passer commande en ligne. Les employés suivent et mettent à jour les commandes, et les administrateurs disposent d'un back-office complet.

---

## Stack technique

| Technologie | Version |
|---|---|
| PHP | ≥ 8.4 |
| Symfony | 8.0 |
| Doctrine ORM | 3.6+ |
| PostgreSQL | 16 |
| Twig | 3.x |
| Asset Mapper / Stimulus / Turbo | — |
| Docker | Compose v2 |

---

## Fonctionnalités

### Côté client (`ROLE_USER`)
- **Catalogue** : liste des produits, filtrables par catégorie
- **Produit** : page détail avec ingrédients personnalisables (ajouter / enlever des suppléments)
- **Panier** : ajout, suppression, vidage, calcul automatique du total — persisté en session et lié au compte
- **Commande** : validation du panier avec adresse de livraison, confirmation immédiate
- **Mes commandes** : historique des commandes avec suivi visuel de progression (Préparation → Livraison → Livrée)
- **Compte** : inscription par email + mot de passe, connexion / déconnexion

### Côté employé (`ROLE_EMPLOYE`)
- **Suivi des commandes** : tableau de toutes les commandes avec compteurs par statut
- **Mise à jour du statut** : passage de `en_preparation` → `en_livraison` → `livree` (ou `annulee`) via un formulaire sécurisé CSRF

### Côté admin (`ROLE_ADMIN`)
- **Dashboard** : statistiques globales (produits, catégories, utilisateurs, commandes) + 5 dernières commandes
- **CRUD Produits** : création, modification, suppression, gestion des ingrédients associés et upload d'image
- **CRUD Catégories** : gestion des catégories de produits
- **CRUD Ingrédients** : gestion des ingrédients et de leurs prix de supplément
- **Gestion des utilisateurs** : liste, modification du rôle métier (`client` / `employe` / `admin`), suppression
- **Suivi des commandes** : visualisation et mise à jour du statut de toutes les commandes

---

## Modèle de données

```
Utilisateur ──< Panier ──< PanierProduit ──< PanierProduitIngredient
     │             │              │                      │
     └──< Commande │            Produit >── Categorie  Ingredient
                   │              │
                   └──────────────┘
                        (OneToOne)
```

Chaque `Commande` est directement liée à un `Utilisateur` (champ `utilisateur_id`) en plus du `Panier` associé.

---

## Rôles utilisateur

| Rôle | Héritage | Routes accessibles |
|---|---|---|
| `ROLE_USER` | — | `/`, `/catalogue`, `/produit`, `/panier`, `/commande`, `/commande/mes-commandes` |
| `ROLE_EMPLOYE` | `ROLE_USER` | + `/employe/commandes` |
| `ROLE_ADMIN` | `ROLE_EMPLOYE` | + `/admin/*` |

Le rôle effectif est déterminé par le champ `roleMetier` (`client` / `employe` / `admin`) de l'entité `Utilisateur`.

---

## Installation

### Prérequis

- Docker & Docker Compose
- PHP ≥ 8.4 + Composer
- [Symfony CLI](https://symfony.com/download) (optionnel, pour `symfony server:start`)

### Démarrage

```bash
# 1. Cloner le projet
git clone <url-du-repo>
cd PollosGemelos

# 2. Copier et configurer les variables d'environnement
cp .env .env.local
# Éditer .env.local si nécessaire (voir section Configuration)

# 3. Démarrer la base de données PostgreSQL via Docker
docker compose up -d

# 4. Installer les dépendances PHP
composer install

# 5. Créer la base de données et appliquer les migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# 6. Charger les données de test
php bin/console doctrine:fixtures:load

# 7. Lancer le serveur de développement
symfony server:start
# ou : php -S localhost:8000 -t public/
```

L'application est accessible sur `https://localhost:8000`.

---

## Configuration

Copier `.env` en `.env.local` et ajuster les variables :

```dotenv
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
```

Le fichier `compose.yaml` démarre un conteneur PostgreSQL 16 avec les valeurs par défaut :
- Utilisateur : `app`
- Mot de passe : `!ChangeMe!`
- Base : `app`
- Port : `5432`

---

## Données de test (Fixtures)

Les fixtures (`php bin/console doctrine:fixtures:load`) chargent :

- **7 catégories** : Burgers, Pizzas, Poulet, Accompagnements, Salades, Desserts, Boissons
- **26 ingrédients** dont des suppléments payants (ex. Fromage +1.00 €, Bacon +1.50 €, Avocat +0.80 €…)
- **~29 produits** avec prix, description et associations d'ingrédients

> ⚠️ Les fixtures ne créent pas de comptes utilisateur. Créer les comptes via `/inscription` ou directement en BDD.

**Comptes de test suggérés :**

| Email | Rôle | `roleMetier` |
|---|---|---|
| `admin@example.com` | `ROLE_ADMIN` | `admin` |
| `employe@example.com` | `ROLE_EMPLOYE` | `employe` |
| `client@example.com` | `ROLE_USER` | `client` |

---

## Structure du projet

```
src/
├── Controller/
│   ├── AdminController.php       # Back-office complet (CRUD produits, catégories, users, commandes)
│   ├── CatalogueController.php   # Liste des produits par catégorie
│   ├── CommandeController.php    # Passage de commande, confirmation, mes commandes
│   ├── EmployeController.php     # Suivi et mise à jour des commandes (employé)
│   ├── HomeController.php        # Page d'accueil
│   ├── LoginController.php       # Connexion / déconnexion
│   ├── PanierController.php      # Gestion du panier (ajout, suppression, vidage)
│   ├── ProduitController.php     # Page détail produit
│   └── RegistrationController.php
├── Entity/
│   ├── Categorie.php
│   ├── Commande.php              # Commande (liée à Panier + Utilisateur)
│   ├── Ingredient.php
│   ├── Panier.php                # Panier (statut: en_cours / valide)
│   ├── PanierProduit.php         # Ligne de panier (produit × quantité)
│   ├── PanierProduitIngredient.php  # Modification d'ingrédient sur une ligne
│   ├── Produit.php
│   └── Utilisateur.php           # UserInterface, rôle via roleMetier
├── Repository/                   # Repositories Doctrine avec méthodes métier
└── DataFixtures/
    └── AppFixtures.php

templates/
├── base.html.twig                # Layout principal (navbar adaptive au rôle)
├── admin/                        # Templates back-office
├── catalogue/                    # Liste des produits
├── commande/                     # Confirmation, mes commandes
├── employe/                      # Suivi commandes employé
├── home/                         # Page d'accueil
├── panier/                       # Panier
├── produit/                      # Page détail produit
└── security/                     # Connexion / inscription

config/
├── packages/
│   ├── security.yaml             # Firewalls, role_hierarchy, access_control
│   └── doctrine.yaml             # Configuration Doctrine / PostgreSQL
└── routes.yaml

migrations/                       # Migrations Doctrine (versionnées)
public/                           # Point d'entrée index.php + images produits
assets/                           # JS (Stimulus controllers) + CSS
```

---

## Statuts de commande

| Statut | Label affiché |
|---|---|
| `en_preparation` | 🟡 En préparation |
| `en_livraison` | 🚚 En livraison |
| `livree` | ✅ Livrée |
| `annulee` | ❌ Annulée |

---

## Sécurité

- Mots de passe hachés avec l'algorithme `auto` de Symfony (bcrypt/argon2)
- Protection CSRF sur tous les formulaires sensibles (panier, commandes, admin)
- Accès aux routes `/employe/*` et `/admin/*` contrôlé via `access_control` dans `security.yaml`
- Attribut `#[IsGranted]` utilisé au niveau des contrôleurs en complément
