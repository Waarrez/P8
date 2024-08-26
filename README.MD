### Prérequis :
- PHP 8
- MYSQL
- Composer
- Symfony CLI

## INITIALISATION

- Télécharger le projet avec la commande : ``` git clone https://github.com/Waarrez/P8.git ```
- Dans le dossier du projet et faire la commande suivante : ``` composer install ```

- Insérer le fichier p8.sql qui se trouve dans le dossier sql dans phpmyadmin
- Ensuite fait un copier coller du .env et renommer le en .env.local et connecter votre BDD avec cette ligne ! ``` DATABASE_URL=mysql://root:@localhost:3306/P8?charset=utf8mb4 ```
- Importer ensuite la structure de base de donnée en faisant ``` php bin/console d:m:m --no-interaction ```
- Démarrer le serveur avec la commande suivante : ``` symfony serve -d ```
- Suivez les étapes si dessous

## CONNEXION

- Pour vous connecter en administrateur : ``` username: admin | password : admin ```

[![PHP Version](https://img.shields.io/badge/php-8.0-blue)](https://www.php.net/releases/8.0/en.php)
![Composer Version](https://img.shields.io/badge/Composer-2.6.6-blue)

