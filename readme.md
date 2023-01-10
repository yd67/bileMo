
# BileMo Api - Projet Symfony OpenClassrooms


![Logo](https://user.oc-static.com/upload/2016/11/17/14793813656252_shutterstock_143808508.jpg)


## Description du projet 

Projet n°7 de la formation [Développeur d'application - PHP / Symfony](https://openclassrooms.com/fr/paths/500-developpeur-dapplication-php-symfony#path-tabs).
## Contexte

BileMo est une entreprise offrant toute une sélection de téléphones mobiles haut de gamme.

Vous êtes en charge du développement de la vitrine de téléphones mobiles de l’entreprise BileMo. Le business modèle de BileMo n’est pas de vendre directement ses produits sur le site web, mais de fournir à toutes les plateformes qui le souhaitent l’accès au catalogue via une API (Application Programming Interface). Il s’agit donc de vente exclusivement en B2B (business to business).

Il va falloir que vous exposiez un certain nombre d’API pour que les applications des autres plateformes web puissent effectuer des opérations.
## Description du besoin 


 Après une réunion dense avec le client, il a été identifié un certain nombre d’informations. Il doit être possible de :

    - consulter la liste des produits BileMo ;
    - consulter les détails d’un produit BileMo ;
    - consulter la liste des utilisateurs inscrits liés à un client sur le site web ;
    - consulter le détail d’un utilisateur inscrit lié à un client ;
    - ajouter un nouvel utilisateur lié à un client ;
    - supprimer un utilisateur ajouté par un client.

Seuls les clients référencés peuvent accéder aux API. Les clients de l’API doivent être authentifiés via OAuth ou JWT.
## Installation du projet

  **Etape 1 : Cloner le Repository sur votre serveur.**

  **Etape 2 : Installer les dépendances .**

   ```http 
  composer install
  ```
  **Etape 3 : Création de la base de données.**
  
  Dans le fichier .env (racine) changer le " DATABASE_URL " ,et lancer la 
  commande suivante afin de créer votre base de données.

 ```http 
  php bin/console doctrine:database:create
  ```
  Effectué la migration de la base avec la commande :

  ```http 
  php bin/console make:migration
  ```

  suivie de la commande :

  ```http 
   php bin/console doctrine:migrations:migrate
  ```


  **Etape 4 : Remplir la bdd de données d'exemple** 
 
 lancer la commande :
  ```http 
  php bin/console doctrine:fixtures:load
  ```

  l'ensemble des Clients crée ont pour mot de passe "test98"
  

   **Etape 5 : Généré votre clé public et clé privé Jwt** 
 
 Afin de généré votre clé privé lancer la commande :
  ```http 
 openssl genpkey -out config/jwt/private.pem -aes256 -algorithm rsa -pkeyopt rsa
_keygen_bits:4096
  ```

  Afin de Généré votre clé public lancer la commande
```http
openssl pkey -in config/jwt/private.pem -out config/jwt/public.pem
 -pubout

```

Une “passphrase” vous sera demandée. Cette passphrase va servir de clef pour l’encodage/décodage du token.

il vous restera plus qu'au ajout les informations suivante dans votre .env

```http
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE= <VOTRE PASSPHARE>
```




 😄 c'est terminé. 

 
 


## Auteur

- [Darius Yvens ](https://github.com/yd67)

[![portfolio](https://img.shields.io/badge/my_portfolio-000?style=for-the-badge&logo=ko-fi&logoColor=white)](https://www.darius-yvens.com/)
[![linkedin](https://img.shields.io/badge/linkedin-0A66C2?style=for-the-badge&logo=linkedin&logoColor=white)](https://fr.linkedin.com/in/yvens-darius)
