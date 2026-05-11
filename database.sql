-- ============================================================
-- Script de création de la base de données - Pollos Gemelos
-- Généré depuis les entités Doctrine (Symfony)
-- Compatible PostgreSQL
-- ============================================================

-- Créer la base (à exécuter en tant que superutilisateur) :
-- CREATE DATABASE pollos_gemelos ENCODING 'UTF8';
-- \c pollos_gemelos

-- ------------------------------------------------------------
-- Table : categorie
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS categorie (
    id          SERIAL       NOT NULL,
    nom         VARCHAR(255) NOT NULL,
    description TEXT         DEFAULT NULL,
    PRIMARY KEY (id)
);

-- ------------------------------------------------------------
-- Table : ingredient
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ingredient (
    id               SERIAL         NOT NULL,
    nom              VARCHAR(255)   NOT NULL,
    prix_supplement  NUMERIC(6, 2)  DEFAULT NULL,
    PRIMARY KEY (id)
);

-- ------------------------------------------------------------
-- Table : utilisateur
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS utilisateur (
    id          SERIAL       NOT NULL,
    email       VARCHAR(180) NOT NULL,
    roles       VARCHAR(255) NOT NULL DEFAULT 'ROLE_USER',
    password    VARCHAR(255) NOT NULL,
    nom         VARCHAR(255) NOT NULL,
    prenom      VARCHAR(255) NOT NULL,
    telephone   VARCHAR(20)  DEFAULT NULL,
    role_metier VARCHAR(20)  NOT NULL DEFAULT 'client',
    PRIMARY KEY (id),
    CONSTRAINT uniq_email UNIQUE (email)
);

-- ------------------------------------------------------------
-- Table : produit
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS produit (
    id           SERIAL         NOT NULL,
    categorie_id INT            NOT NULL,
    nom          VARCHAR(255)   NOT NULL,
    prix         NUMERIC(8, 2)  NOT NULL,
    description  TEXT           DEFAULT NULL,
    image        VARCHAR(255)   DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_produit_categorie FOREIGN KEY (categorie_id)
        REFERENCES categorie (id)
);

-- ------------------------------------------------------------
-- Table de jointure : produit_ingredient  (ManyToMany)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS produit_ingredient (
    produit_id    INT NOT NULL,
    ingredient_id INT NOT NULL,
    PRIMARY KEY (produit_id, ingredient_id),
    CONSTRAINT fk_pi_produit    FOREIGN KEY (produit_id)    REFERENCES produit    (id) ON DELETE CASCADE,
    CONSTRAINT fk_pi_ingredient FOREIGN KEY (ingredient_id) REFERENCES ingredient (id) ON DELETE CASCADE
);

-- ------------------------------------------------------------
-- Table : panier
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS panier (
    id             SERIAL          NOT NULL,
    utilisateur_id INT             DEFAULT NULL,
    date_creation  TIMESTAMP       NOT NULL,
    statut         VARCHAR(20)     NOT NULL DEFAULT 'en_cours',
    montant_total  NUMERIC(10, 2)  DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT fk_panier_utilisateur FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateur (id)
);

-- ------------------------------------------------------------
-- Table : panier_produit
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS panier_produit (
    id         SERIAL NOT NULL,
    panier_id  INT    NOT NULL,
    produit_id INT    NOT NULL,
    quantite   INT    NOT NULL DEFAULT 1,
    PRIMARY KEY (id),
    CONSTRAINT fk_pp_panier  FOREIGN KEY (panier_id)  REFERENCES panier  (id) ON DELETE CASCADE,
    CONSTRAINT fk_pp_produit FOREIGN KEY (produit_id) REFERENCES produit (id)
);

-- ------------------------------------------------------------
-- Table : panier_produit_ingredient
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS panier_produit_ingredient (
    id                INT         NOT NULL GENERATED ALWAYS AS IDENTITY,
    panier_produit_id INT         NOT NULL,
    ingredient_id     INT         NOT NULL,
    action            VARCHAR(10) NOT NULL DEFAULT 'ajouter',  -- 'ajouter' ou 'enlever'
    PRIMARY KEY (id),
    CONSTRAINT fk_ppi_panier_produit FOREIGN KEY (panier_produit_id)
        REFERENCES panier_produit (id) ON DELETE CASCADE,
    CONSTRAINT fk_ppi_ingredient     FOREIGN KEY (ingredient_id)
        REFERENCES ingredient (id)
);

-- ------------------------------------------------------------
-- Table : commande
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS commande (
    id                SERIAL          NOT NULL,
    panier_id         INT             NOT NULL,
    utilisateur_id    INT             DEFAULT NULL,
    date_commande     TIMESTAMP       NOT NULL,
    statut            VARCHAR(30)     NOT NULL DEFAULT 'en_preparation',
    montant_total     NUMERIC(10, 2)  NOT NULL,
    adresse_livraison TEXT            DEFAULT NULL,
    PRIMARY KEY (id),
    CONSTRAINT uniq_commande_panier    UNIQUE (panier_id),
    CONSTRAINT fk_commande_panier      FOREIGN KEY (panier_id)
        REFERENCES panier (id),
    CONSTRAINT fk_commande_utilisateur FOREIGN KEY (utilisateur_id)
        REFERENCES utilisateur (id)
);

-- ============================================================
-- Données de référence (exemples)
-- ============================================================

-- Catégories
INSERT INTO categorie (nom, description) VALUES
    ('Poulets', 'Nos spécialités de poulets grillés et rôtis'),
    ('Burgers', 'Burgers maison préparés à la commande'),
    ('Accompagnements', 'Frites, salades et autres accompagnements'),
    ('Boissons', 'Boissons fraîches et chaudes'),
    ('Desserts', 'Desserts gourmands');

-- Ingrédients
INSERT INTO ingredient (nom, prix_supplement) VALUES
    ('Salade', NULL),
    ('Tomate', NULL),
    ('Oignon', NULL),
    ('Fromage', 0.50),
    ('Bacon', 1.00),
    ('Sauce piquante', 0.30),
    ('Sauce barbecue', 0.30),
    ('Avocat', 1.20),
    ('Jalapeño', 0.50),
    ('Cornichons', NULL);

-- Comptes de démonstration (mot de passe : à hacher via Symfony avant import)
-- Le champ password doit contenir un hash bcrypt/argon2i généré par Symfony.
INSERT INTO utilisateur (email, roles, password, nom, prenom, telephone, role_metier) VALUES
    ('admin@pollosgemelos.fr',   'ROLE_ADMIN',   '$2y$13$PLACEHOLDER_HASH', 'Admin',  'Pollos', NULL,         'admin'),
    ('employe@pollosgemelos.fr', 'ROLE_EMPLOYE', '$2y$13$PLACEHOLDER_HASH', 'Dupont', 'Jean',   '0612345678', 'employe');
