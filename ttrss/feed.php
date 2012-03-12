<?
	require_once "../include/session.php";
	require_once "../include/database.php";
	require_once "entry.php";
	require_once "minimalist.php";
	if($session->logged_in){$username=$session->username;}
	else{header('location:/main.php');exit;}
	if(!$username){print "No username<br>\n";}
//	error_reporting (E_ALL);


class Feed
{
	var $tablename='ttrss_feeds';
	var $entries_table='ttrss_entries';
	function feedNew($url)
	{
		if(!$url){return 0;}
		global $database,$username;
		$sql="insert into ".$this->tablename." (feed_url,title,user_name,view_mode) 
			VALUES ('$url', '','$username','default')";
		$result=$database->query($sql);
		$sql2="select LAST_INSERT_ID() as id";
		$result2=$database->query($sql2);
		$vals=mysql_fetch_array($result2);
		return $vals['id'];
	}
	function feedUrl($id,$url='')
	{
		if(!$id){return;}
		if($url){$this->updatefield($id,'feed_url',$url);}
		$data=$this->feedData($id);
		return $data['feed_url'];
	}
	function nextEntry($feed_id,$id)
	{
		if(!$id){return;}
		global $database;
		$query="select id from ". $this->entries_table ." where feed_id='$feed_id' and id > '$id' and unread=1 limit 1";
		$result=$database->query($query);
		$vals=mysql_fetch_array($result);
        return $vals[0];
	}
	function viewMode($id,$mode='')
	{
		if(!$id){return;}
		if($mode){$this->updatefield($id,'view_mode',$mode);}
		$data=$this->feedData($id);
		return $data['view_mode'];
	}
	function feedCategory($id,$newcat='')
	{
		if(!$id){return;}
		if($newcat){$this->updatefield($id,'category_id',$newcat);}
		$data=$this->feedData($id);
		return $data['category_id'];
	}
	function resetCategory($id)
	{
                if(!$id){return 0;}
                if(!$this->permission($id)){print "Insufficient permission";return 0;}
                global $database;
                $sql="update ".$this->tablename." set 'category_id'=NULL where id='$id' limit 1";
                $result=$database->query($sql);
                return $result;
	}
	function feedExpirey($id,$newexpirey='')
	{
		if(!$id){return;}
		if($newexpirey){$this->updatefield($id,'expirey',$newexpirey);}
		$data=$this->feedData($id);
		return $data['expirey'];
	}
	function feedDelete($id)
	{
		global $database;
		if(!$id){return 0;}
		$result=$database->query($entries_sql);
		$entries_sql="delete from ".$this->entries_table." where feed_id='$id' and marked='0'";
		$sql="delete from ".$this->tablename." where id='$id' limit 1";
		$result=$database->query($sql);
		return $result;
	}
	function feedData($id)
	{
		global $database;
		$sql="select * from ". $this->tablename ." where id='$id'";
		$result=$database->query($sql);
		$vals=mysql_fetch_array($result);
		return $vals;
	}
	function feedAutoScroll($id,$newautoscroll='')
	{
		global $database;
		if(!$id){return0;}
		if($newautoscroll){$this->updatefield($id,'autoscroll_px',$newautoscroll);}
		$data=$this->feedData($id);
		return $data['autoscroll_px'];
	}
	function unreadCount($id)// this should be in the feed section...
	{
		if(!$id){return;}
		global $database;
		$sql="select count(id) as count from ".$this->entries_table." where feed_id='$id' and unread='1'";
		$result=$database->query($sql);
		$vals=mysql_fetch_array($result);
		return $vals['count'];
	}
	function feedList() // returns an array of feed id's belonging to the current username
	{
		global $database,$username;
		$sql="select id from ".$this->tablename." where user_name='$username' order by title";
		$result=$database->query($sql);
		$list=array();
		while($vals=mysql_fetch_array($result))
		{
			$list[]=$vals['id'];		
		}
		return $list;
	}
	function allFeeds()
	{
	   	global $database;
      	$sql="select id from ".$this->tablename." order by title";
  		$result=$database->query($sql);
  		$list=array();
  		while($vals=mysql_fetch_array($result))
  		{
  			$list[]=$vals['id'];		
  		}
  		return $list;
	}
	function unreadEntries($id)
	{
		global $database,$username;
		$sql="select id from ".$this->entries_table." where feed_id='$id' and unread='1'";
		//$sql="select id from ".$this->entries_table." where feed_id='$id' and unread='1' order by updated ASC";
		$result=$database->query($sql);
		$list=array();
		while($vals=mysql_fetch_array($result))
		{
			$list[]=$vals['id'];		
		}
		return $list;
	}
	function readEntries($id,$count=0,$skip=0)
	{
		global $database,$username;
		$countwhere='';
		if($count){$countwhere=' limit $count ';}
		$sql="select id from ".$this->entries_table." where feed_id='$id' and unread='0' $countwhere order by updated DESC";
		$list=array();
		while($vals=mysql_fetch_array($result))
                {
                        $list[]=$vals['id'];
                }
                return $list;
	}
	function readunreadEntries($id,$count,$skip=0)
	{
		global $database,$username;
		$countwhere=$count?' limit $count ':'';
		$sql="select id from ".$this->entries_table." where feed_id='$id' $countwhere order by updated DESC";
		$result=$database->query($sql);
		$list=array();
		while($vals=mysql_fetch_array($result))
                {
                        $list[]=$vals['id'];
                }
                return $list;
	}
	function emptyFeed($id)
	{
		global $database;
		if(!$id){return;}
		$sql="UPDATE ttrss_entries SET unread = '0' WHERE feed_id = '$id'";
		$result=$database->query($sql);
	}
	function feedTitle($id,$title='')
	{
		if(!$id){return;}
		if($title){$this->updatefield($id,'title',$title);}
		$data=$this->feedData($id);
		return $data['title'];
	}
	function feedIconUrl($id,$url='')
	{
		if(!$id){return 0;}
		if($url){$this->updatefield($id,'icon_url',$url);}
		$data=$this->feedData($id);
		return $data['icon_url'];
	}
	function feedPublic($id,$ispublic='')
	{
		if(!$id){return;}
		if($ispublic){$this->updatefield($id,'public',$ispublic);}
		$data=$this->feedData($id);
		return $data['public'];
	}
	function updateFeed($id)
	{
		require_once "magpierss/rss_fetch.inc"; 	// include magpierss functionality.
		global $database,$username,$entry;
		$data   =$this->feedData($id);
		$feedurl    =$data['feed_url'];
		$registered_title=$data['title'];
		$icon_url=$data['icon_url'];
		$feed_username=$data['user_name'];
		$rss = fetch_rss($feedurl);  		// Fetch new entries using magpierss
		if(!$rss){return;}				// No entries returned - return;
		if(!$registered_title){$this->feedTitle($id,$rss->channel["title"]);} 	// New title for feed.
		$new_icon_url=$rss->image["url"];
		if($new_icon_url){$this->feedIconUrl($id,$new_icon_url);}			// Set icon url
		foreach ($rss->items as $item) 		// go through all the entries returned.
		{	
			$entry_guid = $item["id"];
			if (!$entry_guid) $entry_guid = $item["guid"];
			if (!$entry_guid) $entry_guid = $item["link"];
			if (!$entry_guid) continue;
			$rss_2_date = $item['pubdate'];
			$rss_1_date = $item['dc']['date'];
			$atom_date = $item['issued'];
			if ($atom_date != "") $entry_timestamp = parse_w3cdtf($atom_date);
			if ($rss_1_date != "") $entry_timestamp = parse_w3cdtf($rss_1_date);
			if ($rss_2_date != "") $entry_timestamp = strtotime($rss_2_date);
				
			if ($entry_timestamp == "") {
				$entry_timestamp = time();
				$no_orig_date = 'true';
			} else {
				$no_orig_date = 'false';
			}

			$entry_timestamp_fmt = strftime("%Y/%m/%d %H:%M:%S", time());
			$entry_title = $item["title"];
			$entry_link = $item["link"];

			if (!$entry_title) continue;
			if (!$entry_link) continue;

			$entry_content = $item["content:escaped"];
			if (!$entry_content) $entry_content = $item["content:encoded"];
			if (!$entry_content) $entry_content = $item["content"];
			if (!$entry_content) $entry_content = $item["description"];

			if (is_array($entry_content)) {
				$entry_content = $entry_content["encoded"];
				if (!$entry_content) $entry_content = $entry_content["escaped"];
			}
			$content_hash = "SHA1:" . sha1(strip_tags($entry_content));

			$entry_comments = $item["comments"];
      			$sql="
				SELECT id,last_read,no_orig_date,title,feed_id,content_hash  
				FROM ttrss_entries  WHERE guid = '$entry_guid' and user_name='$username'";
			$result = $database->query($sql);
			$count= mysql_num_rows($result);
			if (!$count) 		// none returned for this - entry is new
			{
        			$ref['title']=safe_param($entry_title);
        			$ref['guid']=$entry_guid;
        			$ref['link']=safe_param($entry_link);
        			$ref['content']=safe_param($entry_content);
     			   	$ref['content_hash']=safe_param($content_hash);
     			   	$ref['feed_id']=$id;
    			    	$ref['comments']=safe_param($entry_comments);
    			    	$ref['no_orig_date']=0;
    			    	$ref['user_name']=$feed_username;
    			    	$entry->entryNew($ref);
			}
			else
			{
				// updated entry - get id of original and update it.
				// $entry->entrydata set to values above etc...
			}
		}
		// don't forget to set the update date/time also to now.
	}
	function updatefield($id,$field,$value)
	{
		if(!$id){return 0;}
		if(!$field){return 0;}
		if(!$this->permission($id)){print "Insufficient permission";return 0;}
		global $database;
		$sql="update ".$this->tablename." set $field='$value' where id='$id'";
		$result=$database->query($sql);
		return $result;
	}
	function permission($id)
	{
		global $username;
		$data=$this->feedData($id);
		if($username != $data['user_name']){return 0;}else{return 1;}
	}
}

$feed=new Feed;


/*
Field		Type		Attributes	Null	Default	Extra	Action	
id		int(11)	auto_increment	 	 	 	 	 	 	 	
title		varchar(200)		No			 	 	 	 	 	 	 	
feed_url	varchar(250)		No			 	 	 	 	 	 	 	
icon_url	varchar(250)		Yes	NULL		 	 	 	 	 	 	 	
last_updateddatetime			Yes	0000-00-00 00:00:00		 	 	 	 	 	 	 	
user_name	varchar(128)		Yes	NULL		 	 	 	 	 	 	 	
show_extendedtinyint(1)			Yes	0		 	 	 	 	 	 	 	
public	tinyint(1)			Yes	0		 	 	 	 	 	 	 	
expirey	text				Yes	NULL		 	 	 	 	 	 	 	
category_id	int(10) 	UNSIGNED	Yes	NULL		 	 	 	 	 	 	 	
view_mode	enum('default', 		Yes	NULL	
		'link', 
		'proxy', 
		'extended', 
		'linknew', 
		'proxynew')			 	 	 	 	 	 	 	


*/
?>
