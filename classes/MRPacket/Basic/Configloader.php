<?php

namespace MRPacket\Basic;

/**
 * Class for loading config files.
 *
 * @author	   MRPacket
 * @copyright  (c) 2024 MRPacket
 * @license    all rights reserved
 */
class Configloader
{
	static function load($file, $key)
	{
		$data = include(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . $file . '.php');

		if (isset($data[$key])) {
			return $data[$key];
		}

		return null;
	}
}
