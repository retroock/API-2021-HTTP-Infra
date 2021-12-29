# Step 5: Dynamic reverse proxy configuration

## Création du fichier apache2-foreground

Ce fichier est exécuté à la fin du Dockerfile de l'image php:7.2-apache pour lancer apache2 en premier plan. Nous allons donc créer le notre afin de pouvoir récupérer les valeurs passées en paramètre de la ligne de commande de docker run. Nous avons copié le contenu du fichier `apache2-foreground` qui se trouvait dans le serveur et nous lui avons rajouté ces lignes : 
```
echo "Setup for the API lab..."
echo "Static app URL: $STATIC_APP"
echo "Dynamic app URL: $DYNAMIC_APP"
php /var/apache2/templates/config-template.php > /etc/apache2/sites-available/001-reverse-proxy.conf
```
## Création d'un fichier PHP

Création d'un dossier `templates` dans le même dossier que le Dockerfile du reverse proxy. A l'intérieur on crée un fichier `config-template.php`. 

### Contenu du fichier config-template.php
Le contenu de ce fichier ressemble beaucoup à ce  qu'on a dans le fichier `001-reverse-proxy.conf` ce qui est normal étant donné que l'on veut écraser ce fichier par le contenu ci-dessous.
```php
<?php
  $dynamic_app = getenv('DYNAMIC_APP');
  $static_app = getenv('STATIC_APP');
?>

<VirtualHost *:80>
	ServerName proxy.api.ch
	
	#Localisation des fichiers de logs
	ErrorLog /var/log/apache2/error.log
	CustomLog /var/log/apache2/access.log combined
	
	#Configuration pour accéder au site dynamique 
	ProxyPass '/api/zoo/' 'http://<?php print "$dynamic_app"?>/'
	ProxyPassReverse '/api/zoo/' 'http://<?php print "$dynamic_app"?>/'
	
	#Configuration pour accéder au site statique
	ProxyPass '/' 'http://<?php print "$static_app"?>/'
	ProxyPassReverse '/' '<?php print "$static_app"?>/'
</VirtualHost>
```

## Modifier le Dockerfile du proxy

Il faut maintenant copier ces fichiers dans les bons répertoires du serveur :
```Dockerfile
COPY apache2-foreground /usr/local/bin/
COPY templates /var/apache2/templates
```