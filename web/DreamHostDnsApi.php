<?php
/**
 * @author Oleg Stepura <oleg.stepura [at] gmail.com>
 * @copyright Oleg Stepura <oleg.stepura [at] gmail.com>
 * @version $Id: DreamHostDnsApi.php,v 449176a34800 2011/04/14 15:32:14 C0BA $
 */

/**
 * DreamHostApi class
 * @author Oleg Stepura <oleg.stepura [at] gmail.com>
 * @version 1.0
 */
class DreamHostDnsApi
{
	/**
	 * API Url.
	 * @var string
	 */
	const API_URL = "https://api.dreamhost.com/";

	/**
	 * API key.
	 * @var string
	 */
	protected $key = '';

	/**
	 * Mail to send errors to.
	 * @var string
	 */
	protected $errorMail = '';

	/**
	 * Log file location.
	 * @var string
	 */
	protected $logFile = '';

	/**
	 * Flag indicating DDNS has errors.
	 * @var bool
	 */
	protected $hasErrors = false;

	/**
	 * Log messages.
	 * @var bool
	 */
	protected $messages = array();

	/**
	 * Flag indicating the result was already reported.
	 * @var bool
	 */
	protected $reported = false;

	/**
	 * Constructor.
	 * @param string $key
	 * @param string $errorMail
	 * @param string $logFile
	 */
	public function __construct($key, $errorMail, $logFile = '')
	{
		$this->errorMail = $errorMail;
		$this->logFile = $logFile;

		if (empty($key)) {
			$message = 'API key is empty. Cannot continue.';
			$this->error($message)->report();
			trigger_error($message, E_USER_ERROR);
			exit();
		}

		$this->key = $key;
	}

	/**
	 * Adds a record.
	 * @param string $record
	 * @param string $type
	 * @param string $value
	 * @return string
	 */
	public function addRecord($record, $type, $value)
	{
		$this->info("About to add $record of type $type with value '$value'");

		$params = array(
			'key' => $this->key,
			'cmd' => 'dns-add_record',
			'record' => $record,
			'type' => $type,
			'value' => $value
		);

		return $this->send($params);
	}

	/**
	 * Lists all records.
	 * @param string $record
	 * @param string $type
	 * @param string $value
	 * @return array
	 */
	public function listRecords($type, $editable = 1)
	{
		$this->info("About to list records of type $type (editable=$editable)");

		$params = array(
			'key' => $this->key,
			'cmd' => 'dns-list_records',
			'type' => $type,
			'editable' => $editable,
			'format' => 'json'
		);

		$result = $this->send($params, false);
		$data = @json_decode($result);

		if (@$data->result !== 'success') {
			$this->error('Json parsing error: ' . $result);
		}

		return is_array(@$data->data) ? $data->data : array();
	}

	/**
	 * Removes a record.
	 * @param string $record
	 * @param string $type
	 * @return string
	 */
	public function removeRecord($record, $type)
	{
		$this->info("About to remove $record of type $type");

		foreach ($this->listRecords($type) as $existingRecord) {
			if ($existingRecord->record === $record && $existingRecord->type === $type) {
				$value = $existingRecord->value;
				$params = array(
					'key' => $this->key,
					'cmd' => 'dns-remove_record',
					'record' => $record,
					'type' => $type,
					'value' => $value
				);

				return $this->send($params);
			}
		}

		$this->error("Record $record with type $type did not exist");

		return '';
	}

	/**
	 * Sends request to API server.
	 * @param array $data
	 * @return string
	 */
	public function send(array $data, $logResponse = true)
	{
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, self::API_URL);
		curl_setopt($curl, CURLOPT_PORT, 443);
		curl_setopt($curl, CURLOPT_SSLVERSION, 3);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		$dataResult = curl_exec($curl);

		$sentData = $data;
		$sentData['key'] = '***';

		if (curl_errno($curl)) {
			$this->error(
				'Error sending ' . var_export($sentData, true) .
				' via curl. Response: ' . $dataResult
			);
		} else {
			$this->info(
				'Sent ' . var_export($sentData, true) .
				' via curl. Response: ' .
				($logResponse ? $dataResult : '[cut]')
			);
		}

		curl_close($curl);
		return $dataResult;
	}

	/**
	 * Returns true if any error had happened.
	 * @return bool
	 */
	public function hasError()
	{
		return $this->hasErrors;
	}

	/**
	 * Adds error message.
	 * @param string $message
	 * @return self
	 */
	public function error($message)
	{
		$this->hasErrors = true;
		$this->log('ERROR: ' . $message);

		return $this;
	}

	/**
	 * Adds info message.
	 * @param string $message
	 * @return self
	 */
	public function info($message)
	{
		$this->log('INFO: ' . $message);

		return $this;
	}

	/**
	 * Logs messages.
	 * @param string $message
	 * @return self
	 */
	protected function log($message)
	{
		$this->messages[] = '[' . date('Y-m-d H:i:s') . '] ' . $message;

		return $this;
	}

	/**
	 * Updates record.
	 * @param string $record
	 * @param string $type
	 * @param string $value
	 * @return self
	 */
	public function updateRecord($record, $type, $value)
	{
		$success = "success";

		$result = $this->removeRecord($record, $type);

		if (substr($result, 0, strlen($success)) !== $success) {
			$this->error('Removing record error: ' . $result);
		}

		$result = $this->addRecord($record, $type, $value);

		if (substr($result, 0, strlen($success)) !== $success) {
			$this->error('Adding record error: ' . $result);
		}

		return $this;
	}

	/**
	 * Reports all messages to log and mail.
	 * @param bool $force
	 * @return self
	 */
	public function report($force = true)
	{
		if ($this->reported && !$force) {
			return $this;
		}

		$suffix = '';

		if ($this->hasErrors) {
			$suffix = ' ERRORS detected!';
		}

		if (!empty($this->errorMail)) {
			mail(
				$this->errorMail,
				'DDNS update result.' . $suffix,
				implode("\n", $this->messages)
			);
		}

		if (!empty($this->logFile)) {
			file_put_contents(
				$this->logFile,
				implode("\n", $this->messages) . "\n",
				FILE_APPEND
			);
		}

		$this->reported = true;

		return $this;
	}

	/**
	 * Do stuff on exit.
	 * @return void
	 */
	public function __destruct()
	{
		$this->report(false);
	}
}
