<?php

define("HELP_IMPORT_LICENSE", "Here you can paste a license that was generated somewhere else (for example manually) and it will be checked and added to the database.");
define("HELP_NEW_PRODUCT", "Fill the form below to create a new product. Choose the key length carefully, as the larger values mean more security, but slower processing and larger serial numbers. Leave the default length if unsure.");
define("HELP_IMPORT_PRODUCT", "Pick a local VMProtect project file and upload it to the database with all the serial numbers and other licensing-related features. You may then use \"Export\" command to download the project back. Very useful for synchronization purposes.");
define("HELP_NEW_REGISTRATOR", "Put your e-commerce provider name here. You may also put an IP range here, so License Manager will only accept keygen calls from those addresses.");
define("HELP_COPY_URL", "Select your e-commerce provider from the drop-down list below and put the key generator url to the e-commerce provider's control panel. If your provider is not listed here, please build your own url using the same principles and send the template to us, so we can add it in the next update.");

define("VERSION_TXT", "Version");

/* Login page */
define("LOGIN_HEADER_TXT", "Login");
define("LOGIN_BTN_TXT", "Log in");
define("CAPTCHA_TXT", "Security code<br/>(case-insensitive)");
define("CAPTCHA_ANOTHER_TXT", "Show another image");
define("CAPTCHA_ERROR_TXT", "Wrong security code.");
define("LOGIN_ERROR_TXT", "Wrong login or password.");
define("USERNAME_TXT", "Username");
define("PASSWORD_TXT", "Password");
define("FORGOT_TXT", "Forgot password?");

/* Retrive password page */
define("RP_RETPASS_TXT", "Retrieve Password");
define("RP_EMAIL_TXT", "Email");
define("RP_SEND_TXT", "Send");
define("RP_USERNOTFOUND_TXT", "User was not found.");
define("RP_SENT_TXT", "New password has been sent to your email address.");
define("RP_ERROR_TXT", "Error while sending mail.");

define("HELLO_TXT", "Hello");
define("HELP_TXT", "Help");
define("LOGOUT_TXT", "Logout");

define("M_LICS_TXT", "Licenses");
define("M_NEWLIC_TXT", "Add New License");
define("H_EDITLIC_TXT", "Edit License");
define("M_IMPORTLIC_TXT", "Import License");
define("M_ACTS_TXT", "Activation Codes");
define("M_NEWACT_TXT", "Add New Code");
define("H_NEWACT_TXT", "Add New Activation Code");
define("H_EDITACT_TXT", "Edit Activation Code");
define("M_IMPORTACT_TXT", "Import Codes");
define("H_IMPORTACT_TXT", "Import Activation Codes");
define("M_PRODS_TXT", "Products");
define("M_NEWPROD_TXT", "Add New Product");
define("H_EDITPROD_TXT", "Edit Product");
define("M_NEWMODE_TXT", "Add New Mode");
define("H_EDITMODE_TXT", "Edit Mode");
define("M_IMPORTPROD_TXT", "Import Product");
define("M_REGS_TXT", "Agents");
define("M_NEWREG_TXT", "Add New Agent");
define("H_EDITREG_TXT", "Edit Agent");
define("M_USERS_TXT", "Users");
define("M_NEWUSER_TXT", "Add New User");
define("H_EDITUSER_TXT", "Edit User");

define("ADD_TXT", "Add New");
define("ITEMS_TXT", "Items");
define("ALL_TXT", "All");
define("BLOCKED_TXT", "Blocked");
define("INACTIVE_TXT", "Inactive");
define("SAVE_TXT", "Save");
define("IMPORT_TXT", "Import");
define("EDIT_TXT", "Edit");
define("DELETE_TXT", "Delete");
define("CANCEL_TXT", "Cancel");
define("BLOCK_TXT", "Block");
define("UNBLOCK_TXT", "Unblock");
define("COPYCPB_TXT", "Copy to Clipboard");

