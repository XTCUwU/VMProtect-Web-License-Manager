<?php

/* Overload string functions */
function bin_strlen($string)
{
	$overloaded = extension_loaded("mbstring") && ini_get("mbstring.func_overload") == "2";
	return $overloaded ? mb_strlen($string, "8bit") : strlen($string);
}

function bin_substr($string, $start, $length = null)
{
	$overloaded = extension_loaded("mbstring") && ini_get("mbstring.func_overload") == "2";
	if (func_num_args() < 3)
		$length = bin_strlen($string) - $start;
	return $overloaded ? mb_substr($string, $start, $length, "8bit") : substr($string, $start, $length);
}

?>