# Step 4: AJAX requests with JQuery

## Modifier le fichier Dockerfile du serveur statique (optionel)

Ajouter cette ligne : 

```Dockerfile
RUN apt-get update && \
	apt-get install -y vim
```

Pour permettre d'utiliser `vi`qui est un éditeur de texte.

## Connection sur le serveur statique

Utiliser la commande `docker exec -it <nom du container> /bin/bash` afin de se connecter sur le serveur statique.

## Ajouter un script js

A l'aide de vi, éditer le fichier index.html et rajouter à la fin du fichier :
```html
<script src="js/zoo.js"></script>
```
Ajouter aussi cette ligne quelque part dans l'index, nous avons choisi de le mettre en dessous du titre de la page. 
```html
<div>
	<span> Voici notre mascotte : </span>
	<span class="zoo">  </span>
</div>
```

Puis dans le dossier `js`créer un fichier nommé `zoo.js` et y ajouter une fonction permettant de récupérer des données de notre serveur dynamique. Notre fonction se trouve [ici](./../Static HTTP server/src/js/zoo.js).

Nous avons donc maintenant du contenu dynamique qui change toute les deux secondes sur notre site statique ! (Ne pas oublier de re build l'image du serveur statique)