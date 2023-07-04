<?php
set_time_limit(0);

define('CRYPT_RSA_MODE_INTERNAL', 1);
define('CRYPT_RSA_MODE_OPENSSL', 2);
define('CRYPT_RSA_OPENSSL_CONFIG', dirname(__FILE__) . '/openssl.cnf');

define('BIGINT_MODE_GMP', 'GMP');
define('BIGINT_MODE_BCMATH', 'BCMath');

define('CRYPT_RSA_ASN1_INTEGER',   2);
define('CRYPT_RSA_ASN1_BITSTRING', 3);
define('CRYPT_RSA_ASN1_SEQUENCE', 48);

require_once "binstr.inc.php";

class RSA
{
	public $key_length;
	public $private_exp;
	public $public_exp;
	public $modulus;
	public $_random_generator = null;
	
	function RSA($key_len)
	{
		$this->key_length = intval($key_len);
		$this->_math_obj = $this->LoadMath();
		$this->_random_generator = create_function('', '$a=explode(" ",microtime());return(int)($a[0]*1000000);');

		if ( !defined('CRYPT_RSA_MODE') ) {
			if (extension_loaded('openssl') && version_compare(PHP_VERSION, '4.2.0', '>=') && file_exists(CRYPT_RSA_OPENSSL_CONFIG))
				define('CRYPT_RSA_MODE', CRYPT_RSA_MODE_OPENSSL);
			else
				define('CRYPT_RSA_MODE', CRYPT_RSA_MODE_INTERNAL);
		}
	}

 	function LoadMath()
	{
		if (defined('BIGINT_MODE'))
			$math_wrappers = array(BIGINT_MODE);
		else
			$math_wrappers = array(BIGINT_MODE_GMP, BIGINT_MODE_BCMATH);

		foreach ($math_wrappers as $wrapper)
		{
			$obj = $this->LoadMathWrapper($wrapper);
			if ($obj !== FALSE)
			{
				if (!defined('BIGINT_MODE'))
					define('BIGINT_MODE', $wrapper);
				return $obj;
			}
		}
		return NULL;
	}

	function LoadMathWrapper($wrapper_name)
	{
		$class_name = 'Crypt_RSA_Math_' . $wrapper_name;
		$class_filename =  dirname(__FILE__) . "/" . $wrapper_name . ".php";
		@include_once $class_filename;
		if (!class_exists($class_name)) {
			return FALSE;
		}
		$obj = new $class_name;
		if ($obj->errstr) {
			return FALSE;
		}
		return $obj;
	}

	function _string_shift(&$string, $index = 1)
	{
		$substr = bin_substr($string, 0, $index);
		$string = bin_substr($string, $index);
		return $substr;
	}

	function _decodeLength(&$string)
	{
		$length = ord($this->_string_shift($string));
		if ( $length & 0x80 ) { // definite length, long form
			$length&= 0x7F;
			$temp = $this->_string_shift($string, $length);
			list(, $length) = unpack('N', bin_substr(str_pad($temp, 4, chr(0), STR_PAD_LEFT), -4));
		}
		return $length;
	}

