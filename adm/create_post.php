<?php
define('IN_PHPBB', true);
define('ADMIN_START', true);
define('NEED_SID', true);

// Include files
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './../';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
require($phpbb_root_path . 'common.' . $phpEx);
require($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
require($phpbb_root_path . 'includes/functions_module.' . $phpEx);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);

// Start session management
$user->session_begin();
$auth->acl($user->data);
// End session management


// New Topic Example
//creating a new topic
$_POST["username"] = "ritesh";
$_POST["password"] = "Srinika2701";
$_POST["title"] = "hello Earth";
$_POST["text"] = "hello world";
$_POST["forumid"] = 6; //this is the forum id you wanna post to (required for a reply too)
$_POST["topicid"] = ''; //if you wanna submit a reply to a thread add topic id here
$title = htmlspecialchars($_POST['title']);
$name = htmlspecialchars($_POST['name']);
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
    'forum_id'            => 24,    // The forum ID in which the post will be placed. (int)
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
echo $mode,$subject, $username, $topic_type, $topic_type, $poll, $data, $update_message;
// function which submits the post, make sure we include the necessary functions on the top
submit_post ( 'post',  $subject,  $username,  POST_NORMAL,  $poll,  $data, [ $update_message = true]);
?>