/* Licenses */
define("LIC_DATE_TXT", "Purchase Date");
define("LIC_PROD_TXT", "Product");
define("LIC_NAME_TXT", "Customer Name");
define("LIC_EMAIL_TXT", "Customer Email");
define("LIC_ORDERREF_TXT", "Order Ref");
define("LIC_COMMENTS_TXT", "Comments");
define("LIC_HWID_TXT", "Hardware ID");
define("LIC_EXPDATE_TXT", "Expire Date");
define("LIC_LIMIT_TXT", "Time Limit");
define("LIC_MAXBDATE_TXT", "Max Build Date");
define("LIC_DATA_TXT", "Data");
define("LIC_BLOCKED_TXT", "Blocked");
define("LIC_COPYSN_TXT", "Copy Serial Number");
define("LIC_SN_TXT", "License Serial Number");
define("LIC_DELETE_TXT", "Delete License");
define("LIC_DELMSG_TXT", "Are you sure you want to delete the license?");
define("LIC_ENOTP_TXT", "Product for this serial number not found.");

/* Activations */
define("ACT_CODE_TXT", "Code");
define("ACT_COUNT_TXT", "Activations Count");
define("ACT_USED_TXT", "Used");
define("ACT_LICS_TXT", "Show Licenses");
define("ACT_BLOCKED_TXT", "Blocked");
define("ACT_DELETE_TXT", "Delete Activation Code");
define("ACT_DELMSG_TXT", "Are you sure you want to delete the activation code?");
define("ACT_CODES_TXT", "Activation Codes");
define("ACT_ECODES_TXT", "codes are wrong.");
define("ACT_NONE_TXT", "none");
define("ACT_FROMURL_TXT", "from URL");
define("ACT_DELAY_TXT", "days from activation date");
define("ACT_EXPDELAY_TXT", "days from purchase date");
define("ACT_VALUE_TXT", "value");
define("ACT_EXPDATE_TXT", "Expire Date");

/* Products */
define("PR_NAME_TXT", "Product Name");
define("PR_MODE_TXT", "Add Mode");
define("PR_LICS_TXT", "Show Licenses");
define("PR_EXPORT_TXT", "Export");
define("PR_KEYGEN_TXT", "Keygen URL");
define("PR_REG_TXT", "Select template");
define("PR_ACTIVE_TXT", "Active");
define("PR_ALG_TXT", "Algorithm");
define("PR_BITS_TXT", "Bits");
define("PR_KGMODE_TXT", "Keygen mode");
define("PR_ACTPAT_TXT", "Activation codes pattern");
define("PR_ACTEX_TXT", "Extra activations");
define("PR_DELETE_TXT", "Delete Product");
define("PR_DELMSG_TXT", "Are you sure you want to delete the product?");
define("PR_FILE_TXT", "Product File");
define("PR_KGSN_TXT", "Serial Numbers");
define("PR_KGAC_TXT", "Activation Codes");
define("PR_ENAME_TXT", "Product with this name already exists.");
define("PR_EFILESIZE_TXT", "File is too big. Maximum file size is %d.");
define("PR_EXMLFILE_TXT", "Error parsing XML file.");
define("PR_EXMLDATA_TXT", "Wrong XML data.");
define("PR_IMPORTED_TXT", "Imported product \'%s\' and %d licenses.");

/* Registrators */
define("R_NAME_TXT", "Agent Name");
define("R_RANGES_TXT", "IP Ranges");
define("R_START_TXT", "Start IP");
define("R_END_TXT", "End IP");
define("R_ADD_TXT", "Add");
define("R_ACTIVE_TXT", "Active");
define("R_DELETE_TXT", "Delete Agent");
define("R_DELMSG_TXT", "Are you sure you want to delete the agent?");
define("R_EIP_TXT", "Not valid IP address.");
define("R_EOCTET_TXT", "Each octet value must be between 0 and 255.");
define("R_EENDIP_TXT", "End IP should be greater.");
define("R_EIPS_TXT", "Need to add at least one IP.");
define("R_AUTHMODE_TXT", "Auth Mode");
define("R_AUTHMODE_IP_TXT", "access by IP");
define("R_AUTHMODE_LOGIN_TXT", "access by login and password");
define("R_LOGIN_TXT", "Login");
define("R_PASSWORD_TXT", "Password");
define("R_VALUE_TXT", "Value");

