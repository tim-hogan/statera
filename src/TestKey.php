<?php
require dirname(__FILE__) . "/includes/classSecure.php";
require dirname(__FILE__) . "/includes/classInputParam.php";



function encrypt($v,$key)
{
	// Remove the base64 encoding from our key
	if ($key)
	{
		$flag = "FFFF";
		$data = $flag . (string) $v;
		$encryption_key = base64_decode($key);

        echo "Encrypt: key = {$encryption_key}<br/>";
		// Generate an initialization vector
		$iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
		echo "New iv = " . bin2hex($iv) . "<br/>";
		// Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
		$encrypted = openssl_encrypt($data, 'aes-256-cbc', $encryption_key, 0, $iv);
		// The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
		$result = urlencode(base64_encode($encrypted . '::' . $iv));

        echo "Encrypted = {$encrypted}<br/>";
        echo "Base64 =" . base64_encode($encrypted . '::' . $iv) . "<br/>";
        $bb = base64_encode($encrypted . '::' . $iv);
        echo "B64 encode decode = " .  base64_decode($bb) . "<br/>";
        echo "URLEncode {$result}<br/>";
        echo "URL Decode = " . urldecode($result) . "<br/>";
        return $result;
	}
	else
		return null;
}

	function decrypt($data,$key)
	{
		if ($key)
		{

			// Remove the base64 encoding from our key
			$encryption_key = base64_decode($key);
			echo "Encrypt: key = {$encryption_key}<br/>";
            echo "Decoded base64 data = " . base64_decode($data) . "<br/>";


        // To decrypt, split the encrypted data from our IV - our unique separator used was "::"
			list($encrypted_data, $iv) = explode('::', base64_decode($data), 2);

			$iv = substr($iv, 0, 16);
			if (strlen($iv) != 16)
			{
				echo "IV Length not 16<br/>";
				echo "IV  is " . bin2hex($iv) . "<br/>";
				return null;
			}

			$de =  openssl_decrypt($encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv);
			if (substr($de,0,4) == 'FFFF')
			{
				return substr($de,4,strlen($de)-4);
			}
		}
		return null;
	}


$key = "ff88945d52475bad8723fd378fb1b6226d4c3e44f1bbfbf3f6a159da965458a0";
$sk = base64_encode($key);
echo "Session Key: {$key}  Base64 = {$sk}<br/>";

echo "Inline result follows<br/>";
$v =  encrypt("w=cash", $sk) . "<br/>";
echo "v = {$v}<br/>";

$r = decrypt(urldecode($v), $sk);
echo "r = {$r}<br/>";

echo "External result follows<br/>";
$v = InputParam::encryptFromString("w=cash", $key);
echo "v = {$v}<br/>";
$inputParams = InputParam::load(urlencode($v), $key);
var_dump($inputParams);
?>