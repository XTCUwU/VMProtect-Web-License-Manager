<?php
require_once "../include/lang.inc.php";
?>

var isValid;

function addError(control, text){
	$(control).after('<label class="error">' + text + '</label>');
	$(control).addClass('errorField');
}

function clearError(control){
    $(control).removeClass('errorField');
    $(control + ' + label.error').remove();
}

function validateRequired(){
	if ($(this).attr('disabled') != undefined)
		return;
    if ($(this).val() == null || $(this).val() == "")
    {
        isValid = false;
		addError(this, '<?php echo V_REQ_TXT; ?>');
    }
}

function validateRegex(r, field, message){
    if (field.value != '')
    if (r.test(field.value) == false)
    {
        isValid = false;
		addError(field, message);
    }
}

function validateNumeric(){
    validateRegex(/^\d*$/, this, '<?php echo V_DIG_TXT; ?>');
}

function validateEmail(){
    validateRegex(/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/i, this, '<?php echo V_EMAIL_TXT; ?>');
}

function validateDate(){
    validateRegex(/^(19|20)\d\d-(0[1-9]|1[012])-(0[1-9]|[12][0-9]|3[01])$/, this, '<?php echo V_DATE_TXT; ?>');
}

function validateEqual(field1, field2){
    if ($('#' + field1).val() != $('#' + field2).val())
    {
        var name1 = $('label[for=' + field1 + ']').text();
        var name2 = $('label[for=' + field2 + ']').text();
        isValid = false;
		addError('#' + field2, name2 + ' <?php echo V_EQ_TXT; ?> ' + name1 + '.');
    }
}

function validateForm(){
    isValid = true;
    $('form input').removeClass('errorField');
    $('label.error').remove();
    
    $('.required').each(validateRequired);
    $('.numeric').each(validateNumeric);
    $('.email').each(validateEmail);
    $('.date').each(validateDate);
    
    if (typeof(customValidate) == 'function')
        customValidate();
    
    return isValid;
}