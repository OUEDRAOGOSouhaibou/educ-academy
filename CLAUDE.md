# Educ'Academy — Mémoire du Projet & Guide de Développement

## 1. Résumé du projet
- **Nom :** Educ'Academy
- **Version :** 1.0 (MVP)
- **Objectif :** Plateforme web éducative permettant aux élèves autorisés de Terminale de consulter des vidéos de cours de mathématiques (hébergées sur YouTube en mode non répertorié) et de télécharger des documents PDF (cours, séries, devoirs, corrigés) organisés par mois scolaire.
- **Identifiant principal élève :** Numéro de téléphone (normalisé, unique).
- **Rôles :** Administrateur (`admin`) et Élève (`student`).
- **Statuts élèves :** En attente (`pending`), Autorisé (`approved`), Bloqué (`blocked`).

---

## 2. Conventions retenues

### Base de données & Code
- Tables et colonnes en **anglais** selon les conventions Laravel (`users`, `school_years`, `months`, `videos`, `documents`).
- Tous les textes de l'interface, libellés, formulaires, messages de validation et pages d'erreur sont strictement en **français**.
- Protection contre l'assignation de masse : attributs `$fillable` explicites. `role` et `status` ne sont **jamais** fillables sur `User`.
- Utilisation d'**Enums PHP** typés pour les valeurs fixes (`UserRole`, `UserStatus`, `DocumentType`).

### Organisation des contenus
- Les contenus sont organisés **par mois** rattachés à une **année scolaire** (`SchoolYear`).
- Une seule année scolaire active à la fois (`is_current = true`).
- Les mois sont triés par le champ `position` (Octobre = 1, Novembre = 2... Juin = 9), jamais par numéro calendaire.
- Règle d'or : tout mois publié de l'année en cours reste accessible aux élèves autorisés.

### Sécurité
- Normalisation systématique du numéro de téléphone (suppression espaces, tirets, points).
- Vérification du statut du compte élève à chaque requête via middleware : un élève `blocked` est immédiatement déconnecté.
- Mot de passe oublié : pas d'e-mail dans la V1. Génération par l'administrateur d'un mot de passe temporaire + bascule `must_change_password = true`.
- Fichiers PDF stockés dans `storage/app/private` (non accessibles publiquement), téléchargement et visualisation via contrôleur avec contrôle d'autorisation.
- Suppression des fichiers PDF sur le disque lors du remplacement d'un fichier ou de la suppression d'un document / mois.
- Vidéos YouTube : extraction de l'identifiant, intégration via `youtube-nocookie.com`, façade miniature pour économie de bande passante mobile.

---

## 3. Commandes utiles

```powershell
# Démarrage du serveur de développement et compilation des assets
php artisan serve
npm run dev

# Compilation des assets pour production
npm run build

# Tests automatisés
php artisan test

# Création du premier administrateur (Phase 3)
php artisan app:create-admin
```

---

## 4. Décisions techniques importantes
- **Framework & PHP :** Laravel 13 sur PHP 8.4 (compatible PHP 8.3+).
- **Frontend :** Blade + Bootstrap 5.3 + Bootstrap Icons. Aucun framework JS complexe (pas de React, Vue, Livewire, ni Tailwind).
- **Assets de production :** Compilation locale via Vite (`npm run build`) vers `public/build`, compatible hébergement mutualisé sans Node.js.
- **Régionalisation :** `APP_LOCALE=fr`, `APP_FALLBACK_LOCALE=fr`, `APP_TIMEZONE=Africa/Ouagadougou`.

---

## 5. Journal d'avancement

- [x] **Phase 1 : Analyse de l'environnement**
  - Vérification de PHP (8.4.27), Composer (2.9.2), Node (v24.12.0), npm (11.6.2), Git (2.52).
  - Vérification des extensions (`pdo_mysql`, `pdo_sqlite`, `fileinfo`, `mbstring`, `intl`, `gd`, `zip`).
  - Validation des limites upload : `upload_max_filesize = 40M` et `post_max_size = 40M` (supérieur à `PDF_MAX_SIZE_MB=20`).

