<?

	require_once "../include/session.php";    // session include 	- gives $username
	require_once "../include/database.php";   // database function 	- use "global $database;" in functions
	require_once "feed.php";				// feed object 		- use "global $feed;" in functions 
	require_once "entry.php";				// entry obj 		- use "global $entry;" in functions
	require_once "category.php";				// category obj 		- use "global $category;" in functions
	$view_options = array('Default','Link','Extended','Proxy'); //List of options for viewing
	if(isset($_REQUEST['op']))  				//Use isset to avoid warnings.  :)
	{
	$op=$_REQUEST['op'];
	switch($op){
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
		case "empty_feed":
			empty_feed();
			break;
		case "empty_category";
			empty_category();
			break;
		case "unread_feed":
			unread_feed();
			break;
		case "unread_category":
			unread_category();
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
		case "update_viewmode":
			update_view_mode($_REQUEST['id'],$_REQUEST['view_mode']);
			break;
		case "print_menu":
			print_menu($_REQUEST['view_mode'],$_REQUEST['id']);
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
	};
	}  // End of if isset op
	function update_feed_link($id,$link)
	{
		global $feed;
		if(!$id || !$link){print "Not updated!";return;}
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
		$feed->feedCategory($id,$category);
		print_menu('feed',$id);
	}
	function update_view_mode($id,$view_mode)
	{
		global $feed;
		$feed->viewMode($id,$view_mode);
		print_menu('feed',$id);
	}
	//  To print the menu above the entry listing
	function print_menu($viewmode='',$feed_id='')
	{
		global $category,$feed,$entry,$view_options;
		$link=$feed->feedUrl($feed_id);
		if(isset($_REQUEST['skip_div'])){$skip_div=$_REQUEST['skip_div'];}else{$skip_div=0;}
//		if(!$skip_div){print "<div id='settings_div' style='height:25px;border:1px solid #979797;'>";}
		if(!$skip_div){print "<div id='settings_div'>";}
		print "
		<table width='100%'>
		<tr>
			<td align='left'>
				<div id='menu_status' style='height:20px;width:140px'></div>
			</td>
				<td align='right'>
					<a href='prefs.php'>Preferences</a>&nbsp;
					<a href='javascript:empty_current();'>Mark all read</a>
				</td>
				<td align='right'>
					<form name='add_feed_form'>
					<input type='text' name='add_feed_text'>

					<input type='button' value='Add' onclick='add_feed(this.form)'>
					</form>
				</td>
				<td>
		";
			if($viewmode=='feed'){print "<div id='toggle_settings_div'><a href='javascript:toggle_visible(\"modify_div\");'>^</a></div>\n";}
		
		print "
				</td>
			</tr>
			</table>
			<div id='modify_div' style='border:1px solid #979797;display:none'>
			<table>
			<tr>
				<td>

				</td>

		<td align='right'>
		";
			print "
				<form name='view_customize_form'>
				<select name='select' onchange='customize(this.form)'>
				<option value=''>Customize
			\n";
			$current_view_mode=$feed->viewMode($feed_id);
			$current_category	=$feed->feedCategory($feed_id);
			print "<option value=''>--View Mode--";
			foreach($view_options as $option)
			{
				$lc_option=strtolower($option);
				if($lc_option == $current_view_mode){$option="*$option";}
				print "<option value='$lc_option'>$option\n";
			}
			print "<option value=''>--Category--\n";
			$id_list=$category->categoryList();
			foreach($id_list as $cat_id)
			{
				$data=$category->categoryData($cat_id);	
				$name=$data['name'];
				if($cat_id == $current_category){$name="*$name";}
				print "<option value='$cat_id'>$name\n";
			}
			print "
				<option value=''>--Delete--
				<option value='delete'>Delete Feed
				</select>\n
				</form>
				</td>
				<td align='right'>
				<form name='rename_feed_form'>
				<input type='text' name='rename_feed_text'>

					<input type='button' value='Rename' onclick='rename_feed(this.form)'>
					</form>
				</td>
				<td>
					<form name='update_link_form'>
					<input type='text' name='update_link_text' value='$link'>
					<input type='button' value='Change Link' onclick='update_link(this.form)'>
					</form>
				</td>
				</tr>
				</table>
				</div>
		";
		if(!$skip_div){print"	</div>\n";}
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
			$entry->setMarked($id,1);
			$markunmark="mark_set.png";
		}
		print "<img src='images/$markunmark' alt='Set mark' onclick='javascript:toggleMark($id);'>\n";
	}

	function empty_feed()
	{
		global $feed;
		$id=$_REQUEST['id'];
		if(!$id){return;}
		$feed->emptyFeed($id);
		$unread=$feed->unreadCount($id);
		if(!$unread){print "Feed Emptied";}
		else{print "Emptied?  $unread";}
	}
	function empty_category()
	{
		global $category;
		$id=$_REQUEST['id'];
		if(!$id){return;}
		$category->emptyCategory($id);
		$unread=$category->unreadCount($id);
		if(!$unread){print "Category Emptied";}
		else{print "Emptied?  $unread";}
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
		$id=$_REQUEST['id'];
		$previous=$_REQUEST['previous'];
		$next=$_REQUEST['next'];
		$feedorcat=$_REQUEST['method'];
		$debug=$_REQUEST['debug'];
		$feed_id=$entry->feedID($id);
		if(!isset($id)){print "No feed specified";return;}
		$data=$entry->entryData($id);
		$view_mode=$feed->viewMode($feed_id);


		if($debug){print "id=$id<br>\n";}
		if($debug){print "title={$data['title']}<br>
			link={$data['link']}<br>
			marked={$data['marked']}<br>
			show_extended={$data['show_extended']}<br>
			content={$data['content']}<br>\n";}
		if(!$data){print "No data for entry $id found";return;}
		if($data['icon_url']){$feed_icon="<img class=\"feedIcon\" src=\"".$data['icon_url']."\">";}
		else{$feed_icon="&nbsp;";}
		if ($data["comments"] && $data["link"] != $data["comments"]) {
			$entry_comments = "(<a href=\"".$data["comments"]."\">Comments</a>)";
		} else {$entry_comments = "";	}
		$marked=$data['marked'];
		if($marked){$markunmark='mark_set.png';}else{$markunmark='mark_unset.png';}
		if($view_mode !='link')
		{
			print "<div class=\"postReply\">";
			print "<div class=\"postHeader\"><table>";
			print "<tr></td><td><b>Title:</b></td><td width='100%'>" . $data["title"] . "</td>";
			print "<td align=right>
				<div id=\"entry_previous_next\" style=\"width:85px\">
					<a href='javascript:show_entry(".$previous.");'>Previous</a>
					<a href='javascript:show_entry(".$next.");'>Next</a></div>
				</td>";
			print "</tr>\n";
			$label=substr($data["link"],0,60);
			print "<tr><td><b>Link:</b></td>
				<td width='100%'>
				<a href=\"" . $data["link"] . "\">$label</a>
				$entry_comments</td>
				<td align=right>
				<a href=\"backend.php?op=view_entry&id=$id\" target=\"_blank\">External</a>
				</td>
				<td align=right>
				<a href=\"https://home.chriskaufmann.com/rss/gui/nph-proxy.cgi/010110A/x-proxy/start?URL=".$data["link"]."&rc=checked&rs=checked&fa=checked&br=checked&if=checked\" target=\"_blank\">Proxy</a>
				</td>
				<td align=right>
				<a href=\"javascript:showPageData('{$data['link']}');\">Page</a>
				</td>
				<td align=right>
				<a href=\"".$data["link"]."\" target=\"_blank\">New</a>
				</td>
				<td align=right>
				<div id='EMARKPIC-$id'><img src='images/$markunmark' alt='Set mark' onclick='javascript:toggleMark($id);'></div>

				</td>
				</tr>";

			print "</table></div>";
			print "<div class=\"postIcon\">" . $feed_icon . "</div>";
		}//end if not $view_mode is 'link'
		switch($view_mode){
			case "link":
			print "<table width='100%'><tr><td>". $data["title"] ."</td>
				<td align='right'><a href='".$data['link']. "' target='_blank'>new</a><td>
				<td align='right'><div id='EMARKPIC-$id'><img src='images/$markunmark' alt='Set mark' onclick='javascript:toggleMark($id);'></div></td>
				</tr></table>";
				print "<iframe src=".$data['link']. " frameborder=\"0\" width=\"100%\" height=\"90%\" security=\"restricted\">\n</iframe>\n";
				break;
			case "proxy":
				print_proxy_data($data['link']);
				return;
				break;
			case "extended":
				print "<div class=\"postContent\">" . $data["content"] . "</div>\n";
				viewExtendedPage($data['link']);
				break;
			case "default":
				print "<div class=\"postContent\">" . $data["content"] . "</div>\n";
				print "<div class=\"expPane\" id=\"extendedContentPane\">\n";
				print"<a href=\"javascript:showExtendedContentPane('$id');\">Extended view</a>";
				print "</div>";
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
		$entry_list=$feed->unreadEntries($id);
		$unread_entry_list=$feed->readEntries($id,100);
		if(!$entry_list && !$unread_entry_list){print "No entries found";return;}
		print_entry_list($entry_list,'feed');
		print_entry_list($unread_entry_list,'feed',1);
		exit();
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
		print '<table class="headlinesList" id="headlinesList" width="100%">';
		for($i=0;$i<sizeof($id_list);$i++)
		{
			$id		=$id_list[$i];
			$previous	=$id_list[$i-1];if(!$previous){$previous=0;}
			$next		=$id_list[$i+1];if(!$next){$next=0;}
			$js_link	="javascript:show_entry($id,$previous,$next);";
			if($count%2 == 0){$evenodd='even';}else{$evenodd='odd';}
			$line		=$entry->entryData($id);
			$entry_id	=$id;
			$title	=$line['title'];
			$updated	=$line['updated'];$updated=pretty_date($updated);
			$marked	=$line['marked'];
			$link		=$line['link'];
			$feed_data	=$feed->feedData($line['feed_id']);
			$feed_name	=$feed_data['title'];
			if($marked)	{$markunmark='mark_set.png';}else{$markunmark='mark_unset.png';}
			print "<tr class='{$evenodd}$readunread' id='RROW-$id'>\n";
			print "\t<td valign='center' align='center'><div id='FMARKPIC-$id'><img src='images/$markunmark' alt='Set mark' onclick='javascript:toggleMark($id);'></div></td>\n";
			print "\t<td ><a href='javascript:remove_entry($id);'>-</a></td>\n";
			print "\t<td width='22%'><a href='$js_link'> $feed_name</a></td>\n";
			print "\t<td width='60%'><a href='$js_link'> $title </a></td>\n";
			print "\t<td width='12%'><a href='$js_link'> $updated </a></td>\n";
			print "\t<td align='right'><a href='$link' target='_blank'>-></a></td>\n";
			print "</tr>\n";
			$count++;
		}
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
		print "link='$link'<br>\n";
		$ch = curl_init();
		$timeout = 10; // set to zero for no timeout
		curl_setopt ($ch, CURLOPT_URL, $link);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);// allow redirects
		$file_contents=curl_exec($ch);
		$curl_error=curl_error($ch);
		curl_close($ch);
		$domain=str_replace("http://","",$link);
		$list=split("/",$domain);
		$domain=$list[0];
		$file_contents = str_replace("script","",$file_contents);
		$file_contents = str_replace("http://$domain/","",$file_contents);
		$file_contents = str_replace("src=\"","src=\"http://$domain/",$file_contents);
		print $file_contents;
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


?>
