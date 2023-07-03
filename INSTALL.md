Pré-requis:

- Uitlisation d'une base de données MYSQL dont les informations correspondent à votre fichier de configuratin d'environnement ".env"
- Exécuté la commande "php artisan migrate" pour la mise en place tables dans la base de données

Chaque user est lié à une entrée de  "membre" qui contient ces informations. 
- Avoir créer un "user" avec le rôle "admin" dans la base de données et remplir une entrée de "membre" qui lui correspond


Mise à jour:

- Avant le premier lancement, exécuté un "composer install" et un "npm install" pour mettre à jours toutes les dépnedances


Lancement

- Deux terminal: Un avec la commande "php artisan serve" et l'autre avec "npm run dev"
