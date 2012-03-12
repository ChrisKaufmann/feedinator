<html>
<head>
	<title>Feedinator : Preferences</title>
	<link rel="stylesheet" href="tt-rss.css" type="text/css">
	<script type="text/javascript" src="prefs.js"></script>
	<script type="text/javascript" src="jquery.js"></script>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<? include("../include/session.php");
if($session->logged_in){$username=$session->username;}
else{header('location:/main.php');}
?>
<?
 include_once("../include/database.php");
 $userinfo=$database->getUserInfo($_SESSION['username']);
?>
<table width="100%" height="100%" cellspacing="0" cellpadding="0" class="main">
<tr>
	<td class="prefsToolbar" valign="middle">

		<table width='100%' cellspacing='0' cellpadding='0'>	
			<td><div id='menu_status'></div></td>
			<td align='right'>
				<?print $username;?><input type="submit" onclick="gotoMain()" 
					class="button" value="Return to main"></td>
		</table>
	</td>
</tr>
</tr>
	<td id="prefContent" class="prefContent" valign="top">
		<h2>Feed Configuration</h2>
		<table>
			<tr>
				<td>
				<form name='add_feed_form'>
				<input type='text' name='add_feed_text'>
				<input type='button' value='Add' onclick='add_feed(this.form)'>
				</form>
				</td>
			</tr>
			</form>
		</table>
		<a class="button"href="javascript:expandPane('feedConfPane')">Expand section &gt;</a>
		<div id="feedConfPane">
		</div>

		<h2>OPML Import</h2>

		<div class="expPane">
	
		<form	enctype="multipart/form-data" method="POST" action="opml.php">
			File: <input id="opml_file" name="opml_file" type="file">&nbsp;
			<input class="button" name="op" onclick="return validateOpmlImport();"
				type="submit" value="Import">
			</form>

		</div>

		<h2>Content Filtering</h2>

		<div class="expPane" id="filterConfPane">
			<a class="button" 
				href="javascript:expandPane('filterConfPane')">Expand section &gt;</a>

		</div>


		<h2>Public Feeds</h2>
		<div class="expPane" id="publicConfPane">
			<a class="button"
				href="javascript:expandPane('publicConfPane')">Expand Section &gt;</a>
		</div>

		<h2>Categories</h2>
		<table class="prefAddFeed"><tr>
			<td><input id="fadd_cat_name" value="Name"></td>
			<td><input id="fadd_cat_desc" value="Description"></td>
			<td><a class="button" href="javascript:addCategory()">Add Category</a></td>
		</tr></table>
		<div class="expPane" id="categoryConfPane">
			<a class="button" 
				href="javascript:expandPane('categoryConfPane')">Expand Categories</a>

		</div>
		<h2>Instapaper</h2>
		<table class="prefAddFeed">
		<form name='update_instapaper_form'>
		<tr><td>Username:</td><td><input type="text" name="instapaper_username" value="<? 
	
	print $userinfo['instapaper_username'] 
?>" size="32"></td></tr>
		<tr><td>Password(if exists):</td><td><input type="password" name="instapaper_password" size="32"></td></tr>
		<tr><td><input type="button" value="Update" onclick='update_instapaper(this.form)'></td></tr>
		</form>
		</table>
	</td>
</tr>
<tr>
	<td class="footer">
		<a href="http://www.chriskaufmann.com">Chris Kaufmann</a> &copy; 2005 Feedinator
	</td>
</td>
</table>


</body>
</html>
