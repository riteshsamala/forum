<?php
print_r($_GET);
define('IN_PHPBB', true);
define('ADMIN_START', true);
define('NEED_SID', true);

// Include files
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require($phpbb_root_path . 'common.' . $phpEx);
require($phpbb_root_path. 'adm/create_forum_function.' . $phpEx);
require($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
require($phpbb_root_path . 'includes/functions_module.' . $phpEx);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
// End session management

// function which creates the forum directly into db using sql
$prob_name=$_GET['n'];
$forum_id=$_GET['fid'];


global $phpbb_root_path, $phpEx, $user, $auth, $cache, $db, $config, $template, $table_prefix;
$response = array();
$data = array(
    'forum_name' => $prob_name,
);

// Forum info
$sql = 'SELECT forum_id
FROM ' . FORUMS_TABLE . '
WHERE ' . $db->sql_build_array('SELECT', $data);
$result = $db->sql_query($sql);

echo $sql;
$forum_id = (int) $db->sql_fetchfield('forum_id');
$db->sql_freeresult($result);

echo $forum_id."is forum id";
if ($forum_id)
{
    $response['error'] = TRUE;
    $response['error_msg'] = 'FORUM_EXISTS';
    $response['forum_id'] = $forum_id;
    $next_url="../viewforum.php?f=".$forum_id;
    header( "Location: $next_url" );
}
else{
$forum_check=create_forum($prob_name,$_GET['fid']);

}

?>