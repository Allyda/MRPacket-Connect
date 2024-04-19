<?php

namespace MRPacket\Basic;

define("TIMEOUT_CURL_REGULAR", 5);

/**
 * Class for issuing calls via lib cURL.
 *
 * @author	   MRPacket
 * @copyright  (c) 2024 MRPacket
 * @license    all rights reserved
 */
class MyCurl
{
	/**
	 * Call an endpoint to transmit a payload (e.g. JSON or XML) matching different server specific prerequisites.
	 * 
	 * @param String $endpoint				URL of the resource to be called.
	 * @param String $payload				The data to be transmitted.
	 * @param Array $header					List of headers to be encompassed in the request.
	 * @param Bool $post					Use POST method for the call. (optional)
	 * @param Bool $outputHeader			Include the header in the body output for requests. (optional)
	 * @param String $userName				Username to be used for auth. (optional)
	 * @param String $password				Password to be used for auth. (optional)
	 * @param Int $curlTimeoutSeconds		Nr of seconds for CURL to wait for response until timeout. (optional)
	 * @param String $encoding				Encoding of the payload. (optional)
	 * @param Bool $skipBody				Only check for the response header. (optional)
	 * @param String $userAgent				User agent to expose to the remote server. (optional)
	 * @param Bool $verfiySSLPeer			Do verify peer. (optional)
	 * @param Bool $verfiySSLHost			Do verify host. (optional)
	 * @param Bool $skipDNS					Tell cURL to manually resolve host on same server. (optional)
	 * @return array
	 * FORMAT OF RESULT:
	 * array(
	 * 		'header' 		=> ...
	 * 		'body'	 		=> ...
	 * 		'curl_error'	=> ...
	 * 		'http_code'		=> ...
	 * 		'last_url'		=> ...
	 * )
	 */
	public function sendCurlRequest($endpoint, $payload, $header, $post = TRUE, $outputHeader = FALSE, $userName = NULL, $password = NULL, $curlTimeoutSeconds = null, $encoding = "UTF-8", $skipBody = FALSE, $userAgent = null, $verfiySSLPeer = 1, $verfiySSLHost = 2, $skipDNS = null)
	{
		$result = array(
			'header' 		=> '',
			'body' 			=> '',
			'curl_error' 	=> '',
			'http_code' 	=> '1000',
			'last_url'		 => ''
		);

		if (empty($endpoint)) {
			return $result;
		}

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $endpoint);
		if ($outputHeader === TRUE) {
			curl_setopt($ch, CURLOPT_HEADER, TRUE);
		} else {
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
		}

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, ($curlTimeoutSeconds != null) ? $curlTimeoutSeconds : 4);

		if ($curlTimeoutSeconds !== NULL) {
			curl_setopt($ch, CURLOPT_TIMEOUT, $curlTimeoutSeconds);
		} else {
			curl_setopt($ch, CURLOPT_TIMEOUT, TIMEOUT_CURL_REGULAR);
		}

		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		if ($userAgent !== null) {
			curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
		}

		curl_setopt($ch, CURLOPT_POST, $post);
		if (!empty($payload)) {
			curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		}

		if ($skipDNS) {
			$myhostname = str_replace(array('http://', 'https://'), '', $endpoint);
			$pos 		= strpos($myhostname, '/');
			$myhostname = substr($myhostname, 0, $pos);
			$myhost = array($myhostname . ':443:127.0.0.1');
			curl_setopt($ch, CURLOPT_RESOLVE, $myhost);
		}

		if ($header != NULL) {
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		}

		curl_setopt($ch, CURLINFO_HEADER_OUT, true);

		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verfiySSLPeer);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $verfiySSLHost);

		if ($userName && $password) {
			curl_setopt($ch, CURLOPT_USERPWD, $userName . ":" . $password);
		}

		if ($encoding !== FALSE) {
			curl_setopt($ch, CURLOPT_ENCODING, $encoding);
		}

		if ($skipBody) {
			curl_setopt($ch, CURLOPT_NOBODY, 1);
		}

		$response = curl_exec($ch);
		$error = curl_error($ch);

		if ($error != "") {
			$result['curl_error'] = $error;
			return $result;
		}

		$header_size 			= strlen($response) - curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD);
		$result['header'] 		= substr($response, 0, $header_size);
		$result['body'] 		= substr($response, $header_size);
		$result['http_code'] 	= curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$result['last_url'] 	= curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);

		return $result;
	}

	/**
	 * @param String $background_process	The url that you want to run in background.
	 * @param Bool $debug					Values: 2 == show verbose debug info, 0 or 1 = ignore.		(optional)
	 * @param Bool $skipDNS					True = will route to localhost via CURLOPT_RESOLVE instead DNS based address resolution. (optional)
	 * @return boolean
	 */
	function sendCurlRequestAsync($background_process = '', $debug = 0, $skipDNS = true, $timeout = 100)
	{
		$ch = curl_init($background_process);
		curl_setopt_array($ch, array(
			CURLOPT_HEADER 			=> 0,
			CURLOPT_RETURNTRANSFER	=> true,
			CURLOPT_FOLLOWLOCATION  => 1,
			CURLOPT_NOSIGNAL 		=> 1,
			CURLOPT_TIMEOUT_MS 		=> $timeout,
			CURLOPT_VERBOSE 		=> 1,
			CURLOPT_HEADER 			=> 1,
			CURLOPT_SSL_VERIFYPEER	=> 0,
			CURLOPT_SSL_VERIFYHOST	=> 0,
		));

		if ($skipDNS) {
			$myhostname = str_replace(array('http://', 'https://'), '', $background_process);
			$pos 		= strpos($myhostname, '/');
			$myhostname = substr($myhostname, 0, $pos);
			$myhost = array($myhostname . ':443:127.0.0.1');
			curl_setopt($ch, CURLOPT_RESOLVE, $myhost);
		}

		$out = curl_exec($ch);
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($out, 0, $header_size);
		$body = substr($out, $header_size);
		curl_close($ch);

		return $body;
	}
}
