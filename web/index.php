<?php
/**
 * @author Oleg Stepura <oleg.stepura [at] gmail.com>
 * @copyright Oleg Stepura <oleg.stepura [at] gmail.com>
 * @version $Id$
 */ 

require dirname(__FILE__) . "/DreamHostDnsApi.php";
require dirname(__FILE__) . "/DreamHostDnsUpdater.php";

$updater = new DreamHostDnsUpdater(array(
	'dreamhost_api_key' => 'DREAMHOST_API_KEY', // Get one at https://panel.dreamhost.com/?tree=home.api
	'email' => '', // Place your email address here to receive notifications
	'log_file_path' => '', //dirname(__FILE__) . '/../../ddns.log',
	'personal_key' => 'YOUR_PERSONAL_KEY', // generate some and place here
	'ddns_domains' => array( // Place desired domains in this list below
		'your.domain.com',
		'*.your.domain.com',
	)
));

$updater->run();