- [x] **Phase 2 : Initialisation & Mémoire**
  - Fichier `docs/cahier-des-charges.md` intégré.
  - Fichier `CLAUDE.md` créé pour la mémoire du projet.
  - Configuration régionale française (`config/app.php`, traductions `lang/fr/*`).
  - Intégration de Bootstrap 5.3 et Bootstrap Icons (suppression de Tailwind, Vite configuré, build testé et validé).
  - Styles personnalisés Educ'Academy (`resources/css/app.css`) et façade de lazy-load vidéo (`resources/js/app.js`).

- [x] **Phase 3 : Base de données & Modèles**
  - Enums (`UserRole`, `UserStatus`, `DocumentType`).
  - Migrations pour `users`, `school_years`, `months`, `videos`, `documents`.
  - Modèles Eloquent (`User`, `SchoolYear`, `Month`, `Video`, `Document`) avec relations, scopes et `$fillable` stricts.
  - Commande Artisan interactive `php artisan app:create-admin` (saisie masquée du mot de passe).
  - Seeders (`SchoolYearSeeder`, `DatabaseSeeder`) avec année 2026-2027, mois Octobre-Juin, vidéos et PDF de test.
  - Tests unitaires et fonctionnels validés (10 tests, 59 assertions).

- [x] **Phase 4 : Authentification & Sécurité**
  - Formulaires et contrôleurs d'inscription et de connexion (identifiant `phone` normalisé).
  - Rate limiting sur les connexions et inscriptions contre le bruteforce.
  - Attribution stricte côté serveur de `role = student` et `status = pending`.
  - Middleware `EnsureAccountIsActive` vérifié à chaque requête : déconnexion immédiate des élèves `blocked`, redirection des `pending`.
  - Changement forcé du mot de passe (`must_change_password`).
  - Module d'administration des élèves (`StudentController`) : validation, suspension, déblocage, suppression, génération de mot de passe temporaire pour WhatsApp.
  - Vues Blade Bootstrap 5 : `layouts/app`, `layouts/auth`, `auth/login`, `auth/register`, `auth/change-password`, `student/pending`, `admin/students/index`.
  - Tests unitaires et fonctionnels validés (22 tests, 110 assertions).

- [x] **Phase 5 : Gestion des contenus**
  - CRUD des mois scolaires (`MonthController`) : publication/masquage instantané, respect du tri `position` (Octobre=1).
  - CRUD des vidéos (`VideoController`) : extraction de l'identifiant YouTube, intégration nocookie, façade miniature lazy-load.
  - CRUD des documents PDF (`DocumentController`) : validation d'extension et MIME réel `application/pdf`, limite 20 Mo, stockage sécurisé dans `storage/app/private/documents`.
  - Contrôleur de téléchargement et consultation sécurisée (`DocumentDownloadController`) : protection stricte (interdiction aux visiteurs et mois non publiés, nom lisible au téléchargement).
  - Nettoyage physique automatique des PDF sur le disque lors du remplacement de fichier ou de la suppression d'un document ou d'un mois.
  - Vues Blade Administrateur (`admin/months/index`, `admin/months/show`).
  - Tests automatisés validés (29 tests, 134 assertions au total).

- [ ] **Phase 6 : Interfaces**
  - Layouts Blade (Public, Élève, Admin).
  - Page d'accueil publique.
  - Espace Élève (consultation par mois, vidéos, PDF).
  - Tableau de bord Administrateur (statistiques, listes, modales).
  - Responsive testé sur format smartphone (360px).

- [ ] **Phase 7 : Tests & Déploiement**
  - Tests automatisés (Feature tests complets).
  - Guide de mise en ligne et export de structure pour hébergement mutualisé.

