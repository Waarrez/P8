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


## CONTRIBUTION AU PROJET

Pour contribuer au projet merci de lire les information ci-dessous :

1. Forker et cloner le dépôt
   Utilisez le bouton "Fork" pour copier ce dépôt sur votre compte GitHub.
   Clonez votre fork localement avec la commande :
   bash
   Copier le code
   git clone https://github.com/votre-utilisateur/projet.git
2. Créer une branche pour vos modifications
   Créez une nouvelle branche pour vos changements :
   bash
   Copier le code
   git checkout -b nom-de-branche
3. Apporter des modifications
   Apportez les modifications souhaitées tout en respectant le style de code et les bonnes pratiques du projet.
4. Tester vos modifications
   Assurez-vous que tout fonctionne correctement en exécutant les tests du projet.
5. Soumettre une Pull Request
   Poussez votre branche sur votre fork et ouvrez une Pull Request vers le dépôt principal. Expliquez clairement les modifications apportées.
   Merci d'avance pour vos contributions !



[![PHP Version](https://img.shields.io/badge/php-8.0-blue)](https://www.php.net/releases/8.0/en.php)
![Composer Version](https://img.shields.io/badge/Composer-2.6.6-blue)
[![SymfonyInsight](https://insight.symfony.com/projects/0f2de786-c11d-43cb-958e-6fa3ae7299c2/big.svg)](https://insight.symfony.com/projects/0f2de786-c11d-43cb-958e-6fa3ae7299c2)
[![Maintainability](https://api.codeclimate.com/v1/badges/2c48b986db898253dfbf/maintainability)](https://codeclimate.com/github/Waarrez/P8/maintainability)