/* Users */
define("U_ADMINS_TXT", "Administrators");
define("U_MANS_TXT", "Managers");
define("U_ADMIN_TXT", "Administrator");
define("U_MAN_TXT", "Manager");
define("U_ROLE_TXT", "Role");
define("U_UN_TXT", "Username");
define("U_PASS_TXT", "Password");
define("U_PASS2_TXT", "Confirm Password");
define("U_EMAIL_TXT", "Email");
define("U_DELETE_TXT", "Delete User");
define("U_DELMSG_TXT", "Are you sure you want to delete the user?");
define("U_PROFILE_TXT", "Profile");
define("U_ELOGIN_TXT", "This login name is already in use.");
define("U_EEMAIL_TXT", "This email is already in use.");
define("U_EADMIN_TXT", "Can't delete first user.");
define("U_ESELF_TXT", "Can't delete yourself.");

/* Validation script */
define("V_DIG_TXT", "Only digits are allowed.");
define("V_REQ_TXT", "This field is required.");
define("V_EMAIL_TXT", "Not valid email address.");
define("V_DATE_TXT", "Not valid date. Format is YYYY-MM-DD.");
define("V_EQ_TXT", "should be equal to");
define("V_ERROR_TXT", "Error");

/* Installation */
define("I_MODULES_TXT", "Checking PHP modules");
define("I_CONTINUE_TXT", "Continue");
define("I_OK_TXT", "OK");
define("I_FAILED_TXT", "FAILED");
define("I_EMODULE_TXT", "Please install required PHP modules before continue.");
define("I_EGLOBALS_TXT", "Please disable 'register_globals' PHP option.");
define("I_DATABASE_TXT", "Connecting to database");
define("I_DBSERVER_TXT", "Database Server");
define("I_DBNAME_TXT", "Database Name");
define("I_DBUSER_TXT", "Database Username");
define("I_DBPASS_TXT", "Database Password");
define("I_DBPREFIX_TXT", "Table Prefix");
define("I_INSTALL_TXT", "Install data tables");
define("I_UPDATE_TXT", "Update data tables");
define("I_CONNECT_TXT", "Connect");
define("I_DBEXISTS_TXT", "Data tables already exist. You can choose update it to save your data or do clear installation.");
define("I_ADMIN_TXT", "Create administrator");
define("I_CREATE_TXT", "Create");
define("I_SAVING_TXT", "Saving configuration");
define("I_ERROR_TXT", "Failed to write file 'include/config.inc.php'");
define("I_SUCCESS_TXT", "Configuration file has been saved successfully.<br/>Now you can disable write attribute on file 'include/config.inc.php'.<br />To complete installation delete file 'install.php' from server and start work <a href=\"index.php\">here</a>.");

/* Offline activation */
define("OFF_STRING_TXT", "Enter your activation / deactivation string:");
define("OFF_SUBMIT_TXT", "Submit");
define("OFF_SN_TXT", "Your serial number:");
define("OFF_COPIED_TXT", "Copied");
define("OFF_ESTRING_TXT", "Wrong activation string");
define("OFF_ECODE_TXT", "Your activation code is invalid");
define("OFF_ELIMIT_TXT", "Activations limit exceed");
define("OFF_EBLOCKED_TXT", "Your activation code is blocked");
define("OFF_EEXPIRED_TXT", "Your activation code is expired");
define("OFF_ESN_TXT", "Your serial number is invalid");
define("OFF_DEACT_TXT", "Your serial number has been deactivated");

?>
