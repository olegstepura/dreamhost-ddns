<?php
/**
 * @author Oleg Stepura <oleg.stepura [at] gmail.com>
 * @copyright Oleg Stepura <oleg.stepura [at] gmail.com>
 * @version $Id: random.php,v 343cbf4c5fe4 2011/04/14 15:35:59 C0BA $
 */

echo createRandomKey(64);

/**
 * Creates random passwords.
 * @author Oleg Stepura <oleg.stepura [at] gmail.com>
 * @version 1.0
 */
function createRandomKey($length)
{
	$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$key = "";
	for ($i = 0; $i < $length; $i++) {
		$key .= $chars{rand(0, strlen($chars)-1)};
	}
	return $key;
}