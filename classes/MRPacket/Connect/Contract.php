<?php

namespace MRPacket\Connect;

use MRPacket\Basic\MyCurl;
use MRPacket\Basic\Configloader;
use MRPacket\CrException;
use InvalidArgumentException;

/**
 * @author	   MRPacket
 * @copyright  (c) 2024 MRPacket
 * @license    all rights reserved
 */
class Contract extends Call
{
	protected $token = null;

	public function __construct($shopFrameworkName, $shopFrameWorkVersion, $shopModuleVersion, $token)
	{
		parent::__construct($shopFrameworkName, $shopFrameWorkVersion, $shopModuleVersion);

		if (empty($token)) {
			throw new InvalidArgumentException("Param 'token' must not be empty. Please call \Connect\Authentication to obtain auth token!");
		} else {
			$this->token = $token;
		}
	}

	public function create(ContractParcel $entry)
	{
		$curl = new MyCurl();

		$status = array(
			'success' 	=> false,
			'data' 		=> null,
			'errors'	=> []
		);

		$input = [];
		$prefix = 'receiver_';
		foreach ($entry->receiver as $fieldname => $value) {
			$input[$prefix . '' . $fieldname] = $value;
		}

		$prefix = 'order_';
		foreach ($entry->order as $fieldname => $value) {
			$input[$prefix . '' . $fieldname] = $value;
		}

		$prefix = 'package_';
		foreach ($entry->package as $fieldname => $value) {
			$input[$prefix . '' . $fieldname] = $value;
		}

		foreach ($entry->courier_contract_products as $ccp) {
			$input['courier_contract_products'][] = $ccp;
		}

		if (empty($input['order_reference'])) {
			$status['success']	= 0;
			$status['errors'][]	= "Invalid value for required field 'order_reference'. Field must not be empty.";
			return $status;
		}

		$requestJSON = json_encode($input);
		if (!$requestJSON) {
			throw new CrException("Failed to encode JSON request: " . $this->getJSONLastError());
		}

		$endpoint = Configloader::load('settings', 'mrpacket_server_domain');
		if (!$endpoint) {
			throw new CrException("Failed to load endpoint via Configloader.");
		}

		/** @todo */
		$endpoint .= 'URL_CREATE_PACKET';
		$header 			= $this->buildHttpDefaultHeaders($this->token);
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

		/* send data to server */
		$response = $curl->sendCurlRequest($endpoint, $requestJSON, $header, $post, $outputHeader, $userName, $password, $curlTimeoutSeconds, $encoding, $skipBody, $userAgent, $verfiySSLPeer, $verfiySSLHost);
		if (ENVIRONMENT == 'DEV') {
			echo "RESPONSE: (curl)<pre>" . var_export($response, true) . "</pre>";
		}

		$httpcode = $response['http_code'];
		if ($httpcode != 201) {
			throw new CrException("Unexpected status. Server responded with http code $httpcode.", $httpcode);
		}

		$result = json_decode($response['body'], true);
		if (!$result) {
			throw new CrException("Failed to decode JSON response: " . $this->getJSONLastError(), $httpcode);
		}

		$status['success']  = true;
		$status['data']		= $result;

		$vitalFields = array('pk', 'order_reference', 'tracking_code');
		foreach ($vitalFields as $key) {
			if (!isset($result[$key])) {
				$status['success']  = false;
				$status['errors'][]	= "Missing field '$key' in server response.";
			}
		}

		return $status;
	}

	public function update()
	{
		echo "currently not implemented!";
	}

	public function delete($pk)
	{
		$curl = new MyCurl();
		$status = array(
			'success' 	=> false,
			'data' 		=> null,
			'errors'	=> array()
		);

		$endpoint = Configloader::load('settings', 'mrpacket_server_domain');
		if (!$endpoint) {
			throw new CrException("Failed to load endpoint via Configloader.");
		}

		/** @todo */
		$endpoint .= 'URL_DELETE_PACKET';
		$header 			= $this->buildHttpDefaultHeaders($this->token);
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
				'body' 			=> '',
				'last_url'		=> $endpoint
			);
			echo "REQUEST: (curl)<pre>" . var_export($request, true) . "</pre>\n";
		}

		/* send data to server */
		$response = $curl->sendCurlRequest($endpoint, 'pk=' . $pk, $header, $post, $outputHeader, $userName, $password, $curlTimeoutSeconds, $encoding, $skipBody, $userAgent, $verfiySSLPeer, $verfiySSLHost);

		if (ENVIRONMENT == 'DEV') {
			echo "RESPONSE: (curl)<pre>" . var_export($response, true) . "</pre>";
		}

		$httpcode = $response['http_code'];
		if ($httpcode != 200) {
			throw new CrException("Unexpected status. Server responded with http code $httpcode.", $httpcode);
		}

		if ($result = json_decode($response['body'], true)) {
			$status['success']  = true;
			$status['data']		= $result;
		} else {
			throw new CrException("Failed to decode JSON response: " . $this->getJSONLastError(), $httpcode);
		}

		return $status;
	}
}
