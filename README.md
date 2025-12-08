# 🚆 Défi Full stack - Routage de Train & Statistiques

Application complète permettant de calculer la distance entre deux stations de train.
Il est possible de visualiser les statistiques par code analytique via l'accès à un espace sécurisé.
__-

## 📌 Fonctionnalités
🛠 Backend (Symfony + ApiPlatform)
- API REST conforme à la spécification OpenApi fournie. Dans le cadre de la gestion des comptes utilisateurs, il a toutefois été enrechi.
- Calcul du plus court chemin entre deux stations de train via l'algorithme de Dijkstra et intégration des stations entre les deux destinations.
- Gestion du réseau ferroviaire via le fichier JSON de stations et de distances.
- Exceptions métier et gestion des erreurs.
- Récupération des statistiques par code analytique.
- Sécurité JWT pour protéger l'endpoint de récupération des statistiques.

🎯 Frontend (Vue.js)
- Formulaire de calcul de la distance entre deux stations de train.
- Affichage du trajet et des stations parcourues.
- Formulaires de création de compte et de connexion.
- Accès à un espace sécurisé pour consulter les statistiques.
- Recherche et visualisation des statistiques  par code analytique dans un grahique Chart.js.


## 🧩 Technologies & Composants principaux
L'application repose sur un ensemble de technologies et composants principaux permettant de garantir sa performance, sa sécurité et sa maintenabilité.

### 🔙 Backend – Symfony (API REST)

#### 🏛 Architecture

- Clean Architecture (Ports & Adapters)
Séparation stricte entre Domain, Application, Infrastructure et UI.

- Domain-Driven Design (DDD light)
Entités, Value Objects, Exceptions métier, interfaces de repository.

#### 📚 Principaux composants
 - PHP 8.4
 - Symfony 7.4
 - ApiPlatform 4.2
 - Doctrine ORM 3.5
 - Firebase PHP-JWT 6.11
 - PhpUnit 12.4
 - Phpstan 2.1

### 🎨 Frontend – Vue.js 3 + TypeScript

#### 🧱 Structure
- Composition API
- Vue Router
- Service TypeScript pour l'accès API REST.


####  📚 Principaux composants
- TypeScript 5.9
- Vue 3.5
- Vue Router 4.6
- Tailwind CSS 4.1
- Axios 1.13
- Chart.js 4.5
- Fuse.js 7.1
- Vite 2.9

## 🌐 Infrastructure

- Traefik 2.10 utilisé comme reverse proxy
- FrankenPHP: serveur d'application pour le backend
- Mariadb 10.6: base de données de l'application
- Certificats auto générés
- Environnement 100% Docker

## 🏗 Architecture du projet

