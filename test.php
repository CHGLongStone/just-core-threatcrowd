<?php 

$THREATCROWD = new JCORE\SERVICE\WRAPPER\THREATCROWD2_API;
$TCargs = array(
	'TYPE' => 'email', #email, domain, ip, antivirus, file
	'VALUE' => 'william19770319@yahoo.com',
);
echo $THREATCROWD->getReport($TCargs); 


?>