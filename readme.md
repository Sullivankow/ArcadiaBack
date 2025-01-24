🐘 Application Web du Zoo Arcadia - Mode d'emploi pour le déploiement en local du backend

🐾 Description
Cette application web est développée pour le Zoo Arcadia, situé en Bretagne près de la forêt de Brocéliande. Elle offre une expérience immersive aux visiteurs pour explorer les habitats, les animaux et les services du zoo, tout en reflétant ses valeurs écologiques.
Les outils intégrés permettent aux employés, vétérinaires, et administrateurs de gérer efficacement les opérations quotidiennes.

✨ Fonctionnalités principales

Pour les visiteurs :
Explorer les habitats, animaux, et services proposés.
Soumettre des avis (sous validation d’un employé).
Contacter le zoo via un formulaire dédié.

Pour les employés :
Valider ou rejeter les avis des visiteurs.
Gérer les services (ajouter, modifier, supprimer).
Suivre l'alimentation des animaux.

Pour les vétérinaires :
Rédiger des rapports sur les animaux (santé, alimentation).
Commenter et signaler l’état des habitats.
Accéder à l’historique alimentaire des animaux.

Pour les administrateurs :
Gérer les utilisateurs (employés et vétérinaires).
Administrer les données : services, habitats, et animaux.
Consulter des statistiques sur la popularité des animaux.

🚀 Déploiement du backend
Prérequis pour le backend :
PHP 8.1+ (compatible avec Symfony).
Composer pour la gestion des dépendances PHP.
Symfony CLI pour lancer le serveur.
MySQL via XAMPP ou un autre serveur SQL.
Étape 1 : Cloner le dépôt
Récupérez le projet backend depuis GitHub :

git clone https://github.com/Sullivankow/ArcadiaBack.git backend
cd backend

2 : Installer les dépendances
Installez les dépendances nécessaires avec Composer :

Dans le terminal de commande enrez :
composer install

3 : Configurer la base de données
Créer la base de données :

Démarrez XAMPP et activez le serveur MySQL.
Accédez à mysql et créez une base de données (ex. : zoo_arcadia).

Configurer l’URL de connexion à la base :

Modifiez ou créez un fichier .env.local dans le dossier backend avec les informations de votre bases de données

4 : Initialiser la base de données
Exécuter les migrations :

Dans le terminal de commande entrez :
php bin/console doctrine:migrations:migrate
(Optionnel) Charger des données initiales : Si des fixtures sont disponibles pour des données d’exemple :

Dans le terminal de commande entrez :
php bin/console doctrine:fixtures:load

5 : Configurer CORS
Assurez-vous que le fichier nelmio_cors.yaml autorise les requêtes provenant du frontend.
Par exemple, pour un frontend accessible sur http://localhost:3000, la configuration pourrait ressembler à ceci :

La configuration du fichier doit ressembler à celle-ci :
nelmio_cors:
defaults:
allow_origin: ['http://localhost:3000']
allow_methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS']
allow_headers: ['Content-Type', 'Authorization']
max_age: 3600

6 : Lancer le serveur backend
Démarrez le serveur Symfony :

Dans le terminal de commande entrez :
symfony server:start

Par défaut, l’API sera accessible sur http://127.0.0.1:8000.

7 : Résolution des problèmes courants
Erreur de connexion à la base de données :

Vérifiez les identifiants dans le fichier .env.local.
Assurez-vous que le serveur MySQL est bien démarré.

Problèmes de migrations :

Vérifiez que les entités sont bien synchronisées avec la structure de la base de données.

🔗 Remarque : Déploiement du frontend
Pour une expérience complète, déployez le frontend (Il se trouve dans son repository) et configurez-le pour pointer vers le backend à l’adresse http://127.0.0.1:8000/api/doc. Consultez le guide de déploiement du frontend pour plus d’informations.
