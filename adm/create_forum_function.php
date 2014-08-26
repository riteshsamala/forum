<?php
function create_forum($forum_name, $parent_id)
{
    global $phpbb_root_path, $phpEx, $user, $auth, $cache, $db, $config, $template, $table_prefix;

    $response = array();
    $data = array(
        'forum_name' => $forum_name,
    );

// Forum info



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

// DONE CREATING A NEW FORUM
        $_POST["username"] = "ritesh";
        $_POST["title"] = "general discussion";
        $_POST["text"] = "welcome to general discussions";
        $_POST["forumid"] = $forum_data['forum_id']; //this is the forum id you wanna post to (required for a reply too)
        $_POST["topicid"] = ''; //if you wanna submit a reply to a thread add topic id here
        $_POST["description"] = "creating a general discussion forum";
		$_POST["priority"] = "urgent";
		$_POST["type"] = "";
        $title = htmlspecialchars($_POST['title']);
        $name = htmlspecialchars($_POST['username']);
        $text = htmlspecialchars($_POST['description']);
        $Prioritet = $_POST['priority'];
        $Typ = $_POST['type'];
        $text = utf8_normalize_nfc($text);

        $poll = $uid = $bitfield = $options = '';
        $message=generate_text_for_storage($text, $uid, $bitfield, $bbcode_options, false, false, false);

        $subject = utf8_normalize_nfc($title);
        $username = utf8_normalize_nfc($name);

        $data = array(
            // General Posting Settings
            'forum_id'            => $forum_data['forum_id'],    // The forum ID in which the post will be placed. (int)
            'topic_id'            => 0,    // Post a new topic or in an existing one? Set to 0 to create a new one, if not, specify your topic ID here instead.
            'icon_id'            => false,    // The Icon ID in which the post will be displayed with on the viewforum, set to false for icon_id. (int)

            // Defining Post Options
            'enable_bbcode'    => true,    // Enable BBcode in this post. (bool)
            'enable_smilies'    => true,    // Enabe smilies in this post. (bool)
            'enable_urls'        => true,    // Enable self-parsing URL links in this post. (bool)
            'enable_sig'        => true,    // Enable the signature of the poster to be displayed in the post. (bool)

            // Message Body
            'message'            => "checking before committing",        // Your text you wish to have submitted. It should pass through generate_text_for_storage() before this. (string)
            'message_md5'    => md5($message),// The md5 hash of your message

            // Values from generate_text_for_storage()
            'bbcode_bitfield'    => $bitfield,    // Value created from the generate_text_for_storage() function.
            'bbcode_uid'        => $uid,        // Value created from the generate_text_for_storage() function.

            // Other Options
            'post_edit_locked'    => 0,        // Disallow post editing? 1 = Yes, 0 = No
            'topic_title'        => $subject,    // Subject/Title of the topic. (string)

            // Email Notification Settings
            'notify_set'        => false,        // (bool)
            'notify'            => false,        // (bool)
            'post_time'         => 0,        // Set a specific time, use 0 to let submit_post() take care of getting the proper time (int)
            'forum_name'        => '',        // For identifying the name of the forum in a notification email. (string)

            // Indexing
            'enable_indexing'    => true,        // Allow indexing the post? (bool)

            // 3.0.6
            'force_approved_state'    => true, // Allow the post to be submitted without going into unapproved queue

            // 3.1-dev, overwrites force_approve_state
            'force_visibility'            => true, // Allow the post to be submitted without going into unapproved queue, or make it be deleted

        );
//just for printing sake to make sure what the variables are
        //echo $mode,$subject, $username, $topic_type, $topic_type, $poll, $data, $update_message;
// function which submits the post, make sure we include the necessary functions on the top

        $final_res= submit_post ( 'post',  $subject,  $username,  POST_NORMAL,  $poll,  $data, $update_message = true);
        $final_res=html_entity_decode($final_res);
        return array("redirect_to"=>$final_res, "forum_id"=>$forum_data['forum_id']);
}
?>