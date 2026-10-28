# Guide de Déploiement et d'Exploitation — Educ'Academy V1

Ce guide vous accompagne pas à pas pour déployer Educ'Academy sur un hébergement web (gratuit ou économique) et assurer les sauvegardes régulières, même sans accès SSH ni Node.js sur le serveur.

---

## 1. Choix de l'hébergement web

### Option A : Hébergements gratuits (pour démarrer à 0 €)
1. **Alwaysdata (Recommandé en gratuit) :**
   - **Avantages :** 100 Mo d'espace, support PHP 8.3/8.4, MySQL natif, certificat SSL HTTPS gratuit Let's Encrypt automatique, configuration facile du répertoire racine vers `public/`, terminal SSH disponible même sur l'offre gratuite.
   - **Limites :** 100 Mo d'espace (suffisant pour démarrer avec les vidéos sur YouTube et quelques dizaines de PDF légers).
2. **InfinityFree / ByetHost :**
   - **Avantages :** Espace disque plus grand, gratuit, MySQL inclus.
   - **Limites :** Pas d'accès SSH, configuration `public_html` nécessitant une adaptation du fichier `.htaccess`, mise en veille possible en cas d'inactivité.

### Option B : Plan B — Hébergements abordables (Recommandé pour la sérénité)
1. **Hostinger (Offre Single / Premium - env. 2 à 3 € / mois) :**
   - **Avantages :** Espace NVMe rapide (50 à 100 Go), SSL gratuit, sauvegardes hebdomadaires automatiques, support PHP 8.4 à jour, gestionnaire de fichiers en ligne ultra-simple.
2. **o2switch (Offre unique - 7 € HT / mois) :**
   - **Avantages :** Hébergeur français de référence, ressources illimitées, support très réactif, cPanel complet avec choix de version PHP en 1 clic.

---

## 2. Préparation du projet en local (sur votre ordinateur)

Toutes les compilations et installations doivent être préparées en local avant l'envoi :

### Étape 2.1 : Compiler les assets finaux
Dans votre terminal local :
```powershell
npm run build
```
Cela génère les fichiers finaux minifiés dans le dossier `public/build/`. Le serveur de production n'aura donc **jamais besoin de Node.js ni de npm**.

### Étape 2.2 : Préparer les dépendances PHP de production
Générez un dossier `vendor` propre sans les outils de développement (PHPUnit, Faker) :
```powershell
composer install --no-dev --optimize-autoloader
```

### Étape 2.3 : Exporter la base de données MySQL locale
1. Ouvrez votre **Panneau XAMPP** et cliquez sur **Admin** en face de MySQL (ou ouvrez `http://localhost/phpmyadmin`).
2. Sélectionnez la base de données `educ_academy` dans la colonne de gauche.
3. Cliquez sur l'onglet **Exporter** en haut.
4. Choisissez la méthode **Rapide** (Format : SQL) et cliquez sur **Exporter**.
5. Conservez le fichier téléchargé (ex : `educ_academy.sql`).

---

## 3. Mise en ligne sur le serveur (via Gestionnaire de fichiers ou FTP)

### Étape 3.1 : Structure des dossiers sur l'hébergeur

#### Cas 1 : L'hébergeur permet de choisir la racine du site (ex: Alwaysdata, cPanel, Hostinger)
- Définissez le dossier racine du domaine (Document Root) sur : `/public`
- Déposez l'ensemble des fichiers de votre projet (y compris `vendor`, `app`, `config`, `storage`, etc.) à la racine de votre compte.

#### Cas 2 : L'hébergeur impose un dossier `public_html` fixe
Pour ne jamais exposer votre fichier `.env` ou vos dossiers privés :
1. Créez un dossier nommé `educ_core` au même niveau que `public_html`.
2. Déplacez **tous les fichiers** du projet dans `educ_core`, **sauf** le contenu du dossier `public/`.
3. Déplacez tout le contenu de votre dossier `public/` (fichiers `index.php`, `.htaccess`, dossiers `build/`, `images/`) directement à l'intérieur de `public_html`.
4. Modifiez la ligne 12 du fichier `public_html/index.php` pour pointer vers le nouveau chemin :
   ```php
   require __DIR__.'/../educ_core/vendor/autoload.php';
   $app = require_once __DIR__.'/../educ_core/bootstrap/app.php';
   ```

---

## 4. Configuration de la base de données et du `.env` de production

### Étape 4.1 : Importer la base de données sur l'hébergeur
1. Dans le cPanel ou l'espace client de votre hébergeur, créez une base de données MySQL (notez bien le nom de la base, l'utilisateur et le mot de passe créé).
2. Ouvrez **phpMyAdmin** chez votre hébergeur.
3. Sélectionnez votre nouvelle base et cliquez sur l'onglet **Importer**.
4. Sélectionnez le fichier `educ_academy.sql` exporté à l'étape 2.3 et lancez l'importation.

### Étape 4.2 : Créer le fichier `.env` de production
Sur le serveur, créez le fichier `.env` (à partir de votre `.env.example`) avec les paramètres de production stricts :

```ini
APP_NAME="Educ'Academy"
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:1DAQf+MffmcPSYbSdxQ3GegeG5XVgWHExAX6Vjsn8y4=
APP_URL=https://votre-domaine.com

APP_LOCALE=fr
APP_FALLBACK_LOCALE=fr
APP_TIMEZONE=Africa/Ouagadougou

DB_CONNECTION=mysql
DB_HOST=localhost # ou l'adresse fournie par l'hébergeur
DB_PORT=3306
DB_DATABASE=nom_de_la_base_chez_hebergeur
DB_USERNAME=utilisateur_cree
DB_PASSWORD=mot_de_passe_securise

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_SECURE_COOKIE=true

FILESYSTEM_DISK=local
PDF_MAX_SIZE_MB=20
```

> [!CAUTION]
> En production, veillez impérativement à ce que `APP_DEBUG=false`. Cela empêche d'afficher des erreurs techniques détaillées aux visiteurs en cas d'incident.

### Étape 4.3 : Permissions des répertoires
Assurez-vous que les dossiers suivants ont les droits d'écriture (CHMOD `775` ou `755`) :
- `storage/` (et tous ses sous-dossiers : `storage/app/private/documents`, `storage/framework/`, `storage/logs/`)
- `bootstrap/cache/`

---

## 5. Activation du certificat HTTPS et partage

1. Dans le panneau de votre hébergeur, activez le certificat SSL gratuit (Let's Encrypt).
2. Activez l'option **Forcer la redirection HTTPS** afin que toutes les connexions soient sécurisées.
3. Testez l'ouverture de votre plateforme sur smartphone : `https://votre-domaine.com`.
4. Vous pouvez désormais partager ce lien directement à vos élèves de Terminale sur le groupe WhatsApp !

---

## 6. Procédure simple de sauvegarde régulière (à faire soi-même)

Pour ne jamais perdre les données des élèves et les documents :

### Sauvegarde 1 : Les données (Élèves, Vidéos, Statuts)
- **Fréquence recommandée :** Une fois par mois ou après chaque validation d'inscriptions massives.
- **Action :** Ouvrez phpMyAdmin chez l'hébergeur, cliquez sur votre base, puis **Exporter** &rarr; Téléchargez le fichier SQL et conservez-le sur votre ordinateur ou Google Drive.

### Sauvegarde 2 : Les fichiers PDF privés
- **Dossier à sauvegarder :** `storage/app/private/documents/`.
- **Action :** Téléchargez ce dossier via le gestionnaire de fichiers ou FTP pour en garder une copie locale.
