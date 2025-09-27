<?php

$docroot = $_SERVER["DOCUMENT_ROOT"];
$ip = $_SERVER["REMOTE_ADDR"];
$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);

echo "Document root ist: $docroot<br>";

echo "IP Adresse: $ip<br>";

echo "Hostname: $host<br>";  

phpinfo(INFO_GENERAL);

phpinfo(INFO_CONFIGURATION);

phpinfo(INFO_MODULES);

phpinfo(INFO_ENVIRONMENT);

phpinfo(INFO_VARIABLES);

phpinfo(INFO_ALL);

?>