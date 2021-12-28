# Step 3: Reverse proxy with apache (static configuration)
Le reverse proxy mis en place ici n'est pas dynamique, nous écrivons donc l'adresse IP de nos serveurs que nous voulons accéder en dur.
## Trouver l'adresse IP de nos docker
Après avoir lancé les deux containers, effectuer cette ligne de commande :
``` shell
docker inspect <nom du docker> | grep -i ipaddress
```

## Création  d'un Dockerfile

```Dockerfile
FROM php:7.2-apache

COPY conf/ /etc/apache2/

RUN a2enmod proxy proxy_http
RUN a2ensite 000-* 001-*
```
Nous allons faire deux commande aux lancements du container afin d'activer deux modules de proxy et d'activer les deux sites que nous avons.
## Création du dossier conf
Voici l'arborescence de ce dossier :
<img src="./figures/arborescence.png" alt="arborescence"  />

Comme écrit dans le Dockerfile, nous allons copier le contenu de ce dossier dans le dossier de configuration de notre serveur apache. Les deux fichiers commençant par 000 et 001 contiennent les informations de configuration des sites. 

Le premier fichier est le fichier par défaut, nous le gardons car comme ça si le client ne précise pas qu'elle site il veut accéder, c'est ce fichier qui sera accédé. 

Le deuxième fichier contient la configuration de notre reverse proxy statique, c'est ici que nous allons décrire comment on peut accéder à nos sites internet.

### 000-default.conf

```
<VirtualHost *:80>
</VirtualHost>
```

### 001-reverse-proxy.conf

```
<VirtualHost *:80>
	ServerName proxy.api.ch
	
	#Localisation des fichiers de logs
	ErrorLog /var/log/apache2/error.log
	CustomLog /var/log/apache2/access.log combined
	
	#Configuration pour accéder au site dynamique 
	ProxyPass "/api/zoo/" "http://172.17.0.3:3000/"
	ProxyPassReverse "/api/zoo/" "http://172.17.0.3:3000/"
	
	#Configuration pour accéder au site statique
	ProxyPass "/" "http://172.17.0.2:80/"
	ProxyPassReverse "/" "http://172.17.0.2:80/"
</VirtualHost>
```
Comme dit précédemment cette configuration n'est pas optimal, en effet nous écrivons les adresses IP de nos serveurs en dur. Donc si nous redémarrons nos deux serveurs (dynamique et statique), rien ne nous garanti que docker leurs donnera les mêmes adresses IP et donc il faudra venir remodifier ce fichier, recréer l'image docker, etc... ce qui n'est vraiment pas pratique.

## Création de l'image

```shell
docker build -t apache_rp .
```
## Modification fichier hosts
Lorsque l'on lance un container avec cette image, nous n'arrivons pas y accéder avec le navigateur. Il faut donc modifier le fichier hosts de la machine.
Le fichier hosts se trouve à cet emplacement sous windows : `C:\Windows\System32\drivers\etc\hosts`

Ensuite ajouter cette ligne : `127.0.0.1 proxy.api.ch`. Il est maintenant possible d'accéder au site internet en passant par le proxy en utilisant proxy.api.ch:8080. Si l'on désire accéder à notre deuxième site il faut faire proxy.api.ch:8080/api/zoo/. 

Il est aussi important de noter que nous ne pouvons plus accéder aux sites sans passer par ce proxy. Car ces derniers n'ont pas de ports mappés avec notre ordinateur et ils sont donc bien inaccessibles.
