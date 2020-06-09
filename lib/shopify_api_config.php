<?php
	/*
		ShopifyAPI Config File
		You can find your API Key, and Secret in your Shopify partners account (http://www.shopify.com/partners/)
	*/

	define('API_KEY', 'EXAMPLEAPIKEY');
	define('SECRET', 'EXAMPLEAPIKEY');
	define('FORMAT', 'xml');
	define('GZIP_ENABLED', true); // set to false if you do not want gzip encoding. If false GZIP_PATH is not needed to be set
	define('GZIP_PATH', '/tmp'); // path for gzip decoding (this file will need write permissions)
	define('USE_SSL', true);
    define('DBNAME', 'shopify3');
    define('DBHOST', 'localhost');
    define('DBUSER', 'maju');
    define('DBPASSWORD', '1234maju');

	/* These values only need to be set if USE_SSL is true and the API cannot verify the certificate */
	define('USE_SSL_PEM', false); //set to true if pem file is needed
	define('CA_FILE', './cacert.pem');
		
	/*
		Note that all XML tags with an - in the tag name are returned with a _ (underscore) in JSON	
	*/
?>
