# 🌐 GUIDE DE DÉPLOIEMENT & INSTALLATION — svmdev_ges_com
> **Outil tout-en-un de Gestion Commerciale, Caisse POS, Facturation & Vitrine Web intégrée.**  
> *Adaptable à tous les secteurs d'activité : commerces, boutiques, supérettes, restaurants, services, distribution.*

---

## 🎯 1. Vue d'ensemble du système

**svmdev_ges_com** est une suite de gestion commerciale complète et moderne qui regroupe :
- 🖥️ **Caisse & Point de Vente (POS)** : Interface tactile et rapide avec gestion des sessions, monnaie, écarts et paiements mixtes (Espèces, Wave, Orange Money, Carte).
- 📦 **Catalogue & Gestion des Stocks** : Produits, catégories, gestion des variantes, paliers de tarifs dégressifs groupés.
- 💼 **Ventes, Devis & Facturation** : Génération instantanée de tickets de caisse Z, factures et devis normalisés en PDF.
- 🚚 **Achats & Gestion Fournisseurs** : Commandes d'achats, règlements fournisseurs, gestion des mouvements de stock.
- 💳 **Dépenses d'exploitation** : Suivi des charges avec ventilation par catégorie de dépenses.
- 👥 **Clients & Fidélité** : Base clients, historiques d'achats et comptes clients.
- 🛍️ **Site Vitrine & Shop en Ligne intégré** : Tunnel de commande client avec panier dynamique et notification WhatsApp / Passerelle PayDunya.

---

## 📦 2. Prérequis & Pack d'Installation Rapide

Pour équiper la machine d'un client rapidement sans télécharger les outils un par un sur internet :

### Option A — Téléchargement des outils via pack centralisé
Vous pouvez mettre à disposition de vos techniciens ou clients un dossier zippé (ex: sur Google Drive / Dropbox / clé USB) contenant les installeurs officiels :
- `1_xampp-windows-x64-8.2.x-installer.exe`
- `2_Composer-Setup.exe`
- `3_Git-64-bit.exe`
- `4_node-v20-x64.msi` *(optionnel car les fichiers de production `public/build` sont déjà compilés dans le dépôt)*

