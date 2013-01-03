<?
        require_once "../include/session.php";    // session include    - gives $username
        require_once "../include/database.php";   // database function  - use "global $database;" in functions
        require_once "feed.php";                                // feed object          - use "global $feed;" in functions
        require_once "entry.php";                               // entry obj            - use "global $entry;" in functions
        require_once "category.php";                            // category obj                 - use "global $category;" in functions
        $view_options = array('Default','Link','Extended','Proxy'); //List of options for viewing
        if(isset($_REQUEST['op']))                              //Use isset to avoid warnings.  :)
        {
        $op=$_REQUEST['op'];
        switch($op){

		//print_entries($id,$mode,$readunread) //feed_entries, category entries, 
		case "feed_entries":	
			print_feed_entries();
			break;
		case "category_entries":
			print_category_entries();
			break;
		case "feed_list":
			print_feed_list();
			break;
		case "category_list":
			print_category_list();
			break;
		case "view_entry":
			print_entry();
			break;
		case "empty_category";
			empty_category();
			break;
		case "unread_feed":
			unread_feed();
			break;
		case "read_category":
			read_feed();
			break;
		case "togglemark":
			toggle_mark();
			break;
		case "extended_content":
			viewExtendedPage();
			break;
		case "marked_entries":
			view_marked_entries();
			break;
		case "mark_read":
			mark_entry_read();
			break;
		case "mark_list_read":
			mark_list_read();
			break;
		case "mark_unread":
			mark_entry_unread();
			break;
		case "update_viewmode":
			update_view_mode($_REQUEST['id'],$_REQUEST['view_mode']);
			break;
		case "print_menu":
			print_menu($_REQUEST['view_mode'],$_REQUEST['id']);
			break;
		case "view_customize_dropdown":
			print_customize_dropdown($_REQUEST['id']);
			break;
		case "update_category":
			update_category($_REQUEST['id'],$_REQUEST['category']);
			break;
		case "add_feed":
			add_feed($_REQUEST['url']);
			break;
		case "delete_feed":
			delete_feed($_REQUEST['id']);
			break;
		case "rename_feed":
			rename_feed($_REQUEST['id']);
			break;
		case "update_expirey":
			update_expirey($_REQUEST['id']);
			break;
		case "update_exclude_list":
			update_exclude_list($_REQUEST['id']);
			break;
		case "rename_category":
			rename_category($_REQUEST['id']);
			break;
		case "describe_category":
			describe_category($_REQUEST['id']);
			break;
		case "json_feed_entries":
			json_feed_entries($_REQUEST['id']);
			break;
		case "json_category_entries":
			json_category_entries($_REQUEST['id']);
			break;
		case "print_feed_config":
			print_feed_config($_REQUEST['id']);
			break;
		case "update_feed_link":
			update_feed_link($_REQUEST['id'],$_REQUEST['link']);
			break;
		case "update_feed_autoscroll":
			update_feed_autoscroll($_REQUEST['id'],$_REQUEST['autoscroll']);
			break;
		case "update_feed_info":	#for editing multiple things for the feed, from the prefs page
			update_feed_info($_REQUEST['id']);
			break;
		case "update_instapaper":
			update_instapaper($_REQUEST['username'],$_REQUEST['password']);
			break;
		case "feed_prefs":
			feed_prefs();
			break;
		case "raw_feeds":
			print_raw_feeds();
			break;
		case "raw_categories":
			print_raw_categories();
			break;
		case "next_entry":
			get_next_entry($_REQUEST['id'],$_REQUEST['view_mode'],$_REQUEST['view_mode_id']);
			break;
		case "previous_entry":
			get_previous_entry($_REQUEST['id'],$_REQUEST['view_mode'],$_REQUEST['view_mode_id']);
			break;
	};
	}  // End of if isset op
	function get_previous_entry($id,$view_mode,$mode_id)
	{
		global $feed,$category;
		if($view_mode == 'category')
		{
			print $category->previousEntry($mode_id,$id);
		}
		if($view_mode == 'feed')
		{
			print $feed->previousEntry($mode_id,$id);
		}
	}
	function get_next_entry($id,$view_mode,$mode_id)
	{
		global $feed,$category;
		if($view_mode == 'category')
		{
			print $category->nextEntry($mode_id,$id);
		}
		if($view_mode == 'feed')
		{
			print $feed->nextEntry($mode_id,$id);
		}
	}
	function update_feed_link($id,$link)
	{
		global $feed;
		if(!id || !$link){print "Not updated!";return;}
		$feed->feedUrl($id,$link);
		print "Updated feed link...";
		return;
	}
	function rename_feed($id)
	{
		global $feed;
		if(!isset($_REQUEST['name'])){return;}
		$new_name=$_REQUEST['name'];
		if(!$id){return;}
		$id=(integer)$id;
		$feed->feedTitle($id,$new_name);
		print "Updated...\n";
	}
	function rename_category($id)
	{
		global $category;
		if(!isset($_REQUEST['name'])){return;}
		$new_name=$_REQUEST['name'];
		if(!$id){return;}
		$id=(integer)$id;
		$category->categoryName($id,$new_name);
		print "Updated...\n";
	}
	function update_exclude_list($id)
	{
		global $feed;
		if(!isset($_REQUEST['exclude_list'])){return;}
		$exclude_list=$_REQUEST['exclude_list'];
		if(!$id){return;}
		$id=(integer)$id;
		$feed->feedExcludeList($id,$exclude_list);
		print "Updated...\n";
	}
	function update_expirey($id)
	{
		global $feed;
		if(!isset($_REQUEST['expirey'])){return;}
		$expirey=$_REQUEST['expirey'];
		if(!$id){return;}
		$id=(integer)$id;
		$feed->feedExpirey($id,$expirey);
		print "Updated...\n";
	}
	function describe_category($id)
	{
		global $category;
		if(!isset($_REQUEST['description'])){return;}
		$new_desc=$_REQUEST['description'];
		if(!$id){return;}
		$id=(integer)$id;
		$category->categoryDescription($id,$new_desc);
		print "Updated...\n";
	}
	function delete_feed($id)
	{
		global $feed;
		if(!$id){return;}
		$id=(integer)$id;
		$feed->feedDelete($id);
		print "Deleted Feed";
	}
	function add_feed($url)
	{
		global $feed;
		if(!$url){return;}
		$feed->feedNew($url);
		print "Added feed";
	}
	function update_category($id,$category)
	{
		global $feed;
		if($category == 'null')
			{$feed->resetCategory($id);}
		else
			{$feed->feedCategory($id,$category);}
		print "Category:".$feed->feedCategory($id);
	}
	function update_view_mode($id,$view_mode)
	{
		global $feed;
		$feed->viewMode($id,$view_mode);
		print "View mode:".$feed->viewMode($id);
	}
	//  To print the menu above the entry listing
	function print_menu($viewmode='',$id='')
	{
		global $category,$feed,$entry,$view_options;
		if(isset($_REQUEST['skip_div'])){$skip_div=$_REQUEST['skip_div'];}else{$skip_div=0;}
		if(!$skip_div){print "<div id='settings_div'>";}
		print "
		<table width='100%'>
		<tr>
			<td align='left'>
				<div id='menu_status' style='height:20px;width:140px'></div>
			</td>
				<td align='right'>
					<a href='javascript:toggle_visible(\"entries_list_div\")'>Show/Hide</a>
					<a href='javascript:view_starred();'>Starred</a>&nbsp;
					<a href='javascript:view_read();'>Read</a>&nbsp;
					<a href='prefs.php' target='_default'>Preferences</a>&nbsp;
					<a href='javascript:mark_list_read();'>Mark all read</a>
				</td>
				<td>
		";
		print "<div id='toggle_settings_div'><a href='javascript:toggle_visible(\"modify_div\");'>^</a></div>\n";
		print "
				</td>
			</tr>
			</table>
			<div id='modify_div' style='border:1px solid #979797;display:none'>
		";
		#print either feed or category menu here:
		if($viewmode=='category'){print_category_menu($id);}
		elseif($viewmode=='feed'){print_feed_menu($id);}
		#end the menu div
		print "</div>\n";
		if(!$skip_div){print"	</div>\n";}
	}
	function print_customize_dropdown($feed_id)
	{
		global $feed,$category,$view_options;
		print "
                <form name='view_customize_form'>
                <select name='select' onchange='customize(this.form)'>
                <option value=''>Customize
			";
		$current_view_mode=$feed->viewMode($feed_id);
            $current_category   =$feed->feedCategory($feed_id);
            print "<option value=''>--View Mode--";
            foreach($view_options as $option)
            {
                $lc_option=strtolower($option);
                if($lc_option == $current_view_mode){$option="*$option";}
                print "<option value='$lc_option'>$option\n";
            }
		 print "<option value='none'>--Category--\n";
            $id_list=$category->categoryList();
            foreach($id_list as $cat_id)
            {
                $data=$category->categoryData($cat_id);
                $name=$data['name'];
                if($cat_id == $current_category){$name="*$name";}
                print "<option value='$cat_id'>$name\n";
            }
            print "<option value='null'>None\n";
            print "
                <option value=''>--Delete--
                <option value='delete'>Delete Feed
                </select>\n
                </form>
			";
	}
	function print_feed_menu($feed_id)
	{
		global 			$category,$feed,$entry,$view_options;
		$title=			$feed->feedTitle($feed_id);
        $link=			$feed->feedUrl($feed_id);
		$expirey=		$feed->feedExpirey($feed_id);
		$autoscroll_px=	$feed->feedAutoScroll($feed_id);
		$exclude_list=	$feed->feedExcludeList($feed_id);
		print "
			<table>
			<tr>
				<td>
				</td>

		<td align='right'>
                <div id='customize_dropdown'>
		";
		print_customize_dropdown($feed_id);
		print"
				</div>
				</td>
				<td align='right' valign='center'>
					<form name='rename_feed_form'>
					<input type='text' size=20 name='rename_feed_text' value='$title'>
					<input type='button' value='Rename' onclick='rename_feed(this.form)'>
					</form>
				</td>
				<td>
					<form name='update_link_form'>
					<input type='text' name='update_link_text' value='$link'>
					<input type='button' value='Change Link' onclick='update_link(this.form)'>
					</form>
				</td>
				<td>
					<form name='update_feed_expirey'>
					<input type='text' size=10 name='update_feed_expirey' value='$expirey'>
					<input type='button' value='Update Expirey' onclick='update_expirey(this.form)'>
					</form>
				</td>
				<td>
					<form name='update_feed_autoscroll'>
					<input type='text' size=5 name='update_feed_autoscroll' value='$autoscroll_px'>
					<input type='button' value='Update Autoscroll' onclick='update_autoscroll(this.form)'>
					</form>
				</td>
				<td>
					<form name='update_exclude_list'>
					<input type='text' size=20 name='rename_exclude_text' value='$exclude_list'>
					<input type='button' value='Exclude' onclick='update_exclude(this.form)'>
					</form>
				</td>
				</tr>
				</table>
		";
	}
	function print_category_menu($id)
	{
		global $category;
		$data=$category->categoryData($id);
		$name=$data['name'];
		$desc=$data['description'];
		print "
			<table>
			<tr>
			<td>
				<form name='rename_category_form'>
					<input type='text' name='rename_category_text' value='$name'>
					<input type='button' value='Rename' onclick='rename_category(this.form)'>
				</form>
			</td>
		";
		print "
			<td>
				<form name='description_form'>
					<input type='text' name='describe_category_text' value='$desc'>
					<input type='button' value='Describe' onclick='describe_category(this.form)'>
				</form>
			</td>
			</tr>
			</table>
		";
	}

	//  Marks an individual entry as read without having to read it
	function mark_entry_read()
	{
		global $entry;
		$id=$_REQUEST['id'];
		if(!$id){print "No id entered\n";return;}
		$entry->markRead($id);
		print "Marked Read\n";
	}
	function mark_entry_unread()
	{
		global $entry;
		$id=$_REQUEST['id'];
		if(!$id){print "No id entered\n";return;}
		$entry->markUnread($id);
		print "Marked Unread\n";
	}
	function toggle_mark()
	{
		global $entry;
		$id=$_REQUEST['id'];
		if(!$id){return;}
		$data=$entry->entryData($id);
		$marked=$data['marked'];
		$markunmark='';			// placeholder for the image source.
		if($marked)
		{
			$entry->setMarked($id,0);
			$markunmark="mark_unset.png";
		}
		else
		{
			global $database;
			$userinfo=$database->getUserInfo($_SESSION['username']);;
			$entry->setMarked($id,1);
			$markunmark="mark_set.png";
			$u=$userinfo['instapaper_username'];
			$p=$userinfo['instapaper_password'];
			if($u)
			{
				$sel=urlencode(un_escape($data['content']));
				$link=un_escape($data['link']);
#				print "link=$link<br>\n";
				$title=urlencode($data['title']);
				$inst_root="http://www.instapaper.com/api/add";
				$inst_link="$inst_root?username=$u&password=$p&url=$link&selection=$sel&title=$title";
				$curl_handle=curl_init();
				curl_setopt($curl_handle,CURLOPT_URL,$inst_link);
				curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
				curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
				$buffer = curl_exec($curl_handle);
				curl_close($curl_handle);
			}
		}
		print "<img src='images/$markunmark' alt='Set mark' onclick='javascript:toggleMark($id);'>\n";
	}

	function mark_list_read()
	{
		global $entry;
		$id=$_POST['id'];
		foreach($id as $i)
		{
			$entry->markRead($i);
		}
		print "Emptied\n";
	}

	function unread_feed()
	{
		global $feed;
		$id=$_REQUEST['id'];
		if(!$id){return;}		
		$unread=$feed->unreadCount($id);
		return $unread;
	}
	function unread_category()
	{
		global $category;
		$id=$_REQUEST['id'];
		if(!$id){return;}		
		$unread=$category->unreadCount($id);
		return $unread;
	}

	function print_entry()
	{
		global $entry,$feed;
		$id=		$_REQUEST['id'];
		if(!isset($id)){print "No feed specified";return;}
//		$previous=	$_REQUEST['previous'];
//		$next=		$_REQUEST['next'];
//		$feedorcat=	$_REQUEST['method'];
		$debug=		$_REQUEST['debug'];
		$feed_id=	$entry->feedID($id);
		$data=		$entry->entryData($id);
		$feed_data=	$feed->feedData($feed_id);
		$feedname=	substr($feed_data['title'],0,40);
		$feedtitle=	substr($data['title'],0,150);
		if(!$data){print "No data for entry $id found";return;}
		if(!$autoscroll){$autoscroll=0;}

		//set a few variables.  
		$autoscroll=	$_REQUEST['autoscroll']	?$_REQUEST['autoscroll']:$feed_data['autoscroll_px'];
		$feed_icon=		$data['icon_url']		?"<img class=\"feedIcon\" src=\"".$data['icon_url']."\">":"&nbsp;";
		$view_mode=		$_REQUEST['view_mode']	?$_REQUEST['view_mode']:$feed->viewMode($feed_id);
		$markunmark=	$data['marked']			?'mark_set.png':'mark_unset.png';
		$entry_comments=$data["comments"] && $data["link"] != $data["comments"]?"(<a href=\"".$data["comments"]."\">Comments</a>)":"";

		if($debug)
		{
			print "id=$id<br>\n";
			print "title={$data['title']}<br>
				link={$data['link']}<br>
				marked={$data['marked']}<br>
				show_extended={$data['show_extended']}<br>
				content={$data['content']}<br>\n";
		}

		//print the header, always the same.
		print "<div id='content_container'>\n";
		print "<div id='table_container' style='z-index:1;position:relative;opacity:1;background-color:#FFFFFF'>\n";
		print "<table width='100%'>
				<tr class='evenunread'>
				<td width=150>$feedname</td>
				<td style='text-overflow:ellipsis;white-space:nowrap;overflow:hidden'>$feedtitle</td>
			";

		//print a select for some ease of use
		print "<td width=100 align='right'>
		<div id='viewentry-dropdown'>
			<select name='select' onchange='customizeentry(this.form)'>
				<option value=''>--Mark--</option>
				<option value='unread'>Unread</option>
				";
		foreach($view_options as $option)
		{
			$lc_option=strtolower($option);
			if($lc_option == $view_mode){$option="*$option";}
			print "<option value='$lc_option'>$option\n";
		}
		print "
				<option value=''>--View--</option>
				<option value='viewdefault'>Default</option>
				<option value='viewproxy'>Proxy</option>
				<option valie='viewlink'>Link</option>
			</option>
		</div></td>
		";

		//Print a tweet button
			print "<td align='right' width=18><a href='https://twitter.com/home?status=". $data['link']. "' target='_blank' ><img src='025.png' height='15' ></a></td>\n";
		//Print the new, previous, etc buttons
			print "
			<td align='right' width=22><a href='".$data['link']. "' target='_blank'>New</a>
			<td align='right' width=22><a href=\"javascript:showPreviousEntry('$id');\">Previous</a>
			<td align='right' width=22><a href=\"javascript:showNextEntry('$id');\">Next</a>
			<td align='right' width=16><div id='EMARKPIC-$id'><img src='images/$markunmark' alt='Set mark' onclick='javascript:toggleMark($id);'></div></td>
		";
		if($view_mode == 'default'){print "<td width=11></td>";}
		print "	</tr></table>";
		print "</div>\n";
		switch($view_mode){
			case "link":
				//print a div around the iframe, that starts scrolled down to autoscroll
				print "<div id='iframe_container' style=\"height:1000%;z-index=0;position:relative;top:-$autoscroll;overflow:none;height:100%\">\n";
				print "<iframe id='view_iframe' src=\"".$data['link']. "\" style='overflow:auto;height:1000%;frameborder:0;width:100%' security=\"restricted\">\n</iframe>\n";
				print "</div>\n";
				print "</div>\n";
				break;
			case "proxy":
				print_link_data($data['link']);
//				print_proxy_data($data['link']);
				break;
			case "extended":
				print "<div class=\"postContent\">" . $data["content"] . "</div>\n";
				viewExtendedPage($data['link']);
				break;
			case "default":
				print "<div id='entry_content' style='position:absolute;height:70%;overflow:auto;width:100%' >" . $data["content"] . "</div>\n";
				break;
		};
		print "</div>";
		$entry->markRead($id);
	}

	function print_feed_list()
	{
		global $feed;
		$id_list=$feed->feedList();
		$total_unread=0;
		print "<ul class=\"feedList\" id=\"feedList\">\n";
 		foreach($id_list as $id)
		{
			$data=$feed->feedData($id);
			$unread=$feed->unreadCount($id);
			$title = $data['title'];
			$class = "odd";
			if ($unread > 0) $class .= "Unread";
			$total_unread += $unread;
			printFeedEntry($id, $class, $title, $unread, "icons/$id.ico");
		}
		print "</ul>\n";
                print "
                <td align='right'>
                                        <form name='add_feed_form'>
                                        <input type='text' name='add_feed_text'>

                                        <input type='button' value='Add' onclick='add_feed(this.form)'>
                                        </form>
                                </td>
                ";# print the add feed box
	}
	function print_category_list()
	{
		global $category,$feed;
		if(isset($_REQUEST['id'])){$view_id=$_REQUEST['id'];}else{$view_id=0;}
		$id_list=$category->categoryList();
		print '<ul class="feedList" id="feedList">';
		foreach($id_list as $id)
		{
			$data=$category->categoryData($id);
			$unread=$category->unreadCount($id);
			if($id == $view_id)
			{
				$expand_id='0';
				$print_cat_list=1;
			}
			else
			{
				$expand_id=$id;
				$print_cat_list=0;
			}
			$class = "odd";
			if ($unread > 0) $class .= "Unread";
			$title=$data['name'];
			print "\n<li id='CATROW-$id' class='$class'>\n";
			print "\t<div id='FEEDN-$id' style='width:240px;'>\n";
			print "\t\t<div id='LBL-$id' style='float:left;'><a href=\"javascript:categoryList($expand_id);\">+</a><a 
				href=\"javascript:category_entries($id);\">$title</a></div>\n";
			print "\t\t<div style='float:left;'>(</div>\n";
			print "\t\t<div id=\"FEEDU-$id\" style='float:left;'>$unread</div>\n";
			print "\t\t<div style='float:left;'>)</div>\n";
#			print "\t\t<div style='float:left;'>($read)</div>\n";
			print "\t</div>\n";
			print "</li>\n";
			print "<br>\n";
			if($print_cat_list)
			{
				$id_list=$category->categoryFeeds($id);
				foreach($id_list as $id)
				{
					$data=$feed->feedData($id);
					$unread=$feed->unreadCount($id);
					$title = $data['title'];
					$class = "odd";
					if ($unread > 0) $class .= "Unread";
					$total_unread += $unread;
					printFeedEntry($id, $class, $title, $unread, "icons/$id.ico",1);
					print "<br>\n";
				}
			}
		}
		print "<br>\n";
		//get list of non-categorized entries and print them
		$non_list=$category->non_category();
		$total_unread=0;
		foreach($non_list as $id)
		{
			$data=$feed->feedData($id);
			$unread=$feed->unreadCount($id);
			$title = $data['title'];
			$class = "odd";
			if ($unread > 0) $class .= "Unread";
			$total_unread += $unread;
			if(!$title){$title=$data['link'];}
			printFeedEntry($id, $class, $title, $unread, "icons/$id.ico");
			print "<br>\n";
		}
		print "</ul>\n";
		print "
		<td align='right'>
                                        <form name='add_feed_form'>
                                        <input type='text' name='add_feed_text'>

                                        <input type='button' value='Add' onclick='add_feed(this.form)'>
                                        </form>
                                </td>
		";# print the add feed box
	}
	//
	//	Print the unread entries in a given category.
	//
	function print_category_entries()
	{
		global $feed,$entry,$category;
		$id=$_REQUEST['id'];
		if(!$id){print "No id for print_category_entries<br>\n";return;}
		$entry_list=$category->unreadEntries($id);
		if(!$entry_list){print "No entries found";return;}
		print_entry_list($entry_list,'category');
	}
	//
	//	Print the unread entries in a given feed.
	//
	function print_feed_entries()
	{
		global $feed,$entry;
		$id=$_REQUEST['id'];
		if(!$id){print "No id for print_feed_entries<br>\n";return;}
		if(!isset($_REQUEST['view_unread']))
		{
			$entry_list=$feed->unreadEntries($id);
		}
		else
		{
			$entry_list=$feed->readEntries($id,1);
		}
		#$unread_entry_list=$feed->readEntries($id,100);
		#if(!$entry_list && !$unread_entry_list){print "No entries found";return;}
		print_entry_list($entry_list,'feed');
		exit();
	}
	//
	//  Print entries - catchall for feed/category and read/unread/starred
	//
	function print_entries($id,$mode,$readunread)
	{
		global $feed,$entry;
		switch($readunread){
			case 'read':
				if($mode == 'feed')		{$list=$feed->readEntries($id);}
				if($mode == 'category')	{$list=$category->readEntries($id);}
			case 'unread':
				if($mode == 'feed')		{$list=$feed->unreadEntries($id,100);}
				if($mode == 'category')	{$list=$category->unreadEntries($id,100);}
			case 'marked':
				if($mode == 'feed')		{$list=$feed->markedEntries($id);}
				if($mode == 'category')	{$list=$category->markedEntries($id);}
		}
		print_entry_list($list,$mode);
	}
	//
	//	Print all marked (starred) entries..
	//
	function view_marked_entries()
	{
		global $entry;
		$entry_list=$entry->markedEntries();
		if(!$entry_list){print "No entries found";return;}
		print_entry_list($entry_list);
	}
	function printFeedEntry($id,$class,$feed,$unread,$icon,$spacing=0,$next=0,$previous=0){
		if(!$feed){$feed='--untitled--';}
		print "<li id='FEEDROW-$id' class='$class'>";
		print "<div id='FEEDN-$id' style='width:240px;'>\n";
		if($spacing){print"<div id='SPC-$id' style='float:left'><img src=\"images/blank_icon.gif\"></div>\n";}
		print "<div id='LBL-$id' style='float:left;'><a href=\"javascript:feed_entries($id);\">$feed</a></div>\n";
		print "<div style='float:left;'>(</div>
			<div id=\"FEEDU-$id\" style='float:left;'>$unread</div>
			<div style='float:left;'>)</div>
			";
		print "</div>\n";
		print "</li>\n";
	}
	//
	//	Print the entries passed from another function - requires a list of entry id's to print.
	//
	function print_entry_list($id_list,$view_mode='',$read=0)
	{
		global $entry,$feed;
		$count=0;
		$height=175;if($view_mode == 'feed'){$height=157;}
		$readunread=$read?"read":"unread";
		print "<form action='backend.php' method='POST' id='entries_form'>\n";
		print "<input type='hidden' name='op' value='mark_list_read'>\n";
		print "<table class='headlinesList' id='headlinesList' width='100%'>\n";
		//print "<div class='entries_form'>\n";
		for($i=0;$i<sizeof($id_list);$i++)
		{
			$id		=$id_list[$i];
			$previous	=$id_list[$i-1];if(!$previous){$previous=0;}
			$next		=$id_list[$i+1];if(!$next){$next=0;}
			if($count%2 == 0){$evenodd='even';}else{$evenodd='odd';}
			$line		=$entry->entryData($id);
			$entry_id	=$id;
			$title	=$line['title'];
			$updated	=$line['updated'];$updated=pretty_date($updated);
			$marked	=$line['marked'];
			$link		=$line['link'];
			$feed_data	=$feed->feedData($line['feed_id']);
			$feed_name	=$feed_data['title'];
			$autoscroll	=$feed_data['autoscroll_px']?$feed_data['autoscroll_px']:0;
			$js_link	="javascript:show_entry($id);";
			if($marked)	{$markunmark='mark_set.png';}else{$markunmark='mark_unset.png';}
			print "<tr class='{$evenodd}$readunread' id='RROW-$id'>\n";
			print "<input type='hidden' name='id[]' value='$id'>\n";
			print "\t<td valign='center' align='center'><div id='FMARKPIC-$id'><img src='images/$markunmark' alt='Set mark' onclick='javascript:toggleMark($id);'></div></td>\n";
			print "\t<td ><a href='javascript:remove_entry($id);'>-</a></td>\n";
			print "\t<td width='22%'><a href='$js_link'> $feed_name</a></td>\n";
			print "\t<td width='60%'><a href='$js_link'> $title </a></td>\n";
			print "\t<td width='12%'><a href='$js_link'>".$line['updated']."</a></td>\n";
			print "\t<td align='right'><a href='$link' target='_blank'>-></a></td>\n";
			print "</tr>\n";
			$count++;
		}
		print "</form>\n";
		print "</table>\n";
		//print "</div>\n";
		return;
	}
	//
	//	Prints the HTML returned from a lynx session of that page.  A poor person's proxy, if you will.
	//
	function viewExtendedPage($link){
		global $entry;
		$id=$_REQUEST['id'];
		if(!$link){$link=$entry->entryLink($id);}
		print "id=$id<br>\n";
		print "link=$link<br>\n";
		exec("/usr/bin/lynx -nolist -hiddenlinks=ignore -dump \"$link\"", $tmpary);
		$extended_content=implode($tmpary,"\n");
		$extended_content=ereg_replace("\n","<br>",$extended_content);
		print $extended_content;
		print "End extended content<br>\n";
	}
	//
	//	here's the deal. if it's today, I want just the time.  If it's not today, I want just the date. okay?
	//
	function pretty_date($date_in){
		list($date,$time)=split(" ",$date_in);
		$today=date("Y-m-d");
		if($date==$today)
		{
			list($h,$m,$s)=split(":",$time);
			if($h<12){$label='AM';}else{$label='PM';}
			if($h>=12){$h-=12;}
			if($h==0){$h=12;}
			return "$h:$m:$s $label";
		}
		else{return $date;}
	}
	//
	//	Another "proxy" - prints the html straight from the link using libcurl.
	//
	function print_link_data($link)
	{
		$link=un_escape($link);
		$ch = curl_init();
		$timeout = 10; // set to zero for no timeout
		curl_setopt ($ch, CURLOPT_URL, $link);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER,1);
		$file_contents=curl_exec($ch);
		$curl_error=curl_error($ch);
		curl_close($ch);
		$domain=str_replace("http://","",$link);
		$list=split("/",$domain);
		$domain=$list[0];
		$file_contents = str_replace("script",'!--',$file_contents);
		$file_contents = str_replace("stylesheet",'',$file_contents);
		$file_contents = str_replace("http://$domain/","",$file_contents);
		$file_contents = str_replace("src=\"","src=\"http://$domain/",$file_contents);
		print "$file_contents";
		print "--end of curl--<br>\n";
	}
	function print_proxy_data($link)
	{
		$proxyurl="https://home.chriskaufmann.com/rss/gui/nph-proxy.cgi/010110A/x-proxy/start?URL=$link&rc=checked&rs=checked&fa=checked&br=checked&if=checked";
 header("Location: $proxyurl");
return;
		$ch = curl_init();
		$timeout = 5; // set to zero for no timeout
		curl_setopt ($ch, CURLOPT_URL, $link);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
		ob_start();
		curl_exec($ch);
		curl_close($ch);
		$file_contents = ob_get_contents();
		ob_end_clean();
	
		print $file_contents;
	}
	function json_feed_entries($id)
	{
		global $feed;
		$entry_list=$feed->unreadEntries($id);
		print_json_entries($entry_list);
	}
	function json_category_entries($id)
	{
		global $category;
		$entry_list=$category->unreadEntries($id);
		print_json_entries($entry_list);
	}

	function print_json_entries($entry_list)
	{
		global $entry,$feed;
		print "\n\t{\"entries\":{\n";	//open the json
		//foreach($entry_list as $id)
		for($i=0;$i<sizeof($entry_list);$i++)
		{
			$id		=$entry_list[$i];
			$data		=$entry->entryData($id);
			$entry_id	=$id;
			$title		=$data['title'];
			$updated	=$data['updated'];$updated=pretty_date($updated);
			$marked	=$data['marked'];
			$link		=$data['link'];
			$feed_data	=$feed->feedData($data['feed_id']);
			$feed_id	=$data['feed_id'];
			$feed_name	=$feed_data['title'];
			$unread	=$data['unread'];
			$category_id	=$feed->feedCategory($data['feed_id']);
			$prev_id='';if($entry_list[$i-1]){$prev_id=$entry_list[$i-1];}
			$next_id='';if($entry_list[$i+1]){$next_id=$entry_list[$i+1];}
			if($marked)	{$markunmark='mark_set.png';}else{$markunmark='mark_unset.png';}
			print "\t\t\"$id\": { \n";	// open this entry
			print "\t\t\"next_id\": \"$next_id\",\n";
			print "\t\t\"previous_id\": \"$prev_id\",\n";
			$title=str_replace('&#34;',"",$title);
			$title=str_replace('"',"",$title);
			$title=str_replace("\n","",$title);
			print "\t\t\"title\": \"$title\",\n";
			print "\t\t\"updated\": \"$updated\",\n";
			print "\t\t\"marked\": \"$marked\",\n";
			print "\t\t\"unread\": $unread,\n";
			$link=str_replace('&#34;',"",$link);
			$link=str_replace('"',"",$link);
			$link=str_replace("\n","",$link);
			print "\t\t\"link\": \"$link\",\n";
			print "\t\t\"feed_name\": \"$feed_name\",\n";
			print "\t\t\"category_id\": \"$category_id\",\n";
			print "\t\t\"feed_id\": \"$feed_id\",\n";
			print "\t\t\"img\": \"$markunmark\"\n";
			print "\t\t}";		// close this entry
			if($next_id){print ',';}
			print "\n";
		}
		print "\t\t}\n\t}\n";			// close the json
	}
	function edit_feed_info($id)
	{
		global $feed;
		if($_REQUEST['t'])       {$feed->feedTitle($id,$_REQUEST['T']); }
		if($_REQUEST['l'])       {$feed->feedUrl($id,$_REQUEST['l']); }
		if($_REQUEST['pub'])     {$feed->feedPublic($id,$_REQUEST['pub']); }
		if($_REQUEST['exp'])     {$feed->feedExpirey($id,$_REQUEST['exp']); }
		if($_REQUEST['cat'])     {$feed->feedCategory($id,$_REQUEST['cat']); }
		if($_REQUEST['viewmode']){$feed->viewMode($id,$_REQUEST['viewmode']); }
	}
	function update_feed_autoscroll($id,$autoscroll)
	{
		global $feed;
		$newas=$feed->feedAutoScroll($id,$autoscroll);
		print "$newas";
	}
	function update_instapaper($newusername,$newpassword)
	{
		global $database,$username;
		$database->updateUserField($username, 'instapaper_username', $newusername);
		$database->updateUserField($username, 'instapaper_password', $newpassword);
		print "Instapaper values updated";
		#instapaper_username and instapaper_password
	}
	function feed_prefs()
	{
		global $feed,$category;
		$editid=0;
		if($_REQUEST['id'])		{$editid=$_REQUEST['id'];}
		$id_list=$feed->feedList();
		print "<p><table width=\"100%\" class=\"prefFeedList\" id=\"prefFeedList\">";
		print "<tr class=\"title\"><td>Select</td><td width=\"35%\">Title</td><td width=\"35%\">Link</td>
		<td>Expirey</td><td>Public</td><td>Category</td><td>Viewmode</td></tr>";
		$lnum=0;
 		foreach($id_list as $feed_id)
		{
			$class=($lnum % 2) ? "even" : "odd";
			$data=$feed->feedData($feed_id);
			$catname=$category->categoryName($data['category_id']);
			$href="<a href=\"javascript:editFeed($feed_id);\">";
			print "<tr class=\"$class\" id=\"FEEDR-$feed_id\">";
			print "<td><input onclick='toggleSelectRow(this);' type=\"checkbox\" id=\"FRCHK-".$feed_id."\"></td>";
			print "<td>$href".$data['title']."</a></td>\n";
			print "<td>$href".$data['feed_url']."</a></td>\n";
			print "<td>$href".$data['expirey']."</a></td>\n";
			print "<td></td>\n";//public
			print "<td>$href".$catname."</a></td>\n";//category
			print "<td>$href".$data['view_mode']."</a></td>\n";
			print "</tr>\n";
		}
		print "</table>\n";
	}
	function print_raw_feeds()
	{
		global $feed;
		global $category;
                $id_list=$feed->feedList();
                foreach($id_list as $id)
                {
			$cat_id = $feed->feedCategory($id);
			$cat_data=$category->categoryData($cat_id);
                        $cat_name=$cat_data['name'];
                        $data=$feed->feedData($id);
                        $title = $data['title'];
			$link = $data['feed_url'];
			print "$id,$title,$link,$cat_name<br>\n";
                }
	}
	function print_raw_categories()
	{
		global $category;
		$id_list=$category->categoryList();
		foreach($id_list as $cat_id)
		{
			$data=$category->categoryData($cat_id);
			$name=$data['name'];
			print "$name<br>\n";
		}
	}
	function un_escape($mything)
	{
	    $mything=str_replace('&#39;',"'",$mything);
		$mything=str_replace('&#34;','"',$mything);
		$mything=str_replace('&#42;','*',$mything);
		$mything=str_replace('&#47;','/',$mything);
		$mything=str_replace('&#92;','\\',$mything);
		return $mything;
	}	

?>
