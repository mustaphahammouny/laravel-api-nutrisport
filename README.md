# NutriSport API - Partie Laravel

API Laravel multi-sites pour NutriSport (catalogue produits, panier, commandes, backoffice admin).

## Sommaire
- [1. Stack et prerequis](#1-stack-et-prerequis)
- [2. Installation](#2-installation)
- [3. Configuration locale (multi-sites)](#3-configuration-locale-multi-sites)
- [4. Utilisation rapide](#4-utilisation-rapide)
- [5. Endpoints API](#5-endpoints-api)
- [6. Tests](#6-tests)

## 1. Stack et prerequis
- PHP `>= 8.2`
- Composer
- MySQL/MariaDB
- Node.js + npm (utile pour les assets, optionnel pour l'API pure)
- Laravel 12
- Auth JWT via `tymon/jwt-auth`

## 2. Installation

### 2.1 Cloner et installer les dependances
```bash
composer install
```

### 2.2 Configurer l'environnement
```bash
copy .env.example .env
```

Puis verifier au minimum:
- `APP_URL`
- `DB_CONNECTION`, `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- `JWT_SECRET` (genere a l'etape suivante)

### 2.3 Generer les cles et initialiser la base
```bash
php artisan key:generate
php artisan jwt:secret
php artisan migrate --seed
```

### 2.4 Lancer l'application
Option simple:
```bash
php artisan serve
```

Option dev complete:
```bash
composer run dev
```

`composer run dev` lance en parallele:
- serveur Laravel
- worker queue
- Vite

## 3. Configuration locale (multi-sites)

Les routes front utilisent un middleware `site` base sur le `Host` HTTP.  
Le domaine doit exister dans la table `sites`.

Avec le seeding, les domaines par defaut sont:
- `fr.localhost`
- `it.localhost`
- `be.localhost`

Les clients seedes:
- `customer-fr@example.com` / `password`
- `customer-it@example.com` / `password`
- `customer-be@example.com` / `password`

Compte admin seede:
- `admin@example.com` / `password`

Important:
- Les endpoints front (`/api/...` hors `/api/backoffice/...`) doivent etre appeles avec un host de site valide.
- Les endpoints backoffice ne dependent pas du host de site.

## 4. Utilisation rapide

### 4.1 Headers importants
- `Authorization: Bearer <token>` pour les routes protegees.
- `X-Cart-Token` pour le panier (renvoye automatiquement dans les reponses panier).
- `Accept`:
  - `application/json` ou `application/xml` pour `GET /api/produits` et `GET /api/produits/{id}`
  - JSON pour le reste des endpoints.

### 4.2 Flux front typique (client)
1. Login client:
```bash
curl -X POST "http://fr.localhost/api/auth/login" ^
  -H "Accept: application/json" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"customer-fr@example.com\",\"password\":\"password\"}"
```

2. Recuperer les produits:
```bash
curl "http://fr.localhost/api/produits" ^
  -H "Accept: application/json"
```

3. Ajouter un produit au panier:
```bash
curl -X POST "http://fr.localhost/api/panier/items/1" ^
  -H "Accept: application/json" ^
  -H "Content-Type: application/json" ^
  -H "X-Cart-Token: MON_TOKEN_PANIER" ^
  -d "{\"quantity\":2}"
```

4. Creer une commande:
```bash
curl -X POST "http://fr.localhost/api/commandes" ^
  -H "Accept: application/json" ^
  -H "Content-Type: application/json" ^
  -H "Authorization: Bearer MON_JWT" ^
  -H "X-Cart-Token: MON_TOKEN_PANIER" ^
  -d "{\"payment_method\":\"bank_transfer\",\"shipping_full_name\":\"Client FR\",\"shipping_full_address\":\"10 rue Exemple\",\"shipping_city\":\"Paris\",\"shipping_country\":\"France\"}"
```

## 5. Endpoints API

Base URL:
- Front: `http://{site-domain}/api`
- Backoffice: `http://{host}/api/backoffice`

Pagination:
- Les endpoints listes pagines acceptent `?page=<n>`.

### 5.1 Front - Auth

| Methode | URI | Auth | Description | Body |
|---|---|---|---|---|
| POST | `/api/auth/login` | Non | Login client | `email`, `password` |
| GET | `/api/auth/me` | `front-api` | Profil du client connecte | - |
| POST | `/api/auth/refresh` | `front-api` | Refresh du token JWT | - |
| POST | `/api/auth/logout` | `front-api` | Logout | - |

### 5.2 Front - Produits

| Methode | URI | Auth | Description | Body |
|---|---|---|---|---|
| GET | `/api/produits` | Non | Liste des produits du site courant | - |
| GET | `/api/produits/{product}` | Non | Detail produit | - |

Notes:
- `Accept` obligatoire: `application/json` ou `application/xml`.
- Le prix retourne est celui du site courant.

### 5.3 Front - Panier

| Methode | URI | Auth | Description | Body |
|---|---|---|---|---|
| GET | `/api/panier` | Non | Afficher le panier courant | - |
| PUT | `/api/panier` | Non | Mettre a jour des quantites | `items[]` (`product_id`, `quantity`) |
| DELETE | `/api/panier` | Non | Vider le panier | - |
| POST | `/api/panier/items/{product}` | Non | Ajouter un produit | `quantity` (optionnel, defaut `1`) |
| DELETE | `/api/panier/items/{product}` | Non | Retirer un produit | - |

Notes:
- Header panier: `X-Cart-Token`.
- Si absent, il est cree automatiquement et renvoye dans la reponse.

### 5.4 Front - Profil et mot de passe

| Methode | URI | Auth | Description | Body |
|---|---|---|---|---|
| GET | `/api/profile` | `front-api` | Voir profil client | - |
| PATCH | `/api/profile` | `front-api` | Modifier profil | `name`, `email` |
| PUT | `/api/password` | `front-api` | Modifier mot de passe | `current_password`, `password`, `password_confirmation` |

### 5.5 Front - Commandes

| Methode | URI | Auth | Description | Body |
|---|---|---|---|---|
| GET | `/api/commandes` | `front-api` | Liste des commandes du client | - |
| POST | `/api/commandes` | `front-api` + `X-Cart-Token` | Creer une commande depuis le panier | `payment_method`, `shipping_full_name`, `shipping_full_address`, `shipping_city`, `shipping_country` |
| GET | `/api/commandes/{order}` | `front-api` | Detail commande (si proprietaire) | - |

Valeurs `payment_method` supportees:
- `bank_transfer`

### 5.6 Backoffice - Auth

| Methode | URI | Auth | Description | Body |
|---|---|---|---|---|
| POST | `/api/backoffice/auth/login` | Non | Login admin/user backoffice | `email`, `password` |
| GET | `/api/backoffice/auth/me` | `back-api` | Profil utilisateur backoffice | - |
| POST | `/api/backoffice/auth/refresh` | `back-api` | Refresh du token JWT | - |
| POST | `/api/backoffice/auth/logout` | `back-api` | Logout | - |

### 5.7 Backoffice - Profil et mot de passe

| Methode | URI | Auth | Description | Body |
|---|---|---|---|---|
| GET | `/api/backoffice/profile` | `back-api` | Voir profil | - |
| PATCH | `/api/backoffice/profile` | `back-api` | Modifier profil | `name`, `email` |
| PUT | `/api/backoffice/password` | `back-api` | Modifier mot de passe | `current_password`, `password`, `password_confirmation` |

### 5.8 Backoffice - Commandes

| Methode | URI | Auth | Description | Body |
|---|---|---|---|---|
| GET | `/api/backoffice/commandes` | `back-api` + droit `can_view_orders` | Liste des commandes (5 derniers jours) | - |
| GET | `/api/backoffice/commandes/{order}` | `back-api` + droit `can_view_orders` | Detail commande | - |

### 5.9 Backoffice - Produits

| Methode | URI | Auth | Description | Body |
|---|---|---|---|---|
| GET | `/api/backoffice/produits` | `back-api` | Liste produits | - |
| POST | `/api/backoffice/produits` | `back-api` + droit `can_create_products` | Creer un produit et ses prix par site | `name`, `stock`, `prices[]` (`site_id`, `price`) |
| GET | `/api/backoffice/produits/{product}` | `back-api` | Detail produit | - |

## 6. Tests

Lancer les tests:
```bash
composer test
```

Ou directement:
```bash
php artisan test
```
