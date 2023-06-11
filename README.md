# TITRE DU PROJET

Mon premier blog professionnel 2dbubbles
Il est développé en from scratch avec PHP 8.1.12, le design pattern MVC (Model Vue Controler) Active Record et le moteur de template TWIG.

## DESCRIPTION DU PROJET

Mon site web se décompose en deux grandes parties:

### FRONT

Les utilisateurs connectés à leur compte peuvent consulter les articles illustrant mes centres d'intérêt et les commenter.
Ils pourront consulter, modifier, supprimer leurs commentaires.
Ces commentaires seront soumis à validation.
Un formulaire de contact permettant de contacter l'administrateur du site.

### BACK

La deuxième est la partie administrative, réservée aux utilisateurs avec un droit d'accès.
Les utilisateurs connectés pourront gérer les articles création, modifications et suppression.
Les commentaires sont soumis à validation avant publication.
La gestion des comptes utilisateurs.

## PRÉREQUIS

- PHP 8.1
- MySQL ou Mariadb
- Composer version 2

## INSTALLATION

1. Clôner le projet depuis mon compte gitHub sur votre disque dur.
2. Ouvrez le projet dans votre éditeur de texte.
3. Installer les dépendances Composer.
```composer install```
4. Noter que le dossier vendor et le vendor/autoload.php script sont générés par Composer.
5. Créer votre base de données encodée `utf8mb4_general_ci`.
6. Importer la base de données dans votre SGBDR (Système de Gestion de Base de Données Relationnelle).
7. Copier le fichier `config.ini.dist` et le coller sous le nom de `config.ini`.
8. Renseigner les informations de connexion à la base de données.

## DOCUMENTATION À UTILISER

[Documentation Twig](https://twig.symfony.com/doc/)

[Docummentation PHP](https://www.php.net/docs.php)
