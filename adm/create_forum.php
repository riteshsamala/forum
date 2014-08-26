<?php
define('IN_PHPBB', true);
define('ADMIN_START', true);
define('NEED_SID', true);
// Include files
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : "../";
echo $phpbb_root_path."is the root path";
$phpEx = substr(strrchr(__FILE__, '.'), 1);
echo "root path".$phpbb_root_path;
require($phpbb_root_path . 'common.' . $phpEx);
echo "common included";
require($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
require($phpbb_root_path . 'includes/functions_module.' . $phpEx);
include($phpbb_root_path . 'includes/functions_posting.' . $phpEx);
echo "includes finished";
require($phpbb_root_path. 'adm/create_forum_function.' . $phpEx);
require($phpbb_root_path. 'adm/create_post_function.' . $phpEx);



echo "Start session management";
$user->session_begin();
echo "authentication";
$auth->acl($user->data);
// End session management

// function which creates the forum directly into db using sql
$prob_name=$_GET['n'];
$forum_id=$_GET['fid'];
$sid_f=$_GET['sid'];
if(isset($_GET['nid'])) {	
	$node_id=$_GET['nid'];
    }
if(isset($_GET['ndesc'])){
	 $node_desc=$_GET['ndesc'];
    }
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
	//forum is already existing
    $response['error'] = TRUE;
    $response['error_msg'] = 'FORUM_EXISTS';
    $response['forum_id'] = $forum_id;
    $next_url="../viewtopic.php?f=".$forum_id;
    //Check if the node corresponding variables are set
    if($node_id && $node_desc){
    	echo "Node Id and Node Desc Are Set<br/>";
    	echo $forum_id; echo " ".$node_id;
		$qu="select * from check_node_topics where forum_id='$forum_id' && node_id='$node_id'";
		echo "<br>query is".$qu;
		$check_topic_exists=$db->sql_query($qu);
		if($res=$db->sql_fetchrow($check_topic_exists)){
			echo "resource exists";
    		echo "redirect to new node topic";
    		//if yes, extract the topic_id and redirect to the topic under the forum
			//header( "Location: $next_url"."&t=".$res['topic_id']."&sid=".$sid_f );	
		}
		else{
			//if not create the topic
			$return_url=create_node_topic($forum_id,$node_id,$node_desc);
			print_r($return_url);
	    	//Now, redirect to the topic inside the node
		    //header("Location: $return_url[redirect_to]");	
		}	
	}
	else{
		//redirect to general discussion thread
	    $tid_a=$db->sql_query("select topic_id from phpbb_topics where forum_id='$forum_id' AND topic_title='general discussion'");
		$tid=$db->sql_fetchrow($tid_a);
	    $next_url=$next_url."&t=".$tid['topic_id']."sid=".$sid_f;
	    //header("Location: $next_url");
	}
    
 }
else{
$forum_check=create_forum($prob_name,$_GET['fid']);
	
if($node_id && $node_desc){
		
			//create the topic
			$return_url=create_node_topic($forum_check['forum_id'],$node_id,$node_desc);
	
			print_r($return_url);
	    	//Now, redirect to the topic inside the node
		    //header("Location: $return_url[redirect_to]");
        }	
	
	else{
		//redirect to general discussion thread
	    $tid_a=$db->sql_query("select topic_id from phpbb_topics where forum_id=$forum_id AND topic_title='general discussion'");
		$tid=$db->sql_fetchrow($tid_a);
	    $next_url=$next_url."&t=".$tid['topic_id']."sid=".$sid_f;
	    //header("Location: $next_url");
	}

//redirect to newly created thread corresponding to the node_id
}

?>