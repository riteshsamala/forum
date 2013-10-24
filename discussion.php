<?php

  // For each section, the admin must create a corresponding group
  // and forum in phpBB.
 
define('IN_PHPBB',true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include( $phpbb_root_path . "common." . $phpEx );
include($phpbb_root_path . 'includes/functions_display.' . $phpEx);
include_once($phpbb_root_path . 'includes/functions_user.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
$user->setup();

$student=request_var('u','');
$author=request_var('a','');  // Only for existing problem
$section=request_var('s','');
$problem=request_var('p','');
$content=request_var('t','');
$action=request_var('action','');

echo "action=$action<br>\n";
echo "user=$user section=$section<br>\n";

// Test that user exists
$sql = "SELECT `user_id` FROM `laits_users` 
        WHERE student=$student AND section=$section";
$result = $db->sql_query($sql);
$forum_data = $db->sql_fetchrow($result);
if ($forum_data){
  $user_id=$db->sql_fetchfield('user_id');
  echo "user $user_id exists<br>\n";
  
} else {
  echo "user does not exist<br>\n";
  // Create user, with standard password
  // Need to pick out a valid user name and the correct group
  $i=1;
  $student1=$student
  while(!validate_username($student1){
      $sutdent1=$student . $i++;
  }
  $user_row = array(
		    'username' => $student1,
		    'user_password' => phpbb_hash($student1),  // have to pass in md5 hashed pword
		    'user_email' => 'someone@somewhere.com',  // this is just a placeholder
		    'group_id' => '2',   // Need to find group associated with class section
		    'user_type' => USER_NORMAL,
		    'user_ip' => $user_ip
		    );
  $err = user_add($user_row);    
}

// In Dragoon, user names are local to a section while
// phpBB user names are global.  Thus, we
// need to find a map between 

if ($user->data['user_id'] == ANONYMOUS){
  echo 'Please logging student in with $student1 $password';
  $auth->login($student1, $student1);
} else {
  echo 'Thanks for logging in, ' . $user->data['username_clean'];
}

if($action=='new'){

  // Create new thread in forum associated with given section
  // variables to hold the parameters for submit_post
  $my_subject   = utf8_normalize_nfc($problem . ": " . $author, '', true);
  $my_text   = utf8_normalize_nfc($content, '', true);

  // variables to hold the parameters for submit_post
  $poll = $uid = $bitfield = $options = '';

  generate_text_for_storage($my_subject, $uid, $bitfield, $options, false, false, false);
  generate_text_for_storage($my_text, $uid, $bitfield, $options, true, true, true);

  submit_post('post', $my_subject, $user->data['username'], POST_NORMAL, $poll, $data);

}else if($action=='existing'){
  // Go to existing thread.

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

} else {
  trigger_error("Bad choice of action=".$action);
}

?>