	function ParseKey($key)
	{
		$lines = explode("\n", trim($key));
		//remove last and first line
		unset($lines[count($lines)-1]);
		unset($lines[0]);
		//join remaining lines
		$key = base64_decode(implode('', $lines));

		$components = array();

		if (ord($this->_string_shift($key)) != CRYPT_RSA_ASN1_SEQUENCE) {
			return false;
		}
		if ($this->_decodeLength($key) != bin_strlen($key)) {
			return false;
		}

		$tag = ord($this->_string_shift($key));
		/* intended for keys for which OpenSSL's asn1parse returns the following:

			0:d=0  hl=4 l= 631 cons: SEQUENCE
			4:d=1  hl=2 l=   1 prim:  INTEGER           :00
			7:d=1  hl=2 l=  13 cons:  SEQUENCE
			9:d=2  hl=2 l=   9 prim:   OBJECT            :rsaEncryption
		   20:d=2  hl=2 l=   0 prim:   NULL
		   22:d=1  hl=4 l= 609 prim:  OCTET STRING */

		if ($tag == CRYPT_RSA_ASN1_INTEGER && bin_substr($key, 0, 3) == "\x01\x00\x30") {
			$this->_string_shift($key, 3);
			$tag = CRYPT_RSA_ASN1_SEQUENCE;
		}

		if ($tag == CRYPT_RSA_ASN1_SEQUENCE) {
			/* intended for keys for which OpenSSL's asn1parse returns the following:

				0:d=0  hl=4 l= 290 cons: SEQUENCE
				4:d=1  hl=2 l=  13 cons:  SEQUENCE
				6:d=2  hl=2 l=   9 prim:   OBJECT            :rsaEncryption
			   17:d=2  hl=2 l=   0 prim:   NULL
			   19:d=1  hl=4 l= 271 prim:  BIT STRING */
			$this->_string_shift($key, $this->_decodeLength($key));
			$tag = ord($this->_string_shift($key)); // skip over the BIT STRING / OCTET STRING tag
			$this->_decodeLength($key); // skip over the BIT STRING / OCTET STRING length
			// "The initial octet shall encode, as an unsigned binary integer wtih bit 1 as the least significant bit, the number of
			//  unused bits in the final subsequent octet. The number shall be in the range zero to seven."
			//  -- http://www.itu.int/ITU-T/studygroups/com17/languages/X.690-0207.pdf (section 8.6.2.2)
			if ($tag == CRYPT_RSA_ASN1_BITSTRING) {
				$this->_string_shift($key);
			}
			if (ord($this->_string_shift($key)) != CRYPT_RSA_ASN1_SEQUENCE) {
				return false;
			}
			if ($this->_decodeLength($key) != bin_strlen($key)) {
				return false;
			}
			$tag = ord($this->_string_shift($key));
		}
		if ($tag != CRYPT_RSA_ASN1_INTEGER) {
			return false;
		}

		$length = $this->_decodeLength($key);
		$temp = $this->_string_shift($key, $length);
		if (bin_strlen($temp) != 1 || ord($temp) > 2) {
			$components['modulus'] = $temp;
			$this->_string_shift($key); // skip over CRYPT_RSA_ASN1_INTEGER
			$length = $this->_decodeLength($key);
			$components['privateExponent'] = $this->_string_shift($key, $length);

			return $components;
		}
		if (ord($this->_string_shift($key)) != CRYPT_RSA_ASN1_INTEGER) {
			return false;
		}
		$length = $this->_decodeLength($key);
		$components['modulus'] = $this->_string_shift($key, $length);
		$this->_string_shift($key);
		$length = $this->_decodeLength($key);
		$components['publicExponent'] = $this->_string_shift($key, $length);
		$this->_string_shift($key);
		$length = $this->_decodeLength($key);
		$components['privateExponent'] = $this->_string_shift($key, $length);

		return $components;
	}

