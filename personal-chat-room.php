<?php
/* 	Plugin Name: Personal Chat room
	Plugin Uri: http://businessadwings.com
	Description: This plugin gives you the facility to put a button to chat your users with each other [LORDLINUS_PERSONAL_CHAT]
	Version: 1.0
	Author: Lord Linus
	Author URI: http://businessadwings.com/contact-us
	Licence: GPVl
*/
?>
<?php

register_activation_hook( __FILE__, 'Chat_InstallScript' );
function Chat_InstallScript()
{
	include('install-script.php');
}
function chat_lord_menu()
{
	add_menu_page( 'Chat Room', 'Chat Room', 'administrator','chat-room' ,'chat_room_lord');
}
function chat_room_lord()
{
		if(isset($_POST['save_lord']))
		{
			$dbname = DB_NAME;
			$hostname = $_POST['lord_linus_host'];
			$username=$_POST['lord_linus_user'];
			$password = $_POST['lord_linus_password'];
			$lord_Array = array($dbname,$hostname,$username,$password);
			update_option('chat_lord_linus_database',$lord_Array);
			echo "<font color='red'> Your Data has been updated ... </font>";
		}
		echo "<form action='' method='post'>";
		echo "<h1> Please Set up Your Database here</h1>";
		echo "<table><tr><td>HOST :</td><td> <input type='text' name='lord_linus_host' size='50' required='required'/> </td></tr>";
		echo "<tr><td>Database Name : </td><td><input type='text' name ='lord_linus_dbname' value='".DB_NAME."' size='50' disabled='disabled'></td></tr>";
		echo "<tr><td>Database User name : </td><td><input type='text' name='lord_linus_user' size='50' required='required'></td></tr>";
		echo "<tr><td>Database Password : </td><td><input type='text' name = 'lord_linus_password' size='50' ></td></tr>";
		echo "<tr><td><input type='submit' value='save record' name='save_lord'></tr></table></form>";
	
}
add_shortcode('PERSONAL_CHAT_ROOM','chat_room_shorcode');
function chat_room_shorcode()
{
$value = serialize(get_option('chat_lord_linus_database'));
$url = plugins_url('/chat/index.php?value='.$value.'', __FILE__);
echo "<IFRAME target='_blank' width='100%' frameborder='0' scrolling='auto'></iframe>";
echo "<style>#support_btn {top: 50%!important;background: #FECC33!important;border-radius: 0px 0px 7px 7px;font-family: Arial, Helvetica, sans-serif;border: solid 2px #fff;margin: 0;cursor: pointer;overflow: hidden;position: fixed;height: 25px;min-width: 110px;z-index: 10000;white-space: nowrap;padding: 0 10px 35px 10px;}#support_btn:hover {background:#F7D55B!important}#support_btn #middle_left_text {float: left;font-size: 22px;font-weight: bold;text-align: center;color: #444;letter-spacing: 1px;margin-top: 25px;margin-left: 10px;text-decoration:none;}#support_btn.middle_left {left: -50px;background-position: right 0;-webkit-transform: rotate(-90deg);-moz-transform: rotate(-90deg);-o-transform: rotate(-90deg);-ms-transform: rotate(-90deg);transform: rotate(-90deg);}</style> ";
echo "<div class='middle_left' id='support_btn'><div id='middle_left_text'><a style='text-decoration:none;' href=$url target='iframe1'>Live Chat</a></div></div>";
}
add_action( 'admin_menu','chat_lord_menu' );

?>