<?php if (!defined("FOOTER_HIDE_COPYRIGHT")) { ?>
<div style="float:left">&copy; Copyright 2003-2013 VMProtect Software</div>
<?php } ?>
<?php if (!defined("FOOTER_HIDE_LANGUAGES")) { ?>
<div style="float:right">
<?php
$langs_str = "";
if (isset($LANGUAGES))
foreach ($LANGUAGES as $l => $n)
    $langs_str .= ($langs_str != "" ? "|" : "") . "&nbsp;<a href=\"#\" onclick=\"set_lang('{$l}')\">{$n}</a>&nbsp;";
echo $langs_str;
?>
</div>
<script type="text/javascript">
function set_lang(lang){
    document.cookie = 'lang=' + lang;
    document.location.reload();
    return false;
}
</script>
<?php } ?>