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
		case 'something':
			action();
			break;
		}
	}
	function pref_feeds()
	{

	}



}



