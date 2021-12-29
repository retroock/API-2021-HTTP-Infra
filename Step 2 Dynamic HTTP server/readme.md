# Step2: Dynamic HTTP server with express.js

## 1. Création d'un fichier Dockerfile ayant comme contenu :

```Dockerfile
FROM node:16

COPY src/ /opt/app/

CMD [ "node", "/opt/app/index.js" ]
```
Créer dans le même répertoire que le fichier dockerfile un dossier src et un fichier index.js.
## 2. Installer NodeJS sur l'ordinateur
L'installation de nodejs permet par la même occasion d'installer npm et donc de pouvoir télécharger des packages.
## 3. Création d'un package JSON, installation de chance et installation d'express
Créer un package jason à l'aide de npm :
```bash
npm init
	package name : animals
	version : 0.1.0
	description : Send random name, birthYear, gender and animal type
npm install --save chance
npm install --save express
```
Mettre tous les fichiers générés (c'est à dire le fichier package.json et le dossier node_modules) dans le dossier src.

### 4. Ecrire un code NodeJS dynamique
Dans le fichier `index.js` se trouve le code qui permet d'initialiser un serveur à l'aide d'express. Ce serveur retourne du contenu aléatoire. Ce contenu est généré à l'aide de Chance.

### 5. Test du serveur
Création de l'image :
```Dockerfile
docker build -f ./Dockerfile -t my-nodejs-srv .
```
Création d'un docker  :
```Dockerfile
docker run -p 9090:3000 my-nodejs-srv
```


