<?php

echo "CHECKING SYSTEM REQUIREMENTS... <br/><br/>";

echo "PHP Version: ";

$v = phpversion();
$versionParts = explode('.', $v);

$versionOK = false;
if (intval($versionParts[0]) > 5) {
	$versionOK = true;
} else {
	if (intval($versionParts[0]) == 5) {
		if (intval($versionParts[1]) >= 3) {
			$versionOK;
		}
	}
}

echo $versionOK ? 'OK' : 'ERROR';
if (!$versionOK) {
	echo "(your PHP Version: $v)";
}
echo "</br>";

echo 'cURL: ', function_exists('curl_version') ? 'OK' : 'ERROR';
