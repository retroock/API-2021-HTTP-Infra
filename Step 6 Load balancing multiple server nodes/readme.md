# Load balancing multiple server nodes

Nous allons utiliser traefik afin de faire du load balancing. Il s'agit d'une solution utilisable sur docker grâce à docker-compose. Comme traefik permet aussi d'avoir un reverse-proxy, nous allons remplacer le serveur du point 5 par celui-ci. Traefik permet aussi d'avoir un dashboard.

## Mis en place

Ajouter dans le docker file l'activation du module proxy_balancer

compléter le fichier config-template.php avec 

	<Proxy "balancer://">
		BalancerMember "<?php print "$dynamic_app"?>"
		BalancerMember "<?php print "$dynamic_app_bis"?>"
	</Proxy>
	
	<Proxy "balancer://api/zoo/">
		BalancerMember "<?php print "$static_app"?>"
		BalancerMember "<?php print "$static_app_bis"?>"
	</Proxy>

Si apache version est supérieur ou égal à la 2.4 rajouter lbmethod_byrequests dans le dockerfile