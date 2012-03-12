var backend='http://www.feedinator.com/gui/backend.php';
function update_instapaper(form) {
        document.getElementById('menu_status').innerHTML='Updating...';
        var username	=form.instapaper_username.value;
		var password	=form.instapaper_password.value;
        var url         ='op=update_instapaper&username='+username+'&password='+password;
        $.ajax({type: "GET",url: backend, data:url,success:function(html){$('#menu_status').html(html);}});
}

function expandPane(id) {
	$('#menu_status').html('Loading...');
	if (id == "feedConfPane") 
	{
		url="op=feed_prefs"
		$.ajax({type: "GET",url: backend, data:url,success:function(html){$('#feedConfPane').html(html);$('#menu_status').html('Done');}})
	}
	 else if (id == "filterConfPane") {
		updateFilterList();
	} else if (id == "labelConfPane") {
		updateLabelList();
	} else if (id == "publicConfPane") {
		updatePublicList();
	} else if (id == "categoryConfPane") {
		updateCategoryList();
	}
}
function editFeed(feed) {
	//put results into feedConfPane
	$('#menu_status').html('Loading...');
	var url="backend.php?op=pref_feeds&id="+feed;
	$.ajax({type: "GET",url:backend,data:url,success:function(html){$('#feedConfPane').html(html);$('#menu_status').html('Done');}})
}

