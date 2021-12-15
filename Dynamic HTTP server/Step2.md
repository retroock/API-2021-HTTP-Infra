Créer une image Dockerfile avec ce contenu : 
```Dockerfile
FROM node:16

COPY src/ /opt/app/

CMD [ "node", "/opt/app/index.js" ]
```
Installer nodejs
Créer un package jason à l'aide de npm, installer chance avec npm et mettre dans le fichier src

écrire un code en nodeJS et le mettre dans un fichier index.js

faire la commande docker build pour tester le bon fonctionnement 

installer express à l'aide de npm et le mettre dans le fichier src

