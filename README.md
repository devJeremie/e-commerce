
> This README is available in both **English** and **French**.  
> Ce README est disponible en **anglais** et en **français**.

# 🛒 Shop – Symfony 7 E‑commerce Application

Shop is a complete e‑commerce application built with **Symfony 7.4**, using **MySQL** as the main database and **Stripe** as the payment provider. It supports selling any type of product, from physical items to digital goods, with a full back‑office for order and user management.

---

## 🇬🇧 English Version

### Overview

Shop is a full‑featured e‑commerce platform designed with clean architecture and modern PHP practices.  
It includes user authentication with multiple roles, product management, order workflows, Stripe payment integration, and PDF invoicing, all powered by Symfony 7.4 and MySQL.

### Main Features

- **Product management**
  - CRUD for products (create, edit, list, delete).
  - Any type of product can be sold (no hard restriction on categories).
- **User & roles**
  - Authentication system based on Symfony Security.
  - Three roles:
    - `ROLE_ADMIN` – full administration of the platform.
    - `ROLE_EDITOR` – manages content/products.
    - `ROLE_USER` – regular customer account.
- **Orders & checkout**
  - Full checkout flow with **Stripe** integration (payment tunnel).
  - Orders can be marked as:
    - Paid / unpaid.
    - Delivered / not delivered.
- **Invoices**
  - Invoice generation as **PDF**.
  - Invoice sending by **email** to the customer.
- **Admin area**
  - Dedicated back‑office for:
    - Managing orders (status, payments, shipping).
    - Managing users.
    - Managing products and possibly other entities.
- **Testing & CI/CD**
  - Functional tests (e.g. login, security) using Symfony’s testing tools.
  - Continuous Integration set up with GitHub Actions to automatically run the test suite on each push.

### Tech Stack

- **Backend**
  - PHP 8.x
  - **Symfony 7.4**
  - Doctrine ORM
  - Symfony Security / HTTP Foundation / Console components
- **Database**
  - **MySQL** (development & production)
- **Web server / local dev**
  - **WAMP** stack for local development
- **Payments**
  - **Stripe** payment integration (checkout tunnel)
- **Other**
  - PDF generation for invoices
  - Mail sending for invoices and notifications
  - GitHub Actions for CI (running tests automatically)

### Installation (Local)

> These steps assume a local WAMP environment with PHP, MySQL and Composer installed.

1. **Clone the repository**
2. **Install PHP dependencies**
  composer install
3. **Configure environment**
- Copy the default environment file:
  ```
  cp .env .env.local
  ```
- Edit `.env.local` and set:
  - `DATABASE_URL` to your local MySQL configuration.
  - Stripe API keys.
  - Mailer configuration if needed.

4. **Create database & run migrations**
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
5. **Run the local server**
- Using Symfony CLI:
  ```
  symfony server:start
  ```
- Or configure your **WAMP** virtual host to point to the `public/` directory.

6. **Access the application**
- Front‑office: `http://localhost` (or your virtual host)
- Admin area: the route you’ve defined for the back‑office (e.g. `/admin`)

### Running Tests

Functional tests are configured with PHPUnit.
  php bin/phpunit
These tests cover, among others:

- Login and security (authentication, redirects).
- Some critical application flows (and can be extended to products, orders, etc.).

### Continuous Integration (CI)

GitHub Actions is configured to run the test suite automatically on each push and pull request on configured branches (e.g. `master`, `main`, `develop`).  
This ensures that:

- The application remains stable over time.
- New features do not break existing behavior.

You can see the build status in the **Actions** tab of the repository.

### Author

- **devJeremie**
  ## Licence

This project is licensed under the MIT License.  
Ce projet est distribué sous licence MIT.

--------------
## 🇫🇷 Version Française

### Présentation

**Shop** est une application e‑commerce complète développée avec **Symfony 7.4** et **MySQL**.  
Elle permet de vendre n’importe quel type de produit (physique ou numérique) avec un tunnel de paiement Stripe, une gestion avancée des commandes et un espace d’administration riche.

### Fonctionnalités principales

- **Gestion des produits**
  - CRUD complet sur les produits (création, édition, suppression, listing).
  - Possibilité de vendre tout type de produit (pas de limitation stricte de catégorie).

- **Utilisateurs & rôles**
  - Authentification basée sur le composant Security de Symfony.
  - Trois rôles :
    - `ROLE_ADMIN` – administration complète du site.
    - `ROLE_EDITOR` – gestion du contenu/produits.
    - `ROLE_USER` – compte client classique.

- **Commandes & tunnel de paiement**
  - Tunnel de commande avec intégration **Stripe** (paiement en ligne).
  - Suivi de l’état des commandes :
    - Payée / non payée.
    - Livrée / non livrée.

- **Facturation**
  - Génération de factures au format **PDF**.
  - Envoi de la facture par **email** au client.

- **Espace administrateur**
  - Interface d’administration pour :
    - Gérer les commandes (statuts, paiement, livraison).
    - Gérer les utilisateurs.
    - Gérer les produits, et potentiellement d’autres entités liées à la boutique.

- **Tests & CI/CD**
  - Tests fonctionnels (par exemple sur le login et la sécurité).
  - Intégration continue via GitHub Actions pour lancer automatiquement les tests à chaque push.

### Stack technique

- **Backend**
  - PHP 8.x
  - **Symfony 7.4**
  - Doctrine ORM
  - Composants Security, HTTP, Console, etc.

- **Base de données**
  - **MySQL**

- **Serveur / environnement de dev**
  - **WAMP** utilisé en local

- **Paiement**
  - Intégration de **Stripe** pour le tunnel de paiement.

- **Autres**
  - Génération de factures PDF.
  - Envoi de mails (factures, notifications).
  - GitHub Actions pour l’exécution automatique des tests.

### Installation (Local)

> Ces étapes supposent que tu as déjà un environnement **WAMP**, PHP, MySQL et Composer installés.

1. **Cloner le dépôt**
2. **Installer les dépendances PHP**
   composer install
3. **Configurer l’environnement**
- Copier le fichier d’environnement :
  ```
  cp .env .env.local
  ```
- Éditer `.env.local` et renseigner :
  - `DATABASE_URL` avec ta config MySQL locale.
  - Les clés API Stripe.
  - La config du mailer si nécessaire.

4. **Créer la base & lancer les migrations**
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
5. **Lancer le serveur**
- Avec Symfony CLI :
  ```
  symfony server:start
  ```
- Ou via une virtual host WAMP pointant vers le dossier `public/`.

6. **Accéder à l’application**
- Front‑office : `http://localhost` (ou ton vhost)
- Back‑office : route de l’admin (par ex. `/admin` selon ta config)

### Lancer les tests

Les tests fonctionnels sont exécutés avec PHPUnit :
  php bin/phpunit
Ils couvrent notamment :

- Le login et le comportement de sécurité (redirections, accès).
- Des parties critiques de l’application (extensibles aux produits, commandes, etc.).

### Intégration continue (CI)

Une configuration **GitHub Actions** permet d’exécuter automatiquement la suite de tests à chaque push ou pull request sur les branches configurées (par exemple `master`, `main`, `develop`).  
Cela permet de :

- Détecter les régressions avant le déploiement.
- Garantir une meilleure stabilité de l’application dans le temps.

### Auteur

- **devJeremie**

---------------
  ## Licence

This project is licensed under the MIT License.  
Ce projet est distribué sous licence MIT.
