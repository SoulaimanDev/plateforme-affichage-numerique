# Plateforme d'Affichage Numérique

## Description
Système de gestion d'affichage numérique avec architecture MVC.

## Installation

1. Cloner le projet
2. Configurer `.env` avec vos paramètres de base de données
3. Importer `data/digital_signage.sql`
4. Lancer `composer install`
5. Accéder via navigateur

## Structure
- `public/` : Point d'entrée et assets
- `src/` : Code source PHP (Architecture MVC Refactorisée)
  - `Controller/` : Chefs d'orchestre
  - `Repository/` : Accès aux données (ex-Model)
  - `Service/` : Logique métier et tiers
  - `Middleware/` : Sécurité et Intercepteurs (Auth)
  - `Http/` : Abstraction Request/Response
  - `Core/` : Noyau du framework
  - `View/` : Gabarits d'affichage
- `config/` : Configuration
- `storage/` : Logs et cache
