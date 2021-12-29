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