```defi-fullstack-app/
    |__ backend/
    |      |__ src/
    |      |     |__ DataFixtures/
    |      |     |__ Phpstan/
    |      |     |__ Route/
    |      |     |    |__ Application/
    |      |     |    |     |__ CalculateRoute/
    |      |     |    |     |__ GetStats/
    |      |     |    |__ Domain/
    |      |     |    |     |__ Entity/
    |      |     |    |     |__ Exception/
    |      |     |    |     |__ Repository/
    |      |     |    |     |__ Service/
    |      |     |    |     |__ ValueObject/
    |      |     |    |__ Infrastructure/
    |      |     |    |     |__ Doctrine/
    |      |     |    |     |    |__ Mapping/
    |      |     |    |     |    |__ Type/
    |      |     |    |     |__ Repository/
    |      |     |    |     |__ Service/
    |      |     |    |__ UI/
    |      |     |          |__ Http/
    |      |     |               |__ Rest/
    |      |     |                    |__ V1/
    |      |     |                        |__ Controller/
    |      |     |                        |__ Formatter/
    |      |     |                        |__ Input/
    |      |     |                        |__ Resource/ 
    |      |     |__ Security
    |      |     |    |__ Application/
    |      |     |    |     |__ CreateUser/
    |      |     |    |     |__ Login/
    |      |     |    |__ Domain/
    |      |     |    |     |__ Entity/
    |      |     |    |     |__ Exception/
    |      |     |    |     |__ Repository/
    |      |     |    |     |__ Service/
    |      |     |    |     |__ ValueObject/
    |      |     |    |__ Infrastructure/
    |      |     |    |     |__ Context/
    |      |     |    |     |__ Doctrine/
    |      |     |    |     |    |__ Mapping/
    |      |     |    |     |    |__ Type/
    |      |     |    |     |__ EventSubscriber/
    |      |     |    |     |__ Repository/
    |      |     |    |     |__ Service/
    |      |     |    |__ UI/
    |      |     |          |__ Http/
    |      |     |               |__ Rest/
    |      |     |                          |__ Controller/
    |      |     |                          |__ Input/
    |      |     |                          |__ Resource/
    |      |     |__ Shared/
    |      |          |__ Application/
    |      |          |__ Domain/
    |      |          |    |__ Aggregate/
    |      |          |    |__ Bus/
    |      |          |    |__ Exception/
    |      |          |    |__ ValueObject/
    |      |          |__ Infrastructure/
    |      |          |    |__ Bus/
    |      |          |    |__ Doctrine/
    |      |          |    |    |__ Type/
    |      |          |    |__ Repository/
    |      |          |    |__ Service/
    |      |          |__ UI/
    |      |                |__ Http/
    |      |                     |__ Rest/
    |      |__ tests/
    |      |__ Dockerfile
    |__ certs/
    |__ frontend/
    |     |__ src/
    |     |      |__ tests__/
    |     |      |__ components/
    |     |      |__ data/
    |     |      |__ router/
    |     |      |__ services/
    |     |      |__ types/
    |     |      |__ views/
    |     |__ public/
    |     |__ Dockerfile
    |__ traefik/
    |__ docker-compose.yml
    |__ Makefile
```

## 🚀 Lancer l’application en local

✅ Prérequis
- Docker Engine >= 20.10
- Docker Compose >= 1.29

### Récupération du projet
```
git clone https://github.com/farochell/defi-fullstack-app.git
```

### ▶️ Démarrage du projet

Une fois le projet récupéré, se placer dans le dossier récupéré.

Créer et modifier un fichier .env à partir du fichier **.env.example.**

```
  cp .env.example .env
```
Vous pouvez renseigner les valeurs de des variables d'environnement contenus dans ce fichier **.env**.
```
    MYSQL_ROOT_PASSWORD=password
    MYSQL_DATABASE=symfony
    MYSQL_USER=symfony
    MYSQL_PASSWORD=password
```

🚀 Avec Docker

Une fois le fichier .env renseigné vous pouvez lancer la commande suivante:

``` 
docker compose up -d
```  
Le projet est maintenant accessible sur les domaines suivants :

- http://localhost:5173 (application frontend)
- http://localhost:8000/api/docs (documentation API)

🌐 Fichiers hosts (optionnel)

En ajoutant les lignes suivantes dans le fichier hosts de votre machine,
vous pouvez accéder aux applications via les domaines suivants :
``` 
127.0.0.1   api.defifullstack.com
127.0.0.1   app.defifullstack.com
127.0.0.1   traefik.defifullstack.com
```
Le projet est maintenant accessible sur les domaines suivants :
- https://api.defifullstack.com
- https://app.defifullstack.com

## 🧪 Tests & Couverture
Un fichier Makefile est disponible pour faciliter les tests et la couverture du code.

###  Backend
Avant de lancer les tests, il faut au préalable installer la base de données de test via la commande suivante à la racine du projet:

#### Base de données

``` 
make db-init-test 
```  
#### Fixtures

La commande suivante permet de lancer les fixtures de test :
``` 
make fixtures
```  
 #### Tests
``` 
make test
``` 
#### Tests avec couverture
``` 
make test-coverage
```  
Le rapport de couverture est généré dans le dossier backend/reports/coverage.

###  Frontend

#### Tests
``` 
make vitest
``` 
#### Tests avec couverture
``` 
make vitest-coverage
``` 
Le rapport de couverture est généré dans le dossier frontend/coverage.

## 🧹 Qualité du code

- Analyse avec PhpStan (backend)
``` 
make phpstan
``` 

- Analyse avec lint (frontend)
``` 
make lint
``` 
