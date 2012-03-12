<?
	require_once "../include/session.php";
	require_once "../include/database.php";
	if($session->logged_in){$username=$session->username;}
	else{header('location:/main.php');exit;}
	if(!$username){print "No username<br>\n";}

class Category
{
	var $tablename='ttrss_categories';
	var $entries_table='ttrss_entries';
	var $feeds_table='ttrss_feeds';
	function categoryData($id)
	{
		global $database;
		$sql="select * from ".$this->tablename." where id='$id'";
		$result=$database->query($sql);
		$data=mysql_fetch_array($result);
		return $data;
	}
	function categoryList()
	{
		global $database,$username;
		$sql="select id from ".$this->tablename." where user_name='$username'";
		$result=$database->query($sql);
		$list=array();
		while($vals=mysql_fetch_array($result))
		{
			$list[]=$vals['id'];		
		}
		return $list;
	}
	function nextEntry($cat_id,$id)
	{
		if(!$id){return;}
		global $database;
		$te=$this->entries_table;
		$tf=$this->feeds_table;
		$query="select $te.id from $te,$tf  where $tf.category_id='$cat_id' and $te.feed_id=$tf.id and $te.id > $id order by $te.id ASC limit 1";
//		$query="select id from ". $this->entries_table ." where feed_id='$feed_id' and id > '$id' and unread=1 limit 1";
		$result=$database->query($query);
		$vals=mysql_fetch_array($result);
		return $vals[0];
	}
	function previousEntry($cat_id,$id)
	{
		if(!$id){return;}
		global $database;
		$te=$this->entries_table;
		$tf=$this->feeds_table;
		$query="select $te.id from $te,$tf  where $tf.category_id='$cat_id' and $te.feed_id=$tf.id and $te.id < $id order by $te.id DESC limit 1";
		$result=$database->query($query);
		 $vals=mysql_fetch_array($result);
         return $vals[0];
	}
	function categoryName($id,$name)
	{
		global $database,$username;
		if(!$id){return;}
		if(!$name){return;}
		$sql="update ".$this->tablename." set name='$name' where id='$id' and user_name='$username' limit 1";
		$result=$database->query($sql);
	}
	function categoryDescription($id,$desc)
	{
		global $database,$username;
		if(!$id){return;}
		if(!$desc){return;}
		$sql="update ".$this->tablename." set description='$desc' where id='$id' and user_name='$username' limit 1";
		$result=$database->query($sql);
	}
	function categoryFeeds($id)
	{
		if(!$id){return;}
		global $database;
		$sql="select id from ".$this->feeds_table." where category_id='$id'";
		$result=$database->query($sql);
		$list=array();
		while($vals=mysql_fetch_array($result))
		{
			$list[]=$vals['id'];		
		}
		return $list;
	}
	function unreadCount($id)
	{
		if(!$id){return;}
		global $database,$username;
		$te=$this->entries_table;
		$tf=$this->feeds_table;
		$sql="select count($te.id) as unread from $te ,$tf  where 
			$tf.category_id='$id' and $te.feed_id=$tf.id and $te.unread='1'";
		$result=$database->query($sql);
		$data=mysql_fetch_array($result);
		return $data['unread'];
	}
	function readCount($id)
	{
		if(!$id){return;}
		global $database,$username;
		$te=$this->entries_table;
		$tf=$this->feeds_table;
		$sql="select count($te.id) as readcount from $te ,$tf  where $tf.category_id='$id' and $te.feed_id=$tf.id and $te.unread='0'";
		$result=$database->query($sql);
		$data=mysql_fetch_array($result);
		return $data['readcount'];
    }
	function unreadEntries($id)
	{
		if(!$id){return;}
		global $database,$username;
		$te=$this->entries_table;
		$tf=$this->feeds_table;
		$sql="select $te.id from $te,$tf  where $tf.category_id='$id' and $te.feed_id=$tf.id and $te.unread='1' order by $te.id ASC";
		#$sql="select $te.id from $te,$tf  where $tf.category_id='$id' and $te.feed_id=$tf.id and $te.unread='1' order by $te.id ASC";
		$result=$database->query($sql);
		$list=array();
		while($vals=mysql_fetch_array($result))
		{
			$list[]=$vals['id'];		
		}
		asort($list);
		return $list;
	}
	function emptyCategory($id)
	{
		if(!$id){return;}
		global $database,$username;
		$te=$this->entries_table;
		$tf=$this->feeds_table;
		$table=$this->tablename;
		$sql="update $te,$tf set $te.unread='0' where $tf.category_id='$id' and $te.feed_id=$tf.id and $te.unread='1'";
		$result=$database->query($sql);
		return $result;
	}
	function non_category()
	{
		global $database,$username;
		$sql="select id from ".$this->feeds_table." where category_id is NULL and user_name='$username'";
		$result=$database->query($sql);
		$list=array();
		while($vals=mysql_fetch_array($result))
		{
			$list[]=$vals['id'];		
		}
		return $list;
	}
}
$category=new Category;


?>
