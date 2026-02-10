<a name="readme-top"></a>

<div align="center">
  <h1 align="center">Plateforme d'Affichage Numérique</h1>
  
  <p align="center">
    Une solution complète et moderne de gestion d'affichage numérique pour entreprises et institutions.
    <br />
    <a href="#fonctionnalités"><strong>Explorer les fonctionnalités »</strong></a>
    <br />
    <br />
    <a href="#démonstration">Voir la Démo</a>
    ·
    <a href="https://github.com/SoulaimanDev/plateforme-affichage-numerique/issues">Signaler un Bug</a>
    ·
    <a href="https://github.com/SoulaimanDev/plateforme-affichage-numerique/issues">Demander une Feature</a>
  </p>
</div>

<!-- TABLE OF CONTENTS -->
<details>
  <summary>Table des Matières</summary>
  <ol>
    <li>
      <a href="#à-propos-du-projet">À propos du projet</a>
      <ul>
        <li><a href="#construit-avec">Construit avec</a></li>
      </ul>
    </li>
    <li>
      <a href="#fonctionnalités">Fonctionnalités</a>
    </li>
    <li>
      <a href="#commencer">Commencer</a>
      <ul>
        <li><a href="#prérequis">Prérequis</a></li>
        <li><a href="#installation">Installation</a></li>
      </ul>
    </li>
    <li><a href="#usage">Usage</a></li>
    <li><a href="#architecture">Architecture</a></li>
    <li><a href="#contributeurs">Contributeurs</a></li>
    <li><a href="#contact">Contact</a></li>
  </ol>
</details>

<!-- ABOUT THE PROJECT -->
## À propos du projet

Ce projet est une plateforme web robuste conçue pour gérer et diffuser du contenu multimédia sur un réseau d'écrans numériques. Elle offre une interface d'administration intuitive pour la gestion centralisée des écrans, des contenus, et des planifications, ainsi qu'un lecteur (player) léger et performant pour la diffusion.

L'architecture est basée sur le modèle **MVC (Modèle-Vue-Contrôleur)**, garantissant une séparation claire des responsabilités, une maintenance aisée et une évolutivité future.

<p align="right">(<a href="#readme-top">retour en haut</a>)</p>

### Construit avec

*   [![PHP][PHP-badge]][PHP-url] **v7.4+**
*   [![MySQL][MySQL-badge]][MySQL-url]
*   [![Composer][Composer-badge]][Composer-url]
*   [![JavaScript][JS-badge]][JS-url]
*   [![HTML5][HTML-badge]][HTML-url]
*   [![CSS3][CSS-badge]][CSS-url]

<p align="right">(<a href="#readme-top">retour en haut</a>)</p>

<!-- FEATURES -->
## Fonctionnalités

La plateforme propose un ensemble complet de fonctionnalités pour la gestion d'affichage dynamique :

*   **🔐 Authentification & Sécurité** : Système de connexion sécurisé, gestion des rôles utilisateurs, et réinitialisation de mot de passe.
*   **🖥️ Gestion des Écrans** : Enregistrement, surveillance (statut ONLINE/OFFLINE), et configuration à distance des écrans d'affichage.
*   **📁 Gestion de Contenus** : Upload et organisation de médias (Images, Vidéos) via une médiathèque centralisée.
*   **📅 Planification Avancée** : Programmation des contenus sur des plages horaires spécifiques, gestion des récurrences.
*   **📑 Création de Playlists** : Assemblage de contenus en séquences de diffusion personnalisées.
*   **📍 Zones & Emplacements** : Gestion fine des zones de diffusion pour cibler des écrans spécifiques.
*   **🔌 API RESTful** : Endpoints dédiés pour la communication temps réel avec les lecteurs (players).
*   **📊 Dashboard** : Vue d'ensemble de l'état du parc d'écrans et des diffusions en cours.

<p align="right">(<a href="#readme-top">retour en haut</a>)</p>

<!-- GETTING STARTED -->
## Commencer

Pour lancer une copie locale, suivez ces étapes simples.

### Prérequis

*   PHP >= 7.4
*   MySQL / MariaDB
*   Composer
*   Serveur Web (Apache/Nginx) ou serveur interne PHP

### Installation

1.  **Cloner le dépôt**
    ```sh
    git clone https://github.com/SoulaimanDev/plateforme-affichage-numerique.git
    cd plateforme-affichage-numerique
    ```

2.  **Installer les dépendances**
    ```sh
    composer install
    ```

3.  **Configurer l'environnement**
    Renommez le fichier `.env.example` en `.env` et configurez vos accès base de données.
    ```sh
    cp .env.example .env
    ```
    *Éditez `.env` avec vos paramètres (DB_HOST, DB_NAME, DB_USER, DB_PASS).*

4.  **Base de données**
    Importez le script SQL fourni pour structurer la base de données.
    ```sh
    mysql -u votre_user -p votre_base < data/digital_signage.sql
    ```

5.  **Lancer l'application**
    Si vous utilisez le serveur interne PHP :
    ```sh
    php -S localhost:8000 -t public
    ```

<p align="right">(<a href="#readme-top">retour en haut</a>)</p>

<!-- USAGE EXAMPLES -->
## Usage

### Interface Administration
Accédez à l'URL de votre site (ex: `http://localhost:8000`) pour vous connecter au tableau de bord.
*   Gérez vos médias, créez des playlists, et associez-les à des plannings.
*   Ajoutez des écrans et récupérez leurs identifiants uniques.

### Lecteur (Player)
Le lecteur est accessible via une URL spécifique pour chaque écran, généralement sous la forme :
`http://votre-domaine/player/{SCREEN_ID}`

Il se connecte automatiquement à l'API pour récupérer sa programmation et affiche le contenu en boucle.

<p align="right">(<a href="#readme-top">retour en haut</a>)</p>

<!-- ARCHITECTURE -->
## Architecture

Le projet suit une structure MVC stricte pour une meilleure organisation :

    ├── public/           # Point d'entrée (index.php) et assets (JS, CSS, Images)
    ├── config/           # Fichiers de configuration
    ├── src/              # Code Source
    │   ├── Controller/   # Logique de traitement des requêtes
    │   ├── Core/         # Noyau du framework (Router, Database, etc.)
    │   ├── Repository/   # Coche d'accès aux données (SQL)
    │   ├── Service/      # Logique métier réutilisable
    │   ├── View/         # Templates d'affichage
    │   └── Middleware/   # Filtres (Auth, CSRF)
    ├── storage/          # Logs et fichiers uploadés
    └── vendor/           # Dépendances Composer

<p align="right">(<a href="#readme-top">retour en haut</a>)</p>

<!-- MARKDOWN LINKS & IMAGES -->
[PHP-badge]: https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white
[PHP-url]: https://www.php.net/
[MySQL-badge]: https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white
[MySQL-url]: https://www.mysql.com/
[Composer-badge]: https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white
[Composer-url]: https://getcomposer.org/
[JS-badge]: https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black
[JS-url]: https://developer.mozilla.org/en-US/docs/Web/JavaScript
[HTML-badge]: https://img.shields.io/badge/HTML5-E34F26?style=for-the-badge&logo=html5&logoColor=white
[HTML-url]: https://developer.mozilla.org/en-US/docs/Web/HTML
[CSS-badge]: https://img.shields.io/badge/CSS3-1572B6?style=for-the-badge&logo=css3&logoColor=white
[CSS-url]: https://developer.mozilla.org/en-US/docs/Web/CSS
