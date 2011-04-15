<?php
/**
 * @author Oleg Stepura <oleg.stepura [at] gmail.com>
 * @copyright Oleg Stepura <oleg.stepura [at] gmail.com>
 * @version $Id: DreamHostDnsUpdater.php,v 449176a34800 2011/04/14 15:32:14 C0BA $
 */

/**
 * Updater class.
 * Does all the work for you.
 * @author Oleg Stepura <oleg.stepura [at] gmail.com>
 * @version 1.0
 */
class DreamHostDnsUpdater
{
	/**
	 * Config.
	 * @var array
	 */
	protected $config = array(
		'dreamhost_api_key' => '',
		'email' => '',
		'log_file_path' => '',
		'personal_key' => '',
		'ddns_domains' => array()
	);

	/**
	 * @var DreamHostDnsApi
	 */
	protected $dnsApi;

	/**
	 * Constructor.
	 * @param array $config
	 */
	public function __construct(array $config)
	{
		$this->config = array_merge($this->config, $config);

		$this->dnsApi = new DreamHostDnsApi(
			$this->config['dreamhost_api_key'],
			$this->config['email'],
			$this->config['log_file_path']
		);

		if (empty($this->config['personal_key'])) {
			$message = 'Personal key is empty. Cannot continue.';
			$this->dnsApi->error($message)->report();
			trigger_error($message, E_USER_ERROR);
			exit();
		}
	}

	/**
	 * Runs updating procedure.
	 * @return void
	 */
	public function run()
	{
		$ip = $_SERVER['REMOTE_ADDR'];
		$result = 'Denied from ' . $ip;
		$success = false;

		if (isset($_GET['key'])) {
			if ($_GET['key'] === $this->config['personal_key']) {
				foreach ($this->config['ddns_domains'] as $domain) {
					$this->dnsApi->updateRecord(
						$domain,
						'A',
						$ip
					);
				}
				$result = 'Finished from ' . $ip;
				$this->dnsApi->info($result);
				$success = true;
			}
		}
		if (!$success) {
			$this->dnsApi->error($result);
		}

		$this->dnsApi->report();

		if ($this->dnsApi->hasError()) {
			$result .= ' [ERRORS occurred while processing!]';
		}

		echo $result;
	}
}