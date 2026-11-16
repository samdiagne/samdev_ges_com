# 🍽️ Guide de Déploiement & Installation Client (Restaurant POS)

Ce guide décrit la procédure complète pour déployer l'application chez un nouveau client sur une machine Windows (PC de caisse ou serveur local).

---

## 📋 1. Prérequis sur la machine client

Avant de commencer, installez ces outils indispensables sur le poste client :

| Outil | Version recommandée | Lien de téléchargement |
|---|---|---|
| **XAMPP** | PHP 8.2 ou PHP 8.3 | [apachefriends.org](https://www.apachefriends.org/fr/index.html) |
| **Composer** | Version 2.x | [getcomposer.org/download](https://getcomposer.org/download/) |
| **Git for Windows** | Dernière version | [git-scm.com](https://git-scm.com/download/win) |
| **Node.js** *(optionnel si `public/build` est déjà inclus)* | Version 20+ LTS | [nodejs.org](https://nodejs.org/) |

> ⚠️ **Important pour PHP dans XAMPP :**
> Assurez-vous que le chemin PHP (`C:\xampp\php`) est bien ajouté à la variable d'environnement `PATH` de Windows, et que les extensions suivantes sont activées dans `C:\xampp\php\php.ini` :
> - `extension=curl`
> - `extension=fileinfo`
> - `extension=gd`
> - `extension=mbstring`
> - `extension=openssl`
> - `extension=pdo_mysql`
> - `extension=zip`

---

## 🚀 2. Procédure d'installation (Étape par étape)

### Étape 2.1 : Cloner le dépôt
Ouvrez le terminal (PowerShell ou Git Bash) dans le dossier web de XAMPP :
```bash
cd C:\xampp\htdocs
git clone <URL_DU_DEPOT_GIT> nom-du-restaurant
cd nom-du-restaurant
```

### Étape 2.2 : Créer la base de données
1. Lancez **XAMPP Control Panel** et démarrez **Apache** et **MySQL**.
2. Allez sur **phpMyAdmin** : `http://localhost/phpmyadmin`.
3. Cliquez sur **Nouvelle base de données** :
   - Nom : `resto_db` (ou le nom convenu)
   - Interclassement : `utf8mb4_unicode_ci`
   - Cliquez sur **Créer**.

### Étape 2.3 : Configuration du fichier `.env`
1. Copiez le fichier modèle :
   ```bash
   cp .env.example .env
   ```
2. Ouvrez `.env` avec le Bloc-notes ou VS Code et ajustez :
   ```env
   APP_NAME="Nom du Restaurant"
   APP_URL=http://localhost:8000

   DB_DATABASE=resto_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### Étape 2.4 : Lancement automatique du script d'installation
Double-cliquez simplement sur le fichier **`install.bat`** (ou lancez-le dans le terminal) :
```cmd
install.bat
```

Ce script exécute automatiquement :
1. `composer install`
2. `php artisan key:generate`
3. `php artisan storage:link`
4. `php artisan migrate --seed` (création des tables, rôles, permissions et admin)
5. `npm install && npm run build` (si Node.js est présent)
6. `php artisan optimize:clear`

---

## 🎨 3. Personnalisation de la Palette de Couleurs

Pour adapter l'interface aux couleurs du nouveau restaurant :

1. Ouvrez `tailwind.config.js` et modifiez les codes couleurs :
   ```javascript
   theme: {
       extend: {
           colors: {
               primary:   '#0284C7', // Couleur principale (boutons, badges)
               secondary: '#10B981', // Couleur secondaire
               accent:    '#38BDF8', // Accents
               dark:      '#1E293B', // Textes et fonds sombres
               light:     '#F8FAFC',
           },
       },
   }
   ```
2. Ouvrez `resources/views/layouts/app.blade.php` (lignes 92 et 256) pour adapter le dégradé de la sidebar avec votre nouvelle couleur `primary` :
   ```html
   style="background: linear-gradient(180deg, #0284C7 0%, #0369A1 100%)"
   ```
3. Recompilez :
   ```bash
   npm run build
   ```

---

## 🔑 4. Comptes d'accès initiaux

Une fois le seed terminé, deux comptes sont créés par défaut :

| Rôle | Email | Mot de passe | Accès |
|---|---|---|---|
| **Administrateur** | `admin@maffood.sn` *(modifiable)* | `Admin@2025!` | Tout le système, paramètres, utilisateurs |
| **Caissier / Gestionnaire** | `caisse@maffood.sn` *(modifiable)* | `Caisse@2025!` | Caisse POS, Ventes, Factures, Achats, Dépenses |

> 🔒 **Recommandation de sécurité :**  
> Dès la première connexion, rendez-vous dans le menu **Utilisateurs** pour changer l'adresse email et le mot de passe de l'administrateur, ou créez directement le compte nominatif du gérant.

---

## ⚙️ 5. Paramétrage Entreprise (Interface Web)

Connectez-vous avec le compte administrateur et rendez-vous dans **Admin $\rightarrow$ Paramètres** (`/parametres`) :
- Renseignez le nom officiel du restaurant.
- Uploadez le logo du restaurant.
- Numéro de téléphone & WhatsApp pour les commandes en ligne.
- Adresse physique et mentions légales (NINEA / RC).
- Préfixes de factures et devis.

---

## 🖥️ 6. Lancement au quotidien par le client

Pour que le caissier puisse démarrer la caisse en 1 clic :
1. Créez un raccourci sur le Bureau Windows pointant vers le fichier **`start.bat`**.
2. Renommez le raccourci : **« Démarrer Caisse Resto »** et attribuez-lui une belle icône.
3. Un double-clic dessus démarre le serveur local et ouvre automatiquement Google Chrome sur `http://localhost:8000` !
