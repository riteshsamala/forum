<?php
       
define('IN_PHPBB',true);
$phpbb_root_path = "forum/";
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require_once( $phpbb_root_path . "common." . $phpEx );
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$user_row = array(
		  //'user_id' => $user_id,   // commented - automatically generated on the user insert
		  'username' => $user,
		  'user_password' => phpbb_hash($pass),  // have to pass in a password hashed by phpBB!
		  'user_email' => $email,
		  'group_id' => '2',
		  'user_type'        => USER_NORMAL,
		  'user_ip' => $user->ip,
		  );
$err = user_add($user_row);  
    
?>
