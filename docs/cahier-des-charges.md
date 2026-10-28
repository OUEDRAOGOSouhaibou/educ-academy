# Cahier des charges — Educ'Academy V1

Version du 26 octobre 2026.

## 1. Contexte et rôle

Tu es un ingénieur logiciel senior, expert en développement web full-stack, particulièrement en Laravel, PHP, MySQL, Bootstrap, JavaScript, architecture MVC, sécurité des applications et déploiement web.

Je souhaite que tu m'accompagnes dans la conception et le développement complet d'une application web appelée **Educ'Academy**.

Je suis développeur informatique et je souhaite construire cette application avec ton assistance, en respectant les bonnes pratiques de programmation, de sécurité, d'architecture logicielle et d'expérience utilisateur.

Tu dois travailler comme un véritable collaborateur technique : analyser les besoins, proposer une architecture adaptée, écrire du code propre, expliquer les choix importants, effectuer des vérifications et m'accompagner jusqu'au déploiement.

Toute l'interface de l'application (textes, boutons, messages de validation, e-mails, pages d'erreur) doit être en **français**.

## 2. Présentation du projet

- **Nom :** Educ'Academy
- **Version :** 1.0 (MVP)
- **Type :** plateforme web éducative de diffusion de vidéos pédagogiques et de documents PDF.
- **Public cible :** élèves de Terminale.
- **Matière :** Mathématiques.

### Problématique

Actuellement, nous partageons nos vidéos de cours de mathématiques aux élèves via WhatsApp. Cette méthode présente plusieurs inconvénients :

- Les vidéos téléchargées occupent beaucoup d'espace de stockage sur les téléphones.
- Les anciens cours deviennent difficiles à retrouver dans les conversations WhatsApp.
- Les vidéos et documents ne sont pas organisés de manière structurée.
- Les élèves ont parfois besoin de retrouver rapidement les cours des mois précédents.

### Solution attendue

Développer une petite plateforme web permettant aux élèves de se connecter à partir d'un lien partagé sur WhatsApp, puis de regarder directement les vidéos de cours en ligne, sans téléchargement obligatoire.

La plateforme permettra également de consulter et de télécharger des documents PDF : cours, séries d'exercices, devoirs et corrigés.

L'application doit être simple, rapide, responsive, sécurisée et adaptée aux téléphones Android, qui seront les principaux appareils utilisés par les élèves. Beaucoup d'élèves n'ont pas d'adresse e-mail qu'ils consultent régulièrement : l'identifiant principal sera donc le **numéro de téléphone**.

## 3. Technologies imposées

- Backend : PHP avec Laravel.
- Base de données : MySQL.
- Frontend : Blade, HTML5, CSS3, Bootstrap 5, Bootstrap Icons et JavaScript.
- Architecture : MVC.
- Authentification : mécanismes sécurisés natifs de Laravel.
- Développement local : Windows avec XAMPP, Composer et Visual Studio Code.
- Gestion des versions : Git.

Privilégier une architecture Laravel monolithique avec Blade, sans React, Vue.js, Next.js, Livewire ni infrastructure complexe.

**Authentification :** l'implémenter avec des vues Blade + Bootstrap. Ne pas utiliser de starter kit basé sur Tailwind, React, Vue ou Livewire, afin de ne pas mélanger deux frameworks CSS. Si un package d'authentification est utilisé, il doit être compatible Bootstrap ; sinon, coder l'authentification directement avec les outils natifs de Laravel.

**Assets front-end :** l'hébergement de production n'aura probablement ni Node.js ni accès SSH. Les assets doivent donc être compilés en local (`npm run build`) et le dossier `public/build` déployé tel quel. Le serveur de production ne doit jamais avoir besoin de Node.js. Si c'est plus simple, Bootstrap peut aussi être chargé via un CDN fiable.

Choisir une version stable de Laravel compatible avec les versions de PHP et Composer réellement disponibles sur mon ordinateur **et** chez l'hébergeur visé. Vérifier les prérequis avant l'installation.

L'application doit être conçue pour fonctionner sur un hébergement PHP/MySQL abordable ou gratuit, sous réserve de compatibilité.

Éviter les dépendances inutiles et les services payants obligatoires.

**Configuration régionale :** `APP_LOCALE=fr`, `APP_FALLBACK_LOCALE=fr`, messages de validation Laravel traduits en français, et fuseau horaire `APP_TIMEZONE=[À COMPLÉTER : fuseau horaire, format Continent/Ville]`.

## 4. Fonctionnalités principales

### 4.1. Page d'accueil

Créer une page d'accueil professionnelle présentant Educ'Academy. Elle devra contenir :

- Le nom et l'identité visuelle Educ'Academy.
- Une présentation de la plateforme.
- La mention « Terminale — Mathématiques ».
- Une présentation des avantages de la plateforme.
- Un bouton « Se connecter ».
- Un bouton « S'inscrire ».
- Un pied de page professionnel.

Créer un design moderne avec une identité visuelle cohérente, de belles couleurs, des icônes, des cartes élégantes et une interface adaptée aux smartphones.

### 4.2. Authentification et comptes

Prévoir deux rôles :

**Administrateur**

- Accéder à un tableau de bord sécurisé.
- Gérer les comptes élèves : autoriser, bloquer, débloquer, supprimer.
- Réinitialiser le mot de passe d'un élève.
- Gérer les années scolaires, les mois et les contenus pédagogiques.

**Élève**

- Créer un compte.
- Se connecter.
- Consulter les mois publiés.
- Regarder les vidéos.
- Consulter et télécharger les PDF.
- Changer son mot de passe.
- Se déconnecter.

**Inscription :**

- Champs : nom complet, **numéro de téléphone (obligatoire, unique)**, e-mail (facultatif), mot de passe et confirmation.
- Normaliser le numéro de téléphone avant enregistrement (suppression des espaces, tirets et points, format homogène) pour garantir l'unicité.
- La connexion se fait avec le **numéro de téléphone + mot de passe**.
- Le rôle et le statut ne doivent **jamais** pouvoir être définis depuis le formulaire d'inscription. Tout nouvel élève est créé côté serveur avec `role = student` et `status = pending`. Protéger les modèles contre l'assignation de masse (`$fillable` explicite, sans `role` ni `status`).

**Statuts des comptes élèves :**

- `pending` : inscription en attente. L'élève peut se connecter mais voit uniquement un message indiquant que son inscription est en attente de validation par l'administrateur.
- `approved` : accès complet aux contenus publiés.
- `blocked` : accès refusé. Un élève bloqué doit être **déconnecté immédiatement**, y compris s'il a une session ouverte. Vérifier le statut à chaque requête via un middleware, pas seulement à la connexion. Afficher un message clair indiquant que le compte est bloqué.

**Mot de passe oublié :**

L'envoi d'e-mails n'est pas fiable sur les hébergements gratuits et beaucoup d'élèves n'ont pas d'e-mail. Dans la V1, il n'y a donc **pas de réinitialisation par e-mail**. À la place :

- L'administrateur peut générer un mot de passe temporaire aléatoire pour un élève, affiché une seule fois à l'écran, afin de le transmettre à l'élève par WhatsApp.
- À sa prochaine connexion, l'élève doit obligatoirement choisir un nouveau mot de passe (champ `must_change_password`).
- La page de connexion affiche un message du type : « Mot de passe oublié ? Contactez votre professeur. »

Prévoir une protection contre les accès non autorisés et une gestion sécurisée des mots de passe.

### 4.3. Organisation des contenus par mois

**IMPORTANT : les contenus ne doivent pas être organisés par chapitre, mais par mois.**

La plateforme concerne une seule classe, Terminale, et une seule matière, Mathématiques.

Prévoir les mois de l'année scolaire : Octobre, Novembre, Décembre, Janvier, Février, Mars, Avril, Mai, Juin. L'administrateur doit pouvoir ajouter d'autres mois si nécessaire (par exemple Septembre ou Juillet).

Chaque mois constitue une section regroupant les vidéos et documents PDF publiés durant cette période.

**Ordre d'affichage :** les mois doivent s'afficher dans l'ordre de l'année scolaire (Octobre → Juin), et non dans l'ordre du calendrier. Utiliser un champ `position` dédié (Octobre = 1, Novembre = 2… Juin = 9) pour le tri. Ne jamais trier par numéro de mois calendaire.

**Années scolaires :** les mois sont rattachés à une année scolaire (par exemple 2026–2027), gérée dans une table dédiée. Une seule année scolaire est marquée comme **année en cours**. Les élèves voient uniquement les mois publiés de l'année en cours. Les années précédentes restent en base et visibles par l'administrateur.

L'administrateur doit pouvoir créer, modifier, publier, masquer et supprimer un mois. La suppression d'un mois contenant des vidéos ou des documents doit demander une confirmation claire et supprimer proprement les fichiers PDF associés.

**Règle fondamentale : tous les mois déjà publiés de l'année en cours restent accessibles aux élèves autorisés.** Ainsi, un élève qui consulte la plateforme en mars doit pouvoir revenir sur les vidéos et PDF d'octobre, novembre, décembre, janvier et février.

Un mois non publié ne doit pas être accessible aux élèves, même par son URL directe. Il en va de même pour les vidéos et PDF qu'il contient.

### 4.4. Gestion des vidéos

L'administrateur doit pouvoir :

- Ajouter une vidéo.
- Définir son titre.
- Ajouter une description facultative.
- Associer la vidéo à un mois.
- Coller son lien YouTube.
- Définir son ordre d'affichage.
- Modifier ou supprimer la vidéo.

Une vidéo est visible par les élèves dès que son mois est publié. Dans les statistiques, « vidéos publiées » signifie « vidéos appartenant à un mois publié de l'année en cours ».

Les vidéos seront hébergées sur YouTube en mode non répertorié afin de réduire les coûts de stockage et d'hébergement. La plateforme devra intégrer un lecteur vidéo responsive permettant de les regarder directement sur le site. Ne pas télécharger les vidéos sur le serveur Laravel.

Valider les URL YouTube (formats `youtube.com/watch?v=`, `youtu.be/`, `youtube.com/shorts/`, `youtube.com/embed/`) et extraire correctement l'identifiant vidéo. Enregistrer uniquement l'identifiant en base. Utiliser le domaine `youtube-nocookie.com` pour l'intégration.

Prévoir un chargement différé des lecteurs : afficher d'abord la miniature de la vidéo avec un bouton de lecture, et ne charger le lecteur YouTube qu'au clic. Cela économise beaucoup de données mobiles quand une page contient plusieurs vidéos.

**Limitation de sécurité à prendre en compte :** les vidéos YouTube non répertoriées ne sont pas totalement privées. Une personne disposant du lien direct peut les partager. Ne pas présenter ce système comme une protection vidéo inviolable.

### 4.5. Gestion des documents PDF

Chaque document a un **type** parmi : Cours, Série d'exercices, Devoir, Corrigé.

L'administrateur doit pouvoir :

- Ajouter un document PDF.
- Définir son titre et son type.
- Ajouter une description facultative.
- Associer le document à un mois.
- Définir son ordre d'affichage.
- Modifier ses informations.
- Remplacer le fichier.
- Supprimer le document.

Les élèves autorisés doivent pouvoir :

- Voir la liste des documents disponibles, regroupés ou filtrables par type.
- Voir la taille de chaque fichier avant de le télécharger (important pour les forfaits mobiles).
- Consulter un PDF dans le navigateur lorsque cela est possible.
- Télécharger un PDF sur leur téléphone, avec un nom de fichier lisible (par exemple `Serie-exercices-Novembre.pdf`).

Pour des raisons de sécurité :

- Accepter uniquement les fichiers PDF valides (vérification de l'extension **et** du type MIME réel).
- Définir une taille maximale configurable dans `.env` (par exemple `PDF_MAX_SIZE_MB=20`).
- Vérifier que les limites PHP `upload_max_filesize` et `post_max_size` sont cohérentes avec cette taille, en local (XAMPP) et chez l'hébergeur, et me signaler si elles doivent être modifiées.
- Générer des noms de fichiers uniques sur le disque et conserver le nom d'origine en base.
- Stocker les fichiers dans un espace non public (`storage/app/private` ou équivalent).
- Utiliser des contrôleurs Laravel pour vérifier les autorisations avant de servir les fichiers.
- Empêcher l'accès direct aux PDF par des URL publiques non protégées.
- Supprimer l'ancien fichier du disque lors d'un remplacement et lors de la suppression d'un document ou d'un mois.

### 4.6. Tableau de bord administrateur

Créer un tableau de bord avec :

- Nombre total d'élèves.
- Nombre d'élèves autorisés.
- Nombre de comptes en attente (mis en évidence, avec un accès direct à la liste à valider).
- Nombre de comptes bloqués.
- Nombre de vidéos publiées.
- Nombre de documents PDF.
- Liste des derniers contenus ajoutés.

La liste des élèves doit permettre de rechercher par nom ou téléphone et de filtrer par statut.

Prévoir une interface d'administration claire et responsive, utilisable aussi depuis un téléphone.

### 4.7. Tableau de bord élève

Après connexion, un élève autorisé doit voir :

- Un message de bienvenue.
- La mention « Terminale — Mathématiques ».
- L'année scolaire en cours.
- La liste des mois publiés, dans l'ordre de l'année scolaire.
- Le nombre de vidéos et de PDF disponibles pour chaque mois.

En cliquant sur un mois, l'élève accède à ses contenus. Les vidéos et les PDF doivent être clairement distingués.

L'interface doit rester très simple afin que même un élève peu habitué aux plateformes numériques puisse l'utiliser facilement.

## 5. Base de données MySQL

Nom de la base : `educ_academy`. Encodage : `utf8mb4`.

**Convention de nommage :** tables et colonnes en **anglais**, selon les conventions Laravel (pluriels automatiques corrects, code plus lisible). Tous les libellés affichés à l'écran restent en français. Les noms ci-dessous sont une base : tu peux proposer des ajustements en les justifiant.

### users

| Colonne | Type | Remarques |
| --- | --- | --- |
| id | bigint | clé primaire |
| name | string | nom complet |
| phone | string | obligatoire, unique, normalisé ; identifiant de connexion |
| email | string | facultatif, unique si renseigné |
| password | string | haché |
| role | enum | `admin`, `student` ; défaut `student` |
| status | enum | `pending`, `approved`, `blocked` ; défaut `pending` ; indexé |
| must_change_password | boolean | défaut `false` |
| approved_at | timestamp | nullable |
| remember_token, timestamps | — | standard Laravel |

### school_years

| Colonne | Type | Remarques |
| --- | --- | --- |
| id | bigint | clé primaire |
| label | string | par exemple `2026-2027` ; unique |
| is_current | boolean | une seule année en cours à la fois (garanti côté application) |
| timestamps | — | standard Laravel |

### months

| Colonne | Type | Remarques |
| --- | --- | --- |
| id | bigint | clé primaire |
| school_year_id | foreignId | vers `school_years`, suppression restreinte |
| name | string | Octobre, Novembre… |
| calendar_number | tinyint | 1 à 12 (mois du calendrier) |
| position | tinyint | ordre dans l'année scolaire (Octobre = 1) ; utilisé pour le tri |
| is_published | boolean | défaut `false` |
| timestamps | — | standard Laravel |

Contraintes : unique (`school_year_id`, `calendar_number`) ; index (`school_year_id`, `is_published`, `position`).

### videos

| Colonne | Type | Remarques |
| --- | --- | --- |
| id | bigint | clé primaire |
| month_id | foreignId | vers `months`, suppression en cascade |
| title | string |  |
| description | text | nullable |
| youtube_id | string(20) | identifiant extrait de l'URL |
| position | unsigned int | ordre d'affichage ; défaut 0 |
| timestamps | — | standard Laravel |

Index : (`month_id`, `position`).

### documents

| Colonne | Type | Remarques |
| --- | --- | --- |
| id | bigint | clé primaire |
| month_id | foreignId | vers `months`, suppression en cascade (fichiers supprimés du disque par l'application) |
| title | string |  |
| description | text | nullable |
| type | enum | `cours`, `serie`, `devoir`, `corrige` |
| file_path | string | chemin du fichier unique sur le disque privé |
| original_name | string | nom d'origine du fichier |
| file_size | unsigned int | en octets, affiché à l'élève |
| position | unsigned int | ordre d'affichage ; défaut 0 |
| timestamps | — | standard Laravel |

Index : (`month_id`, `type`, `position`).

### Éléments à créer

- Migrations, modèles Eloquent et relations (`SchoolYear` → `Month` → `Video` / `Document`).
- `$fillable` explicite sur chaque modèle ; `role` et `status` exclus de `User`.
- Enums PHP (ou constantes) pour `role`, `status` et `type`, plutôt que des chaînes répétées dans le code.
- Contrôleurs, Form Requests, middlewares et policies nécessaires.
- Seeders pour les données de démonstration (une année scolaire, quelques mois, vidéos et PDF d'exemple), utilisables uniquement en local.
- Une commande Artisan sécurisée pour créer le premier administrateur, par exemple `php artisan app:create-admin`, qui demande le nom, le téléphone et le mot de passe de manière interactive (saisie masquée).

Ne jamais utiliser un mot de passe administrateur codé en dur dans le dépôt.

Garder une architecture permettant d'ajouter plus tard d'autres classes, matières et enseignants, sans les créer dans la V1.

## 6. Sécurité

Mettre en œuvre :

- Authentification sécurisée et régénération de la session à la connexion.
- Hachage des mots de passe (mécanisme natif de Laravel), avec une longueur minimale de 8 caractères.
- Protection CSRF.
- Validation côté serveur de toutes les entrées.
- Protection contre l'assignation de masse : un utilisateur ne doit jamais pouvoir modifier son `role` ou son `status` via une requête.
- Contrôle des rôles.
- Vérification du statut du compte élève **à chaque requête** (un compte bloqué est déconnecté immédiatement).
- Protection des routes administrateur.
- Protection des pages des mois non publiés et des contenus qu'ils contiennent, y compris par URL directe.
- Protection des téléchargements PDF.
- Limitation des tentatives de connexion (par numéro de téléphone et par adresse IP).
- Limitation des tentatives d'inscription pour éviter les comptes en masse.
- Prévention des injections SQL et des failles XSS.
- Configuration sécurisée des sessions et des cookies (`secure`, `httponly`, `same_site`) en production.
- Mots de passe temporaires générés aléatoirement, jamais enregistrés en clair ni écrits dans les logs.
- Gestion appropriée des erreurs : pages d'erreur 403, 404, 419 et 500 en français, sans détails techniques en production.

Ne jamais faire confiance uniquement aux contrôles présents dans l'interface utilisateur.

## 7. Design et expérience utilisateur

Je souhaite un site visuellement professionnel, moderne et agréable. Utiliser Bootstrap 5 avec une personnalisation CSS soignée.

Le design doit être :

- Mobile-first.
- Responsive.
- Rapide à charger, même sur une connexion 3G (pages légères, images optimisées, pas de bibliothèques inutiles).
- Facile à naviguer.
- Adapté aux connexions Internet parfois limitées.
- Accessible, avec des contrastes suffisants et des boutons faciles à toucher sur un petit écran.

Prévoir des états vides élégants lorsqu'aucune vidéo ou aucun PDF n'est encore publié.

Créer une navigation simple et cohérente entre les différentes pages.

Avant de coder les interfaces, me proposer une identité visuelle (palette de couleurs, typographie, style des cartes) et attendre ma validation.

## 8. Architecture et qualité du code

Respecter les bonnes pratiques Laravel :

- Séparation claire des responsabilités.
- Routes nommées, regroupées par rôle (`admin.*`, `student.*`).
- Contrôleurs organisés (par exemple un dossier `Admin` séparé).
- Modèles Eloquent.
- Validation avec Form Requests lorsque pertinent.
- Middleware et policies pour les autorisations.
- Templates Blade réutilisables (layouts public, élève et admin).
- Composants d'interface réutilisables (cartes, alertes, états vides, lecteur vidéo).
- Logique réutilisable isolée dans de petites classes (par exemple l'extraction d'identifiant YouTube, la normalisation du téléphone).
- Code commenté uniquement lorsque cela apporte une réelle valeur.
- Gestion claire des erreurs.

Ne pas surdimensionner le projet : il s'agit d'un MVP destiné à une seule classe et une seule matière. Cependant, garder une architecture permettant d'ajouter plus tard d'autres classes, matières, enseignants et fonctionnalités de paiement.

## 9. Tests

Prévoir des tests automatisés (Feature tests Laravel) pour vérifier notamment :

1. Un élève peut s'inscrire avec son numéro de téléphone ; son compte est créé avec le statut `pending`.
2. Un élève ne peut pas s'attribuer le rôle `admin` ou le statut `approved` en ajoutant ces champs à la requête d'inscription.
3. Un élève peut se connecter avec son téléphone, même saisi avec des espaces ou des tirets.
4. Un élève en attente ne peut pas accéder aux contenus et voit le message d'attente.
5. Un administrateur peut autoriser, bloquer et débloquer un élève.
6. Un élève bloqué est déconnecté à sa requête suivante et ne peut plus accéder aux contenus.
7. Un élève autorisé peut consulter les mois publiés de l'année en cours, dans le bon ordre (Octobre avant Janvier).
8. Un élève ne peut pas accéder à un mois non publié, ni à ses vidéos et PDF, même par URL directe.
9. Un élève autorisé peut consulter et télécharger les PDF d'un mois publié.
10. Un visiteur non connecté ne peut pas télécharger les PDF protégés.
11. Un fichier non PDF ou trop volumineux est refusé.
12. Les URL YouTube valides de différents formats sont acceptées et les URL invalides refusées.
13. Un élève ne peut pas accéder à l'administration.
14. L'administrateur peut ajouter, modifier et supprimer les mois, vidéos et documents ; le remplacement et la suppression d'un PDF suppriment bien l'ancien fichier du disque.
15. La réinitialisation du mot de passe par l'administrateur oblige l'élève à changer son mot de passe à la connexion suivante.

Exécuter les tests et corriger les erreurs constatées. Utiliser une base de test séparée (SQLite en mémoire si l'extension est disponible, sinon une base MySQL `educ_academy_test`), sans jamais toucher à la base de développement.

## 10. Hébergement et déploiement

Je souhaite, dans un premier temps, minimiser les dépenses et rechercher si possible un hébergement gratuit compatible avec Laravel et MySQL. L'objectif est d'obtenir une adresse publique HTTPS que je pourrai partager aux élèves sur WhatsApp.

Préparer l'application pour un déploiement sur hébergement PHP/MySQL classique (mutualisé, souvent sans SSH ni Composer ni Node.js).

Prendre en compte :

- Les versions PHP compatibles et les extensions PHP nécessaires.
- Le déploiement sans SSH : dossier `vendor` installé en local (`composer install --no-dev --optimize-autoloader`) puis envoyé, assets compilés en local.
- Les migrations MySQL sans accès en ligne de commande (export SQL depuis la base locale ou route d'installation protégée et désactivée après usage).
- La configuration du fichier `.env` de production.
- La sécurité de `APP_KEY`.
- `APP_ENV=production` et `APP_DEBUG=false` en production.
- Le point d'entrée Laravel dans le dossier `public` : si l'hébergeur ne permet pas de pointer le domaine vers `public`, proposer une solution sûre qui n'expose ni `.env`, ni `storage`, ni `vendor`.
- Les permissions des répertoires `storage` et `bootstrap/cache`.
- Le certificat HTTPS et la redirection HTTP → HTTPS.
- Les limites de stockage et de taille d'upload pour les PDF.
- Les sauvegardes de la base de données et des documents PDF, avec une procédure simple que je peux faire moi-même.

Ne jamais supposer qu'un hébergement gratuit prend automatiquement en charge Laravel. Vérifier sa compatibilité et ses restrictions avant de proposer son utilisation : version de PHP, accès au dossier `public`, quotas de fichiers et d'espace disque, mise en veille, interdiction éventuelle d'héberger des fichiers.

Proposer aussi un **plan B** : un ou deux hébergements payants peu coûteux compatibles Laravel, avec leurs avantages et leurs limites.

C'est moi qui créerai les comptes chez l'hébergeur et qui saisirai les identifiants. Tu me guides étape par étape, sans jamais me demander de te communiquer de mot de passe.

## 11. Méthode de travail attendue

Je veux que tu travailles directement dans mon environnement de développement, mais progressivement.

### Règle de passage entre les phases

À la fin de **chaque** phase :

1. Vérifie que tout fonctionne (commandes, tests, pages).
2. Fais un commit Git avec un message clair.
3. Mets à jour la section « Avancement » de `CLAUDE.md`.
4. Donne-moi un court résumé : ce qui a été fait, ce qui a été vérifié, ce qui reste à décider.
5. **Attends ma validation explicite avant de commencer la phase suivante.**

### Mémoire du projet

Le projet s'étalera sur plusieurs sessions. Dès la création du projet :

- Enregistre ce cahier des charges complet dans `docs/cahier-des-charges.md`.
- Crée un fichier `CLAUDE.md` à la racine contenant : le résumé du projet, les conventions retenues (nommage, structure, commandes utiles), les décisions techniques importantes et une section « Avancement » tenue à jour.
- Au début de chaque nouvelle session, relis `CLAUDE.md` et `docs/cahier-des-charges.md` avant toute action.

### Phases

**Phase 1 : Analyse de l'environnement**

- Vérifier Windows, PHP, Composer, Node.js, npm, Git et MySQL (XAMPP).
- Vérifier les versions, les extensions PHP requises et leur compatibilité avec la version de Laravel envisagée.
- Vérifier les limites `upload_max_filesize` et `post_max_size` de XAMPP.
- Identifier le dossier de travail.
- Me signaler les éléments manquants et comment les installer.

**Phase 2 : Initialisation**

- Créer le projet Laravel.
- Initialiser Git avec un `.gitignore` correct (`.env`, `vendor`, `node_modules` exclus).
- Créer `docs/cahier-des-charges.md` et `CLAUDE.md`.
- Configurer MySQL, la langue française et le fuseau horaire.
- Préparer les variables d'environnement (`.env` et `.env.example` sans secret).
- Vérifier que Laravel démarre correctement.

**Phase 3 : Base de données**

- Créer les migrations.
- Créer les modèles, enums et relations.
- Créer les seeders et la commande de création de l'administrateur.
- Exécuter les migrations et vérifier la structure.

**Phase 4 : Authentification**

- Créer les pages de connexion (téléphone + mot de passe) et d'inscription.
- Implémenter les rôles et les statuts (`pending`, `approved`, `blocked`).
- Implémenter la validation des comptes et la réinitialisation de mot de passe par l'administrateur.

**Phase 5 : Gestion des contenus**

- Implémenter les années scolaires et les mois.
- Implémenter les vidéos.
- Implémenter les PDF.
- Sécuriser l'accès aux contenus.

**Phase 6 : Interfaces**

- Me proposer l'identité visuelle et attendre ma validation.
- Développer la page d'accueil.
- Développer le tableau de bord élève.
- Développer le tableau de bord administrateur.
- Vérifier le rendu sur un écran de téléphone (environ 360 px de large).

**Phase 7 : Tests et déploiement**

- Exécuter tous les tests.
- Corriger les erreurs.
- Comparer les hébergements possibles (gratuits et plan B payant).
- Préparer le déploiement et un guide de mise en ligne pas à pas.
- M'accompagner pour obtenir un lien public HTTPS.

### Consignes importantes

- Ne génère pas tout le projet en une seule fois.
- Commence par analyser mon environnement.
- Explique brièvement les décisions techniques importantes.
- Avant toute opération destructive (suppression de fichiers, `migrate:fresh`, réinitialisation de base), demande ma confirmation.
- Ne supprime ni ne modifie des projets existants sans autorisation.
- Ne publie aucun secret ou identifiant sensible, et ne les écris jamais dans un fichier versionné.
- Si une commande échoue, analyse l'erreur et propose une correction.
- Ne déclare pas une fonctionnalité terminée sans l'avoir vérifiée.
- Privilégie la simplicité, la sécurité et la maintenabilité.
