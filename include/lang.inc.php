<?php

$LANGUAGES = array(
    "en" => "English",
    "ru" => "Русский",
    "zh" => "中文"
);

function getDefaultLanguage()
{
    global $LANGUAGES;
    $langs = array_keys($LANGUAGES);
    return $langs[0];
}

// Default value: "en"
$lang = getDefaultLanguage();

// Get lang from HTTP header
if (!empty($_SERVER["HTTP_ACCEPT_LANGUAGE"]))
    foreach ($LANGUAGES as $l => $n)
        if (strpos($_SERVER["HTTP_ACCEPT_LANGUAGE"], $l) === 0)
            $lang = $l;

// Get lang from cookie
if (isset($_COOKIE["lang"]))
    $lang = $_COOKIE["lang"];

// This cookie used for path building. We should use preset values only.
if (!array_key_exists($lang,$LANGUAGES))
    $lang = getDefaultLanguage();

@include_once("lang_" . $lang . ".inc.php");
setcookie("lang", $lang,  time()+60*60*24*30, "/");

@include_once("lang_en.inc.php");

?>