<?php
  $dynamic_app = getenv('DYNAMIC_APP');
  $static_app = getenv('STATIC_APP');
  $dynamic_app_bis = getenv('DYNAMIC_APP_BIS');
  $static_app_bis = getenv('STATIC_APP_BIS');
?>

<VirtualHost *:80>
	ServerName proxy.api.ch
	
	#Localisation des fichiers de logs
	ErrorLog /var/log/apache2/error.log
	CustomLog /var/log/apache2/access.log combined
	
	#Configuration pour accéder au site dynamique 
	ProxyPass '/api/zoo/' 'balancer://dynamicApp'
	ProxyPassReverse '/api/zoo/' 'balancer://dynamicApp'
	
	
	#Configuration pour accéder au site statique
	ProxyPass '/' 'balancer://staticApp/'
	ProxyPassReverse '/' 'balancer://staticApp/'
	
	<Proxy balancer://dynamicApp>
		BalancerMember 'http://<?php print "$dynamic_app"?>'
		BalancerMember 'http://<?php print "$dynamic_app_bis"?>'
		ProxySet lbmethod=byrequests
	</Proxy>
	
	<Proxy balancer://staticApp>
		BalancerMember 'http://<?php print "$static_app"?>'
		BalancerMember 'http://<?php print "$static_app_bis"?>'
		ProxySet lbmethod=byrequests
	</Proxy>
	

</VirtualHost>