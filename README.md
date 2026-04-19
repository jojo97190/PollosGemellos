# PollosGemelos

Application web de restauration en ligne développée avec **Symfony 8**, permettant aux clients de parcourir un catalogue, de personnaliser leurs produits et de passer commande.

---

## Stack technique

| Technologie | Version |
|---|---|
| PHP | ≥ 8.4 |
| Symfony | 8.0 |
| Doctrine ORM | 3.6+ |
| PostgreSQL | 16 |
| Twig | 3.x |
| Asset Mapper / Stimulus | — |
| Docker | Compose v2 |

---

## Fonctionnalités

- **Catalogue** : liste des produits filtrables par catégorie
- **Produit** : page détail avec ingrédients personnalisables (ajouter / enlever)
- **Panier** : gestion en session (ajout, suppression, vidage, calcul du total)
- **Commande** : validation du panier, suivi de statut (en préparation → en livraison → livrée)
- **Authentification** : inscription / connexion par email + mot de passe
- **Espace admin** (réservé `ROLE_ADMIN`) :
  - Dashboard avec statistiques
  - CRUD produits et catégories
  - Gestion des utilisateurs et de leurs rôles
  - Suivi et mise à jour du statut des commandes

---

## Modèle de données

```
Utilisateur ──< Panier ──< PanierProduit ──< PanierProduitIngredient
                   │              │
               Commande        Produit >── Categorie
                                  │
                             Ingredient
```

---

## Installation

### Prérequis

- Docker & Docker Compose
- PHP ≥ 8.4 + Composer

### Démarrage

```bash
# Cloner le projet
git clone <url-du-repo>
cd PollosGemelos

# Démarrer la base de données (PostgreSQL + Mailpit)
docker compose up -d

# Installer les dépendances PHP
composer install

# Créer la base de données et appliquer les migrations
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate

# Charger les données de test (fixtures)
php bin/console doctrine:fixtures:load

# Lancer le serveur de développement
symfony server:start
```

L'application est accessible sur `https://localhost:8000`.

---

## Configuration

Copier `.env` en `.env.local` et ajuster les variables :

```dotenv
DATABASE_URL="postgresql://app:!ChangeMe!@127.0.0.1:5432/pollosgemellos?serverVersion=16&charset=utf8"
```

---

## Données de test (Fixtures)

Les fixtures chargent :

- **7 catégories** : Burgers, Pizzas, Poulet, Accompagnements, Salades, Desserts, Boissons
- **26 ingrédients** (dont des suppléments payants : fromage +1.00€, bacon +1.50€, etc.)
- **~29 produits** avec prix et associations d'ingrédients

---

## Structure du projet

```
src/
├── Controller/     # Contrôleurs (Home, Catalogue, Panier, Commande, Admin…)
├── Entity/         # Entités Doctrine
├── Repository/     # Repositories Doctrine
└── DataFixtures/   # Données de test

templates/          # Templates Twig
config/             # Configuration Symfony (sécurité, doctrine, routage…)
migrations/         # Migrations Doctrine
public/             # Point d'entrée (index.php) + assets publics
assets/             # JS / CSS (Stimulus, Turbo)
```

---

## Rôles utilisateur

| Rôle | Accès |
|---|---|
| `ROLE_USER` (client) | Catalogue, panier, commande |
| `ROLE_EMPLOYE` | — |
| `ROLE_ADMIN` | Espace admin complet (`/admin`) |

# PollosGemellos