### Option B — Téléchargements directs officiels
| Outil | Version requise | Lien direct |
|---|---|---|
| **XAMPP** | PHP 8.2 ou 8.3 | [apachefriends.org](https://www.apachefriends.org/fr/index.html) |
| **Composer** | Version 2.x | [getcomposer.org/download](https://getcomposer.org/download/) |
| **Git for Windows** | Dernière version | [git-scm.com](https://git-scm.com/download/win) |

> ⚙️ **Vérification rapide dans XAMPP :**  
> Dans `C:\xampp\php\php.ini`, vérifiez que ces extensions sont activées (sans point-virgule `;` au début) :  
> `extension=curl`, `extension=fileinfo`, `extension=gd`, `extension=mbstring`, `extension=openssl`, `extension=pdo_mysql`, `extension=zip`.

---

## 🚀 3. Installation chez le client en 4 étapes

### Étape 3.1 : Cloner le projet dans XAMPP
Ouvrez PowerShell ou l'invite de commandes :
```powershell
cd C:\xampp\htdocs
git clone https://github.com/samdiagne/samdev_ges_com.git mon-commerce
cd mon-commerce
```

### Étape 3.2 : Créer la Base de Données
1. Démarrez **Apache** et **MySQL** dans le panneau de contrôle XAMPP.
2. Rendez-vous sur `http://localhost/phpmyadmin`.
3. Cliquez sur **Nouvelle base de données** :
   - Nom : `commerce_db`
   - Interclassement : `utf8mb4_unicode_ci`
   - Cliquez sur **Créer**.

### Étape 3.3 : Configurer le `.env`
1. Copiez le fichier d'exemple :
   ```powershell
   copy .env.example .env
   ```
2. Modifiez le nom de la base dans `.env` :
   ```env
   APP_NAME="Nom de l'Entreprise"
   APP_URL=http://localhost:8000

   DB_DATABASE=commerce_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

### Étape 3.4 : Exécuter l'installation automatique
Double-cliquez simplement sur :
👉 **`install.bat`**

Le script configure automatiquement l'application, les clés de sécurité, le lien de stockage et crée la structure de base.

---

## 🎨 4. Personnalisation Visuelle avec l'IA (Prompt Magique)

Grâce à notre mise à jour du template, **vous n'avez plus besoin de modifier le code HTML ni de toucher aux dégradés de la sidebar** ! La sidebar et tous les éléments de l'application s'adaptent désormais automatiquement à la couleur configurée dans Tailwind.

Pour obtenir une palette harmonieuse adaptée au logo du client, copiez-collez le prompt ci-dessous dans **ChatGPT** ou **Claude** en joignant l'image du logo de votre client :

```text
Bonjour ! Je déploie une application de gestion commerciale (ERP/POS) pour mon client dont voici le logo ci-joint.

Analyse les couleurs dominantes de ce logo et propose-moi 3 variations de palettes professionnelles et harmonieuses.
Pour chaque variation, fournis-moi UNIQUEMENT le bloc de code Tailwind à copier-coller dans mon fichier tailwind.config.js sous cette forme :

colors: {
    primary:   '#XXXXXX', // Couleur principale (boutons, en-têtes, sidebar)
    secondary: '#XXXXXX', // Couleur secondaire d'accent
    accent:    '#XXXXXX', // Couleur de mise en valeur (badges, alertes)
    dark:      '#1E293B', // Texte foncé
    light:     '#F8FAFC', // Fond clair
},

Veille à un excellent contraste de lisibilité.
```

Une fois le code obtenu, remplacez simplement les couleurs dans [`tailwind.config.js`](file:///C:/xampp/htdocs/maf-food/tailwind.config.js), puis compilez :
```powershell
npm run build
```
*(L'ensemble de l'interface, des boutons, badges et de la sidebar prendra instantanément les nouvelles couleurs !)*

---

## 🔑 5. Identifiants de Connexion par Défaut

Dès que la base est initialisée, deux comptes sécurisés sont immédiatement fonctionnels :

| Profil | Identifiant (Email) | Mot de passe | Rôle & Permissions |
|---|---|---|---|
| **Administrateur** | `admin@my-business.sn` | `Admin@2025!` | Accès total : Paramètres, Utilisateurs, Rapports, Audit |
| **Caisse / Gestionnaire** | `caisse@my-business.sn` | `Caisse@2025!` | Ventes, Caisse POS, Facturation, Stocks, Achats |

> 💡 **Conseil :** Dès la première connexion, rendez-vous dans le menu **Admin $\rightarrow$ Utilisateurs** pour mettre à jour l'email et le mot de passe de l'administrateur.

---

## 🏢 6. Paramétrage Entreprise

Connectez-vous avec le compte administrateur et rendez-vous dans **Admin $\rightarrow$ Paramètres** (`/parametres`) pour personnaliser :
- Nom commercial & Slogan
- Logo officiel de l'entreprise
- Numéros Téléphone & WhatsApp pour les réceptions de commandes
- Adresse physique et mentions légales (NINEA / Registre du Commerce)
- Devise (ex: `FCFA`, `€`, `$`) et préfixes de factures / devis (`FAC-`, `DEV-`)

---

## 💻 7. Lancement Quotidien pour le Client

Pour rendre l'utilisation enfantine pour le client et ses caissiers :
1. Créez un raccourci Windows sur le Bureau pointant vers **`start.bat`**.
2. Renommez le raccourci : **« Ouvrir la Caisse »** ou **« Gestion Commerciale »**.
3. Associez-lui une icône personnalisée.
4. Au clic, le serveur démarre et Google Chrome s'ouvre automatiquement sur la page de connexion !
