# Step1: Static HTTP server with apache httpd

## 1. Création d'un fichier dockerfile ayant comme contenu :
```dockerfile
FROM php:7.2-apache
COPY src/ /var/www/html/
```
Ce contenu à été trouvé sur docker hub : https://hub.docker.com/_/php/ dans la section "Images variant". s'agit de l'image officiel implémentant déjà php.

## 2. Exécuter la commande dans un cmd :
```dockerfile 
docker run -d -p 80:80 php:7.2-apache
```
Cette étape n'est pas nécessaire, elle sert juste à télécharger l'image que nous désirons et pouvoir déjà faire un premier test afin de regarder si notre docker fonctionne bien. Normalement lorsqu'on
essaie d'accéder à la page internet un message comme quoi nous n'avons pas le droit d'accéder à ce serveur devrait s'afficher car notre serveur ne comporte aucune page `.html` à afficher.

## 3. Créer un dossier src dans le même répertoire que le fichier Dockerfile

## 4. Y mettre le contenu d'un site
nous avons choisi ce site https://startbootstrap.com/previews/sb-admin-2

## 5. Créer une nouvelle image
Attention il faut se trouver dans le répertoire du docker file afin d'exécuter cette commande :
```dockerfile
docker build -f ./Dockerfile -t my-apache-php .
```
Le `-f ./Dockerfile` n'est pas nécessaire mais il peut résoudre des problèmes liés au fait que docker ne trouve pas le Dockerfile. Cette commande sera à utiliser à chaque fois qu'on met à jour notre site internet. Le `-t` est là pour nommer notre image.

## 6. Run le container
Maintenant que nous créé notre image nous pouvons relancer la commande du point 2 mais remplaçant le nom par le nom spécifié au point 5.
```dockerfile 
docker run -d -p 80:80 my-apache-php
```

## 7. Configuration d'apache
Pour pouvoir visualiser la configuration d'apache, il faut:
1. Entrer en commande dans le docker:
```dockerfile
docker exec -it docker_name bash
```
2. Aller à l'endroit ou est le fichier de configuration:
```dockerfile
cd conf
```
3. Visualiser le fichier de configuration:
```dockerfile
cat httpd.conf
```
   