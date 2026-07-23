# Élevage de Lapins

Application de gestion d'élevage cunicole : suivi du cheptel, reproduction, santé, alimentation, ventes et comptabilité. Construite avec Laravel 12, Livewire 3 et Tailwind CSS.

## Fonctionnalités

- **Cheptel** — fiches individuelles des lapins (généalogie, poids, statut, photo), historique de pesées.
- **Reproduction** — cycle complet saillie → diagnostic de gestation → mise bas → sevrage → génération des fiches lapereaux.
- **Infrastructure** — clapiers (avec capacité et occupation), races, stocks d'aliments et mouvements (entrées/distributions).
- **Santé** — suivi des interventions (maladie, traitement, vaccination) avec clôture guéri/décès.
- **Sorties** — ventes, abattages, mortalité, dons ; le statut du lapin et sa cage se mettent à jour automatiquement.
- **Finance** — ventes, clients, dépenses, comptabilité mensuelle (recettes/dépenses/profit) et rapports exportables en CSV.
- **Administration** — gestion des utilisateurs et des employés, réservée aux comptes admin.
- **Corbeille** — la plupart des suppressions sont réversibles (suppression douce + restauration) avant un effacement définitif.
- Tableau de bord avec alertes (diagnostics à faire, mises bas/sevrages imminents, stocks bas, suivis santé en cours).

## Rôles

Deux rôles : `admin` et `eleveur`.

- `eleveur` : accès à la gestion quotidienne de l'élevage (cheptel, reproduction, santé, alimentation, ventes, clients...).
- `admin` : accès complet, y compris utilisateurs, employés (données RH/salaires), et toutes les corbeilles.

Il n'y a pas d'inscription publique : les comptes se créent uniquement depuis la page **Utilisateurs**, par un administrateur.

## Installation

Prérequis : PHP 8.2+, Composer, Node.js, une base MySQL.

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Configurer la connexion à la base dans `.env` (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`), puis :

```bash
php artisan migrate
php artisan db:seed
npm install
npm run build
```

Le seeder de base crée les races, clapiers et aliments de référence, ainsi qu'un compte administrateur :

- Email : `admin@elevage.local`
- Mot de passe : `Lapin@2026`

Pour explorer l'application avec un cheptel de démonstration (lapins, saillies, ventes...) :

```bash
php artisan db:seed --class=DemoSeeder
```

> Changez le mot de passe administrateur avant tout déploiement en production.

## Développement

```bash
composer dev
```

Lance en parallèle le serveur PHP, la file d'attente, les logs (`pail`) et Vite en mode watch.

## Tests

```bash
php artisan test
```

La suite couvre l'authentification, le cycle de reproduction complet, les alertes du tableau de bord, les contrôles d'accès par rôle, et le cycle suppression/restauration des corbeilles.

## Stack technique

- [Laravel 12](https://laravel.com/docs) — framework applicatif
- [Livewire 3](https://livewire.laravel.com/) — composants interactifs côté serveur
- [Tailwind CSS](https://tailwindcss.com/) — mise en forme
- [Chart.js](https://www.chartjs.org/) — graphiques du tableau de bord et de la comptabilité