/*

function feedlist_callback() {
	var container = document.getElementById('feedConfPane');
	if (xmlhttp.readyState == 4) {
		container.innerHTML=xmlhttp.responseText;

		if (active_feed) {
			var row = document.getElementById("FEEDR-" + active_feed);
			if (row) {
				if (!row.className.match("Selected")) {
					row.className = row.className + "Selected";
				}		
			}
			var checkbox = document.getElementById("FRCHK-" + active_feed);
			if (checkbox) {
				checkbox.checked = true;
			}
		}
	}
}

function filterlist_callback() {
	var container = document.getElementById('filterConfPane');
	if (xmlhttp.readyState == 4) {
		container.innerHTML=xmlhttp.responseText;

		if (active_filter) {
			var row = document.getElementById("FILRR-" + active_filter);
			if (row) {
				if (!row.className.match("Selected")) {
					row.className = row.className + "Selected";
				}		
			}
			var checkbox = document.getElementById("FICHK-" + active_filter);
			
			if (checkbox) {
				checkbox.checked = true;
			}
		}
	}
}

function labellist_callback() {
	var container = document.getElementById('labelConfPane');
	if (xmlhttp.readyState == 4) {
		container.innerHTML=xmlhttp.responseText;

		if (active_filter) {
			var row = document.getElementById("LILRR-" + active_label);
			if (row) {
				if (!row.className.match("Selected")) {
					row.className = row.className + "Selected";
				}		
			}
			var checkbox = document.getElementById("LICHK-" + active_label);
			
			if (checkbox) {
				checkbox.checked = true;
			}
		}
	}
}
function categorylist_callback() {
	var container = document.getElementById('categoryConfPane');
	if (xmlhttp.readyState == 4) {
		container.innerHTML=xmlhttp.responseText;

		if (active_filter) {
			var row = document.getElementById("CATRR-" + active_label);
			if (row) {
				if (!row.className.match("Selected")) {
					row.className = row.className + "Selected";
				}		
			}
			var checkbox = document.getElementById("CATCHK-" + active_label);
			
			if (checkbox) {
				checkbox.checked = true;
			}
		}
	}
}

function notify_callback() {
	var container = document.getElementById('notify');
	if (xmlhttp.readyState == 4) {
		container.innerHTML=xmlhttp.responseText;
	}
}

function publiclist_callback() {
        var container = document.getElementById('publicConfPane');
        if (xmlhttp.readyState == 4) {
                container.innerHTML=xmlhttp.responseText;
        }
}



function updatePublicList()  {

	        if (!xmlhttp_ready(xmlhttp)) {
                printLockingError();
                return
        }

        document.getElementById("publicConfPane").innerHTML = "Loading feeds, please wait...";
        xmlhttp.open("GET", "backend.php?op=public_feeds", true);
        xmlhttp.onreadystatechange=publiclist_callback;
        xmlhttp.send(null);

}

function toggleSelectRow(sender) {
	var parent_row = sender.parentNode.parentNode;

	if (sender.checked) {
		if (!parent_row.className.match("Selected")) {
			parent_row.className = parent_row.className + "Selected";
		}
	} else {
		if (parent_row.className.match("Selected")) {
			parent_row.className = parent_row.className.replace("Selected", "");
		}
	}
}

function addLabel() {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	var sqlexp = document.getElementById("ladd_expr");

	if (sqlexp.value.length == 0) {
		notify("Missing SQL expression.");
	} else {
		notify("Adding label...");

		xmlhttp.open("GET", "backend.php?op=pref-labels&subop=add&exp=" +
			param_escape(sqlexp.value), true);			
			
		xmlhttp.onreadystatechange=labellist_callback;
		xmlhttp.send(null);

		sqlexp.value = "";
	}

}

function addFilter() {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	var regexp = document.getElementById("fadd_regexp");
	var match = document.getElementById("fadd_match");

	if (regexp.value.length == 0) {
		notify("Missing filter expression.");
	} else {
		notify("Adding filter...");

		var v_match = match[match.selectedIndex].text;

		xmlhttp.open("GET", "backend.php?op=pref-filters&subop=add&regexp=" +
			param_escape(regexp.value) + "&match=" + v_match, true);			
			
		xmlhttp.onreadystatechange=filterlist_callback;
		xmlhttp.send(null);

		regexp.value = "";
	}

}
function addFeedLink(link) {

	expandPane('feedConfPane');
	var linkfield=document.getElementById("fadd_link");
	linkfield.value=link;
	addFeed();
}

function addFeed() {
	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	var link = document.getElementById("fadd_link");

	if (link.value.length == 0) {
		notify("Missing feed URL.");
	} else {
		notify("Adding feed...");
		xmlhttp.open("GET", "backend.php?op=pref-feeds&subop=add&link=" +
			param_escape(link.value), true);
		xmlhttp.onreadystatechange=feedlist_callback;
		xmlhttp.send(null);
		link.value = "";
	}
}
function addCategory() {
        if (!xmlhttp_ready(xmlhttp)) {
                printLockingError();
                return
        }

        var name = document.getElementById("fadd_cat_name");
        var desc = document.getElementById("fadd_cat_desc");

	if(name.value.length == 0){notify("Missing category name...");return;}
	if(desc.value.length == 0){notify("Missing category description...");return;}
	notify("Adding category...");
	xmlhttp.open("GET", "backend.php?op=categories-pane&subop=add&name=" +
		param_escape(name.value) +
		"&desc="+desc.value, true);
	xmlhttp.onreadystatechange=categorylist_callback;
	xmlhttp.send(null);
	name.value="";
	desc.value="";
	notify("");
}

function editLabel(id) {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	active_label = id;

	xmlhttp.open("GET", "backend.php?op=pref-labels&subop=edit&id=" +
		param_escape(id), true);
	xmlhttp.onreadystatechange=labellist_callback;
	xmlhttp.send(null);

}

function editFilter(id) {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	active_filter = id;

	xmlhttp.open("GET", "backend.php?op=pref-filters&subop=edit&id=" +
		param_escape(id), true);
	xmlhttp.onreadystatechange=filterlist_callback;
	xmlhttp.send(null);

}

function editFeed(feed) {
	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	active_feed = feed;

	xmlhttp.open("GET", "backend.php?op=pref-feeds&subop=edit&id=" +
		param_escape(feed), true);
	xmlhttp.onreadystatechange=feedlist_callback;
	xmlhttp.send(null);

}

function editCategory(category) {
	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	active_category = category;

	xmlhttp.open("GET", "backend.php?op=categories-pane&subop=edit&id=" +
		param_escape(category), true);
	xmlhttp.onreadystatechange=categorylist_callback;
	xmlhttp.send(null);

}


function getSelectedLabels() {

	var content = document.getElementById("prefLabelList");

	var sel_rows = new Array();

	for (i = 0; i < content.rows.length; i++) {
		if (content.rows[i].className.match("Selected")) {
			var row_id = content.rows[i].id.replace("LILRR-", "");
			sel_rows.push(row_id);	
		}
	}

	return sel_rows;
}


function getSelectedFilters() {

	var content = document.getElementById("prefFilterList");

	var sel_rows = new Array();

	for (i = 0; i < content.rows.length; i++) {
		if (content.rows[i].className.match("Selected")) {
			var row_id = content.rows[i].id.replace("FILRR-", "");
			sel_rows.push(row_id);	
		}
	}

	return sel_rows;
}

function getSelectedFeeds() {

	var content = document.getElementById("prefFeedList");

	var sel_rows = new Array();

	for (i = 0; i < content.rows.length; i++) {
		if (content.rows[i].className.match("Selected")) {
			var row_id = content.rows[i].id.replace("FEEDR-", "");
			sel_rows.push(row_id);	
		}
	}

	return sel_rows;
}

function getSelectedCategories() {
	var content = document.getElementById("categoriesFeedList");
	var sel_rows = new Array();
	for (i = 0; i < content.rows.length; i++) {
		if (content.rows[i].className.match("Selected")) {
			var row_id = content.rows[i].id.replace("CATR-", ""); // #### ?????
			sel_rows.push(row_id);	
		}
	}
	return sel_rows;
}

function readSelectedFeeds() {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	var sel_rows = getSelectedFeeds();

	if (sel_rows.length > 0) {

		notify("Marking selected feeds as read...");

		xmlhttp.open("GET", "backend.php?op=pref-rpc&subop=unread&ids="+
			param_escape(sel_rows.toString()), true);
		xmlhttp.onreadystatechange=notify_callback;
		xmlhttp.send(null);

	} else {

		notify("Please select some feeds first.");

	}
}

function unreadSelectedFeeds() {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	var sel_rows = getSelectedFeeds();

	if (sel_rows.length > 0) {

		notify("Marking selected feeds as unread...");

		xmlhttp.open("GET", "backend.php?op=pref-rpc&subop=unread&ids="+
			param_escape(sel_rows.toString()), true);
		xmlhttp.onreadystatechange=notify_callback;
		xmlhttp.send(null);

	} else {

		notify("Please select some feeds first.");

	}
}

function removeSelectedLabels() {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	var sel_rows = getSelectedLabels();

	if (sel_rows.length > 0) {

		notify("Removing selected labels...");

		xmlhttp.open("GET", "backend.php?op=pref-labels&subop=remove&ids="+
			param_escape(sel_rows.toString()), true);
		xmlhttp.onreadystatechange=labellist_callback;
		xmlhttp.send(null);

	} else {
		notify("Please select some labels first.");
	}
}

function removeSelectedFilters() {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	var sel_rows = getSelectedFilters();

	if (sel_rows.length > 0) {

		notify("Removing selected filters...");

		xmlhttp.open("GET", "backend.php?op=pref-filters&subop=remove&ids="+
			param_escape(sel_rows.toString()), true);
		xmlhttp.onreadystatechange=filterlist_callback;
		xmlhttp.send(null);

	} else {
		notify("Please select some filters first.");
	}
}


function removeSelectedFeeds() {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	var sel_rows = getSelectedFeeds();

	if (sel_rows.length > 0) {

		notify("Removing selected feeds...");

		xmlhttp.open("GET", "backend.php?op=pref-feeds&subop=remove&ids="+
			param_escape(sel_rows.toString()), true);
		xmlhttp.onreadystatechange=feedlist_callback;
		xmlhttp.send(null);

	} else {

		notify("Please select some feeds first.");

	}

}

function removeSelectedCategories() {
	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}
	var sel_rows = getSelectedCategories();
	if (sel_rows.length > 0) {
		notify("Removing selected categories...");
		xmlhttp.open("GET", "backend.php?op=categories-pane&subop=remove&ids="+
			param_escape(sel_rows.toString()), true);
		xmlhttp.onreadystatechange=categorylist_callback;
		xmlhttp.send(null);
	} else {
		notify("Please select some categories first.");
	}
	notify("");
}



function feedEditCancel() {
	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	active_feed = false;
	notify("Operation cancelled.");

	xmlhttp.open("GET", "backend.php?op=pref-feeds", true);
	xmlhttp.onreadystatechange=feedlist_callback;
	xmlhttp.send(null);
}

function categoryEditCancel() {
	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	active_feed = false;
	notify("Operation cancelled.");

	xmlhttp.open("GET", "backend.php?op=categories-pane", true);
	xmlhttp.onreadystatechange=categorylist_callback;
	xmlhttp.send(null);
}
function categoryEditSave() {
	var category=active_category;
	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return;
	}
	var title	= document.getElementById("catedit_name").value;
	var desc	= document.getElementById("catedit_desc").value;
	if(title.length == 0){notify("Category title cannot be blank.");return;}
	active_category = false;

	xmlhttp.open("GET", "backend.php?op=categories-pane&subop=editSave&id=" +
		category + 
		"&name=" + param_escape(title) +
		"&desc=" + param_escape(desc) ,true);
	xmlhttp.onreadystatechange=categorylist_callback;
	xmlhttp.send(null);
}

function feedEditSave() {
	var feed = active_feed;
	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	var link     		= document.getElementById("iedit_link").value;
	var title    		= document.getElementById("iedit_title").value;
	var expirey  		= document.getElementById("iedit_expirey").value;
	var isextendedchk 	= document.getElementById("iedit_extended");
	var ispublicchk 	= document.getElementById("iedit_public");
	var categoryid		= document.getElementById("iedit_category").value;
	var viewmode		= document.getElementById("iedit_viewmode").value;
	var isextended		= 0;
	var ispublic		= 0;
	if (isextendedchk.checked){isextended=1;}
	if (ispublicchk.checked){ispublic=1;}
	if (link.length == 0) {notify("Feed link cannot be blank.");return;}
	if (title.length == 0) {notify("Feed title cannot be blank.");return;}

	active_feed = false;

	xmlhttp.open("GET", "backend.php?op=pref-feeds&subop=editSave&id=" +
		feed + "&l=" + param_escape(link) + 
		"&t=" + param_escape(title) + 
		"&exp=" + param_escape(expirey) +
		"&ext=" + param_escape(isextended) +
		"&cat=" + param_escape(categoryid) +
		"&viewmode=" + param_escape(viewmode) +
		"&pub=" + param_escape(ispublic) ,true);
	xmlhttp.onreadystatechange=feedlist_callback;
	xmlhttp.send(null);
}

function labelEditCancel() {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	active_label = false;

	notify("Operation cancelled.");

	xmlhttp.open("GET", "backend.php?op=pref-labels", true);
	xmlhttp.onreadystatechange=labellist_callback;
	xmlhttp.send(null);

}


function filterEditCancel() {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	active_filter = false;

	notify("Operation cancelled.");

	xmlhttp.open("GET", "backend.php?op=pref-filters", true);
	xmlhttp.onreadystatechange=filterlist_callback;
	xmlhttp.send(null);

}

function labelEditSave() {

	var label = active_label;

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	var sqlexp = document.getElementById("iedit_expr").value;
	var descr = document.getElementById("iedit_descr").value;

//	notify("Saving label " + sqlexp + ": " + descr);

	if (sqlexp.length == 0) {
		notify("SQL expression cannot be blank.");
		return;
	}

	if (descr.length == 0) {
		notify("Caption cannot be blank.");
		return;
	}

	active_label = false;

	xmlhttp.open("GET", "backend.php?op=pref-labels&subop=editSave&id=" +
		label + "&s=" + param_escape(sqlexp) + "&d=" + param_escape(descr),
		true);
		
	xmlhttp.onreadystatechange=labellist_callback;
	xmlhttp.send(null);

}

function filterEditSave() {

	var filter = active_filter;

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	var regexp = document.getElementById("iedit_regexp").value;
	var descr = document.getElementById("iedit_descr").value;
	var match = document.getElementById("iedit_match");

	var v_match = match[match.selectedIndex].text;

//	notify("Saving filter " + filter + ": " + regexp + ", " + descr + ", " + match);

	if (regexp.length == 0) {
		notify("Filter expression cannot be blank.");
		return;
	}

	active_filter = false;

	xmlhttp.open("GET", "backend.php?op=pref-filters&subop=editSave&id=" +
		filter + "&r=" + param_escape(regexp) + "&d=" + param_escape(descr) +
		"&m=" + param_escape(v_match), true);
		
	xmlhttp.onreadystatechange=filterlist_callback;
	xmlhttp.send(null); 

}

function editSelectedLabel() {
	var rows = getSelectedLabels();

	if (rows.length == 0) {
		notify("No labels are selected.");
		return;
	}

	if (rows.length > 1) {
		notify("Please select one label.");
		return;
	}

	editLabel(rows[0]);

}


function editSelectedFilter() {
	var rows = getSelectedFilters();

	if (rows.length == 0) {
		notify("No filters are selected.");
		return;
	}

	if (rows.length > 1) {
		notify("Please select one filter.");
		return;
	}

	editFilter(rows[0]);

}


function editSelectedFeed() {
	var rows = getSelectedFeeds();

	if (rows.length == 0) {
		notify("No feeds are selected.");
		return;
	}

	if (rows.length > 1) {
		notify("Please select one feed.");
		return;
	}

	editFeed(rows[0]);

}

function editSelectedCategory() {
	var rows = getSelectedCategories();
	if (rows.length == 0) {
		notify("No Categories are selected.");
		return;
	}

	if (rows.length > 1) {
		notify("Please select one feed.");
		return;
	}

	editCategory(rows[0]);
}

function localPiggieFunction(enable) {
	if (enable) {
		piggie.style.display = "block";
		seq = "";
		notify("I loveded it!!!");
	} else {
	//	piggie.style.display = "none";
		notify("");
	}
}

function validateOpmlImport() {
	
	var opml_file = document.getElementById("opml_file");

	if (opml_file.value.length == 0) {
		notify("Please select OPML file to upload.");
		return false;
	} else {
		return true;
	}
}

function updateFilterList() {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	document.getElementById("filterConfPane").innerHTML = "Loading filters, please wait...";

	xmlhttp.open("GET", "backend.php?op=pref-filters", true);
	xmlhttp.onreadystatechange=filterlist_callback;
	xmlhttp.send(null);

}

function updateLabelList() {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	document.getElementById("labelConfPane").innerHTML = "Loading labels, please wait...";

	xmlhttp.open("GET", "backend.php?op=pref-labels", true);
	xmlhttp.onreadystatechange=labellist_callback;
	xmlhttp.send(null);

}

function updateCategoryList() {

	if (!xmlhttp_ready(xmlhttp)) {
		printLockingError();
		return
	}

	document.getElementById("categoryConfPane").innerHTML = "Loading categories, please wait...";

	xmlhttp.open("GET", "backend.php?op=categories-pane", true);
	xmlhttp.onreadystatechange=categorylist_callback;
	xmlhttp.send(null);

}



function init() {

	// IE kludge

	if (!xmlhttp) {
		document.getElementById("prefContent").innerHTML = 
			"<b>Fatal error:</b> This program needs XmlHttpRequest " + 
			"to function properly. Your browser doesn't seem to support it.";
		return;
	}

//	updateFeedList();
	document.onkeydown = hotkey_handler;
	notify("");

}
*/
