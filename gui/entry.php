<?
	require_once "../include/session.php";
	require_once "../include/database.php";
	require_once "minimalist.php";
	if($session->logged_in){$username=$session->username;}
	else{header('location:/main.php');exit;}
	if(!$username){print "No username<br>\n";}
#	error_reporting (E_ALL);

class Entry
{
	var $tablename='ttrss_entries';
	var $feed_tablename='ttrss_feeds';
	var $pending_update;
	function entryNew($ref,$do_insert=1)
	{
		global $database,$username;
		if(!$ref){return;}
		$ref_username=$ref['username'];
		if(!$ref_username){$ref_username=$username;}
		$ref['content']=safe_param($ref['content']);
		$ref['title']=safe_param($ref['title']);
		$ref['link']=safe_param($ref['link']);
		$ref['comments']=safe_param($ref['comments']);
		$sql="insert into ".$this->tablename."  
			(title,guid,link,updated,content,content_hash,feed_id,comments,no_orig_date,date_entered,user_name)
			values ('{$ref['title']}','{$ref['guid']}','{$ref['link']}',NOW(),'{$ref['content']}',
		      '{$ref['content_hash']}','{$ref['feed_id']}','{$ref['comments']}','{$ref['no_orig_date']}',
		       NOW(),'$ref_username')";
		$this->pending_update[]="('{$ref['title']}','{$ref['guid']}','{$ref['link']}',NOW(),'{$ref['content']}',
		      '{$ref['content_hash']}','{$ref['feed_id']}','{$ref['comments']}','{$ref['no_orig_date']}',
		       NOW(),'$ref_username')";
		if($do_insert){$result=$database->query($sql);}
		$num_rows=mysql_affected_rows();
		return;
	}
	function feedId($id)
	{
		$data=$this->entryData($id);
		return $data['feed_id'];
	}
	function flushUpdate()
	{
		global $database;
		$insert_string=join(',',$this->pending_update);
		$sql="insert into ".$this->tablename."  
			(title,guid,link,updated,content,content_hash,feed_id,comments,no_orig_date,date_entered,user_name)
			values $insert_string";
print "sql=$sql<br>\n";
		$result=$database->query($sql);
	}
	function entryData($id)
	{
		global $database;
		$sql="select te.title,te.link,te.content,te.feed_id,te.comments,te.extended_content,
			tf.icon_url,tf.view_mode,te.updated, te.marked, te.unread 
		 from ". $this->tablename ." as te,".$this->feed_tablename." as tf 
		where te.id='$id' and te.feed_id=tf.id";
		$result=$database->query($sql);
		$vals=mysql_fetch_array($result);
		$vals['comments']=de_escape($vals['comments']);
		$vals['content']=de_escape($vals['content']);
		$vals['title']=de_escape($vals['title']);
		return $vals;
	}

	function markRead($id)
	{
		if(!$id){return 0;}
		global $database;
		$sql="update ".$this->tablename." set unread='0',last_read=NOW() where id='$id' limit 1";
		$result=$database->query($sql);
		$data=$this->entryData($id);
		if($data['unread']=='0'){return 1;}else{return 0;}
	}
	function markUnread($id)
	{
		if(!$id){return 0;}
		global $database;
		$sql="update ".$this->tablename." set unread='1',last_read=NOW() where id='$id' limit 1";
		$result=$database->query($sql);
		$data=$this->entryData($id);
		if($data['unread']=='1'){return 1;}else{return 0;}
	}
	function setMarked($id,$ismarked=0)
	{
		global $database;
		if(!$id){return;}
		if($ismarked != 0 && $ismarked != 1){return;}
		$sql="update ".$this->tablename." set marked='$ismarked' where id='$id' limit 1";
		$result=$database->query($sql);
		$data=$this->entryData($id);
	}
	function markedEntries()
	{
		global $database,$username;
		$sql="select ".$this->tablename.".id from ".$this->tablename.",".$this->feed_tablename." where ".$this->tablename.".marked='1' and ".$this->tablename.".feed_id=".$this->feed_tablename.".id and ".$this->feed_tablename.".user_name='$username' order by updated DESC";
		$result=$database->query($sql);
		while($vals=mysql_fetch_array($result))
		{
			$ret_vals[]=$vals['id'];
		}
		return $ret_vals;
	}
	function markedCount()
	{
		global $database,$username;
		$sql="select count(id) from ".$this->tablename." where marked='1' and user_name='$username'";
		$result=$database->query($sql);
		$vals=mysql_fetch_array($result);
		return $vals['count'];
	}
	function matchGuid($guid)
	{
	 	global $database;
	 	$sql="select id from ".$this->tablename." where guid='$guid'";
	 	$result=$database->query($sql);
    		$count= mysql_num_rows($result);
    		if($count){$vals=mysql_fetch_array($result);return $vals['id'];}
    		else{return 0;}
	}
	function entryLink($id,$newurl=''){if(!$id){return 0;}
		if($newurl){updatefield($this->tablename,$id,'link',$newurl);return;}
		$data=$this->entryData($id);return $data['link'];}
}	

$entry = new Entry;

?>
