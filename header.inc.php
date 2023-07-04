<table style="width:100%; height:100%;">
    <tr>
        <td style="vertical-align: center;"><img style="display:block" alt="Web License Manager" src="images/header.png" title="<?php echo VERSION_TXT; ?> <?php echo $VERSION; ?>" /></td>
        
        <?php if (isset($_SESSION["cur_user"])) { ?>
        
        <td style="vertical-align: center;" align="right">
            <span id="helloTxt"><?php echo HELLO_TXT; ?>, <a href="#" onclick="return loadcontent('user/profile');" id="curUserLogin"><?php echo htmlspecialchars($cur_user->login); ?></a> (<span id="curUserEmail"><?php echo htmlspecialchars($cur_user->email); ?></span>)</span>
            <a id="helpLnk" target="_blank" href="help/index.htm?quick_start_guide.htm"><?php echo HELP_TXT; ?></a>
            <a id="logoutLnk" href="logout.php"><?php echo LOGOUT_TXT; ?></a>
        </td>
        
        <?php } ?>
    </tr>
</table>
