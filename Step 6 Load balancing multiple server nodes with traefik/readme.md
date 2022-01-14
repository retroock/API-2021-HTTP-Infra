# Step 6 additional steps

Nous avons utilisé Traefik avec docker-compose afin de faire ces trois point supplémentaires :
- Load balancing: multiple server nodes
- Load balancing: round-robin vs sticky sessions
- Dynamic cluster management

En effet Traefik permet d'effectuer ces trois steps d'un coup. Le load balancing est géré automatiquement par Traefik, pour le sticky session il s'agira d'ajouter une simple ligne de configuration dans le docker-compose et pour le dynamic cluster Traefik gère aussi cette partie automatique. De plus un dashboard est mis à disposition afin d'avoir une vue d'ensemble du réseau.

## Configuration du docker-compose

Plusieurs moyen peuvent être utilisé pour mettre en place Traefik, comme par exemple utiliser plusieurs fichier de configuration en plus du docker-compose. (Traefik.yml qui contiendra la configuration dites statique, config.yml qui contiendra la configuration dites dynamique, etc...) mais il est plus pratique de tout faire uniquement dans le docker-compose.

```dockerfile
version: "3.7"

services: # permet de spécifier les dockers que nous voulons lancer
  traefik:
    image: "traefik:v2.5" # nom de l'image désirée (qui sera récupérée sur docker hub)
    container_name: "traefik
    hostname: "traefik" 
    # active le dashboard
    command:
     - "--api.insecure=true"
     - "--api.dashboard=true"
     - "--providers.docker"
     - "--log.level=DEBUG"
    ports:
      - "80:80" # port d'écoute des requêtes entrantes
      - "8080:8080" # port pour le dashboard
    volumes: # permet de spécifier des fichiers qui doivent être chargés dans le container
      - "/var/run/docker.sock:/var/run/docker.sock:ro" # permet d'avoir docker comme provider
    
    # partie où l'on va déclarer les dockers qui vont accompagner Traefik
  staticsrv: 
    build: 
      context: ./server_static # //chemin du dockerfile
    labels: # "règle" qui va permettre de configurer le docker
      - "traefik.enable=true" # permet au docker d'être visible dans Traefik
      
      # permet de configurer le Hostname par lequel on a accès à ce serveur. 
      - "traefik.http.routers.staticsrv.rule=Host(`proxy.api.ch`)" 
      
      # comme le serveur statique doit utiliser les sticky sessions nous allons les activer grâce à cette ligne
      - "traefik.http.services.staticCookies.loadbalancer.sticky.cookie=true" 
      
  dynamicsrv: 
    build: 
      context: ./server_dynamic/ # //chemin du dockerfile
    labels:
      - "traefik.enable=true"
      
      # la règle pour hostname est un peu plus complex car ici pour atteindre le site dynamique il faut utiliser /api/zoo/ donc nous devons rajouter le PathPrefix
      - "traefik.http.routers.dynamicsrv.rule=Host(`proxy.api.ch`) && PathPrefix(`/api/zoo/`)" 
      
      # notre serveur dynamique s'attend à ce que l'on se connecte sur la racine or avec la configuration qu'on a fait jusqu'à présent avec Traefik nous allons faire notre requête sur /api/zoo/ il faut donc enlever ce préfix afin de ne pas avoir de problème
      - "traefik.http.middlewares.dynamicsrvpathstrip.stripprefix.prefixes=/api/zoo"
      
      # pour mettre en place le middlewares spécifier au dessus, il faut l'actvier dans les routes
      - "traefik.http.routers.dynamicsrv.middlewares=dynamicsrvpathstrip@docker"
```
Nous avons délibérément choisit de ne pas donner de nom aux dockers des serveurs statiques et dynamiques car cela allait nous empêcher de lancer plusieurs serveurs d'un coup. 

Afin de lancer les différents docker voici la commande à effectuer : `docker-compose up -d --scale staticsrv=2 --scale dynamicsrv=2`

Cette commande va nous permettre de lancer un container Traefik et deux containers statique et dynamique. Cela va nous permettre de tester le Load-Balancing et de tester le cluster management.

## Validation du bon fonctionnement

Plusieurs tests ont été effectués afin de vérifier le bon fonctionnement du cluster :

### Accéder au dashboard
Le premier est d'essayer d'accéder au dashboard via l'adresse : `proxy.api.ch:8080`.

### Vérifier la détection des différents serveurs
Dans le dashboard, nous pouvons cliquer sur la section `http services` qui va lister tous les services qui ont été trouvé sur le réseaux. Voici ce que nous avons :

![http services](https://github.com/retroock/API-2021-HTTP-Infra/blob/master/Step%206%20Load%20balancing%20multiple%20server%20nodes%20with%20traefik/figures/capture1.PNG) 

Nous pouvons voir que le `dynamicsrv-traefik@docker` possède deux serveurs en loadbalancer et pareil pour le `staticCookies@docker`. Cela veut bien dire que nos quatre serveurs se sont lancés. En cliquant sur ces services nous pouvons accéder aux adresses IP des serveurs et aux différentes configurations.

### Vérification de la gestion du sticky cookie
Toujours dans `http services`, si nous accédons au service `staticCookies@docker`. Nous pouvons voir que le sticky cookie sont activés. Il est possible en modifiant le docker-compose de préciser les cookies que l'on doit garder et/ou en rajouter.

Si nous accédons au service `dynamicsrv-traefik@docker` nous pouvons voir que ce sticky cookie n'est pas actif sur ces serveurs.

### Test de l'accès aux sites
Maitenant nous pouvons regarder si les sites sont accessibles en y accédant via l'adresse : `proxy.api.ch/` et `proxy.api.ch/api/zoo/` et nous pouvons voir qu'ils sont bien accessibles.

### Test dynamic cluster
Comme dit précédemment Traefik se met à jour automatique si des noeuds apparaissent ou disparaissent dans le réseau. Nous pouvons donc désactiver un serveur dynamique et statique afin de voir si dans le menu `http services` ils y sont toujours.

![http services](https://github.com/retroock/API-2021-HTTP-Infra/blob/master/Step%206%20Load%20balancing%20multiple%20server%20nodes%20with%20traefik/figures/capture2.PNG) 

Nous pouvons voir que les services sont toujours présent mais il n'y a plus qu'un serveur présent sur chacun d'eux. Donc Traefik à bien vu que ces serveurs n'étaient plus accessible. Et si nous réactivons ces serveurs nous verrons qu'ils vont être de nouveau présent dans Traefik.
