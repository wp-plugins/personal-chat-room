<?php

// set error reporting level
if (version_compare(phpversion(), '5.3.0', '>=') == 1)
  error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
else
  error_reporting(E_ALL & ~E_NOTICE);

require_once('classes/Services_JSON.php');
require_once('classes/CMySQL.php'); // including service class to work with database
require_once('classes/CLogin.php'); // including service class to work with login processing

$sErrors = '';
if(isset($_GET['value']))
{
		
		$val_data = $_GET['value'];
		$val_data_unser = unserialize($val_data);
		$_SESSION['host'] = $val_data_unser[1];
		$_SESSION['user'] = $val_data_unser[2];
		$_SESSION['pass'] = $val_data_unser[3];
		$_SESSION['dbs'] = $val_data_unser[0];
		$GLOBALS['MySQL']->sDbHost = $_SESSION['host'];
        $GLOBALS['MySQL']->sDbName = $_SESSION['dbs'];
        $GLOBALS['MySQL']->sDbUser = $_SESSION['user'];
        $GLOBALS['MySQL']->sDbPass = $_SESSION['pass'];
}
$GLOBALS['MySQL']->sDbHost = $_SESSION['host'];
$GLOBALS['MySQL']->sDbName = $_SESSION['dbs'];
$GLOBALS['MySQL']->sDbUser = $_SESSION['user'];
$GLOBALS['MySQL']->sDbPass = $_SESSION['pass'];
$GLOBALS['MySQL']->vLink = mysql_connect($GLOBALS['MySQL']->sDbHost, $GLOBALS['MySQL']->sDbUser, $GLOBALS['MySQL']->sDbPass);
 mysql_select_db($GLOBALS['MySQL']->sDbName, $GLOBALS['MySQL']->vLink);
// join processing
if (! isset($_SESSION['member_id']) && $_POST['Join'] == 'Join') {
    $sUsername = $GLOBALS['MySQL']->escape($_POST['username']);
    $sFirstname = $GLOBALS['MySQL']->escape($_POST['firstname']);
    $sLastname = $GLOBALS['MySQL']->escape($_POST['lastname']);
    $sEmail = $GLOBALS['MySQL']->escape($_POST['email']);
    $sPassword = $GLOBALS['MySQL']->escape($_POST['password']);

    if ($sUsername && $sEmail && $sPassword) {
        // check if already exist
        $aProfile = $GLOBALS['MySQL']->getRow("SELECT * FROM `cs_profiles` WHERE `email`='{$sEmail}'");
        if ($aProfile['id'] > 0) {
            $sErrors = '<h2>Another profile with same email already exist</h2>';
        } else {
            // generate Salt and Cached password
            $sSalt = 'testing'; // TODO - we will add generation of salt in future
            $sPass = sha1(md5($sPassword) . $sSalt);

            // add new member into database
            $sSQL = "
                INSERT INTO `cs_profiles` SET 
                `name` = '{$sUsername}',
                `first_name` = '{$sFirstname}',
                `last_name` = '{$sLastname}',
                `email` = '{$sEmail}',
                `password` = '{$sPass}',
                `salt` = '{$sSalt}',
                `status` = 'active',
                `role` = '1',
                `date_reg` = NOW();
            ";
            $GLOBALS['MySQL']->res($sSQL);

            // autologin
            $GLOBALS['CLogin']->performLogin($sUsername, $sPassword);
        }
    }
}

// login system init and generation code
$sLoginForm = $GLOBALS['CLogin']->getLoginBox();

$sChat = '<h2>You do not have rights to use chat</h2>';

$sInput = '';
if ($_SESSION['member_id'] && $_SESSION['member_status'] == 'active' && $_SESSION['member_role']) {
    require_once('classes/CChat.php'); // including service class to work with chat

    // get last messages
    $sChat = $GLOBALS['MainChat']->getMessages();
    if ($_GET['action'] == 'get_last_messages') { // regular updating of messages in chat
        $oJson = new Services_JSON();
        header('Content-type: application/json');
        echo $oJson->encode(array('messages' => $sChat));
        exit;
    }

    // get input form
    $sInput = $GLOBALS['MainChat']->getInputForm();

    if ($_POST['message']) { // POST-ing of message
        $iRes = $GLOBALS['MainChat']->acceptMessage();

        $oJson = new Services_JSON();
        header('Content-type: application/json');
        echo $oJson->encode(array('result' => $iRes));
        exit;
    }
}

// draw common page
echo strtr(file_get_contents('templates/main_page.html'), array('{form}' => $sLoginForm . $sErrors, '{chat}' => $sChat, '{input}' => $sInput));
