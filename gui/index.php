<?

	require_once "../include/session.php";
	require_once "../include/database.php";
	require_once "feed.php";
	require_once "entry.php";
	require_once "category.php";
 	include_once "backend.php";
	if($session->logged_in)	{$username=$session->username;}
	else				{header('location:/main.php');exit;}
	if(!$username)		{print "No username<br>\n";}
#  <script type="text/javascript" src="prototype.js"></script><!-- prototype ajax library -->
?>
<html>
<head>
  <title>Feedinator</title>
  <script type="text/javascript" src="jquery.js"></script>
  <script type="text/javascript" src="jsdump.js"></script>
  <script type="text/javascript" src="widgets.js"></script>
  <script type="text/javascript" src="include.js"></script><!-- ajax functions -->
  <link rel="stylesheet" href="tt-rss.css" type="text/css">
  <link rel="stylesheet" href="menu.css">
    <style media="screen" type="text/css">
    /* <!-- */
    /* General styles */
    body {
        margin:0;
        padding:0;
        border:0;			/* This removes the border around the viewport in old versions of IE */
        width:100%;
	height:100%;
        background:#fff;
        min-width:600px;    /* Minimum width of layout - remove line if not required */
							/* The min-width property does not work in old versions of Internet Explorer */
		font-size:90%;
    }

    h1, h2, h3 {
        margin:.8em 0 .2em 0;
        padding:0;
    }
    p {
        margin:.4em 0 .8em 0;
        padding:0;
    }

	/* Header styles */
    #header {
        clear:both;
        float:left;
        width:100%;
    }
	#header {
		border-bottom:1px solid #000;
	}
	#header p,
	#header h1,
	#header h2 {
	    padding:.4em 15px 0 15px;
        margin:0;
	}
	#header ul {
	    clear:left;
	    float:left;
	    width:100%;
	    list-style:none;
	    margin:10px 0 0 0;
	    padding:0;
	}
	#header ul li {
	    display:inline;
	    list-style:none;
	    margin:0;
	    padding:0;
	}
	#header ul li a {
	    display:block;
	    float:left;
	    margin:0 0 0 1px;
	    padding:3px 10px;
	    text-align:center;
	    background:#eee;
	    color:#000;
	    text-decoration:none;
	    position:relative;
	    left:15px;
		line-height:1.3em;
	}
	#header ul li a:hover {
	    background:#369;
		color:#fff;
	}
	#header ul li a.active,
	#header ul li a.active:hover {
	    color:#fff;
	    background:#000;
	    font-weight:bold;
	}
	#header ul li a span {
	    display:block;
	}
	/* 'widths' sub menu */
	#layoutdims {
		clear:both;
		background:#eee;
		border-top:4px solid #000;
		margin:0;
		padding:6px 15px !important;
		text-align:right;
	}
	/* column container */
	.colmask {
	    	position:relative;		/* This fixes the IE7 overflow hidden bug */
	    	clear:both;
	    	float:left;
       		width:100%;			/* width of whole page */
		overflow:hidden;	/* This chops off any overhanging divs */
		height:100%;
	}
	/* common column settings */
	.colright,
	.colmid,
	.colleft {
		float:left;
		width:100%;
		position:relative;
		height:100%;
	}
	.col1,
	.col2,
	.col3 {
		float:left;
		position:relative;
		#padding:0 0 1em 0;
		overflow:hidden;
		height:100%;
	}
	.col1 .entries {
		height:22%;
		border:1px solid #979797;
		overflow:auto;
		min-height:150px;
		overflow-x:hidden;
	}
	.col1 .view {
		height:200%;
		border:1px solid #979797;
		overflow:auto;
		overflow-x:hidden;
		min-height:400px;
	}
	/* 2 Column (left menu) settings */
	.leftmenu {
		background:#fff;		/* right column background colour */
	}
	.leftmenu .colleft {
		right:84%;				/* right column width */
		background:#f4f4f4;		/* left column background colour */
	}
	.leftmenu .col1 {
		width:84%;				/* right column content width */
		left:100%;				/* 100% plus left column left padding */
	}
	.leftmenu .col2 {
		width:16%;				/* left column content width (column width minus left and right padding) */
		left:0%;	/* indentation (from right) of the text content of the feed/category list */
	}
	/* Footer styles */
	#footer {
        clear:both;
        float:left;
        width:100%;
		border-top:1px solid #000;
    }
    #footer p {
        padding:10px;
        margin:0;
    }
    /* --> */
    </style>
</head>
<body>
<div class="colmask leftmenu">
	<div class="colleft">
		<div class="col1">
			<?  print_menu(); ?>  
			<div id='entries_list_div' class='entries'>
			</div>
			<div id='view_div' class='view'>
			</div>
		</div>
		<div class="col2">
			<div id='left_nav_top' style='height:40px';>
				<table><tr>
				<td><a href="javascript:categoryList();">Categories</a>&nbsp;</td>
				<td><a href="javascript:feedList();">Feeds</a>&nbsp;</td>
				<td><a href="javascript:marked_entries();">Starred</a></td>
				<td><div id='feeds_status' style='width:20px'></div></td>
				</tr></table>
			</div>
			<div id='feeds_div' style='overflow:auto;overflow-x:hidden;'>
				<? print_category_list();   ?>
			</div>
		</div>
	</div>
</div>
</body>
</html>
