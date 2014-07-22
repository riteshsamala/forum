<?php
define('IN_PHPBB', true);
define('ADMIN_START', true);
define('NEED_SID', true);

// Include files
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require($phpbb_root_path . 'common.' . $phpEx);

// function which creates the forum directly into db using sql
create_forum("Test Before Committing",6);

function create_forum($forum_name, $parent_id)
{
global $phpbb_root_path, $phpEx, $user, $auth, $cache, $db, $config, $template, $table_prefix;

$response = array();
$data = array(
'forum_name' => $forum_name,
);

// Forum info
$sql = 'SELECT forum_id
FROM ' . FORUMS_TABLE . '
WHERE ' . $db->sql_build_array('SELECT', $data);
echo " cp 1";
$result = $db->sql_query($sql);

$forum_id = (int) $db->sql_fetchfield('forum_id');
$db->sql_freeresult($result);


if ($forum_id)
{
$response['error'] = TRUE;
$response['error_msg'] = 'FORUM_EXISTS';
$response['forum_id'] = $forum_id;
}
else
{
$forum_data = array(
'parent_id'   =>   $parent_id,
'left_id'   =>   0,
'right_id'   =>   0,
'forum_parents'   =>   '',
'forum_name'   =>   $data['forum_name'],
'forum_desc'  =>   '',
'forum_desc_bitfield'   =>   '',
'forum_desc_options'   =>   7,
'forum_desc_uid'   =>   '',
'forum_link'   =>   '',
'forum_password'   =>   '',
'forum_style'   =>   0,
'forum_image'   =>   '',
'forum_rules'   =>   '',
'forum_rules_link'   =>   '',
'forum_rules_bitfield'   =>   '',
'forum_rules_options'   =>   7,
'forum_rules_uid'   =>   '',
'forum_topics_per_page'   =>   0,
'forum_type'   =>   1,
'forum_status'   =>   0,
'forum_posts'   =>   0,
'forum_topics'   =>   0,
'forum_topics_real'   =>   0,
'forum_last_post_id'   =>   0,
'forum_last_poster_id'   =>   0,
'forum_last_post_subject'   =>   '',
'forum_last_post_time'   =>   0,
'forum_last_poster_name'   =>   '',
'forum_last_poster_colour'   =>   '',
'forum_flags'   =>   32,
'display_on_index'   =>   FALSE,
'enable_indexing'   =>   TRUE,
'enable_icons'   =>   FALSE,
'enable_prune'   =>   FALSE,
'prune_next'   =>   0,
'prune_days'   =>   7,
'prune_viewed'   =>   7,
'prune_freq'   =>   1,
);
/**
/*Changed the code from here
/*Pulled straight from acl_forums.php from line 973 to line 1002
/*Removed lines 980 -> 989
/*Changed $forum_data_sql['parent_id']; line 975 to $parent_id
/*Changed $forum_data_sql on lines 1001, 1002 to $forum_data
**/

$sql = 'SELECT left_id, right_id, forum_type
FROM ' . FORUMS_TABLE . '
WHERE forum_id = ' . $parent_id;
echo $sql;
echo " cp 2";
$result = $db->sql_query($sql);
$row = $db->sql_fetchrow($result);
print_r($row);
$db->sql_freeresult($result);

$sql = 'UPDATE ' . FORUMS_TABLE . '
SET left_id = left_id + 2, right_id = right_id + 2
WHERE left_id > ' . $row['right_id'];echo " cp 3";

$db->sql_query($sql);

$sql = 'UPDATE ' . FORUMS_TABLE . '
SET right_id = right_id + 2
WHERE ' . $row['left_id'] . ' BETWEEN left_id AND right_id'; echo " cp 4";
$db->sql_query($sql);

$forum_data['left_id'] = $row['right_id'];
$forum_data['right_id'] = $row['right_id'] + 1;

// And as last, a insert query
$sql = 'INSERT INTO ' . FORUMS_TABLE . ' ' . $db->sql_build_array('INSERT', $forum_data); echo " cp 5";
$db->sql_query($sql);

$forum_data['forum_id'] = $db->sql_nextid();


// successful result
$response['error'] = FALSE;
$response['error_msg'] = '';
$response['forum_id'] = $forum_data['forum_id'];


/* PERMISSIONS ----------------------------------------------- */

// copy permissions from parent forum
$forum_perm_from = $parent_id;

///////////////////////////
// COPY USER PERMISSIONS //
///////////////////////////

// Copy permisisons from/to the acl users table (only forum_id gets changed)
$sql = 'SELECT user_id, auth_option_id, auth_role_id, auth_setting
FROM ' . ACL_USERS_TABLE . '
WHERE forum_id = ' . $forum_perm_from;echo " cp 6";
$result = $db->sql_query($sql);

$users_sql_ary = array();
while ($row = $db->sql_fetchrow($result))
{
$users_sql_ary[] = array(
'user_id'         => (int) $row['user_id'],
'forum_id'         => $forum_data['forum_id'],
'auth_option_id'   => (int) $row['auth_option_id'],
'auth_role_id'      => (int) $row['auth_role_id'],
'auth_setting'      => (int) $row['auth_setting']
);
}
$db->sql_freeresult($result);

////////////////////////////
// COPY GROUP PERMISSIONS //
////////////////////////////

// Copy permisisons from/to the acl groups table (only forum_id gets changed)
$sql = 'SELECT group_id, auth_option_id, auth_role_id, auth_setting
FROM ' . ACL_GROUPS_TABLE . '
WHERE forum_id = ' . $forum_perm_from;echo " cp 7";
$result = $db->sql_query($sql);

$groups_sql_ary = array();
while ($row = $db->sql_fetchrow($result))
{
$groups_sql_ary[] = array(
'group_id'         => (int) $row['group_id'],
'forum_id'         => $forum_data['forum_id'],
'auth_option_id'   => (int) $row['auth_option_id'],
'auth_role_id'      => (int) $row['auth_role_id'],
'auth_setting'      => (int) $row['auth_setting']
);
}
$db->sql_freeresult($result);

//////////////////////////////////
// INSERT NEW FORUM PERMISSIONS //
//////////////////////////////////

$db->sql_multi_insert(ACL_USERS_TABLE, $users_sql_ary);
$db->sql_multi_insert(ACL_GROUPS_TABLE, $groups_sql_ary);

$auth->acl_clear_prefetch();
print_r($response);
return $response;
}
}
?>