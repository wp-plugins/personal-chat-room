<?php

class CChat {

    // constructor
    function CChat() {}

    // add to DB message
    function acceptMessage() {
        $sName = $GLOBALS['MySQL']->escape($_SESSION['member_name']);
        $iPid = (int)$_SESSION['member_id'];
        $sMessage = $GLOBALS['MySQL']->escape($_POST['message']);

        if ($iPid && $sName != '' && $sMessage != '') {
            $sSQL = "
                SELECT `id`
                FROM `cs_messages`
                WHERE `sender` = '{$iPid}' AND UNIX_TIMESTAMP( ) - `when` < 5
                LIMIT 1
            ";
            $iLastId = $GLOBALS['MySQL']->getOne($sSQL);
            if ($iLastId) return 2; // as protection from very often messages

            $bRes = $GLOBALS['MySQL']->res("INSERT INTO `cs_messages` SET `sender` = '{$iPid}', `message` = '{$sMessage}', `when` = UNIX_TIMESTAMP()");
            return ($bRes) ? 1 : 3;
        }
    }

    // return input text form
    function getInputForm() {
        return file_get_contents('templates/chat.html');
    }

    // get last 10 messages
    function getMessages() {
        $sSQL = "
            SELECT `a` . * , `cs_profiles`.`name` , UNIX_TIMESTAMP( ) - `a`.`when` AS 'diff'
            FROM `cs_messages` AS `a`
            INNER JOIN `cs_profiles` ON `cs_profiles`.`id` = `a`.`sender`
            ORDER BY `a`.`id` DESC
            LIMIT 10 
        ";
        $aMessages = $GLOBALS['MySQL']->getAll($sSQL);
        asort($aMessages);

        // create list of messages
        $sMessages = '';
        foreach ($aMessages as $i => $aMessage) {
            $sExStyles = $sExJS = '';
            $iDiff = (int)$aMessage['diff'];
            if ($iDiff < 7) { // less than 7 seconds
                $sExStyles = 'style="display:none;"';
                $sExJS = "<script> $('#message_{$aMessage['id']}').fadeIn('slow'); </script>";
            }

            $sWhen = date("H:i:s", $aMessage['when']);
            $sMessages .= '<div class="message" id="message_'.$aMessage['id'].'" '.$sExStyles.'><b>' . $aMessage['name'] . ':</b> ' . $aMessage['message'] . '<span>(' . $sWhen . ')</span></div>' . $sExJS;
        }
        return $sMessages;
    }
}

$GLOBALS['MainChat'] = new CChat();