	function GenerateKeys()
	{
		if (CRYPT_RSA_MODE == CRYPT_RSA_MODE_OPENSSL) {
			$config = array(
				'private_key_bits' => $this->key_length,
				'private_key_type' => OPENSSL_KEYTYPE_RSA,
				'encrypt_key' => false,
				'config' => CRYPT_RSA_OPENSSL_CONFIG
			);
			$pkey = openssl_pkey_new($config);
			if ($pkey !== FALSE)
			{
				openssl_pkey_export($pkey, $pkey_text, NULL, $config);
				$details = $this->ParseKey($pkey_text);

				if ($details !== FALSE)
				{
					$this->modulus 		= base64_encode($details["modulus"]);
					$this->public_exp 	= base64_encode($details["publicExponent"]);
					$this->private_exp 	= base64_encode($details["privateExponent"]);
					return TRUE;
				}
			}
		}

		$key_len = $this->key_length;
		// set [e] to 0x10001 (65537)
		$this->public_exp = base64_encode("\x00\x01\x00\x01");		//reversive
		$e = $this->_math_obj->bin2int("\x01\x00\x01\x00");

		// generate [p], [q] and [n]
		$p_len = intval(($key_len + 1) / 2);
		$q_len = $key_len - $p_len;
		$p1 = $q1 = 0;
		do
		{
			// generate prime number [$p] with length [$p_len] with the following condition:
			// GCD($e, $p - 1) = 1
			do
			{
				$p = $this->_math_obj->getPrime($p_len, $this->_random_generator);
				$p1 = $this->_math_obj->dec($p);
				$tmp = $this->_math_obj->GCD($e, $p1);
			} 
			while (!$this->_math_obj->isOne($tmp));
			
			// generate prime number [$q] with length [$q_len] with the following conditions:
			// GCD($e, $q - 1) = 1
			// $q != $p
			do
			{
				$q = $this->_math_obj->getPrime($q_len, $this->_random_generator);
				$q1 = $this->_math_obj->dec($q);
				$tmp = $this->_math_obj->GCD($e, $q1);
			} 
			while (!$this->_math_obj->isOne($tmp) && !$this->_math_obj->cmpAbs($q, $p));
			
			// if (p < q), then exchange them
			if ($this->_math_obj->cmpAbs($p, $q) < 0)
			{
				$tmp = $p;
				$p = $q;
				$q = $tmp;
				$tmp = $p1;
				$p1 = $q1;
				$q1 = $tmp;
			}
			// calculate n = p * q
			$n = $this->_math_obj->mul($p, $q);
		}
		while ($this->_math_obj->bitLen($n) != $key_len);

		// calculate d = 1/e mod (p - 1) * (q - 1)
		$pq = $this->_math_obj->mul($p1, $q1);
		$d = $this->_math_obj->invmod($e, $pq);

		$this->modulus = base64_encode(strrev($this->_math_obj->int2bin($n)));
		$this->private_exp = base64_encode(strrev($this->_math_obj->int2bin($d)));

		return true; // key pair successfully generated
	}
	
	function Encrypt($plain_data)
	{
		$plain_data = $this->_math_obj->bin2int(strrev($plain_data)); 
		$exp = $this->_math_obj->bin2int(strrev(base64_decode($this->private_exp)));
		$modulus = $this->_math_obj->bin2int(strrev(base64_decode($this->modulus)));
		
		// divide plain data into chunks
		$data_len = $this->_math_obj->bitLen($plain_data);
		$chunk_len = $this->key_length - 1;
		$block_len = (int) ceil($chunk_len / 8);
		$curr_pos = 0;
		$enc_data = '';
		while ($curr_pos < $data_len) 
		{
			$tmp = $this->_math_obj->subint($plain_data, $curr_pos, $chunk_len);
			$enc_data .= str_pad($this->_math_obj->int2bin($this->_math_obj->powmod($tmp, $exp, $modulus)), $block_len, "\0");
			$curr_pos += $chunk_len;
		}
		return strrev($enc_data);
	}
	
	function Decrypt($enc_data)
	{
		$exp = $this->_math_obj->bin2int(strrev(base64_decode($this->public_exp)));
		$modulus = $this->_math_obj->bin2int(strrev(base64_decode($this->modulus)));
		$enc_data = strrev($enc_data);

		$data_len = bin_strlen($enc_data);
		$chunk_len = $this->key_length - 1;
		$block_len = (int) ceil($chunk_len / 8);
		$curr_pos = 0;
		$bit_pos = 0;
		$plain_data = $this->_math_obj->bin2int("\0");
		while ($curr_pos < $data_len) 
		{
			$tmp = $this->_math_obj->bin2int(bin_substr($enc_data, $curr_pos, $block_len));
			$tmp = $this->_math_obj->powmod($tmp, $exp, $modulus);
			$plain_data = $this->_math_obj->bitOr($plain_data, $tmp, $bit_pos);
			$bit_pos += $chunk_len;
			$curr_pos += $block_len;
		}
		
		$result = strrev($this->_math_obj->int2bin($plain_data));
		return $result;
	}
}

?>