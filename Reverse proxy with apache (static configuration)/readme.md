# Step 3: Reverse proxy with apache (static configuration)
Le reverse proxy mis en place ici n'est pas dynamique, nous écrivons donc l'adresse IP de nos serveurs à protéger en dur. Il ne s'agit pas d'une méthode optimal mais nous la changerons plus tard.
## Trouver l'adresse IP de nos docker
Après avoir lancé les deux containers, effectuer cette ligne de commande :
``` 
docker inspect <nom du docker> | grep -i ipaddress
```

## Création  d'un Dockerfile
