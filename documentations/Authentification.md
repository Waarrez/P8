# Comprendre l’authentification Symfony

## Où sont stockés les utilisateurs ?
Les utilisateurs sont stockés dans la table `User`.

## Comment s'authentifier ?
L’authentification se passe au niveau de la route `/login`. Vous devrez fournir votre nom d’utilisateur et mot de passe.

## Fichier de configuration : security.yaml
Dans un premier temps, il existe un fichier dans le dossier `config/packages`, appelé `security.yaml`. Ce fichier contient la configuration pour l’authentification de l’utilisateur.

## Contrôleur d'authentification : SecurityController.php
Ce fichier est le contrôleur du système d'authentification. Il s'agit principalement du fonctionnement interne de Symfony, vous ne devriez donc probablement pas modifier quoi que ce soit ici.

- La méthode `login` définit la route vers le formulaire de connexion.
- La méthode `logout` définit la route pour se déconnecter.

## Formulaire de connexion : login.html.twig
Enfin, le fichier `templates/security/login.html.twig` contient le formulaire de connexion.
