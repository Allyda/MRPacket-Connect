<?php

namespace MRPacket\Connect;

use MRPacket\Basic\MyCurl;
use MRPacket\Basic\Configloader;
use MRPacket\CrException;

/**
 * User authentication and token exchange class.
 *
 * @author	   MRPacket
 * @copyright  (c) 2024 MRPacket
 * @license    all rights reserved
 */
class Authentication extends Call
{
	private $token = null;
	private $userProfile;

	function __construct($shopFrameworkName, $shopFrameWorkVersion, $shopModuleVersion)
	{
		parent::__construct($shopFrameworkName, $shopFrameWorkVersion, $shopModuleVersion);

		$userProfile = array(
			'pk'			=> '',
			'username'		=> '',
			'first_name'	=> '',
			'last_name'		=> '',
			'is_active'		=> ''
		);
	}

	protected function refreshAuthToken($username, $password)
	{
		$curl = new MyCurl();
		$input = array(
			'username'	=> trim($username),
			'password'	=> trim($password)
		);

		$requestJSON = json_encode($input);
		if (!$requestJSON) {
			throw new CrException("Failed to encode JSON request. Bad data.");
		}

		$endpoint = Configloader::load('settings', 'mrpacket_server_domain');
		if (!$endpoint) {
			throw new CrException("Failed to load endpoint via Configloader.");
		}

		/** @todo */
		$endpoint .= 'URL_GET_TOKEN';
		$header 			= $this->buildHttpDefaultHeaders();
		$post				= true;
		$outputHeader		= true;
		$userName	= null;
		$password 	= null;
		$curlTimeoutSeconds = 5;
		$encoding			= "UTF-8";
		$skipBody			= false;
		$userAgent			= 'Connect b' . $this->build . ' ' . $this->shopFrameWorkName;
		$verfiySSLPeer		= ENVIRONMENT == 'DEV' ? 0 : 1;
		$verfiySSLHost		= ENVIRONMENT == 'DEV' ? 0 : 2;

		if (ENVIRONMENT == 'DEV') {
			$request = array(
				'header' 		=> implode("\n", $header),
				'body' 			=> $requestJSON,
				'last_url'		=> $endpoint
			);
			echo "REQUEST: (curl)<pre>" . var_export($request, true) . "</pre>\n";
		}

		$response = $curl->sendCurlRequest($endpoint, $requestJSON, $header, $post, $outputHeader, $userName, $password, $curlTimeoutSeconds, $encoding, $skipBody, $userAgent, $verfiySSLPeer, $verfiySSLHost);

		if (ENVIRONMENT == 'DEV') {
			echo "RESPONSE: (curl)<pre>" . var_export($response, true) . "</pre>";
		}

		$httpcode = $response['http_code'];
		if ($httpcode != 200) {
			throw new CrException("Unexpected status. Server responded with http code $httpcode.", $httpcode);
		}

		$result = json_decode($response['body'], true);
		if (!$result) {
			throw new CrException("Failed to decode JSON response: " . $this->getJSONLastError(), $httpcode);
		}

		$tokenUpdated = false;
		if (isset($result['token'])) {
			if (!empty($result['token'])) {
				$this->token = $result['token'];
				$tokenUpdated = true;
			}
		}

		if (!$tokenUpdated) {
			throw new CrException("Failed to update token. Empty or corrupted data.", $httpcode);
		}

		if (isset($result['pk'])) {
			$this->userProfile['pk'] = $result['pk'];
		}

		if (isset($result['username'])) {
			$this->userProfile['username'] = $result['username'];
		}

		if (isset($result['email'])) {
			$this->userProfile['email'] = $result['email'];
		}

		if (isset($result['first_name'])) {
			$this->userProfile['first_name'] = $result['first_name'];
		}

		if (isset($result['last_name'])) {
			$this->userProfile['last_name'] = $result['last_name'];
		}

		if (isset($result['is_active'])) {
			$this->userProfile['is_active'] = $result['is_active'];
		}

		return $this->token;
	}

	public function getAuthToken($username, $password)
	{
		if ($this->token === null) {
			return $this->refreshAuthToken($username, $password);
		} else {
			return $this->token;
		}
	}

	public function getUserProfile()
	{
		return $this->userProfile;
	}
}
